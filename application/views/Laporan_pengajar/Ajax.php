<!-- Required vendors -->
<script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
<script src="<?php echo base_url() ?>assets/vendor/chart.js/Chart.bundle.min.js"></script>
<script src="<?php echo base_url() ?>assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>

<!-- Datatable -->
<script src="<?php echo base_url() ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/plugins-init/datatables.init.js"></script>

<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Bootstrap Datepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.id.min.js"></script>

<script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>

<script>
    var table;
    
    $(document).ready(function() {
        // Initialize DataTable
        table = $('#tabel_laporan_pengajar').DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": {
                "url": "<?php echo site_url('Laporan_pengajar/data_list')?>",
                "type": "POST",
                "data": function(d) {
                    // Add filter parameters to AJAX request
                    d.filter_lembaga = $('#filter_lembaga').val();
                    d.filter_bulan = $('#filter_bulan').val();
                    d.filter_tahun = $('#filter_tahun').val();
                }
            },
            "columnDefs": [
                {
                    "targets": [0, 2, 3, 5, 6], // No, Bulan, Tahun, Status, Aksi
                    "className": "text-center"
                },
                {
                    "targets": [4], // Jumlah Nominal
                    "className": "text-right",
                    "render": function(data, type, row) {
                        return '<span class="text-success font-weight-bold">' + data + '</span>';
                    }
                },
                {
                    "targets": [6], // Aksi column
                    "orderable": false
                }
            ],
            "order": [[3, 'desc'], [2, 'desc']], // Order by Tahun, then Bulan
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            "language": {
                "processing": "Memproses...",
                "lengthMenu": "Tampilkan _MENU_ data",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                "infoFiltered": "(disaring dari _MAX_ total data)",
                "search": "Cari:",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            }
        });

        // Initialize Select2 for Lembaga dropdown
        $('#filter_lembaga').select2({
            placeholder: 'Pilih Lembaga',
            allowClear: true,
            width: '100%'
        });

        // Initialize Bootstrap Datepicker as Month Picker
        $('#filter_periode').datepicker({
            format: "MM yyyy",
            startView: "months", 
            minViewMode: "months",
            language: 'id',
            autoclose: true,
            todayHighlight: true,
            orientation: "bottom auto"
        }).on('changeDate', function(e) {
            var date = e.date;
            var monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                              "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            
            if(date) {
                var month = monthNames[date.getMonth()];
                var year = date.getFullYear();
                $('#filter_bulan').val(month);
                $('#filter_tahun').val(year);
            }
        });
    });

    function refreshTable() {
        table.ajax.reload(null, false);
    }

    function applyFilter() {
        table.ajax.reload();
        toastr.success('Filter diterapkan', 'Info');
    }

    function resetFilter() {
        $('#filter_lembaga').val('').trigger('change');
        $('#filter_periode').val('');
        $('#filter_bulan').val('');
        $('#filter_tahun').val('');
        table.ajax.reload();
        toastr.success('Filter direset', 'Info');
    }
</script>
