<!-- Modal Hapus Kriteria -->
<div class="modal fade" id="modalHapusKriteria" tabindex="-1" aria-labelledby="modalHapusKriteriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <!-- Form Hapus -->
        <form class="modal-content" method="GET" action="" id="formHapusKriteria">
            <!-- Hidden Fields -->
            <input type="hidden" name="url" value="kriteria/delete">
            <input type="hidden" name="id" id="hapusIdKriteria">
            <!-- Modal Header -->
            <div class="modal-header">
                <h2 class="modal-title fs-5 fw-bold" id="modalHapusLabel">
                    <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Konfirmasi Hapus
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body text-center pt-2 pb-4">
                <p class="mb-1">Apakah Anda yakin ingin menghapus kriteria:</p>
                <strong id="hapusNamaKriteria" class="d-block text-danger fs-5 mb-3"></strong>
                <p class="text-danger small mb-0"><i class="bi bi-info-circle me-1"></i>Data yang dihapus tidak dapat dikembalikan.</p>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger px-4">Hapus</button>
            </div>
        </form>
    </div>
</div>
