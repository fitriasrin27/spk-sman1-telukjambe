<!-- MODAL HITUNG ELIGIBLE -->
<?php if ($tahunAjaran && $jurusan): ?>
<div class="modal fade" id="modalHitungEligible" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <!-- Form Hitung -->
        <form class="modal-content" method="POST" action="<?= e(url('perhitungan/eligible-hitung')) ?>">
            <input type="hidden" name="tahun_ajaran" value="<?= e($tahunAjaran) ?>">
            <input type="hidden" name="jurusan" value="<?= e($jurusan) ?>">
            
            <!-- Header Modal -->
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-calculator text-primary me-2"></i>Konfirmasi Hitung Eligible
                </h5>

                <!-- Tombol Close -->
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <!-- Body Modal -->
            <div class="modal-body">
                <!-- Info Batch -->
                <div class="p-3 bg-light rounded mb-3">
                    <div class="row text-center g-2">
                        <!-- Tahun Ajaran -->
                        <div class="col-6">
                            <div class="small text-muted">Tahun Ajaran (Angkatan)</div>
                            <div class="fw-semibold"><?= e($tahunAjaran) ?></div>
                        </div>
                        <!-- Jurusan -->
                        <div class="col-6">
                            <div class="small text-muted">Jurusan</div>
                            <div class="fw-semibold"><?= e($jurusan) ?></div>
                        </div>
                    </div>
                </div>
                <!-- Info -->
                <div class="alert alert-info small mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Sistem akan menarik data akumulasi dari <b>Semester 1 sampai 5</b> untuk seluruh siswa kelas XII sesuai filter di atas.
                    Proses ini menggunakan 13 mata pelajaran utama sesuai standar SNBP.
                </div>
            </div>
            
            <!-- Footer Modal -->
            <div class="modal-footer border-0">
                <!-- Tombol Batal -->
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <!-- Tombol Mulai Hitung -->
                <button type="submit" class="btn btn-primary px-4 fw-medium">
                    <i class="bi bi-play-circle me-1"></i>Mulai Hitung
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- MODAL HAPUS -->
<div class="modal fade" id="modalHapusEligible" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="GET" action="<?= e(url('')) ?>">
            <!-- Hidden Input -->
            <input type="hidden" name="url" value="perhitungan/eligible-delete">
            <input type="hidden" name="id" id="hapusEligibleId" value="">
            <!-- Header Modal -->
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger">
                    <i class="bi bi-trash me-2"></i>Hapus Riwayat Perhitungan
                </h5>
                <!-- Tombol Close -->
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body Modal -->
            <div class="modal-body">
                <!-- Info -->
                <p class="mb-1 text-center">Yakin ingin menghapus riwayat perhitungan ini?</p>
                <div class="my-3 text-center">
                    <div class="text-danger fw-bold fs-6" id="hapusEligibleInfo"></div>
                    <div class="text-muted small" id="hapusEligibleWaktu"></div>
                </div>
                <!-- Alert -->
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

<!-- MODAL HAPUS BATCH -->
<div class="modal fade" id="modalHapusBatchEligible" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <!-- Form -->
        <form class="modal-content border-0 shadow" method="POST" action="<?= e(url('perhitungan/eligible-delete-batch')) ?>">
            <!-- Hidden Input -->
            <div id="containerIdsHapus"></div>
            <!-- Header Modal -->
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger">
                    <i class="bi bi-trash3 me-2"></i>Hapus Riwayat Terpilih
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Body Modal -->
            <div class="modal-body">
                <!-- Info -->
                <p class="mb-2 text-center">Yakin ingin menghapus <strong id="txtJumlahHapusBatch">0</strong> riwayat perhitungan yang dipilih?</p>
                <!-- Alert -->
                <div class="alert alert-danger small mb-0">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Tindakan ini akan menghapus seluruh data hasil peringkat dan log peserta dari riwayat-riwayat tersebut. Data yang sudah dihapus <strong>tidak dapat dikembalikan</strong>.
                </div>
            </div>
            <!-- Footer Modal -->
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger px-4 fw-medium">
                    <i class="bi bi-trash me-1"></i>Hapus Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- TOOLBAR AKSI HAPUS BATCH -->
<div id="batchToolbar" class="bg-white border-top p-3 d-none shadow-lg" 
     style="position: sticky; bottom: 0; z-index: 1020; margin: 0 -1.25rem -1.25rem -1.25rem; border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem;">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <!-- Info -->
            <div class="fw-bold text-danger">
                <i class="bi bi-check2-all me-1"></i>
                <span id="selectedCount">0</span> Data Terpilih
            </div>
            <!-- Alert -->
            <div class="text-muted small d-none d-md-block">Centang riwayat perhitungan eligible yang ingin dihapus sekaligus.</div>
        </div>
        <!-- Tombol -->
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-light px-4" id="cancelBatch">Batal</button>
            <button type="button" class="btn btn-danger px-4 fw-medium" id="btnHapusBatch">
                <i class="bi bi-trash me-1"></i>Hapus Terpilih
            </button>
        </div>
    </div>
</div>
