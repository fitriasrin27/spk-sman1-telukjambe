<!-- Modal Tambah Konversi -->
<div class="modal fade" id="modalTambahKonversi" tabindex="-1" aria-labelledby="modalTambahKonversiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <!-- Form Konversi -->
        <form class="modal-content" method="POST" action="<?= e(url('konversi/store')) ?>">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title fs-5 fw-bold" id="modalTambahKonversiLabel">
                    <i class="bi bi-plus-circle-fill text-primary me-2"></i>Tambah Konversi Nilai
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <!-- Kriteria -->
                <div class="mb-3">
                    <label class="form-label">Kriteria <span class="text-danger">*</span></label>
                    <select name="id_kriteria" class="form-select" required>
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
                        <input type="text" class="form-control" name="nilai_asli" placeholder="Contoh: SB" required>
                    </div>
                    <!-- Nilai Konversi -->
                    <div class="col-md-6">
                        <label class="form-label">Nilai Konversi (Skor) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control" name="nilai_konversi" placeholder="0.00" required>
                    </div>
                </div>
                <div class="alert alert-info mt-3 mb-0 py-2 border-0" style="background-color: #f0f9ff;">
                    <div class="d-flex gap-2">
                        <i class="bi bi-info-circle-fill text-info"></i>
                        <div class="small text-muted" style="line-height: 1.4;">
                            <b>Info:</b> Nilai asli adalah label/teks, sedangkan Nilai Konversi adalah skor angka untuk perhitungan SPK.
                        </div>
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
