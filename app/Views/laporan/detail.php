<?php
[$active, $subActive] = resolveMenu();
$bulanId = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
// fungsi tglIdTimeWIB
function tglIdTimeWIB($datetimeStr, $bulanId) {
    if (!$datetimeStr) return '-';
    $time = strtotime($datetimeStr);
    $tgl = date('d', $time);
    $bln = $bulanId[(int)date('n', $time)];
    $thn = date('Y', $time);
    $jam = date('H:i:s', $time);
    return "$tgl $bln $thn, $jam WIB";
}

if ($laporan['jenis_laporan'] === 'leger') {
    $jenisLabel = 'Leger Nilai';
    $meta = json_decode($laporan['komponen_laporan'], true);
    if (!empty($meta)) {
        $tahunAjaran = $meta['ta'] ?? '—';
        $kelasJurusanLabel = ($meta['kelas'] ?? '—') . ' (' . ucfirst($meta['sem'] ?? '') . ')';
    } else {
        $filename = basename($laporan['file_path']);
        $parts = explode('_', $filename);
        $tahunAjaran = isset($parts[1]) ? str_replace('-', '/', $parts[1]) : '—';
        $rawKelas = isset($parts[2]) ? str_replace('-', ' ', $parts[2]) : '—';
        $rawSem   = isset($parts[3]) ? ucfirst($parts[3]) : '';
        $kelasJurusanLabel = $rawKelas . ($rawSem ? " ($rawSem)" : "");
    }
} else {
    $jenisLabel = $laporan['jenis_laporan'] === 'kelas' ? 'Peringkat Kelas' : 'Peringkat Eligible';
    $kelasJurusanLabel = $laporan['jenis_laporan'] === 'kelas' ? $laporan['batch_kelas'] : $laporan['jurusan'];
    $tahunAjaran = $laporan['tahun_ajaran'];
}
$judulFile = basename($laporan['file_path']);
?>

<!-- Header Halaman Laporan Detail -->
<div class="page-header d-flex align-items-center gap-3 mb-4">
    <a href="<?= url('laporan') ?>" class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px; padding: 0;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h2 class="page-title mb-0" style="font-size: 1.75rem; font-weight: 600; color: #1e293b;">Laporan</h2>
</div>

<div class="detail-container mx-auto" style="max-width: 1200px;">
    <!-- Box Title Detail Laporan -->
    <div class="header-box mb-4 text-center fw-bold shadow" style="background-color: #e0f2f1; color: #00838f; border-radius: 6px; padding: 0.85rem; font-size: 1.35rem;">
        Detail Laporan
    </div>

    <!-- Kotak Putih Pembungkus Informasi & Preview -->
    <div class="card border-0 shadow-sm p-4" style="border-radius: 8px;">
        <!-- Metadata Laporan -->
        <div class="metadata-section mb-4 text-dark" style="font-size: 0.95rem; line-height: 1.8;">
            <div class="d-flex">
                <div class="fw-bold" style="width: 140px;">Nama File</div>
                <div style="width: 20px;">:</div>
                <div><?= e($judulFile) ?></div>
            </div>
            <div class="d-flex">
                <div class="fw-bold" style="width: 140px;">Jenis Laporan</div>
                <div style="width: 20px;">:</div>
                <div><?= $jenisLabel ?></div>
            </div>
            <div class="d-flex">
                <div class="fw-bold" style="width: 140px;">Tahun Ajaran</div>
                <div style="width: 20px;">:</div>
                <div><?= e($tahunAjaran) ?></div>
            </div>
            <div class="d-flex">
                <div class="fw-bold" style="width: 140px;">Kelas/Jurusan</div>
                <div style="width: 20px;">:</div>
                <div><?= e($kelasJurusanLabel) ?></div>
            </div>
            <div class="d-flex">
                <div class="fw-bold" style="width: 140px;">Dibuat pada</div>
                <div style="width: 20px;">:</div>
                <div><?= tglIdTimeWIB($laporan['tanggal_buat'], $bulanId) ?></div>
            </div>
        </div>

        <!-- PDF Viewer -->
        <div class="pdf-viewer-wrapper shadow-sm rounded border" style="background: #525659; overflow: hidden;">
            <!-- Browser akan otomatis menggunakan native PDF viewer -->
            <iframe src="<?= asset($laporan['file_path']) ?>#toolbar=1&navpanes=1&scrollbar=1" 
                    width="100%" 
                    style="height: 80vh; min-height: 600px; border: none; display: block;" 
                    title="<?= e($judulFile) ?>">
            </iframe>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?= e(asset('assets/css/pages/laporan.css')); ?>">

