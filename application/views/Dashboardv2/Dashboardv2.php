<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Laporan</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Barokah Bulanan</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Laporan Barokah Bulanan</h4>
                </div>

                <div class="card-body">

                    <!-- FILTER -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label>Pilih Periode</label>
                            <input type="month" id="periode" class="form-control">
                        </div>

                        <div class="col-md-2">
                            <label>&nbsp;</label><br>
                            <button class="btn btn-primary w-100" id="btn_filter">
                                Tampilkan
                            </button>
                        </div>
                    </div>

                    <!-- TABEL -->
                    <div class="table-responsive">
                        <table id="tabel_laporan" class="table table-bordered table-striped" width="100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIK</th>
                                    <th>Nama Lembaga</th>
                                    <th>Nama Lengkap</th>
                                    <th>Jabatan</th>
                                    <th>Hadir</th>
                                    <th>Kehadiran</th>
                                    <th>Tunkel</th>
                                    <th>Tunj Anak</th>
                                    <th>Transport</th>
                                    <th>Kehormatan</th>
                                    <th>TBK</th>
                                    <th>Khusus</th>
                                    <th>Total Awal</th>
                                    <th>Potongan</th>
                                    <th>Diterima</th>
                                    <th>Tgl Kirim</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
