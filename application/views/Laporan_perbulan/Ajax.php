<script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
    <script src="<?php echo base_url() ?>assets/vendor/chart.js/Chart.bundle.min.js"></script>
	<!-- Apex Chart -->
	<script src="<?php echo base_url() ?>assets/vendor/apexchart/apexchart.js"></script>
	
    <!-- Datatable -->
    <script src="<?php echo base_url() ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/plugins-init/datatables.init.js"></script>

	<script src="<?php echo base_url() ?>assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>

    <script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
	<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>

    <script>
    var save_method; //for save method string
    var table;


    $(function () {
      // $("#example1").DataTable();
    table =   $('#tabel_view').DataTable({
        "ajax": {
            "url": "<?php echo site_url('laporan/data_list_perbulan')?>",
            "type": "POST"
        },

        "columnDefs": [
            { 
                "targets": [ -1 ], //last column
                "orderable": false, //set not orderable
            },
        
        ],  

        "paging": true,
        "searching": true,
        "ordering": true,
        });
    });

    function detail(bulan_tahun) {
        $("#modal_detail").modal('show');
        $.ajax({
            url: '<?php echo base_url('Laporan/data_list_perbulan_perlembaga'); ?>',
            type: 'post',
            data: {
                bulan_tahun: bulan_tahun
            },
            dataType: 'json',
            success: function(response) {
                var len = response.data.length;
                $("#dataBody").empty();
                for (var i = 0; i < len; i++) {
                    var row = "<tr>";
                    row += "<td>" + (i + 1) + "</td>";
                    row += "<td>" + response.data[i][1] + "</td>";
                    row += "<td>" + response.data[i][2] + "</td>";
                    row += "<td>" + response.data[i][3] + "</td>";
                    row += "</tr>";
                    $("#dataBody").append(row);
                }
                
            }
        });
        console.log();
    }

    function reload_table()
    {
        table.ajax.reload(null,false); //reload datatable ajax 
    }

</script>


<div id="modal_detail" class="modal fade bd-example-modal-lg" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <form action="#" id="form">
                        <div class="card-header">
                            <h4 class="card-title">Detail Jumlah Barokah Lembaga</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <th>No</th>
                                    <th>Lembaga</th>
                                    <th>Jumlah</th>
                                    <th>Ket</th> 
                                </thead>
                                <tbody id="dataBody">
                                    <!-- Data akan dimasukkan disini menggunakan Ajax -->
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>