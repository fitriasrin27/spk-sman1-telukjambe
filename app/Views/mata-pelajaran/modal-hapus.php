<!-- MODAL HAPUS MAPEL -->
<div class="modal fade" id="modalHapusMapel" tabindex="-1" aria-labelledby="modalHapusMapelLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Body -->
            <div class="modal-body text-center p-4">
                <!-- Ikon -->
                <div class="mb-3">
                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-bold mb-3" id="modalHapusMapelLabel">Hapus Mata Pelajaran?</h5>
                <!-- Konfirmasi -->
                <p class="text-muted mb-4">
                    Apakah Anda yakin ingin menghapus mapel <strong id="hapusNamaMapel" class="text-dark"></strong> (<span id="hapusKodeMapel"></span>)?<br>
                    <span class="small text-danger">Tindakan ini tidak dapat dibatalkan. Nilai siswa yang terkait dengan mapel ini juga akan terhapus.</span>
                </p>
                <!-- Tombol Aksi -->
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <a href="#" id="btnConfirmHapusMapel" class="btn btn-danger">
                        <i class="bi bi-trash3 me-1"></i>Ya, Hapus Data
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL HAPUS BATCH -->
<div class="modal fade" id="modalHapusBatch" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow">
            <!-- Modal Body -->
            <div class="modal-body text-center p-4">
                <!-- Ikon -->
                <div class="mb-3">
                    <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                </div>
                <!-- Teks Konfirmasi -->
                <h5 class="fw-bold mb-2">Hapus <span id="batchDeleteCount">0</span> Mapel Terpilih?</h5>
                <p class="text-muted small mb-4">
                    Mata pelajaran yang dipilih akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
                </p>
                <form action="<?= e(url('mata-pelajaran/delete-batch')); ?>" method="POST" id="formHapusBatch">
                    <!-- Tombol Aksi -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger py-2 fw-medium">Ya, Hapus Semua</button>
                        <button type="button" class="btn btn-light py-2 fw-medium" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

