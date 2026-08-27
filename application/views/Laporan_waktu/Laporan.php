    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Laporan Waktu Pengajuan</h4>
                    <!-- <p class="mb-0">Memantau durasi dari upload kehadiran hingga ditransfer</p> -->
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Laporan</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Laporan Waktu Pengajuan</a></li>
                </ol>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Daftar Durasi Waktu Pengajuan Barokah</h4>
                    </div>
                    <div class="card-body">
                        <!-- Tambahkan baris filter -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label>Bulan</label>
                                <select class="form-control default-select" id="filter_bulan">
                                    <option value="">-- Semua Bulan --</option>
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
                            <div class="col-md-3">
                                <label>Tahun</label>
                                <select class="form-control default-select" id="filter_tahun">
                                    <option value="">-- Semua Tahun --</option>
                                    <?php 
                                        $currentYear = date('Y');
                                        for($i = $currentYear; $i >= $currentYear - 3; $i--){
                                            echo "<option value='$i'>$i</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end justify-content-end" id="export_container">
                                <!-- Export button will be injected here by DataTables -->
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="tabel_waktu" class="table table-hover table-responsive-sm" style="min-width: 1000px">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Lembaga</th>
                                        <th>Kategori</th>
                                        <th class="text-nowrap">Bulan - Tahun</th>
                                        <th class="text-nowrap">Waktu Upload</th>
                                        <th class="text-nowrap">Waktu ACC</th>
                                        <th class="text-nowrap">Waktu Transfer</th>
                                        <th class="text-nowrap">Durasi Total</th>
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
