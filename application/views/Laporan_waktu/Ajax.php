<script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
<script src="<?php echo base_url() ?>assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>
<script src="<?php echo base_url() ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script type="text/javascript">
    var table;
    $(document).ready(function() {
        table = $('#tabel_waktu').DataTable({ 
            "processing": true, 
            "serverSide": true, 
            "order": [], 
            "dom": 'Blfrtip',
            "buttons": [
                {
                    extend: 'excelHtml5',
                    className: 'btn btn-success',
                    text: '<i class="fas fa-file-excel"></i> Export Excel',
                    title: 'Laporan Waktu Pengajuan Barokah',
                    exportOptions: {
                        columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ]
                    },
                    init: function(api, node, config) {
                        $(node).removeClass('dt-button');
                    }
                }
            ],
            "ajax": {
                "url": "<?php echo site_url('laporan_waktu/data_list')?>",
                "type": "POST",
                "data": function ( data ) {
                    data.filter_bulan = $('#filter_bulan').val();
                    data.filter_tahun = $('#filter_tahun').val();
                }
            },
            "columnDefs": [
                { 
                    "targets": [ 0, 7 ], 
                    "orderable": false, 
                },
            ],
            "language": {
                "paginate": {
                "next": '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                "previous": '<i class="fa fa-angle-double-left" aria-hidden="true"></i>' 
                }
            }
        });

        // Inject Buttons to the new container
        table.buttons().container().appendTo('#export_container');

        // Trigger reload when filter changes
        $('#filter_bulan, #filter_tahun').on('change', function() {
            table.ajax.reload();
        });
    });
</script>
