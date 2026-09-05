const express = require('express');
const cors    = require('cors');
const QRCode  = require('qrcode');
const pino    = require('pino');
const fs      = require('fs');
const path    = require('path');
const {
    default: makeWASocket,
    DisconnectReason,
    useMultiFileAuthState,
    fetchLatestBaileysVersion
} = require('@whiskeysockets/baileys');

const app         = express();
const PORT        = process.env.PORT || 3000;
const SESSION_DIR = path.join(__dirname, 'session');

app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// ─── Anti-ban configuration ──────────────────────────────────────────────────
const ANTI_BAN = {
    minDelay      : 15000, // min jeda antar pesan pribadi: 15 detik (Anti-Ban Aman)
    maxDelay      : 35000, // max jeda antar pesan pribadi: 35 detik (Jitter Acak)
    maxPerHour    : 25,    // maks 25 pesan personal per jam (mencegah suspend 5 jam)
    minTyping     : 3000,  // min simulasi mengetik: 3 detik
    maxTyping     : 6500,  // max simulasi mengetik: 6.5 detik
    availableDelay: 1500,  // durasi online sebelum mulai mengetik (ms)
};

let sentThisHour   = 0;
let hourResetTimer = null;

function resetHourCounter() {
    sentThisHour = 0;
    console.log('[Anti-Ban] 🔄 Counter pesan jam ini direset. Siap mengirim kembali.');
}

// ─── State ────────────────────────────────────────────────────────────────────
let sock             = null;
let latestQr         = null;
let isConnected      = false;
let connectedPhone   = '-';
let connectionStatus = 'initializing';

// ─── Message queue ────────────────────────────────────────────────────────────
let messageQueue        = [];
let isProcessingQueue   = false;
let currentlyProcessing = null; // { jid, number, recipientName, status, statusText, startedAt, isGroup }
let recentDispatches    = [];   // Array of { id, jid, number, recipientName, isGroup, status, time, timestamp }

const CLOSINGS = [
    '_Management PT. Besmindo Materi Sewatama_',
    '_Tim Operasional PT. Besmindo Materi Sewatama_',
    '_Salam, Manajemen PT. Besmindo Materi Sewatama_',
    '_Hormat kami, PT. Besmindo Materi Sewatama_',
    '_PT. Besmindo Materi Sewatama — Divisi Operasional Rig_',
];

function randomClosing() {
    return CLOSINGS[Math.floor(Math.random() * CLOSINGS.length)];
}

