<!-- MODAL KONFIRMASI HAPUS -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <!-- Modal Dialog -->
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <!-- Modal Content -->
        <div class="modal-content border-0 shadow">
            <!-- Modal Body -->
            <div class="modal-body text-center py-4">
                <!-- Icon -->
                <div class="text-danger mb-3">
                    <i class="bi bi-exclamation-circle" style="font-size: 3rem;"></i>
                </div>
                <!-- Title -->
                <h5 class="fw-bold mb-1">Hapus Akun?</h5>
                <!-- Description -->
                <p class="text-muted small mb-4">Akun <span id="deleteTargetName" class="fw-bold text-dark"></span> akan dihapus permanen.</p>
                <!-- Modal Footer -->
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <a id="btnConfirmDelete" href="#" class="btn btn-danger px-4">Ya, Hapus</a>
                </div>
            </div>
        </div>
    </div>
</div>
