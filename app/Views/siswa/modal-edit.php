<!-- MODAL EDIT -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <!-- Form Edit Siswa -->
        <form class="modal-content" method="POST" action="<?= e(url('siswa/update')); ?>">
            <!-- ID Siswa -->
            <input type="hidden" name="id_siswa" id="editId">

            <!-- Header Modal -->
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="modalEditLabel">
                    <i class="bi bi-pencil-square me-2" style="color:#f59e0b;"></i>Edit Data Siswa
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <!-- Body Modal -->
            <div class="modal-body">
                <!-- Nama Siswa -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Nama Lengkap <span class="text-danger">*</span></label>
                    <input class="form-control" type="text" name="nama" id="editNama" placeholder="Nama lengkap siswa" required>
                </div>
                <div class="row g-3 mb-3">
                    <!-- NISN -->
                    <div class="col">
                        <label class="form-label fw-semibold small">NISN <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="nisn" id="editNisn" required>
                    </div>
                    <!-- NIS -->
                    <div class="col">
                        <label class="form-label fw-semibold small">NIS <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="nis" id="editNis" required>
                    </div>
                </div>
                <!-- Jenis Kelamin -->
                <div class="mb-0">
                    <label class="form-label fw-semibold small">Jenis Kelamin <span class="text-danger">*</span></label>
                    <select class="form-select" name="jenis_kelamin" id="editGender" required>
                        <option value="L">Laki-Laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
