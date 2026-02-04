<div class="container-fluid">
	<div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Laporan</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Per Bulan</a></li>
        </ol>
    </div>
    <!-- row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Laporan Barokah Per Bulan</h4>
                        <p class="text-muted mb-0 mt-1"><i class="fas fa-info-circle"></i> Menampilkan daftar barokah per lembaga pada periode tertentu</p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm" onclick="refreshTable()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Period Selector -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-calendar-alt"></i> Pilih Periode</label>
                            <input type="text" class="form-control form-control-lg" id="periode_selector" placeholder="Pilih Bulan & Tahun" readonly>
                            <input type="hidden" id="selected_bulan">
                            <input type="hidden" id="selected_tahun">
                        </div>
                        <div class="col-md-8 d-flex align-items-end">
                            <button class="btn btn-info btn-lg" onclick="loadData()">
                                <i class="fas fa-search"></i> Tampilkan Data
                            </button>
                            <button class="btn btn-secondary btn-lg ms-2" onclick="resetPeriode()">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-4" id="summary_cards" style="display: none;">
                        <div class="col-xl-3 col-sm-6">
                            <div class="card gradient-1 card-bx">
                                <div class="card-body d-flex align-items-center">
                                    <div class="me-auto text-white">
                                        <h2 class="text-white" id="card_total_lembaga">0</h2>
                                        <span class="fs-18">Total Lembaga</span>
                                    </div>
                                    <i class="fas fa-building fa-3x text-white opacity-50"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="card gradient-2 card-bx">
                                <div class="card-body d-flex align-items-center">
                                    <div class="me-auto text-white">
                                        <h2 class="text-white" id="card_total_barokah">Rp 0</h2>
                                        <span class="fs-18">Total Barokah</span>
                                    </div>
                                    <i class="fas fa-money-bill-wave fa-3x text-white opacity-50"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="card gradient-3 card-bx">
                                <div class="card-body d-flex align-items-center">
                                    <div class="me-auto text-white">
                                        <h2 class="text-white" id="card_rata_rata">Rp 0</h2>
                                        <span class="fs-18">Rata-rata</span>
                                    </div>
                                    <i class="fas fa-chart-line fa-3x text-white opacity-50"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="card gradient-4 card-bx">
                                <div class="card-body d-flex align-items-center">
                                    <div class="me-auto text-white">
                                        <h4 class="text-white mb-0" id="card_periode">-</h4>
                                        <span class="fs-18">Periode</span>
                                    </div>
                                    <i class="fas fa-calendar-check fa-3x text-white opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table id="tabel_per_bulan" class="table table-hover table-striped display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Nama Lembaga</th>
                                    <th class="text-right">Jumlah Nominal</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <i class="fas fa-info-circle"></i> Silakan pilih periode untuk menampilkan data
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Nama Lembaga</th>
                                    <th class="text-right">Jumlah Nominal</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
