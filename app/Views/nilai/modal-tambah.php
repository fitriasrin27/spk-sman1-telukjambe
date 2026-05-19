<div class="modal fade" id="modalTambahNilai" tabindex="-1" aria-labelledby="modalTambahNilaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form action="<?= e(url('nilai/store')); ?>" method="POST" class="modal-content" id="formTambahNilai">
            <!-- modal header -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahNilaiLabel"><i class="bi bi-journal-text me-2"></i>Tambah Data Nilai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <!-- modal body -->
            <div class="modal-body bg-light">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <!-- Cari Siswa -->
                        <div class="mb-3 position-relative">
                            <label class="form-label"><i class="bi bi-search me-2"></i> Cari Nama Siswa <span class="text-danger">*</span></label>
                            <input type="text" id="nilaiCariNama" class="form-control" placeholder="Ketik nama siswa..." autocomplete="off">
                            <div id="nilaiSuggestions" class="rk-suggestions d-none"></div>
                        </div>

                        <!-- Info Siswa & Riwayat Terpilih -->
                        <div id="nilaiInfoSiswa" class="row g-3" style="display: none;">
                            <input type="hidden" name="id_siswa" id="nilaiIdSiswa">
                            <div class="col-md-3">
                                <label class="form-label text-muted small fw-bold">NISN</label>
                                <input type="text" id="nilaiNisn" class="form-control bg-light" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted small fw-bold">NIS</label>
                                <input type="text" id="nilaiNis" class="form-control bg-light" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-muted small fw-bold">Jenis Kelamin</label>
                                <input type="text" id="nilaiGender" class="form-control bg-light" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold">Penempatan Kelas <span class="text-danger">*</span></label>
                                <select name="id_riwayat" id="nilaiPilihRiwayat" class="form-select border-primary" required disabled>
                                    <option value="" selected disabled>-- Tidak ada data --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Input Nilai & Absensi -->
                <div id="nilaiFormDetails" style="display: none;">
                    
                    <div class="row g-3 mb-3">
                        <!-- Daftar Mapel -->
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3"><i class="bi bi-journal-text me-2"></i>Nilai Mata Pelajaran</h6>
                                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-bordered table-hover align-middle mb-0">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th class="text-center" style="width: 50px;">No</th>
                                                    <th class="text-center">Mata Pelajaran</th>
                                                    <th class="text-center" style="width: 120px;">Nilai</th>
                                                </tr>
                                            </thead>
                                            <tbody id="nilaiMapelContainer">
                                                <!-- Baris dinamis JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar: Absensi, Ekskul, Prestasi -->
                        <div class="col-lg-6 d-flex flex-column gap-3">
                            <!-- Absensi -->
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3"><i class="bi bi-calendar-x me-2"></i>Ketidakhadiran</h6>
                                    <div class="row g-2">
                                        <div class="col-4">
                                            <label class="form-label small text-muted">Sakit</label>
                                            <input type="number" name="absen[sakit]" id="absenSakit" class="form-control text-center" min="0" placeholder="0">
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label small text-muted">Izin</label>
                                            <input type="number" name="absen[izin]" id="absenIzin" class="form-control text-center" min="0" placeholder="0">
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label small text-muted">Alpa</label>
                                            <input type="number" name="absen[alpa]" id="absenAlpa" class="form-control text-center" min="0" placeholder="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ekstrakurikuler -->
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold mb-0"><i class="bi bi-dribbble me-2"></i>Ekstrakurikuler</h6>
                                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" id="btnTambahEkskul" title="Tambah Baris">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                    <div id="ekskulContainer">
                                        <!-- Baris dinamis JS -->
                                    </div>
                                </div>
                            </div>

                            <!-- Prestasi -->
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold mb-0"><i class="bi bi-trophy me-2"></i>Prestasi</h6>
                                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" id="btnTambahPrestasi" title="Tambah Baris">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                    <div id="prestasiContainer">
                                        <!-- Baris dinamis JS -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" id="btnSimpanNilai" disabled>
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>
