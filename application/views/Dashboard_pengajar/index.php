<div class="container-fluid">
    <!-- Page Header -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor"><i class="fas fa-chalkboard-teacher"></i> Dashboard Barokah Pengajar</h3>
        </div>
        <div class="col-md-7 align-self-center text-end">
            <div class="d-flex justify-content-end align-items-center">
                <ol class="breadcrumb justify-content-end">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
                    <li class="breadcrumb-item active">Barokah Pengajar</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"><i class="fas fa-filter"></i> Filter Data</h4>
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Bulan</label>
                            <select class="form-select" id="filterBulan" name="bulan">
                                <option value="Januari" <?= ($default_bulan == 'Januari') ? 'selected' : '' ?>>Januari</option>
                                <option value="Februari" <?= ($default_bulan == 'Februari') ? 'selected' : '' ?>>Februari</option>
                                <option value="Maret" <?= ($default_bulan == 'Maret') ? 'selected' : '' ?>>Maret</option>
                                <option value="April" <?= ($default_bulan == 'April') ? 'selected' : '' ?>>April</option>
                                <option value="Mei" <?= ($default_bulan == 'Mei') ? 'selected' : '' ?>>Mei</option>
                                <option value="Juni" <?= ($default_bulan == 'Juni') ? 'selected' : '' ?>>Juni</option>
                                <option value="Juli" <?= ($default_bulan == 'Juli') ? 'selected' : '' ?>>Juli</option>
                                <option value="Agustus" <?= ($default_bulan == 'Agustus') ? 'selected' : '' ?>>Agustus</option>
                                <option value="September" <?= ($default_bulan == 'September') ? 'selected' : '' ?>>September</option>
                                <option value="Oktober" <?= ($default_bulan == 'Oktober') ? 'selected' : '' ?>>Oktober</option>
                                <option value="November" <?= ($default_bulan == 'November') ? 'selected' : '' ?>>November</option>
                                <option value="Desember" <?= ($default_bulan == 'Desember') ? 'selected' : '' ?>>Desember</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tahun Anggaran</label>
                            <select class="form-select" id="filterTahun" name="tahun">
                                <option value="2022/2023" <?= ($default_tahun == '2022/2023') ? 'selected' : '' ?>>2022/2023</option>
                                <option value="2023/2024" <?= ($default_tahun == '2023/2024') ? 'selected' : '' ?>>2023/2024</option>
                                <option value="2024/2025" <?= ($default_tahun == '2024/2025') ? 'selected' : '' ?>>2024/2025</option>
                                <option value="2025/2026" <?= ($default_tahun == '2025/2026') ? 'selected' : '' ?>>2025/2026</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Lembaga</label>
                            <select class="form-select" id="filterLembaga" name="lembaga">
                                <option value="semua" selected>Semua Lembaga</option>
                                <?php
                                $this->db->where('tenaga_pengajar', 'Ya');
                                $lembaga = $this->db->get('lembaga')->result();
                                foreach ($lembaga as $l) {
                                    echo '<option value="'.$l->id_lembaga.'">'.$l->nama_lembaga.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-primary me-2" id="btnFilter">
                                <i class="fa fa-filter"></i> Terapkan
                            </button>
                            <button type="button" class="btn btn-secondary" id="btnReset">
                                <i class="fa fa-redo"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="card card-summary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-primary">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Total Barokah</h6>
                            <h4 class="mb-0" id="cardTotalBarokah">Rp 0</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-summary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-success">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Jumlah Pengajar Aktif</h6>
                            <h4 class="mb-0" id="cardJumlahPengajar">0 Pengajar</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-summary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-info">
                            <i class="fas fa-male"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Pengajar Putra</h6>
                            <h4 class="mb-0" id="cardPengajarPutra">0 Pengajar</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-summary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-warning">
                            <i class="fas fa-female"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Pengajar Putri</h6>
                            <h4 class="mb-0" id="cardPengajarPutri">0 Pengajar</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"><i class="fas fa-chart-pie"></i> Rincian Komponen Barokah</h4>
                    <canvas id="chartDonut" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"><i class="fas fa-chart-bar"></i> 10 Lembaga dengan Barokah Terbesar</h4>
                    <canvas id="chartBar" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Trend Chart Row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"><i class="fas fa-chart-line"></i> Trend Total Barokah 4 Bulan Terakhir</h4>
                    <canvas id="chartTrend" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title"><i class="fas fa-table"></i> Detail Data Barokah Pengajar</h4>
                        <button class="btn btn-success" id="btnExport">
                            <i class="fa fa-file-excel"></i> Export Excel
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="tableDetail" class="table table-striped table-bordered nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Bulan</th>
                                    <th>Tahun</th>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Lembaga</th>
                                    <th>Kategori</th>
                                    <th>Mengajar</th>
                                    <th>DTY</th>
                                    <th>Jml Hadir</th>
                                    <th>Kehadiran</th>
                                    <th>Tunkel</th>
                                    <th>Tunj Anak</th>
                                    <th>Kehormatan</th>
                                    <th>Walkes</th>
                                    <th>Potongan</th>
                                    <th>Diterima</th>
                                    <th>Tgl Kirim</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
