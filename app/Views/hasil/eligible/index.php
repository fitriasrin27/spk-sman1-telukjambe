<?php
/**
 * @var string $title
 * @var string $tahunAjaran
 * @var string $jurusan
 * @var array  $riwayat
 * @var int    $total
 * @var int    $totalPages
 * @var int    $page
 * @var int    $limit
 * @var int    $offset
 * @var array  $optTahunAjaran
 */

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

$filterQuery = http_build_query(array_filter([
    'tahun_ajaran' => $tahunAjaran,
    'jurusan'      => $jurusan,
    'limit'        => $limit !== 10 ? $limit : '',
]));
$baseUrl = url('hasil/eligible') . ($filterQuery ? '&' . $filterQuery : '');

$from = $total > 0 ? $offset + 1 : 0;
$to   = min($offset + $limit, $total);
?>

<!-- JUDUL HALAMAN -->
<div class="page-header">
    <h2 class="page-title"><?= e($title) ?></h2>
</div>

<!-- FILTER -->
<div class="mb-4">
    <form method="GET" action="<?= e(url('')) ?>">
        <input type="hidden" name="url" value="hasil/eligible">
        <div class="row g-2 align-items-end">
            <!-- Dropdown Tahun Ajaran -->
            <div class="col-auto">
                <select name="tahun_ajaran" class="form-select form-select-sm" style="min-width:160px;">
                    <option value="">Tahun Ajaran</option>
                    <?php foreach ($optTahunAjaran as $ta): ?>
                        <option value="<?= e($ta) ?>" <?= $tahunAjaran === $ta ? 'selected' : '' ?>><?= e($ta) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Dropdown Jurusan -->
            <div class="col-auto">
                <select name="jurusan" class="form-select form-select-sm" style="min-width:160px;">
                    <option value="">Jurusan</option>
                    <option value="MIPA" <?= $jurusan === 'MIPA' ? 'selected' : '' ?>>MIPA</option>
                    <option value="IPS"  <?= $jurusan === 'IPS'  ? 'selected' : '' ?>>IPS</option>
                </select>
            </div>
            <!-- Tombol Submit -->
            <div class="col-auto">
                <button type="submit" class="btn btn-warning btn-sm text-white px-3 fw-medium shadow-sm">
                    <i class="bi bi-search me-1"></i>Tampilkan
                </button>
            </div>
            <!-- Tombol Reset -->
            <?php if ($tahunAjaran || $jurusan): ?>
            <div class="col-auto">
                <a href="<?= e(url('hasil/eligible')) ?>" class="btn btn-outline-secondary btn-sm px-3">
                    <i class="bi bi-arrow-clockwise me-1"></i>Reset
                </a>
            </div>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- RIWAYAT HASIL AKHIR -->
