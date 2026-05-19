<section class="row g-4 mb-4">
    <!-- Card: Total Siswa -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card summary-card border-0 shadow-sm rounded-4 overflow-hidden h-100 transition-all">
            <div class="card-body p-4 position-relative">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-3 p-2">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">Aktif</span>
                </div>
                <!-- Title -->
                <p class="text-muted small fw-bold mb-1 uppercase tracking-wider">DATABASE SISWA</p>
                <!-- Siswa -->
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="display-6 fw-bold mb-0 text-dark"><?= number_format($totalSiswa, 0, ',', '.'); ?></h3>
                    <span class="text-muted small">Siswa</span>
                </div>
                <!-- Button -->
                <?php if (current_user()['role'] !== 'kepala_sekolah'): ?>
                <div class="mt-3">
                    <a href="<?= e(url('siswa')); ?>" class="text-primary small text-decoration-none fw-bold">
                        Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Card: Target Eligible -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card summary-card border-0 shadow-sm rounded-4 overflow-hidden h-100 transition-all">
            <div class="card-body p-4 position-relative">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="icon-shape bg-warning bg-opacity-10 text-warning rounded-3 p-2">
                        <i class="bi bi-mortarboard-fill fs-4"></i>
                    </div>
                    <!-- Badge -->
                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2">Target 40%</span>
                </div>
                <!-- Title -->
                <p class="text-muted small fw-bold mb-1 uppercase tracking-wider">KUOTA ELIGIBLE</p>
                <!-- Siswa -->
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="display-6 fw-bold mb-0 text-dark"><?= number_format($totalSiswaKelasXii, 0, ',', '.'); ?></h3>
                    <span class="text-muted small">Siswa</span>
                </div>
                <!-- Progress Bar -->
                <div class="mt-3">
                    <div class="progress rounded-pill mb-1" style="height: 6px; background-color: #f1f5f9;">
                        <div class="progress-bar bg-warning" style="width: 100%"></div>
                    </div>
                    <!-- Text -->
                    <span class="text-muted" style="font-size: 0.7rem;">Dihitung dari total siswa Kelas XII aktif</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Card: Ranking Kelas -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card action-card bg-primary border-0 shadow-sm rounded-4 h-100 text-white overflow-hidden position-relative">
            <div class="card-body p-4 d-flex flex-column justify-content-between z-index-2 position-relative">
                <!-- Title -->
                <div>
                    <!-- Icon -->
                    <i class="bi bi-bar-chart-line fs-2 mb-3 d-block"></i>
                    <!-- Text -->
                    <h3 class="h5 fw-bold mb-1">Laporan Peringkat Kelas</h3>
                    <p class="small text-white-50">Arsip perankingan kelas per semester berjalan.</p>
                </div>
                <!-- Button -->
                <a href="<?= e(url('laporan') . '&jenis_laporan=kelas&tahun_ajaran=' . urlencode($tahunAjaran ?: ($daftarTahunAjaran[0] ?? ''))); ?>" class="btn btn-white btn-sm fw-bold rounded-pill text-primary stretched-link py-2 mt-2" style="background: white;">
                    Buka Arsip Laporan
                </a>
            </div>
            <!-- Decorative circle -->
            <div class="position-absolute" style="width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%; top: -50px; right: -50px;"></div>
        </div>
    </div>

    <!-- Action Card: Ranking Eligible -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card action-card bg-dark border-0 shadow-sm rounded-4 h-100 text-white overflow-hidden position-relative">
            <div class="card-body p-4 d-flex flex-column justify-content-between z-index-2 position-relative">
                <!-- Title -->
                <div>
                    <!-- Icon -->
                    <i class="bi bi-award fs-2 mb-3 d-block text-warning"></i>
                    <!-- Text -->
                    <h3 class="h5 fw-bold mb-1">Laporan Peringkat Eligible</h3>
                    <p class="small text-white-50">Hasil seleksi otomatis untuk kuota SNBP nasional.</p>
                </div>
                <!-- Button -->
                <a href="<?= e(url('laporan') . '&jenis_laporan=eligible&tahun_ajaran=' . urlencode($tahunAjaran ?: ($daftarTahunAjaran[0] ?? ''))); ?>" class="btn btn-warning btn-sm fw-bold rounded-pill text-dark stretched-link py-2 mt-2">
                    Buka Arsip Laporan
                </a>
            </div>
            <!-- Decorative icon bg -->
            <div class="position-absolute" style="bottom: -20px; right: -20px; font-size: 100px; color: rgba(255,255,255,0.05); transform: rotate(-15deg);">
                <i class="bi bi-award"></i>
            </div>
        </div>
    </div>
