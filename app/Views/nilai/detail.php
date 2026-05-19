<?php
$semesterLabel = ((int) $riwayat['semester'] % 2 === 0) ? 'Genap' : 'Ganjil';
?>

<link rel="stylesheet" href="<?= e(asset('assets/css/pages/nilai.css')); ?>">

<!-- Judul Halaman -->
<div class="page-header">
    <h2 class="page-title">Data Nilai</h2>
</div>
<!-- Tombol Kembali -->
<div class="nilai-detail-back">
    <a href="<?= e(url('nilai')); ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<!-- Box Judul Detail Nilai Siswa -->
<div class="nilai-detail-title-box">
    <h4 class="mb-0 fw-bold">Detail Nilai Siswa</h4>
</div>

<!-- Panel Detail Nilai Siswa -->
<div class="nilai-detail-panel">
    <div class="row g-4 mb-4 align-items-start">
        <div class="col-lg-5">
            <table class="table table-borderless table-sm mb-0 align-middle nilai-info-table">
                <!-- Informasi Siswa -->
                <tbody>
                    <!-- Nama Siswa -->
                    <tr>
                        <td class="label-cell">Nama Siswa</td>
                        <td class="nilai-separator-cell">:</td>
                        <td><?= e($riwayat['nama']); ?></td>
                    </tr>
                    <!-- NISN -->
                    <tr>
                        <td class="label-cell">NISN</td>
                        <td class="nilai-separator-cell">:</td>
                        <td><?= e($riwayat['nisn']); ?></td>
                    </tr>
                    <!-- NIS -->
                    <tr>
                        <td class="label-cell">NIS</td>
                        <td class="nilai-separator-cell">:</td>
                        <td><?= e($riwayat['nis']); ?></td>
                    </tr>
                    <!-- Tahun Ajaran -->
                    <tr>
                        <td class="label-cell">Tahun Ajaran</td>
                        <td class="nilai-separator-cell">:</td>
                        <td><?= e($riwayat['tahun_ajaran']); ?></td>
                    </tr>
                    <!-- Kelas -->
                    <tr>
                        <td class="label-cell">Kelas</td>
                        <td class="nilai-separator-cell">:</td>
                        <td><?= e($riwayat['kelas']); ?></td>
                    </tr>
                    <!-- Semester -->
                    <tr>
                        <td class="label-cell">Semester</td>
                        <td class="nilai-separator-cell">:</td>
                        <td><?= e($semesterLabel); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Absensi -->
        <div class="col-sm-6 col-lg-3">
            <div class="nilai-section-title">Absensi</div>
            <div class="nilai-mini-row">
                <span>Sakit</span>
                <span>:</span>
                <span class="nilai-mini-value"><?= (int) $absen['sakit']; ?></span>
            </div>
            <div class="nilai-mini-row">
                <span>Izin</span>
                <span>:</span>
                <span class="nilai-mini-value"><?= (int) $absen['izin']; ?></span>
            </div>
            <div class="nilai-mini-row">
                <span>Alpa</span>
                <span>:</span>
                <span class="nilai-mini-value"><?= (int) $absen['alpa']; ?></span>
            </div>
        </div>

        <!-- Hasil -->
        <div class="col-sm-6 col-lg-2">
            <div class="nilai-section-title">Hasil</div>
            <!-- Total Nilai -->
            <div class="nilai-result-row">
                <span>Total Nilai</span>
                <span>:</span>
                <strong><?= number_format((float) $total, 2, ',', '.'); ?></strong>
            </div>
            <!-- Rata-Rata -->
            <div class="nilai-result-row">
                <span>Rata-Rata</span>
                <span>:</span>
                <strong><?= number_format((float) $rataRata, 3, ',', '.'); ?></strong>
            </div>
        </div>

        <!-- Tombol Edit Nilai -->
        <div class="col-lg-2 text-end nilai-edit-area">
            <?php if (!in_array(current_user()['role'], ['wakasek', 'kepala_sekolah', 'tu'])): ?>
            <a href="<?= e(url('nilai/edit&id=' . $riwayat['id_riwayat'])); ?>" class="btn btn-warning px-3 fw-medium text-white">
                <i class="bi bi-pencil-square me-1"></i>Edit Nilai
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-4">
        <!-- Mata Pelajaran -->
        <div class="col-lg-5">
            <h6 class="fw-bold mb-2">Mata Pelajaran</h6>
            <div class="table-responsive">
                <!-- Table Mata Pelajaran -->
                <table class="table table-bordered nilai-detail-table mb-0 align-middle">
                    <thead>
                        <!-- Header Table Mata Pelajaran -->
                        <tr>
                            <th style="width: 72px;">Kode</th>
                            <th>Mata Pelajaran</th>
                            <th style="width: 88px;">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data Mata Pelajaran -->
                        <?php if (empty($mapel)): ?>
                            <!-- Jika tidak ada data -->
                            <tr>
                                <td colspan="3" class="text-center text-muted">Belum ada mata pelajaran untuk kelas ini.</td>
                            </tr>
                        <?php else: ?>
                            <!-- Jika ada data -->
                            <?php foreach ($mapel as $m): ?>
                                <!-- Ambil nilai -->
                                <?php
                                    $nilaiVal    = array_key_exists($m['id_mapel'], $nilai) ? $nilai[$m['id_mapel']] : false;
                                    $belumDiisi  = ($nilaiVal === false || $nilaiVal === null || $nilaiVal === '');
                                ?>
                                <!-- Baris Data Mata Pelajaran -->
                                <tr <?= $belumDiisi ? 'class="nilai-row-kosong"' : ''; ?>>
                                    <td class="nilai-mapel-kode"><?= e($m['kode_mapel'] ?? '-'); ?></td>
                                    <td class="nilai-mapel-nama"><?= e($m['nama_mapel']); ?></td>
                                    <td class="text-center">
                                        <!-- Jika tidak ada nilai -->
                                        <?php if ($belumDiisi): ?>
                                            <span class="nilai-kosong-mark">—</span>
                                        <?php else: ?>
                                            <!-- Format Nilai -->
                                            <?= number_format((float) $nilaiVal, 2, ',', '.'); ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-lg-3">
            <h6 class="fw-bold mb-2">Ekstrakurikuler</h6>
            <div class="table-responsive">
                <table class="table table-bordered nilai-detail-table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Ekstrakurikuler</th>
                            <th style="width: 90px;">Predikat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ekskul)): ?>
                            <tr class="nilai-table-empty">
                                <td class="text-center text-muted">-</td>
                                <td class="text-center text-muted">-</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ekskul as $e): ?>
                                <tr>
                                    <td><?= e($e['nama_ekskul']); ?></td>
                                    <td class="text-center"><?= e($e['predikat']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-lg-4">
            <h6 class="fw-bold mb-2">Prestasi</h6>
            <div class="table-responsive">
                <table class="table table-bordered nilai-detail-table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Prestasi</th>
                            <th style="width: 130px;">Tingkat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($prestasi)): ?>
                            <tr class="nilai-table-empty">
                                <td class="text-center text-muted">-</td>
                                <td class="text-center text-muted">-</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($prestasi as $p): ?>
                                <tr>
                                    <td><?= e($p['nama_prestasi']); ?></td>
                                    <td class="text-center"><?= e($p['tingkat']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
