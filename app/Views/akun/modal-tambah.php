<!-- MODAL TAMBAH AKUN -->
<div class="modal fade" id="modalAkun" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <!-- Form Akun -->
        <form id="formAkun" action="<?= url('akun/store') ?>" method="POST" class="modal-content border-0 shadow">
            <!-- Hidden ID -->
            <input type="hidden" name="id_user" id="userId">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalTitle">
                    <i class="bi bi-person-plus-fill me-2"></i>Tambah Akun Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body py-4">
                <!-- Nama Lengkap -->
                <div class="mb-3">
                    <label class="form-label fw-medium small">Nama Lengkap</label>
                    <input type="text" name="nama" id="userNama" class="form-control" placeholder="Masukkan nama..." required>
                </div>
                <!-- Posisi -->
                <div class="mb-3">
                    <label class="form-label fw-medium small">Posisi</label>
                    <input type="text" name="posisi" id="userPosisi" class="form-control" placeholder="Contoh: Staff IT, Guru BK, dll" required>
                </div>
                <div class="row g-3 mb-3">
                    <!-- Username -->
                    <div class="col-md-6">
                        <label class="form-label fw-medium small">Username</label>
                        <input type="text" name="username" id="userUsername" class="form-control" placeholder="username" required>
                    </div>
                    <!-- Password -->
                    <div class="col-md-6">
                        <label class="form-label fw-medium small">Password</label>
                        <div class="input-group input-group-sm">
                            <input class="form-control" type="password" id="password" name="password" placeholder="Ketik Password disini..." autocomplete="current-password" required>
                            <button class="btn" type="button" id="togglePassword" aria-label="Tampilkan password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Role -->
                <div class="mb-0">
                    <label class="form-label fw-medium small">Role / Hak Akses</label>
                    <select name="role" id="userRole" class="form-select" required>
                        <option value="" disabled selected>Pilih Role...</option>
                        <option value="operator">Operator (OP)</option>
                        <option value="wali_kelas">Wali Kelas (Walas)</option>
                        <option value="bk">Bimbingan Konseling (BK)</option>
                        <option value="tu">Tata Usaha (TU)</option>
                        <option value="wakasek">Wakasek Kurikulum (Wakasek)</option>
                        <option value="kepala_sekolah">Kepala Sekolah (Kepsek)</option>
                    </select>
                    <div class="form-text text-info small mt-2" id="passwordHint" style="display:none;">
                        <i class="bi bi-info-circle me-1"></i>Kosongkan jika tidak ingin mengubah password.
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary px-4 fw-medium shadow-sm">
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>
