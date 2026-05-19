<!-- MODAL EDIT MAPEL -->
<div class="modal fade" id="modalEditMapel" tabindex="-1" aria-labelledby="modalEditMapelLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <!-- Form Mapel -->
        <form action="<?= e(url('mata-pelajaran/update')); ?>" method="POST" class="modal-content">
            <!-- Hidden ID -->
            <input type="hidden" name="id_mapel" id="editIdMapel">
            
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditMapelLabel">Edit Mata Pelajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body bg-light">
                <!-- Tingkat & Jurusan -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <!-- Tingkat -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="editTingkat" class="form-label text-muted small fw-bold">Tingkat <span class="text-danger">*</span></label>
                                <select class="form-select" id="editTingkat" name="tingkat" required>
                                    <option value="" selected disabled>-- Pilih --</option>
                                    <option value="X">X</option>
                                    <option value="XI">XI</option>
                                    <option value="XII">XII</option>
                                </select>
                            </div>
                            <!-- Jurusan -->
                            <div class="col-md-6">
                                <label for="editJurusan" class="form-label text-muted small fw-bold">Jurusan <span class="text-danger">*</span></label>
                                <select class="form-select" id="editJurusan" name="jurusan" required>
                                    <option value="" selected disabled>-- Pilih --</option>
                                    <option value="MIPA">MIPA</option>
                                    <option value="IPS">IPS</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kode & Nama Mapel -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <!-- Kode Mapel -->
                        <div class="mb-3">
                            <label for="editKodeMapel" class="form-label text-muted small fw-bold">Kode Mapel <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editKodeMapel" name="kode_mapel" required>
                        </div>
                        <!-- Nama Mapel -->
                        <div class="mb-0">
                            <label for="editNamaMapel" class="form-label text-muted small fw-bold">Nama Mapel <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editNamaMapel" name="nama_mapel" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
