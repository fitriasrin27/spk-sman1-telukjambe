<!-- MODAL HAPUS -->
<div class="modal fade" id="modalHapusNilai" tabindex="-1" aria-labelledby="modalHapusNilaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <!-- modal header -->
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalHapusNilaiLabel"><i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <!-- modal body -->
            <div class="modal-body text-center p-4">
                <i class="bi bi-trash3 text-danger mb-3 d-block" style="font-size: 3rem;"></i>
                <h5 class="mb-3">Yakin ingin menghapus data nilai ini?</h5>
                <p class="text-muted mb-0">Semua data nilai, absensi, ekskul, dan prestasi untuk siswa <strong id="hapusNilaiNama"></strong> akan dihapus permanen.</p>
            </div>
            <form id="formHapusNilai" method="POST" action="">
                <input type="hidden" name="id" id="hapusIdNilai" value="">
                <!-- modal footer -->
                <div class="modal-footer bg-light justify-content-center border-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4">Ya, Hapus Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL HAPUS BATCH -->
<div class="modal fade" id="modalHapusBatchNilai" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow">
            <!-- modal body -->
            <div class="modal-body text-center p-4">
                <!-- ikon -->
                <div class="mb-3">
                    <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                </div>
                <!-- jumlah data -->
                <h5 class="fw-bold mb-2">Hapus <span id="batchCountNilai">0</span> Data Nilai?</h5>
                <p class="text-muted small mb-4">
                    Tindakan ini akan menghapus seluruh data nilai (Akademik, Absensi, Ekskul, Prestasi) untuk siswa yang dipilih secara permanen.
                </p>
                <!-- form -->
                <form action="<?= e(url('nilai/delete-batch')) ?>" method="POST" id="formHapusBatchNilai">
                    <div id="batchIdsContainerNilai"></div>
                    <!-- button -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger fw-medium py-2">Ya, Hapus Semua</button>
                        <button type="button" class="btn btn-light py-2" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- TOOLBAR AKSI HAPUS BATCH -->
<div id="toolbarHapusNilai" class="bg-white border-top p-3 d-none shadow-lg" 
     style="position: sticky; bottom: 0; z-index: 1020; margin: 0 -1.25rem -1.25rem -1.25rem; border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem;">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <!-- jumlah terpilih -->
            <div class="fw-bold text-danger">
                <i class="bi bi-check2-all me-1"></i>
                <span id="txtJumlahTerpilihNilai">0</span> Data Terpilih
            </div>
            <!-- deskripsi -->
            <div class="text-muted small d-none d-md-block">Centang data nilai yang ingin dihapus sekaligus.</div>
        </div>
        <!-- button -->
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-light px-4" id="btnBatalHapusNilai">Batal</button>
            <button type="button" class="btn btn-danger px-4 fw-medium" id="btnKonfirmasiHapusBatchNilai" disabled>
                <i class="bi bi-trash me-1"></i>Hapus Terpilih
            </button>
        </div>
    </div>
</div>
