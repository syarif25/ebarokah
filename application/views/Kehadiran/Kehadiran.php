<div class="container-fluid">
	<div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Master</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Kehadiran</a></li>
        </ol>
    </div>
    <!-- row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Kehadiran Umana</h4>
                    <!--<button class="btn btn-info" onclick="reload_table()">Refresh</button>-->
                    <button type="button" class="btn btn-rounded btn-success" onclick="add_blanko()"><span class="btn-icon-start text-success"><i class="fa fa-plus color-info"></i>
                        </span>Tambah Blanko Kehadiran</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabel_view" class="table table-hover table-responsive-sm" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama lembaga</th>
                                    <!-- <th>Bidang</th> -->
                                    <th>Jumlah Umana'</th>
                                    <th>Periode</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                               
                            </tbody>
                            <!-- <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Nama lembaga</th>
                                    <th>Bidang</th>
                                    <th>Jumlah Umana'</th>
                                    <th>Aksi</th>
                                    
                                </tr>
                            </tfoot> -->
                        </table>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<!-- Stylings for Audit Trail / Timeline -->
<style>
.timeline-vertical { position: relative; padding-left: 2rem; margin: 1rem 0; list-style: none; }
.timeline-vertical::before { content: ''; position: absolute; top: 0; bottom: 0; left: 16px; width: 2px; background-color: #e9ecef; }
.timeline-item { position: relative; margin-bottom: 1.5rem; }
.timeline-indicator { position: absolute; left: -2rem; width: 14px; height: 14px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 0 2px #e9ecef; top: 5px; z-index: 1;}
.timeline-indicator.status-Belum { background-color: #6c757d; }
.timeline-indicator.status-Sudah { background-color: #17a2b8; }
.timeline-indicator.status-Terkirim { background-color: #ffc107; }
.timeline-indicator.status-Revisi { background-color: #dc3545; }
.timeline-indicator.status-acc { background-color: #28a745; }
.timeline-content { background: #fff; padding: 1rem; border-radius: 0.5rem; border: 1px solid #e9ecef; }
.timeline-date { font-size: 0.8rem; color: #6c757d; }
.timeline-title { font-weight: 600; margin-bottom: 0.2rem; font-size: 0.95rem; }
.timeline-user { font-size: 0.85rem; font-weight: 500; color: #495057; }
</style>

<!-- Modal Timeline Riwayat -->
<div class="modal fade" id="modal_riwayat" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title font-weight-bold text-dark"><i class="fa fa-history mr-2"></i> Sinkronisasi Riwayat Barokah</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="background-color: #f4f6f9;">
         <ul class="timeline-vertical p-0" id="riwayat_timeline_container">
            <!-- Content injected via AJAX -->
            <li class="text-center text-muted"><i class="fa fa-circle-o-notch fa-spin"></i> Memuat Riwayat...</li>
         </ul>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>