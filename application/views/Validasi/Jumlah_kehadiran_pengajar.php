<!-- <div class="content-body"> -->
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)"></a></li> 
            <?php foreach($isilist as $periode){} ?>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Kehadiran Bulan <?php echo $periode->bulan." ".$periode->id_bidang; ?></a> <h2><?php echo $periode->nama_lembaga; ?></h2> </li>
        </ol>
    </div>
    <!-- row -->
    <div class="row">
       <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                <a href="<?php echo base_url() ?>upload/<?php echo $periode->file; ?>" target="_blank" class="btn btn-twitter me-1 px-3"><i class="fa fa-file m-0"></i> Lihat File Absensi</a>
                <a href="<?php echo base_url() ?>Kehadiran/Cetak_pengajar/<?php echo $periode->id_kehadiran_lembaga; ?>" class="btn btn-success  right" target="_blank"><i class="fas fa-print"></i> Rincian Barokah</a>
                <a href="<?php echo base_url() ?>Kehadiran/Cetak_Potongan_pengajar/<?php echo $periode->id_lembaga; ?>" class="btn btn-danger " target="_blank"><i class="fas fa-print"></i> Potongan</a>
            </div>
            
                <form action="#" id="form">
                    <div class="card-body">
                        <div class="table-responsive table-container">
                            <table id="tabel_rek" class="table table-striped table-responsive-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th scope="col">Nama Lengkap</th>
                                        <th scope="col">IK</th>
                                        <th scope="col">IT</th>
                                        <th scope="col">TMT</th>
                                        <th scope="col">MP</th>
                                        <th scope="col">Jam/SKS</th>
                                        <th scope="col">Rank</th>
                                        <th scope="col">Mengajar</th>
                                        <th scope="col">DTY</th>
                                        <th scope="col">Jafung</th>
                                        <th colspan="2" class="text-center" scope="col">Kehadiran Normal</th>
                                        <th colspan="2" class="text-center" scope="col">Kehadiran 15.000</th>
                                        <th colspan="2" class="text-center" scope="col">Kehadiran 10.000</th>
                                        <th colspan="3" class="text-center" scope="col">Jam Piket</th>
                                        <th scope="col">Tunkel</th>
                                        <th scope="col">Tun Anak</th>
                                        <th scope="col">Kehormatan</th>
                                        <th scope="col">Walkes</th>
                                        <th scope="col">Tambahan</th>
                                        <th scope="col">Jumlah</th>
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
                                            $nominal_hadir_15 = $key->jumlah_hadir_15 * 15000;
                                            $nominal_hadir_10 = $key->jumlah_hadir_10 * 10000;
                                            $tahun_madrasah = 2025;
                                            $tahun_sekolah = 2025;
                                            $tahun_pt = 2025;
                                            
                                            if (($key->kategori == 'GTY' && $key->id_bidang == "Bidang DIKJAR-M") || ($key->kategori == 'GTT' && $key->id_bidang == "Bidang DIKJAR-M")) {
                                                $mp = $tahun_madrasah - date("Y", strtotime($key->tmt_guru));
                                                if ($mp == 0) {
                                                    $masa_p = 0;
                                                } else {
                                                    $masa_p = $mp;
                                                }
                                            } elseif (($key->kategori == 'GTY' && $key->id_bidang == "Bidang DIKJAR") || ($key->kategori == 'GTT' && $key->id_bidang == "Bidang DIKJAR")) {
                                                $mp = $tahun_sekolah - date("Y", strtotime($key->tmt_guru));
                                                if ($mp == 0) {
                                                    $masa_p = 0;
                                                } else {
                                                    $masa_p = $mp;
                                                }
                                            } elseif (($key->kategori == 'DTY' && $key->nama_lembaga == "Ma'had Aly Sukorejo") || ($key->kategori == 'DTT' && $key->nama_lembaga == "Ma'had Aly Sukorejo")) {
                                                $mp = $tahun_pt - date("Y", strtotime($key->tmt_maif));
                                                if ($mp == 0) {
                                                    $masa_p = 0;
                                                } else {
                                                    $masa_p = $mp;
                                                }
                                            }else {
                                                $mp = $tahun_pt - date("Y", strtotime($key->tmt_dosen));
                                                if ($mp == 0) {
                                                    $masa_p = 0;
                                                } else {
                                                    $masa_p = $mp;
                                                }
                                            }
                                            
                                            
                                            
                                            if ($key->tunj_kel == "Ya" and $mp >= 2){
                                                $tunkel = $nominaltunkel->besaran_tunkel;
                                            } else {
                                                $tunkel = 0;
                                            }
                                            
                                            if ($key->status_aktif == "Cuti 50%") {
                                                $tunkel *= 0.5; // Mengurangi masa_p sebesar 50%
                                            } elseif ($key->status_aktif == "Cuti 100%") {
                                                $tunkel = 0; // Mengatur masa_p menjadi 0 untuk cuti 100%
                                            }

                                            
                                            //tunjangan anak
                                            if ($key->tunj_anak == "Ya" ){
                                                $tunja_anak = $nominaltunj_anak->nominal_tunj_anak;
                                            } else {
                                                $tunja_anak = 0;
                                            }
                                            
                                            if ($key->status_aktif == "Cuti 50%") {
                                                $tunja_anak *= 0.5; // Mengurangi masa_p sebesar 50%
                                            } elseif ($key->status_aktif == "Cuti 100%") {
                                                $tunja_anak = 0; // Mengatur masa_p menjadi 0 untuk cuti 100%
                                            }

                                            //barokah walikelas
                                            if ($key->walkes == "Ya" ){
                                                $tunj_walkes = 75000;
                                            } else if($key->walkes == "walkes_sklh") {
                                                $tunj_walkes = 50000;
                                            } else {
                                                $tunj_walkes = 0;
                                            }
                                            
                                            if ($key->status_aktif == "Cuti 50%") {
                                                $tunj_walkes *= 0.5; // Mengurangi masa_p sebesar 50%
                                            } elseif ($key->status_aktif == "Cuti 100%") {
                                                $tunj_walkes = 0; // Mengatur masa_p menjadi 0 untuk cuti 100%
                                            }
                                            
                                            if ($key->kategori == 'GTY' or $key->kategori == 'GTT'){
                                                $hitung_rank = $this->db->query("select nominal from barokah_pengajar where min_tmp_mengajar <= $masa_p and max_tmp_mengajar >= $masa_p and ijazah = '$key->ijazah_terakhir' and kategori = 'Guru' ")->result();
                                                foreach($hitung_rank as $nilai_rank) {
                                                    $rank = $nilai_rank->nominal;
                                                }
                                            } else {
                                                if ($key->id_lembaga == '39'){
                                                     $hitung_rank = $this->db->query("select nominal from barokah_pengajar where min_tmp_mengajar <= $masa_p and max_tmp_mengajar >= $masa_p and ijazah = '$key->ijazah_terakhir' and kategori = 'Dosen MAIF' ")->result();
                                                    foreach($hitung_rank as $nilai_rank) {
                                                        $rank = $nilai_rank->nominal;
                                                    }
                                                } elseif ($key->id_lembaga == '37') {
                                                    $hitung_rank = $this->db->query("select nominal from barokah_pengajar where min_tmp_mengajar <= $masa_p and max_tmp_mengajar >= $masa_p and ijazah = '$key->ijazah_terakhir' and kategori = 'Dosen FIK' ")->result();
                                                    foreach($hitung_rank as $nilai_rank) {
                                                        $rank = $nilai_rank->nominal;
                                                    }
                                                } else {
                                                    $hitung_rank = $this->db->query("select nominal from barokah_pengajar where min_tmp_mengajar <= $masa_p and max_tmp_mengajar >= $masa_p and ijazah = '$key->ijazah_terakhir' and kategori = 'Dosen UNIB' ")->result();
                                                    foreach($hitung_rank as $nilai_rank) {
                                                        $rank = $nilai_rank->nominal;
                                                    }
                                                }
                                            }
                                            
                                            $mengajar = $rank * $key->jumlah_sks;
                                            if ($key->status_aktif == "Cuti 50%") {
                                                $mengajar *= 0.5; // Mengurangi masa_p sebesar 50%
                                            } elseif ($key->status_aktif == "Cuti 100%") {
                                                $mengajar = 0; // Mengatur masa_p menjadi 0 untuk cuti 100%
                                            }
                                            $rank_piket = $rank / 4;
                                            $barokah_piket = $rank_piket * $key->jumlah_hadir_piket;
                                           
                                            if ($key->kategori == 'GTY' || $key->kategori == 'GTT'){
                                                $hitung_kehormatan = $this->db->query("select nominal from barokah_kehormatan_pengajar where min_masa_pengabdian <= $mp and max_masa_pengabdian >= $mp and kategori = 'Guru' ")->result();
                                                if(!empty($hitung_kehormatan) and $key->kehormatan == 'Ya') {
                                                    foreach($hitung_kehormatan as $nilai_kehormatan) {
                                                        $kehormatan = $nilai_kehormatan->nominal;
                                                    }
                                                } else {
                                                    $kehormatan = 0; // atau dapat juga menghasilkan pesan error atau log error
                                                }
                                            } else {
                                                $hitung_kehormatan = $this->db->query("select nominal from barokah_kehormatan_pengajar where min_masa_pengabdian <= $mp and max_masa_pengabdian >= $mp and kategori = 'Dosen' ")->result();
                                                
                                                if(!empty($hitung_kehormatan) and $key->kehormatan == 'Ya') {
                                                    foreach($hitung_kehormatan as $nilai_kehormatan) {
                                                        $kehormatan = $nilai_kehormatan->nominal;
                                                    }
                                                } else {
                                                    $kehormatan = 0; // atau dapat juga menghasilkan pesan error atau log error
                                                }
                                            }
                                            
                                            if ($key->status_aktif == "Cuti 50%") {
                                                $kehormatan *= 0.5; // Mengurangi masa_p sebesar 50%
                                            } elseif ($key->status_aktif == "Cuti 100%") {
                                                $kehormatan = 0; // Mengatur masa_p menjadi 0 untuk cuti 100%
                                            }

                                            if ($key->kategori == 'GTY' and $key->status_sertifikasi == 'Belum' and $masa_p > 2 ){
                                                $hitung_dty = $this->db->query("SELECT nominal from barokah_pengajar_tetap where kategori = 'Guru' ")->result();
                                                if(!empty($hitung_dty)) {
                                                    foreach($hitung_dty as $nilai_dty) {
                                                        $dty = $nilai_dty->nominal;
                                                    }
                                                } else {
                                                    $dty = 0; // atau dapat juga menghasilkan pesan error atau log error
                                                }
                                            } else if ($key->kategori == 'DTY' and $key->status_sertifikasi == 'Belum' and $masa_p > 2) {
                                                $hitung_dty = $this->db->query("SELECT nominal from barokah_pengajar_tetap where kategori = 'Dosen' ")->result();
                                                if(!empty($hitung_dty)) {
                                                    foreach($hitung_dty as $nilai_dty) {
                                                        $dty = $nilai_dty->nominal;
                                                    }
                                                } else {
                                                    $dty = 0; // atau dapat juga menghasilkan pesan error atau log error
                                                }
                                            } else {
                                                $dty = 0;
                                            }
                                            
                                             if ($key->status_aktif == "Cuti 50%") {
                                                $dty *= 0.5; // Mengurangi masa_p sebesar 50%
                                            } elseif ($key->status_aktif == "Cuti 100%") {
                                                $dty = 0; // Mengatur masa_p menjadi 0 untuk cuti 100%
                                            }
                                            
                                            if ($key->jabatan_akademik != '' and $key->jafung == 'Ya' ) {
                                                $hitung_jafung = $this->db->query("SELECT nominal from barokah_jafung, umana where umana.jabatan_akademik = barokah_jafung.id_barokah_jafung and umana.jabatan_akademik = $key->jabatan_akademik  ")->result();
                                            
                                                if(!empty($hitung_jafung)) {
                                                    foreach($hitung_jafung as $nilai_jafung) {
                                                        $jafung = $nilai_jafung->nominal;
                                                    }
                                                } else {
                                                    $jafung = 0; // atau dapat juga menghasilkan pesan error atau log error
                                                }
                                            } else {
                                                $jafung = 0;
                                            }
                                            
                                            if ($key->status_aktif == "Cuti 50%") {
                                                $jafung *= 0.5; // Mengurangi masa_p sebesar 50%
                                            } elseif ($key->status_aktif == "Cuti 100%") {
                                                $jafung = 0; // Mengatur masa_p menjadi 0 untuk cuti 100%
                                            }
                                            
                                             $hitung_potongan = $this->db->query("SELECT SUM(nominal_potongan) as jumlah from potongan_pengajar, pengajar where potongan_pengajar.id_pengajar = pengajar.id_pengajar and potongan_pengajar.id_pengajar = $key->id_pengajar and potongan_pengajar.max_periode_potongan >= CURDATE() ")->result();
                                           
                                            if(!empty($hitung_potongan)) {
                                                foreach($hitung_potongan as $jumlah_potongan) {
                                                    $potongan = $jumlah_potongan->jumlah;
                                                }
                                            } else {
                                                $potongan = 0; // atau dapat juga menghasilkan pesan error atau log error
                                            }
                                            
                                            $hitung_tambahan = $this->db->query("SELECT SUM(nominal_tambahan) as jumlah from barokah_tambahan, pengajar where barokah_tambahan.id_pengajar = pengajar.id_pengajar and barokah_tambahan.id_pengajar = $key->id_pengajar and barokah_tambahan.max_periode_tambahan >= CURDATE() ")->result();
                                           
                                            if(!empty($hitung_tambahan)) {
                                                foreach($hitung_tambahan as $jumlah_tambahan) {
                                                    $tambahan = $jumlah_tambahan->jumlah;
                                                }
                                            } else {
                                                $tambahan = 0; // atau dapat juga menghasilkan pesan error atau log error
                                            }

                                            // $potongan = 0;

                                            // ========================== HITUNG TMP   =====================================================================
                                            $tmp = 0;
                                            // jika sudah 3 tahun, naikkan tmp sebesar 10.000
                                            
                                            $tahunSekarang = 2024; // mendapatkan tahun sekarang
                                            // $tahunKerja = $tahunSekarang - date("Y", strtotime($key->tmt_dosen)); // menghitung masa kerja dalam tahun
                                            if ($mp >= 3 ) {
                                              $kenaikanGaji = floor($mp / 3) * 10000; // menghitung jumlah kenaikan gaji
                                              $tmp_Akhir = $tmp + $kenaikanGaji; // menghitung gaji saat ini
                                            } else {
                                              $tmp_Akhir = $tmp; // jika belum waktunya kenaikan gaji, gaji tetap sama dengan awal
                                            }
                                            //   ==================================================================================================================  
                                            
                                            $diterima = $barokah_piket + $jml_kehadiran + $nominal_hadir_15 + $nominal_hadir_10 + $tunkel + $tunja_anak + $mengajar + $dty + $jafung + $kehormatan + $tunj_walkes + $tambahan - $potongan;
                                            $jumlah = $barokah_piket + $jml_kehadiran + $nominal_hadir_15 + $nominal_hadir_10 + $tunkel + $tunja_anak + $mengajar + $dty + $jafung + $kehormatan + $tunj_walkes + $tambahan ;
                                            
                                            $jumlah_tot += $jumlah;
                                            $jumlah_total += $diterima;
                                            $jumlah_mengajar += $mengajar;
                                            $jumlah_dty += $dty;
                                            $jumlah_jafung += $jafung;
                                            $jumlah_kehadiran += $jml_kehadiran;
                                            $jumlah_kehadiran_15 += $nominal_hadir_15;
                                            $jumlah_kehadiran_10 += $nominal_hadir_10;
                                            $jumlah_kehadiran_piket += $barokah_piket;
                                            
                                            $jumlah_tunkel += $tunkel;
                                            $jumlah_tunanak += $tunja_anak;
                                            $jumlah_tmp += $tmp_Akhir;
                                            $jumlah_kehormatan += $kehormatan;
                                            $jumlah_walkes += $tunj_walkes;
                                            $jumlah_bk += $tambahan;
                                            $jumlah_potong += (int)$potongan;
                                            
                                            $list_potongan = $this->db->query("SELECT id_potongan_pengajar, nama_potongan, nominal_potongan as jumlah from potongan_pengajar, pengajar, potongan where potongan_pengajar.jenis_potongan = potongan.id_potongan and potongan_pengajar.id_pengajar = pengajar.id_pengajar and potongan_pengajar.id_pengajar = 10
                                            and potongan_pengajar.max_periode_potongan >= CURDATE()")->result();
                                            foreach ($list_potongan as $list){
                                                ?>
                                            <input type="hidden" name="" value="<?php echo $key->id_pengajar ?>">
                                            <input type="hidden" name="id_potongan_umana[]" value="<?php echo $list->id_potongan_pengajar ?>">
                                            <input type="hidden" name="" value="<?php echo $list->jumlah ?>">
                                            <?php };
                                    ?>
                                    <tr>
                                        <td>
                                            <?php echo $no++; ?>
                                            <input type="hidden" name="bulan[]" value="<?php echo $key->bulan ?>">
                                            <input type="hidden" name="tahun[]" value="<?php echo $key->tahun ?>">
                                            <input type="text" name="id_kehadiran_lembaga" value="<?php echo $key->id_kehadiran_lembaga ?>">
                                            <input type="hidden" name="id_kehadiran" value="<?php echo $key->id_kehadiran_pengajar ?>">
                                        </td>
                                        <td>
                                            <?php 
                                                // if($key->status == "Terkirim" and $this->session->userdata('jabatan') != 'AdminLembaga'){
                                                if($key->status == "Terkirim" and $this->session->userdata('jabatan') != 'AdminLembaga' and $this->session->userdata('jabatan') != 'SDM'){
                                            ?>
                                                <a class="text-success" onclick="detail_pengajar('<?php echo $key->id_kehadiran_pengajar; ?>')"  href="#"><?php echo htmlentities($key->gelar_depan.' '.$key->nama_lengkap.' '.$key->gelar_belakang); ?></a>    
                                            <?php } else {
                                            ?>
                                                 <span class="text-success"><?php echo htmlentities($key->gelar_depan.' '.$key->nama_lengkap.' '.$key->gelar_belakang); ?></span>
                                            <?php } ?>
                                            <?php if($key->status_aktif == "Cuti 50%" or $key->status_aktif == "Cuti 100%"){ ?>
                                            <span class="text-danger"> (<?php echo htmlentities($key->status_aktif); ?>)</span>
                                            <?php }?> 
                                            <input type="hidden" name="nik_umana[]" value="<?php echo $key->id_pengajar ?>">
                                        </td>
                                        <td>
                                            <?php echo htmlentities($key->kategori); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlentities($key->ijazah_terakhir); ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (
                                                ($key->kategori == 'DTY' && $key->nama_lembaga != "Ma'had Aly Sukorejo") ||
                                                ($key->kategori == 'DTT' && $key->nama_lembaga != "Ma'had Aly Sukorejo")
                                            ) {
                                                $tmt = date("Y", strtotime($key->tmt_dosen));
                                            } elseif (
                                                ($key->kategori == 'DTY' && $key->nama_lembaga == "Ma'had Aly Sukorejo") ||
                                                ($key->kategori == 'DTT' && $key->nama_lembaga == "Ma'had Aly Sukorejo")
                                            ) {
                                                $tmt = date("Y", strtotime($key->tmt_maif));
                                            } else {
                                                $tmt = date("Y", strtotime($key->tmt_guru));
                                            }
                                            echo $tmt;
                                            ?>
                                    
                                        </td>
                                        <td>
                                            <?php echo htmlentities($masa_p); ?>
                                             <input type="hidden" name="mp[]" value="<?php echo $masa_p ?>">
                                        </td>
                                        <td>
                                            <?php echo htmlentities($key->jumlah_sks); ?>
                                            <input type="hidden" name="jumlah_sks[]" value="<?php echo $key->jumlah_sks ?>">
                                        </td>
                                        <td>
                                            <?php echo htmlentities(rupiah($rank)); ?>
                                            <input type="hidden" name="rank[]" value="<?php echo $rank ?>">
                                        </td>
                                        <td>
                                            <?php if($key->status_aktif == "Cuti 50%" or $key->status_aktif == "Cuti 100%"){ ?>
                                            <span class="text-danger"><?php echo htmlentities(rupiah($mengajar)); ?></span>
                                            <?php } else { ?>
                                            <?php 
                                            echo htmlentities(rupiah($mengajar)); } ?>
                                            <input type="hidden" name="mengajar[]" value="<?php echo $mengajar ?>">
                                        </td>
                                        <td>
                                            <?php if($key->status_aktif == "Cuti 50%" or $key->status_aktif == "Cuti 100%"){ ?>
                                            <span class="text-danger"><?php echo htmlentities(rupiah($dty)); ?></span>
                                            <?php } else { ?>
                                            <?php echo htmlentities(rupiah($dty)); }?>
                                            <input type="hidden" name="dty[]" value="<?php echo $dty ?>">
                                        </td>
                                        <td>
                                             <?php if($key->status_aktif == "Cuti 50%" or $key->status_aktif == "Cuti 100%"){ ?>
                                            <span class="text-danger"><?php echo htmlentities(rupiah($jafung)); ?></span>
                                            <?php } else { ?>
                                            <?php echo htmlentities(rupiah($jafung)); } ?>
                                            <input type="hidden" name="jafung[]" value="<?php echo $jafung ?>">
                                        </td>
                                         <!-- //hadir normal -->
                                        <td>
                                            <?php echo htmlentities($key->jumlah_hadir); ?>
                                            <input type="hidden" name="jml_hadir[]" value="<?php echo $key->jumlah_hadir ?>">
                                        </td>
                                        <td>
                                            <?php echo htmlentities(rupiah($jml_kehadiran)); ?>
                                            <input type="hidden" name="nominal_kehadiran[]" value="<?php echo $jml_kehadiran ?>">
                                        </td>

                                        <!-- hadir 15.000 -->
                                        <td>
                                            <?php echo htmlentities($key->jumlah_hadir_15); ?>
                                            <input type="hidden" name="jml_hadir_15[]" value="<?php echo $key->jumlah_hadir_15 ?>">
                                        </td>
                                        <td>
                                            <?php echo htmlentities(rupiah($nominal_hadir_15)); ?>
                                            <input type="hidden" name="nominal_kehadiran_15[]" value="<?php echo $nominal_hadir_15 ?>">
                                        </td>

                                        <!-- hadir 10.000 -->
                                        <td>
                                            <?php echo htmlentities($key->jumlah_hadir_10); ?>
                                            <input type="hidden" name="jml_hadir_10[]" value="<?php echo $key->jumlah_hadir_10 ?>">
                                        </td>
                                        <td>
                                            <?php echo htmlentities(rupiah($nominal_hadir_10)); ?>
                                            <input type="hidden" name="nominal_kehadiran_10[]" value="<?php echo $nominal_hadir_10 ?>">
                                        </td>
                                        
                                         <!-- hadir guru piket -->
                                        <td>
                                            <?php echo htmlentities($key->jumlah_hadir_piket); ?>
                                            <input type="hidden" name="jumlah_hadir_piket[]" value="<?php echo $key->jumlah_hadir_piket ?>">
                                        </td>
                                        <td>
                                            X <?php echo htmlentities(rupiah($rank_piket)); ?>
                                            <input type="hidden" name="rank_piket[]" value="<?php echo $rank_piket ?>">
                                        </td>
                                        <td>
                                            <?php echo htmlentities(rupiah($barokah_piket)); ?>
                                            <input type="hidden" name="barokah_piket[]" value="<?php echo $barokah_piket ?>">
                                        </td>


                                        <td>
                                            <?php if($key->status_aktif == "Cuti 50%" or $key->status_aktif == "Cuti 100%"){ ?>
                                            <span class="text-danger"><?php echo htmlentities(rupiah($tunkel)); ?></span>
                                            <?php } else { ?>
                                            <?php echo htmlentities(rupiah($tunkel)); }?>
                                            <input type="hidden" name="tunkel[]" value="<?php echo $tunkel ?>">
                                        </td>
                                        <td>
                                            <?php if($key->status_aktif == "Cuti 50%" or $key->status_aktif == "Cuti 100%"){ ?>
                                            <span class="text-danger"><?php echo htmlentities(rupiah($tunja_anak)); ?></span>
                                            <?php } else { ?>
                                            <?php echo htmlentities(rupiah($tunja_anak)); } ?> 
                                            <input type="hidden" name="tunj_anak[]" value="<?php echo $tunja_anak ?>">
                                        </td>
                                        <td>
                                            <?php if($key->status_aktif == "Cuti 50%" or $key->status_aktif == "Cuti 100%"){ ?>
                                            <span class="text-danger"><?php echo htmlentities(rupiah($kehormatan)); ?></span>
                                            <?php } else { ?>
                                            <?php echo htmlentities(rupiah($kehormatan)); }?>
                                            <input type="hidden" name="kehormatan[]" value="<?php echo $kehormatan ?>">
                                        </td>
                                        <td>
                                            <?php if($key->status_aktif == "Cuti 50%" or $key->status_aktif == "Cuti 100%"){ ?>
                                            <span class="text-danger"><?php echo htmlentities(rupiah($tunj_walkes)); ?></span>
                                            <?php } else { ?>
                                            <?php echo htmlentities(rupiah($tunj_walkes)); }?>
                                            <input type="hidden" name="tunj_walkes[]" value="<?php echo $tunj_walkes ?>">
                                        </td>
                                        <td>
                                            <a class="text-primary" href="#" onclick="detail_bk('<?php echo $key->id_pengajar; ?>')"><?php echo htmlentities(rupiah($tambahan)); ?></a>
                                            <input type="hidden" name="bk[]" value="<?php echo $tambahan ?>">
                                        </td>
                                        <td>
                                            <span class="text-primary" href="#" ><?php echo htmlentities(rupiah($jumlah)); ?></span>
                                        </td>
                                        <td>
                                        <a class="text-primary" href="#" ><?php echo htmlentities(rupiah($potongan)); ?></a>
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
                                    <td colspan="8"></td>
                                    <td><?php echo htmlentities(rupiah($jumlah_mengajar)); ?></td> 
                                    <td><?php echo htmlentities(rupiah($jumlah_dty)); ?></td>
                                    <td><?php echo htmlentities(rupiah($jumlah_jafung)); ?></td>
                                    <td colspan="2">
                                        <?php echo htmlentities(rupiah($jumlah_kehadiran)); ?>
                                    </td>
                                    <td colspan="2">
                                        <?php echo htmlentities(rupiah($jumlah_kehadiran_15)); ?>
                                    </td>
                                    <td colspan="2">
                                        <?php echo htmlentities(rupiah($jumlah_kehadiran_10)); ?>
                                    </td>
                                     <td colspan="3">
                                        <?php echo htmlentities(rupiah($jumlah_kehadiran_piket)); ?>
                                    </td>
                                    <td><?php echo htmlentities(rupiah($jumlah_tunkel)); ?></td>
                                    <td>
                                        <?php echo htmlentities(rupiah($jumlah_tunanak)); ?>
                                    </td>
                                    <td><?php echo htmlentities(rupiah($jumlah_kehormatan)); ?></td>
                                    <td><?php echo htmlentities(rupiah($jumlah_walkes)); ?></td>
                                    <td><?php echo htmlentities(rupiah($jumlah_bk)); ?></td>
                                    <td><?php echo htmlentities(rupiah($jumlah_tot)); ?></td>
                                    <td><?php echo htmlentities(rupiah($jumlah_potong)); ?></td>
                                    <td><?php echo htmlentities(rupiah($jumlah_total)); ?>
                                    <input type="text" name="jumlah_total" value="<?php echo $jumlah_total ?>"></td>
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
                            <button type="submit" id="btnSave" onclick="simpan_pengajar()" class="btn btn-rounded btn-primary mb-9" ><span class="btn-icon-start text-primary"><i class="fa fa-check"></i>
                            </span>Setujui</button>

                            <!--<a href="#" type="submit" onclick="add_catatan()" class="btn btn-rounded btn-info mb-9" ><span class="btn-icon-start text-info"><i class="fa fa-plus"></i>-->
                            <!--</span>Tambah Catatan</a>-->
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