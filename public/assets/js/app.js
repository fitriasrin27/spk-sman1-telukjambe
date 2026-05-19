/* ==========================================================================
   DAFTAR ISI (Ctrl+F untuk navigasi cepat):
   1. LOGIN    – Toggle show/hide password di halaman login
   2. NAVBAR   – Klik dropdown langsung navigasi ke href
   3. MASTER DATA – Modal Edit & Hapus (Siswa, Riwayat Kelas, Mapel, Nilai)
   4. RK HELPER   – Fungsi autocomplete penempatan kelas (rkSelectSiswa)
   5. NOTIFIKASI  – Sistem toast popup & render waktu lokal
   6. NOTIF DISMISS – Hapus satu/semua notif dari dropdown
   7. NILAI MODAL  – Autocomplete siswa & form dinamis (Ekskul, Prestasi)
   ========================================================================== */


/* ==========================================================================
   1. LOGIN – Toggle show/hide password
   ========================================================================== */
/*Toggle password visibility (halaman login) */
document.addEventListener('DOMContentLoaded', () => {
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#password');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', () => {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

            const icon = togglePassword.querySelector('i');
            if (icon) {
                icon.classList.toggle('bi-eye', !isPassword);
                icon.classList.toggle('bi-eye-slash', isPassword);
            }
        });
    }
});


/* ==========================================================================
   2. NAVBAR – Dropdown klik langsung navigasi ke href
   ========================================================================== */
/* ==================================================
   Navbar dropdown toggle - klik langsung ke href
   ================================================== */
document.querySelectorAll('.dropdown-toggle').forEach(function (el) {
    el.addEventListener('click', function (e) {
        if (this.getAttribute('href') !== '#') {
            window.location = this.getAttribute('href');
        }
    });
});


/* ==========================================================================
   3. MASTER DATA – Populate modal Edit & Hapus
      (Siswa, Riwayat Kelas, Mata Pelajaran, Nilai)
   ========================================================================== */
/* ==================================================
   Halaman Siswa – populate modal Edit & Hapus
   ================================================== */
