document.addEventListener('DOMContentLoaded', function() {
    // =========================================================
    // Search Bar (Debounce)
    // =========================================================
    const searchInput = document.getElementById('searchQueryInput');
    if (searchInput) {
        let timeout = null;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });

        // Set cursor to end
        if (searchInput.value !== '') {
            const val = searchInput.value;
            searchInput.value = '';
            searchInput.value = val;
            searchInput.focus();
        }
    }

    // =========================================================
    // Mode Hapus Batch
    // =========================================================
    const btnModeHapus      = document.getElementById('btnModeHapus');
    const btnBatalHapus     = document.getElementById('btnBatalHapus');
    const toolbarHapus      = document.getElementById('toolbarHapusSiswa');
    const colCheckboxes     = document.querySelectorAll('.col-checkbox');
    const colAksi           = document.querySelectorAll('.col-aksi');
    const checkAll          = document.getElementById('selectAllSiswa');
    const checkRows         = document.querySelectorAll('.chk-siswa');
    const btnKonfirmasiBatch = document.getElementById('btnKonfirmasiHapusBatch');
    const txtJumlah         = document.getElementById('txtJumlahTerpilih');

    function toggleModeHapus(active) {
        if (active) {
            btnModeHapus.classList.add('d-none');
            toolbarHapus.classList.remove('d-none');
            colCheckboxes.forEach(el => el.classList.remove('d-none'));
            // Disable action buttons while in delete mode
            document.querySelectorAll('.siswa-row .btn').forEach(btn => btn.classList.add('disabled', 'opacity-50'));
        } else {
            btnModeHapus.classList.remove('d-none');
            toolbarHapus.classList.add('d-none');
            colCheckboxes.forEach(el => el.classList.add('d-none'));
            document.querySelectorAll('.siswa-row .btn').forEach(btn => btn.classList.remove('disabled', 'opacity-50'));

            // Reset selection
            checkAll.checked = false;
            checkRows.forEach(c => c.checked = false);
            updateSelectionCount();
        }
    }

    function updateSelectionCount() {
        const count = [...checkRows].filter(c => c.checked).length;
        txtJumlah.textContent = count;
        btnKonfirmasiBatch.disabled = count === 0;

        // Sync master checkbox
        if (checkAll) {
            checkAll.checked       = count > 0 && count === checkRows.length;
            checkAll.indeterminate = count > 0 && count < checkRows.length;
        }
    }

    if (btnModeHapus)  btnModeHapus.addEventListener('click',  () => toggleModeHapus(true));
    if (btnBatalHapus) btnBatalHapus.addEventListener('click', () => toggleModeHapus(false));

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkRows.forEach(c => c.checked = this.checked);
            updateSelectionCount();
        });
    }

    checkRows.forEach(c => {
        c.addEventListener('change', updateSelectionCount);
    });

    if (btnKonfirmasiBatch) {
        btnKonfirmasiBatch.addEventListener('click', function() {
            const ids = [...checkRows].filter(c => c.checked).map(c => c.value);
            if (ids.length > 0) {
                if (typeof window.konfirmasiHapusBatchSiswa === 'function') {
                    window.konfirmasiHapusBatchSiswa(ids);
                }
            }
        });
    }
});

// =========================================================
// Konfirmasi Hapus Batch (dipanggil dari toolbar)
// =========================================================
window.konfirmasiHapusBatchSiswa = function(ids) {
    const modal = new bootstrap.Modal(document.getElementById('modalHapusBatchSiswa'));
    document.getElementById('batchCountSiswa').textContent = ids.length;

    const container = document.getElementById('batchIdsContainerSiswa');
    container.innerHTML = '';
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        container.appendChild(input);
    });

    modal.show();
};

