<!-- MODAL EDIT RIWAYAT KELAS -->
<div class="modal fade" id="modalEditRiwayat" tabindex="-1" aria-labelledby="modalEditRiwayatLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content" method="POST" action="<?= e(url('riwayat-kelas/update')); ?>">
            <!-- Hidden ID -->
            <input type="hidden" name="id_riwayat" id="editRkId">
            <!-- Header Modal -->
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="modalEditRiwayatLabel">
                    <i class="bi bi-pencil-square me-2" style="color:#f59e0b;"></i>Edit Penempatan Kelas
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <!-- Body Modal -->
            <div class="modal-body">
                <!-- Info Siswa (readonly) -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Siswa</label>
                    <input type="text" id="editRkNama" class="form-control" readonly disabled>
                </div>
                <!-- Info NISN, NIS, Jenis Kelamin (readonly) -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">NISN</label>
                        <input type="text" id="editRkNisn" class="form-control" readonly disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">NIS</label>
                        <input type="text" id="editRkNis" class="form-control" readonly disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jenis Kelamin</label>
                        <input type="text" id="editRkGender" class="form-control" readonly disabled>
                    </div>
                </div>

                <!-- Divider -->
                <div class="modal-section-divider mb-3">
                    <span>Ubah Data Penempatan</span>
                </div>

                <!-- Form Penempatan -->
                <div class="row">
                    <!-- Input Tahun Ajaran -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="tahun_ajaran" id="editRkTahun" placeholder="Cth: 2024/2025" required>
                    </div>
                    <!-- Input Kelas -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Kelas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="kelas" id="editRkKelas" placeholder="Cth: XII MIPA 1" required>
                    </div>
                    <!-- Input Semester -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Semester <span class="text-danger">*</span></label>
                        <select class="form-select" name="semester_jenis" id="editRkSemJenis" required>
                            <option value="">Pilih Semester</option>
                            <option value="ganjil">Ganjil</option>
                            <option value="genap">Genap</option>
                        </select>
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