function randomDelay(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function humanizeMessage(message) {
    return message
        .replace(/_Management PT\.? Besmindo (Materi Sewatama|Oilfield Operations)_/gi, randomClosing())
        .replace(/_PT\.? Besmindo (Materi Sewatama|Oilfield Operations)_/gi, randomClosing());
}

function normalizePhone(number) {
    let clean = number.toString().trim();
    if (clean.includes('@g.us')) {
        return clean;
    }
    clean = clean.replace(/[^0-9]/g, '');
    if (clean.startsWith('0'))   clean = '62' + clean.substring(1);
    if (!clean.startsWith('62')) clean = '62' + clean;
    return `${clean}@s.whatsapp.net`;
}

// ─── Queue processor ─────────────────────────────────────────────────────────
async function processQueue() {
    if (isProcessingQueue) return;
    isProcessingQueue = true;

    while (messageQueue.length > 0) {
        if (!isConnected || !sock) {
            console.log('[Queue] ⚠️ Gateway belum terhubung, antrian ditahan.');
            break;
        }

        const isGroup = messageQueue[0].jid.endsWith('@g.us');

        // Batas per jam hanya berlaku untuk chat personal, bukan group
        if (!isGroup && sentThisHour >= ANTI_BAN.maxPerHour) {
            console.log(`[Anti-Ban] 🛑 Batas aman ${ANTI_BAN.maxPerHour} pesan/jam tercapai agar nomor bebas dari ban. Antrian ditahan sampai jam berikutnya.`);
            break;
        }

        const item = messageQueue.shift();
        const { jid, message, resolve, recipientName } = item;
        const numberOnly = jid.split('@')[0];
        const isGroupItem = jid.endsWith('@g.us');

        currentlyProcessing = {
            jid,
            number        : numberOnly,
            recipientName : recipientName || (isGroupItem ? 'Grup WhatsApp Rig' : numberOnly),
            status        : 'typing',
            statusText    : 'Sedang Mengetik Pesan...',
            isGroup       : isGroupItem,
            startedAt     : Date.now()
        };

        try {
            const targetLabel = recipientName ? `${recipientName} (${jid})` : jid;
            console.log(`[Anti-Ban] 👤 Mempersiapkan pengiriman ke: ${targetLabel}`);

            // 1. Simulasi buka aplikasi / online status
            await sock.sendPresenceUpdate('available');
            await new Promise(r => setTimeout(r, ANTI_BAN.availableDelay));

            // 2. Simulasi mengetik realistis (composing)
            const typingDuration = randomDelay(ANTI_BAN.minTyping, ANTI_BAN.maxTyping);
            await sock.sendPresenceUpdate('composing', jid);
            console.log(`[Anti-Ban] ⌨️ Sedang mengetik pesan (${(typingDuration / 1000).toFixed(1)} detik)...`);
            await new Promise(r => setTimeout(r, typingDuration));

            // 3. Jeda berhenti mengetik sesaat sebelum kirim
            await sock.sendPresenceUpdate('paused', jid);
            await new Promise(r => setTimeout(r, 600));

            // 4. Kirim pesan ke WhatsApp
            const finalMessage = humanizeMessage(message);
            const result = await sock.sendMessage(jid, { text: finalMessage });
            
            if (!isGroup) {
                sentThisHour++;
            }

            // Catat ke daftar berhasil terkirim
            recentDispatches.unshift({
                id            : Date.now() + Math.random().toString(36).substr(2, 4),
                jid,
                number        : numberOnly,
                recipientName : recipientName || (isGroupItem ? 'Grup WhatsApp Rig' : numberOnly),
                isGroup       : isGroupItem,
                status        : 'sent',
                time          : new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }),
                timestamp     : Date.now()
            });
            if (recentDispatches.length > 50) recentDispatches.pop();

            console.log(`[Queue] ✅ TERKIRIM ke ${targetLabel} (${sentThisHour}/${ANTI_BAN.maxPerHour} pesan jam ini). Sisa antrian: ${messageQueue.length}`);

            if (!hourResetTimer) {
                hourResetTimer = setTimeout(() => {
                    resetHourCounter();
                    hourResetTimer = null;
                    if (messageQueue.length > 0) processQueue();
                }, 3600 * 1000);
            }

            if (resolve) resolve({ success: true, message: `Pesan terkirim ke ${jid}`, result });

        } catch (err) {
            console.error(`[Queue] ❌ Gagal kirim ke ${jid}:`, err.message);

            recentDispatches.unshift({
                id            : Date.now() + Math.random().toString(36).substr(2, 4),
                jid,
                number        : numberOnly,
                recipientName : recipientName || (isGroupItem ? 'Grup WhatsApp Rig' : numberOnly),
                isGroup       : isGroupItem,
                status        : 'failed',
                error         : err.message,
                time          : new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }),
                timestamp     : Date.now()
            });
            if (recentDispatches.length > 50) recentDispatches.pop();

            if (resolve) resolve({ success: false, message: 'Gagal kirim: ' + err.message });
        }

        // 5. Jeda aman antar pesan
        if (messageQueue.length > 0) {
            // Jika pesan berikutnya adalah ke Group, jeda cukup 3-5 detik
            // Jika pesan berikutnya personal, berikan jeda santai 15–35 detik
            const nextIsGroup = messageQueue[0].jid.endsWith('@g.us');
            const delay = nextIsGroup 
                ? randomDelay(3000, 5000) 
                : randomDelay(ANTI_BAN.minDelay, ANTI_BAN.maxDelay);

            const nextName = messageQueue[0].recipientName || messageQueue[0].jid.split('@')[0];
            currentlyProcessing = {
                jid,
                number        : numberOnly,
                recipientName : recipientName || (isGroupItem ? 'Grup WhatsApp Rig' : numberOnly),
                status        : 'waiting_delay',
                statusText    : `Jeda Aman Anti-Ban (${Math.round(delay / 1000)}s)...`,
                delaySec      : Math.round(delay / 1000),
                nextRecipient : nextName
            };

            console.log(`[Anti-Ban] ⏳ Menunggu jeda aman ${(delay / 1000).toFixed(1)} detik sebelum memproses pesan berikutnya...`);
            await new Promise(r => setTimeout(r, delay));
        }
    }

    currentlyProcessing = null;
    isProcessingQueue   = false;
}

