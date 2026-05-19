<!-- MODAL EDIT AKUN -->
<div class="modal fade" id="modalEditAkun" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <!-- Form Akun -->
        <form id="formEditAkun" action="<?= url('akun/update') ?>" method="POST" class="modal-content border-0 shadow">
            <!-- Hidden ID -->
            <input type="hidden" name="id_user" id="editUserId">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Edit Informasi Akun
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body py-4">
                <!-- Nama Lengkap -->
                <div class="mb-3">
                    <label class="form-label fw-medium small">Nama Lengkap</label>
                    <input type="text" name="nama" id="editUserNama" class="form-control" placeholder="Masukkan nama..." required>
                </div>
                <!-- Posisi -->
                <div class="mb-3">
                    <label class="form-label fw-medium small">Posisi</label>
                    <input type="text" name="posisi" id="editUserPosisi" class="form-control" placeholder="Contoh: Staff IT, Guru BK, dll" required>
                </div>
                <div class="row g-3 mb-3">
                    <!-- Username -->
                    <div class="col-md-6">
                        <label class="form-label fw-medium small">Username</label>
                        <input type="text" name="username" id="editUserUsername" class="form-control" placeholder="username" required>
                    </div>
                    <!-- Password -->
                    <div class="col-md-6">
                        <label class="form-label fw-medium small">Password Baru (Opsional)</label>
                        <div class="input-group input-group-sm">
                            <input class="form-control" type="password" id="editPassword" name="password" placeholder="Ketik Password baru..." autocomplete="current-password">
                            <button class="btn" type="button" id="toggleEditPassword" aria-label="Tampilkan password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <!-- Password Saat Ini -->
                        <div class="mt-1" style="font-size: 0.7rem;">
                            <span class="text-muted">Password saat ini:</span> 
                            <span id="currentPasswordDisplay" class="fw-semibold text-dark"></span>
                        </div>
                    </div>
                </div>
                <!-- Role -->
                <div class="mb-0">
                    <label class="form-label fw-medium small">Role / Hak Akses</label>
                    <select name="role" id="editUserRole" class="form-select" required>
                        <option value="" disabled selected>Pilih Role...</option>
                        <option value="operator">Operator (OP)</option>
                        <option value="wali_kelas">Wali Kelas (Walas)</option>
                        <option value="bk">Bimbingan Konseling (BK)</option>
                        <option value="tu">Tata Usaha (TU)</option>
                        <option value="wakasek">Wakasek Kurikulum (Wakasek)</option>
                        <option value="kepala_sekolah">Kepala Sekolah (Kepsek)</option>
                    </select>
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
