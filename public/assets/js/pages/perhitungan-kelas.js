/**
 * ==========================================================================
 * Modul Perhitungan Kelas JS
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

    // 2. Logic Batch Mode Hapus (Index Kelas)
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

                const modalHapusBatch = new bootstrap.Modal(document.getElementById('modalHapusBatch'));
                const batchDeleteCount = document.getElementById('batchDeleteCount');
                if (batchDeleteCount) batchDeleteCount.textContent = ids.length;
                
                const form = document.getElementById('formHapusBatch');
                if (form) {
                    form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
                    ids.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });
                }

                modalHapusBatch.show();
            });
        }
    }
});

// 3. Fungsi Global untuk Trigger Modul Hapus/Modal Konfirmasi
window.konfirmasiHapus = function(id, kelas, tanggal) {
    const hapusId = document.getElementById('hapusId');
    const hapusInfo = document.getElementById('hapusInfo');
    const modalEl = document.getElementById('modalHapus');

    if (hapusId) hapusId.value = id;
    if (hapusInfo) hapusInfo.textContent = 'Kelas ' + kelas + ' · ' + tanggal;
    if (modalEl) {
        new bootstrap.Modal(modalEl).show();
    }
};
