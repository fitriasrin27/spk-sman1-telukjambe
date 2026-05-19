<!-- MODAL TAMBAH MAPEL -->
<div class="modal fade" id="modalTambahMapel" tabindex="-1" aria-labelledby="modalTambahMapelLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <!-- Form Mapel -->
        <form action="<?= e(url('mata-pelajaran/store')); ?>" method="POST" class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahMapelLabel">Tambah Mata Pelajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body bg-light">
                <!-- Pilih Kelompok -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <!-- Tingkat & Jurusan -->
                        <h6 class="fw-bold mb-3"><i class="bi bi-tags me-2"></i>Tujuan Kelompok Kelas</h6>
                        <!-- Tingkat & Jurusan -->
                        <div class="row g-3">
                            <!-- Tingkat -->
                            <div class="col-md-6">
                                <label for="tingkatSelect" class="form-label text-muted small fw-bold">Tingkat <span class="text-danger">*</span></label>
                                <select class="form-select" id="tingkatSelect" name="tingkat" required>
                                    <option value="" selected disabled>-- Pilih Tingkat --</option>
                                    <option value="X">X</option>
                                    <option value="XI">XI</option>
                                    <option value="XII">XII</option>
                                </select>
                            </div>
                            <!-- Jurusan -->
                            <div class="col-md-6">
                                <label for="jurusanSelect" class="form-label text-muted small fw-bold">Jurusan <span class="text-danger">*</span></label>
                                <select class="form-select" id="jurusanSelect" name="jurusan" required>
                                    <option value="" selected disabled>-- Pilih Jurusan --</option>
                                    <option value="MIPA">MIPA</option>
                                    <option value="IPS">IPS</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Input Dinamis -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="fw-bold mb-0"><i class="bi bi-list-task me-2"></i>Daftar Mata Pelajaran</h6>
                        </div>
                        
                        <div id="dynamicMapelContainer" class="mb-3 pe-1" style="max-height: 250px; overflow-y: auto; overflow-x: hidden;">
                        <!-- Baris Pertama (Default) -->
                        <div class="row g-2 align-items-center mb-2 mapel-row">
                            <!-- Kode Mapel -->
                            <div class="col-4">
                                    <input type="text" name="mapel[0][kode]" class="form-control" placeholder="Kode Mapel (Misal: MTK)" required>
                                </div>
                                <div class="col-7">
                                    <input type="text" name="mapel[0][nama]" class="form-control" placeholder="Nama Mapel (Misal: Matematika)" required>
                                </div>
                                <div class="col-1 text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-baris" disabled>
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Tambah Baris -->
                        <div class="d-flex align-items-center gap-2 text-start">
                            <button type="button" class="btn btn-sm btn-outline-primary text-nowrap" id="btnTambahBarisMapel">
                                <i class="bi bi-plus-circle me-1"></i>Tambah Baris
                            </button>
                            <!-- Info Tooltip -->
                            <small class="text-muted" style="font-size: 0.75rem; line-height: 1.2;">
                                💡 <b>Shift+Enter</b>: Nambah baris &nbsp;&bull;&nbsp; <b>Shift+Backspace</b>: Hapus baris
                            </small>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>
