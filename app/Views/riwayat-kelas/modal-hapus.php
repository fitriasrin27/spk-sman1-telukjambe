<!-- MODAL HAPUS RIWAYAT KELAS -->
<div class="modal fade" id="modalHapusRiwayat" tabindex="-1" aria-labelledby="modalHapusRiwayatLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <!-- Judul Modal -->
                <h2 class="modal-title fs-5" id="modalHapusRiwayatLabel">
                    <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Hapus Penempatan
                </h2>
                <!-- Tombol Tutup -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body text-center pt-2">
                <!-- Pesan Konfirmasi -->
                <p class="mb-1">Yakin ingin menghapus penempatan kelas:</p>
                <!-- Nama Siswa -->
                <strong id="hapusRkNama" class="d-block text-danger" style="font-size: 1rem;"></strong>
                <!-- Label Semester -->
                <span id="hapusRkSemLabel" class="d-block text-muted" style="font-size: 0.8rem;"></span>
                <!-- Info Tambahan -->
                <p class="mt-2 mb-0 small text-muted">Data siswa di identitas siswa tidak akan terhapus.</p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                <!-- Tombol Batal -->
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                <a id="hapusRkLink" href="#" class="btn btn-danger px-4">
                    <i class="bi bi-trash3 me-1"></i>Hapus
                </a>
            </div>
        </div>
    </div>
</div>

<!-- TOOLBAR HAPUS BATCH RIWAYAT KELAS (Floating/Fixed at bottom of card) -->
<div id="batchToolbar" class="bg-white border-top p-3 d-none shadow-lg" 
     style="position: sticky; bottom: 0; z-index: 1020; margin: 0 -1.25rem -1.25rem -1.25rem; border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem;">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <!-- Jumlah Data Terpilih -->
            <div class="fw-bold text-danger">
                <i class="bi bi-check2-all me-1"></i>
                <span id="selectedCount">0</span> Data Terpilih
            </div>
            <!-- Info Tambahan -->
            <div class="text-muted small d-none d-md-block">Centang data penempatan kelas yang ingin dihapus sekaligus.</div>
        </div>
        <div class="d-flex gap-2">
            <!-- Tombol Batal -->
            <button type="button" class="btn btn-light px-4" id="cancelBatch">Batal</button>
            <!-- Tombol Hapus -->
            <button type="button" class="btn btn-danger px-4 fw-medium" id="btnHapusBatch">
                <i class="bi bi-trash me-1"></i>Hapus Terpilih
            </button>
        </div>
    </div>
</div>

<!-- MODAL HAPUS BATCH RIWAYAT KELAS -->
<div class="modal fade" id="modalHapusBatch" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <!-- Ikon Peringatan -->
                <div class="mb-3">
                    <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                </div>
                <!-- Judul Modal -->
                <h5 class="fw-bold mb-2">Hapus <span id="batchDeleteCount">0</span> Data Terpilih?</h5>
                <!-- Pesan Konfirmasi -->
                <p class="text-muted small mb-4">
                    Data penempatan kelas yang dipilih akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
                </p>
                <!-- Form Hapus -->
                <form action="<?= e(url('riwayat-kelas/delete-batch')); ?>" method="POST" id="formHapusBatch">
                    <div class="d-grid gap-2">
                        <!-- Tombol Submit -->
                        <button type="submit" class="btn btn-danger py-2 fw-medium">Ya, Hapus Semua</button>
                        <!-- Tombol Batal -->
                        <button type="button" class="btn btn-light py-2 fw-medium" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
