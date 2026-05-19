/* ==========================================================================
   JS Khusus: Modul Nilai
   Mencakup: index, edit, modal-cetak, modal-hapus-batch
   (Views/nilai/)
   ========================================================================== */


/* --------------------------------------------------------------------------
   [1] HALAMAN INDEX NILAI
   Fitur: autocomplete search, mode hapus batch
   -------------------------------------------------------------------------- */

document.addEventListener('DOMContentLoaded', function () {

    // Autocomplete search — submit form otomatis 500ms setelah berhenti mengetik
    const searchInput = document.getElementById('searchQueryInput');
    if (searchInput) {
        let timeout = null;
        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(() => { this.form.submit(); }, 500);
        });
        if (searchInput.value !== '') {
            const val = searchInput.value;
            searchInput.value = ''; searchInput.value = val; searchInput.focus();
        }
    }

    // Mode hapus batch
    const btnModeHapus       = document.getElementById('btnModeHapus');
    const btnBatalHapus      = document.getElementById('btnBatalHapusNilai');
    const toolbarHapus       = document.getElementById('toolbarHapusNilai');
    const colCheckboxes      = document.querySelectorAll('.col-checkbox');
    const checkAll           = document.getElementById('selectAllNilai');
    const checkRows          = document.querySelectorAll('.chk-nilai');
    const btnKonfirmasiBatch = document.getElementById('btnKonfirmasiHapusBatchNilai');
    const txtJumlah          = document.getElementById('txtJumlahTerpilihNilai');

    function toggleModeHapusNilai(active) {
        if (active) {
            if (btnModeHapus) btnModeHapus.classList.add('d-none');
            if (toolbarHapus) toolbarHapus.classList.remove('d-none');
            colCheckboxes.forEach(el => el.classList.remove('d-none'));
            document.querySelectorAll('.nilai-row .btn').forEach(btn => btn.classList.add('disabled', 'opacity-50'));
        } else {
            if (btnModeHapus) btnModeHapus.classList.remove('d-none');
            if (toolbarHapus) toolbarHapus.classList.add('d-none');
            colCheckboxes.forEach(el => el.classList.add('d-none'));
            document.querySelectorAll('.nilai-row .btn').forEach(btn => btn.classList.remove('disabled', 'opacity-50'));
            if (checkAll) checkAll.checked = false;
            checkRows.forEach(c => c.checked = false);
            updateSelectionCount();
        }
    }

    function updateSelectionCount() {
        const count = [...checkRows].filter(c => c.checked).length;
        if (txtJumlah) txtJumlah.textContent = count;
        if (btnKonfirmasiBatch) btnKonfirmasiBatch.disabled = count === 0;
        if (checkAll) {
            checkAll.checked       = count > 0 && count === checkRows.length;
            checkAll.indeterminate = count > 0 && count < checkRows.length;
        }
    }

    if (btnModeHapus) btnModeHapus.addEventListener('click', () => toggleModeHapusNilai(true));
    if (btnBatalHapus) btnBatalHapus.addEventListener('click', () => toggleModeHapusNilai(false));

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            checkRows.forEach(c => c.checked = this.checked);
            updateSelectionCount();
        });
    }
    checkRows.forEach(c => { c.addEventListener('change', updateSelectionCount); });

    // Klik konfirmasi hapus batch → panggil modal-hapus-batch
    if (btnKonfirmasiBatch) {
        btnKonfirmasiBatch.addEventListener('click', function () {
            const ids = [...checkRows].filter(c => c.checked).map(c => c.value);
            if (ids.length > 0 && typeof window.konfirmasiHapusBatchNilai === 'function') {
                window.konfirmasiHapusBatchNilai(ids);
            }
        });
    }

});


/* --------------------------------------------------------------------------
   [2] HALAMAN EDIT NILAI
   Fitur: hitung ulang total/rata-rata, tambah/hapus baris ekskul & prestasi
   -------------------------------------------------------------------------- */

