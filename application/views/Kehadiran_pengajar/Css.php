<!-- FAVICONS ICON -->
<link rel="shortcut icon" type="image/png" href="<?php echo base_url() ?>assets/images/favicon.png" />
<!-- Datatable -->
<link href="<?php echo base_url() ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<!-- Custom Stylesheet -->
<link href="<?php echo base_url() ?>assets/vendor/jquery-nice-select/css/nice-select.css" rel="stylesheet">
<link href="<?php echo base_url() ?>assets/css/style.css" rel="stylesheet">
<style>
.table-scroll{
  max-height: 70vh;          /* cukup tinggi, biar enak scroll */
  overflow-y: auto;          /* scroll vertikal */
}

.table-scroll table{
  border-collapse: separate; /* penting agar sticky+border rapi */
  border-spacing: 0;
}

.table-scroll thead th{
  position: sticky;
  top: 0;                    /* nempel di atas container scroll */
  background: #fff;          /* atau var(--bs-body-bg) kalau pakai bootstrap var */
  z-index: 2;                /* di atas isi tabel */
}

/* garis bawah halus di header agar tetap “terlihat header” saat scroll */
.table-scroll thead th::after{
  content: "";
  position: absolute;
  left: 0; right: 0; bottom: 0;
  height: 1px;
  background: #e9ecef;
}

</style>
