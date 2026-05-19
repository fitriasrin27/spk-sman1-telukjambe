/**
 * ==========================================================================
 * Modul Perhitungan Eligible JS
 * ==========================================================================
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Logic Search Input Autocomplete Real-time (dengan delay 500ms)
    const searchInput = document.getElementById('searchQueryInput');
    if (searchInput) {
        const val = searchInput.value;
        searchInput.value = '';
        searchInput.value = val;
        searchInput.focus();

        let timeout = null;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    }

    // 2. Logic Batch Mode Hapus (Index Eligible)
    const btnModeHapus = document.getElementById('btnModeHapus');
    const batchToolbar = document.getElementById('batchToolbar');
    
    if (btnModeHapus && batchToolbar) {
        const colCheckboxes = document.querySelectorAll('.col-checkbox');
        const selectAll = document.getElementById('selectAll');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        const selectedCount = document.getElementById('selectedCount');
        const btnHapusBatch = document.getElementById('btnHapusBatch');
        const cancelBatch = document.getElementById('cancelBatch');

        function updateSelection() {
            const checked = document.querySelectorAll('.item-checkbox:checked');
            selectedCount.textContent = checked.length;
            
            btnHapusBatch.disabled = checked.length === 0;
            
            if (selectAll) {
                selectAll.checked = (checked.length === itemCheckboxes.length && itemCheckboxes.length > 0);
            }
        }

        function toggleModeHapus(active) {
            if (active) {
                btnModeHapus.classList.add('d-none');
                colCheckboxes.forEach(el => el.classList.remove('d-none'));
                batchToolbar.classList.remove('d-none');
                document.querySelectorAll('tbody tr .btn').forEach(btn => btn.classList.add('disabled', 'opacity-50'));
                updateSelection();
            } else {
                btnModeHapus.classList.remove('d-none');
                colCheckboxes.forEach(el => el.classList.add('d-none'));
                batchToolbar.classList.add('d-none');
                document.querySelectorAll('tbody tr .btn').forEach(btn => btn.classList.remove('disabled', 'opacity-50'));
                
                if (selectAll) selectAll.checked = false;
                itemCheckboxes.forEach(cb => cb.checked = false);
                updateSelection();
            }
        }

        btnModeHapus.addEventListener('click', () => toggleModeHapus(true));
        if (cancelBatch) cancelBatch.addEventListener('click', () => toggleModeHapus(false));

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                itemCheckboxes.forEach(cb => cb.checked = this.checked);
                updateSelection();
            });
        }

        itemCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateSelection);
        });

        if (btnHapusBatch) {
            btnHapusBatch.addEventListener('click', function() {
                const checked = document.querySelectorAll('.item-checkbox:checked');
                const ids = Array.from(checked).map(cb => cb.value);
                
                if (ids.length === 0) return;
                window.konfirmasiHapusBatchEligible(ids);
            });
        }
    }

    // 3. Logic Seleksi Peserta SNBP (eligible-seleksi.php)
    const cardSeleksi = document.getElementById('cardSeleksiPendaftar');
    if (cardSeleksi) {
        const infoTotalDipilih = document.getElementById('infoTotalDipilih');
        const infoTotalDipilih2 = document.getElementById('infoTotalDipilih2');
        const infoTampil = document.getElementById('infoTampil');
        const infoDipilihKelas = document.getElementById('infoDipilihKelas');
        const badgeDipilih = document.getElementById('badgeDipilih');
        const btnProses = document.getElementById('btnProsesHitung');
        const selectAllSiswa = document.getElementById('selectAllSiswa');
        const searchSiswa = document.getElementById('searchSiswaEligible');
        const formHitung = document.getElementById('formHitungEligible');
        const tabKelasButtons = document.querySelectorAll('.btn-kelas');

        let allSiswa = [];
        let selectedIds = new Set();
        let filterKelas = '';

        const pageDataEl = document.getElementById('eligiblePageDataInfo');
        const tahunAjaran = pageDataEl ? pageDataEl.dataset.tahunAjaran : '';
        const jurusan = pageDataEl ? pageDataEl.dataset.jurusan : '';
        const ajaxUrl = pageDataEl ? pageDataEl.dataset.ajaxUrl : '';
        const kuota = pageDataEl ? parseInt(pageDataEl.dataset.kuota) : 0;

        function loadSiswa(kelas) {
            const tbody = document.getElementById('tbodySiswaEligible');
            if (tbody) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">
                    <div class="spinner-border spinner-border-sm text-primary me-2"></div>Memuat...</td></tr>`;
            }

            const params = new URLSearchParams({ url: 'perhitungan/eligible-get-siswa', tahun_ajaran: tahunAjaran, jurusan, kelas });
            fetch('?' + params)
                .then(r => r.json())
                .then(data => {
                    allSiswa = data;
                    renderTabel(data);
                    updateCounters();
                })
                .catch(() => {
                    if (tbody) {
                        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">Gagal memuat data.</td></tr>`;
                    }
                });
        }

        function renderTabel(siswa) {
            const tbody = document.getElementById('tbodySiswaEligible');
            if (!tbody) return;

            const search = searchSiswa ? searchSiswa.value.toLowerCase() : '';
            let html = '';
            let no = 0;

            siswa.forEach(s => {
                const match = !search ||
                    s.nama.toLowerCase().includes(search) ||
                    (s.nisn || '').toLowerCase().includes(search);
                const hidden = match ? '' : ' row-hidden';
                const rid = String(s.id_riwayat);

                if (s.terdaftar) {
                    selectedIds.add(rid);
                }

                const checked = selectedIds.has(rid) ? 'checked' : '';
                const prevBadge = s.terdaftar ? ' <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.6rem;">Terdaftar</span>' : '';
                
                const jmlN = parseInt(s.jumlah_nilai || 0);
                const jmlA = parseInt(s.jumlah_absensi || 0);
                let badgeData = '';
                
                if (jmlN >= 65 && jmlA >= 5) {
                    badgeData = '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Lengkap</span>';
                } else {
                    let info = [];
                    if (jmlN < 65) info.push(`Nilai: ${jmlN}/65`);
                    if (jmlA < 5)  info.push(`Absen: ${jmlA}/5`);
                    
                    badgeData = `<span class="badge bg-warning text-dark" title="${info.join(', ')}">
                        <i class="bi bi-exclamation-triangle me-1"></i>${jmlN < 65 ? 'Nilai' : 'Absen'}?
                    </span>`;
                    
                    if (jmlN === 0 && jmlA === 0) {
                        badgeData = '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Kosong</span>';
                    }
                }

                no++;
                html += `<tr class="siswa-row${hidden}" data-id="${rid}" data-nama="${s.nama.toLowerCase()}" data-nisn="${(s.nisn||'').toLowerCase()}" data-kelas="${s.kelas||''}">
                    <td class="text-center text-muted">${no}</td>
                    <td class="text-center">
                        <input class="form-check-input chk-siswa" type="checkbox" value="${rid}" ${checked}>
                    </td>
                    <td>${escHtml(s.nama)}${prevBadge}</td>
                    <td class="text-center text-dark">${escHtml(s.nisn||'—')}</td>
                    <td class="text-center">${escHtml(s.kelas||'—')}</td>
                    <td class="text-center">${badgeData}</td>
                </tr>`;
            });

            tbody.innerHTML = html || `<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada siswa ditemukan.</td></tr>`;

            document.querySelectorAll('.chk-siswa').forEach(chk => {
                chk.addEventListener('change', function() {
                    const id = String(this.value);
                    if (this.checked) selectedIds.add(id);
                    else selectedIds.delete(id);
                    updateCounters();
                });
            });

            updateCounters();
        }

        function updateCounters() {
            const totalDipilih = selectedIds.size;
            const visibleRows = document.querySelectorAll('.siswa-row:not(.row-hidden)');
            const dipilihKelas = [...visibleRows].filter(r => selectedIds.has(r.dataset.id)).length;

            if (infoTotalDipilih) infoTotalDipilih.textContent = totalDipilih + ' Siswa';
            if (infoTotalDipilih2) infoTotalDipilih2.textContent = totalDipilih;
            if (infoTampil) infoTampil.textContent = visibleRows.length;
            if (infoDipilihKelas) infoDipilihKelas.textContent = dipilihKelas;
            if (badgeDipilih) badgeDipilih.textContent = totalDipilih;

            if (btnProses) btnProses.disabled = totalDipilih === 0;

            const container = document.getElementById('containerCheckboxSiswa');
            if (container) {
                container.innerHTML = '';
                selectedIds.forEach(rid => {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = 'id_riwayat[]';
                    inp.value = rid;
                    container.appendChild(inp);
                });
            }

            if (selectAllSiswa) {
                const allVisible = document.querySelectorAll('.siswa-row:not(.row-hidden) .chk-siswa');
                const allChecked = allVisible.length > 0 && [...allVisible].every(c => c.checked);
                selectAllSiswa.checked = allChecked;
                selectAllSiswa.indeterminate = !allChecked && dipilihKelas > 0;
            }
        }

        function escHtml(str) {
            const d = document.createElement('div');
            d.appendChild(document.createTextNode(str));
            return d.innerHTML;
        }

        tabKelasButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                tabKelasButtons.forEach(b => {
                    b.classList.remove('active', 'btn-primary');
                    b.classList.add('btn-outline-primary');
                });
                this.classList.add('active', 'btn-primary');
                this.classList.remove('btn-outline-primary');
                filterKelas = this.dataset.kelas;
                loadSiswa(filterKelas);
            });
        });

        if (searchSiswa) {
            searchSiswa.addEventListener('input', function() {
                document.querySelectorAll('.siswa-row').forEach(r => {
                    const q = this.value.toLowerCase();
                    const match = !q || r.dataset.nama.includes(q) || r.dataset.nisn.includes(q);
                    r.classList.toggle('row-hidden', !match);
                });
                updateCounters();
            });
        }

        if (selectAllSiswa) {
            selectAllSiswa.addEventListener('change', function() {
                const visibleChk = document.querySelectorAll('.siswa-row:not(.row-hidden) .chk-siswa');
                visibleChk.forEach(chk => {
                    chk.checked = this.checked;
                    const id = String(chk.value);
                    if (this.checked) selectedIds.add(id);
                    else selectedIds.delete(id);
                });
                updateCounters();
            });
        }

        const btnBukaSeleksi = document.getElementById('btnHitungPeringkat');
        if (btnBukaSeleksi) {
            btnBukaSeleksi.addEventListener('click', function() {
                const riwayatCard = document.getElementById('cardRiwayatEligible');
                selectedIds.clear();
                
                cardSeleksi.style.display = 'block';
                if (riwayatCard) riwayatCard.style.display = 'none';
                loadSiswa('');
            });
        }

        const btnTutupSeleksi = document.getElementById('btnTutupSeleksi');
        if (btnTutupSeleksi) {
            btnTutupSeleksi.addEventListener('click', function() {
                const riwayatCard = document.getElementById('cardRiwayatEligible');
                cardSeleksi.style.display = 'none';
                if (riwayatCard) riwayatCard.style.display = 'block';
            });
        }

        if (formHitung) {
            formHitung.addEventListener('submit', function() {
                if (btnProses) {
                    btnProses.disabled = true;
                    btnProses.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menghitung...';
                }
            });
        }
    }

    // 4. Logic Modal PDSS (eligible-modal-pdss.php)
    const modalPDSS = document.getElementById('modalExportPDSS');
    if (modalPDSS) {
        const container = document.getElementById('pdssMapelContainer');
        const loading = document.getElementById('pdssMapelLoading');
        const inputCodes = document.getElementById('mapelCodesInput');
        const btnDownload = document.getElementById('btnDownloadNilai');
        const previewHeaders = document.getElementById('pdssPreviewHeaders');
        const previewTitle = document.getElementById('pdssPreviewTitle');
        const selectJurusan = document.getElementById('pdssJurusan');
        const selectTingkat = document.getElementById('pdssTingkat');
        const selectSemester = document.getElementById('pdssSemester');
        
        let isLoaded = false;
        let selectedOrder = [];

        function updatePreviewTitle() {
            if (!previewTitle) return;
            const jur = selectJurusan ? selectJurusan.value : '';
            const tgkt = selectTingkat ? selectTingkat.value : '';
            const smt = selectSemester ? selectSemester.value : '';
            previewTitle.textContent = `Data Nilai ${jur} - Kelas ${tgkt} - Semester ${smt}`;
        }

        if (selectJurusan) selectJurusan.addEventListener('change', updatePreviewTitle);
        if (selectTingkat) selectTingkat.addEventListener('change', updatePreviewTitle);
        if (selectSemester) selectSemester.addEventListener('change', updatePreviewTitle);
        updatePreviewTitle();

        modalPDSS.addEventListener('show.bs.modal', function () {
            if (!isLoaded) {
                const mapelUrl = modalPDSS.dataset.mapelUrl || 'pdss/get-mapel';
                const fetchUrl = mapelUrl.startsWith('?') ? mapelUrl : '?' + new URLSearchParams({ url: mapelUrl });

                fetch(fetchUrl)
                    .then(response => response.json())
                    .then(data => {
                        if (loading) loading.style.display = 'none';
                        if (data.error) {
                            if (container) container.innerHTML = `<div class="alert alert-danger small mb-0"><i class="bi bi-exclamation-triangle me-1"></i>${data.error}</div>`;
                            return;
                        }

                        const wrapper = document.createElement('div');
                        wrapper.className = 'd-flex flex-wrap gap-3 p-2';
                        
                        data.forEach(m => {
                            const badge = document.createElement('div');
                            badge.className = 'mapel-badge rounded py-1 px-3 bg-white text-dark small d-inline-flex align-items-center shadow-sm';
                            badge.dataset.code = m.kode_mapel;
                            badge.title = m.nama_mapel;
                            badge.innerHTML = `
                                <span>${m.kode_mapel}</span>
                                <span class="urutan"></span>
                            `;
                            
                            badge.addEventListener('click', function() {
                                const code = this.dataset.code;
                                const idx = selectedOrder.indexOf(code);
                                
                                if (idx > -1) {
                                    selectedOrder.splice(idx, 1);
                                    this.classList.remove('selected');
                                } else {
                                    selectedOrder.push(code);
                                    this.classList.add('selected');
                                }
                                
                                updateUI();
                            });
                            
                            wrapper.appendChild(badge);
                        });
                        
                        if (container) container.appendChild(wrapper);
                        isLoaded = true;
                    })
                    .catch(err => {
                        if (loading) {
                            loading.innerHTML = '<span class="text-danger"><i class="bi bi-wifi-off me-1"></i>Koneksi terputus. Gagal memuat data mapel.</span>';
                        }
                    });
            }
        });

        function updateUI() {
            if (inputCodes) inputCodes.value = selectedOrder.join(',');
            if (btnDownload) btnDownload.disabled = selectedOrder.length === 0;
            
            if (container) {
                const badges = container.querySelectorAll('.mapel-badge.selected');
                badges.forEach(b => {
                    const code = b.dataset.code;
                    const rank = selectedOrder.indexOf(code) + 1;
                    b.querySelector('.urutan').textContent = rank;
                });
            }
            
            if (previewHeaders) {
                let html = '<th class="bg-light">nisn</th>';
                if(selectedOrder.length > 0) {
                    selectedOrder.forEach(code => {
                        html += `<th>${code}</th>`;
                    });
                } else {
                    html += '<th class="text-muted fw-normal fst-italic">Pilih mapel di atas...</th>';
                }
                previewHeaders.innerHTML = html;
            }
        }
        
        const formPDSS = document.getElementById('formExportNilaiPDSS');
        if (formPDSS) {
            formPDSS.addEventListener('submit', function() {
                setTimeout(() => {
                    bootstrap.Modal.getInstance(modalPDSS).hide();
                }, 1000);
            });
        }
    }
});

// 5. Fungsi Global untuk Trigger Modul Hapus/Modal Konfirmasi
window.konfirmasiHapusEligible = function(id, info, waktu) {
    const hapusEligibleId = document.getElementById('hapusEligibleId');
    const hapusEligibleInfo = document.getElementById('hapusEligibleInfo');
    const hapusEligibleWaktu = document.getElementById('hapusEligibleWaktu');
    const modalEl = document.getElementById('modalHapusEligible');

    if (hapusEligibleId) hapusEligibleId.value = id;
    if (hapusEligibleInfo) hapusEligibleInfo.textContent = info;
    if (hapusEligibleWaktu) hapusEligibleWaktu.textContent = 'Dihitung pada: ' + waktu;
    if (modalEl) {
        new bootstrap.Modal(modalEl).show();
    }
};

window.konfirmasiHapusBatchEligible = function(ids) {
    const container = document.getElementById('containerIdsHapus');
    const txtJumlahHapusBatch = document.getElementById('txtJumlahHapusBatch');
    const modalEl = document.getElementById('modalHapusBatchEligible');

    if (container) {
        container.innerHTML = '';
        ids.forEach(id => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'ids[]';
            inp.value = id;
            container.appendChild(inp);
        });
    }
    if (txtJumlahHapusBatch) txtJumlahHapusBatch.textContent = ids.length;
    if (modalEl) {
        new bootstrap.Modal(modalEl).show();
    }
};