document.addEventListener('DOMContentLoaded', function () {

    // Siswa: Isi form modal Edit
    document.querySelectorAll('.btn-edit').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('editId').value = this.dataset.id;
            document.getElementById('editNama').value = this.dataset.nama;
            document.getElementById('editNisn').value = this.dataset.nisn;
            document.getElementById('editNis').value = this.dataset.nis;
            document.getElementById('editGender').value = this.dataset.gender;
        });
    });

    // Siswa: Isi form modal Hapus
    document.querySelectorAll('.btn-hapus').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var deleteUrl = btn.dataset.deleteUrl;
            document.getElementById('hapusNama').textContent = this.dataset.nama;
            document.getElementById('hapusNisn').textContent = 'NISN: ' + (this.dataset.nisn || '-');
            document.getElementById('hapusLink').href = deleteUrl + '&id=' + this.dataset.id;
        });
    });

    // Riwayat Kelas: Isi form modal Edit
    document.querySelectorAll('.btn-rk-edit').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('editRkId').value = this.dataset.id;
            document.getElementById('editRkNama').value = this.dataset.nama;
            document.getElementById('editRkNisn').value = this.dataset.nisn;
            document.getElementById('editRkNis').value = this.dataset.nis;
            document.getElementById('editRkGender').value = this.dataset.gender === 'L' ? 'Laki-laki' : 'Perempuan';
            document.getElementById('editRkTahun').value = this.dataset.tahun;
            document.getElementById('editRkKelas').value = this.dataset.kelas;
            document.getElementById('editRkSemJenis').value = this.dataset.semjenis;
        });
    });

    // Riwayat Kelas: Isi form modal Hapus
    document.querySelectorAll('.btn-rk-hapus').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('hapusRkNama').textContent = this.dataset.nama;
            document.getElementById('hapusRkSemLabel').textContent = this.dataset.semLabel || '';
            document.getElementById('hapusRkLink').href =
                this.dataset.deleteUrl + '&id=' + this.dataset.id;
        });
    });

    // Riwayat Kelas: Tombol tambah dari baris yang belum punya penempatan
    document.querySelectorAll('.btn-rk-tambah').forEach(function (btn) {
        btn.addEventListener('click', function () {
            rkSelectSiswa({
                id_siswa: this.dataset.idSiswa,
                nama: this.dataset.nama,
                nisn: this.dataset.nisn,
                nis: this.dataset.nis,
                jenis_kelamin: this.dataset.gender,
            });
        });
    });

    // Mata Pelajaran: Isi form modal Edit
    document.querySelectorAll('.btn-edit-mapel').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('editIdMapel').value = this.dataset.id;
            document.getElementById('editKodeMapel').value = this.dataset.kode;
            document.getElementById('editNamaMapel').value = this.dataset.nama;
            document.getElementById('editTingkat').value = this.dataset.tingkat;
            document.getElementById('editJurusan').value = this.dataset.jurusan;
        });
    });

    // Mata Pelajaran: Isi form modal Hapus
    document.querySelectorAll('.btn-hapus-mapel').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('hapusKodeMapel').textContent = this.dataset.kode;
            document.getElementById('hapusNamaMapel').textContent = this.dataset.nama;
            document.getElementById('btnConfirmHapusMapel').href = this.dataset.deleteUrl + '&id=' + this.dataset.id;
        });
    });

    // Hapus Nilai
    document.querySelectorAll('.btn-nilai-hapus').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const namaTarget = document.getElementById('hapusNilaiNama');
            const linkTarget = document.getElementById('btnConfirmHapusNilai');

            if (namaTarget) {
                namaTarget.textContent = this.dataset.nama || '';
            }

            if (linkTarget) {
                linkTarget.href = this.dataset.deleteUrl + '&id=' + this.dataset.id;
            }
        });
    });

    // Tambah Baris Mapel (Modal Tambah)
    const btnTambahBaris = document.getElementById('btnTambahBarisMapel');
    const containerMapel = document.getElementById('dynamicMapelContainer');
    if (btnTambahBaris && containerMapel) {
        let mapelIndex = 1;
        btnTambahBaris.addEventListener('click', function () {
            const html = `
                <div class="row g-2 align-items-center mb-2 mapel-row" style="opacity: 0; transition: opacity 0.2s ease-in-out;">
                    <div class="col-4">
                        <input type="text" name="mapel[${mapelIndex}][kode]" class="form-control" placeholder="Kode Mapel" required>
                    </div>
                    <div class="col-7">
                        <input type="text" name="mapel[${mapelIndex}][nama]" class="form-control" placeholder="Nama Mapel" required>
                    </div>
                    <div class="col-1 text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-baris">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
            `;
            containerMapel.insertAdjacentHTML('beforeend', html);

            // Animasi masuk (vanilla js)
            const newRow = containerMapel.lastElementChild;
            setTimeout(() => {
                newRow.style.opacity = '1';
                // Otomatis arahkan kursor ke input pertama di baris baru
                const firstInput = newRow.querySelector('input');
                if (firstInput) firstInput.focus();
            }, 10);

            mapelIndex++;
        });

        // Shortcut Shift + Enter untuk tambah baris, Shift + Backspace untuk hapus
        containerMapel.addEventListener('keydown', function (e) {
            if (e.shiftKey && e.key === 'Enter') {
                e.preventDefault(); // Mencegah form tersubmit otomatis
                btnTambahBaris.click();
            } else if (e.shiftKey && e.key === 'Backspace') {
                const currentRow = e.target.closest('.mapel-row');
                if (currentRow) {
                    const btnHapus = currentRow.querySelector('.btn-hapus-baris');
                    // Cegah hapus jika tombol disable (baris pertama)
                    if (btnHapus && !btnHapus.disabled) {
                        e.preventDefault(); // Mencegah teks terhapus secara berlebihan
                        // Pindahkan fokus ke input baris sebelumnya jika ada
                        const prevRow = currentRow.previousElementSibling;
                        if (prevRow && prevRow.classList.contains('mapel-row')) {
                            const prevInput = prevRow.querySelector('input');
                            if (prevInput) prevInput.focus();
                        }

                        btnHapus.click(); // Trigger hapus & animasinya
                    }
                }
            }
        });

        // Hapus baris mapel (delegated event)
        containerMapel.addEventListener('click', function (e) {
            const btnHapus = e.target.closest('.btn-hapus-baris');
            if (btnHapus) {
                const row = btnHapus.closest('.mapel-row');
                row.style.opacity = '0';
                setTimeout(() => {
                    row.remove();
                }, 200);
            }
        });

        // Reset dinamis baris saat modal ditutup
        const modalTambahMapel = document.getElementById('modalTambahMapel');
        if (modalTambahMapel) {
            modalTambahMapel.addEventListener('hidden.bs.modal', function () {
                const rows = containerMapel.querySelectorAll('.mapel-row');
                // Hapus semua baris kecuali index 0 (baris pertama)
                for (let i = 1; i < rows.length; i++) {
                    rows[i].remove();
                }
                mapelIndex = 1; // Reset counter

                // Reset isi inputan form kembali ke awal
                const form = modalTambahMapel.querySelector('form');
                if (form) form.reset();
            });
        }
    }

    // Riwayat Kelas: Autocomplete cari nama siswa di modal tambah penempatan
    var rkInput = document.getElementById('rkCariNama');
    if (rkInput) {
        var rkTimer = null;
        var rkBox = document.getElementById('rkSuggestions');

        rkInput.addEventListener('input', function () {
            clearTimeout(rkTimer);
            var q = this.value.trim();
            if (q.length < 2) { rkBox.classList.add('d-none'); rkBox.innerHTML = ''; return; }

            rkTimer = setTimeout(function () {
                fetch(searchSiswaUrl + '&q=' + encodeURIComponent(q))
                    .then(r => r.json())
                    .then(function (rows) {
                        rkBox.innerHTML = '';
                        if (!rows.length) {
                            rkBox.innerHTML = '<div class="rk-suggestion-item text-muted">Tidak ditemukan</div>';
                            rkBox.classList.remove('d-none');
                            return;
                        }
                        rows.forEach(function (s) {
                            var item = document.createElement('div');
                            item.className = 'rk-suggestion-item';
                            item.innerHTML = '<span class="sug-nama">' + escapeHtml(s.nama) + '</span>' +
                                '<br><span class="sug-nisn">NISN: ' + escapeHtml(s.nisn) + '</span>';
                            item.addEventListener('click', function () {
                                rkInput.value = s.nama;
                                rkBox.classList.add('d-none');
                                rkSelectSiswa(s);
                            });
                            rkBox.appendChild(item);
                        });
                        rkBox.classList.remove('d-none');
                    });
            }, 300);
        });

        document.addEventListener('click', function (e) {
            if (!rkInput.contains(e.target) && !rkBox.contains(e.target)) {
                rkBox.classList.add('d-none');
            }
        });

        // Reset modal saat ditutup
        var modalTambahRiwayat = document.getElementById('modalTambahRiwayat');
        if (modalTambahRiwayat) {
            modalTambahRiwayat.addEventListener('hidden.bs.modal', function () {
                rkInput.value = '';
                rkBox.innerHTML = '';
                rkBox.classList.add('d-none');
                rkClearSiswa();
            });
        }
    }

});


