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
                    <a href="<?php echo base_url() ?>upload/<?php echo $periode->file; ?>" target="_blank" class="btn btn-twitter me-1 px-3"><i class="fa fa-file m-0"></i> Lihat File Absensi</a>
                    <a href="<?php echo base_url() ?>Kehadiran/Cetak/<?php echo $periode->id_kehadiran_lembaga; ?>" target="_blank" class="btn btn-warning text-dark"><i class="fas fa-print"></i> Cetak</a> 
                    <a href="<?php echo base_url() ?>Kehadiran/Cetak_Potongan_struktural/<?php echo $periode->id_lembaga; ?>" class="btn btn-danger " target="_blank"><i class="fas fa-print"></i> Potongan</a>
                </div>
                <form action="#" id="form">
                    <div class="card-body">
                        <div class="table-responsive table-container">
                            <table id="tabel_rek" class="table table-striped table-responsive-sm table-bordered">
                                <thead>
                                    <tr>
                                    <th>No</th>
                                        <th scope="col">Nama Lengkap</th>
                                        <th scope="col">Eselon</th>
                                        <th scope="col">TMT</th>
                                        <th scope="col">Tunjab</th>
                                        <th scope="col">MP</th>
                                         <th scope="col">TMP</th>
                                        <th colspan="2" class="text-center" scope="col">Kehadiran</th>
                                        <th scope="col">Tunkel</th>
                                        <th scope="col">Tunj Anak</th>
                                        <th scope="col">Kehormatan</th>
                                        <th scope="col">TBK</th>
                                        <th scope="col">Jumlah Barokah</th>
                                        <th scope="col">Potongan</th>
                                        <th scope="col">Diterima</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php
                                   $no = 1;
                                    foreach($isitunkel as $nominaltunkel);
                                    foreach($isitunj_anak as $nominaltunj_anak);
                                    foreach($isilist as $key){
                                            $jml_kehadiran = $key->jumlah_hadir * $key->nominal_transport;
                                        $sekolah = 2025;
                                        $madrasah = 2025;
                                        $fakultas = 2025;
                                        $kantor_pusat = 2025;
                                            
                                            if ($key->id_bidang == "Bidang DIKTI") {
                                                 $mp = $fakultas - date("Y", strtotime($key->tmt_struktural))  ;
                                                    if($mp == '0' ){
                                                    $masa_p = 0 ;
                                                    }else {
                                                        $masa_p = $mp;
                                                    }
                                            } else if ($key->id_bidang == "Bidang DIKJAR-M") {
                                                 $mp = $madrasah - date("Y", strtotime($key->tmt_struktural))  ;
                                                    if($mp == '0' ){
                                                    $masa_p = 0 ;
                                                    }else {
                                                        $masa_p = $mp;
                                                    }
                                            } else if ($key->id_bidang == "Bidang DIKJAR") {
                                                 $mp = $sekolah - date("Y", strtotime($key->tmt_struktural))  ;
                                                    if($mp == '0' ){
                                                    $masa_p = 0 ;
                                                    }else {
                                                        $masa_p = $mp;
                                                    }
                                            }  else { 
                                                $mp = $kantor_pusat - date("Y", strtotime($key->tmt_struktural))  ;
                                                if($mp == '0' ){
                                                $masa_p = 0 ;
                                                }else {
                                                    $masa_p = $mp;
                                                }
                                            }
                                            
                                            //mendapatkan tunkel
                                            if ($key->tunj_kel == "Ya" and $mp >= 2){
                                                $tunkel = $nominaltunkel->besaran_tunkel;
                                            } else {
                                                $tunkel = 0;
                                            }
                                            
                                            if ($key->tunj_anak == "Ya" ){
                                                $tunja_anak = $nominaltunj_anak->nominal_tunj_anak;
                                            } else {
                                                $tunja_anak = 0;
                                            }

                                            $hitung_kehormatan = $this->db->query("select nominal_kehormatan from barokah_kehormatan where min_masa_pengabdian <= $mp and max_masa_pengabdian >= $mp ")->result();
                                            
                                            if(!empty($hitung_kehormatan) and $key->kehormatan == 'Ya') {
                                                foreach($hitung_kehormatan as $nilai_kehormatan) {
                                                    $kehormatan = $nilai_kehormatan->nominal_kehormatan;
                                                }
                                            } else {
                                                $kehormatan = 0; // atau dapat juga menghasilkan pesan error atau log error
                                            }

                                            $hitung_tbk = $this->db->query("SELECT sum(nominal_tbk) as jumlah_tbk from t_beban_kerja, penempatan where t_beban_kerja.id_penempatan = penempatan.id_penempatan and t_beban_kerja.id_penempatan = $key->id_penempatan and t_beban_kerja.max_periode >= DATE(NOW()) ")->result();
                                           
                                            if(!empty($hitung_tbk)) {
                                                foreach($hitung_tbk as $nilai_tbk) {
                                                    $tbk = $nilai_tbk->jumlah_tbk;
                                                }
                                            } else {
                                                $tbk = 0; // atau dapat juga menghasilkan pesan error atau log error
                                            }
                                            
                                             $hitung_potongan = $this->db->query("SELECT SUM(nominal_potongan) as jumlah  from potongan_umana, penempatan where potongan_umana.id_penempatan = penempatan.id_penempatan and potongan_umana.id_penempatan = $key->id_penempatan and potongan_umana.max_periode_potongan >= DATE(NOW()) ")->result();
                                           
                                            if(!empty($hitung_potongan)) {
                                                foreach($hitung_potongan as $jumlah_potongan) {
                                                    $potongan = $jumlah_potongan->jumlah;
                                                }
                                            } else {
                                                $potongan = 0; // atau dapat juga menghasilkan pesan error atau log error
                                            }

                                        // ========================== HITUNG TMP   =====================================================================
                                            $tmp = 0;
                                            // jika sudah 3 tahun, naikkan tmp sebesar 10.000
                                            
                                            $tahunSekarang = 2024; // mendapatkan tahun sekarang
                                            // $tahunKerja = $tahunSekarang - date("Y", strtotime($key->tmt_struktural)); // menghitung masa kerja dalam tahun
                                            if ($key->tunj_mp != "Tidak" and $mp >= 3 ) {
                                              $kenaikanGaji = floor($mp / 3) * 10000; // menghitung jumlah kenaikan gaji
                                              $tmp_Akhir = $tmp + $kenaikanGaji; // menghitung gaji saat ini
                                            } else {
                                              $tmp_Akhir = $tmp; // jika belum waktunya kenaikan gaji, gaji tetap sama dengan awal
                                            }
                                        //   ==================================================================================================================  
                                            
                                            $diterima = $jml_kehadiran + $tunkel + $tunja_anak + $key->barokah + $tmp_Akhir + $kehormatan + $tbk - $potongan;
                                            $jumlah_barokah = $jml_kehadiran + $tunkel + $tunja_anak + $key->barokah + $tmp_Akhir + $kehormatan + $tbk;
                                            
                                            $jumlah_akhir += $jumlah_barokah;
                                            $jumlah_total += $diterima;
                                            $jumlah_tunjab += $key->barokah;
                                            $jumlah_kehadiran += $jml_kehadiran;
                                            $jumlah_tunkel += $tunkel;
                                            $jumlah_tunanak += $tunja_anak;
                                            $jumlah_tmp += $tmp_Akhir;
                                            $jumlah_kehormatan += $kehormatan;
                                            $jumlah_tbk += $tbk;
                                            $jumlah_potong += (int)$potongan;
                                            
                                            ?>
                                    <tr>
                                        <td>
                                            <?php echo $no++; ?>
                                            <input type="hidden" name="bulan[]" value="<?php echo $key->bulan ?>">
                                            <input type="hidden" name="tahun[]" value="<?php echo $key->tahun ?>">
                                            <input type="hidden" name="id_kehadiran_lembaga" value="<?php echo $key->id_kehadiran_lembaga ?>">
                                        </td>
                                        <td>
                                            <?php 
                                               if($key->status == "Terkirim" and $this->session->userdata('jabatan') != 'AdminLembaga' and $this->session->userdata('jabatan') != 'SDM'){

                                            ?>
                                                <a class="text-success" onclick="detail_umana('<?php echo $key->id_kehadiran; ?>')" href="#"><?php echo htmlentities($key->gelar_depan.' '.$key->nama_lengkap.' '.$key->gelar_belakang); ?></a>    
                                            <?php } else {
                                            ?>
                                                <span class="text-success"><?php echo htmlentities($key->gelar_depan.' '.$key->nama_lengkap.' '.$key->gelar_belakang); ?></span>
                                            <?php } ?>

                                            <input type="hidden" name="id_kehadiran[]" value="<?php echo $key->id_kehadiran ?>">
                                            <input type="hidden" name="nik_umana[]" value="<?php echo $key->id_penempatan ?>">
                                        </td>
                                        <td>
                                            <?php echo htmlentities($key->nama_jabatan); ?>
                                            <input type="hidden" name="jabatan[]" value="<?php echo $key->nama_jabatan ?>">
                                        </td>
                                        <td>
                                            <?php echo date("Y", strtotime($key->tmt_struktural)); ?>
                                            <input type="hidden" name="tmt_struktural[]" value="<?php echo date("Y", strtotime($key->tmt_struktural)) ?>">
                                        </td>
                                        <td>
                                            <?php echo htmlentities(rupiah($key->barokah)); ?>
                                            <input type="hidden" name="tunjab[]" value="<?php echo $key->barokah ?>">
                                        </td>
                                        <td>
                                            <?php if ($masa_p <= 0 ) {
                                                $masa_pengabdian == 0;
                                            } else {
                                                $masa_pengabdian == $masa_p ;
                                            }
                                            
                                            echo htmlentities($masa_p); ?>
                                             <input type="hidden" name="mp[]" value="<?php echo $masa_p ?>">
                                        </td>
                                        <td>
                                            <?php echo htmlentities(rupiah($tmp_Akhir)); ?>
                                            <input type="hidden" name="tmp[]" value="<?php echo $tmp_Akhir ?>">
                                        </td>
                                        <td>
                                            <?php echo htmlentities($key->jumlah_hadir); ?>
                                            <input type="hidden" name="jml_hadir[]" value="<?php echo $key->jumlah_hadir ?>">
                                        </td>
                                        <td>
                                            <?php echo htmlentities(rupiah($jml_kehadiran)); ?>
                                            <input type="hidden" name="nominal_kehadiran[]" value="<?php echo $jml_kehadiran ?>">
                                        </td>
                                        <td>
                                            <?php echo htmlentities(rupiah($tunkel)); ?>
                                            <input type="hidden" name="tunkel[]" value="<?php echo $tunkel ?>">
                                        </td>
                                        <td>
                                            <?php echo htmlentities(rupiah($tunja_anak)); ?> 
                                            <input type="hidden" name="tunj_anak[]" value="<?php echo $tunja_anak ?>">
                                        </td>
                                        <td>
                                            <?php echo htmlentities(rupiah($kehormatan)); ?>
                                            <input type="hidden" name="kehormatan[]" value="<?php echo $kehormatan ?>">
                                        </td>
                                        <td>
                                             <a class="text-primary" href="#" onclick="detail_tbk('<?php echo $key->id_penempatan; ?>')"><?php echo htmlentities(rupiah($tbk)); ?></a>
                                            <input type="hidden" name="tbk[]" value="<?php echo $tbk ?>">
                                        </td>
                                        <td>
                                            <?php echo htmlentities(rupiah($jumlah_barokah)); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlentities(rupiah($potongan)); ?>
                                            <input type="hidden" name="potongan[]" value="<?php echo $potongan ?>">
                                        </td>
                                        <td>
                                            <?php echo htmlentities(rupiah($diterima)); ?>
                                            <input type="hidden" name="diterima[]" value="<?php echo $diterima ?>">
                                        </td>
                                    </tr>    
                                    <?php }?>
                                </tbody>
                                <tfoot>
                                    <td colspan="4"></td>
                                    <td><?php echo htmlentities(rupiah($jumlah_tunjab)); ?></td>
                                    <td></td>
                                     <td><?php echo htmlentities(rupiah($jumlah_tmp)); ?></td>
                                    <td></td>
                                    <td>
                                        <?php echo htmlentities(rupiah($jumlah_kehadiran)); ?>
                                    </td>
                                    <td><?php echo htmlentities(rupiah($jumlah_tunkel)); ?></td>
                                    <td><?php echo htmlentities(rupiah($jumlah_tunanak)); ?></td>
                                    <td><?php echo htmlentities(rupiah($jumlah_kehormatan)); ?></td>
                                    <td><?php echo htmlentities(rupiah($jumlah_tbk)); ?></td>
                                    <td>
                                        <?php echo htmlentities(rupiah($jumlah_akhir)); ?>
                                    </td>
                                    <td><?php echo htmlentities(rupiah($jumlah_potong)); ?></td>
                                    <td><?php echo htmlentities(rupiah($jumlah_total)); ?>
                                    <input type="hidden" name="jumlah_total" value="<?php echo $jumlah_total ?>"></td>
                                </tfoot>
                            </table>
                        </div>
                        <div>
                            <?php 
                                if($key->status == "Sudah" and $this->session->userdata('jabatan') != 'SDM'){
                            ?>
                            <button type="submit" id="btnSave" onclick="kirim()" class="btn btn-rounded btn-primary mb-9" ><span class="btn-icon-start text-primary"><i class="mdi mdi-send"></i>
                            </span>Kirim</button> 
                            <?php
                                } elseif ($this->session->userdata('jabatan') == 'SDM') {
                                } elseif($key->status == "Terkirim" and $this->session->userdata('jabatan') != 'AdminLembaga' ) {
                            ?>
                            <button type="submit" id="btnSave" onclick="save()" class="btn btn-rounded btn-primary mb-9" ><span class="btn-icon-start text-primary"><i class="fa fa-check"></i>
                            </span>Setujui</button>
                            <?php
                                } else {
                                
                                }
                            ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- </div> -->