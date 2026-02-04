<!-- FAVICONS ICON -->
<link rel="shortcut icon" type="image/png" href="<?php echo base_url() ?>assets/images/favicon.png" />

<!-- Datatable -->
<link href="<?php echo base_url() ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<!-- Bootstrap Datepicker -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">

<!-- Custom Stylesheet -->
<link href="<?php echo base_url() ?>assets/vendor/jquery-nice-select/css/nice-select.css" rel="stylesheet">
<link href="<?php echo base_url() ?>assets/css/style.css" rel="stylesheet">

<!-- Custom CSS for Laporan Lembaga -->
<style>
    .widget-stat .media .media-body {
        align-self: center;
    }
    
    .widget-stat .media i {
        font-size: 2.5rem;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }
    
    .table-responsive {
        border-radius: 0.375rem;
        overflow-x: auto !important;
        overflow-y: visible;
        -webkit-overflow-scrolling: touch;
        display: block;
        width: 100%;
    }
    
    .table-responsive table {
        min-width: 100%;
        white-space: nowrap;
    }
    
    .btn-group .btn {
        margin: 0 2px;
    }
    
    /* DataTable custom styling */
    #tabel_laporan_lembaga thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
    
    #tabel_laporan_lembaga tbody tr:hover {
        background-color: #f1f5f9;
        cursor: pointer;
    }
    
    /* Filter section styling */
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #e9ecef;
    }
    
    
    /* Summary cards animation */
    .widget-stat {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .widget-stat:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    /* Rincian table specific styles */
    #tabel_rincian {
        table-layout: auto;
        width: auto !important;
        min-width: 100%;
    }
    
    #tabel_rincian th,
    #tabel_rincian td {
        white-space: nowrap;
        padding: 8px 12px;
        font-size: 0.875rem;
    }
    
    #tabel_rincian thead th {
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        z-index: 10;
    }

</style>
