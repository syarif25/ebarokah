<!-- Required vendors -->
<script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>

<!-- Datatable -->
<script src="<?php echo base_url() ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/plugins-init/datatables.init.js"></script>
<script src="<?php echo base_url() ?>assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>


<?php if(isset($id_enkripsi)): ?>
<script type="text/javascript">
    var table;
    $(document).ready(function() {
        //datatables
        table = $('#table').DataTable({ 
            "processing": true, 
            "serverSide": false, 
            "order": [], 
            
            "ajax": {
                "url": "<?php echo site_url('Laporan_satpam/data_rincian/'.$id_enkripsi)?>",
                "type": "POST"
            },

            "columnDefs": [
                { 
                    "targets": [ 0 ], 
                    "orderable": false, 
                },
                {
                    "class": "text-end",
                    "targets": [3, 4, 6, 7, 9, 10, 11, 12] // Kolom Rupiah align right
                },
                {
                    "class": "text-center",
                    "targets": [2, 5, 8] // Kolom Hari/Shift/Dini align center
                }
            ],
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
    
                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\Rp,.]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                // Total Transport (Column 4)
                totalTransport = api
                    .column( 4 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                // Total Barokah/Shift (Column 7)
                totalShift = api
                    .column( 7 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                // Total Dinihari (Column 10)
                totalDini = api
                    .column( 10 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                // Total Danru (Column 11)
                totalDanru = api
                    .column( 11 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
                    
                // Grand Total (Column 12)
                grandTotal = api
                    .column( 12 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );


                // Helper for currency formatting
                var fmt = function(n) {
                    return 'Rp ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                };
                
                var fmtNum = function(n) {
                    return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                };

                // Update Footer Table
                $( api.column( 4 ).footer() ).html(fmt(totalTransport));
                $( api.column( 7 ).footer() ).html(fmt(totalShift));
                $( api.column( 10 ).footer() ).html(fmt(totalDini));
                $( api.column( 11 ).footer() ).html(fmt(totalDanru));
                $( api.column( 12 ).footer() ).html(fmt(grandTotal));

                // Update Top Badges (Ringkasan)
                $('#sumTransport').html(fmtNum(totalTransport));
                $('#sumBarokah').html(fmtNum(totalShift));
                $('#sumDini').html(fmtNum(totalDini));
                $('#sumDanru').html(fmtNum(totalDanru));
                $('#sumGrand').html(fmtNum(grandTotal));
            }
        });
    });
</script>
<?php endif; ?>