// ─── WhatsApp socket ──────────────────────────────────────────────────────────
async function startSock() {
    if (!fs.existsSync(SESSION_DIR)) {
        fs.mkdirSync(SESSION_DIR, { recursive: true });
    }

    const { state, saveCreds } = await useMultiFileAuthState(SESSION_DIR);
    const { version }          = await fetchLatestBaileysVersion();

    sock = makeWASocket({
        version,
        logger            : pino({ level: 'silent' }),
        auth              : state,
        browser           : ['Chrome', 'Desktop', '120.0.0'],
        retryRequestDelayMs : 2000,
        maxMsgRetryCount  : 3,
        generateHighQualityLinkPreview: false,
    });

    sock.ev.on('creds.update', saveCreds);

    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update;

        if (qr) {
            try {
                latestQr         = await QRCode.toDataURL(qr);
                connectionStatus = 'scan_qr_needed';
                isConnected      = false;
                console.log('[WhatsApp] ✅ QR Code siap. Buka browser dan scan QR sekarang.');
            } catch (err) {
                console.error('[WhatsApp] Gagal buat QR URL:', err.message);
            }
        }

        if (connection === 'close') {
            const reason = lastDisconnect?.error?.output?.statusCode;
            const shouldReconnect = reason !== DisconnectReason.loggedOut;
            isConnected      = false;
            connectionStatus = 'disconnected';
            console.log(`[WhatsApp] Koneksi terputus (kode: ${reason}). Reconnect: ${shouldReconnect}`);

            if (shouldReconnect) {
                setTimeout(startSock, 5000);
            } else {
                console.log('[WhatsApp] Logout. Menghapus sesi lama...');
                if (fs.existsSync(SESSION_DIR)) {
                    fs.rmSync(SESSION_DIR, { recursive: true, force: true });
                }
                latestQr = null;
                setTimeout(startSock, 3000);
            }

        } else if (connection === 'open') {
            isConnected      = true;
            latestQr         = null;
            connectionStatus = 'connected';
            const user = sock.user;
            if (user?.id) connectedPhone = user.id.split(':')[0] || '-';
            console.log(`[WhatsApp] ✅ Terhubung! Nomor pengirim: ${connectedPhone}`);

            // Proses antrian yang mungkin sudah menunggu
            if (messageQueue.length > 0) processQueue();
        }
    });
}

startSock().catch(console.error);

// ─── REST API ─────────────────────────────────────────────────────────────────

app.get('/status', (req, res) => {
    res.json({
        success      : true,
        status       : connectionStatus,
        connected    : isConnected,
        senderNumber : connectedPhone,
        qr           : latestQr,
        antiBan      : {
            sentThisHour,
            maxPerHour  : ANTI_BAN.maxPerHour,
            queueLength : messageQueue.length,
            minDelaySec : ANTI_BAN.minDelay / 1000,
            maxDelaySec : ANTI_BAN.maxDelay / 1000,
        },
        queue        : {
            isProcessing        : isProcessingQueue,
            currentlyProcessing : currentlyProcessing,
            waiting             : messageQueue.map((item, idx) => ({
                position      : idx + 1,
                jid           : item.jid,
                number        : item.jid.split('@')[0],
                recipientName : item.recipientName || item.jid.split('@')[0],
                isGroup       : item.jid.endsWith('@g.us'),
                estWaitSec    : Math.round(((idx + 1) * ((ANTI_BAN.minDelay + ANTI_BAN.maxDelay) / 2000)))
            })),
            history             : recentDispatches.slice(0, 25),
            totalSentSession    : recentDispatches.filter(d => d.status === 'sent').length,
            totalFailedSession  : recentDispatches.filter(d => d.status === 'failed').length
        },
        timestamp    : new Date().toISOString()
    });
});

