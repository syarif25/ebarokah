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

/* ============================================
   STICKY HEADER - Freeze saat scroll
   ============================================ */
.table-responsive {
  max-height: 70vh;
  overflow-y: auto;
  position: relative;
}

thead th {
  position: sticky;
  top: 0;
  z-index: 10;
  background: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* ============================================
   UX IMPROVEMENTS: Harmonious Color Scheme
   ============================================ */

/* Table Header - Soft Professional */
thead.thead-custom th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    padding: 14px 8px;
    border: none;
    font-size: 14px;
}

thead.thead-custom th i {
    color: white;
    opacity: 0.9;
    margin-right: 5px;
}

/* Attendance Input - Wider for 2-3 digits */
.attendance-input {
    font-weight: 600;
    font-size: 16px;
    width: 80px !important;  /* Wider untuk 2-3 digit */
    text-align: center;
    padding: 6px 8px;
}

.attendance-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.attendance-input.is-valid {
    border-color: #10b981 !important;
    background-color: #f0fdf4;
    padding-right: calc(1.5em + 0.75rem);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2310b981' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.attendance-input.is-invalid {
    border-color: #ef4444 !important;
    background-color: #fef2f2;
}

/* Stepper Button - Matching Theme */
.input-group-sm {
    width: auto;
    display: inline-flex;
    align-items: center;  /* Vertical alignment */
}

.input-group-sm .btn {
    padding: 6px 10px;
    font-size: 16px;
    line-height: 1.5;
    min-width: 38px;
    height: 38px;  /* Same height as input */
    font-weight: 600;
    background-color: #f3f4f6;
    border-color: #d1d5db;
    color: #6b7280;
    display: flex;
    align-items: center;
    justify-content: center;
}

.input-group-sm .form-control {
    height: 38px;  /* Consistent height */
}

.input-group-sm .btn:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
    transform: scale(1.05);
    transition: all 0.2s ease;
}

.input-group-sm .btn:active {
    transform: scale(0.98);
}

/* Table Striping - Soft */
.table-striped tbody tr:nth-of-type(odd) {
    background-color: #f9fafb;
}

.table-hover tbody tr:hover {
    background-color: #f3f4f6;
}

/* Responsive */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    thead.thead-custom th {
        font-size: 12px;
        padding: 10px 5px;
    }
    
    .input-group-sm .form-control {
        font-size: 14px;
        width: 70px !important;
    }
    
    .input-group-sm .btn {
        min-width: 32px;
        padding: 5px 8px;
    }
}

</style>