<!-- MODAL CETAK LEGER -->
<div class="modal fade" id="modalCetakLeger" tabindex="-1" aria-labelledby="modalCetakLegerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content" target="_blank" method="GET" action="<?= e(url('')); ?>">
            <input type="hidden" name="url" value="nilai/cetak">
            <input type="hidden" name="tahun_ajaran" value="<?= e($tahunAjaran); ?>">
            <input type="hidden" name="kelas" value="<?= e($kelas); ?>">
            <input type="hidden" name="semester" value="<?= e($semester); ?>">
            <!-- header -->
            <div class="modal-header">
                <h5 class="modal-title fs-5 fw-bold" id="modalCetakLegerLabel">
                    <i class="bi bi-printer text-danger me-2"></i>Cetak Leger (PDF)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <!-- body -->
            <div class="modal-body p-4">
                <!-- Info Read-Only -->
                <div class="mb-4 p-3 bg-light rounded border border-secondary-subtle">
                    <h6 class="fw-bold mb-3 text-secondary">Data Laporan</h6>
                    <div class="row" style="font-size: 0.95rem;">
                        <!-- Tahun Ajaran -->
                        <div class="col-md-4 mb-2">
                            <span class="text-muted d-block small">Tahun Ajaran</span>
                            <span class="fw-medium"><?= e($tahunAjaran); ?></span>
                        </div>
                        <!-- Kelas -->
                        <div class="col-md-4 mb-2">
                            <span class="text-muted d-block small">Kelas</span>
                            <span class="fw-medium"><?= e($kelas); ?></span>
                        </div>
                        <!-- Semester -->
                        <div class="col-md-4 mb-2">
                            <span class="text-muted d-block small">Semester</span>
                            <span class="fw-medium"><?= $semester ? e(\App\Models\RiwayatKelas::labelSemester((int)$semester)) : '-'; ?></span>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Wali Kelas -->
                    <div class="col-12">
                        <h6 class="fw-bold mb-3 text-secondary border-bottom pb-2">Informasi Wali Kelas</h6>
                        <div class="row g-2">
                            <!-- Nama Wali Kelas -->
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Nama Wali Kelas</label>
                                <input type="text" class="form-control" name="wali_kelas" placeholder="Nama & Gelar">
                            </div>
                            <!-- Jenis Identitas -->
                            <div class="col-md-2">
                                <label class="form-label small text-muted">Jenis Identitas</label>
                                <select class="form-select identity-type-select" data-target="tipe_nip_wali">
                                    <option value="NIP.">NIP</option>
                                    <option value="NUPTK.">NUPTK</option>
                                    <option value="NRG.">NRG</option>
                                    <option value="NPK.">NPK</option>
                                    <option value="CUSTOM">Lainnya...</option>
                                </select>
                                <input type="hidden" name="tipe_nip_wali" value="NIP.">
                                <input type="text" class="form-control mt-1 d-none custom-identity-input" placeholder="Contoh: NIP.">
                            </div>
                            <!-- Nomor Identitas -->
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Nomor Identitas</label>
                                <input type="text" class="form-control" name="nip_wali" placeholder="Masukkan nomor...">
                            </div>
                        </div>
                    </div>

                    <!-- Kepala Sekolah -->
                    <div class="col-12">
                        <h6 class="fw-bold mb-3 text-secondary border-bottom pb-2">Informasi Kepala Sekolah</h6>
                        <div class="row g-2">
                            <!-- Nama Kepala Sekolah -->
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Nama Kepala Sekolah</label>
                                <input type="text" class="form-control" name="kepsek" placeholder="Nama & Gelar">
                            </div>
                            <!-- Jenis Identitas -->
                            <div class="col-md-2">
                                <label class="form-label small text-muted">Jenis Identitas</label>
                                <select class="form-select identity-type-select" data-target="tipe_nip_kepsek">
                                    <option value="NIP.">NIP</option>
                                    <option value="NUPTK.">NUPTK</option>
                                    <option value="NRG.">NRG</option>
                                    <option value="NPK.">NPK</option>
                                    <option value="CUSTOM">Lainnya...</option>
                                </select>
                                <input type="hidden" name="tipe_nip_kepsek" value="NIP.">
                                <input type="text" class="form-control mt-1 d-none custom-identity-input" placeholder="Contoh: NIDN.">
                            </div>
                            <!-- Nomor Identitas -->
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Nomor Identitas</label>
                                <input type="text" class="form-control" name="nip_kepsek" placeholder="Masukkan nomor...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- modal footer -->
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger fw-medium px-4" onclick="setTimeout(() => { bootstrap.Modal.getInstance(document.getElementById('modalCetakLeger')).hide(); }, 500);">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Generate PDF
                </button>
            </div>
        </form>
    </div>
</div>


<script src="<?= e(asset('assets/js/pages/nilai.js')); ?>"></script>