</section>

<section class="dashboard-section py-2">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <!-- Title -->
        <div>
            <h2 class="fw-bold mb-0">Kesiapan Data Akademik</h2>
        </div>
        <!-- Form Filter -->
        <div class="year-filter-wrapper">
            <form method="GET" action="<?= e(url('dashboard')); ?>" id="formGlobalFilter" class="d-flex align-items-center gap-2 bg-white p-1 rounded-pill shadow-sm border border-primary-subtle">
                <input type="hidden" name="url" value="dashboard">
                <!-- Tahun Ajaran -->
                <span class="ps-3 text-primary small fw-bold"><i class="bi bi-calendar3 me-1"></i> TA:</span>
                <select name="tahun_ajaran" class="form-select form-select-sm border-0 bg-transparent fw-bold" style="width: 120px; cursor: pointer;" onchange="this.form.submit()">
                    <?php foreach ($daftarTahunAjaran as $ta): ?>
                        <option value="<?= e($ta); ?>" <?= ($tahunAjaran ?: ($daftarTahunAjaran[0] ?? '')) === $ta ? 'selected' : ''; ?>>
                            <?= e($ta); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <!-- Separator -->
                <div class="vr my-2" style="width: 1px; background-color: #dee2e6; opacity: 1;"></div>
                <!-- Semester -->
                <span class="ps-2 text-primary small fw-bold"><i class="bi bi-journal-bookmark me-1"></i> Sem:</span>
                <select name="semester" class="form-select form-select-sm border-0 bg-transparent fw-bold" style="width: 100px; cursor: pointer;" onchange="this.form.submit()">
                    <option value="1" <?= (int)($semester ?: $progresRanking['semester']) === 1 ? 'selected' : ''; ?>>Ganjil</option>
                    <option value="2" <?= (int)($semester ?: $progresRanking['semester']) === 2 ? 'selected' : ''; ?>>Genap</option>
                </select>
                <!-- Button -->
                <div class="pe-1">
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">Terapkan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        <!-- Card: Total Siswa -->
        <?php 
            $cuteColors = [
                'X'   => ['bg' => '#fff1f2', 'text' => '#e11d48', 'border' => '#ffe4e6', 'hover' => '#ffe4e6'],
                'XI'  => ['bg' => '#f5f3ff', 'text' => '#7c3aed', 'border' => '#ede9fe', 'hover' => '#ede9fe'],
                'XII' => ['bg' => '#f0fdfa', 'text' => '#0d9488', 'border' => '#ccfbf1', 'hover' => '#ccfbf1']
            ];
        ?>
        <?php foreach (['X', 'XI', 'XII'] as $tingkat): ?>
            <!-- Loop setiap tingkat -->
            <?php
                $dataTingkat = $kesiapanData[$tingkat] ?? [];
                $totalSiswa  = 0;
                $totalLengkap = 0;
                foreach ($dataTingkat as $j) {
                    $totalSiswa += $j['total'];
                    $totalLengkap += $j['lengkap'];
                }
                $persenTotal = $totalSiswa > 0 ? round(($totalLengkap / $totalSiswa) * 100) : 0;
                $color = $persenTotal == 100 ? '#10b981' : ($persenTotal > 50 ? '#f59e0b' : '#ef4444');
                $cute = $cuteColors[$tingkat];
            ?>

            <div class="col-12 col-md-6 col-xl-4">
                <article class="card distribution-card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <!-- Title -->
                            <div>
                                <h3 class="h5 fw-bold mb-1">Angkatan <?= e($tingkat); ?></h3>
                                <p class="text-muted small mb-0">Validasi Kelengkapan Nilai</p>
                            </div>
                            <!-- Donut Chart -->
                            <div class="donut-container">
                                <div class="donut-ready" style="--ready: <?= $persenTotal ?>; --color: <?= $cute['text'] ?>; --bg: <?= $cute['bg'] ?>;">
                                    <span class="donut-text"><?= $persenTotal ?>%</span>
                                </div>
                            </div>
                        </div>
                        <!-- Progress Bar -->
                        <div class="row g-2 mb-3">

                            <?php foreach (['MIPA', 'IPS'] as $jur): ?>
                                <!-- Kelengkapan Nilai -->
                                <?php 
                                    $jData = $dataTingkat[$jur] ?? ['total' => 0, 'lengkap' => 0];
                                    $jPersen = $jData['total'] > 0 ? round(($jData['lengkap'] / $jData['total']) * 100) : 0;
                                    $jStatusColor = $jPersen == 100 ? 'bg-success' : ($jPersen > 0 ? 'bg-warning' : 'bg-danger');
                                ?>
                                <!-- MIPA & IPS -->
                                <div class="col-6">
                                    <div class="p-2 rounded bg-light border">
                                        <!-- Nama Jurusan & Badge -->
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="small fw-bold text-dark"><?= $jur ?></span>
                                            <span class="badge dot-badge" style="background-color: <?= $cute['text'] ?>;"></span>
                                        </div>
                                        <!-- Jumlah Siswa -->
                                        <div class="small text-muted"><?= $jData['lengkap'] ?> / <?= $jData['total'] ?> Siswa</div>
                                        <!-- Progress Bar -->
                                        <div class="progress mt-2" style="height: 4px;">
                                            <div class="progress-bar" style="width: <?= $jPersen ?>%; background-color: <?= $cute['text'] ?>;"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Tombol Aksi -->
                        <?php if (current_user()['role'] !== 'kepala_sekolah'): ?>
                        <!-- Link ke halaman nilai -->
                        <a href="<?= e(url('nilai&status=incomplete&tingkat=' . $tingkat . '&tahun_ajaran=' . ($tahunAjaran ?: ($daftarTahunAjaran[0] ?? '')))); ?>" 
                           class="btn btn-sm w-100 fw-bold rounded-pill py-2 transition-all btn-cute" 
                           style="background-color: <?= $cute['bg'] ?>; color: <?= $cute['text'] ?>; border: 1px solid <?= $cute['border'] ?>;">
                            Lihat Kelengkapan Data <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<div class="row g-4 mt-2 mb-5">
    <!-- Kolom Kiri: Analisis Perankingan -->
    <div class="col-12 col-xl-7">
        <!-- Status Ranking Kelas -->
        <section class="dashboard-section mb-4">
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <h2 class="fw-bold mb-0">Status Perankingan Kelas</h2>
                <!-- Badge Total Keseluruhan -->
                <div class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 border border-primary-subtle">
                    <i class="bi bi-info-circle me-1"></i> Total Keseluruhan: <?= $progresRanking['persen']; ?>% Selesai
                </div>
            </div>
            <!-- Grid Perjurusan -->
            <div class="row g-3">
                <!-- MIPA & IPS -->
                <?php foreach (['MIPA', 'IPS'] as $jur): 
                    $dataJur = $progresRanking['jurusan'][$jur];
                    $color = ($jur === 'MIPA') ? '#0d6efd' : '#ffc107';
                    $colorClass = ($jur === 'MIPA') ? 'primary' : 'warning';
                ?>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative ranking-status-card">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <!-- Icon -->
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-box-lg bg-<?= $colorClass; ?>-subtle text-<?= $colorClass; ?> rounded-4 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                        <i class="bi bi-mortarboard-fill fs-3"></i>
                                    </div>
                                    <!-- Nama Jurusan & Jumlah Selesai -->
                                    <div>
                                        <h3 class="h5 fw-bold mb-1">Jurusan <?= $jur; ?></h3>
                                        <p class="text-muted small mb-0"><?= $dataJur['selesai']; ?> dari <?= $dataJur['total']; ?> Kelas Selesai</p>
                                    </div>
                                </div>
                                <!-- Donut Chart -->
                                <div class="donut-container" style="width: 64px; height: 64px;">
                                    <div class="donut-ready" style="--ready: <?= $dataJur['persen']; ?>; --color: <?= $color; ?>; width: 64px; height: 64px;">
                                        <div class="donut-text" style="font-size: 0.85rem;"><?= $dataJur['persen']; ?>%</div>
                                    </div>
                                </div>
                            </div>
                            <!-- Progres Pengerjaan -->
                            <div class="mt-4 pt-3 border-top">
                                <div class="d-flex align-items-center justify-content-between small mb-2">
                                    <span class="fw-medium text-muted">Progres Pengerjaan</span>
                                    <span class="fw-bold text-dark"><?= $dataJur['selesai']; ?>/<?= $dataJur['total']; ?> Hasil</span>
                                </div>
                                <!-- Progress Bar -->
                                <div class="progress rounded-pill" style="height: 8px; background-color: #f1f5f9;">
                                    <div class="progress-bar bg-<?= $colorClass; ?> progress-bar-striped progress-bar-animated" style="width: <?= $dataJur['persen']; ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <!-- Link ke halaman hasil dengan filter jurusan -->
                        <?php if (current_user()['role'] !== 'kepala_sekolah'): ?>
                        <a href="<?= e(url('hasil/kelas&jurusan=' . $jur)); ?>" class="stretched-link"></a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <!-- Tombol Aksi -->
            <?php if (!in_array(current_user()['role'], ['kepala_sekolah', 'tu', 'wakasek', 'bk'])): ?>
            <!-- Tombol Mulai Perankingan Baru -->
            <div class="mt-3 text-center">
                <a href="<?= e(url('perhitungan/kelas')); ?>" class="btn btn-light btn-sm text-primary fw-bold rounded-pill border px-4">
                    <i class="bi bi-plus-lg me-1"></i> Mulai Perankingan Baru
                </a>
            </div>
            <?php endif; ?>
        </section>

        <!-- Analisis Kuota Eligible -->
        <section class="dashboard-section <?= (current_user()['role'] === 'kepala_sekolah') ? 'mt-xl-5 pt-xl-4' : ''; ?>">
            <h2 class="fw-bold mb-3">Analisis Kuota Eligible SNBP</h2>
            <!-- Grid Perjurusan -->
            <div class="row g-3">
                <!-- MIPA & IPS -->
                <div class="col-md-6">
                    <article class="card quota-card border-0 shadow-sm overflow-hidden h-100">
                        <div class="card-body p-4 position-relative">
                            <div class="quota-icon-bg"><i class="bi bi-mortarboard-fill"></i></div>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="icon-box bg-primary text-white rounded-3 p-2 shadow-sm">
                                    <i class="bi bi-mortarboard fs-5"></i>
                                </div>
                                <!-- Nama Jurusan & Kuota Tersedia -->
                                <div>
                                    <h3 class="h6 fw-bold mb-0">Jurusan MIPA</h3>
                                    <small class="text-muted" style="font-size: 0.75rem;">Kuota Tersedia (40%)</small>
                                </div>
                            </div>
                            <!-- Jumlah Siswa -->
                            <div class="mb-3">
                                <div class="d-flex align-items-baseline gap-2">
                                    <span class="display-5 fw-bold text-dark"><?= number_format($distribusiEligible['MIPA'], 0, ',', '.'); ?></span>
                                    <span class="text-muted fw-medium">Siswa Terbaik</span>
                                </div>
                            </div>
                            <!-- Link ke halaman hasil eligible dengan filter jurusan -->
                            <?php if (!in_array(current_user()['role'], ['kepala_sekolah', 'wali_kelas'])): ?>
                            <a class="btn btn-primary w-100 fw-bold py-2 rounded-pill shadow-sm" href="<?= e(url('hasil/eligible&jurusan=MIPA')); ?>">
                                Lihat Hasil Peringkat <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>
                <div class="col-md-6">
                    <article class="card quota-card border-0 shadow-sm overflow-hidden h-100">
                        <div class="card-body p-4 position-relative">
                            <div class="quota-icon-bg warning"><i class="bi bi-mortarboard-fill"></i></div>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="icon-box bg-warning text-white rounded-3 p-2 shadow-sm">
                                    <i class="bi bi-mortarboard-fill fs-5"></i>
                                </div>
                                <!-- Nama Jurusan & Kuota Tersedia -->
                                <div>
                                    <h3 class="h6 fw-bold mb-0">Jurusan IPS</h3>
                                    <small class="text-muted" style="font-size: 0.75rem;">Kuota Tersedia (40%)</small>
                                </div>
                            </div>
                            <!-- Jumlah Siswa -->
                            <div class="mb-3">
                                <div class="d-flex align-items-baseline gap-2">
                                    <span class="display-5 fw-bold text-dark"><?= number_format($distribusiEligible['IPS'], 0, ',', '.'); ?></span>
                                    <span class="text-muted fw-medium">Siswa Terbaik</span>
                                </div>
                            </div>
                            <!-- Link ke halaman hasil eligible dengan filter jurusan -->
                            <?php if (!in_array(current_user()['role'], ['kepala_sekolah', 'wali_kelas'])): ?>
                            <a class="btn btn-warning text-white w-100 fw-bold py-2 rounded-pill shadow-sm" href="<?= e(url('hasil/eligible&jurusan=IPS')); ?>">
                                Lihat Hasil Peringkat <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>
            </div>
        </section>
    </div>

    <!-- Kolom Kanan: Parameter SAW -->
    <div class="col-12 col-xl-5 d-flex flex-column">
        <!-- Parameter Bobot SAW -->
        <section class="dashboard-section h-100 d-flex flex-column">
            <!-- Header & Tombol Kelola -->
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="fw-bold mb-0">Parameter Bobot SAW</h2>
                <!-- Tombol Kelola -->
                <?php if (!in_array(current_user()['role'], ['kepala_sekolah', 'wakasek', 'tu'])): ?>
                <a href="<?= e(url('kriteria')); ?>" class="btn btn-link btn-sm p-0 text-decoration-none">Kelola <i class="bi bi-chevron-right"></i></a>
                <?php endif; ?>
            </div>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden flex-grow-1 d-flex flex-column saw-card">
                <!-- Informasi Bobot SAW -->
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <div class="p-3 bg-light rounded-3 small text-muted border-start border-4 border-primary">
                        <i class="bi bi-info-circle-fill text-primary me-1"></i> Bobot global ini digunakan sebagai parameter dalam sistem pendukung keputusan.
                    </div>
                </div>
                <div class="card-body p-4 flex-grow-1 d-flex flex-column justify-content-center">
                    <!-- Daftar Kriteria dengan Bobot -->
                    <div class="d-flex flex-column gap-3">
                        <!-- Warna berdasarkan kriteria -->
                        <?php 
                        $colors = ['#0d6efd', '#ffc107', '#198754', '#6c757d'];
                        $idx = 0;
                        foreach ($kriteria as $k): 
                        ?>
                        <!-- Card kriteria -->
                        <div class="criteria-item p-3 rounded-4 bg-white border d-flex align-items-center justify-content-between transition-all shadow-sm">
                            <div class="d-flex align-items-center gap-3">
                                <!-- Kode kriteria -->
                                <div class="criteria-code rounded-circle text-white d-flex align-items-center justify-content-center fw-bold" 
                                     style="background-color: <?= $colors[$idx % 4] ?>; width: 36px; height: 36px; font-size: 11px;">
                                    <?= e($k['kode_kriteria']) ?>
                                </div>
                                <!-- Nama kriteria dan atribut -->
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 1rem;"><?= e($k['nama_kriteria']) ?></div>
                                    <div class="small text-muted" style="font-size: 0.8rem;">Atribut: <?= ucfirst(e($k['atribut'])) ?></div>
                                </div>
                            </div>
                            <!-- Bobot kriteria (dalam persentase) -->
                            <div class="text-end">
                                <div class="h4 fw-bold text-primary mb-0"><?= (float)$k['bobot'] * 100 ?>%</div>
                            </div>
                        </div>
                        <?php $idx++; endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>


<link rel="stylesheet" href="<?= e(asset('assets/css/pages/dashboard.css')); ?>">


