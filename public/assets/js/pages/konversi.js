/* ==========================================================================
   JS Khusus: Halaman Konversi Nilai (Views/konversi/index.php)
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function() {
    /* Reset semua form saat modal ditutup */
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function() {
            const form = this.querySelector('form');
            if (form) form.reset();
        });
    });

    /* Isi modal Edit Konversi dari data-* tombol */
    const editButtons = document.querySelectorAll('.btn-konversi-edit');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editIdKonversi').value      = this.dataset.id;
            document.getElementById('editKriteria').value        = this.dataset.kriteria;
            document.getElementById('editNilaiAsli').value       = this.dataset.asli;
            document.getElementById('editNilaiKonversi').value   = this.dataset.konversi;
        });
    });

    /* Isi modal Hapus Konversi dari data-* tombol */
    const hapusButtons = document.querySelectorAll('.btn-konversi-hapus');
    hapusButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('hapusIdKonversi').value = this.dataset.id;
            document.getElementById('hapusNamaKonversi').textContent = this.dataset.nama;
            document.getElementById('formHapusKonversi').action = this.dataset.deleteUrl + '?id=' + this.dataset.id;
        });
    });
});
