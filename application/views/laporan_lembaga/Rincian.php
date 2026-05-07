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
                        <?php 
                            $id_lembaga_rincian   = isset($data_rincian[0]->id_lembaga)           ? $data_rincian[0]->id_lembaga           : null;
                            $id_kehadiran_rincian = isset($data_rincian[0]->id_kehadiran_lembaga)  ? $data_rincian[0]->id_kehadiran_lembaga : null;
                            $status_rincian       = isset($data_rincian[0]->status)                ? $data_rincian[0]->status               : '-';
                            $kategori_btn         = isset($kategori) ? $kategori : 'Struktural';
                        ?>
                        <?php if ($id_lembaga_rincian && $kategori_btn == 'Pengajar'): ?>
                        <a href="<?php echo base_url('Kehadiran_pengajar/cetak_potongan/'.$id_lembaga_rincian.'/'.$id_kehadiran_rincian); ?>" target="_blank" class="btn btn-danger text-white btn-sm me-2 mr-1">
                            <i class="fas fa-cut mr-1"></i> Potongan
                        </a>
                        <?php elseif ($id_lembaga_rincian && $kategori_btn == 'Struktural'): ?>
                        <a href="<?php echo base_url('Kehadiran_struktural/cetak_potongan/'.$id_lembaga_rincian.'/'.$id_kehadiran_rincian); ?>" target="_blank" class="btn btn-danger text-white btn-sm me-2 mr-1">
                            <i class="fas fa-cut mr-1"></i> Potongan
                        </a>
                        <?php endif; ?>
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
                                <?php 
                                    $badge_color = 'success';
                                    if ($status_rincian == 'Terkirim') $badge_color = 'warning';
                                    elseif ($status_rincian == 'Revisi') $badge_color = 'danger';
                                    elseif ($status_rincian == 'Sudah') $badge_color = 'info';
                                    elseif ($status_rincian == 'Belum') $badge_color = 'secondary';
                                ?>
                                <h5 class="mb-0 mt-2"><span class="badge badge-<?= $badge_color ?>"><?= htmlentities($status_rincian) ?></span></h5>
                            </div>
                        </div>
                    </div>

                    <!-- DataTable Detail -->
                    <div class="table-responsive">
                        <?php
                        $kategori_view = isset($kategori) ? $kategori : 'Struktural';
                        
                        // Inisialisasi totals
                        $no = 1;
                        $total_diterima = 0;
                        $total_potongan = 0;
                        $total_mengajar = 0;
                        $total_tunkel   = 0;
                        $total_tunj_anak = 0;
                        $total_kehormatan = 0;
                        $total_kehadiran_nominal = 0;
                        $total_tunjab   = 0;
                        $total_tbk      = 0;
                        $total_tmp      = 0;
                        ?>

                        <?php if ($kategori_view == 'Pengajar'): ?>
                        <!-- ========== TABEL PENGAJAR ========== -->
                        <table id="tabel_rincian" class="table table-striped table-bordered table-hover" style="min-width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Nama Lengkap</th>
                                    <th class="text-center">Kat.</th>
                                    <th class="text-center">MP</th>
                                    <th class="text-right">Rank/SKS</th>
                                    <th class="text-right">Mengajar</th>
                                    <th class="text-right">Kehadiran</th>
                                    <th class="text-right">Tunkel</th>
                                    <th class="text-right">Tun Anak</th>
                                    <th class="text-right">Kehormatan</th>
                                    <th class="text-right">Potongan</th>
                                    <th class="text-right">Diterima</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data_rincian)): ?>
                                    <?php foreach ($data_rincian as $data):
                                        $nama_lengkap = trim(($data->gelar_depan ?? '') . ' ' . $data->nama_lengkap . (isset($data->gelar_belakang) && $data->gelar_belakang ? ', ' . $data->gelar_belakang : ''));
                                        $kehadiran_nominal = ($data->nominal_kehadiran ?? 0) + ($data->nominal_hadir_15 ?? 0) + ($data->nominal_hadir_10 ?? 0) + ($data->barokah_piket ?? 0);
                                        $diterima_pengajar = ($data->mengajar ?? 0) + $kehadiran_nominal + ($data->tunkel ?? 0) + ($data->tun_anak ?? 0) + ($data->kehormatan ?? 0) + ($data->dty ?? 0) + ($data->jafung ?? 0) + ($data->walkes ?? 0) + ($data->khusus ?? 0) - ($data->potongan ?? 0);
                                        
                                        $total_mengajar += ($data->mengajar ?? 0);
                                        $total_kehadiran_nominal += $kehadiran_nominal;
                                        $total_tunkel += ($data->tunkel ?? 0);
                                        $total_tunj_anak += ($data->tun_anak ?? 0);
                                        $total_kehormatan += ($data->kehormatan ?? 0);
                                        $total_potongan += ($data->potongan ?? 0);
                                        $total_diterima += $diterima_pengajar;
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td><?= htmlentities($nama_lengkap) ?></td>
                                        <td class="text-center"><span class="badge badge-info badge-sm"><?= htmlentities($data->kategori ?? '-') ?></span></td>
                                        <td class="text-center"><?= htmlentities($data->mp ?? 0) ?></td>
                                        <td class="text-right"><?= rupiah($data->rank ?? 0) ?> × <?= $data->jumlah_sks ?? 0 ?> sks</td>
                                        <td class="text-right"><?= rupiah($data->mengajar ?? 0) ?></td>
                                        <td class="text-right"><?= rupiah($kehadiran_nominal) ?></td>
                                        <td class="text-right"><?= rupiah($data->tunkel ?? 0) ?></td>
                                        <td class="text-right"><?= rupiah($data->tun_anak ?? 0) ?></td>
                                        <td class="text-right"><?= rupiah($data->kehormatan ?? 0) ?></td>
                                        <td class="text-right text-danger"><?= rupiah($data->potongan ?? 0) ?></td>
                                        <td class="text-right"><strong class="text-success"><?= rupiah($diterima_pengajar) ?></strong></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="12" class="text-center">Tidak ada data</td></tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <th colspan="5" class="text-center">TOTAL</th>
                                    <th class="text-right"><?= rupiah($total_mengajar) ?></th>
                                    <th class="text-right"><?= rupiah($total_kehadiran_nominal) ?></th>
                                    <th class="text-right"><?= rupiah($total_tunkel) ?></th>
                                    <th class="text-right"><?= rupiah($total_tunj_anak) ?></th>
                                    <th class="text-right"><?= rupiah($total_kehormatan) ?></th>
                                    <th class="text-right text-danger"><?= rupiah($total_potongan) ?></th>
                                    <th class="text-right"><strong class="text-success"><?= rupiah($total_diterima) ?></strong></th>
                                </tr>
                            </tfoot>
                        </table>

                        <?php elseif ($kategori_view == 'Satpam'): ?>
                        <!-- ========== TABEL SATPAM ========== -->
                        <table id="tabel_rincian" class="table table-striped table-bordered table-hover" style="min-width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Nama Lengkap</th>
                                    <th class="text-center">Hadir</th>
                                    <th class="text-right">Gaji Pokok</th>
                                    <th class="text-right">Tunjangan</th>
                                    <th class="text-right">Potongan</th>
                                    <th class="text-right">Diterima</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data_rincian)): ?>
                                    <?php foreach ($data_rincian as $data):
                                        $nama_lengkap = trim(($data->gelar_depan ?? '') . ' ' . $data->nama_lengkap . (isset($data->gelar_belakang) && $data->gelar_belakang ? ', ' . $data->gelar_belakang : ''));
                                        $total_diterima += ($data->diterima ?? 0);
                                        $total_potongan += ($data->potongan ?? 0);
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td><?= htmlentities($nama_lengkap) ?></td>
                                        <td class="text-center"><?= $data->jumlah_hadir ?? 0 ?></td>
                                        <td class="text-right"><?= rupiah($data->gaji_pokok ?? 0) ?></td>
                                        <td class="text-right"><?= rupiah(($data->tunjangan ?? 0)) ?></td>
                                        <td class="text-right text-danger"><?= rupiah($data->potongan ?? 0) ?></td>
                                        <td class="text-right"><strong class="text-success"><?= rupiah($data->diterima ?? 0) ?></strong></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center">Tidak ada data</td></tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <th colspan="5" class="text-center">TOTAL</th>
                                    <th class="text-right text-danger"><?= rupiah($total_potongan) ?></th>
                                    <th class="text-right"><strong class="text-success"><?= rupiah($total_diterima) ?></strong></th>
                                </tr>
                            </tfoot>
                        </table>

                        <?php else: ?>
                        <!-- ========== TABEL STRUKTURAL (default) ========== -->
                        <table id="tabel_rincian" class="table table-striped table-bordered table-hover" style="min-width:100%">
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
                                <?php if (!empty($data_rincian)):
                                    foreach ($data_rincian as $data):
                                        $nama_lengkap = '';
                                        if (!empty($data->gelar_depan)) $nama_lengkap .= $data->gelar_depan . ' ';
                                        $nama_lengkap .= $data->nama_lengkap;
                                        if (!empty($data->gelar_belakang)) $nama_lengkap .= ', ' . $data->gelar_belakang;

                                        $total_tunjab   += $data->barokah;
                                        $total_kehadiran_nominal += $data->nominal_kehadiran;
                                        $total_tunkel   += $data->tunkel;
                                        $total_tunj_anak += $data->tunj_anak;
                                        $total_tbk      += $data->tbk;
                                        $total_tmp      += $data->tmp;
                                        $total_kehormatan += $data->kehormatan;
                                        $total_potongan += $data->potongan;
                                        $total_diterima += $data->diterima;
                                ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= htmlentities($nama_lengkap) ?></td>
                                    <td><?= htmlentities($data->nama_jabatan) ?></td>
                                    <td class="text-center"><?= htmlentities(substr($data->tmt_struktural, 0, 4)) ?></td>
                                    <td class="text-right"><?= rupiah($data->barokah) ?></td>
                                    <td class="text-center"><?= htmlentities($data->mp) ?></td>
                                    <td class="text-center"><?= htmlentities($data->jumlah_hadir) ?></td>
                                    <td class="text-right"><?= rupiah($data->nominal_kehadiran) ?></td>
                                    <td class="text-right"><?= rupiah($data->tunkel) ?></td>
                                    <td class="text-right"><?= rupiah($data->tunj_anak) ?></td>
                                    <td class="text-right"><?= rupiah($data->tbk) ?></td>
                                    <td class="text-right"><?= rupiah($data->tmp) ?></td>
                                    <td class="text-right"><?= rupiah($data->kehormatan) ?></td>
                                    <td class="text-right text-danger"><?= rupiah($data->potongan) ?></td>
                                    <td class="text-right"><strong class="text-success"><?= rupiah($data->diterima) ?></strong></td>
                                    <td class="text-center"><span class="badge badge-success">Sukses</span></td>
                                </tr>
                                <?php endforeach;
                                else: ?>
                                <tr><td colspan="16" class="text-center">Tidak ada data</td></tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <th colspan="4" class="text-center">TOTAL</th>
                                    <th class="text-right"><?= rupiah($total_tunjab) ?></th>
                                    <th></th>
                                    <th></th>
                                    <th class="text-right"><?= rupiah($total_kehadiran_nominal) ?></th>
                                    <th class="text-right"><?= rupiah($total_tunkel) ?></th>
                                    <th class="text-right"><?= rupiah($total_tunj_anak) ?></th>
                                    <th class="text-right"><?= rupiah($total_tbk) ?></th>
                                    <th class="text-right"><?= rupiah($total_tmp) ?></th>
                                    <th class="text-right"><?= rupiah($total_kehormatan) ?></th>
                                    <th class="text-right text-danger"><?= rupiah($total_potongan) ?></th>
                                    <th class="text-right"><strong class="text-success"><?= rupiah($total_diterima) ?></strong></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

