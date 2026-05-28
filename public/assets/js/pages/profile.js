/* ==========================================================================
   JS Khusus: Halaman Profil (Views/profile/index.php)
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function () {
    const inputFoto = document.getElementById('inputFoto');
    const modalCropEl = document.getElementById('modalCrop');
    const imageToCrop = document.getElementById('imageToCrop');
    const btnCropSave = document.getElementById('btnCropSave');
    const btnHapusFoto = document.getElementById('btnHapusFoto');
    const formHapusFoto = document.getElementById('formHapusFoto');
    const modalHapusFotoEl = document.getElementById('modalHapusFoto');
    const btnKonfirmasiHapusFoto = document.getElementById('btnKonfirmasiHapusFoto');

    let cropper = null;
    let modalCrop = null;
    let modalHapusFoto = null;

    if (modalCropEl) {
        modalCrop = new bootstrap.Modal(modalCropEl);
    }
    if (modalHapusFotoEl) {
        modalHapusFoto = new bootstrap.Modal(modalHapusFotoEl);
    }

    // Handler ketika user memilih file
    if (inputFoto) {
        inputFoto.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const maxSize = 5 * 1024 * 1024; // 5MB

                if (file.size > maxSize) {
                    alert('Ukuran file terlalu besar! Maksimal adalah 5MB.');
                    this.value = ''; // Reset input
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    imageToCrop.src = e.target.result;
                    modalCrop.show();
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Handler ketika modal shown
    if (modalCropEl) {
        modalCropEl.addEventListener('shown.bs.modal', function () {
            cropper = new Cropper(imageToCrop, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 1,
                restore: false,
                guides: false,
                center: false,
                highlight: false,
                cropBoxMovable: false,
                cropBoxResizable: false,
                toggleDragModeOnDblclick: false
            });
        });

        // Handler ketika modal hidden
        modalCropEl.addEventListener('hidden.bs.modal', function () {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            imageToCrop.src = '';
            inputFoto.value = ''; // Reset input file agar change event terpicu lagi untuk file yang sama
        });
    }

    // Event listener untuk tombol kontrol cropper
    document.getElementById('btnZoomIn')?.addEventListener('click', () => {
        cropper?.zoom(0.1);
    });

    document.getElementById('btnZoomOut')?.addEventListener('click', () => {
        cropper?.zoom(-0.1);
    });

    document.getElementById('btnRotateLeft')?.addEventListener('click', () => {
        cropper?.rotate(-90);
    });

    document.getElementById('btnRotateRight')?.addEventListener('click', () => {
        cropper?.rotate(90);
    });

    // Event listener tombol simpan potongan
    if (btnCropSave) {
        btnCropSave.addEventListener('click', function () {
            if (!cropper) return;

            // Dapatkan canvas pemotongan beresolusi persegi optimal 300x300 piksel
            const canvas = cropper.getCroppedCanvas({
                width: 300,
                height: 300,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high'
            });

            // Ubah canvas ke format Base64 Data URL (JPEG, kualitas 90%)
            const croppedDataUrl = canvas.toDataURL('image/jpeg', 0.9);

            const croppedImageInput = document.getElementById('croppedImageInput');
            const formUpdateFoto = document.getElementById('formUpdateFoto');

            if (croppedImageInput && formUpdateFoto) {
                croppedImageInput.value = croppedDataUrl;

                // Disable tombol agar tidak diklik berkali-kali
                btnCropSave.disabled = true;
                btnCropSave.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...';

                // Submit Form
                formUpdateFoto.submit();
            }
        });
    }

    // Handler hapus foto profil
    if (btnHapusFoto && modalHapusFoto) {
        btnHapusFoto.addEventListener('click', function () {
            modalHapusFoto.show();
        });
    }

    if (btnKonfirmasiHapusFoto && formHapusFoto) {
        btnKonfirmasiHapusFoto.addEventListener('click', function () {
            // Disable button to prevent double submit
            btnKonfirmasiHapusFoto.disabled = true;
            btnKonfirmasiHapusFoto.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menghapus...';
            formHapusFoto.submit();
        });
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
});
