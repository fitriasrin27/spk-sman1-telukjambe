<!-- Modal Tambah Kriteria -->
<div class="modal fade" id="modalTambahKriteria" tabindex="-1" aria-labelledby="modalTambahKriteriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <!-- Form Kriteria -->
        <form class="modal-content" method="POST" action="<?= e(url('kriteria/store')) ?>">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title fs-5 fw-bold" id="modalTambahKriteriaLabel">
                    <i class="bi bi-star-fill text-warning me-2"></i>Tambah Kriteria & Bobot
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <!-- Fields Kriteria -->
                <div class="row g-3">
                    <!-- Kode -->
                    <div class="col-md-3">
                        <label class="form-label">Kode <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-light" name="kode_kriteria" value="<?= e($nextKode ?? '') ?>" readonly required>
                    </div>
                    <!-- Nama Kriteria -->
                    <div class="col-md-9">
                        <label class="form-label">Nama Kriteria <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_kriteria" placeholder="Contoh: Nilai Akademik" required>
                    </div>
                    <!-- Atribut -->
                    <div class="col-md-6">
                        <label class="form-label">Atribut <span class="text-danger">*</span></label>
                        <select name="atribut" class="form-select" required>
                            <option value="">-- Pilih Atribut --</option>
                            <option value="Benefit">Benefit</option>
                            <option value="Cost">Cost</option>
                        </select>
                    </div>
                    <!-- Bobot -->
                    <div class="col-md-6">
                        <label class="form-label">Bobot <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" max="<?= number_format($sisaBobot, 2, '.', '') ?>" class="form-control" name="bobot" placeholder="Maks: <?= number_format($sisaBobot, 2, '.', '') ?>" required>
                    </div>
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary fw-medium">
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>
