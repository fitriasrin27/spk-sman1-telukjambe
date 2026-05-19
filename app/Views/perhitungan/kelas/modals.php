<!-- MODAL HITUNG KELAS -->
<?php if ($tahunAjaran && $kelas && $semester && current_user()['role'] !== 'tu'): ?>
<div class="modal fade" id="modalHitung" tabindex="-1" aria-labelledby="modalHitungLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <!-- Form Hitung -->
        <form class="modal-content" method="POST" action="<?= e(url('perhitungan/hitung-kelas')) ?>">
            <input type="hidden" name="tahun_ajaran" value="<?= e($tahunAjaran) ?>">
            <input type="hidden" name="kelas" value="<?= e($kelas) ?>">
            <input type="hidden" name="semester" value="<?= e($semester) ?>">
            
            <!-- Header Modal -->
            <div class="modal-header border-0 pb-0">
                <!-- Judul Modal -->
                <h5 class="modal-title fw-bold" id="modalHitungLabel">
                    <i class="bi bi-calculator text-primary me-2"></i>Konfirmasi Hitung Peringkat
                </h5>
                <!-- Tombol Tutup -->
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <!-- Body Modal -->
            <div class="modal-body">
                <!-- Informasi Filter -->
                <div class="p-3 bg-light rounded mb-3">
                    <div class="row text-center g-2">
                        <!-- Tahun Ajaran -->
                        <div class="col-4">
                            <div class="small text-muted">Tahun Ajaran</div>
                            <div class="fw-semibold"><?= e($tahunAjaran) ?></div>
                        </div>
                        <!-- Kelas -->
                        <div class="col-4">
                            <div class="small text-muted">Kelas</div>
                            <div class="fw-semibold"><?= e($kelas) ?></div>
                        </div>
                        <!-- Semester -->
                        <div class="col-4">
                            <div class="small text-muted">Semester</div>
                            <div class="fw-semibold"><?= ucfirst(e($semester)) ?></div>
                        </div>
                    </div>
                </div>
                <!-- Informasi Tambahan -->
                <div class="alert alert-info small mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Proses ini akan menghitung peringkat menggunakan data nilai, absensi, ekskul, dan prestasi dari
                    <strong>semester <?= ucfirst(e($semester)) ?></strong> tahun ajaran <strong><?= e($tahunAjaran) ?></strong>
                    untuk setiap siswa di kelas <strong><?= e($kelas) ?></strong>.
                    Hasil perhitungan sebelumnya untuk filter yang sama <strong>tidak akan dihapus</strong> — tersimpan sebagai riwayat.
                </div>
            </div>
            
            <!-- Footer Modal -->
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-play-circle me-1"></i>Mulai Hitung
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- MODAL HAPUS -->
<?php if (current_user()['role'] !== 'tu'): ?>
<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <!-- Form Hapus -->
        <form class="modal-content" method="POST" action="<?= e(url('perhitungan/hapus-batch')) ?>">
            <input type="hidden" name="id_perhitungan" id="hapusId">
            <input type="hidden" name="tahun_ajaran" value="<?= e($tahunAjaran) ?>">
            <input type="hidden" name="kelas" value="<?= e($kelas) ?>">
            <input type="hidden" name="semester" value="<?= e($semester) ?>">
            
            <!-- Header Modal -->
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger">
                    <i class="bi bi-trash me-2"></i>Hapus Riwayat Perhitungan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <!-- Body Modal -->
            <div class="modal-body">
                <p class="mb-1">Yakin ingin menghapus riwayat perhitungan ini?</p>
                <p class="text-danger fw-bold text-center fs-6 my-3" id="hapusInfo"></p>
                <div class="alert alert-warning small mt-3 mb-0">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Seluruh data hasil peringkat dalam batch ini akan ikut terhapus dan <strong>tidak dapat dikembalikan</strong>.
                </div>
            </div>
            
            <!-- Footer Modal -->
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger px-4">
                    <i class="bi bi-trash me-1"></i>Hapus
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- MODAL HAPUS BATCH -->
<div class="modal fade" id="modalHapusBatch" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow">
            <!-- Body Modal -->
            <div class="modal-body text-center p-4">
                <!-- Ikon -->
                <div class="mb-3">
                    <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                </div>
                <!-- Info Jumlah -->
                <h5 class="fw-bold mb-2">Hapus <span id="batchDeleteCount">0</span> Riwayat Perhitungan?</h5>
                <p class="text-muted small mb-4">
                    Riwayat perhitungan yang dipilih akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
                </p>
                <!-- Form Hapus Batch -->
                <form action="<?= e(url('perhitungan/kelas-delete-batch')); ?>" method="POST" id="formHapusBatch">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger py-2 fw-medium">Ya, Hapus Semua</button>
                        <button type="button" class="btn btn-light py-2 fw-medium" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- TOOLBAR AKSI HAPUS BATCH -->
<div id="batchToolbar" class="bg-white border-top p-3 d-none shadow-lg" 
     style="position: sticky; bottom: 0; z-index: 1020; margin: 0 -1.25rem -1.25rem -1.25rem; border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem;">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <!-- Info Data Terpilih -->
            <div class="fw-bold text-danger">
                <i class="bi bi-check2-all me-1"></i>
                <span id="selectedCount">0</span> Data Terpilih
            </div>
            <!-- Alert Deskripsi -->
            <div class="text-muted small d-none d-md-block">Centang riwayat perhitungan kelas yang ingin dihapus sekaligus.</div>
        </div>
        <!-- Tombol Aksi -->
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-light px-4" id="cancelBatch">Batal</button>
            <button type="button" class="btn btn-danger px-4 fw-medium" id="btnHapusBatch">
                <i class="bi bi-trash me-1"></i>Hapus Terpilih
            </button>
        </div>
    </div>
</div>
