<!-- Modal Edit Konversi -->
<div class="modal fade" id="modalEditKonversi" tabindex="-1" aria-labelledby="modalEditKonversiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <!-- Form Konversi -->
        <form class="modal-content" method="POST" action="<?= e(url('konversi/update')) ?>">
            <!-- Hidden ID -->
            <input type="hidden" name="id_konversi" id="editIdKonversi">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title fs-5 fw-bold" id="modalEditKonversiLabel">
                    <i class="bi bi-pencil-square text-warning me-2"></i>Edit Konversi Nilai
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <!-- Kriteria -->
                <div class="mb-3">
                    <label class="form-label">Kriteria <span class="text-danger">*</span></label>
                    <select name="id_kriteria" id="editKriteria" class="form-select" required>
                        <option value="">-- Pilih Kriteria --</option>
                        <?php foreach ($allKriteria as $kr): ?>
                            <option value="<?= e($kr['id_kriteria']) ?>">
                                [<?= e($kr['kode_kriteria']) ?>] <?= e($kr['nama_kriteria']) ?> (<?= ucfirst(e($kr['atribut'])) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-3">
                    <!-- Nilai Asli -->
                    <div class="col-md-6">
                        <label class="form-label">Nilai Asli (Label) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nilai_asli" id="editNilaiAsli" placeholder="Contoh: SB" required>
                    </div>
                    <!-- Nilai Konversi -->
                    <div class="col-md-6">
                        <label class="form-label">Nilai Konversi (Skor) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control" name="nilai_konversi" id="editNilaiKonversi" placeholder="Contoh: 4.00" required>
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