/* ==========================================================================
   4. RK HELPER – Fungsi autocomplete pilih siswa (rkSelectSiswa / rkClearSiswa)
   ========================================================================== */
/* ==================================================
   Riwayat Kelas - helper autocomplete
   ================================================== */
function rkSelectSiswa(s) {
    var idInput = document.getElementById('rkIdSiswa');
    var nisnInput = document.getElementById('rkNisn');
    var nisInput = document.getElementById('rkNis');
    var genderInput = document.getElementById('rkGender');
    var infoRow = document.getElementById('rkInfoSiswa');
    var divider = document.getElementById('rkDivider');
    var formRow = document.getElementById('rkFormPenempatan');
    var btnSimpan = document.getElementById('rkBtnSimpan');
    var cariNama = document.getElementById('rkCariNama');

    if (!idInput) return;

    idInput.value = s.id_siswa;
    nisnInput.value = s.nisn;
    nisInput.value = s.nis;
    genderInput.value = s.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    if (cariNama && s.nama) cariNama.value = s.nama;

    infoRow.style.removeProperty('display');
    divider.style.removeProperty('display');
    formRow.style.removeProperty('display');
    if (btnSimpan) btnSimpan.disabled = false;
}

function rkClearSiswa() {
    var els = ['rkIdSiswa', 'rkNisn', 'rkNis', 'rkGender'];
    els.forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.value = '';
    });
    ['rkInfoSiswa', 'rkDivider', 'rkFormPenempatan'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.style.setProperty('display', 'none', 'important');
    });
    var btn = document.getElementById('rkBtnSimpan');
    if (btn) btn.disabled = true;
}


