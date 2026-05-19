<!-- MODAL CONFIG GENERATE LAPORAN KELAS -->
<div class="modal fade" id="modalGenerateLaporanKelas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-xl-custom-kelas">
        <div class="modal-content border-0 shadow modal-content-laporan-kelas">
            <!-- Header Modal -->
            <div class="modal-header bg-dark text-white py-2">
                <h6 class="modal-title fw-bold"><i class="bi bi-gear-wide-connected me-2"></i>Konfigurasi Laporan Kelas</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Body Modal -->
            <div class="modal-body p-0 modal-body-laporan-kelas">
                <div class="row g-0 flex-grow-1">
                    
                    <!-- SISI KIRI: CONFIGURATION FORM -->
                    <div class="col-lg-5 border-end bg-white p-4 config-scroll-kelas">
                        <form id="formConfigLaporanKelas">
                            <!-- Komponen Kolom Tabel -->
                            <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary"><i class="bi bi-list-check me-2"></i>Komponen Laporan</h6>
                            <div class="row g-3 mb-4">
                                <!-- Checkbox Wajib & Pilihan -->
                                <div class="col-6">
                                    <div class="form-check small mb-1">
                                        <input class="form-check-input check-komponen-kelas" type="checkbox" value="rank" id="compRankKelas" checked disabled>
                                        <label class="form-check-label" for="compRankKelas">Rank (Wajib)</label>
                                    </div>
                                    <div class="form-check small mb-1">
                                        <input class="form-check-input check-komponen-kelas" type="checkbox" value="nama" id="compNamaKelas" checked disabled>
                                        <label class="form-check-label" for="compNamaKelas">Nama (Wajib)</label>
                                    </div>
                                    <div class="form-check small mb-1">
                                        <input class="form-check-input check-komponen-kelas" type="checkbox" value="nisn" id="compNisnKelas" checked>
                                        <label class="form-check-label" for="compNisnKelas">NISN</label>
                                    </div>
                                    <div class="form-check small mb-1">
                                        <input class="form-check-input check-komponen-kelas" type="checkbox" value="nis" id="compNisKelas">
                                        <label class="form-check-label" for="compNisKelas">NIS</label>
                                    </div>
                                    <div class="form-check small mb-1">
                                        <input class="form-check-input check-komponen-kelas" type="checkbox" value="preferensi" id="compPrefKelas" checked>
                                        <label class="form-check-label" for="compPrefKelas">Preferensi (Vi)</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check small mb-1 text-warning fw-bold">
                                        <input class="form-check-input check-komponen-kelas" type="checkbox" value="c1c4" id="compRawKelas">
                                        <label class="form-check-label" for="compRawKelas">Kriteria (Raw)</label>
                                    </div>
                                    <div class="form-check small mb-1 text-success fw-bold">
                                        <input class="form-check-input check-komponen-kelas" type="checkbox" value="n1n4" id="compNormKelas">
                                        <label class="form-check-label" for="compNormKelas">Normalisasi</label>
                                    </div>
                                    <div class="form-check small mb-1">
                                        <input class="form-check-input check-komponen-kelas" type="checkbox" value="totalnilai" id="compTotalKelas" checked>
                                        <label class="form-check-label" for="compTotalKelas">Total Nilai</label>
                                    </div>
                                    <div class="form-check small mb-1">
                                        <input class="form-check-input check-komponen-kelas" type="checkbox" value="ratarata" id="compAvgKelas" checked>
                                        <label class="form-check-label" for="compAvgKelas">Rata-Rata Nilai</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Layout & Format -->
                            <div class="row g-4">
                                <!-- Kertas -->
                                <div class="col-6">
                                    <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary"><i class="bi bi-aspect-ratio me-2"></i>Kertas</h6>
                                    <label class="small text-muted mb-1">Orientasi</label>
                                    <select class="form-select form-select-sm" id="configOrientationKelas">
                                        <option value="portrait">Portrait</option>
                                        <option value="landscape">Landscape</option>
                                    </select>
                                    <div id="orientationAlertKelas" class="alert alert-warning py-1 px-2 mt-2 d-none" style="font-size: 10px;">
                                        Otomatis Landscape! (>7 kolom)
                                    </div>
                                </div>
                                <!-- Tanggal -->
                                <div class="col-6">
                                    <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary"><i class="bi bi-calendar-check me-2"></i>Tanggal Laporan</h6>
                                    <div class="d-flex gap-2 mb-2">
                                        <div class="form-check small">
                                            <input class="form-check-input input-ttd-kelas" type="radio" name="dateModeKelas" id="dateAutoKelas" value="auto" checked>
                                            <label class="form-check-label" for="dateAutoKelas">Otomatis</label>
                                        </div>
                                        <div class="form-check small">
                                            <input class="form-check-input input-ttd-kelas" type="radio" name="dateModeKelas" id="dateManualKelas" value="manual">
                                            <label class="form-check-label" for="dateManualKelas">Manual</label>
                                        </div>
                                    </div>
                                    <div id="manualDateContainerKelas" class="d-none">
                                        <input type="date" class="form-control form-control-sm input-ttd-kelas" id="configDateKelas" value="<?= date('Y-m-d') ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- TTD Penandatangan -->
                            <h6 class="fw-bold mt-4 mb-3 border-bottom pb-2 text-primary"><i class="bi bi-person-badge me-2"></i>Penandatangan</h6>
                            <div class="row g-3">
                                <!-- Wali Kelas -->
                                <div class="col-6">
                                    <label class="small text-muted mb-1">Wali Kelas</label>
                                    <input type="text" class="form-control form-control-sm mb-2 input-ttd-kelas" id="nameWalas" placeholder="Nama Wali Kelas" value="">
                                    <div class="row g-1">
                                        <div class="col-4">
                                            <select class="form-select form-select-sm identity-type-select input-ttd-kelas" id="typeWalas">
                                                <option value="NIP.">NIP</option>
                                                <option value="NUPTK.">NUPTK</option>
                                                <option value="CUSTOM">Lainnya</option>
                                            </select>
                                            <input type="text" class="form-control form-control-sm mt-1 d-none custom-identity-input input-ttd-kelas" id="customTypeWalas" placeholder="...">
                                        </div>
                                        <div class="col-8">
                                            <input type="text" class="form-control form-control-sm input-ttd-kelas" id="idWalas" placeholder="Nomor..." value="">
                                        </div>
                                    </div>
                                </div>
                                <!-- Kepala Sekolah -->
                                <div class="col-6">
                                    <label class="small text-muted mb-1">Kepala Sekolah</label>
                                    <input type="text" class="form-control form-control-sm mb-2 input-ttd-kelas" id="nameKepsekKelas" placeholder="Nama Kepala Sekolah" value="">
                                    <div class="row g-1">
                                        <div class="col-4">
                                            <select class="form-select form-select-sm identity-type-select input-ttd-kelas" id="typeKepsekKelas">
                                                <option value="NIP.">NIP</option>
                                                <option value="NUPTK.">NUPTK</option>
                                                <option value="CUSTOM">Lainnya</option>
                                            </select>
                                            <input type="text" class="form-control form-control-sm mt-1 d-none custom-identity-input input-ttd-kelas" id="customTypeKepsekKelas" placeholder="...">
                                        </div>
                                        <div class="col-8">
                                            <input type="text" class="form-control form-control-sm input-ttd-kelas" id="idKepsekKelas" placeholder="Nomor..." value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- SISI KANAN: LIVE PREVIEW -->
                    <div class="col-lg-7 p-0 bg-dark d-flex flex-column" style="position: relative;">
                        <!-- Preview Toolbar -->
                        <div class="p-2 bg-black text-white-50 small d-flex justify-content-between align-items-center">
                            <span id="previewFileNameKelas" class="text-white-50 fw-medium" style="font-size: 11px;"><i class="bi bi-file-earmark-pdf me-2"></i>Menyiapkan Laporan...</span>
                            <div class="d-flex align-items-center gap-2">
                                <div class="btn-group btn-group-sm bg-secondary rounded">
                                    <button class="btn btn-dark border-0 py-0" id="btnZoomOutKelas" style="width: 30px;"><i class="bi bi-dash"></i></button>
                                    <span class="px-2 text-white" id="zoomLevelKelas" style="min-width: 45px; text-align: center;">45%</span>
                                    <button class="btn btn-dark border-0 py-0" id="btnZoomInKelas" style="width: 30px;"><i class="bi bi-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        <!-- Preview Content -->
                        <div id="previewContainerKelas">
                            <div class="pdf-preview-box portrait" id="pdfPreviewKelas">
                                <!-- Kop Surat -->
                                <div class="preview-kop">
                                    <img src="<?= e(asset('assets/img/logo-pemprov-jabar.svg')) ?>" alt="Logo 1">
                                    <div class="preview-kop-text">
                                        <div style="font-size: 14px; font-weight: bold;">PEMERINTAH PROVINSI JAWA BARAT</div>
                                        <div style="font-size: 14px; font-weight: bold;">DINAS PENDIDIKAN</div>
                                        <div style="font-size: 20px; font-weight: bold; color: #000;">SMA NEGERI 1 TELUKJAMBE</div>
                                        <div style="font-size: 12px;">Jl. HS. Ronggowaluyo, Desa Sirnabaya, Kec. Telukjambe Timur, Kab. Karawang 41361</div>
                                        <div style="font-size: 12px;">Telp: (0267) 401778 | Website: www.sman1telukjambe.sch.id</div>
                                    </div>
                                    <img src="<?= e(asset('assets/img/logo-sman1-nobg.png')) ?>" alt="Logo 2">
                                </div>

                                <!-- Judul Laporan -->
                                <div class="preview-title" id="previewTitleTextKelas">Hasil Peringkat Kelas</div>

                                <!-- Info Utama -->
                                <div class="preview-info">
                                    <table style="width: 100%; border: none; font-size: 14px;">
                                        <tr>
                                            <td style="width: 120px; border:none; text-align: left;">Tahun Ajaran</td>
                                            <td style="width: 10px; border:none;">:</td>
                                            <td style="border:none; text-align: left;"><?= e($batch['tahun_ajaran']) ?></td>
                                            <td style="width: 120px; border:none; text-align: left; padding-left: 50px;">Semester</td>
                                            <td style="width: 10px; border:none;">:</td>
                                            <td style="border:none; text-align: left;"><?= $labelSemester[$batch['semester_target']] ?? '—' ?></td>
                                        </tr>
                                        <tr>
                                            <td style="border:none; text-align: left;">Kelas</td>
                                            <td style="border:none;">:</td>
                                            <td style="border:none; text-align: left;"><?= e($batch['kelas']) ?></td>
                                            <td style="border:none; text-align: left; padding-left: 50px;">Wali Kelas</td>
                                            <td style="border:none;">:</td>
                                            <td style="border:none; text-align: left;" id="previewWalasName">—</td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Tabel Preview -->
                                <table class="preview-table">
                                    <thead id="previewTableHeadKelas"></thead>
                                    <tbody id="previewTableBodyKelas"></tbody>
                                </table>

                                <!-- Footer TTD -->
                                <div class="preview-footer">
                                    <div class="footer-ttd">
                                        Mengetahui,<br>Kepala Sekolah
                                        <br><br><br><br>
                                        <strong id="previewKepsekNameKelas">( - )</strong><br>
                                        <span id="previewKepsekTypeKelas">NIP.</span> <span id="previewKepsekIdKelas">-</span>
                                    </div>
                                    <div class="footer-ttd">
                                        <span id="previewDateTextKelas">Karawang, <?= date('d') ?> <?= ($bulanId[(int)date('n')] ?? '') ?> <?= date('Y') ?></span><br>
                                        Wali Kelas
                                        <br><br><br><br>
                                        <strong id="previewWalasNameBottom">( - )</strong><br>
                                        <span id="previewWalasType">NIP.</span> <span id="previewWalasId">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer Modal -->
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger px-4 fw-bold shadow-sm" id="btnSubmitGenerateKelas">
                    <i class="bi bi-printer me-2"></i>Generate
                </button>
            </div>
        </div>
    </div>
</div>
