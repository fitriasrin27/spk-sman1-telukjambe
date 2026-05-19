/* ==========================================================================
   JS Khusus: Halaman Kriteria & Bobot (Views/kriteria/index.php)
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function() {
    /* Isi modal Edit Kriteria dari data-* tombol */
    const editButtons = document.querySelectorAll('.btn-kriteria-edit');
    const editBobotInput = document.getElementById('editBobotKriteria');

    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editIdKriteria').value   = this.dataset.id;
            document.getElementById('editKodeKriteria').value = this.dataset.kode;
            document.getElementById('editNamaKriteria').value = this.dataset.nama;

            const bobotLama   = parseFloat(this.dataset.bobot) || 0;
            const maxBobotBaru = SISA_BOBOT + bobotLama;

            if (editBobotInput) {
                editBobotInput.value = bobotLama.toFixed(2);
                editBobotInput.setAttribute('max', maxBobotBaru.toFixed(2));
                editBobotInput.setAttribute('title', 'Maksimal bobot yang dapat diisi adalah ' + maxBobotBaru.toFixed(2));
            }

            const attrSelect = document.getElementById('editAtributKriteria');
            const targetAttr = this.dataset.atribut.toLowerCase();
            for (let i = 0; i < attrSelect.options.length; i++) {
                if (attrSelect.options[i].value.toLowerCase() === targetAttr) {
                    attrSelect.selectedIndex = i;
                    break;
                }
            }
        });
    });

    /* Isi modal Hapus Kriteria dari data-* tombol */
    const hapusButtons = document.querySelectorAll('.btn-kriteria-hapus');
    hapusButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('hapusIdKriteria').value = this.dataset.id;
            document.getElementById('hapusNamaKriteria').textContent = this.dataset.nama;
        });
    });
});
