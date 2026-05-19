<?php
    // Modal generate laporan eligible
    $bulanId = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $currentKuota = 0;
    if (isset($batch['kuota_eligible'])) {
        $currentKuota = (int)$batch['kuota_eligible'];
    } elseif (isset($hasil) && is_array($hasil)) {
        $currentKuota = count(array_filter($hasil, function($h) { 
            return ($h['status_eligible'] ?? '') === 'ya'; 
        }));
    }
?>

<!-- MODAL CONFIG GENERATE LAPORAN ELIGIBLE -->
<div class="modal fade" id="modalGenerateLaporanEligible" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-xl-custom">
        <div class="modal-content border-0 shadow modal-content-laporan">
            <!-- Header Modal -->
            <div class="modal-header bg-dark text-white py-2">
                <h6 class="modal-title fw-bold"><i class="bi bi-gear-wide-connected me-2"></i>Konfigurasi Laporan Eligible</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Body Modal -->
            <div class="modal-body p-0 modal-body-laporan">
                <div class="row g-0 flex-grow-1">
                    
                    <!-- SISI KIRI: CONFIGURATION FORM -->
                    <div class="col-lg-5 border-end bg-white p-4 config-scroll-eligible">
                        <form id="formConfigLaporanEligible">
                            <!-- Komponen Kolom Tabel -->
                            <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary"><i class="bi bi-list-check me-2"></i>Komponen Laporan</h6>
                            <div class="row g-3 mb-4">
                                <!-- Checkbox Wajib & Pilihan -->
                                <div class="col-6">
                                    <div class="form-check small mb-1">
                                        <input class="form-check-input check-komponen-eligible" type="checkbox" value="rank" id="compRankEligible" checked disabled>
                                        <label class="form-check-label" for="compRankEligible">Rank (Wajib)</label>
                                    </div>
                                    <div class="form-check small mb-1">
                                        <input class="form-check-input check-komponen-eligible" type="checkbox" value="nama" id="compNamaEligible" checked disabled>
                                        <label class="form-check-label" for="compNamaEligible">Nama (Wajib)</label>
                                    </div>
                                    <div class="form-check small mb-1">
                                        <input class="form-check-input check-komponen-eligible" type="checkbox" value="nisn" id="compNisnEligible" checked>
                                        <label class="form-check-label" for="compNisnEligible">NISN</label>
                                    </div>
                                    <div class="form-check small mb-1">
                                        <input class="form-check-input check-komponen-eligible" type="checkbox" value="nis" id="compNisEligible">
                                        <label class="form-check-label" for="compNisEligible">NIS</label>
                                    </div>
                                    <div class="form-check small mb-1">
                                        <input class="form-check-input check-komponen-eligible" type="checkbox" value="preferensi" id="compPrefEligible" checked>
                                        <label class="form-check-label" for="compPrefEligible">Preferensi (Vi)</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check small mb-1 text-warning fw-bold">
                                        <input class="form-check-input check-komponen-eligible" type="checkbox" value="c1c4" id="compRawEligible">
                                        <label class="form-check-label" for="compRawEligible">Kriteria (Raw)</label>
                                    </div>
                                    <div class="form-check small mb-1 text-success fw-bold">
                                        <input class="form-check-input check-komponen-eligible" type="checkbox" value="n1n4" id="compNormEligible">
                                        <label class="form-check-label" for="compNormEligible">Normalisasi</label>
                                    </div>
                                    <div class="form-check small mb-1">
                                        <input class="form-check-input check-komponen-eligible" type="checkbox" value="totalnilai" id="compTotalEligible" checked>
                                        <label class="form-check-label" for="compTotalEligible">Total Nilai</label>
                                    </div>
                                    <div class="form-check small mb-1">
                                        <input class="form-check-input check-komponen-eligible" type="checkbox" value="ratarata" id="compAvgEligible" checked>
                                        <label class="form-check-label" for="compAvgEligible">Rata-Rata Nilai</label>
                                    </div>
                                    <div class="form-check small mb-1">
                                        <input class="form-check-input check-komponen-eligible" type="checkbox" value="status" id="compStatusEligible" checked>
                                        <label class="form-check-label" for="compStatusEligible">Pembatas Kuota</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Layout & Format -->
                            <div class="row g-4">
                                <!-- Kertas -->
                                <div class="col-6">
                                    <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary"><i class="bi bi-aspect-ratio me-2"></i>Kertas</h6>
                                    <label class="small text-muted mb-1">Orientasi</label>
                                    <select class="form-select form-select-sm" id="configOrientationEligible">
                                        <option value="portrait">Portrait</option>
                                        <option value="landscape">Landscape</option>
                                    </select>
                                    <div id="orientationAlertEligible" class="alert alert-warning py-1 px-2 mt-2 d-none" style="font-size: 10px;">
                                        Otomatis Landscape! (>7 kolom)
                                    </div>
                                </div>
                                <!-- Tanggal -->
                                <div class="col-6">
                                    <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary"><i class="bi bi-calendar-check me-2"></i>Tanggal Laporan</h6>
                                    <div class="d-flex gap-2 mb-2">
                                        <div class="form-check small">
                                            <input class="form-check-input input-ttd-eligible" type="radio" name="dateModeEligible" id="dateAutoEligible" value="auto" checked>
                                            <label class="form-check-label" for="dateAutoEligible">Otomatis</label>
                                        </div>
                                        <div class="form-check small">
                                            <input class="form-check-input input-ttd-eligible" type="radio" name="dateModeEligible" id="dateManualEligible" value="manual">
                                            <label class="form-check-label" for="dateManualEligible">Manual</label>
                                        </div>
                                    </div>
                                    <div id="manualDateContainerEligible" class="d-none">
                                        <input type="date" class="form-control form-control-sm input-ttd-eligible" id="configDateEligible" value="<?= date('Y-m-d') ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- TTD Penandatangan -->
                            <h6 class="fw-bold mt-4 mb-3 border-bottom pb-2 text-primary"><i class="bi bi-person-badge me-2"></i>Penandatangan</h6>
                            <div class="row g-3">
                                <!-- Guru BK -->
                                <div class="col-6">
                                    <label class="small text-muted mb-1">Guru Bimbingan Konselling</label>
                                    <input type="text" class="form-control form-control-sm mb-2 input-ttd-eligible" id="nameBK" placeholder="Nama Guru BK" value="">
                                    <div class="row g-1">
                                        <div class="col-4">
                                            <select class="form-select form-select-sm identity-type-select input-ttd-eligible" id="typeBK">
                                                <option value="NIP.">NIP</option>
                                                <option value="NUPTK.">NUPTK</option>
                                                <option value="CUSTOM">Lainnya</option>
                                            </select>
                                            <input type="text" class="form-control form-control-sm mt-1 d-none custom-identity-input input-ttd-eligible" id="customTypeBK" placeholder="...">
                                        </div>
                                        <div class="col-8">
                                            <input type="text" class="form-control form-control-sm input-ttd-eligible" id="idBK" placeholder="Nomor..." value="">
                                        </div>
                                    </div>
                                </div>
                                <!-- Kepala Sekolah -->
                                <div class="col-6">
                                    <label class="small text-muted mb-1">Kepala Sekolah</label>
                                    <input type="text" class="form-control form-control-sm mb-2 input-ttd-eligible" id="nameKepsekEligible" placeholder="Nama Kepala Sekolah" value="">
                                    <div class="row g-1">
                                        <div class="col-4">
                                            <select class="form-select form-select-sm identity-type-select input-ttd-eligible" id="typeKepsekEligible">
                                                <option value="NIP.">NIP</option>
                                                <option value="NUPTK.">NUPTK</option>
                                                <option value="CUSTOM">Lainnya</option>
                                            </select>
                                            <input type="text" class="form-control form-control-sm mt-1 d-none custom-identity-input input-ttd-eligible" id="customTypeKepsekEligible" placeholder="...">
                                        </div>
                                        <div class="col-8">
                                            <input type="text" class="form-control form-control-sm input-ttd-eligible" id="idKepsekEligible" placeholder="Nomor..." value="">
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
                            <span id="previewFileNameEligible" class="text-white-50 fw-medium" style="font-size: 11px;"><i class="bi bi-file-earmark-pdf me-2"></i>Menyiapkan Laporan...</span>
                            <div class="d-flex align-items-center gap-2">
                                <div class="btn-group btn-group-sm bg-secondary rounded">
                                    <button class="btn btn-dark border-0 py-0" id="btnZoomOutEligible" style="width: 30px;"><i class="bi bi-dash"></i></button>
                                    <span class="px-2 text-white" id="zoomLevelEligible" style="min-width: 45px; text-align: center;">45%</span>
                                    <button class="btn btn-dark border-0 py-0" id="btnZoomInEligible" style="width: 30px;"><i class="bi bi-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        <!-- Preview Content -->
                        <div id="previewContainerEligible">
                            <div class="pdf-preview-box portrait" id="pdfPreviewEligible">
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
                                <div class="preview-title" id="previewTitleTextEligible">Hasil Seleksi Siswa Eligible SNBP</div>

                                <!-- Info Utama -->
                                <div class="preview-info">
                                    <table style="width: 100%; border: none; font-size: 14px;">
                                        <tr>
                                            <td style="width: 120px; border:none; text-align: left;">Tahun Ajaran</td>
                                            <td style="width: 10px; border:none;">:</td>
                                            <td style="border:none; text-align: left;"><?= e($batch['tahun_ajaran']) ?></td>
                                            <td style="width: 120px; border:none; text-align: left; padding-left: 50px;">Jurusan</td>
                                            <td style="width: 10px; border:none;">:</td>
                                            <td style="border:none; text-align: left;">Kelas XII <?= e($batch['jurusan']) ?></td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Tabel Preview -->
                                <table class="preview-table">
                                    <thead id="previewTableHeadEligible"></thead>
                                    <tbody id="previewTableBodyEligible"></tbody>
                                </table>

                                <!-- Footer TTD -->
                                <div class="preview-footer">
                                    <div class="footer-ttd">
                                        Mengetahui,<br>Kepala Sekolah
                                        <br><br><br><br>
                                        <strong id="previewKepsekNameEligible">( - )</strong><br>
                                        <span id="previewKepsekTypeEligible">NIP.</span> <span id="previewKepsekIdEligible">-</span>
                                    </div>
                                    <div class="footer-ttd">
                                        <span id="previewDateTextEligible">Karawang, <?= date('d') ?> <?= ($bulanId[(int)date('n')] ?? '') ?> <?= date('Y') ?></span><br>
                                        Guru Bimbingan Konselling
                                        <br><br><br><br>
                                        <strong id="previewBKNameBottom">( - )</strong><br>
                                        <span id="previewBKType">NIP.</span> <span id="previewBKId">-</span>
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
                <button type="button" class="btn btn-danger px-4 fw-bold shadow-sm" id="btnSubmitGenerateEligible">
                    <i class="bi bi-printer me-2"></i>Generate
                </button>
            </div>
        </div>
    </div>
</div>
