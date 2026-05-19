/* ==========================================================================
   JS Khusus: Halaman Mata Pelajaran (Views/mata-pelajaran/index.php)
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function() {
    /* Autocomplete search — submit otomatis 500ms setelah berhenti mengetik */
    const searchInput = document.getElementById('searchQueryInput');
    if (searchInput) {
        let timeout = null;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => { this.form.submit(); }, 500);
        });
        if (searchInput.value !== '') {
            const val = searchInput.value;
            searchInput.value = ''; searchInput.value = val; searchInput.focus();
        }
    }

    const btnModeHapus   = document.getElementById('btnModeHapus');
    const colCheckboxes  = document.querySelectorAll('.col-checkbox');
    const selectAll      = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const batchToolbar   = document.getElementById('batchToolbar');
    const selectedCount  = document.getElementById('selectedCount');
    const btnGroupMain   = document.querySelector('.btn-group-main');
    const cancelBatch    = document.getElementById('cancelBatch');
    const btnHapusBatch  = document.getElementById('btnHapusBatch');

    function updateSelection() {
        const checked = document.querySelectorAll('.item-checkbox:checked');
        if (selectedCount) selectedCount.textContent = checked.length;
        if (btnHapusBatch) btnHapusBatch.disabled = checked.length === 0;
        if (selectAll) selectAll.checked = (checked.length === itemCheckboxes.length && itemCheckboxes.length > 0);
    }

    function toggleModeHapus(active) {
        if (active) {
            if (btnModeHapus) btnModeHapus.classList.add('d-none');
            colCheckboxes.forEach(el => el.classList.remove('d-none'));
            if (btnGroupMain) { btnGroupMain.classList.add('opacity-50'); btnGroupMain.style.pointerEvents = 'none'; }
            if (batchToolbar) batchToolbar.classList.remove('d-none');
            document.querySelectorAll('tbody tr .btn').forEach(btn => btn.classList.add('disabled', 'opacity-50'));
            updateSelection();
        } else {
            if (btnModeHapus) btnModeHapus.classList.remove('d-none');
            colCheckboxes.forEach(el => el.classList.add('d-none'));
            if (btnGroupMain) { btnGroupMain.classList.remove('opacity-50'); btnGroupMain.style.pointerEvents = 'auto'; }
            if (batchToolbar) batchToolbar.classList.add('d-none');
            document.querySelectorAll('tbody tr .btn').forEach(btn => btn.classList.remove('disabled', 'opacity-50'));
            if (selectAll) { selectAll.checked = false; }
            itemCheckboxes.forEach(cb => cb.checked = false);
            updateSelection();
        }
    }

    if (btnModeHapus) btnModeHapus.addEventListener('click', () => toggleModeHapus(true));
    if (cancelBatch) cancelBatch.addEventListener('click', () => toggleModeHapus(false));
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            itemCheckboxes.forEach(cb => cb.checked = this.checked);
            updateSelection();
        });
    }
    itemCheckboxes.forEach(cb => { cb.addEventListener('change', updateSelection); });

    /* Klik hapus batch — kumpulkan id, isi form, tampilkan modal */
    if (btnHapusBatch) {
        btnHapusBatch.addEventListener('click', function() {
            const checked = document.querySelectorAll('.item-checkbox:checked');
            const ids = Array.from(checked).map(cb => cb.value);
            if (ids.length === 0) return;

            const modalHapusBatch = new bootstrap.Modal(document.getElementById('modalHapusBatch'));
            const batchCount = document.getElementById('batchDeleteCount');
            if (batchCount) batchCount.textContent = ids.length;

            const form = document.getElementById('formHapusBatch');
            form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
            ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden'; input.name = 'ids[]'; input.value = id;
                form.appendChild(input);
            });
            modalHapusBatch.show();
        });
    }
});
