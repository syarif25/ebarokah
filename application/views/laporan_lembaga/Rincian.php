<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>laporan_lembaga">Laporan</a></li>
            <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>laporan_lembaga">Per Lembaga</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Rincian</a></li>
        </ol>
    </div>
    
    <!-- row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-1"><?php echo isset($data_rincian[0]) ? htmlentities($data_rincian[0]->nama_lembaga) : 'Tidak Ada Data'; ?></h4>
                        <p class="text-muted mb-0"><i class="fas fa-calendar"></i> Periode: <?php echo isset($data_rincian[0]) ? htmlentities($data_rincian[0]->bulan . ' ' . $data_rincian[0]->tahun) : '-'; ?></p>
                    </div>
                    <div>
                        <a href="<?php echo base_url('laporan_lembaga/cetak/' . $encrypted_id); ?>" target="_blank" class="btn btn-warning text-dark btn-sm me-2">
                            <i class="fas fa-print"></i> Cetak
                        </a>
                        <a href="<?php echo base_url()?>laporan_lembaga" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Summary Info -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="alert alert-primary">
                                <strong><i class="fas fa-users"></i> Total Umana:</strong><br>
                                <h5 class="mb-0 mt-2"><?php echo isset($data_rincian) ? count($data_rincian) : 0; ?> Orang</h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-primary">
                                <strong><i class="fas fa-money-bill-wave"></i> Total Barokah:</strong><br>
                                <?php 
                                $total_barokah = 0;
                                if (isset($data_rincian) && !empty($data_rincian)) {
                                    foreach ($data_rincian as $data) {
                                        $total_barokah += $data->diterima;
                                    }
                                }
                                ?>
                                <h5 class="mb-0 mt-2"><?php echo rupiah($total_barokah); ?></h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-primary">
                                <strong><i class="fas fa-chart-line"></i> Rata-rata:</strong><br>
                                <?php 
                                $rata_rata = 0;
                                if (isset($data_rincian) && count($data_rincian) > 0) {
                                    $rata_rata = $total_barokah / count($data_rincian);
                                }
                                ?>
                                <h5 class="mb-0 mt-2"><?php echo rupiah($rata_rata); ?></h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-info">
                                <strong><i class="fas fa-calendar-check"></i> Status:</strong><br>
                                <h5 class="mb-0 mt-2"><span class="badge badge-success">Selesai</span></h5>
                            </div>
                        </div>
                    </div>

                    <!-- DataTable Detail -->
                    <div class="table-responsive">
                        <table id="tabel_rincian" class="table table-striped table-bordered table-hover" style="min-width: 100%">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Nama Lengkap</th>
                                    <th>Jabatan</th>
                                    <th class="text-center">TMT</th>
                                    <th class="text-right">Tunjab</th>
                                    <th class="text-center">MP</th>
                                    <th class="text-center" colspan="2">Kehadiran</th>
                                    <th class="text-right">Tunkel</th>
                                    <th class="text-right">Tunj Anak</th>
                                    <th class="text-right">TBK</th>
                                    <th class="text-right">TMP</th>
                                    <th class="text-right">Kehormatan</th>
                                    <th class="text-right">Potongan</th>
                                    <th class="text-right">Diterima</th>
                                    <th class="text-center">Status</th>
                                </tr>
                                <tr>
                                    <th colspan="7"></th>
                                    <th class="text-center bg-light">Hari</th>
                                    <th class="text-center bg-light">Nominal</th>
                                    <th colspan="7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Initialize totals
                                $no = 1;
                                $total_tunjab = 0;
                                $total_hadir_nominal = 0;
                                $total_tunkel = 0;
                                $total_tunj_anak = 0;
                                $total_tbk = 0;
                                $total_tmp = 0;
                                $total_kehormatan = 0;
                                $total_potongan = 0;
                                $total_diterima = 0;
                                $total_umana = 0;
                                
                                // Loop through real data from database
                                if (isset($data_rincian) && !empty($data_rincian)) {
                                    foreach ($data_rincian as $data) {
                                        // Build nama lengkap with gelar
                                        $nama_lengkap = '';
                                        if (!empty($data->gelar_depan)) {
                                            $nama_lengkap .= $data->gelar_depan . ' ';
                                        }
                                        $nama_lengkap .= $data->nama_lengkap;
                                        if (!empty($data->gelar_belakang)) {
                                            $nama_lengkap .= ', ' . $data->gelar_belakang;
                                        }
                                        
                                        // Calculate totals
                                        $total_tunjab += $data->barokah;
                                        $total_hadir_nominal += $data->nominal_kehadiran;
                                        $total_tunkel += $data->tunkel;
                                        $total_tunj_anak += $data->tunj_anak;
                                        $total_tbk += $data->tbk;
                                        $total_tmp += $data->tmp;
                                        $total_kehormatan += $data->kehormatan;
                                        $total_potongan += $data->potongan;
                                        $total_diterima += $data->diterima;
                                        $total_umana++;
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $no++; ?></td>
                                    <td><?php echo htmlentities($nama_lengkap); ?></td>
                                    <td><?php echo htmlentities($data->nama_jabatan); ?></td>
                                    <td class="text-center"><?php echo htmlentities(substr($data->tmt_struktural, 0, 4)); ?></td>
                                    <td class="text-right"><?php echo rupiah($data->barokah); ?></td>
                                    <td class="text-center"><?php echo htmlentities($data->mp); ?></td>
                                    <td class="text-center"><?php echo htmlentities($data->jumlah_hadir); ?></td>
                                    <td class="text-right"><?php echo rupiah($data->nominal_kehadiran); ?></td>
                                    <td class="text-right"><?php echo rupiah($data->tunkel); ?></td>
                                    <td class="text-right"><?php echo rupiah($data->tunj_anak); ?></td>
                                    <td class="text-right"><?php echo rupiah($data->tbk); ?></td>
                                    <td class="text-right"><?php echo rupiah($data->tmp); ?></td>
                                    <td class="text-right"><?php echo rupiah($data->kehormatan); ?></td>
                                    <td class="text-right text-danger"><?php echo rupiah($data->potongan); ?></td>
                                    <td class="text-right"><strong class="text-success"><?php echo rupiah($data->diterima); ?></strong></td>
                                    <td class="text-center"><span class="badge badge-success">Sukses</span></td>
                                </tr>
                                <?php 
                                    } 
                                } else { 
                                ?>
                                <tr>
                                    <td colspan="16" class="text-center">Tidak ada data</td>
                                </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <th colspan="4" class="text-center">TOTAL</th>
                                    <th class="text-right"><?php echo rupiah($total_tunjab); ?></th>
                                    <th></th>
                                    <th></th>
                                    <th class="text-right"><?php echo rupiah($total_hadir_nominal); ?></th>
                                    <th class="text-right"><?php echo rupiah($total_tunkel); ?></th>
                                    <th class="text-right"><?php echo rupiah($total_tunj_anak); ?></th>
                                    <th class="text-right"><?php echo rupiah($total_tbk); ?></th>
                                    <th class="text-right"><?php echo rupiah($total_tmp); ?></th>
                                    <th class="text-right"><?php echo rupiah($total_kehormatan); ?></th>
                                    <th class="text-right text-danger"><?php echo rupiah($total_potongan); ?></th>
                                    <th class="text-right"><strong class="text-success"><?php echo rupiah($total_diterima); ?></strong></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
