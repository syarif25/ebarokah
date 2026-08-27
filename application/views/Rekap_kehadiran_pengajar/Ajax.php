<script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
<script src="<?php echo base_url() ?>assets/vendor/chart.js/Chart.bundle.min.js"></script>
<script src="<?php echo base_url() ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<!-- Datatable Buttons -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script src="<?php echo base_url() ?>assets/vendor/select2/js/select2.full.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>

<script>
$(document).ready(function() {
    $('.select2').select2();
    
    // Default table initialization for empty state
    var pivotTable = $('#table_pivot').DataTable({
        "searching": false,
        "paging": false,
        "info": false,
        "ordering": false,
        "language": {
            "emptyTable": "Silakan pilih filter lembaga dan rentang waktu lalu klik Cari"
        }
    });

    $('#btn_filter').click(function() {
        var id_lembaga = $('#filter_lembaga').val();
        var start_month = $('#start_month').val();
        var start_year = $('#start_year').val();
        var end_month = $('#end_month').val();
        var end_year = $('#end_year').val();

        if (id_lembaga == '') {
            alert('Pilih lembaga terlebih dahulu');
            return;
        }

        if (start_year == '' || end_year == '') {
            alert('Tahun tidak boleh kosong');
            return;
        }

        var oldBtnHtml = $('#btn_filter').html();
        $('#btn_filter').html('<i class="fa fa-spinner fa-spin"></i> Loading...').prop('disabled', true);

        $.ajax({
            url: "<?php echo base_url('Rekap_kehadiran_pengajar/get_data'); ?>",
            type: "POST",
            data: {
                id_lembaga: id_lembaga,
                start_month: start_month,
                start_year: start_year,
                end_month: end_month,
                end_year: end_year
            },
            dataType: "json",
            success: function(response) {
                $('#btn_filter').html(oldBtnHtml).prop('disabled', false);

                if (response.status == 'error') {
                    alert(response.message);
                    return;
                }

                // Destroy existing DataTable if any
                if ($.fn.DataTable.isDataTable('#table_pivot')) {
                    $('#table_pivot').DataTable().destroy();
                }

                // Rebuild Header
                var thead = '<tr><th>No</th><th>Nama Pengajar</th><th>Lembaga</th>';
                $.each(response.columns, function(index, col) {
                    thead += '<th>' + col + '</th>';
                });
                thead += '</tr>';
                $('#table_pivot_head').html(thead);

                // Rebuild Body
                var tbody = '';
                if (response.data.length == 0) {
                    var colspan = 3 + response.columns.length;
                    tbody = '<tr><td colspan="' + colspan + '" class="text-center">Tidak ada data ditemukan</td></tr>';
                } else {
                    $.each(response.data, function(index, row) {
                        tbody += '<tr>';
                        tbody += '<td>' + (index + 1) + '</td>';
                        tbody += '<td>' + row.nama + '</td>';
                        tbody += '<td>' + row.lembaga + '</td>';
                        
                        $.each(response.columns, function(i, col) {
                            var val = row.bulan_data[col] !== undefined ? row.bulan_data[col] : 0;
                            var badgeClass = val == 0 ? 'badge-danger light' : 'badge-success light';
                            tbody += '<td class="text-center"><span class="badge ' + badgeClass + '">' + val + '</span></td>';
                        });
                        
                        tbody += '</tr>';
                    });
                }
                $('#table_pivot_body').html(tbody);

                // Re-initialize DataTable
                if (response.data.length > 0) {
                    $('#table_pivot').DataTable({
                        "paging": true,
                        "lengthChange": true,
                        "searching": true,
                        "ordering": true,
                        "info": true,
                        "autoWidth": false,
                        "scrollX": true,
                        "dom": 'Bfrtip',
                        "buttons": [
                            {
                                extend: 'excelHtml5',
                                text: '<i class="fas fa-file-excel"></i> Excel',
                                className: 'btn btn-success btn-sm me-2',
                                title: 'Rekap Kehadiran Pengajar (Pivot)',
                                messageTop: function() {
                                    return 'Lembaga: ' + $('#filter_lembaga option:selected').text() + ' | Periode: ' + $('#start_month').val() + ' ' + $('#start_year').val() + ' s.d. ' + $('#end_month').val() + ' ' + $('#end_year').val();
                                }
                            },
                            {
                                extend: 'print',
                                text: '<i class="fas fa-print"></i> Cetak / PDF',
                                className: 'btn btn-info btn-sm',
                                title: '',
                                customize: function ( win ) {
                                    $(win.document.body)
                                        .css( 'font-size', '10pt' )
                                        .css( 'color', '#000' )
                                        .prepend(
                                            '<div style="display:flex; align-items:center; margin-bottom:20px; border-bottom:3px solid black; padding-bottom:10px;">' +
                                            '<img src="<?php echo base_url("assets/p2s2.png"); ?>" style="width:80px; margin-right:20px;" />' +
                                            '<div>' +
                                            '<h3 style="margin:0; font-size:16pt; font-family:tahoma, sans-serif; font-weight:bold;">PONDOK PESANTREN SALAFIYAH SYAFI\'IYAH SUKOREJO</h3>' +
                                            '<p style="margin:0; font-size:12pt; font-family:tahoma, sans-serif;">SUMBEREJO BANYUPUTIH SITUBONDO JAWA TIMUR</p>' +
                                            '<p style="margin:0; font-size:9pt; font-family:tahoma, sans-serif;">Po Box 2 telp 0388-452666 Fax. 452707 - Situbondo, 68374</p>' +
                                            '</div>' +
                                            '</div>' +
                                            '<h4 style="text-align:center; margin-top:10px; margin-bottom: 5px; font-weight: bold;">Laporan Rekap Kehadiran Pengajar (Pivot)</h4>' +
                                            '<p style="text-align:center; margin-bottom:20px;"><b>Lembaga:</b> ' + $('#filter_lembaga option:selected').text() + '<br><b>Periode:</b> ' + $('#start_month').val() + ' ' + $('#start_year').val() + ' s.d. ' + $('#end_month').val() + ' ' + $('#end_year').val() + '</p>'
                                        );

                                    $(win.document.body).find( 'table' )
                                        .addClass( 'compact' )
                                        .css( 'font-size', 'inherit' )
                                        .css( 'color', '#000' );
                                        
                                    // Remove badges for cleaner print
                                    $(win.document.body).find( 'table tbody td span.badge' ).each(function() {
                                        var val = $(this).text();
                                        $(this).parent().text(val);
                                    });
                                }
                            }
                        ]
                    });
                }
            },
            error: function(xhr, status, error) {
                $('#btn_filter').html(oldBtnHtml).prop('disabled', false);
                alert('Terjadi kesalahan sistem');
            }
        });
    });
});
</script>
