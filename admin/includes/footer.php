</main>

<!-- Footer -->
<footer class="main-footer">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                &copy; <?php echo date('Y'); ?> CDK Wilayah Bojonegoro. All rights reserved.
            </div>
            <div>
                <span class="text-muted">v1.0.0</span>
            </div>
        </div>
    </div>
</footer>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<!-- Summernote -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Custom scripts -->
<script>
    // Toggle sidebar
    document.getElementById('toggle-sidebar').addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('show');
        document.getElementById('content-wrapper').classList.toggle('sidebar-open');
    });

    // Initialize DataTables
    $(document).ready(function () {
        if ($.fn.DataTable) {
            $('.data-table').DataTable({
                responsive: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ entri per halaman",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                    infoFiltered: "(difilter dari _MAX_ total entri)",
                    zeroRecords: "Tidak ada data yang cocok",
                    emptyTable: "Tidak ada data dalam tabel",
                    paginate: {
                        first: "Pertama",
                        previous: "Sebelumnya",
                        next: "Selanjutnya",
                        last: "Terakhir"
                    }
                }
            });
        }

        // Initialize Summernote
        if ($.fn.summernote) {
            $('.summernote').summernote({
                height: 300,
                minHeight: 200,
                maxHeight: 500,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                placeholder: 'Tulis isi konten di sini...',
                callbacks: {
                    onImageUpload: function (files) {
                        // Custom image upload if needed
                        // This can be implemented later to match server-side upload
                    }
                }
            });
        }

        // Initialize Select2
        if ($.fn.select2) {
            $('.select2').select2({
                theme: 'bootstrap-5'
            });
        }

        // Custom file input preview
        $('.custom-file-input').on('change', function () {
            var fileName = $(this).val().split('\\').pop();
            $(this).siblings('.custom-file-label').addClass('selected').html(fileName);

            // Image preview
            var preview = $(this).siblings('.custom-file-preview').find('img');
            if (preview.length && this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    preview.attr('src', e.target.result);
                    preview.parent().show();
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Confirmation dialog for delete actions
        $('.btn-delete').on('click', function (e) {
            if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                e.preventDefault();
            }
        });

        // Auto hide alerts after 5 seconds
        setTimeout(function () {
            $('.alert').fadeOut('slow');
        }, 5000);
    });

    // Function to preview image before upload
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#' + previewId).attr('src', e.target.result);
                $('#' + previewId).parent().show();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
</body>

</html>