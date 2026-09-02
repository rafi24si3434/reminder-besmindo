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
    minDelay     : 4000,  // min jeda antar pesan (ms)
    maxDelay     : 9000,  // max jeda antar pesan (ms)
    maxPerHour   : 50,    // maks pesan per jam
    presenceDelay: 1500,  // durasi simulasi "mengetik" (ms)
};

let sentThisHour   = 0;
let hourResetTimer = null;

function resetHourCounter() {
    sentThisHour = 0;
    console.log('[Anti-Ban] Counter jam direset. Siap kirim lagi.');
}

// ─── State ────────────────────────────────────────────────────────────────────
let sock             = null;
let latestQr         = null;
let isConnected      = false;
let connectedPhone   = '085148410891';
let connectionStatus = 'initializing';

// ─── Message queue ────────────────────────────────────────────────────────────
let messageQueue      = [];
let isProcessingQueue = false;

const CLOSINGS = [
    '_Management PT Besmindo Oilfield Operations_',
    '_Tim Operasional PT Besmindo_',
    '_Salam, Manajemen Besmindo_',
    '_Hormat kami, PT Besmindo Oilfield Operations_',
    '_PT Besmindo — Divisi Operasional Rig_',
];

function randomClosing() {
    return CLOSINGS[Math.floor(Math.random() * CLOSINGS.length)];
}

function randomDelay(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function humanizeMessage(message) {
    return message
        .replace(/_Management PT Besmindo Oilfield Operations_/g, randomClosing())
        .replace(/_PT Besmindo Oilfield Operations_/g, randomClosing());
}

function normalizePhone(number) {
    let clean = number.toString().replace(/[^0-9]/g, '');
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
            console.log('[Queue] Gateway belum terhubung, antrian ditahan.');
            break;
        }
        if (sentThisHour >= ANTI_BAN.maxPerHour) {
            console.log(`[Anti-Ban] Batas ${ANTI_BAN.maxPerHour} pesan/jam tercapai. Menunggu reset...`);
            break;
        }

        const { jid, message, resolve } = messageQueue.shift();

        try {
            // Simulasi "sedang mengetik"
            await sock.sendPresenceUpdate('composing', jid);
            await new Promise(r => setTimeout(r, ANTI_BAN.presenceDelay));
            await sock.sendPresenceUpdate('paused', jid);

            const result = await sock.sendMessage(jid, { text: humanizeMessage(message) });
            sentThisHour++;

            console.log(`[Queue] ✅ Terkirim ke ${jid} (${sentThisHour}/${ANTI_BAN.maxPerHour} pesan jam ini)`);

            if (!hourResetTimer) {
                hourResetTimer = setTimeout(() => {
                    resetHourCounter();
                    hourResetTimer = null;
                }, 3600 * 1000);
            }

            if (resolve) resolve({ success: true, message: `Pesan terkirim ke ${jid}`, result });

        } catch (err) {
            console.error(`[Queue] ❌ Gagal kirim ke ${jid}:`, err.message);
            if (resolve) resolve({ success: false, message: 'Gagal kirim: ' + err.message });
        }

        if (messageQueue.length > 0) {
            const delay = randomDelay(ANTI_BAN.minDelay, ANTI_BAN.maxDelay);
            console.log(`[Anti-Ban] Menunggu ${(delay / 1000).toFixed(1)} detik sebelum pesan berikutnya...`);
            await new Promise(r => setTimeout(r, delay));
        }
    }

    isProcessingQueue = false;
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
            if (user?.id) connectedPhone = user.id.split(':')[0] || '085148410891';
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
        timestamp    : new Date().toISOString()
    });
});

app.post('/send-message', async (req, res) => {
    const { number, message } = req.body;

    if (!number || !message) {
        return res.status(400).json({ success: false, message: 'Parameter "number" dan "message" wajib diisi.' });
    }
    if (!isConnected || !sock) {
        return res.status(503).json({ success: false, message: 'WhatsApp belum terhubung. Scan QR terlebih dahulu.' });
    }
    if (sentThisHour >= ANTI_BAN.maxPerHour) {
        return res.status(429).json({ success: false, message: `Batas ${ANTI_BAN.maxPerHour} pesan/jam tercapai. Tunggu beberapa menit.` });
    }

    const jid = normalizePhone(number);

    const result = await new Promise((resolve) => {
        messageQueue.push({ jid, message, resolve });
        processQueue();
    });

    return res.json(result);
});

app.post('/send-bulk', (req, res) => {
    const { numbers, message } = req.body;

    if (!Array.isArray(numbers) || numbers.length === 0 || !message) {
        return res.status(400).json({ success: false, message: 'Parameter "numbers" (array) dan "message" wajib diisi.' });
    }
    if (!isConnected || !sock) {
        return res.status(503).json({ success: false, message: 'Gateway belum terhubung.' });
    }

    const remaining = ANTI_BAN.maxPerHour - sentThisHour;
    if (remaining <= 0) {
        return res.status(429).json({ success: false, message: 'Batas pesan/jam tercapai. Coba lagi nanti.' });
    }

    let queued = 0;
    for (const number of numbers) {
        if (queued >= remaining) break;
        const jid = normalizePhone(number);
        messageQueue.push({ jid, message, resolve: null });
        queued++;
    }
    processQueue();

    return res.json({
        success          : true,
        message          : `${queued} pesan masuk antrian (jeda otomatis ${ANTI_BAN.minDelay/1000}–${ANTI_BAN.maxDelay/1000} detik/pesan).`,
        queued,
        estimatedMinutes : Math.ceil((queued * ANTI_BAN.maxDelay) / 60000)
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
