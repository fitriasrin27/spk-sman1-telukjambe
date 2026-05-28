<?php
$roleBadges = [
    'operator'        => ['label' => 'OP',      'style' => 'background:#cfe2ff;color:#084298;'],
    'wali_kelas'      => ['label' => 'Walas',   'style' => 'background:#d1e7dd;color:#0a3622;'],
    'bk'              => ['label' => 'BK',      'style' => 'background:#e8d5ff;color:#432874;'],
    'tu'              => ['label' => 'TU',      'style' => 'background:#ffe5d0;color:#7c3c00;'],
    'wakasek'         => ['label' => 'Wakasek', 'style' => 'background:#cff4fc;color:#055160;'],
    'kepala_sekolah'  => ['label' => 'Kepsek',  'style' => 'background:#f8d7da;color:#842029;'],
    'admin'           => ['label' => 'Admin',   'style' => 'background:#e0f2fe;color:#0369a1;'],
];
?>

<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <h2 class="page-title d-flex align-items-center gap-2 mb-0">
        <i class="bi bi-clock-history text-primary"></i> Log Aktivitas
    </h2>
    <!-- Live pulse indicator -->
    <div class="live-indicator d-flex align-items-center gap-2 px-3 py-1-5 bg-white border rounded-pill shadow-sm">
        <span class="live-pulse" id="livePulse"></span>
        <span class="text-success fw-bold small mb-0" id="liveStatusText" style="font-size: 0.78rem; letter-spacing: 0.5px;">LIVE MONITORING</span>
    </div>
</div>

