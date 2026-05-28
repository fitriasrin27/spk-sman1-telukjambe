/**
 * Logic JS Halaman Log Aktivitas Real-time (AJAX Powered)
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Inisialisasi State Global
    let lastId = 0;
    let currentPage = 1;
    let activeAbortController = null;

    // Ambil references element
    const tbody = document.getElementById('logTableBody');
    const searchInput = document.getElementById('searchLog');
    const filterRole = document.getElementById('filterRole');
    const filterModul = document.getElementById('filterModul');
    const filterRentang = document.getElementById('filterRentang');
    const filterStartDate = document.getElementById('filterStartDate');
    const filterEndDate = document.getElementById('filterEndDate');
    const customDateWrapper = document.getElementById('customDateWrapper');
    const limitSelect = document.getElementById('logLimitSelect');
    const btnReset = document.getElementById('btnResetFilters');
    const btnManualRefresh = document.getElementById('btnManualRefresh');
    const refreshIcon = document.getElementById('refreshIcon');
    const livePulse = document.getElementById('livePulse');
    const liveStatusText = document.getElementById('liveStatusText');

    // Tentukan limit saat ini dari DOM yang dirender PHP
    let currentLimit = parseInt(limitSelect ? limitSelect.value : 10);

    // Ambil lastId awal dari element baris pertama
    if (tbody) {
        const firstRow = tbody.querySelector('tr.log-row');
        if (firstRow) {
            lastId = parseInt(firstRow.getAttribute('data-id')) || 0;
        }
    }

    // 2. Helper Utilities
    function getInitials(name) {
        const parts = name.split(' ');
        let initials = '';
        parts.forEach(p => {
            if (p) initials += p.charAt(0).toUpperCase();
        });
        return initials.substring(0, 2) || 'U';
    }

    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Generator baris HTML
    function generateRowHtml(log, index, page, limit) {
        const num = (page - 1) * limit + index + 1;
        const userRole = log.user_role || '';
        
        // Definisi badge style peran (Konsisten dengan Kelola Akun)
        const roleBadges = {
            'operator': { label: 'OP', style: 'background:#cfe2ff;color:#084298;' },
            'wali_kelas': { label: 'Walas', style: 'background:#d1e7dd;color:#0a3622;' },
            'bk': { label: 'BK', style: 'background:#e8d5ff;color:#432874;' },
            'tu': { label: 'TU', style: 'background:#ffe5d0;color:#7c3c00;' },
            'wakasek': { label: 'Wakasek', style: 'background:#cff4fc;color:#055160;' },
            'kepala_sekolah': { label: 'Kepsek', style: 'background:#f8d7da;color:#842029;' },
            'admin': { label: 'Admin', style: 'background:#e0f2fe;color:#0369a1;' }
        };
        
        const rb = roleBadges[userRole] || null;
        let badgeHtml = '<span class="badge bg-secondary text-white" style="font-size: 0.62rem; padding: 2px 6px; font-weight: 600;">System</span>';
        if (rb) {
            badgeHtml = `<span class="badge" style="${rb.style}; font-size: 0.62rem; padding: 2px 6px; font-weight: 600;">${rb.label}</span>`;
        }
        
        // Browser Icons Mapping
        // PENTING: Samsung, UC, Opera, Edge harus dicek SEBELUM Chrome
        // karena UA mereka juga mengandung kata "Chrome"
        const ua = log.user_agent || '';
        let browser = 'Unknown';
        if (/firefox/i.test(ua))                           browser = 'Firefox';
        else if (/SamsungBrowser/i.test(ua))               browser = 'Samsung';
        else if (/UCBrowser/i.test(ua))                    browser = 'UC Browser';
        else if (/opr|Opera Mini|opera/i.test(ua))         browser = 'Opera';
        else if (/Edg\/|Edge/i.test(ua))                  browser = 'Edge';
        else if (/MSIE|Trident\//i.test(ua))               browser = 'IE';
        else if (/chrome/i.test(ua))                       browser = 'Chrome';
        else if (/safari/i.test(ua))                       browser = 'Safari';

        // Warna brand asli + logo nyata browser (outline, vivid)
        const cdnBase = 'https://cdn.jsdelivr.net/gh/alrra/browser-logos@main/src';
        const browserStyles = {
            'Chrome':      { logo: `${cdnBase}/chrome/chrome_24x24.png`,                                  icon: 'bi-google',          bg: '#eef3fd', color: '#4285F4', border: '#4285F4' },
            'Edge':        { logo: `${cdnBase}/edge/edge_24x24.png`,                                      icon: 'bi-browser-edge',    bg: '#e3f3fb', color: '#0078D4', border: '#0078D4' },
            'Firefox':     { logo: `${cdnBase}/firefox/firefox_24x24.png`,                                icon: 'bi-browser-firefox', bg: '#fff4ef', color: '#FF7139', border: '#FF7139' },
            'Safari':      { logo: `${cdnBase}/safari/safari_24x24.png`,                                  icon: 'bi-compass',         bg: '#e5f0ff', color: '#006CFF', border: '#006CFF' },
            'Opera':       { logo: `${cdnBase}/opera/opera_24x24.png`,                                    icon: 'bi-globe2',          bg: '#ffebec', color: '#FF1B2D', border: '#FF1B2D' },
            'Samsung':     { logo: `${cdnBase}/samsung-internet/samsung-internet_24x24.png`,              icon: 'bi-phone',           bg: '#e8eaf6', color: '#1428A0', border: '#1428A0' },
            'UC Browser':  { logo: `${cdnBase}/uc/uc_24x24.png`,                                         icon: 'bi-lightning-charge',bg: '#fff3e0', color: '#FF6900', border: '#FF6900' },
            'IE':          { logo: `${cdnBase}/internet-explorer_9-11/internet-explorer_9-11_24x24.png`,  icon: 'bi-window',          bg: '#e5f7fc', color: '#1EBBEE', border: '#1EBBEE' },
            'Unknown':     { logo: '',                                                                     icon: 'bi-globe',           bg: '#f1f3f5', color: '#6c757d', border: '#adb5bd' },
        };
        const bs = browserStyles[browser] || browserStyles['Unknown'];

        // Versi LENGKAP dari UA — Firefox/Samsung/Opera/dll tampil versi asli berbeda
        // Chrome & Edge sama-sama .0.0.0 karena UA Reduction policy, tapi itu wajar
        let browserVersion = '';
        if (browser === 'Chrome')          { const m = ua.match(/Chrome\/([\d.]+)/);          browserVersion = m?.[1] || ''; }
        else if (browser === 'Edge')       { const m = ua.match(/Edg\/([\d.]+)/);             browserVersion = m?.[1] || ''; }
        else if (browser === 'Firefox')    { const m = ua.match(/Firefox\/([\d.]+)/);         browserVersion = m?.[1] || ''; }
        else if (browser === 'Safari')     { const m = ua.match(/Version\/([\d.]+)/);         browserVersion = m?.[1] || ''; }
        else if (browser === 'Opera')      { const m = ua.match(/(?:OPR|Version)\/([\d.]+)/); browserVersion = m?.[1] || ''; }
        else if (browser === 'Samsung')    { const m = ua.match(/SamsungBrowser\/([\d.]+)/);  browserVersion = m?.[1] || ''; }
        else if (browser === 'UC Browser') { const m = ua.match(/UCBrowser\/([\d.]+)/);       browserVersion = m?.[1] || ''; }
        else if (browser === 'IE')         { const m = ua.match(/(?:MSIE |rv:)([\d.]+)/);     browserVersion = m?.[1] || ''; }
        const browserTooltip = browser + (browserVersion ? ' ' + browserVersion : '');

        // HTML logo: pakai gambar nyata, fallback ke Bootstrap icon jika gagal load
        const logoHtml = bs.logo
            ? `<img src="${bs.logo}" width="13" height="13" style="vertical-align:middle;flex-shrink:0;"
                    onerror="this.style.display='none';this.nextElementSibling.classList.remove('d-none');">
               <i class="bi ${bs.icon} d-none" style="font-size:0.8rem;"></i>`
            : `<i class="bi ${bs.icon}" style="font-size:0.8rem;"></i>`;
        
        // Modul styles
        let modClass = 'bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle';
        switch (log.modul) {
            case 'auth':        modClass = 'bg-danger bg-opacity-10 text-danger border border-danger-subtle'; break;
            case 'siswa':       modClass = 'bg-success bg-opacity-10 text-success border border-success-subtle'; break;
            case 'nilai':       modClass = 'bg-warning bg-opacity-10 text-warning border border-warning-subtle'; break;
            case 'perhitungan': modClass = 'bg-info bg-opacity-10 text-info border border-info-subtle'; break;
            case 'kriteria':    modClass = 'bg-primary bg-opacity-10 text-primary border border-primary-subtle'; break;
            case 'laporan':     modClass = 'bg-dark bg-opacity-10 text-dark border border-dark-subtle'; break;
            case 'akun':        modClass = 'bg-purple-subtle text-purple border border-purple-subtle'; break;
        }
        
        // Avatar HTML
        let avatarHtml = '';
        if (log.foto) {
            avatarHtml = `<img src="${assetBaseUrl}public/uploads/profile/${log.foto}" class="rounded-circle me-2-5 object-fit-cover border" style="width: 32px; height: 32px;">`;
        } else {
            const initials = getInitials(log.nama || log.username_fallback || 'U');
            avatarHtml = `<div class="avatar-circle me-2-5 fw-bold text-primary rounded-circle" style="font-size: 0.75rem; background: #e0f2fe; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">${initials}</div>`;
        }
        
        const namaDisplay = escapeHtml(log.nama_display);
        const aktivitas = log.aktivitas; // Sudah di-escape secara aman di backend PHP dengan link HTML
        const ipAddress = escapeHtml(log.ip_address);
        
        return `
            <tr class="log-row" data-id="${log.id_log}">
                <td class="text-center align-middle text-dark num-cell">${num}</td>
                <td class="text-center align-middle time-cell">
                    <div class="fw-bold text-dark mb-0.5" style="font-size: 0.8rem;">
                        ${log.time_elapsed}
                    </div>
                    <small class="text-muted d-block" style="font-size: 0.72rem;">
                        ${log.formatted_date}
                    </small>
                </td>
                <td class="align-middle">
                    <div class="d-flex align-items-center ps-1">
                        ${avatarHtml}
                        <div>
                            <div class="fw-bold text-dark lh-1 mb-1" style="font-size: 0.85rem;">
                                ${namaDisplay}
                            </div>
                            ${badgeHtml}
                        </div>
                    </div>
                </td>
                <td class="text-center align-middle">
                    <span class="badge rounded-pill ${modClass}" style="font-size:0.68rem; font-weight:600; padding: 4px 8px;">
                        ${log.modul.toUpperCase()}
                    </span>
                </td>
                <td class="text-dark small lh-sm align-middle">${aktivitas}</td>
                <td class="text-center align-middle small text-muted">
                    <span class="badge bg-light text-secondary border border-light-subtle px-2 py-1"><i class="bi bi-laptop me-1"></i>${ipAddress}</span>
                </td>
                <td class="text-center align-middle small">
                    <span data-bs-toggle="tooltip" title="${escapeHtml(browserTooltip)}"
                          class="badge px-2 py-1 d-inline-flex align-items-center gap-1"
                          style="background:${bs.bg}; color:${bs.color}; border:1px solid ${bs.border}; font-weight:600; line-height:1.3;">
                        ${logoHtml}
                        ${browser}
                    </span>
                </td>
            </tr>
        `;
    }


    // Render Pagination links
    function updatePagination(page, totalPages) {
        const wrapper = document.getElementById('paginationNavWrapper');
        if (!wrapper) return;
        
        if (totalPages <= 1) {
            wrapper.innerHTML = '';
            return;
        }
        
        let html = `<ul class="pagination pagination-sm justify-content-center mb-0" id="logPagination">`;
        
        // Previous Button
        const prevDisabled = (page <= 1) ? 'disabled' : '';
        html += `<li class="page-item ${prevDisabled}" data-page="${page - 1}">
            <a class="page-link" href="#">Previous</a>
        </li>`;
        
        // Page links
        for (let p = 1; p <= totalPages; p++) {
            const activeClass = (p === page) ? 'active' : '';
            html += `<li class="page-item ${activeClass}" data-page="${p}">
                <a class="page-link" href="#">${p}</a>
            </li>`;
        }
        
        // Next Button
        const nextDisabled = (page >= totalPages) ? 'disabled' : '';
        html += `<li class="page-item ${nextDisabled}" data-page="${page + 1}">
            <a class="page-link" href="#">Next</a>
        </li>`;
        
        html += `</ul>`;
        wrapper.innerHTML = html;
        
        // Re-bind click handlers
        wrapper.querySelectorAll('.page-item:not(.disabled)').forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                const newPage = parseInt(this.getAttribute('data-page'));
                if (newPage && newPage !== currentPage) {
                    currentPage = newPage;
                    fetchLogs();
                }
            });
        });
    }

    // Render metrics cards
    function updateMetrics(metrics) {
        if (!metrics) return;
        
        const mTotalAct = document.getElementById('metric-total-activities');
        const mActUser = document.getElementById('metric-active-users');
        const mTotalCalc = document.getElementById('metric-total-calculations');
        const mRepCreated = document.getElementById('metric-reports-created');
        const mRepDownloaded = document.getElementById('metric-reports-downloaded');
        
        if (mTotalAct) mTotalAct.textContent = metrics.total_activities;
        if (mActUser) mActUser.textContent = metrics.active_users;
        if (mTotalCalc) mTotalCalc.textContent = metrics.total_calculations;
        if (mRepCreated) mRepCreated.textContent = metrics.total_reports_created;
        if (mRepDownloaded) mRepDownloaded.textContent = metrics.total_reports_downloaded;
    }

    // 3. Inti Aksi AJAX
    function fetchLogs(isManualRefresh = false) {
        // Abort previous request to prevent race condition
        if (activeAbortController) {
            activeAbortController.abort();
        }
        activeAbortController = new AbortController();

        const q = searchInput ? searchInput.value.trim() : '';
        const role = filterRole ? filterRole.value : '';
        const modul = filterModul ? filterModul.value : '';
        const rentang = filterRentang ? filterRentang.value : 'all';
        const start_date = filterStartDate ? filterStartDate.value : '';
        const end_date = filterEndDate ? filterEndDate.value : '';
        const limit = parseInt(limitSelect ? limitSelect.value : 10);
        
        currentLimit = limit;

        // Visual Spinner
        if (isManualRefresh && refreshIcon) {
            refreshIcon.classList.add('spin-icon');
        }

        const params = new URLSearchParams({
            q,
            role,
            modul,
            rentang,
            start_date,
            end_date,
            limit,
            page: currentPage
        });

        const fetchUrl = `${apiFetchUrl}&${params.toString()}`;

        fetch(fetchUrl, { signal: activeAbortController.signal })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (tbody) {
                        if (data.logs.length > 0) {
                            let html = '';
                            data.logs.forEach((log, index) => {
                                html += generateRowHtml(log, index, data.page, data.limit);
                            });
                            tbody.innerHTML = html;

                            // Update lastId jika berada di Page 1 (untuk kelanjutan polling)
                            if (data.page === 1) {
                                lastId = data.logs[0] ? data.logs[0].id_log : 0;
                            }
                        } else {
                            tbody.innerHTML = `
                                <tr id="emptyRow">
                                    <td colspan="7" class="text-center py-5 text-muted align-middle">Belum ada data log aktivitas.</td>
                                </tr>
                            `;
                        }
                    }

                    // Update components
                    updatePagination(data.page, data.totalPages);
                    updateMetrics(data.metrics);

                    // Entry counter
                    const entryCounter = document.getElementById('entryCounter');
                    if (entryCounter) {
                        const start = data.total > 0 ? (data.page - 1) * data.limit + 1 : 0;
                        const end = Math.min(data.page * data.limit, data.total);
                        entryCounter.textContent = `Show ${start} to ${end} of ${data.total} entries`;
                    }

                    // Update timestamp refresh terakhir
                    const lastRefreshTime = document.getElementById('lastRefreshTime');
                    if (lastRefreshTime) {
                        const now = new Date();
                        const pad = (n) => String(n).padStart(2, '0');
                        lastRefreshTime.textContent = `Terakhir diperbarui: ${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
                    }

                    // Reset Bootstrap Tooltips
                    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                    tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
                }
            })
            .catch(err => {
                if (err.name !== 'AbortError') {
                    console.error("Gagal memuat log via AJAX:", err);
                }
            })
            .finally(() => {
                if (isManualRefresh && refreshIcon) {
                    refreshIcon.classList.remove('spin-icon');
                }
            });
    }

    // Cek kelayakan Live Polling (hanya page 1 dan tanpa filter/search)
    function checkIfPollingActive() {
        if (currentPage > 1) return false;
        
        const q = searchInput ? searchInput.value.trim() : '';
        const role = filterRole ? filterRole.value : '';
        const modul = filterModul ? filterModul.value : '';
        const rentang = filterRentang ? filterRentang.value : 'all';

        return (q === '' && role === '' && modul === '' && rentang === 'all');
    }

    // 4. Setup Event Listeners
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            currentPage = 1;
            fetchLogs(); // auto-search instan tanpa jeda
        });
    }

    [filterRole, filterModul].forEach(select => {
        if (select) {
            select.addEventListener('change', () => {
                currentPage = 1;
                fetchLogs();
            });
        }
    });

    if (filterRentang) {
        filterRentang.addEventListener('change', function () {
            if (this.value === 'custom') {
                if (customDateWrapper) {
                    customDateWrapper.classList.remove('d-none');
                }
            } else {
                if (customDateWrapper) {
                    customDateWrapper.classList.add('d-none');
                }
                // Bersihkan input kalender
                if (filterStartDate) filterStartDate.value = '';
                if (filterEndDate) filterEndDate.value = '';
            }
            currentPage = 1;
            fetchLogs();
        });
    }

    [filterStartDate, filterEndDate].forEach(dateInput => {
        if (dateInput) {
            dateInput.addEventListener('change', () => {
                currentPage = 1;
                fetchLogs();
            });
        }
    });

    if (limitSelect) {
        limitSelect.addEventListener('change', () => {
            currentPage = 1;
            fetchLogs();
        });
    }

    // Reset filters
    if (btnReset) {
        btnReset.addEventListener('click', () => {
            if (searchInput) searchInput.value = '';
            if (filterRole) filterRole.value = '';
            if (filterModul) filterModul.value = '';
            if (filterRentang) filterRentang.value = 'all';
            if (filterStartDate) filterStartDate.value = '';
            if (filterEndDate) filterEndDate.value = '';
            
            if (customDateWrapper) {
                customDateWrapper.classList.add('d-none');
            }

            currentPage = 1;
            fetchLogs();
        });
    }

    // Manual Refresh
    if (btnManualRefresh) {
        btnManualRefresh.addEventListener('click', () => {
            fetchLogs(true);
        });
    }

    // Initialize tooltips dari PHP render pertama
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // 5. Background Polling (Setiap 5 detik)
    setInterval(function () {
        const isPollingActive = checkIfPollingActive();

        if (isPollingActive) {
            if (livePulse) {
                livePulse.style.backgroundColor = '#10b981';
                livePulse.style.boxShadow = '0 0 0 0 rgba(16, 185, 129, 0.7)';
            }
            if (liveStatusText) {
                liveStatusText.textContent = 'LIVE MONITORING';
                liveStatusText.className = 'text-success fw-bold small mb-0';
            }
        } else {
            if (livePulse) {
                livePulse.style.backgroundColor = '#64748b';
                livePulse.style.boxShadow = 'none';
            }
            if (liveStatusText) {
                liveStatusText.textContent = 'MONITORING PAUSED';
                liveStatusText.className = 'text-secondary fw-semibold small mb-0';
            }
            return; // Skip polling if filters or paging active
        }

        if (lastId === 0) return;

        // Fetch logs baru semenjak lastId
        const fetchUrl = `${apiFetchUrl}&last_id=${lastId}`;

        fetch(fetchUrl)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.logs.length > 0) {
                    // Update lastId dengan log terbaru
                    lastId = data.logs[data.logs.length - 1].id_log;

                    const emptyRow = document.getElementById('emptyRow');
                    if (emptyRow) {
                        emptyRow.remove();
                    }

                    // Prepend log baru secara terbalik (dari lama ke baru agar berurutan)
                    data.logs.forEach(log => {
                        const trHtml = generateRowHtml(log, 0, 1, currentLimit);
                        const tempDiv = document.createElement('tbody');
                        tempDiv.innerHTML = trHtml;
                        const tr = tempDiv.firstElementChild;
                        tr.classList.add('new-log-highlight');

                        if (tbody) {
                            tbody.insertBefore(tr, tbody.firstChild);
                        }
                    });

                    // Potong baris berlebih sesuai limit
                    const rows = tbody.querySelectorAll('tr.log-row');
                    if (rows.length > currentLimit) {
                        for (let i = currentLimit; i < rows.length; i++) {
                            rows[i].remove();
                        }
                    }

                    // Re-index urutan nomor kolom No
                    const updatedRows = tbody.querySelectorAll('tr.log-row');
                    updatedRows.forEach((r, idx) => {
                        const numCell = r.querySelector('.num-cell');
                        if (numCell) {
                            numCell.textContent = idx + 1;
                        }
                    });

                    // Re-initialize tooltips
                    const tooltips = tbody.querySelectorAll('[data-bs-toggle="tooltip"]');
                    tooltips.forEach(el => new bootstrap.Tooltip(el));
                }

                // Selalu update metrics jika ada data baru
                if (data.metrics) {
                    updateMetrics(data.metrics);
                }

                // Update timestamp refresh terakhir
                const lastRefreshTime = document.getElementById('lastRefreshTime');
                if (lastRefreshTime) {
                    const now = new Date();
                    const pad = (n) => String(n).padStart(2, '0');
                    lastRefreshTime.textContent = `Terakhir diperbarui: ${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
                }
            })
            .catch(err => {
                console.error("Gagal melakukan polling data log:", err);
            });
    }, 5000);
});
