<!-- MODAL HAPUS -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 360px;">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h2 class="modal-title fs-5 fw-bold" id="modalHapusLabel">
                    <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Hapus Data
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body text-center pt-2">
                <p class="mb-1">Yakin ingin menghapus data siswa:</p>
                <strong id="hapusNama" class="d-block text-danger" style="font-size: 1rem;"></strong>
                <span id="hapusNisn" class="d-block text-muted" style="font-size: 0.8rem;">NISN: —</span>
            </div>
            <form id="formHapusSiswa" method="POST" action="">
                <input type="hidden" name="id" id="hapusInputId" value="">
                <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="bi bi-trash3 me-1"></i>Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL HAPUS BATCH -->
<div class="modal fade" id="modalHapusBatchSiswa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <!-- Icon Peringatan -->
                <div class="mb-3">
                    <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                </div>
                <!-- Judul Modal -->
                <h5 class="fw-bold mb-2">Hapus <span id="batchCountSiswa">0</span> Data Terpilih?</h5>
                <!-- Deskripsi -->
                <p class="text-muted small mb-4">
                    Tindakan ini akan menghapus data siswa yang dipilih secara permanen. Data yang sudah dihapus tidak dapat dikembalikan.
                </p>
                <!-- Form Hapus -->
                <form action="<?= e(url('siswa/delete-batch')) ?>" method="POST" id="formHapusBatchSiswa">
                    <!-- Hidden IDs -->
                    <div id="batchIdsContainerSiswa"></div>
                    <!-- Tombol Aksi -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger fw-medium py-2">Ya, Hapus Semua</button>
                        <button type="button" class="btn btn-light py-2" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
