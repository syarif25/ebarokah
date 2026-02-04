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
<script src="<?php echo base_url() ?>assets/js/demo.js"></script>

<script>
    var table;
    
    $(document).ready(function() {
        // Initialize DataTable
        table = $('#tabel_laporan_lembaga').DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": {
                "url": "<?php echo site_url('laporan_lembaga/data_list')?>",
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
                    "targets": [0, 2, 3, 5], // No, Bulan, Tahun, Aksi
                    "className": "text-center"
                },
                {
                    "targets": [4], // Jumlah Nominal
                    "className": "text-right",
                    "render": function(data, type, row) {
                        return '<span class="text-success">' + data + '</span>';
                    }
                },
                {
                    "targets": [5], // Aksi column
                    "orderable": false,
                    "render": function(data, type, row) {
                        return `<a href="${row.detail_url}" class="btn btn-primary btn-sm shadow sharp" title="Lihat Rincian">
                            <i class="fas fa-list-alt"></i> Detail
                        </a>`;
                    }
                }
            ],
            "order": [[0, 'asc']],
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
    });

    function refreshTable() {
        table.ajax.reload(null, false);
    }

    function applyFilter() {
        var lembaga = $('#filter_lembaga').val();
        var bulan = $('#filter_bulan').val();
        var tahun = $('#filter_tahun').val();
        
        console.log('Filter applied:', { lembaga, bulan, tahun });
        
        // Reload DataTable with filter parameters
        table.ajax.reload();
        
        // Show notification
        var filterMsg = 'Filter diterapkan';
        if (lembaga || bulan || tahun) {
            var filters = [];
            if (lembaga) filters.push('Lembaga');
            if (bulan && tahun) filters.push('Periode: ' + bulan + ' ' + tahun);
            else if (bulan) filters.push('Bulan: ' + bulan);
            else if (tahun) filters.push('Tahun: ' + tahun);
            filterMsg += ': ' + filters.join(', ');
        }
        toastr.success(filterMsg, 'Filter Berhasil');
    }

    function resetFilter() {
        $('#filter_lembaga').val('').trigger('change'); // trigger change for Select2
        $('#filter_periode').datepicker('clearDates');
        $('#filter_bulan').val('');
        $('#filter_tahun').val('');
        refreshTable();
        toastr.success('Filter berhasil direset', 'Berhasil');
    }


    // Initialize Select2 for Lembaga dropdown
    $(document).ready(function() {
        $('#filter_lembaga').select2({
            placeholder: 'Pilih Lembaga',
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() {
                    return "Tidak ada hasil ditemukan";
                },
                searching: function() {
                    return "Mencari...";
                }
            }
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
            // Extract month and year from selected date
            var date = e.date;
            var monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                              "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            var month = monthNames[date.getMonth()];
            var year = date.getFullYear();
            
            // Set hidden fields
            $('#filter_bulan').val(month);
            $('#filter_tahun').val(year);
            
            console.log('Periode selected:', month, year);
        });
    });
</script>
