<!-- MODAL UPLOAD EXCEL – RIWAYAT KELAS -->
<div class="modal fade" id="modalUploadRiwayat" tabindex="-1" aria-labelledby="modalUploadRiwayatLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content" method="POST" action="<?= e(url('riwayat-kelas/import')); ?>" enctype="multipart/form-data">
            <!-- Header Modal -->
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="modalUploadRiwayatLabel">
                    <i class="bi bi-file-earmark-excel me-2 text-success"></i>Upload Data Excel
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <!-- Body Modal -->
            <div class="modal-body">
                <div class="alert alert-info small mb-4">
                    <i class="bi bi-info-circle me-1"></i>
                    Format file: <strong>.xlsx / .xls</strong>. Kolom yang diperlukan (header kolom wajib ada, urutan bebas):
                    <!-- Info Data Siswa -->
                    <div class="row mt-2 mb-0">
                        <div class="col-6">
                            <ul class="ps-3 mb-0">
                                <li><strong>Nama</strong> — Nama lengkap siswa</li>
                                <li><strong>NISN</strong> — 10 digit NISN siswa</li>
                                <li><strong>Tahun Ajaran</strong> — contoh: <em>2024/2025</em></li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <ul class="ps-3 mb-0">
                                <li><strong>Kelas</strong> — contoh: <em>XII MIPA 1</em></li>
                                <li><strong>Semester</strong> — isi <em>Ganjil</em> atau <em>Genap</em></li>
                            </ul>
                        </div>
                    </div>
                    <!-- Info Upload -->
                    <div class="mt-2 text-muted">
                        <i class="bi bi-shield-check me-1"></i>
                        Penempatan yang sudah ada akan <strong>diabaikan</strong>.
                        NISN yang tidak terdaftar di identitas siswa akan <strong>dilewati</strong>.
                    </div>
                </div>

                <!-- Input File Excel -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Pilih File Excel <span class="text-danger">*</span></label>
                    <input class="form-control" type="file" name="excel_file" accept=".xlsx,.xls" id="excelFileInputRk">
                    <!-- Download Template -->
                    <div class="form-text mt-2">
                        Butuh template kenaikan kelas? <a href="#" data-bs-toggle="modal" data-bs-target="#modalDownloadTemplateRiwayat" data-bs-dismiss="modal" class="text-decoration-none fw-medium"><i class="bi bi-download me-1"></i>Download Template</a>
                    </div>
                </div>
            </div>
            <!-- Footer Modal -->
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-upload me-1"></i>Upload
                </button>
            </div>
        </form>
    </div>
</div>
