<?php
/**
 * @var string   $tahunAjaran
 * @var string   $kelas
 * @var string   $semester
 * @var int      $semesterTarget
 * @var array[]  $optTahunAjaran
 * @var array[]  $optKelas
 * @var array[]  $riwayat
 * @var int      $total
 * @var int      $totalPages
 * @var int      $page
 * @var int      $limit
 * @var int      $offset
 * @var array|null $hasilDipilih
 * @var int      $idPerhitungan
 */

$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
// Label Semester
$labelSemester = [
    1 => 'Ganjil', 2 => 'Genap',
    3 => 'Ganjil', 4 => 'Genap',
    5 => 'Ganjil', 6 => 'Genap',
];

// Bulan ID
$bulanId = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
if (!function_exists('tglId')) {
    function tglId(string $datetime, array $bulan): string {
        $ts = strtotime($datetime);
        return date('d', $ts) . ' ' . $bulan[(int)date('n', $ts)] . ' ' . date('Y, H:i', $ts);
    }
}

// Role Badge
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
    'kelas'        => $kelas,
    'semester'     => $semester,
    'limit'        => $limit !== 10 ? $limit : '',
]));
// Base URL
$baseUrl = url('perhitungan/kelas') . ($filterQuery ? '&' . $filterQuery : '');
// From and To
$from = $total > 0 ? $offset + 1 : 0;
$to   = min($offset + $limit, $total);
?>
<link rel="stylesheet" href="<?= e(asset('assets/css/pages/perhitungan.css')); ?>">


<!-- Judul Halaman -->
<div class="page-header">
    <h2 class="page-title">Perhitungan Peringkat Kelas</h2>
</div>

<!-- Informasi Filter & Log -->
<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <?= e($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php 
// 1. Filter Form
include __DIR__ . '/filter.php'; 

// 2. Tabel Riwayat (Log)
include __DIR__ . '/riwayat.php'; 

// 3. Tabel Hasil Peringkat (jika dipilih)
include __DIR__ . '/hasil.php'; 

// 4. Modals & Scripts
include __DIR__ . '/modals.php'; 
?>

<!-- Script dipindahkan ke perhitungan-kelas.js -->
<script src="<?= e(asset('assets/js/pages/perhitungan-kelas.js')); ?>"></script>