/* ==========================================================================
   5. NOTIFIKASI – Sistem toast popup & render waktu lokal browser
   ========================================================================== */
/* ==================================================
   Sistem Notifikasi
   ================================================== */

/**
 * Tentukan ikon & warna berdasarkan isi pesan notif.
 */
function notifStyle(message) {
    if (/dihapus/i.test(message)) return { icon: 'bi-trash3-fill', color: '#dc3545' };
    if (/import/i.test(message)) return { icon: 'bi-file-earmark-arrow-up-fill', color: '#6f42c1' };
    if (/diperbarui/i.test(message)) return { icon: 'bi-pencil-square', color: '#fd7e14' };
    return { icon: 'bi-person-plus-fill', color: '#198754' };
}

/**
 * Format Unix timestamp ke "HH:mm" pakai timezone lokal browser.
 * ts = Unix timestamp (detik).
 */
function fmtJam(ts) {
    if (!ts) return '';
    return new Date(ts * 1000).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    });
}

/**
 * Render semua elemen .notif-time-exact[data-ts] dengan jam lokal browser.
 * Dipanggil sekali saat DOM siap.
 */
function renderNotifTimes() {
    document.querySelectorAll('.notif-time-exact[data-ts]').forEach(function (el) {
        const ts = parseInt(el.dataset.ts, 10);
        if (ts) el.textContent = fmtJam(ts);
    });
}

/**
 * Tampilkan toast popup di pojok kanan bawah.
 * Auto-dismiss setelah 4 detik.
 */
function showToast(message, time, ts) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const style = notifStyle(message);

    // Format jam pakai timezone lokal browser (ts = Unix timestamp detik)
    const jam = ts ? fmtJam(ts) : '';

    const toast = document.createElement('div');
    toast.className = 'notif-toast';
    toast.style.setProperty('--toast-color', style.color);
    toast.innerHTML = `
        <span class="notif-toast-icon"><i class="bi ${style.icon}"></i></span>
        <div class="notif-toast-body">
            <div class="notif-toast-msg">${escapeHtml(message)}</div>
            ${jam ? `<div class="notif-toast-time">Hari ini pukul ${jam}</div>` : ''}
        </div>
        <button class="notif-toast-close" title="Tutup">&times;</button>
    `;

    container.appendChild(toast);

    // Tombol close manual
    toast.querySelector('.notif-toast-close').addEventListener('click', () => dismissToast(toast));

    // Auto-close setelah 4 detik
    setTimeout(() => dismissToast(toast), 4000);
}

function dismissToast(toast) {
    if (toast.classList.contains('hide')) return;
    toast.classList.add('hide');
    toast.addEventListener('animationend', () => toast.remove(), { once: true });
}

/**
 * Escape HTML untuk konten dinamis.
 */
function escapeHtml(str) {
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}


/* ==========================================================================
   6. NOTIF DISMISS – Hapus satu / semua notif dari panel dropdown
   ========================================================================== */
/* ==================================================
   Dismiss satu notif dari dropdown (tombol x)
   ================================================== */