app.get('/queue-status', (req, res) => {
    res.json({
        success             : true,
        isProcessing        : isProcessingQueue,
        currentlyProcessing : currentlyProcessing,
        waiting             : messageQueue.map((item, idx) => ({
            position      : idx + 1,
            jid           : item.jid,
            number        : item.jid.split('@')[0],
            recipientName : item.recipientName || item.jid.split('@')[0],
            isGroup       : item.jid.endsWith('@g.us'),
            estWaitSec    : Math.round(((idx + 1) * ((ANTI_BAN.minDelay + ANTI_BAN.maxDelay) / 2000)))
        })),
        history             : recentDispatches.slice(0, 25),
        totalSentSession    : recentDispatches.filter(d => d.status === 'sent').length
    });
});

app.get('/groups', async (req, res) => {
    if (!isConnected || !sock) {
        return res.status(503).json({ success: false, message: 'WhatsApp Gateway belum terhubung. Silakan scan QR terlebih dahulu.' });
    }
    try {
        const groups = await sock.groupFetchAllParticipating();
        const list = Object.values(groups).map(g => ({
            id: g.id,
            name: g.subject || 'Tanpa Nama',
            participantsCount: g.participants ? g.participants.length : 0
        }));
        return res.json({ success: true, data: list });
    } catch (err) {
        return res.status(500).json({ success: false, message: 'Gagal memuat grup WhatsApp: ' + err.message });
    }
});

app.get('/group-participants', async (req, res) => {
    let { groupId } = req.query;
    if (!groupId) {
        return res.status(400).json({ success: false, message: 'Parameter groupId wajib disertakan.' });
    }
    if (!isConnected || !sock) {
        return res.status(503).json({ success: false, message: 'WhatsApp Gateway belum terhubung. Silakan scan QR terlebih dahulu.' });
    }
    try {
        let cleanId = groupId.toString().trim();
        if (!cleanId.includes('@g.us')) cleanId += '@g.us';

        const metadata = await sock.groupMetadata(cleanId);
        const participants = (metadata.participants || []).map(p => {
            const targetJid = (p.jid && p.jid.includes('@s.whatsapp.net')) ? p.jid : p.id;
            const rawNumber = targetJid.split('@')[0];
            const phone08 = rawNumber.startsWith('62') ? '0' + rawNumber.substring(2) : rawNumber;
            return {
                jid: targetJid,
                number: rawNumber,
                phoneFormatted: phone08,
                isAdmin: p.admin === 'admin' || p.admin === 'superadmin'
            };
        });

        return res.json({
            success: true,
            groupId: metadata.id,
            groupName: metadata.subject || 'Grup WhatsApp',
            total: participants.length,
            participants: participants
        });
    } catch (err) {
        return res.status(500).json({ success: false, message: 'Gagal mengambil anggota grup: ' + err.message });
    }
});

