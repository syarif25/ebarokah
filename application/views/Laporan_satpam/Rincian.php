    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Laporan</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">Satpam</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)"><?php echo $header->bulan . ' ' . $header->tahun; ?></a></li>
            </ol>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                             <h4 class="card-title">Laporan Barokah Satpam</h4>
                             <span><?php echo $header->nama_lembaga; ?> - <?php echo $header->bulan . ' ' . $header->tahun; ?></span>
                        </div>
                        <a href="<?php echo base_url('Laporan_satpam/cetak/'.$id_enkripsi); ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-print me-2"></i> Cetak Laporan</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table" class="table table-striped table-bordered" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="align-middle text-center">No</th>
                                        <th rowspan="2" class="align-middle text-center">Nama Lengkap</th>
                                        <th colspan="3" class="text-center">Transport</th>
                                        <th colspan="3" class="text-center">Barokah (Shift)</th>
                                        <th colspan="3" class="text-center">Dinihari</th>
                                        <th rowspan="2" class="align-middle text-center">Tunj. Danru</th>
                                        <th rowspan="2" class="align-middle text-center">Total Diterima</th>
                                    </tr>
                                    <tr>
                                        <th>Hari</th>
                                        <th>Nominal</th>
                                        <th>Jumlah</th>
                                        
                                        <th>Shift</th>
                                        <th>Nominal</th>
                                        <th>Jumlah</th>

                                        <th>Kali</th>
                                        <th>Nominal</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-center">Total</th>
                                        <th colspan="3"></th>
                                        <th colspan="3"></th>
                                        <th colspan="3"></th>
                                        <th id="total_danru" class="text-right"></th>
                                        <th id="grand_total" class="text-right"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