document.addEventListener('DOMContentLoaded', function () {
    const notifList = document.getElementById('notifList');
    if (!notifList) return;

    notifList.addEventListener('click', function (e) {
        const btn = e.target.closest('.notif-dismiss');
        if (!btn) return;

        e.stopPropagation(); // Jangan tutup dropdown

        const index = parseInt(btn.dataset.index, 10);
        const item = btn.closest('.notif-item');

        // Kirim AJAX ke server untuk hapus dari sesi
        fetch(dismissUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ index }),
        })
            .then(r => r.json())
            .then(data => {
                if (!data.ok) return;

                // Animasi keluar lalu hapus dari DOM
                item.style.transition = 'opacity 0.2s, transform 0.2s';
                item.style.opacity = '0';
                item.style.transform = 'translateX(20px)';
                setTimeout(() => {
                    item.remove();

                    // Update badge
                    updateNotifBadge(data.remaining);

                    // Jika sudah kosong, tampilkan state empty
                    if (data.remaining === 0) {
                        notifList.innerHTML = `
                        <div class="notif-empty">
                            <i class="bi bi-bell-slash"></i>
                            <span>Tidak ada notifikasi</span>
                        </div>`;
                    }
                }, 200);
            })
            .catch(() => {
                // Fallback: hapus dari DOM saja
                item.remove();
            });
    });

    // Delete All Notification
    const btnDeleteAll = document.getElementById('btnDeleteAllNotif');
    if (btnDeleteAll) {
        btnDeleteAll.addEventListener('click', function (e) {
            e.stopPropagation();

            fetch(clearAllUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            })
                .then(r => r.json())
                .then(data => {
                    if (!data.ok) return;

                    // Animasi keluar untuk semua item
                    const items = notifList.querySelectorAll('.notif-item');
                    items.forEach(item => {
                        item.style.transition = 'opacity 0.2s, transform 0.2s';
                        item.style.opacity = '0';
                        item.style.transform = 'translateX(20px)';
                    });

                    setTimeout(() => {
                        updateNotifBadge(0);
                        notifList.innerHTML = `
                        <div class="notif-empty">
                            <i class="bi bi-bell-slash"></i>
                            <span>Tidak ada notifikasi</span>
                        </div>`;
                        btnDeleteAll.remove(); // Hapus tombol delete all
                    }, 200);
                });
        });
    }
});

/**
 * Update badge angka di ikon lonceng + header dropdown.
 */
function updateNotifBadge(count) {
    const badge = document.querySelector('.notif-badge');
    const countBadge = document.querySelector('.notif-count-badge');

    if (count <= 0) {
        if (badge) badge.remove();
        if (countBadge) countBadge.remove();
    } else {
        const display = count > 9 ? '9+' : count;
        if (badge) badge.textContent = display;
        if (countBadge) countBadge.textContent = count;
    }
}

/* ==================================================
   Trigger toast otomatis saat ada notif_new dari sesi
   ================================================== */
document.addEventListener('DOMContentLoaded', function () {
    // Render jam lokal di semua item dropdown
    renderNotifTimes();

    // Tampilkan toast untuk notif terbaru
    if (typeof window._newNotif !== 'undefined' && window._newNotif) {
        showToast(window._newNotif.message, window._newNotif.time, window._newNotif.ts);
    }
});


/* ==========================================================================
   7. NILAI MODAL – Autocomplete siswa & form dinamis (Mapel, Ekskul, Prestasi)
   ========================================================================== */
/* ==================================================
   MODAL TAMBAH NILAI (Autocomplete & Dynamic Forms)
   ================================================== */
