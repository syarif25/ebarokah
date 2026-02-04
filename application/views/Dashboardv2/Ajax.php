<script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
<script src="<?php echo base_url() ?>assets/vendor/chart.js/Chart.bundle.min.js"></script>
<!-- Apex Chart -->
<script src="<?php echo base_url() ?>assets/vendor/apexchart/apexchart.js"></script>

<!-- Datatable -->
<script src="<?php echo base_url() ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/plugins-init/datatables.init.js"></script>
<script src="<?php echo base_url() ?>assets/js/jquery.mask.js"></script>

<script src="<?php echo base_url() ?>assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>

<script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>

<script>
    var table;

    $(document).ready(function() {

        table = $('#tabel_laporan').DataTable({
            "processing": true,
            "serverSide": false,
            "ordering": true,
            "ajax": {
                "url": "<?= site_url('Dashboardv2/get_laporan') ?>",
                "type": "POST",
                "data": function(d){
                    d.periode = $('#periode').val();
                }
            }
        });

        $("#btn_filter").click(function(){
            table.ajax.reload();
        });

    });
</script>
