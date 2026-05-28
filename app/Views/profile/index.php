<div class="container-fluid py-4">
    <!-- Notifikasi -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="this.parentElement.remove()"></button>
        </div>
    <?php endif; ?>
    
    <div class="row g-4">
        <!-- Kolom Kiri: Visual Identity -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <!-- Header Card -->
                <div class="card-body p-4 text-center">
                    <!-- Foto Profil -->
                    <div class="profile-avatar-wrapper mb-4 position-relative d-inline-block">
                        <?php 
                        $fotoPath = !empty($user['foto']) ? asset('public/uploads/profile/' . $user['foto']) : null;
                        if ($fotoPath): ?>
                            <img src="<?= $fotoPath ?>" id="previewFoto" class="rounded-circle shadow-sm border border-4 border-white" style="width: 150px; height: 150px; object-fit: cover;">
                            <!-- Tombol Hapus Foto -->
                            <button type="button" class="btn btn-sm btn-danger rounded-circle position-absolute bottom-0 start-0 shadow profile-avatar-action-btn" id="btnHapusFoto" title="Hapus Foto Profil">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        <?php else: ?>
                            <div id="initialsAvatar" class="rounded-circle shadow-sm d-flex align-items-center justify-content-center bg-primary text-white fw-bold fs-1 border border-4 border-white" style="width: 150px; height: 150px;">
                                <?php 
                                    $words = explode(" ", $user['nama']);
                                    $initials = "";
                                    foreach ($words as $w) $initials .= $w[0];
                                    echo strtoupper(substr($initials, 0, 2));
                                ?>
                            </div>
                            <img src="" id="previewFoto" class="rounded-circle shadow-sm border border-4 border-white d-none" style="width: 150px; height: 150px; object-fit: cover;">
                        <?php endif; ?>
                        <!-- Form Ganti Foto -->
                        <form action="<?= e(url('profile/update-foto')) ?>" method="POST" id="formUpdateFoto">
                            <input type="hidden" name="cropped_image" id="croppedImageInput">
                            <label for="inputFoto" class="btn btn-sm btn-dark rounded-circle position-absolute bottom-0 end-0 shadow profile-avatar-action-btn" style="cursor: pointer;" title="Ganti Foto">
                                <i class="bi bi-camera-fill"></i>
                            </label>
                            <input type="file" name="foto" id="inputFoto" class="d-none" accept="image/*">
                        </form>
                        <!-- Form Hapus Foto -->
                        <form action="<?= e(url('profile/delete-foto')) ?>" method="POST" id="formHapusFoto">
                        </form>
                    </div>

                    <!-- Nama User -->
                    <h4 class="fw-bold mb-1"><?= e($user['nama']) ?></h4>
                    <!-- Username User -->
                    <p class="text-muted small mb-3">@<?= e($user['username']) ?></p>
                    
                    <!-- Role Badge -->
                    <?php 
                        $roleColors = [
                            'operator'       => 'bg-primary',
                            'kepala_sekolah' => 'bg-danger',
                            'wakasek'        => 'bg-info',
                            'tu'             => 'bg-warning text-dark',
                            'bk'             => 'bg-purple',
                            'wali_kelas'     => 'bg-success'
                        ];
                        $roleLabels = [
                            'operator'       => 'Operator',
                            'kepala_sekolah' => 'Kepala Sekolah',
                            'wakasek'        => 'Wakil Kepala Sekolah',
                            'tu'             => 'Tata Usaha',
                            'bk'             => 'Bimbingan Konseling',
                            'wali_kelas'     => 'Wali Kelas'
                        ];

                        // Role Badge
                        $badgeClass = $roleColors[$user['role']] ?? 'bg-secondary';
                        $roleName = $roleLabels[$user['role']] ?? strtoupper($user['role']);
                    ?>

                    <!-- Role Badge -->
                    <span class="badge <?= $badgeClass ?> px-3 py-2 rounded-pill mb-4 shadow-sm"><?= $roleName ?></span>

                    <!-- Jabatan / Posisi -->
                    <div class="row g-2 text-start mt-2">
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3">
                                <label class="text-muted small d-block mb-1">JABATAN / POSISI</label>
                                <span class="fw-bold text-dark"><?= !empty($user['posisi'] ?? '') ? e($user['posisi']) : '<i class="text-muted fw-normal small">Belum diatur</i>' ?></span>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Settings -->
        <div class="col-12 col-lg-8">
            <!-- Card: Edit Profile -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-person-gear text-primary me-2"></i>Informasi Pribadi</h5>
                </div>

                <!-- Card Body -->
                <div class="card-body p-4 pt-0">
                    <form action="<?= e(url('profile/update')) ?>" method="POST">
                        <div class="row g-3">

                            <!-- Nama Lengkap -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control rounded-3" value="<?= e($user['nama']) ?>" required>
                            </div>

                            <!-- Username -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Username</label>
                                <input type="hidden" name="username" value="<?= e($user['username']) ?>">
                                <div class="input-group">
                                    <input type="text" class="form-control rounded-start-3 bg-light" value="<?= e($user['username']) ?>" disabled>
                                    <span class="input-group-text bg-light text-muted" title="Username hanya dapat diubah oleh Operator">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>
                                </div>
                                <div class="form-text" style="font-size:0.72rem;">Hubungi Operator untuk mengubah username.</div>
                            </div>
                            
                            <!-- Jabatan / Posisi -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Jabatan / Posisi</label>
                                <input type="text" name="posisi" class="form-control rounded-3" value="<?= e($user['posisi'] ?? '') ?>" placeholder="Misal: Staf Tata Usaha">
                            </div>
                            
                            <!-- Tombol Simpan Perubahan -->
                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Card: Change Password -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-lock text-danger me-2"></i>Keamanan Akun</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <form action="<?= e(url('profile/change-password')) ?>" method="POST">
                        <div class="row g-3">
                            <!-- Password Saat Ini -->
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">Password Saat Ini</label>
                                <div class="input-group">
                                    <input type="password" id="old_password" name="old_password" class="form-control rounded-start-3"
                                           value="<?= e($user['password_plain'] ?? '') ?>" placeholder="••••••••" required>
                                    <button class="btn rounded-end-3 btn-outline-secondary" type="button" id="toggleOldPass" aria-label="Tampilkan password">
                                        <i class="bi bi-eye" id="iconOldPass"></i>
                                    </button>
                                </div>
                                <!-- Info Password -->
                                <div class="form-text" style="font-size:0.72rem;"><i class="bi bi-info-circle me-1 text-primary"></i>Password ditampilkan untuk kemudahan akses internal.</div>
                            </div>
                            <!-- Password Baru -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Password Baru</label>
                                <div class="input-group">
                                    <input class="form-control rounded-start-3" type="password" id="new_password" name="new_password" placeholder="Minimal 8 karakter dengan kombinasi huruf dan angka" required>
                                    <button class="btn btn-rounded-end-3" type="button" id="toggleNewPass" aria-label="Tampilkan password">
                                        <i class="bi bi-eye" id="iconNewPass"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- Konfirmasi Password Baru -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <input class="form-control rounded-start-3" type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi password baru" required>
                                    <button class="btn btn-rounded-end-3" type="button" id="toggleConfirmPass" aria-label="Tampilkan password">
                                        <i class="bi bi-eye" id="iconConfirmPass"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- Tombol Ganti Password -->
                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-danger px-4 py-2 rounded-pill fw-bold shadow-sm">
                                    Ganti Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal Crop Foto Profil -->