document.addEventListener('DOMContentLoaded', function () {
    const nilaiCariNama = document.getElementById('nilaiCariNama');
    if (!nilaiCariNama) return;

    const nilaiSuggestions = document.getElementById('nilaiSuggestions');
    const nilaiInfoSiswa = document.getElementById('nilaiInfoSiswa');
    const nilaiFormDetails = document.getElementById('nilaiFormDetails');
    const nilaiPilihRiwayat = document.getElementById('nilaiPilihRiwayat');
    const btnSimpanNilai = document.getElementById('btnSimpanNilai');

    // Autocomplete Search
    let debounceTimer;
    nilaiCariNama.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const term = this.value.trim();
        if (term.length < 2) {
            nilaiSuggestions.classList.add('d-none');
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`${searchSiswaUrl}&q=${encodeURIComponent(term)}`)
                .then(r => r.json())
                .then(data => {
                    nilaiSuggestions.innerHTML = '';
                    if (data.length === 0) {
                        nilaiSuggestions.innerHTML = '<div class="p-2 text-muted small">Siswa tidak ditemukan</div>';
                    } else {
                        data.forEach(s => {
                            const div = document.createElement('div');
                            div.className = 'rk-suggestion-item';
                            div.innerHTML = `<span class="sug-nama">${s.nama}</span><br><span class="sug-nisn">NISN: ${s.nisn}</span>`;
                            div.addEventListener('click', () => nilaiPilihSiswa(s));
                            nilaiSuggestions.appendChild(div);
                        });
                    }
                    nilaiSuggestions.classList.remove('d-none');
                });
        }, 300);
    });

    document.addEventListener('click', function (e) {
        if (!nilaiCariNama.contains(e.target) && !nilaiSuggestions.contains(e.target)) {
            nilaiSuggestions.classList.add('d-none');
        }
    });

    function nilaiPilihSiswa(s) {
        nilaiCariNama.value = s.nama;
        document.getElementById('nilaiIdSiswa').value = s.id_siswa;
        document.getElementById('nilaiNisn').value = s.nisn;
        document.getElementById('nilaiNis').value = s.nis;
        document.getElementById('nilaiGender').value = s.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
        nilaiSuggestions.classList.add('d-none');
        nilaiInfoSiswa.style.display = 'flex';
        nilaiFormDetails.style.display = 'none';
        btnSimpanNilai.disabled = true;

        nilaiPilihRiwayat.innerHTML = '<option value="" selected disabled>-- Sedang memuat... --</option>';
        nilaiPilihRiwayat.disabled = true;

        fetch(`${getRiwayatUrl}&id_siswa=${s.id_siswa}`)
            .then(r => r.json())
            .then(data => {
                if (data.length === 0) {
                    nilaiPilihRiwayat.innerHTML = '<option value="" selected disabled>Belum ada riwayat kelas</option>';
                } else {
                    nilaiPilihRiwayat.innerHTML = '<option value="" selected disabled>-- Pilih Tahun & Kelas --</option>';
                    data.forEach(rk => {
                        const opt = document.createElement('option');
                        opt.value = rk.id_riwayat;
                        const smtStr = rk.semester % 2 === 0 ? 'Genap' : 'Ganjil';
                        opt.textContent = `${rk.tahun_ajaran} - ${rk.kelas} - ${smtStr}`;
                        nilaiPilihRiwayat.appendChild(opt);
                    });
                    nilaiPilihRiwayat.disabled = false;
                }
            });
    }

    nilaiPilihRiwayat.addEventListener('change', function () {
        const idRiwayat = this.value;
        if (!idRiwayat) return;

        nilaiFormDetails.style.display = 'block';
        btnSimpanNilai.disabled = false;

        const mapelContainer = document.getElementById('nilaiMapelContainer');
        mapelContainer.innerHTML = '<tr><td colspan="3" class="text-center text-muted"><div class="spinner-border spinner-border-sm me-2"></div>Memuat...</td></tr>';

        fetch(`${getMapelUrl}&id_riwayat=${idRiwayat}`)
            .then(r => r.json())
            .then(data => {
                mapelContainer.innerHTML = '';
                if (data.mapel.length === 0) {
                    mapelContainer.innerHTML = '<tr><td colspan="3" class="text-center text-muted">Belum ada mata pelajaran untuk kelas ini.</td></tr>';
                } else {
                    data.mapel.forEach((m, idx) => {
                        const val = data.nilai[m.id_mapel] !== undefined ? data.nilai[m.id_mapel] : '';
                        const html = `
                            <tr>
                                <td class="text-center text-muted">${idx + 1}</td>
                                <td>
                                    <span class="fw-medium">${m.nama_mapel}</span>
                                    <input type="hidden" name="nilai[${idx}][id_mapel]" value="${m.id_mapel}">
                                </td>
                                <td>
                                    <input type="number" name="nilai[${idx}][nilai_angka]" class="form-control text-center" 
                                           step="any" min="0" max="100" placeholder="0-100" value="${val}">
                                </td>
                            </tr>
                        `;
                        mapelContainer.insertAdjacentHTML('beforeend', html);
                    });
                }

                document.getElementById('absenSakit').value = data.absen.sakit || '';
                document.getElementById('absenIzin').value = data.absen.izin || '';
                document.getElementById('absenAlpa').value = data.absen.alpa || '';

                const eksContainer = document.getElementById('ekskulContainer');
                eksContainer.innerHTML = '';
                if (data.ekskul && data.ekskul.length > 0) {
                    data.ekskul.forEach(eks => addEkskulRow(eks.nama_ekskul, eks.predikat));
                }

                const presContainer = document.getElementById('prestasiContainer');
                presContainer.innerHTML = '';
                if (data.prestasi && data.prestasi.length > 0) {
                    data.prestasi.forEach(pres => addPrestasiRow(pres.nama_prestasi, pres.tingkat, pres.keterangan));
                }
            });
    });

    let eksIndex = 0;
    const btnTambahEkskul = document.getElementById('btnTambahEkskul');
    const ekskulContainer = document.getElementById('ekskulContainer');

    function addEkskulRow(nama = '', predikat = '') {
        const html = `
            <div class="row g-1 mb-2 align-items-center eks-row">
                <div class="col-7">
                    <input type="text" name="ekskul[${eksIndex}][nama]" class="form-control form-control-sm" placeholder="Nama ekskul..." value="${nama}">
                </div>
                <div class="col-4">
                    <select name="ekskul[${eksIndex}][predikat]" class="form-select form-select-sm px-1">
                        <option value="">Predikat</option>
                        <option value="SB" ${predikat === 'SB' ? 'selected' : ''}>Sangat Baik</option>
                        <option value="B" ${predikat === 'B' ? 'selected' : ''}>Baik</option>
                        <option value="C" ${predikat === 'C' ? 'selected' : ''}>Cukup</option>
                        <option value="K" ${predikat === 'K' ? 'selected' : ''}>Kurang</option>
                    </select>
                </div>
                <div class="col-1 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger p-0 d-flex align-items-center justify-content-center w-100 h-100" style="min-height: 31px;" onclick="this.closest('.eks-row').remove()">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>
        `;
        ekskulContainer.insertAdjacentHTML('beforeend', html);
        eksIndex++;
    }
    if (btnTambahEkskul) {
        btnTambahEkskul.addEventListener('click', () => addEkskulRow());
    }

    let presIndex = 0;
    const btnTambahPrestasi = document.getElementById('btnTambahPrestasi');
    const prestasiContainer = document.getElementById('prestasiContainer');

    function addPrestasiRow(nama = '', tingkat = '', ket = '') {
        const html = `
            <div class="row g-1 mb-2 align-items-center pres-row">
                <div class="col-7">
                    <input type="text" name="prestasi[${presIndex}][nama]" class="form-control form-control-sm" placeholder="Nama prestasi..." value="${nama}">
                </div>
                <div class="col-4">
                    <select name="prestasi[${presIndex}][tingkat]" class="form-select form-select-sm px-1">
                        <option value="">Tingkat</option>
                        <option value="Internasional" ${tingkat === 'Internasional' ? 'selected' : ''}>Internasional</option>
                        <option value="Nasional" ${tingkat === 'Nasional' ? 'selected' : ''}>Nasional</option>
                        <option value="Provinsi" ${tingkat === 'Provinsi' ? 'selected' : ''}>Provinsi</option>
                        <option value="Kabupaten/Kota" ${tingkat === 'Kabupaten/Kota' ? 'selected' : ''}>Kabupaten/Kota</option>
                        <option value="Sekolah" ${tingkat === 'Sekolah' ? 'selected' : ''}>Sekolah</option>
                    </select>
                </div>
                <div class="col-1 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger p-0 d-flex align-items-center justify-content-center w-100 h-100" style="min-height: 31px;" onclick="this.closest('.pres-row').remove()">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>
        `;
        prestasiContainer.insertAdjacentHTML('beforeend', html);
        presIndex++;
    }
    if (btnTambahPrestasi) {
        btnTambahPrestasi.addEventListener('click', () => addPrestasiRow());
    }

    const modalTambahNilai = document.getElementById('modalTambahNilai');
    if (modalTambahNilai) {
        modalTambahNilai.addEventListener('hidden.bs.modal', function () {
            document.getElementById('formTambahNilai').reset();
            nilaiCariNama.value = '';
            nilaiInfoSiswa.style.display = 'none';
            nilaiFormDetails.style.display = 'none';
            btnSimpanNilai.disabled = true;
            ekskulContainer.innerHTML = '';
            prestasiContainer.innerHTML = '';
            document.getElementById('nilaiMapelContainer').innerHTML = '';
        });
    }
});
