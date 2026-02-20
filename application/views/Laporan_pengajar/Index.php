<div class="container-fluid">
	<div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Laporan</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Pengajar</a></li>
        </ol>
    </div>
    <!-- row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Laporan Barokah Pengajar/Dosen</h4>
                        <p class="text-muted mb-0 mt-1"><i class="fas fa-info-circle"></i> Menampilkan daftar barokah yang telah disetujui/dibayarkan</p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm" onclick="refreshTable()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Filter Lembaga</label>
                            <select class="form-control" id="filter_lembaga">
                                <option value="">Semua Lembaga</option>
                                <?php if(isset($lembaga_list) && !empty($lembaga_list)) {
                                    foreach($lembaga_list as $lembaga) { ?>
                                    <option value="<?php echo $lembaga->id_lembaga; ?>"><?php echo htmlentities($lembaga->nama_lembaga); ?></option>
                                <?php }} ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Filter Periode</label>
                            <input type="text" class="form-control" id="filter_periode" placeholder="Pilih Bulan & Tahun" readonly>
                            <input type="hidden" id="filter_bulan">
                            <input type="hidden" id="filter_tahun">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">&nbsp;</label><br>
                            <button class="btn btn-info" onclick="applyFilter()">
                                <i class="fas fa-filter"></i> Terapkan Filter
                            </button>
                            <button class="btn btn-secondary" onclick="resetFilter()">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                        </div>
                    </div>

                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table id="tabel_laporan_pengajar" class="table table-hover table-striped display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5%">No</th>
                                    <th>Nama Lembaga</th>
                                    <th class="text-center">Bulan</th>
                                    <th class="text-center">Tahun</th>
                                    <th class="text-right">Total Barokah</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be populated via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