app.post('/send-message', async (req, res) => {
    const { number, message, recipientName, async: isAsync } = req.body;

    if (!number || !message) {
        return res.status(400).json({ success: false, message: 'Parameter "number" dan "message" wajib diisi.' });
    }
    if (!isConnected || !sock) {
        return res.status(503).json({ success: false, message: 'WhatsApp belum terhubung. Scan QR terlebih dahulu.' });
    }

    const jid = normalizePhone(number);
    const isGroup = jid.endsWith('@g.us');

    if (!isGroup && sentThisHour >= ANTI_BAN.maxPerHour && messageQueue.length >= 25) {
        return res.status(429).json({ success: false, message: `Batas aman ${ANTI_BAN.maxPerHour} pesan/jam tercapai untuk menghindari skors/ban. Coba lagi beberapa saat lagi.` });
    }

    // Jika antrian sedang memproses atau diminta async (agar PHP tidak terkena curl timeout):
    if (isAsync || isProcessingQueue || messageQueue.length > 0) {
        messageQueue.push({ jid, message, recipientName: recipientName || null, resolve: null });
        processQueue();
        return res.json({
            success     : true,
            queued      : true,
            message     : `Pesan ke ${recipientName || number} dijadwalkan dalam Antrian Aman Anti-Ban (antrian #${messageQueue.length}).`,
            queueLength : messageQueue.length
        });
    }

    // Jika antrian kosong dan ini adalah pengiriman tunggal langsung (misal tes kirim):
    const result = await new Promise((resolve) => {
        messageQueue.push({ jid, message, recipientName: recipientName || null, resolve });
        processQueue();
    });

    return res.json(result);
});

app.post('/send-bulk', (req, res) => {
    const { items, numbers, message } = req.body;

    if (!isConnected || !sock) {
        return res.status(503).json({ success: false, message: 'Gateway Mandiri belum terhubung. Silakan scan QR terlebih dahulu.' });
    }

    let queuedCount = 0;

    // Format 1: Array of personalized items: [{ target, message, recipientName, crew_id }, ...]
    if (Array.isArray(items) && items.length > 0) {
        for (const it of items) {
            if (!it.target || !it.message) continue;
            const jid = normalizePhone(it.target);
            messageQueue.push({
                jid,
                message      : it.message,
                recipientName: it.recipientName || it.name || null,
                resolve      : null
            });
            queuedCount++;
        }
    } 
    // Format 2: Array of numbers with single message
    else if (Array.isArray(numbers) && message) {
        for (const num of numbers) {
            if (!num) continue;
            const jid = normalizePhone(num);
            messageQueue.push({
                jid,
                message,
                recipientName: null,
                resolve      : null
            });
            queuedCount++;
        }
    } else {
        return res.status(400).json({ success: false, message: 'Data pengiriman broadcast tidak valid.' });
    }

    processQueue();

    const avgDelaySec = (ANTI_BAN.minDelay + ANTI_BAN.maxDelay) / 2000;
    const estMinutes  = Math.max(1, Math.ceil((queuedCount * avgDelaySec) / 60));

    return res.json({
        success          : true,
        queued           : queuedCount,
        queueLength      : messageQueue.length,
        estimatedMinutes : estMinutes,
        message          : `${queuedCount} pesan personil berhasil dimasukkan ke Antrian Aman Anti-Ban (jeda acak 15–35 detik per orang). Selesai dalam ~${estMinutes} menit.`
    });
});

app.post('/logout', (req, res) => {
    try {
        if (sock) sock.logout();
        if (fs.existsSync(SESSION_DIR)) fs.rmSync(SESSION_DIR, { recursive: true, force: true });
        isConnected      = false;
        latestQr         = null;
        connectionStatus = 'disconnected';
        setTimeout(startSock, 2000);
        return res.json({ success: true, message: 'Sesi WhatsApp berhasil diputus.' });
    } catch (err) {
        return res.status(500).json({ success: false, message: 'Gagal logout: ' + err.message });
    }
});

app.listen(PORT, () => {
    console.log(`\n╔═══════════════════════════════════════════════════╗`);
    console.log(`║  Besmindo WhatsApp Gateway — Anti-Ban Edition     ║`);
    console.log(`║  http://localhost:${PORT}                            ║`);
    console.log(`║  Maks ${ANTI_BAN.maxPerHour} pesan/jam | Jeda ${ANTI_BAN.minDelay/1000}–${ANTI_BAN.maxDelay/1000} detik/pesan  ║`);
    console.log(`╚═══════════════════════════════════════════════════╝\n`);
    console.log('[Gateway] Menginisialisasi koneksi WhatsApp...');
});