<!-- Row Metrics Cards: 5 Cards Grid -->
<div class="row g-3 mb-4" id="metricsRow">
    <!-- Card 1: Total Aktivitas Hari Ini -->
    <div class="col-xl col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm metric-card h-100">
            <div class="card-body d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="text-muted d-block small mb-1 fw-medium" style="font-size: 0.78rem;">Aktivitas Hari Ini</span>
                    <h3 class="fw-bold mb-0 text-dark" id="metric-total-activities"><?= e($metrics['total_activities']); ?></h3>
                </div>
                <div class="metric-icon bg-primary bg-opacity-10 text-primary rounded-circle">
                    <i class="bi bi-activity"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Card 2: Pengguna Aktif Hari Ini -->
    <div class="col-xl col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm metric-card h-100">
            <div class="card-body d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="text-muted d-block small mb-1 fw-medium" style="font-size: 0.78rem;">Pengguna Aktif</span>
                    <h3 class="fw-bold mb-0 text-dark" id="metric-active-users"><?= e($metrics['active_users']); ?></h3>
                </div>
                <div class="metric-icon bg-success bg-opacity-10 text-success rounded-circle">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Card 3: Total Perhitungan Hari Ini -->
    <div class="col-xl col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm metric-card h-100">
            <div class="card-body d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="text-muted d-block small mb-1 fw-medium" style="font-size: 0.78rem;">Perhitungan SAW</span>
                    <h3 class="fw-bold mb-0 text-dark" id="metric-total-calculations"><?= e($metrics['total_calculations']); ?></h3>
                </div>
                <div class="metric-icon bg-warning bg-opacity-10 text-warning rounded-circle">
                    <i class="bi bi-cpu-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Card 4: Laporan Dibuat Hari Ini -->
    <div class="col-xl col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm metric-card h-100">
            <div class="card-body d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="text-muted d-block small mb-1 fw-medium" style="font-size: 0.78rem;">Laporan Dibuat</span>
                    <h3 class="fw-bold mb-0 text-dark" id="metric-reports-created"><?= e($metrics['total_reports_created']); ?></h3>
                </div>
                <div class="metric-icon bg-info bg-opacity-10 text-info rounded-circle">
                    <i class="bi bi-file-earmark-plus-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Card 5: Laporan Diunduh Hari Ini -->
    <div class="col-xl col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm metric-card h-100">
            <div class="card-body d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="text-muted d-block small mb-1 fw-medium" style="font-size: 0.78rem;">Laporan Diunduh</span>
                    <h3 class="fw-bold mb-0 text-dark" id="metric-reports-downloaded"><?= e($metrics['total_reports_downloaded']); ?></h3>
                </div>
                <div class="metric-icon bg-danger bg-opacity-10 text-danger rounded-circle">
                    <i class="bi bi-download"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Halaman Utama Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3 filter-row">
        <form method="GET" action="<?= url('log') ?>" id="formFilterLog" onsubmit="return false;">
            <div class="row g-2 align-items-end">
                <!-- Kolom Kata Kunci -->
                <div class="col-md-3 col-lg-2">
                    <label class="form-label small text-muted fw-semibold mb-1">Cari Aktivitas atau Nama</label>
                    <div class="input-group input-group-sm filter-input-group">
                        <span class="input-group-text bg-white border-end-0 py-1"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="searchLog" class="form-control border-start-0 ps-0 py-1" placeholder="Cari..." value="<?= e($search) ?>" autocomplete="off">
                    </div>
                </div>
                <!-- Filter Role -->
                <div class="col-md-2">
                    <label class="form-label small text-muted fw-semibold mb-1">Filter Peran</label>
                    <select id="filterRole" class="form-select form-select-sm py-1">
                        <option value="">Semua Peran</option>
                        <option value="operator" <?= $role === 'operator' ? 'selected' : '' ?>>Operator (OP)</option>
                        <option value="wali_kelas" <?= $role === 'wali_kelas' ? 'selected' : '' ?>>Wali Kelas (Walas)</option>
                        <option value="bk" <?= $role === 'bk' ? 'selected' : '' ?>>Bimbingan Konseling (BK)</option>
                        <option value="tu" <?= $role === 'tu' ? 'selected' : '' ?>>Tata Usaha (TU)</option>
                        <option value="wakasek" <?= $role === 'wakasek' ? 'selected' : '' ?>>Wakil Kepala Sekolah (Wakasek)</option>
                        <option value="kepala_sekolah" <?= $role === 'kepala_sekolah' ? 'selected' : '' ?>>Kepala Sekolah (Kepsek)</option>
                    </select>
                </div>
                <!-- Filter Modul -->
                <div class="col-md-2">
                    <label class="form-label small text-muted fw-semibold mb-1">Filter Modul</label>
                    <select id="filterModul" class="form-select form-select-sm py-1">
                        <option value="">Semua Modul</option>
                        <option value="auth" <?= $modul === 'auth' ? 'selected' : '' ?>>Auth (Login/Logout)</option>
                        <option value="siswa" <?= $modul === 'siswa' ? 'selected' : '' ?>>Siswa (Data Siswa)</option>
                        <option value="nilai" <?= $modul === 'nilai' ? 'selected' : '' ?>>Nilai (Nilai Rapor)</option>
                        <option value="perhitungan" <?= $modul === 'perhitungan' ? 'selected' : '' ?>>Perhitungan (SAW)</option>
                        <option value="kriteria" <?= $modul === 'kriteria' ? 'selected' : '' ?>>Kriteria & Konversi</option>
                        <option value="laporan" <?= $modul === 'laporan' ? 'selected' : '' ?>>Laporan (Cetak)</option>
                        <option value="akun" <?= $modul === 'akun' ? 'selected' : '' ?>>Kelola Akun</option>
                    </select>
                </div>
                <!-- Rentang Waktu -->
                <div class="col-md-2">
                    <label class="form-label small text-muted fw-semibold mb-1">Rentang Waktu</label>
                    <select id="filterRentang" class="form-select form-select-sm py-1">
                        <option value="all" <?= $rentang === 'all' ? 'selected' : '' ?>>Semua Waktu</option>
                        <option value="today" <?= $rentang === 'today' ? 'selected' : '' ?>>Hari Ini</option>
                        <option value="week" <?= $rentang === 'week' ? 'selected' : '' ?>>7 Hari Terakhir</option>
                        <option value="month" <?= $rentang === 'month' ? 'selected' : '' ?>>30 Hari Terakhir</option>
                        <option value="custom" <?= $rentang === 'custom' ? 'selected' : '' ?>>Kustom Tanggal</option>
                    </select>
                </div>
                <!-- Custom Date Inputs (only visible when custom is selected) -->
                <div class="col-md-3 <?= $rentang === 'custom' ? '' : 'd-none' ?>" id="customDateWrapper">
                    <div class="d-flex align-items-center gap-2">
                        <div class="flex-fill">
                            <label class="form-label small text-muted fw-semibold mb-1" style="font-size:0.75rem;">Mulai</label>
                            <input type="date" id="filterStartDate" class="form-control form-control-sm py-1" value="<?= e($startDate) ?>">
                        </div>
                        <div class="flex-fill">
                            <label class="form-label small text-muted fw-semibold mb-1" style="font-size:0.75rem;">Sampai</label>
                            <input type="date" id="filterEndDate" class="form-control form-control-sm py-1" value="<?= e($endDate) ?>">
                        </div>
                    </div>
                </div>
                <!-- Tombol Reset -->
                <div class="col-auto ms-auto">
                    <button type="button" id="btnResetFilters" class="btn btn-sm btn-secondary py-1-5 px-3 fw-medium d-flex align-items-center gap-1" style="height:31px; font-size:0.82rem;" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Log Aktivitas -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-4">
        <!-- Control Row: Limit & Refresh Status -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small">Show</span>
                <select id="logLimitSelect" class="form-select form-select-sm w-auto">
                    <?php foreach ([10, 25, 50, 100] as $opt): ?>
                        <option value="<?= $opt ?>" <?= $limit === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="text-muted small">entries</span>
            </div>
            
            <!-- Refresh Indicator & Button -->
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small fw-medium" id="lastRefreshTime" style="font-size: 0.78rem;">
                    Terakhir diperbarui: <?= date('H:i:s') ?>
                </span>
                <button type="button" id="btnManualRefresh" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1-5 py-1 px-2-5 fw-semibold" style="font-size: 0.78rem; border-radius: 6px;">
                    <i class="bi bi-arrow-repeat" id="refreshIcon"></i> Refresh
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0 custom-table" id="logTable">
                <thead class="bg-light align-middle text-center text-dark fw-bold">
                    <tr>
                        <th width="60" class="py-3 align-middle text-center">No</th>
                        <th width="160" class="align-middle text-center">Waktu</th>
                        <th width="220" class="align-middle text-center">Pengguna</th>
                        <th width="150" class="align-middle text-center">Modul</th>
                        <th class="align-middle text-center">Aktivitas</th>
                        <th width="140" class="align-middle text-center">IP Address</th>
                        <th width="130" class="align-middle text-center">Browser</th>
                    </tr>
                </thead>
                <tbody id="logTableBody">
                    <?php if (!empty($logs)): ?>
                        <?php foreach ($logs as $i => $l): ?>
                            <?php include __DIR__ . '/row.php'; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr id="emptyRow">
                            <td colspan="7" class="text-center py-5 text-muted align-middle">Belum ada data log aktivitas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="row align-items-center mt-4 gy-2" id="paginationWrapper">
            <div class="col-md-4 text-center text-md-start">
                <div class="text-muted small" id="entryCounter">
                    Show <?= ($page - 1) * $limit + 1 ?> to <?= min($page * $limit, $total) ?> of <?= $total ?> entries
                </div>
            </div>
            <div class="col-md-4 d-flex justify-content-center">
                <nav id="paginationNavWrapper">
                    <?php if ($totalPages > 1): ?>
                        <ul class="pagination pagination-sm justify-content-center mb-0" id="logPagination">
                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>" data-page="<?= $page - 1 ?>">
                                <a class="page-link" href="#">Previous</a>
                            </li>
                            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                <li class="page-item <?= $p == $page ? 'active' : '' ?>" data-page="<?= $p ?>">
                                    <a class="page-link" href="#"><?= $p ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>" data-page="<?= $page + 1 ?>">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    <?php endif; ?>
                </nav>
            </div>
            <div class="col-md-4"></div>
        </div>

        <!-- Info Retensi Log -->
        <div class="d-flex align-items-center justify-content-center gap-2 mt-3 mb-1" style="opacity: 0.55;">
            <i class="bi bi-clock-history" style="font-size: 0.72rem; color: #6c757d;"></i>
            <span style="font-size: 0.72rem; color: #6c757d; letter-spacing: 0.01em;">
                Log disimpan maks. <strong>25.000 data</strong> &middot; Dibersihkan otomatis tiap <strong>Juni</strong> untuk log berusia lebih dari 1 tahun
            </span>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?= e(asset('assets/css/pages/log.css')); ?>">
<script>
    const apiFetchUrl = '<?= e(url('log/api-fetch')); ?>';
    const pageUrl     = '<?= e(url('log')); ?>';
    const assetBaseUrl = '<?= e(asset('')); ?>';
    const currentLimit = <?= $limit ?>;
</script>
<script src="<?= e(asset('assets/js/pages/log.js')); ?>"></script>
