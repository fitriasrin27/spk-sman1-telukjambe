<!-- Modal Hapus Konversi -->
<div class="modal fade" id="modalHapusKonversi" tabindex="-1" aria-labelledby="modalHapusKonversiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
        <!-- Form Hapus -->
        <form class="modal-content" method="POST" action="<?= e(url('konversi/delete')) ?>" id="formHapusKonversi">
            <!-- Hidden ID -->
            <input type="hidden" name="id" id="hapusIdKonversi">
            <!-- Modal Header -->
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fs-5 fw-bold" id="modalHapusKonversiLabel">
                    <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body text-center pt-3 pb-4">
                <p class="mb-1">Apakah Anda yakin ingin menghapus data konversi:</p>
                <strong id="hapusNamaKonversi" class="d-block text-danger fs-5 mb-3"></strong>
                <p class="text-muted small mb-0">
                    <i class="bi bi-info-circle me-1"></i>Data yang dihapus tidak dapat dikembalikan.
                </p>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger px-4">
                    <i class="bi bi-trash3 me-1"></i>Hapus
                </button>
            </div>
        </form>
    </div>
</div>
