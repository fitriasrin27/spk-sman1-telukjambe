/* ==========================================================================
   JS Khusus: Halaman Profil (Views/profile/index.php)
   ========================================================================== */

/* Preview foto baru sebelum disimpan */
function handlePreview(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const maxSize = 5 * 1024 * 1024; // 5MB

        if (file.size > maxSize) {
            alert('Ukuran file terlalu besar! Maksimal adalah 5MB.');
            input.value = ''; // Reset input
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('previewFoto');
            const initials = document.getElementById('initialsAvatar');

            preview.src = e.target.result;
            preview.classList.remove('d-none');
            if (initials) initials.classList.add('d-none');

            document.getElementById('saveFotoContainer').classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

/* Toggle show/hide password (dipakai untuk 3 field password di halaman ini) */
function makeToggle(btnId, inputId, iconId) {
    const btn = document.getElementById(btnId);
    if (!btn) return;
    btn.addEventListener('click', function () {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        const show  = input.type === 'password';
        input.type  = show ? 'text' : 'password';
        icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
}

makeToggle('toggleOldPass',     'old_password',     'iconOldPass');
makeToggle('toggleNewPass',     'new_password',     'iconNewPass');
makeToggle('toggleConfirmPass', 'confirm_password', 'iconConfirmPass');