document.addEventListener('DOMContentLoaded', function () {
    // Ambil data ekskul & prestasi dari data-attribute elemen penampung
    const dataHolder     = document.getElementById('editNilaiData');
    if (!dataHolder) return; // Keluar jika bukan halaman edit nilai

    const existingEkskul   = JSON.parse(dataHolder.dataset.ekskul   || '[]');
    const existingPrestasi = JSON.parse(dataHolder.dataset.prestasi || '[]');
    const totalEl = document.getElementById('editTotalNilai');
    const rataEl  = document.getElementById('editRataRata');
    let editEksIndex  = 0;
    let editPresIndex = 0;

    // Format angka ke format Indonesia (2 desimal)
    function formatId(value) {
        return value.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Hitung ulang Total dan Rata-rata saat nilai mapel berubah
    function recalcHasil() {
        const inputs = document.querySelectorAll('.edit-nilai-mapel');
        let total = 0;
        inputs.forEach(function (inp) {
            const v = parseFloat(String(inp.value).replace(',', '.'));
            if (!Number.isNaN(v)) { total += v; }
        });
        if (totalEl) totalEl.textContent = formatId(total);
        if (rataEl)  rataEl.textContent  = inputs.length > 0 ? formatId(total / inputs.length) : formatId(0);
    }

    // Tambah baris ekskul baru di tabel
    function addEkskulRow(nama, predikat) {
        nama     = nama     || '';
        predikat = predikat || '';
        const tbody = document.querySelector('#editEkskulTable tbody');
        if (!tbody) return;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="p-1">
                <input type="text" name="ekskul[${editEksIndex}][nama]"
                       class="nilai-input-text" placeholder="Nama ekstrakulikuler">
            </td>
            <td class="p-1">
                <select name="ekskul[${editEksIndex}][predikat]" class="nilai-select">
                    <option value="">-</option>
                    <option value="SB">SB</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="K">K</option>
                </select>
            </td>
            <td class="p-1 text-center">
                <button type="button" class="nilai-btn-hapus" title="Hapus baris">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>`;
        row.querySelector('input').value  = nama;
        row.querySelector('select').value = predikat;
        row.querySelector('button').addEventListener('click', function () { row.remove(); });
        tbody.appendChild(row);
        editEksIndex++;
    }

    // Tambah baris prestasi baru di tabel
    function addPrestasiRow(nama, tingkat) {
        nama    = nama    || '';
        tingkat = tingkat || '';
        const tbody = document.querySelector('#editPrestasiTable tbody');
        if (!tbody) return;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="p-1">
                <input type="text" name="prestasi[${editPresIndex}][nama]"
                       class="nilai-input-text" placeholder="Nama prestasi">
            </td>
            <td class="p-1">
                <select name="prestasi[${editPresIndex}][tingkat]" class="nilai-select">
                    <option value="">-</option>
                    <option value="Internasional">Internasional</option>
                    <option value="Nasional">Nasional</option>
                    <option value="Provinsi">Provinsi</option>
                    <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                    <option value="Sekolah">Sekolah</option>
                </select>
            </td>
            <td class="p-1 text-center">
                <button type="button" class="nilai-btn-hapus" title="Hapus baris">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>`;
        row.querySelector('input').value  = nama;
        row.querySelector('select').value = tingkat;
        row.querySelector('button').addEventListener('click', function () { row.remove(); });
        tbody.appendChild(row);
        editPresIndex++;
    }

    // Pasang listener hitung ulang saat nilai mapel berubah
    document.querySelectorAll('.edit-nilai-mapel').forEach(function (inp) {
        inp.addEventListener('input', recalcHasil);
    });

    // Tombol tambah baris ekskul & prestasi
    const btnTambahEkskul   = document.getElementById('btnEditTambahEkskul');
    const btnTambahPrestasi = document.getElementById('btnEditTambahPrestasi');
    if (btnTambahEkskul)   btnTambahEkskul.addEventListener('click', function () { addEkskulRow(); });
    if (btnTambahPrestasi) btnTambahPrestasi.addEventListener('click', function () { addPrestasiRow(); });

    // Isi baris dengan data yang sudah ada di database
    if (existingEkskul.length > 0) {
        existingEkskul.forEach(function (item) { addEkskulRow(item.nama_ekskul, item.predikat); });
    } else { addEkskulRow(); }

    if (existingPrestasi.length > 0) {
        existingPrestasi.forEach(function (item) { addPrestasiRow(item.nama_prestasi, item.tingkat); });
    } else { addPrestasiRow(); }

    recalcHasil();

});


/* --------------------------------------------------------------------------
   [3] MODAL CETAK LEGER PDF
   Fitur: dropdown jenis identitas, pencegahan double submit
   -------------------------------------------------------------------------- */

document.addEventListener('DOMContentLoaded', function () {
    // Keluar jika modal cetak leger tidak ada di halaman ini
    if (!document.getElementById('modalCetakLeger')) return;

    // Logika dropdown Jenis Identitas (NIP / NUPTK / NRG / NPK / Lainnya)
    const identitySelects = document.querySelectorAll('.identity-type-select');
    identitySelects.forEach(select => {
        const parent      = select.closest('.col-md-2');
        const hiddenInput = parent.querySelector('input[type="hidden"]');
        const customInput = parent.querySelector('.custom-identity-input');

        select.addEventListener('change', function () {
            if (this.value === 'CUSTOM') {
                customInput.classList.remove('d-none');
                customInput.focus();
                customInput.name = select.dataset.target;
                hiddenInput.name = '';
            } else {
                customInput.classList.add('d-none');
                customInput.value = '';
                customInput.name  = '';
                hiddenInput.name  = select.dataset.target;
                hiddenInput.value = this.value;
            }
        });
    });

    // Pencegahan double submit saat generate PDF
    const form = document.querySelector('#modalCetakLeger form');
    if (form) {
        form.addEventListener('submit', function () {
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

            setTimeout(() => {
                btn.disabled  = false;
                btn.innerHTML = '<i class="bi bi-file-earmark-pdf me-2"></i>Generate PDF';
                bootstrap.Modal.getInstance(document.getElementById('modalCetakLeger')).hide();
            }, 3000);
        });
    }

});


/* --------------------------------------------------------------------------
   [4] MODAL HAPUS BATCH NILAI
   Fitur: buka modal konfirmasi hapus batch, isi form dengan ids yang dipilih
   -------------------------------------------------------------------------- */

window.konfirmasiHapusBatchNilai = function (ids) {
    const modal = new bootstrap.Modal(document.getElementById('modalHapusBatchNilai'));
    document.getElementById('batchCountNilai').textContent = ids.length;

    const container = document.getElementById('batchIdsContainerNilai');
    container.innerHTML = '';
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'ids[]';
        input.value = id;
        container.appendChild(input);
    });

    modal.show();
};
