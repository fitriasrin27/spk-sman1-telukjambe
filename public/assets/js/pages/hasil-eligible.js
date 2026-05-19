/* ==========================================================================
   JS Khusus: Modul Hasil Peringkat Eligible (SNBP)
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

    // [3] Configuration & Generate PDF: ELIGIBLE LAPORAN
    const modalEl = document.getElementById('modalGenerateLaporanEligible');
    const btnTrigger = document.getElementById('btnGenerateLaporan');
    
    if (!modalEl || !btnTrigger) return;

    const checkKomponen = modalEl.querySelectorAll('.check-komponen-eligible');
    const configOrientation = document.getElementById('configOrientationEligible');
    const inputTtd = modalEl.querySelectorAll('.input-ttd-eligible');
    const pdfPreview = document.getElementById('pdfPreviewEligible');
    const modal = new bootstrap.Modal(modalEl);
    let currentZoom = 45;

    btnTrigger.addEventListener('click', () => modal.show());

    const btnZoomIn = document.getElementById('btnZoomInEligible');
    const btnZoomOut = document.getElementById('btnZoomOutEligible');
    const zoomLevel = document.getElementById('zoomLevelEligible');

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

    modalEl.addEventListener('shown.bs.modal', () => updatePreviewEligible());

    checkKomponen.forEach(chk => chk.addEventListener('change', updatePreviewEligible));
    if (configOrientation) configOrientation.addEventListener('change', updatePreviewEligible);
    inputTtd.forEach(ipt => {
        ipt.addEventListener('input', updatePreviewEligible);
        ipt.addEventListener('change', updatePreviewEligible);
    });

    const dateAutoRad = document.getElementById('dateAutoEligible');
    const dateManualRad = document.getElementById('dateManualEligible');
    if (dateAutoRad && dateManualRad) {
        [dateAutoRad, dateManualRad].forEach(rad => {
            rad.addEventListener('change', function() {
                const manualContainer = document.getElementById('manualDateContainerEligible');
                if (dateManualRad.checked) {
                    manualContainer.classList.remove('d-none');
                } else {
                    manualContainer.classList.add('d-none');
                }
                updatePreviewEligible();
            });
        });
    }

    function updatePreviewEligible() {
        try {
            const selected = Array.from(checkKomponen).filter(c => c.checked).map(c => c.value);
            const thead = document.getElementById('previewTableHeadEligible');
            const body = document.getElementById('previewTableBodyEligible');
            const fileNameEl = document.getElementById('previewFileNameEligible');

            if (!thead || !body) return;

            let totalCols = 2; // Rank, Nama
            if (selected.includes('nisn')) totalCols += 1;
            if (selected.includes('nis')) totalCols += 1;
            if (selected.includes('c1c4')) totalCols += 4;
            if (selected.includes('n1n4')) totalCols += 4;
            if (selected.includes('preferensi')) totalCols += 1;
            if (selected.includes('totalnilai')) totalCols += 1;
            if (selected.includes('ratarata')) totalCols += 1;

            const orientationAlert = document.getElementById('orientationAlertEligible');
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
            const jurClean = batchData ? batchData.dataset.jurClean : 'Jurusan';
            const now = new Date();
            const ts = now.getFullYear() + '' + (now.getMonth() + 1).toString().padStart(2, '0') + now.getDate().toString().padStart(2, '0') + '_' + now.getHours().toString().padStart(2, '0') + now.getMinutes().toString().padStart(2, '0');
            const fullFilename = `Laporan_Eligible_${taClean}_${jurClean}_${ts}.pdf`;
            if (fileNameEl) fileNameEl.innerHTML = `<i class="bi bi-file-earmark-pdf me-2"></i>${fullFilename}`;

            const dateModeEl = document.querySelector('input[name="dateModeEligible"]:checked');
            const dateMode = dateModeEl ? dateModeEl.value : 'auto';
            let displayDate = "";
            if (dateMode === 'auto') {
                displayDate = `${now.getDate()} ${monthNames[now.getMonth() + 1]} ${now.getFullYear()}`;
            } else {
                const val = document.getElementById('configDateEligible').value;
                if (val) {
                    const d = new Date(val);
                    displayDate = `${d.getDate()} ${monthNames[d.getMonth() + 1]} ${d.getFullYear()}`;
                } else displayDate = "-";
            }
            const previewDateText = document.getElementById('previewDateTextEligible');
            if (previewDateText) previewDateText.textContent = `Karawang, ${displayDate}`;

            const nameBK = document.getElementById('nameBK').value || '-';
            const nameKepsek = document.getElementById('nameKepsekEligible').value || '-';
            let tBK = document.getElementById('typeBK').value;
            if (tBK === 'CUSTOM') tBK = document.getElementById('customTypeBK').value || '...';
            let tKepsek = document.getElementById('typeKepsekEligible').value;
            if (tKepsek === 'CUSTOM') tKepsek = document.getElementById('customTypeKepsekEligible').value || '...';

            const pBKNameBottom = document.getElementById('previewBKNameBottom');
            if (pBKNameBottom) pBKNameBottom.textContent = `( ${nameBK} )`;
            
            const pBKType = document.getElementById('previewBKType');
            if (pBKType) pBKType.textContent = tBK;
            
            const pBKId = document.getElementById('previewBKId');
            if (pBKId) pBKId.textContent = document.getElementById('idBK').value || '-';
            
            const pKepsekName = document.getElementById('previewKepsekNameEligible');
            if (pKepsekName) pKepsekName.textContent = `( ${nameKepsek} )`;
            
            const pKepsekType = document.getElementById('previewKepsekTypeEligible');
            if (pKepsekType) pKepsekType.textContent = tKepsek;
            
            const pKepsekId = document.getElementById('previewKepsekIdEligible');
            if (pKepsekId) pKepsekId.textContent = document.getElementById('idKepsekEligible').value || '-';

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
            const showStatus = selected.includes('status');
            const previewKuota = 5;
            
            for(let i=1; i<=10; i++) {
                const isEligible = i <= previewKuota;
                const bgStyle = showStatus ? (isEligible ? 'background-color:#f1f9f1' : 'background-color:#fff5f5') : '';
                
                let rowHtml = `<tr style="${bgStyle}"><td>${i}</td><td class="text-left">Siswa Contoh Ke-${i}</td>`;
                if (selected.includes('nisn')) rowHtml += `<td>001234567${i}</td>`;
                if (selected.includes('nis')) rowHtml += `<td>2122100${i}</td>`;
                if (selected.includes('c1c4')) rowHtml += `<td>85.50</td><td>2</td><td>3</td><td>2</td>`;
                if (selected.includes('n1n4')) rowHtml += `<td>0.95</td><td>1.00</td><td>0.80</td><td>1.00</td>`;
                if (selected.includes('preferensi')) {
                    const prefColor = showStatus ? (isEligible ? '#198754' : '#dc3545') : '';
                    rowHtml += `<td style="color:${prefColor}; font-weight:600;">0.9234</td>`;
                }
                if (selected.includes('totalnilai')) rowHtml += `<td>445.67</td>`;
                if (selected.includes('ratarata')) rowHtml += `<td>88.45</td>`;
                rowHtml += '</tr>';
                body.insertAdjacentHTML('beforeend', rowHtml);

                if (showStatus && i === previewKuota) {
                    const batasText = `Batas Kuota — Peringkat ${previewKuota + 1} ke bawah tidak mendapat kuota SNBP`;
                    body.insertAdjacentHTML('beforeend', `
                        <tr class="eligible-divider">
                            <td colspan="${totalCols}" style="padding: 6px; border-top: 2px solid #fbbf24; border-bottom: 2px solid #fbbf24;">
                                <i class="bi bi-scissors me-1"></i> ${batasText}
                            </td>
                        </tr>
                    `);
                }
            }
        } catch (e) {
            console.error("Preview Error: ", e);
        }
    }

    const btnSubmit = document.getElementById('btnSubmitGenerateEligible');
    if (btnSubmit) {
        btnSubmit.addEventListener('click', function() {
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

            const dateModeEl = document.querySelector('input[name="dateModeEligible"]:checked');
            const dateMode = dateModeEl ? dateModeEl.value : 'auto';
            const batchData = document.getElementById('batchDataInfo');
            const targetId = batchData ? batchData.dataset.batchId : '';
            const fetchUrl = batchData ? batchData.dataset.generateUrl : '';
            const notifAddUrl = batchData ? batchData.dataset.notifAddUrl : '';
            const listLaporanUrl = batchData ? batchData.dataset.listLaporanUrl : '';
            const viewJurusan = batchData ? batchData.dataset.viewJurusan : '';
            const viewTa = batchData ? batchData.dataset.viewTa : '';

            const config = {
                id_perhitungan: targetId,
                jenis: 'eligible',
                orientasi: configOrientation.value,
                komponen: Array.from(checkKomponen).filter(c => c.checked).map(c => c.value),
                ttd: {
                    bk_nama: document.getElementById('nameBK').value,
                    bk_type: (document.getElementById('typeBK').value === 'CUSTOM' ? document.getElementById('customTypeBK').value : document.getElementById('typeBK').value),
                    bk_id: document.getElementById('idBK').value,
                    kepsek_nama: document.getElementById('nameKepsekEligible').value,
                    kepsek_type: (document.getElementById('typeKepsekEligible').value === 'CUSTOM' ? document.getElementById('customTypeKepsekEligible').value : document.getElementById('typeKepsekEligible').value),
                    kepsek_id: document.getElementById('idKepsekEligible').value,
                    tanggal: `Karawang, ${dateMode === 'auto' ? (new Date().getDate() + ' ' + monthNames[new Date().getMonth() + 1] + ' ' + new Date().getFullYear()) : document.getElementById('configDateEligible').value}`
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
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fw-medium animate__animated animate__fadeIn">
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
                    
                    const msg = `Laporan Eligible XII ${viewJurusan} ${viewTa} berhasil dibuat.`;
                    
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
