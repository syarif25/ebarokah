<!-- FAVICONS ICON -->
<link rel="shortcut icon" type="image/png" href="<?php echo base_url() ?>assets/images/favicon.png" />
<!-- Datatable -->
<link href="<?php echo base_url() ?>assets/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<!-- Custom Stylesheet -->
<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="<?php echo base_url() ?>assets/css/style.css" rel="stylesheet">
<style>
/* Dashboard Struktural Custom Styles */
/* Select2 Custom Styling to match Bootstrap */
.select2-container .select2-selection--single {
    height: 40px !important;
    border: 1px solid #d7d7d7 !important;
    border-radius: 0.75rem !important;
    padding: 5px 10px !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
    right: 10px !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 28px !important;
    color: #3d4465 !important;
}

.card-summary {
    border-left: 4px solid #007bff;
    transition: all 0.3s ease;
}

.card-summary:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.icon-box {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

.icon-box.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.icon-box.bg-success {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.icon-box.bg-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.icon-box.bg-warning {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.icon-box.bg-danger {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
}

.icon-box.bg-secondary {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
}

.bg-success-light {
    background-color: #d4edda;
}

.bg-warning-light {
    background-color: #fff3cd;
}

.bg-danger-light {
    background-color: #f8d7da;
}

/* Chart containers */
canvas {
    max-height: 400px;
}

/* Table styles */
#tableDetail {
    font-size: 0.9rem;
}

#tableDetail th {
    background-color: #f8f9fa;
    font-weight: 600;
}

/* Modal styles */
.modal-body table td:first-child {
    font-weight: 600;
    width: 40%;
}

/* Responsive */
@media (max-width: 768px) {
    .card-summary {
        margin-bottom: 15px;
    }
    
    .icon-box {
        width: 50px;
        height: 50px;
        font-size: 20px;
    }
}

/* Loading overlay */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.8);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-overlay.show {
    display: flex;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}
</style>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
