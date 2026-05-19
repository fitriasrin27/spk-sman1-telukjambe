<!-- MODAL EXPORT PDSS -->
<div class="modal fade" id="modalExportPDSS" tabindex="-1" aria-labelledby="modalExportPDSSLabel" aria-hidden="true" data-mapel-url="pdss/get-mapel">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <!-- Judul Modal -->
                <h5 class="modal-title" id="modalExportPDSSLabel">
                    <i class="bi bi-box-arrow-down me-2"></i>Export Data untuk PDSS
                </h5>
                <!-- Tombol Tutup -->
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs mb-3" id="pdssTab" role="tablist">
                    <!-- Tab 1: File Siswa Eligible -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-medium text-success" id="eligible-tab" data-bs-toggle="tab" data-bs-target="#tab-eligible" type="button" role="tab" aria-selected="true">
                            <i class="bi bi-person-lines-fill me-1"></i> 1. File Siswa Eligible
                        </button>
                    </li>
                    <!-- Tab 2: File Nilai Mapel -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-medium text-success" id="nilai-tab" data-bs-toggle="tab" data-bs-target="#tab-nilai" type="button" role="tab" aria-selected="false">
                            <i class="bi bi-table me-1"></i> 2. File Nilai Mapel
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="pdssTabContent">
                    <!-- TAB 1: FILE SISWA ELIGIBLE -->
                    <div class="tab-pane fade show active" id="tab-eligible" role="tabpanel" tabindex="0">
                        <!-- Informasi File -->
                        <div class="alert alert-success bg-success-subtle border-success-subtle">
                            <h6 class="alert-heading"><i class="bi bi-info-circle me-1"></i>Informasi File</h6>
                            <p class="mb-0 small text-dark">Sistem otomatis mengambil daftar NISN siswa berstatus <strong>Lolos Eligible</strong> pada perhitungan ini.</p>
                        </div>
                        
                        <!-- Form Export -->
                        <form id="formExportEligiblePDSS" method="GET" action="<?= url('') ?>" target="_blank">
                            <input type="hidden" name="url" value="pdss/export-eligible">
                            <input type="hidden" name="id" value="<?= $riwayat['id_perhitungan'] ?? 0 ?>">
                            <!-- Opsi Format File -->
                            <div class="d-flex align-items-center justify-content-end gap-3 mt-4 pt-3 border-top">
                                <div class="d-flex align-items-center gap-2">
                                    <label class="fw-medium small mb-0">Format File:</label>
                                    <select name="format" class="form-select form-select-sm border-success-subtle shadow-sm" style="width:130px;">
                                        <option value="xlsx">.xlsx</option>
                                        <option value="csv">.csv</option>
                                    </select>
                                </div>
                                <!-- Tombol Download -->
                                <button type="submit" class="btn btn-success" onclick="setTimeout(() => bootstrap.Modal.getInstance(document.getElementById('modalExportPDSS')).hide(), 1000);">
                                    <i class="bi bi-download me-1"></i> Download
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 2: FILE NILAI MAPEL -->
                    <div class="tab-pane fade" id="tab-nilai" role="tabpanel" tabindex="0">
                        <!-- Form Export -->
                        <form id="formExportNilaiPDSS" method="POST" action="<?= url('pdss/export-nilai') ?>" target="_blank">
                            <input type="hidden" name="id_perhitungan" value="<?= $riwayat['id_perhitungan'] ?? 0 ?>">
                            <input type="hidden" name="mapel_codes" id="mapelCodesInput" value="">
                            <!-- Opsi -->
                            <div class="row mb-3">
                                <!-- Dropdown Jurusan -->
                                <div class="col-md-4">
                                    <label class="form-label fw-medium small">Jurusan</label>
                                    <select class="form-select border-success-subtle" name="jurusan" id="pdssJurusan">
                                        <option value="MIPA" <?= ($riwayat['jurusan'] ?? '') === 'MIPA' ? 'selected' : '' ?>>MIPA</option>
                                        <option value="IPS" <?= ($riwayat['jurusan'] ?? '') === 'IPS' ? 'selected' : '' ?>>IPS</option>
                                    </select>
                                </div>
                                <!-- Dropdown Tingkat -->
                                <div class="col-md-4">
                                    <label class="form-label fw-medium small">Tingkat</label>
                                    <select class="form-select border-success-subtle" name="tingkat" id="pdssTingkat">
                                        <option value="10">10</option>
                                        <option value="11">11</option>
                                        <option value="12">12</option>
                                    </select>
                                </div>
                                <!-- Dropdown Semester -->
                                <div class="col-md-4">
                                    <label class="form-label fw-medium small">Semester</label>
                                    <select class="form-select border-success-subtle" name="semester" id="pdssSemester">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                    </select>
                                </div>
                                <!-- Info -->
                                <div class="col-12 mt-2">
                                    <span class="text-muted small"><i class="bi bi-info-circle me-1"></i>Sesuaikan urutan kolom mata pelajaran di bawah dengan template web PDSS.</span>
                                </div>
                            </div>
                            
                            <!-- Dropdown Urutan Mata Pelajaran -->
                            <div class="mb-3">
                                <label class="form-label fw-medium small">Urutan Mata Pelajaran (Klik sesuai urutan di Template)</label>
                                <div class="border rounded p-3 bg-light" id="pdssMapelContainer" style="min-height: 100px;">
                                    <div class="text-center text-muted small py-3" id="pdssMapelLoading">
                                        <div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>Memuat daftar mapel...
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Preview Header -->
                            <div class="mb-4">
                                <label class="form-label fw-medium small">Preview Header</label>
                                <div class="table-responsive border rounded">
                                    <table class="table table-bordered table-sm mb-0 text-center small" style="white-space: nowrap;">
                                        <!-- Header Tabel -->
                                        <thead>
                                            <!-- Baris Judul -->
                                            <tr class="table-success">
                                                <th colspan="100%" class="text-start fw-normal" id="pdssPreviewTitle">Data Nilai <?= e($riwayat['jurusan'] ?? '') ?> - Kelas XII - Semester 1</th>
                                            </tr>
                                            <!-- Baris Header -->
                                            <tr id="pdssPreviewHeaders">
                                                <th class="bg-light">nisn</th>
                                                <th class="text-muted fw-normal fst-italic">Pilih mapel di atas...</th>
                                            </tr>
                                        </thead>
                                    </table>
                                    <!-- Info -->
                                    <small class="text-muted d-block mt-1">*Baris pertama akan otomatis diabaikan sistem pusat saat di-upload ke web PDSS.</small>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-end gap-3 mt-4 pt-3 border-top">
                                <!-- Format File -->
                                <div class="d-flex align-items-center gap-2">
                                    <label class="fw-medium small mb-0">Format File:</label>
                                    <select name="format" class="form-select form-select-sm border-success-subtle shadow-sm" style="width:130px;">
                                        <option value="xlsx">.xlsx</option>
                                        <option value="csv">.csv</option>
                                        <option value="pdf">.pdf</option>
                                    </select>
                                </div>
                                <!-- Tombol Download -->
                                <button type="submit" class="btn btn-success" id="btnDownloadNilai" disabled>
                                    <i class="bi bi-download me-1"></i> Download
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Style & Script dipindahkan ke perhitungan.css & perhitungan.js -->
