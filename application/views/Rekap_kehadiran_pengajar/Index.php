<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Laporan</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Rekap Kehadiran Pengajar</a></li>
        </ol>
    </div>
    <!-- row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Rekap Kehadiran Pengajar</h4>
                        <p class="text-muted mb-0 mt-1"><i class="fas fa-info-circle"></i> Filter laporan berdasarkan lembaga dan rentang waktu (Mulai tahun 2025).</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Lembaga <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="filter_lembaga">
                                <option value="">-- Pilih Lembaga --</option>
                                <?php foreach($lembaga_list as $l): ?>
                                <option value="<?php echo $l->id_lembaga; ?>"><?php echo $l->nama_lembaga; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Dari Bulan</label>
                            <select class="form-control" id="start_month">
                                <option value="Januari">Januari</option>
                                <option value="Februari">Februari</option>
                                <option value="Maret">Maret</option>
                                <option value="April">April</option>
                                <option value="Mei">Mei</option>
                                <option value="Juni">Juni</option>
                                <option value="Juli">Juli</option>
                                <option value="Agustus">Agustus</option>
                                <option value="September">September</option>
                                <option value="Oktober">Oktober</option>
                                <option value="November">November</option>
                                <option value="Desember">Desember</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Dari Tahun</label>
                            <input type="number" class="form-control" id="start_year" value="<?php echo date('Y') < 2025 ? 2025 : date('Y'); ?>" min="2025">
                        </div>
                        
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Sampai Bulan</label>
                            <select class="form-control" id="end_month">
                                <option value="Januari">Januari</option>
                                <option value="Februari">Februari</option>
                                <option value="Maret">Maret</option>
                                <option value="April">April</option>
                                <option value="Mei">Mei</option>
                                <option value="Juni">Juni</option>
                                <option value="Juli">Juli</option>
                                <option value="Agustus">Agustus</option>
                                <option value="September">September</option>
                                <option value="Oktober">Oktober</option>
                                <option value="November">November</option>
                                <option value="Desember">Desember</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Sampai Tahun</label>
                            <input type="number" class="form-control" id="end_year" value="<?php echo date('Y') < 2025 ? 2025 : date('Y'); ?>" min="2025">
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-12 text-end text-right">
                            <button class="btn btn-primary" id="btn_filter" style="width: 150px;">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="table_pivot" class="table table-bordered table-striped table-hover dt-responsive nowrap w-100">
                            <thead id="table_pivot_head">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pengajar</th>
                                    <th>Lembaga</th>
                                    <!-- Kolom bulan dinamis -->
                                </tr>
                            </thead>
                            <tbody id="table_pivot_body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
