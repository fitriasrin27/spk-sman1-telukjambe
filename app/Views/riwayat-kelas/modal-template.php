<!-- MODAL DOWNLOAD TEMPLATE RIWAYAT KELAS -->
<div class="modal fade" id="modalDownloadTemplateRiwayat" tabindex="-1" aria-labelledby="modalDownloadTemplateRiwayatLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content" method="GET" action="<?= e(url('')); ?>">
            <input type="hidden" name="url" value="riwayat-kelas/template">
            <!-- Header Modal -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalDownloadTemplateRiwayatLabel">
                    <i class="bi bi-file-earmark-arrow-down me-2 text-success"></i>Download Template Riwayat Kelas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <!-- Body Modal -->
            <div class="modal-body bg-light">
                <!-- Info Template -->
                <div class="alert alert-info small mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Pilih <strong>Tahun Ajaran</strong>, <strong>Kelas</strong>, dan <strong>Semester</strong> untuk mendapatkan template Excel yang sudah terisi otomatis dengan daftar NISN dan Nama Siswa pada kelas tersebut.
                </div>
                
                <!-- Form Filter -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Tahun Ajaran -->
                            <div class="col-md-4">
                                <label class="form-label fw-medium small text-muted">Tahun Ajaran (Sebelumnya) <span class="text-danger">*</span></label>
                                <select name="tahun_ajaran" class="form-select" required>
                                    <option value="">-- Pilih Tahun Ajaran --</option>
                                    <?php foreach ($daftarTahunAjaran as $ta): ?>
                                        <option value="<?= e($ta); ?>" <?= $tahunAjaran === $ta ? 'selected' : ''; ?>><?= e($ta); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Kelas -->
                            <div class="col-md-4">
                                <label class="form-label fw-medium small text-muted">Kelas (Sebelumnya) <span class="text-danger">*</span></label>
                                <select name="kelas" class="form-select" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    <?php foreach ($daftarKelas as $k): ?>
                                        <option value="<?= e($k); ?>" <?= $kelas === $k ? 'selected' : ''; ?>><?= e($k); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Semester -->
                            <div class="col-md-4">
                                <label class="form-label fw-medium small text-muted">Semester (Sebelumnya) <span class="text-danger">*</span></label>
                                <select name="semester" class="form-select" required>
                                    <option value="">-- Pilih Semester --</option>
                                    <option value="ganjil" <?= in_array((int)$semester, [1, 3, 5]) ? 'selected' : ''; ?>>Ganjil</option>
                                    <option value="genap" <?= in_array((int)$semester, [2, 4, 6]) ? 'selected' : ''; ?>>Genap</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Info Catatan -->
                <div class="text-muted small mt-3">
                    <i class="bi bi-exclamation-triangle me-1 text-warning"></i>
                    <strong>Catatan:</strong> Pilih kelas dari semester lalu. File Excel yang ter-download akan berisi daftar siswa di kelas tersebut. Anda tinggal mengubah kolom Tahun Ajaran, Kelas, dan Semester di Excel ke data yang baru untuk melakukan kenaikan kelas secara massal.
                </div>
            </div>
            <!-- Footer Modal -->
            <div class="modal-footer bg-white d-flex justify-content-between">
                <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#modalUploadRiwayat" data-bs-dismiss="modal">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </button>
                <button type="submit" class="btn btn-success fw-medium">
                    <i class="bi bi-download me-1"></i>Download Template
                </button>
            </div>
        </form>
    </div>
</div>
