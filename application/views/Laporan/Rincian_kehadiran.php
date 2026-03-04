<!-- <div class="content-body"> -->
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)"></a></li> 
            <?php foreach($isilist as $periode){} ?>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Kehadiran Bulan <?php echo $periode->bulan." ".$periode->tahun; ?></a></li>
        </ol>
    </div>
    <!-- row -->
    <div class="row">
       <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><?php echo $periode->nama_lembaga ?></h4>
                    <a href="<?php echo base_url() ?>Kehadiran/Cetak/<?php echo $periode->id_kehadiran_lembaga; ?>" class="btn btn-warning text-dark"><i class="fas fa-print"></i> Cetak</a> 
                    <a href="<?php echo base_url() ?>upload/<?php echo $periode->file; ?>" target="_blank" class="btn btn-twitter me-1 px-3"><i class="fa fa-file m-0"></i> Lihat File Absensi</a> 
                </div>
                <form action="#" id="form">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tabel_rek" class="table table-striped table-responsive-sm table-bordered">
                                <thead>
                                    <tr>
                                    <th>No</th>
                                        <th>Nama Lengkap</th>
                                        <th>Jabatan</th>
                                        <th>TMT</th>
                                        <th>Tunjab</th>
                                        <th>MP</th>
                                        <th colspan="2" class="text-center">Kehadiran</th>
                                        <th>Tunkel</th>
                                        <th>Tunj Anak</th>
                                        <th>TBK</th>
                                        <th>TMP</th>
                                        <th>Kehormatan</th>
                                        <th>Potongan</th>
                                        <th>Diterima</th>
                                        <TH>Status</TH>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php
                                   $no = 1;
                                    // foreach($isitunkel as $nominaltunkel);
                                    $jumlah_barokah = 0;
                                    $jumlah_tunkel = 0;
                                    $jumlah_hadir = 0;
                                    $jumlah_tmp = 0;
                                    foreach($isilist as $key){
                                        $jumlah_barokah += $key->barokah;
                                        $jumlah_tunkel += $key->tunkel;
                                        $jumlah_tunanak += $key->tunj_anak;
                                        $jumlah_hadir += $key->nominal_kehadiran;
                                        $jumlah_tmp += $key->tmp;
                                        $jumlah_kehormatan += $key->kehormatan;
                                         $jumlah_potongan += $key->potongan;
                                        ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo htmlentities($key->gelar_depan.' '.$key->nama_lengkap.' '.$key->gelar_belakang); ?></td>
                                        <td><?php echo htmlentities($key->nama_jabatan.' '.$key->jabatan_lembaga); ?></td>
                                        <td><?php echo date("Y", strtotime($key->tmt_struktural)); ?></td>
                                        <td><?php echo htmlentities(rupiah($key->barokah)); ?></td>
                                        <td><?php echo htmlentities($key->mp); ?></td>
                                        <td><?php echo htmlentities($key->jumlah_hadir); ?></td>
                                        <td><?php echo htmlentities(rupiah($key->nominal_kehadiran)); ?></td>
                                        <td><?php echo htmlentities(rupiah($key->tunkel)); ?></td>
                                        <td><?php echo htmlentities(rupiah($key->tunj_anak)); ?></td>
                                        <td><?php echo htmlentities(rupiah($key->tbk)); ?></td>
                                        <td><?php echo htmlentities(rupiah($key->tmp)); ?></td>
                                        <td><?php echo htmlentities(rupiah($key->kehormatan)); ?></td>
                                        <td><?php echo htmlentities(rupiah($key->potongan)); ?></td>
                                        <td><?php echo htmlentities(rupiah($key->diterima)); ?></td>
                                        <td>Sukses</td>
                                    </tr>    
                                    <?php }?>
                                </tbody>
                                <tfoot>
                                    <td colspan="4" ></td>
                                    <td><b><?php echo htmlentities(rupiah($jumlah_barokah)); ?></b></td>
                                    <td colspan="2"></td>
                                    <td><b><?php echo htmlentities(rupiah($jumlah_hadir)); ?></b></td>
                                    <td><b><?php echo htmlentities(rupiah($jumlah_tunkel)); ?></b></td>
                                    <td><b><?php echo htmlentities(rupiah($jumlah_tunanak)); ?></b></td>
                                    <td><b><?php echo htmlentities(rupiah($jumlah_tbk)); ?></b></td>
                                    <td><b><?php echo htmlentities(rupiah($jumlah_tmp)); ?></b></td>
                                    <td><b><?php echo htmlentities(rupiah($jumlah_kehormatan)); ?></b></td>
                                    <td><b><?php echo htmlentities(rupiah($jumlah_potongan)); ?></b></td>
                                    <td><b><?php echo htmlentities(rupiah($key->jumlah_total)); ?></b></td>
                                </tfoot>
                            </table>
                        </div>
                        <!-- <form action="<?php echo base_url() ?>validasi/save_data" method="POST"> -->
                        <form action="#" id="form">
                        <div>
                                <a type="submit" href="<?php echo base_url() ?>kehadiran" class="btn btn-rounded btn-primary mb-9" ><span class="btn-icon-start text-primary"><i class="fa fa-check"></i>
                                </span>Kembali</a>
                            </div>
                        <br>
                        <div class="row">
                            
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- </div> -->