<!-- FAVICONS ICON -->
<link rel="shortcut icon" type="image/png" href="<?php echo base_url() ?>assets/images/favicon.png" />
<!-- Datatable -->
<link href="<?php echo base_url() ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<!-- Custom Stylesheet -->
<link href="<?php echo base_url() ?>assets/vendor/jquery-nice-select/css/nice-select.css" rel="stylesheet">
<link href="<?php echo base_url() ?>assets/css/style.css" rel="stylesheet">
<style>
.table-scroll{
  max-height: 70vh;        
  overflow-y: auto;          /* scroll vertikal */
}

.table-scroll table{
  border-collapse: separate; 
  border-spacing: 0;
}

.table-scroll thead th{
  position: sticky;
  top: 0;                    
  background: #fff;          
  z-index: 2;                
}

.table-scroll thead th::after{
  content: "";
  position: absolute;
  left: 0; right: 0; bottom: 0;
  height: 1px;
  background: #e9ecef;
}

</style>