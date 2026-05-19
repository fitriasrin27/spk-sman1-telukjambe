/* ==========================================================================
   JS Khusus: Modul Hasil Peringkat Kelas
   Mencakup: autocomplete search dan konfigurasi modal preview laporan PDF
   ========================================================================== */

const monthNames = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

document.addEventListener('DOMContentLoaded', function () {
    // [1] Autocomplete search
    const searchInput = document.getElementById('searchQueryInput');
    if (searchInput) {
        const val = searchInput.value;
        searchInput.value = '';
        searchInput.value = val;
        searchInput.focus();

        let timeout = null;
        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    }

    // [2] Identity type selector
    document.querySelectorAll('.identity-type-select').forEach(sel => {
        sel.addEventListener('change', function () {
            const customInput = this.parentElement.querySelector('.custom-identity-input');
            if (customInput) {
                if (this.value === 'CUSTOM') {
                    customInput.classList.remove('d-none');
                } else {
                    customInput.classList.add('d-none');
                }
            }
        });
    });

    // [3] Configuration & Generate PDF: KELAS LAPORAN
    const modalEl = document.getElementById('modalGenerateLaporanKelas');
    const btnTrigger = document.getElementById('btnGenerateLaporan');
    
    if (!modalEl || !btnTrigger) return;

    const checkKomponen = modalEl.querySelectorAll('.check-komponen-kelas');
    const configOrientation = document.getElementById('configOrientationKelas');
    const inputTtd = modalEl.querySelectorAll('.input-ttd-kelas');
    const pdfPreview = document.getElementById('pdfPreviewKelas');
    const modal = new bootstrap.Modal(modalEl);
    let currentZoom = 45;

    btnTrigger.addEventListener('click', () => modal.show());

    const btnZoomIn = document.getElementById('btnZoomInKelas');
    const btnZoomOut = document.getElementById('btnZoomOutKelas');
    const zoomLevel = document.getElementById('zoomLevelKelas');

    if (btnZoomIn) {
        btnZoomIn.addEventListener('click', () => {
            currentZoom = Math.min(currentZoom + 5, 100);
            applyZoom();
        });
    }
    if (btnZoomOut) {
        btnZoomOut.addEventListener('click', () => {
            currentZoom = Math.max(currentZoom - 10, 10);
            applyZoom();
        });
    }

    function applyZoom() {
        if (pdfPreview) pdfPreview.style.transform = `scale(${currentZoom / 100})`;
        if (zoomLevel) zoomLevel.textContent = currentZoom + '%';
    }

    if (pdfPreview) {
        pdfPreview.addEventListener('wheel', (e) => {
            if (e.ctrlKey) {
                e.preventDefault();
                currentZoom = (e.deltaY < 0) ? Math.min(currentZoom + 5, 150) : Math.max(currentZoom - 5, 10);
                applyZoom();
            }
        }, { passive: false });
    }

    modalEl.addEventListener('shown.bs.modal', () => updatePreviewKelas());

    checkKomponen.forEach(chk => chk.addEventListener('change', updatePreviewKelas));
    if (configOrientation) configOrientation.addEventListener('change', updatePreviewKelas);
    inputTtd.forEach(ipt => {
        ipt.addEventListener('input', updatePreviewKelas);
        ipt.addEventListener('change', updatePreviewKelas);
    });

    const dateAutoRad = document.getElementById('dateAutoKelas');
    const dateManualRad = document.getElementById('dateManualKelas');
    if (dateAutoRad && dateManualRad) {
        [dateAutoRad, dateManualRad].forEach(rad => {
            rad.addEventListener('change', function() {
                const manualContainer = document.getElementById('manualDateContainerKelas');
                if (dateManualRad.checked) {
                    manualContainer.classList.remove('d-none');
                } else {
                    manualContainer.classList.add('d-none');
                }
                updatePreviewKelas();
            });
        });
    }

    function updatePreviewKelas() {
        try {
            const selected = Array.from(checkKomponen).filter(c => c.checked).map(c => c.value);
            const thead = document.getElementById('previewTableHeadKelas');
            const body = document.getElementById('previewTableBodyKelas');
            const fileNameEl = document.getElementById('previewFileNameKelas');

            if (!thead || !body) return;

            let totalCols = 2; // Rank, Nama
            if (selected.includes('nisn')) totalCols += 1;
            if (selected.includes('nis')) totalCols += 1;
            if (selected.includes('c1c4')) totalCols += 4;
            if (selected.includes('n1n4')) totalCols += 4;
            if (selected.includes('preferensi')) totalCols += 1;
            if (selected.includes('totalnilai')) totalCols += 1;
            if (selected.includes('ratarata')) totalCols += 1;

            const orientationAlert = document.getElementById('orientationAlertKelas');
            if (totalCols > 7) {
                if (configOrientation) {
                    configOrientation.value = 'landscape';
                    configOrientation.disabled = true;
                }
                if (orientationAlert) orientationAlert.classList.remove('d-none');
            } else {
                if (configOrientation) configOrientation.disabled = false;
                if (orientationAlert) orientationAlert.classList.add('d-none');
            }

            const orientation = configOrientation ? configOrientation.value : 'portrait';
            if (pdfPreview) pdfPreview.className = 'pdf-preview-box ' + orientation;
            applyZoom();

            // Filename
            const batchData = document.getElementById('batchDataInfo');
            const taClean = batchData ? batchData.dataset.taClean : 'TA';
            const klsClean = batchData ? batchData.dataset.klsClean : 'Kelas';
            const smtClean = batchData ? batchData.dataset.smtClean : 'Semester';
            const now = new Date();
            const ts = now.getFullYear() + '' + (now.getMonth() + 1).toString().padStart(2, '0') + now.getDate().toString().padStart(2, '0') + '_' + now.getHours().toString().padStart(2, '0') + now.getMinutes().toString().padStart(2, '0');
            const fullFilename = `Laporan_Kelas_${smtClean}_${taClean}_${klsClean}_${ts}.pdf`;
            if (fileNameEl) fileNameEl.innerHTML = `<i class="bi bi-file-earmark-pdf me-2"></i>${fullFilename}`;

            const dateModeEl = document.querySelector('input[name="dateModeKelas"]:checked');
            const dateMode = dateModeEl ? dateModeEl.value : 'auto';
            let displayDate = "";
            if (dateMode === 'auto') {
                displayDate = `${now.getDate()} ${monthNames[now.getMonth() + 1]} ${now.getFullYear()}`;
            } else {
                const val = document.getElementById('configDateKelas').value;
                if (val) {
                    const d = new Date(val);
                    displayDate = `${d.getDate()} ${monthNames[d.getMonth() + 1]} ${d.getFullYear()}`;
                } else displayDate = "-";
            }
            const pDateText = document.getElementById('previewDateTextKelas');
            if (pDateText) pDateText.textContent = `Karawang, ${displayDate}`;

            const nameWalas = document.getElementById('nameWalas').value || '-';
            const nameKepsek = document.getElementById('nameKepsekKelas').value || '-';
            let tWalas = document.getElementById('typeWalas').value;
            if (tWalas === 'CUSTOM') tWalas = document.getElementById('customTypeWalas').value || '...';
            let tKepsek = document.getElementById('typeKepsekKelas').value;
            if (tKepsek === 'CUSTOM') tKepsek = document.getElementById('customTypeKepsekKelas').value || '...';

            const pWalasName = document.getElementById('previewWalasName');
            if (pWalasName) pWalasName.textContent = nameWalas;
            
            const pWalasNameBottom = document.getElementById('previewWalasNameBottom');
            if (pWalasNameBottom) pWalasNameBottom.textContent = `( ${nameWalas} )`;
            
            const pWalasType = document.getElementById('previewWalasType');
            if (pWalasType) pWalasType.textContent = tWalas;
            
            const pWalasId = document.getElementById('previewWalasId');
            if (pWalasId) pWalasId.textContent = document.getElementById('idWalas').value || '-';
            
            const pKepsekName = document.getElementById('previewKepsekNameKelas');
            if (pKepsekName) pKepsekName.textContent = `( ${nameKepsek} )`;
            
            const pKepsekType = document.getElementById('previewKepsekTypeKelas');
            if (pKepsekType) pKepsekType.textContent = tKepsek;
            
            const pKepsekId = document.getElementById('previewKepsekIdKelas');
            if (pKepsekId) pKepsekId.textContent = document.getElementById('idKepsekKelas').value || '-';

            let mainHeaderHtml = '<tr><th rowspan="2" style="width:30px;">Rank</th><th rowspan="2" class="text-center">Nama Lengkap</th>';
            if (selected.includes('nisn')) mainHeaderHtml += '<th rowspan="2">NISN</th>';
            if (selected.includes('nis')) mainHeaderHtml += '<th rowspan="2">NIS</th>';
            if (selected.includes('c1c4')) mainHeaderHtml += '<th colspan="4">Kriteria (Raw)</th>';
            if (selected.includes('n1n4')) mainHeaderHtml += '<th colspan="4">Normalisasi</th>';
            if (selected.includes('preferensi')) mainHeaderHtml += '<th rowspan="2">Preferensi</th>';
            if (selected.includes('totalnilai')) mainHeaderHtml += '<th rowspan="2">Total Nilai</th>';
            if (selected.includes('ratarata')) mainHeaderHtml += '<th rowspan="2">Rata-Rata Nilai</th>';
            mainHeaderHtml += '</tr>';
            
            let subHeaderHtml = '';
            if (selected.includes('c1c4') || selected.includes('n1n4')) {
                subHeaderHtml = '<tr>';
                if (selected.includes('c1c4')) subHeaderHtml += '<th>C1</th><th>C2</th><th>C3</th><th>C4</th>';
                if (selected.includes('n1n4')) subHeaderHtml += '<th>N1</th><th>N2</th><th>N3</th><th>N4</th>';
                subHeaderHtml += '</tr>';
            }
            thead.innerHTML = mainHeaderHtml + subHeaderHtml;

            body.innerHTML = '';
            for(let i=1; i<=5; i++) {
                let rowHtml = `<tr><td>${i}</td><td class="text-left">Siswa Contoh Ke-${i}</td>`;
                if (selected.includes('nisn')) rowHtml += `<td>001234567${i}</td>`;
                if (selected.includes('nis')) rowHtml += `<td>2122100${i}</td>`;
                if (selected.includes('c1c4')) rowHtml += `<td>85.50</td><td>2</td><td>3</td><td>2</td>`;
                if (selected.includes('n1n4')) rowHtml += `<td>0.95</td><td>1.00</td><td>0.80</td><td>1.00</td>`;
                if (selected.includes('preferensi')) rowHtml += `<td>0.9234</td>`;
                if (selected.includes('totalnilai')) rowHtml += `<td>445.67</td>`;
                if (selected.includes('ratarata')) rowHtml += `<td>88.45</td>`;
                rowHtml += '</tr>';
                body.insertAdjacentHTML('beforeend', rowHtml);
            }
        } catch (e) {
            console.error("Preview Error: ", e);
        }
    }

    const btnSubmit = document.getElementById('btnSubmitGenerateKelas');
    if (btnSubmit) {
        btnSubmit.addEventListener('click', function() {
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

            const dateModeEl = document.querySelector('input[name="dateModeKelas"]:checked');
            const dateMode = dateModeEl ? dateModeEl.value : 'auto';
            const batchData = document.getElementById('batchDataInfo');
            const targetId = batchData ? batchData.dataset.batchId : '';
            const fetchUrl = batchData ? batchData.dataset.generateUrl : '';
            const notifAddUrl = batchData ? batchData.dataset.notifAddUrl : '';
            const listLaporanUrl = batchData ? batchData.dataset.listLaporanUrl : '';
            const viewKelas = batchData ? batchData.dataset.viewKelas : '';
            const viewTa = batchData ? batchData.dataset.viewTa : '';

            const config = {
                id_perhitungan: targetId,
                jenis: 'kelas',
                orientasi: configOrientation.value,
                komponen: Array.from(checkKomponen).filter(c => c.checked).map(c => c.value),
                ttd: {
                    walas_nama: document.getElementById('nameWalas').value,
                    walas_type: (document.getElementById('typeWalas').value === 'CUSTOM' ? document.getElementById('customTypeWalas').value : document.getElementById('typeWalas').value),
                    walas_id: document.getElementById('idWalas').value,
                    kepsek_nama: document.getElementById('nameKepsekKelas').value,
                    kepsek_type: (document.getElementById('typeKepsekKelas').value === 'CUSTOM' ? document.getElementById('customTypeKepsekKelas').value : document.getElementById('typeKepsekKelas').value),
                    kepsek_id: document.getElementById('idKepsekKelas').value,
                    tanggal: `Karawang, ${dateMode === 'auto' ? (new Date().getDate() + ' ' + monthNames[new Date().getMonth() + 1] + ' ' + new Date().getFullYear()) : document.getElementById('configDateKelas').value}`
                }
            };

            fetch(fetchUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(config)
            })
            .then(async response => {
                const text = await response.text();
                try { return JSON.parse(text); } catch (e) { throw new Error(text); }
            })
            .then(response => {
                if (response.success) {
                    modal.hide();

                    const badgeContainer = document.getElementById('badgeContainer');
                    if (badgeContainer) {
                        badgeContainer.innerHTML = `
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-medium animate__animated animate__fadeIn">
                                <i class="bi bi-check-circle-fill me-1"></i>Laporan Berhasil Dibuat
                            </span>
                        `;
                    }

                    const rightContainer = document.getElementById('rightActionContainer');
                    if (rightContainer) {
                        rightContainer.innerHTML = `
                            <a href="${response.download_url}" class="btn btn-primary btn-sm px-3 fw-medium shadow-sm animate__animated animate__fadeIn" download>
                                <i class="bi bi-download me-1"></i>Unduh PDF
                            </a>
                            <a href="${listLaporanUrl}" class="btn btn-outline-primary btn-sm px-3 fw-medium shadow-sm animate__animated animate__fadeIn">
                                <i class="bi bi-list-ul me-1"></i>Daftar Laporan
                            </a>
                        `;
                    }
                    
                    const msg = `Laporan Kelas ${viewKelas} ${viewTa} berhasil dibuat.`;
                    
                    fetch(notifAddUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ message: msg })
                    });

                    if (typeof window.showToast === 'function') {
                        window.showToast(msg, 'Hari ini', Math.floor(Date.now() / 1000));
                    }
                    
                    const notifList = document.getElementById('notifList');
                    if (notifList) {
                        const empty = notifList.querySelector('.notif-empty');
                        if (empty) empty.remove();
                        
                        const jamStr = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false });
                        const newItem = document.createElement('div');
                        newItem.className = 'notif-item';
                        newItem.style.backgroundColor = '#f0fff4';
                        newItem.innerHTML = `
                            <div class="notif-item-icon" style="color: #198754">
                                <i class="bi bi-person-plus-fill"></i>
                            </div>
                            <div class="notif-item-body">
                                <div class="notif-item-msg">${msg}</div>
                                <div class="notif-item-footer">
                                    <span class="notif-time-ago">Baru saja</span>
                                    <span class="notif-time-exact">${jamStr}</span>
                                </div>
                            </div>
                            <button class="notif-dismiss" title="Tutup">&times;</button>
                        `;
                        notifList.prepend(newItem);
                        
                        const badge = document.querySelector('.notif-badge');
                        if (badge) {
                            let count = parseInt(badge.textContent.replace('+', '')) || 0;
                            if (typeof window.updateNotifBadge === 'function') {
                                window.updateNotifBadge(count + 1);
                            }
                        } else {
                            const notifIcon = document.querySelector('.notif-icon');
                            if (notifIcon) {
                                notifIcon.insertAdjacentHTML('beforeend', '<span class="notif-badge">1</span>');
                            }
                        }
                    }
                } else alert('Gagal: ' + response.message);
            })
            .catch(error => {
                console.error(error);
                alert('Terjadi kesalahan sistem. Detail: ' + error.message.substring(0, 200));
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-printer me-2"></i>Generate';
            });
        });
    }
});
