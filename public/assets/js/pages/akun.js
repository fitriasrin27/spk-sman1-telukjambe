/* ==========================================================================
   JS Khusus: Halaman Kelola Akun (Views/akun/index.php)
   ========================================================================== */

const modalAkun = new bootstrap.Modal(document.getElementById('modalAkun'));
const modalEditAkun = new bootstrap.Modal(document.getElementById('modalEditAkun'));
const formAkun = document.getElementById('formAkun');
const modalTitle = document.getElementById('modalTitle');

/* Reset form saat buka modal Tambah Akun */
function resetForm() {
    formAkun.action = akunStoreUrl;
    modalTitle.innerHTML = '<i class="bi bi-person-plus-fill me-2"></i>Tambah Akun Baru';
    formAkun.reset();
    document.getElementById('userId').value = '';
    document.getElementById('userPosisi').value = '';
    document.getElementById('password').required = true;
    document.getElementById('password').type = 'password';
    const icon = document.querySelector('#togglePassword i');
    if (icon) {
        icon.classList.add('bi-eye');
        icon.classList.remove('bi-eye-slash');
    }
}

/* Isi form modal Edit Akun dari data-* tombol */
function populateEditModal(btn) {
    document.getElementById('editUserId').value = btn.dataset.id;
    document.getElementById('editUserNama').value = btn.dataset.nama;
    document.getElementById('editUserPosisi').value = btn.dataset.posisi || '';
    document.getElementById('editUserUsername').value = btn.dataset.username;
    document.getElementById('editUserRole').value = btn.dataset.role;
    document.getElementById('editPassword').value = '';
    document.getElementById('currentPasswordDisplay').textContent = btn.dataset.password || '(Belum tersimpan)';
}

/* Buka modal konfirmasi hapus akun */
function confirmDelete(id, nama) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    document.getElementById('deleteTargetName').textContent = nama;
    document.getElementById('btnConfirmDelete').href = akunDeleteUrl + '&id=' + id;
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    /* Autocomplete search akun: submit form otomatis saat mengetik */
    const searchInput = document.getElementById('searchQueryInput');
    if (searchInput) {
        if (searchInput.value !== '') {
            const val = searchInput.value;
            searchInput.value = '';
            searchInput.value = val;
            searchInput.focus();
        }

        let timeout = null;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                const form = document.getElementById('formFilterAkun');
                if (form) form.submit();
            }, 100);
        });
    }

    /* Toggle show/hide password di modal Tambah */
    const btnToggle = document.getElementById('togglePassword');
    const inputPass = document.getElementById('password');
    if (btnToggle && inputPass) {
        btnToggle.addEventListener('click', function() {
            const isPassword = inputPass.getAttribute('type') === 'password';
            inputPass.setAttribute('type', isPassword ? 'text' : 'password');
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.toggle('bi-eye', !isPassword);
                icon.classList.toggle('bi-eye-slash', isPassword);
            }
        });
    }

    /* Toggle show/hide password di modal Edit */
    const btnToggleEdit = document.getElementById('toggleEditPassword');
    const inputPassEdit = document.getElementById('editPassword');
    if (btnToggleEdit && inputPassEdit) {
        btnToggleEdit.addEventListener('click', function() {
            const isPassword = inputPassEdit.getAttribute('type') === 'password';
            inputPassEdit.setAttribute('type', isPassword ? 'text' : 'password');
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.toggle('bi-eye', !isPassword);
                icon.classList.toggle('bi-eye-slash', isPassword);
            }
        });
    }
});
