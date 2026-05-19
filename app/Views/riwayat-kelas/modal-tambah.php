<!-- MODAL TAMBAH PENEMPATAN -->
<div class="modal fade" id="modalTambahRiwayat" tabindex="-1" aria-labelledby="modalTambahRiwayatLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" method="POST" action="<?= e(url('riwayat-kelas/store')); ?>">
            <input type="hidden" name="id_siswa" id="rkIdSiswa">
            <!-- Header Modal -->
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="modalTambahRiwayatLabel">
                    <i class="bi bi-journal-plus me-2 text-primary"></i>Tambah Penempatan Kelas
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <!-- Cari Siswa by Nama -->
                <div class="mb-3 position-relative">
                    <label class="form-label"><i class="bi bi-search me-2"></i> Cari Nama Siswa <span class="text-danger">*</span></label>
                    <input type="text" id="rkCariNama" class="form-control" placeholder="Ketik nama siswa..." autocomplete="off">
                    <div id="rkSuggestions" class="rk-suggestions d-none"></div>
                </div>

                <!-- Info Siswa (readonly, auto-fill) -->
                <div class="row mb-3" id="rkInfoSiswa" style="display:none!important;">
                    <div class="col-md-4">
                        <label class="form-label">NISN</label>
                        <input type="text" id="rkNisn" class="form-control" readonly disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">NIS</label>
                        <input type="text" id="rkNis" class="form-control" readonly disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jenis Kelamin</label>
                        <input type="text" id="rkGender" class="form-control" readonly disabled>
                    </div>
                </div>

                <!-- Divider -->
                <div class="modal-section-divider mb-3" id="rkDivider" style="display:none!important;">
                    <span>Input Penempatan Siswa Terkini</span>
                </div>

                <!-- Tahun, Kelas, Semester -->
                <div class="row" id="rkFormPenempatan" style="display:none!important;">
                    <!-- Input Tahun Ajaran -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="tahun_ajaran" placeholder="Cth: 2024/2025">
                    </div>
                    <!-- Input Kelas -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Kelas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="kelas" placeholder="Cth: XII MIPA 1">
                    </div>
                    <!-- Input Semester -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Semester <span class="text-danger">*</span></label>
                        <select class="form-select" name="semester_jenis">
                            <option value="">Pilih Semester</option>
                            <option value="ganjil">Ganjil</option>
                            <option value="genap">Genap</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" id="rkBtnSimpan" disabled>
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>