<div class="card border-0 shadow-sm mb-4">
    <!-- Header Card -->
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
        <strong class="fs-6"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Hasil Akhir Peringkat Eligible</strong>
    </div>
    <!-- Body Card -->
    <div class="card-body">
        <!-- Control Panel Entries -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div class="d-flex align-items-center gap-2">
                <label class="text-muted small mb-0">Show</label>
                <!-- Form Limit -->
                <form method="GET" action="<?= e(url('')) ?>" id="formLimit">
                    <input type="hidden" name="url" value="hasil/eligible">
                    <input type="hidden" name="tahun_ajaran" value="<?= e($tahunAjaran) ?>">
                    <input type="hidden" name="jurusan" value="<?= e($jurusan) ?>">
                    <select name="limit" class="form-select form-select-sm d-inline-block w-auto"
                            onchange="this.form.submit()">
                        <?php foreach ([10, 25, 50, 100] as $opt): ?>
                            <option value="<?= $opt ?>" <?= $limit === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <label class="text-muted small mb-0">entries</label>
            </div>
        </div>

        <!-- Tabel Riwayat Hasil -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 small">
                <!-- Header Tabel -->
                <thead class="table-light text-center">
                    <tr>
                        <th style="width:45px;">No</th>
                        <th style="min-width:130px;">Tahun Ajaran</th>
                        <th style="min-width:110px;">Jurusan</th>
                        <th style="min-width:150px;">Tanggal Hitung</th>
                        <th style="width:120px;">Jumlah Siswa</th>
                        <th style="width:350px;">Dibuat Oleh</th>
                        <th style="width:180px;">Aksi</th>
                    </tr>
                </thead>
                <!-- Body Tabel -->
                <tbody>
                    <?php if (!empty($riwayat)): ?>
                        <?php foreach ($riwayat as $i => $r): ?>
                            <tr class="riwayat-row">
                                <!-- Nomor -->
                                <td class="text-center text-muted"><?= $offset + $i + 1 ?></td>
                                <!-- Tahun Ajaran -->
                                <td class="text-center fw-medium text-dark"><?= e($r['tahun_ajaran']) ?></td>
                                <!-- Jurusan -->
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1">
                                        <?= e($r['jurusan']) ?>
                                    </span>
                                </td>
                                <!-- Tanggal Hitung -->
                                <td class="text-center text-dark"><?= tglId($r['tanggal_hitung'], $bulanId) ?></td>
                                <!-- Jumlah Siswa -->
                                <td class="text-center"><?= e($r['jumlah_siswa']) ?></td>
                                <!-- Pembuat -->
                                <td>
                                    <?php if (!empty($r['nama_user'])): ?>
                                        <!-- Get Role Badge -->
                                        <?php
                                        $rb = $roleBadge[$r['role_user']] ?? ['label' => $r['role_user'], 'style' => 'background:#6c757d;color:#fff;'];
                                        $initials = '';
                                        foreach (explode(' ', $r['nama_user']) as $w) $initials .= strtoupper(substr($w, 0, 1));
                                        $initials = substr($initials, 0, 2);
                                        ?>
                                        <!-- User Info -->
                                        <div class="d-flex align-items-center gap-2">
                                            <!-- Foto User -->
                                            <?php if (!empty($r['foto_user'])): ?>
                                                <img src="<?= asset('public/uploads/profile/' . $r['foto_user']) ?>" class="rounded-circle" style="width:28px;height:28px;object-fit:cover;border:1.5px solid #dee2e6;" alt="">
                                            <?php else: ?>
                                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white fw-bold" style="width:28px;height:28px;font-size:0.6rem;flex-shrink:0;"><?= $initials ?></div>
                                            <?php endif; ?>
                                            <!-- Nama dan Badge Role -->
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="small fw-medium text-nowrap"><?= e($r['nama_user']) ?></span>
                                                <span style="font-size:0.55rem;padding:1px 5px;border-radius:4px;white-space:nowrap;font-weight:600;<?= $rb['style'] ?>"><?= $rb['label'] ?></span>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center text-muted">—</div>
                                    <?php endif; ?>
                                </td>
                                <!-- Tombol Aksi -->
                                <td class="text-center">
                                    <a href="<?= e(url('hasil/eligible-lihat')) ?>&id=<?= $r['id_perhitungan'] ?>" 
                                       class="btn btn-sm btn-outline-primary px-3 fw-medium" style="font-size:0.75rem;">
                                        <i class="bi bi-trophy me-1"></i>Lihat Hasil
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Empty State -->
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-clock-history fs-2 d-block mb-2 opacity-50"></i>
                                Belum ada riwayat hasil akhir eligible.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total > 0): ?>
        <div class="d-flex align-items-center mt-3" style="position: relative; min-height: 32px;">
            <div class="text-muted small">
                Show <?= $from ?> to <?= $to ?> of <?= $total ?> entries
            </div>
            <?php if ($totalPages > 1): ?>
            <nav style="position: absolute; left: 50%; transform: translateX(-50%);">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= e($baseUrl . '&page=' . ($page - 1)) ?>">‹</a>
                    </li>
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <?php if ($p === 1 || $p === $totalPages || abs($p - $page) <= 1): ?>
                            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                <a class="page-link" href="<?= e($baseUrl . '&page=' . $p) ?>"><?= $p ?></a>
                            </li>
                        <?php elseif (abs($p - $page) === 2): ?>
                            <li class="page-item disabled"><span class="page-link">…</span></li>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= e($baseUrl . '&page=' . ($page + 1)) ?>">›</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- STYLING CSS -->
<link rel="stylesheet" href="<?= e(asset('assets/css/pages/hasil.css')); ?>">
