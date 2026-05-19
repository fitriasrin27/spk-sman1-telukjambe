<!-- EDIT NILAI -->
<?php
$semesterLabel = ((int) $riwayat['semester'] % 2 === 0) ? 'Genap' : 'Ganjil';
?>
<!-- Import CSS -->
<link rel="stylesheet" href="<?= e(asset('assets/css/pages/nilai.css')); ?>">
<!-- Data PHP dioper ke JS eksternal via data-attribute -->
<div id="editNilaiData" class="d-none"
    data-ekskul="<?= e(json_encode($ekskul, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)); ?>"
    data-prestasi="<?= e(json_encode($prestasi, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)); ?>">
</div>

<!-- Judul Halaman -->
<div class="page-header">
    <h2 class="page-title">Data Nilai</h2>
</div>

<!-- Tombol Kembali -->
<div class="nilai-detail-back">
    <a href="<?= e(url('nilai/detail&id=' . $riwayat['id_riwayat'])); ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<!-- Box Judul Detail Nilai Siswa -->
<div class="nilai-detail-title-box">
    <h4 class="mb-0 fw-bold">Edit Nilai Siswa</h4>
</div>

<!-- Form Edit Nilai Siswa -->
<form action="<?= e(url('nilai/update')); ?>" method="POST" id="formEditNilai">
    <input type="hidden" name="id_riwayat" value="<?= e($riwayat['id_riwayat']); ?>">
    <input type="hidden" name="redirect_to" value="detail">

    <!-- Panel Detail Nilai Siswa -->
    <div class="nilai-detail-panel">
        <!-- ===== BARIS ATAS: Info Siswa | Absensi | Hasil | Tombol ===== -->
        <div class="row g-4 mb-4 align-items-start">
            <!-- Info Siswa (read-only, sama seperti detail) -->
            <div class="col-lg-5">
                <table class="table table-borderless table-sm mb-0 align-middle nilai-info-table">
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

            <!-- Absensi (editable, style mirip badge detail) -->
            <div class="col-sm-6 col-lg-3">
                <div class="nilai-section-title">Absensi</div>
                <div class="nilai-absen-row">
                    <span>Sakit</span>
                    <span>:</span>
                    <input type="number" name="absen[sakit]" class="nilai-absen-input" min="0"
                           value="<?= (int) $absen['sakit']; ?>">
                </div>
                <div class="nilai-absen-row">
                    <span>Izin</span>
                    <span>:</span>
                    <input type="number" name="absen[izin]" class="nilai-absen-input" min="0"
                           value="<?= (int) $absen['izin']; ?>">
                </div>
                <div class="nilai-absen-row">
                    <span>Alpa</span>
                    <span>:</span>
                    <input type="number" name="absen[alpa]" class="nilai-absen-input" min="0"
                           value="<?= (int) $absen['alpa']; ?>">
                </div>
            </div>

            <!-- Hasil (dihitung otomatis, sama seperti detail tapi di-update JS) -->
            <div class="col-sm-6 col-lg-2">
                <div class="nilai-section-title">Hasil</div>
                <!-- Total Nilai -->
                <div class="nilai-result-row">
                    <span>Total Nilai</span>
                    <span>:</span>
                    <strong id="editTotalNilai"><?= number_format((float) $total, 2, ',', '.'); ?></strong>
                </div>
                <!-- Rata-Rata -->
                <div class="nilai-result-row">
                    <span>Rata-Rata</span>
                    <span>:</span>
                    <strong id="editRataRata"><?= number_format((float) $rataRata, 2, ',', '.'); ?></strong>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="col-lg-2 nilai-edit-action-area">
                <!-- Simpan -->
                <button type="submit" class="btn btn-primary fw-medium shadow-sm px-3">
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
                <!-- Batal -->
                <a href="<?= e(url('nilai/detail&id=' . $riwayat['id_riwayat'])); ?>"
                   class="btn btn-light border fw-medium shadow-sm px-4">
                    <i class="bi bi-x-circle me-1"></i>Batal
                </a>
            </div>
        </div>

        <!-- ===== BARIS BAWAH: Mata Pelajaran | Ekskul | Prestasi ===== -->
        <div class="row g-4">

            <!-- Mata Pelajaran -->
            <div class="col-lg-5">
                <div class="nilai-section-header">
                    <h6 class="fw-bold mb-0">Mata Pelajaran</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered nilai-detail-table mb-0 align-middle">
                        <!-- Header Tabel -->
                        <thead>
                            <tr>
                                <th style="width: 72px;">Kode</th>
                                <th>Mata Pelajaran</th>
                                <th style="width: 88px;">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Jika Tidak Ada Data -->
                            <?php if (empty($mapel)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada mata pelajaran untuk kelas ini.</td>
                                </tr>
                            <!-- Jika Ada Data -->
                            <?php else: ?>
                                <?php foreach ($mapel as $idx => $m): ?>
                                    <!-- Isi Mata Pelajaran -->
                                    <tr>
                                        <td class="nilai-mapel-kode">
                                            <?= e($m['kode_mapel'] ?? '-'); ?>
                                        </td>
                                        <td class="nilai-mapel-nama">
                                            <?= e($m['nama_mapel']); ?>
                                            <input type="hidden" name="nilai[<?= $idx; ?>][id_mapel]"
                                                   value="<?= e($m['id_mapel']); ?>">
                                        </td>
                                        <!-- Nilai Angka -->
                                        <td class="text-center p-1">
                                            <input type="number"
                                                   name="nilai[<?= $idx; ?>][nilai_angka]"
                                                   class="nilai-input-mapel edit-nilai-mapel"
                                                   step="0.01" min="0" max="100"
                                                   value="<?= isset($nilai[$m['id_mapel']]) ? e($nilai[$m['id_mapel']]) : ''; ?>"
                                                   placeholder="00.00">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Ekstrakulikuler -->
            <div class="col-lg-3">
                <!-- Header Ekstrakulikuler -->
                <div class="nilai-section-header">
                    <h6 class="fw-bold mb-0">Ekstrakulikuler</h6>
                    <button type="button" class="nilai-btn-tambah-baris" id="btnEditTambahEkskul">
                        <i class="bi bi-plus-lg"></i> Baris
                    </button>
                </div>
                <!-- Tabel Ekstrakulikuler -->
                <div class="table-responsive">
                    <table class="table table-bordered nilai-detail-table mb-0 align-middle" id="editEkskulTable">
                        <thead>
                            <!-- Header Tabel -->
                            <tr>
                                <th>Ekstrakulikuler</th>
                                <th style="width: 72px;">Predikat</th>
                                <th style="width: 40px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <!-- Prestasi -->
            <div class="col-lg-4">
                <!-- Header Prestasi -->
                <div class="nilai-section-header">
                    <h6 class="fw-bold mb-0">Prestasi</h6>
                    <button type="button" class="nilai-btn-tambah-baris" id="btnEditTambahPrestasi">
                        <i class="bi bi-plus-lg"></i> Baris
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered nilai-detail-table mb-0 align-middle" id="editPrestasiTable">
                        <thead>
                            <!-- Header Tabel -->
                            <tr>
                                <th>Prestasi</th>
                                <th style="width: 110px;">Tingkat</th>
                                <th style="width: 40px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

        </div>
    </div><!-- /.nilai-detail-panel -->
</form>


<script src="<?= e(asset('assets/js/pages/nilai.js')); ?>"></script>



