<?php
$bulanId = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
if (!function_exists('tglId')) {
    function tglId(string $datetime, array $bulan): string {
        $ts = strtotime($datetime);
        return date('d', $ts) . ' ' . $bulan[(int)date('n', $ts)] . ' ' . date('Y, H:i', $ts);
    }
}

$roleBadge = [
    'operator'        => ['label' => 'OP',      'style' => 'background:#cfe2ff;color:#084298;'],
    'wali_kelas'      => ['label' => 'Walas',   'style' => 'background:#d1e7dd;color:#0a3622;'],
    'bk'              => ['label' => 'BK',      'style' => 'background:#e8d5ff;color:#432874;'],
    'tu'              => ['label' => 'TU',      'style' => 'background:#ffe5d0;color:#7c3c00;'],
    'wakasek'         => ['label' => 'Wakasek', 'style' => 'background:#cff4fc;color:#055160;'],
    'kepala_sekolah'  => ['label' => 'Kepsek',  'style' => 'background:#f8d7da;color:#842029;'],
];

// Query string filter aktif (untuk pagination & link)
$filterQuery = http_build_query(array_filter([
    'tahun_ajaran' => $tahunAjaran,
    'jurusan'      => $jurusan,
    'limit'        => ($limit ?? 10) !== 10 ? $limit : '',
]));
$baseUrl = url('perhitungan/eligible') . ($filterQuery ? '&' . $filterQuery : '');
$from = ($total ?? 0) > 0 ? ($offset ?? 0) + 1 : 0;
$to   = min(($offset ?? 0) + ($limit ?? 10), ($total ?? 0));
?>
<link rel="stylesheet" href="<?= e(asset('assets/css/pages/perhitungan.css')); ?>">

<!-- Judul Halaman -->
<div class="page-header">
    <h2 class="page-title">Perhitungan Peringkat Eligible (SNBP)</h2>
</div>

<!-- Filter Section -->
<?php include __DIR__ . '/filter.php'; ?>

<!-- Riwayat Section -->
<?php include __DIR__ . '/riwayat.php'; ?>

<!-- Panel Seleksi Pendaftar (toggle via JS) -->
<?php include __DIR__ . '/seleksi.php'; ?>

<!-- Hasil Section (If Selected) -->
<?php if ($hasilDipilih): ?>
    <?php 
    $riwayat = $hasilDipilih['riwayat'];
    $hasil   = $hasilDipilih['hasil'];
    include __DIR__ . '/hasil.php'; 
    ?>
<?php endif; ?>


<!-- Modals Section -->
<?php include __DIR__ . '/modals.php'; ?>
<?php include __DIR__ . '/modal-pdss.php'; ?>

<!-- Script & Style dipindahkan ke perhitungan.css & perhitungan-eligible.js -->
<script src="<?= e(asset('assets/js/pages/perhitungan-eligible.js')); ?>"></script>