<div class="modal fade" id="modalCrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalCropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalCropLabel">Sesuaikan Foto Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <div class="img-container mb-3 d-flex justify-content-center align-items-center" style="max-height: 350px; min-height: 250px; overflow: hidden; background: #f8f9fa; border-radius: 8px;">
                    <img id="imageToCrop" src="" style="max-width: 100%; max-height: 350px; display: block;">
                </div>
                <!-- Control Buttons -->
                <div class="d-flex justify-content-center gap-1 mb-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" id="btnZoomIn" title="Perbesar">
                        <i class="bi bi-zoom-in"></i> Perbesar
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" id="btnZoomOut" title="Perkecil">
                        <i class="bi bi-zoom-out"></i> Perkecil
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-2.5" id="btnRotateLeft" title="Putar Kiri">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-2.5" id="btnRotateRight" title="Putar Kanan">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-between">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" id="btnCropSave">Potong & Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Foto Profil -->
<div class="modal fade" id="modalHapusFoto" tabindex="-1" aria-labelledby="modalHapusFotoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-body text-center p-4">
                <!-- Icon Peringatan -->
                <div class="mb-3">
                    <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                </div>
                <!-- Judul Modal -->
                <h5 class="fw-bold mb-2" id="modalHapusFotoLabel">Hapus Foto Profil?</h5>
                <!-- Deskripsi -->
                <p class="text-muted small mb-4">
                    Apakah Anda yakin ingin menghapus foto profil Anda? Tampilan akan dikembalikan menggunakan inisial nama Anda secara otomatis.
                </p>
                <!-- Tombol Aksi -->
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-danger fw-medium py-2 rounded-pill" id="btnKonfirmasiHapusFoto">Ya, Hapus Foto</button>
                    <button type="button" class="btn btn-light py-2 rounded-pill" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<link rel="stylesheet" href="<?= e(asset('assets/css/pages/profile.css')); ?>">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<script src="<?= e(asset('assets/js/pages/profile.js')); ?>"></script>
