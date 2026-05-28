/* ==========================================================================
   JS Khusus: Halaman Laporan (Views/laporan/index.php)
   ========================================================================== */

/* Buka modal konfirmasi hapus laporan satuan */
function confirmDeleteLaporan(id) {
    const modal = new bootstrap.Modal(document.getElementById('deleteLaporanModal'));
    const form = document.getElementById('formHapusLaporanSatuan');
    if (form) {
        form.action = laporanDeleteUrl;
        document.getElementById('hapusIdLaporanSatuan').value = id;
    }
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    const btnModeHapus    = document.getElementById('btnModeHapus');
    const btnCancelBatch  = document.getElementById('btnCancelBatchLaporan');
    const btnConfirmBatch = document.getElementById('btnConfirmBatchLaporan');
    const batchActionBar  = document.getElementById('batchActionBarLaporan');
    const colCheckboxes   = document.querySelectorAll('.col-checkbox');
    const selectAll       = document.getElementById('selectAllLaporan');
    const itemCheckboxes  = document.querySelectorAll('.row-checkbox-laporan');
    const selectedCount   = document.getElementById('selectedCountLaporan');

    /* Aktifkan/nonaktifkan mode hapus batch */
    function toggleModeHapus(active) {
        if (active) {
            colCheckboxes.forEach(el => el.classList.remove('d-none'));
            btnModeHapus.classList.add('d-none');
            batchActionBar.classList.remove('d-none');
            document.querySelectorAll('.col-aksi a, .col-aksi button').forEach(el => el.classList.add('disabled', 'opacity-50'));
            updateCount();
        } else {
            colCheckboxes.forEach(el => el.classList.add('d-none'));
            btnModeHapus.classList.remove('d-none');
            batchActionBar.classList.add('d-none');
            if (selectAll) selectAll.checked = false;
            itemCheckboxes.forEach(cb => cb.checked = false);
            updateCount();
            document.querySelectorAll('.col-aksi a, .col-aksi button').forEach(el => el.classList.remove('disabled', 'opacity-50'));
        }
    }

    if (btnModeHapus) btnModeHapus.addEventListener('click', () => toggleModeHapus(true));
    if (btnCancelBatch) btnCancelBatch.addEventListener('click', () => toggleModeHapus(false));

    /* Hitung dan tampilkan jumlah laporan yang dicentang */
    function updateCount() {
        const checked = document.querySelectorAll('.row-checkbox-laporan:checked').length;
        if (selectedCount) selectedCount.textContent = checked;
        if (btnConfirmBatch) btnConfirmBatch.disabled = checked === 0;
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            itemCheckboxes.forEach(cb => cb.checked = this.checked);
            updateCount();
        });

        itemCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const allChecked = document.querySelectorAll('.row-checkbox-laporan:checked').length === itemCheckboxes.length && itemCheckboxes.length > 0;
                selectAll.checked = allChecked;
                updateCount();
            });
        });
    }

    /* Kumpulkan id yang dicentang lalu tampilkan modal konfirmasi hapus batch */
    if (btnConfirmBatch) {
        btnConfirmBatch.addEventListener('click', function() {
            const checked = document.querySelectorAll('.row-checkbox-laporan:checked');
            if (checked.length === 0) return;

            const form = document.getElementById('batchDeleteLaporanForm');
            form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());

            checked.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                form.appendChild(input);
            });

            const modal = new bootstrap.Modal(document.getElementById('batchDeleteLaporanModal'));
            modal.show();
        });
    }
});
