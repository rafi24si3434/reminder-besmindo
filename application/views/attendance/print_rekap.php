<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f1f5f9;
            color: #0f172a;
            font-size: 12px;
            padding: 24px;
        }

        .paper {
            background: #ffffff;
            max-width: 900px;
            margin: 0 auto;
            padding: 36px 44px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            border-radius: 8px;
        }

        /* Kop Surat */
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px double #0f172a;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }

        .kop-logo {
            width: 130px;
            margin-right: 20px;
        }

        .kop-logo img {
            max-width: 100%;
            height: auto;
        }

        .kop-text {
            flex: 1;
            text-align: center;
        }

        .kop-text h2 {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #0f172a;
            text-transform: uppercase;
        }

        .kop-text h3 {
            font-size: 13px;
            font-weight: 700;
            color: #0284c7;
            margin: 2px 0;
        }

        .kop-text p {
            font-size: 10px;
            color: #475569;
            line-height: 1.4;
        }

        /* Judul Dokumen */
        .doc-title {
            text-align: center;
            margin-bottom: 18px;
        }

        .doc-title h1 {
            font-size: 15px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: underline;
        }

        .doc-title p {
            font-size: 11px;
            color: #64748b;
            margin-top: 3px;
        }

        /* Meta Box */
        .meta-table {
            width: 100%;
            margin-bottom: 18px;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 4px 8px;
            vertical-align: top;
            font-size: 11px;
        }

        .meta-table td.label {
            font-weight: 600;
            width: 140px;
            color: #334155;
        }

        .meta-table td.separator {
            width: 10px;
            color: #64748b;
        }

        .meta-table td.value {
            color: #0f172a;
        }

        /* KPI Box */
        .kpi-row {
            display: flex;
            gap: 10px;
            margin-bottom: 18px;
        }

        .kpi-box {
            flex: 1;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            text-align: center;
            background: #f8fafc;
        }

        .kpi-box .num {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
        }

        .kpi-box .lbl {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            color: #475569;
            margin-top: 2px;
        }

        /* Table Presensi */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }

        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
        }

        table.data-table th {
            background-color: #f1f5f9;
            font-weight: 700;
            text-align: left;
            text-transform: uppercase;
            font-size: 10px;
            color: #334155;
        }

        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 700;
            text-align: center;
        }

        .badge-hadir { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .badge-terlambat { background: #fef3c7; color: #b45309; border: 1px solid #fcd34d; }
        .badge-izin { background: #e0f2fe; color: #0369a1; border: 1px solid #7dd3fc; }
        .badge-tidak { background: #ffe4e6; color: #be123c; border: 1px solid #fca5a5; }

        /* Notulensi Box */
        .notulensi-box {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 12px 16px;
            background: #fafafa;
            margin-bottom: 24px;
        }

        .notulensi-box h4 {
            font-size: 11px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            margin-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }

        .notulensi-box p {
            font-size: 11px;
            line-height: 1.5;
            color: #334155;
            white-space: pre-line;
        }

        /* Tanda Tangan */
        .sign-row {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .sign-col {
            width: 240px;
            text-align: center;
            font-size: 11px;
        }

        .sign-space {
            height: 70px;
        }

        .sign-name {
            font-weight: 700;
            text-decoration: underline;
            color: #0f172a;
        }

        .sign-role {
            font-size: 10px;
            color: #64748b;
        }

        /* Floating Action */
        .print-actions {
            position: fixed;
            bottom: 24px;
            right: 24px;
            display: flex;
            gap: 10px;
            background: rgba(15, 23, 42, 0.9);
            padding: 10px 16px;
            border-radius: 9999px;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.3);
            backdrop-filter: blur(4px);
        }

        .btn-print {
            background: #0284c7;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-print:hover {
            background: #0369a1;
        }

        .btn-back {
            background: #334155;
            color: #f8fafc;
            border: none;
            padding: 8px 16px;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 12px;
            text-decoration: none;
        }

        .btn-back:hover {
            background: #475569;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .paper {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .print-actions {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="paper">
        <!-- Kop Surat Resmi -->
        <div class="kop-surat">
            <div class="kop-logo">
                <img src="<?= base_url('assets/images/logo_besmindo.png') ?>" alt="PT Besmindo Materi Sewatama">
            </div>
            <div class="kop-text">
                <h2>PT. BESMINDO MATERI SEWATAMA</h2>
                <h3>OILFIELD DRILLING &amp; RENTAL SERVICES</h3>
                <p>
                    Jl. Hang Kesturi I No. 1, Kawasan Industri Batamindo, Mukakuning, Batam - Indonesia<br>
                    Website: https://besmindoms.com &bull; Email: ops@besmindo.com
                </p>
            </div>
        </div>

        <!-- Judul Laporan -->
        <div class="doc-title">
            <h1>BERITA ACARA &amp; REKAPITULASI PRESENSI RAPAT</h1>
            <p>Nomor Dokumen: BSM/BA-MTG/<?= date('Ym', strtotime($meeting['meeting_date'])) ?>/<?= str_pad($meeting['id'], 4, '0', STR_PAD_LEFT) ?></p>
        </div>

        <!-- Meta Informasi Rapat -->
        <table class="meta-table">
            <tr>
                <td class="label">Unit Rig Operasional</td>
                <td class="separator">:</td>
                <td class="value"><strong><?= htmlspecialchars($meeting['rig_name']) ?></strong> (Kode: <?= htmlspecialchars($meeting['rig_code']) ?>)</td>
                <td class="label">Tanggal Pelaksanaan</td>
                <td class="separator">:</td>
                <td class="value"><?= date('d F Y', strtotime($meeting['meeting_date'])) ?></td>
            </tr>
            <tr>
                <td class="label">Agenda / Rapat</td>
                <td class="separator">:</td>
                <td class="value"><strong><?= htmlspecialchars($meeting['title']) ?></strong></td>
                <td class="label">Waktu Pertemuan</td>
                <td class="separator">:</td>
                <td class="value"><?= substr($meeting['start_time'], 0, 5) ?> - <?= substr($meeting['end_time'], 0, 5) ?> WIB</td>
            </tr>
            <tr>
                <td class="label">Topik Bahasan</td>
                <td class="separator">:</td>
                <td class="value"><?= htmlspecialchars($meeting['topic']) ?></td>
                <td class="label">Penanggung Jawab / Host</td>
                <td class="separator">:</td>
                <td class="value"><?= htmlspecialchars(!empty($meeting['pj_name']) ? $meeting['pj_name'] : 'Management Besmindo') ?></td>
            </tr>
            <tr>
                <td class="label">Status Sesi Rapat</td>
                <td class="separator">:</td>
                <td class="value">
                    <strong><?= ($meeting['status'] === 'completed') ? 'RESMI DITUTUP (COMPLETED)' : 'MASIH BERLANGSUNG' ?></strong>
                    <?php if (!empty($meeting['closed_at'])): ?>
                        (Ditutup: <?= date('d/m/Y H:i', strtotime($meeting['closed_at'])) ?> WIB)
                    <?php endif; ?>
                </td>
                <td class="label">Platform Rapat</td>
                <td class="separator">:</td>
                <td class="value">Microsoft Teams Online Meeting</td>
            </tr>
        </table>

        <!-- Ringkasan Statistik Presensi -->
        <div class="kpi-row">
            <div class="kpi-box">
                <div class="num"><?= $stats['total'] ?></div>
                <div class="lbl">Total Undangan</div>
            </div>
            <div class="kpi-box" style="border-color: #86efac; background: #f0fdf4;">
                <div class="num" style="color: #15803d;"><?= $stats['hadir'] ?></div>
                <div class="lbl" style="color: #15803d;">Hadir Tepat Waktu</div>
            </div>
            <div class="kpi-box" style="border-color: #fcd34d; background: #fffbeb;">
                <div class="num" style="color: #b45309;"><?= $stats['terlambat'] ?></div>
                <div class="lbl" style="color: #b45309;">Terlambat</div>
            </div>
            <div class="kpi-box" style="border-color: #7dd3fc; background: #f0f9ff;">
                <div class="num" style="color: #0369a1;"><?= $stats['izin'] ?></div>
                <div class="lbl" style="color: #0369a1;">Izin / Dispensasi</div>
            </div>
            <div class="kpi-box" style="border-color: #fca5a5; background: #fff1f2;">
                <div class="num" style="color: #be123c;"><?= $stats['tidak_hadir'] ?></div>
                <div class="lbl" style="color: #be123c;">Tidak Hadir (Alpha)</div>
            </div>
            <div class="kpi-box" style="border-color: #c7d2fe; background: #eef2ff;">
                <div class="num" style="color: #4338ca;"><?= $stats['percentage'] ?>%</div>
                <div class="lbl" style="color: #4338ca;">Tingkat Kehadiran</div>
            </div>
        </div>

        <!-- Notulensi / Hasil Keputusan Rapat -->
        <div class="notulensi-box">
            <h4>Notulensi, Kesimpulan &amp; Tindak Lanjut Rapat:</h4>
            <?php if (!empty($meeting['meeting_notes'])): ?>
                <p><?= htmlspecialchars($meeting['meeting_notes']) ?></p>
            <?php else: ?>
                <p style="color: #94a3b8; font-style: italic;">(Tidak ada catatan notulensi khusus yang dimasukkan)</p>
            <?php endif; ?>
        </div>

        <!-- Tabel Roster Absensi -->
        <h4 style="font-size: 11px; text-transform: uppercase; margin-bottom: 6px; color: #1e293b;">
            Daftar Presensi Personil Crew (<?= count($attendances) ?> Orang):
        </h4>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 30px; text-align: center;">No</th>
                    <th>Nama Personil</th>
                    <th>Jabatan</th>
                    <th>No. Telepon / WA</th>
                    <th style="width: 100px; text-align: center;">Status</th>
                    <th style="width: 80px; text-align: center;">Waktu Masuk</th>
                    <th style="width: 60px; text-align: center;">Durasi</th>
                    <th>Keterangan / Sumber</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($attendances)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: #94a3b8; padding: 14px;">Tidak ada data personil.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($attendances as $att): ?>
                        <tr>
                            <td style="text-align: center;"><?= $no++ ?></td>
                            <td><strong><?= htmlspecialchars($att['crew_name']) ?></strong></td>
                            <td><?= htmlspecialchars($att['position']) ?></td>
                            <td><?= htmlspecialchars($att['phone']) ?></td>
                            <td style="text-align: center;">
                                <?php if ($att['status'] === 'HADIR'): ?>
                                    <span class="badge badge-hadir">HADIR</span>
                                <?php elseif ($att['status'] === 'TERLAMBAT'): ?>
                                    <span class="badge badge-terlambat">TERLAMBAT</span>
                                <?php elseif ($att['status'] === 'IZIN'): ?>
                                    <span class="badge badge-izin">IZIN</span>
                                <?php elseif ($att['status'] === 'TIDAK_HADIR'): ?>
                                    <span class="badge badge-tidak">TIDAK HADIR</span>
                                <?php else: ?>
                                    <span class="badge" style="background:#e2e8f0; color:#475569;">BELUM HADIR</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;"><?= !empty($att['join_time']) ? date('H:i:s', strtotime($att['join_time'])) : '-' ?></td>
                            <td style="text-align: center;"><?= !empty($att['duration_minutes']) ? $att['duration_minutes'] . ' mnt' : '-' ?></td>
                            <td style="font-size: 10px; color: #475569;">
                                <?= !empty($att['notes']) ? htmlspecialchars($att['notes']) : '-' ?>
                                <?php if (!empty($att['source'])): ?>
                                    (<?= $att['source'] ?>)
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Lembar Tanda Tangan Formal -->
        <div class="sign-row">
            <div class="sign-col">
                <p>Mengetahui,</p>
                <p><strong>Penanggung Jawab Rig (Toolpusher / Rig Super)</strong></p>
                <div class="sign-space"></div>
                <p class="sign-name"><?= htmlspecialchars(!empty($meeting['pj_name']) ? $meeting['pj_name'] : '( ........................................ )') ?></p>
                <p class="sign-role"><?= htmlspecialchars($meeting['rig_name']) ?></p>
            </div>
            <div class="sign-col">
                <p>Batam, <?= date('d F Y', strtotime($meeting['meeting_date'])) ?></p>
                <p><strong>Operasional PT. Besmindo Materi Sewatama</strong></p>
                <div class="sign-space"></div>
                <p class="sign-name">( Ir. H. Bambang Sugiarto )</p>
                <p class="sign-role">Operations &amp; Rig Fleet Manager</p>
            </div>
        </div>
    </div>

    <!-- Floating Print Action -->
    <div class="print-actions">
        <button type="button" class="btn-print" onclick="window.print()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><path d="M6 14h12v8H6z"/></svg>
            <span>Cetak Dokumen (Ctrl + P)</span>
        </button>
        <a href="<?= base_url('attendance/rekap/' . $meeting['id']) ?>" class="btn-back">
            &larr; Kembali ke Rekap
        </a>
    </div>

</body>
</html>
