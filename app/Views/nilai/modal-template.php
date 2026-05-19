<!-- MODAL DOWNLOAD TEMPLATE -->
<div class="modal fade" id="modalDownloadTemplate" tabindex="-1" aria-labelledby="modalDownloadTemplateLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content" method="GET" action="<?= e(url('')); ?>">
            <input type="hidden" name="url" value="nilai/template">
            <!-- modal header -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalDownloadTemplateLabel">
                    <i class="bi bi-file-earmark-arrow-down me-2 text-success"></i>Download Template Nilai
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <!-- modal body -->
            <div class="modal-body bg-light">
                <div class="alert alert-info small mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Pilih <strong>Tahun Ajaran</strong>, <strong>Kelas</strong>, dan <strong>Semester</strong> untuk mendapatkan template Excel yang sudah terisi otomatis dengan daftar nama siswa dan mata pelajaran yang sesuai.
                </div>
                <!-- card -->
                <div class="card border-0 shadow-sm">
                    <!-- card body -->
                    <div class="card-body">
                        <div class="row g-3">
                            <!--tahun ajaran-->
                            <div class="col-md-4">
                                <label class="form-label fw-medium small text-muted">Tahun Ajaran <span class="text-danger">*</span></label>
                                <select name="tahun_ajaran" class="form-select" required>
                                    <option value="">-- Pilih Tahun Ajaran --</option>
                                    <?php foreach ($optTahunAjaran as $ta): ?>
                                        <option value="<?= e($ta); ?>" <?= $tahunAjaran === $ta ? 'selected' : ''; ?>><?= e($ta); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- kelas -->
                            <div class="col-md-4">
                                <label class="form-label fw-medium small text-muted">Kelas <span class="text-danger">*</span></label>
                                <select name="kelas" class="form-select" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    <?php foreach ($optKelas as $k): ?>
                                        <option value="<?= e($k); ?>" <?= $kelas === $k ? 'selected' : ''; ?>><?= e($k); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- semester -->
                            <div class="col-md-4">
                                <label class="form-label fw-medium small text-muted">Semester <span class="text-danger">*</span></label>
                                <select name="semester" class="form-select" required>
                                    <option value="">-- Pilih Semester --</option>
                                    <option value="ganjil" <?= in_array((int)$semester, [1, 3, 5]) ? 'selected' : ''; ?>>Ganjil</option>
                                    <option value="genap" <?= in_array((int)$semester, [2, 4, 6]) ? 'selected' : ''; ?>>Genap</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- note -->
                <div class="text-muted small mt-3">
                    <i class="bi bi-exclamation-triangle me-1 text-warning"></i>
                    <strong>Catatan:</strong> Perhatikan kelas dan semesternya, pastikan identitas siswa sudah ada sebelumnya (di menu Siswa / Riwayat Kelas) agar seluruh siswa di kelas tersebut otomatis masuk ke dalam 1 file template.
                </div>
            </div>
            <!-- modal footer -->
            <div class="modal-footer bg-white d-flex justify-content-between">
                <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#modalUploadNilai" data-bs-dismiss="modal">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </button>
                <button type="submit" class="btn btn-success fw-medium">
                    <i class="bi bi-download me-1"></i>Download Template
                </button>
            </div>
        </form>
    </div>
</div>
