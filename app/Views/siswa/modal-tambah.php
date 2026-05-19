<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" method="POST" action="<?= e(url('siswa/store')); ?>">

            <!-- Header Modal -->
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="modalTambahLabel">
                    <i class="bi bi-person-plus me-2 text-primary"></i>Tambah Data Siswa
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <!-- Body Modal -->
            <div class="modal-body">
                <!-- Nama -->
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama" placeholder="Masukkan Nama Lengkap Siswa" required>
                </div>

                <!-- NISN & NIS -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NISN <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nisn" placeholder="10 Digit NISN" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NIS <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nis" placeholder="9 Digit NIS" required>
                    </div>
                </div>

                <!-- Jenis Kelamin -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select class="form-select" name="jenis_kelamin" required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>

                <!-- Divider: Penempatan -->
                <div class="modal-section-divider">
                    <span>Input Penempatan Siswa Terkini</span>
                </div>

                <!-- Tahun Ajaran, Kelas, Semester -->
                <div class="row mt-3">
                    <!-- Tahun Ajaran -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="tahun_ajaran" placeholder="Cth: 2024/2025" required>
                    </div>
                    <!-- Kelas -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Kelas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="kelas" placeholder="Cth: XII MIPA 1" required>
                    </div>
                    <!-- Semester -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Semester <span class="text-danger">*</span></label>
                        <select class="form-select" name="semester_jenis" required>
                            <option value="">Pilih Semester</option>
                            <option value="ganjil">Ganjil</option>
                            <option value="genap">Genap</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>
