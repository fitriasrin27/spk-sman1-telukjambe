<!-- MODAL UPLOAD EXCEL – NILAI -->
<div class="modal fade" id="modalUploadNilai" tabindex="-1" aria-labelledby="modalUploadNilaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content" method="POST" action="<?= e(url('nilai/import')); ?>" enctype="multipart/form-data">
            <!-- modal header -->
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="modalUploadNilaiLabel">
                    <i class="bi bi-file-earmark-excel me-2 text-success"></i>Upload Data Nilai Excel
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <!-- modal body -->
            <div class="modal-body">
                <!-- alert info -->
                <div class="alert alert-info small mb-4">
                    <i class="bi bi-info-circle me-1"></i>
                    Format file: <strong>.xlsx / .xls</strong>. Urutan kolom yang disarankan (header wajib ada, urutan bebas):
                    <div class="row mt-2 mb-0">
                        <div class="col-6">
                            <!-- list 1 -->
                            <ul class="ps-3 mb-0">
                                <li><strong>Nama</strong> — Nama lengkap siswa</li>
                                <li><strong>NISN</strong> — 10 digit NISN siswa</li>
                                <li><strong>Tahun Ajaran</strong> — contoh: <em>2024/2025</em></li>
                                <li><strong>Kelas</strong> — contoh: <em>XII MIPA 1</em></li>
                                <li><strong>Semester</strong> — isi <em>Ganjil</em> atau <em>Genap</em></li>
                                <li><strong>(Nama/Kode Mapel)</strong> — nilai tiap mata pelajaran</li>
                                <li><strong>Sakit / Izin / Alpa</strong> — data absensi</li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <!-- list 2 -->
                            <ul class="ps-3 mb-0">
                                <li><strong>Ekskul 1</strong> — nama ekskul pertama <em>(opsional)</em></li>
                                <li><strong>Predikat 1</strong> — predikat ekskul: <em>SB/B/C/K</em></li>
                                <li><strong>Ekskul 2, Predikat 2</strong> — ekskul berikutnya <em>(dst.)</em></li>
                                <li><strong>Prestasi 1</strong> — nama prestasi <em>(opsional, hapus jika tidak ada)</em></li>
                                <li><strong>Tingkat 1</strong> — contoh: <em>Nasional, Provinsi, Kota/Kab</em></li>
                                <li><strong>Prestasi 2, Tingkat 2</strong> — prestasi berikutnya <em>(dst.)</em></li>
                            </ul>
                        </div>
                    </div>
                    <!-- note -->
                    <div class="mt-2 text-muted">
                        <i class="bi bi-shield-check me-1"></i>
                        Data nilai yang sudah ada akan <strong>diperbarui</strong>.
                        Yang tidak terdaftar di identitas siswa akan <strong>dilewati</strong>.
                    </div>
                </div>
                <!-- file input -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Pilih File Excel <span class="text-danger">*</span></label>
                    <input class="form-control" type="file" name="file_excel" accept=".xlsx,.xls" id="excelFileInputNilai" required>
                    <!-- template download link -->
                    <div class="form-text mt-2">
                        Butuh template? <a href="#" data-bs-toggle="modal" data-bs-target="#modalDownloadTemplate" data-bs-dismiss="modal" class="text-decoration-none fw-medium"><i class="bi bi-download me-1"></i>Download Template</a>
                    </div>
                </div>
            </div>
            <!-- modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-upload me-1"></i>Upload
                </button>
            </div>
        </form>
    </div>
</div>
