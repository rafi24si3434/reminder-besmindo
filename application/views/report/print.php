<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) : 'Laporan Kehadiran Meeting' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-white text-slate-900 p-8 text-xs antialiased max-w-5xl mx-auto">

    <div class="no-print mb-6 p-4 bg-slate-100 rounded-xl border border-slate-200 flex items-center justify-between">
        <div class="text-xs text-slate-600">
            <i class="fa-solid fa-print mr-1"></i> Preview Laporan Resmi Cetak. Tekan tombol cetak di samping untuk menyimpan PDF atau mencetak.
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg font-bold text-xs shadow">
                <i class="fa-solid fa-print mr-1.5"></i> Cetak / Simpan PDF
            </button>
            <button onclick="window.close()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-xs font-semibold">
                Tutup
            </button>
        </div>
    </div>

    <div class="border-b-2 border-slate-900 pb-4 mb-6 flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <img src="<?= base_url('assets/images/logo_besmindo_light.png') ?>" alt="PT Besmindo Materi Sewatama" class="h-12 w-auto object-contain">
            <div>
                <h1 class="text-base font-black tracking-wider text-slate-900">PT. BESMINDO MATERI SEWATAMA</h1>
                <p class="text-[10px] text-slate-600 font-medium">Oilfield Equipment Sales & Rental, Drilling & Work Over Rig Services</p>
                <p class="text-[10px] text-slate-500">Light Vehicle & Logistics Yard Duri, Riau | https://besmindoms.com</p>
            </div>
        </div>
        <div class="text-right text-[11px] text-slate-600">
            <p class="font-bold text-slate-900">REKAPITULASI KEHADIRAN MEETING RIG</p>
            <p>Tanggal Cetak: <?= date('d F Y, H:i') ?> WIB</p>
            <p>Status: Dokumen Resmi Operasional</p>
        </div>
    </div>

    <div class="grid grid-cols-4 gap-3 mb-6 p-3 bg-slate-50 rounded-lg border border-slate-200 text-center">
        <div>
            <span class="text-[10px] text-slate-500 uppercase font-semibold">Total Catatan Absensi</span>
            <div class="text-lg font-bold text-slate-900"><?= $stats['total'] ?></div>
        </div>
        <div>
            <span class="text-[10px] text-emerald-700 uppercase font-semibold">Total Hadir</span>
            <div class="text-lg font-bold text-emerald-700"><?= $stats['hadir'] ?></div>
        </div>
        <div>
            <span class="text-[10px] text-blue-700 uppercase font-semibold">Izin / Sakit</span>
            <div class="text-lg font-bold text-blue-700"><?= $stats['izin'] ?></div>
        </div>
        <div>
            <span class="text-[10px] text-rose-700 uppercase font-semibold">Tidak Hadir (Alpha)</span>
            <div class="text-lg font-bold text-rose-700"><?= $stats['tidak_hadir'] ?></div>
        </div>
        <div>
            <span class="text-[10px] text-sky-700 uppercase font-semibold">Persentase Kehadiran</span>
            <div class="text-lg font-bold text-sky-700"><?= $stats['percentage'] ?>%</div>
        </div>
    </div>

    <table class="w-full text-left text-[10px] border border-slate-300 mb-8">
        <thead class="bg-slate-100 text-slate-800 uppercase font-bold border-b border-slate-300">
            <tr>
                <th class="p-2 border-r border-slate-300 w-8 text-center">No</th>
                <th class="p-2 border-r border-slate-300">Tanggal / Jam</th>
                <th class="p-2 border-r border-slate-300">Nama Meeting & Rig</th>
                <th class="p-2 border-r border-slate-300">NIK & Nama Crew</th>
                <th class="p-2 border-r border-slate-300">Jabatan</th>
                <th class="p-2 border-r border-slate-300 text-center">Status</th>
                <th class="p-2 border-r border-slate-300">Join Time</th>
                <th class="p-2">Durasi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            <?php $no = 1; foreach ($records as $r): ?>
                <tr>
                    <td class="p-2 border-r border-slate-200 text-center font-mono"><?= $no++ ?></td>
                    <td class="p-2 border-r border-slate-200">
                        <?= date('d/m/Y', strtotime($r['meeting_date'])) ?> <?= substr($r['start_time'], 0, 5) ?>
                    </td>
                    <td class="p-2 border-r border-slate-200">
                        <strong class="text-slate-900"><?= htmlspecialchars($r['meeting_title']) ?></strong>
                        <span class="block text-slate-500 text-[9px]"><?= htmlspecialchars($r['rig_name']) ?></span>
                    </td>
                    <td class="p-2 border-r border-slate-200">
                        <strong><?= htmlspecialchars($r['crew_name']) ?></strong>
                        <span class="block text-slate-500 text-[9px]"><?= htmlspecialchars($r['nik']) ?></span>
                    </td>
                    <td class="p-2 border-r border-slate-200"><?= htmlspecialchars($r['position']) ?></td>
                    <td class="p-2 border-r border-slate-200 text-center font-bold">
                        <?php if ($r['status'] === 'HADIR'): ?>
                            <span class="text-emerald-700">HADIR</span>
                        <?php elseif ($r['status'] === 'IZIN'): ?>
                            <span class="text-blue-700">IZIN</span>
                        <?php elseif ($r['status'] === 'TIDAK_HADIR'): ?>
                            <span class="text-rose-700">TIDAK HADIR</span>
                        <?php else: ?>
                            <span class="text-slate-500">BELUM HADIR</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-2 border-r border-slate-200 font-mono"><?= $r['join_time'] ? date('H:i:s', strtotime($r['join_time'])) : '-' ?></td>
                    <td class="p-2"><?= $r['duration_minutes'] > 0 ? $r['duration_minutes'] . ' m' : '-' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="grid grid-cols-2 gap-8 text-center text-xs mt-12 pt-6">
        <div>
            <p class="text-slate-600 mb-16">Mengetahui,<br><strong>Penanggung Jawab Rig (PJ)</strong></p>
            <div class="w-48 mx-auto border-b border-slate-900 pb-1 font-bold">Agus Kurniawan, S.T.</div>
            <p class="text-[10px] text-slate-500">Rig Superintendent</p>
        </div>
        <div>
            <p class="text-slate-600 mb-16">Disetujui oleh,<br><strong>Manager Operasional Rig</strong></p>
            <div class="w-48 mx-auto border-b border-slate-900 pb-1 font-bold"><?= htmlspecialchars($this->session->userdata('name') ?: 'Manager Operasional') ?></div>
            <p class="text-[10px] text-slate-500">PT Besmindo Oilfield Operations</p>
        </div>
    </div>

</body>
</html>
