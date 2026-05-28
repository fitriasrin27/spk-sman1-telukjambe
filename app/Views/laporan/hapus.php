<?php if (!in_array(current_user()['role'], ['kepala_sekolah', 'wakasek'])): ?>

<!-- Toolbar Aksi Hapus (Floating/Fixed at bottom of card) -->
<div id="batchActionBarLaporan" class="bg-white border-top p-3 d-none shadow-lg" 
     style="position: sticky; bottom: 0; z-index: 1020; border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem;">
    <div class="d-flex align-items-center justify-content-between">
        <!-- Info Data Terpilih -->
        <div class="d-flex align-items-center gap-3">
            <div class="fw-bold text-danger">
                <i class="bi bi-check2-all me-1"></i>
                <span id="selectedCountLaporan">0</span> Data Terpilih
            </div>
            <div class="text-muted small d-none d-md-block">Centang laporan yang ingin dihapus sekaligus.</div>
        </div>
        <!-- Tombol Aksi -->
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-light px-4" id="btnCancelBatchLaporan">Batal</button>
            <button type="button" class="btn btn-danger px-4 fw-medium" id="btnConfirmBatchLaporan">
                <i class="bi bi-trash me-1"></i>Hapus Terpilih
            </button>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Laporan Satuan -->
<div class="modal fade" id="deleteLaporanModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <!-- Modal Body -->
            <div class="modal-body text-center py-4">
                <!-- Ikon -->
                <i class="bi bi-exclamation-circle text-danger mb-3 d-block" style="font-size: 3rem;"></i>
                <h5 class="fw-bold mb-1">Hapus Laporan?</h5>
                <p class="text-muted small mb-4">File PDF juga akan dihapus permanen dari server.</p>
                <form id="formHapusLaporanSatuan" method="POST" action="">
                    <input type="hidden" name="id" id="hapusIdLaporanSatuan" value="">
                    <!-- Tombol Aksi -->
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger px-4">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Batch -->
<div class="modal fade" id="batchDeleteLaporanModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <!-- Form Hapus Batch -->
            <form id="batchDeleteLaporanForm" action="<?= e(url('laporan/delete-batch')) ?>" method="POST">
                <!-- Modal Body -->
                <div class="modal-body text-center py-4">
                    <!-- Ikon -->
                    <i class="bi bi-exclamation-circle text-danger mb-3 d-block" style="font-size: 3rem;"></i>
                    <h5 class="fw-bold mb-1">Hapus Terpilih?</h5>
                    <p class="text-muted small mb-4">Laporan yang dipilih akan dihapus permanen.</p>
                    <!-- Tombol Aksi -->
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger px-4">Ya, Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php endif; ?>
