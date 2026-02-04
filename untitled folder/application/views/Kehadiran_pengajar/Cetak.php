<?php 
    class CustomPDF extends FPDF {
        private $isFirstPage = true;  
    function Header() {
        if ($this->PageNo() > 1) {
        $this->Cell(1,7,'',0,1);
        $this->SetFont('arial','B',6);
        // $this->SetFillColor(128, 128, 128);
       
        $this->Cell(5,7,'N0',1,0,'C');
            $this->Cell(45,7,'NAMA LENGKAP',1,0,'C');
            $this->Cell(8,7,'IK',1,0,'C');
            $this->Cell(8,7,'IT',1,0,'C');
            $this->Cell(8,7,'TMT',1,0,'C');
            $this->Cell(5,7,'MP',1,0,'C');
            $this->Cell(12,7,'JAM/SKS',1,0,'C');
            $this->Cell(10,7,'RANK',1,0,'C');
            $this->SetFont('arial','B',5);
            $this->Cell(12,7,'MENGAJAR',1,0,'C');
            $this->SetFont('arial','B',6);
            $this->Cell(10,7,'DTY',1,0,'C');
            $this->Cell(12,7,'JAFUNG',1,0,'C');
            $this->Cell(17,7,'TRANSPORT',1,0,'C');
            $this->Cell(17,7,'KEHADIRAN 15',1,0,'C');
            $this->Cell(17,7,'KEHADIRAN 10',1,0,'C');
            $this->SetFont('arial','B',6);
            $this->Cell(24,7,'KEHADIRAN PIKET',1,0,'C');
            $this->Cell(12,7,'TUNKEL',1,0,'C');
            $this->Cell(12,7,'TUNANAK',1,0,'C');
            $this->SetFont('arial','B',6);
            $this->Cell(15,7,'KEHORMATAN',1,0,'C');
            $this->SetFont('arial','B',7);
            $this->Cell(15,7,'WALKES',1,0,'C');
            $this->Cell(15,7,'TAMBAHAN',1,0,'C');
            $this->Cell(15,7,'JUMLAH',1,0,'C');
            $this->Cell(15,7,'POTONGAN',1,0,'C');
            $this->Cell(20,7,'DITERIMA',1,0,'C');
        $this->Cell(0,1,'',0,1);
        }
        if ($this->PageNo() > 1) {
            // $this->SetFont('arial', 'I', 8);
            // $this->Cell(0, 10, 'Elemen ini hanya ditampilkan pada lembar kedua dan seterusnya', 0, 1, 'C');
            $this->setY(25);
           
        }
        }
    }
        $pdf = new CustomPDF('L','mm','LEGAL');
        $pdf->AddFont('bookman','','bookman-old-style.php');
        $pdf->AddFont('tahoma','B','tahomabd.php');
        $pdf->AddFont('tahoma','','tahoma.php');
        $pdf->AddFont('bookatik','B','book-antiqua.php');

        $pdf->SetFont('Times','B',16);
        $pdf->AddPage();

        $pdf->Image('assets/p2s2.png',10,11,30);
        // Title
        $pdf->ln(4);
        $pdf->SetFont('tahoma','B',13);
        $pdf->Cell(31,0,'','0','0','L',false);
        $pdf->Cell(150,1,"PONDOK PESANTREN SALAFIYAH SYAFI'IYAH SUKOREJO",'0','1','L',false);

        $pdf->Ln(4);
        $pdf->SetFont('tahoma','',11);
        $pdf->Cell(31,0,'','0','0','L',false);
        $pdf->Cell(150,1,'SUMBEREJO BANYUPUTIH SITUBONDO JAWA TIMUR','0','1','L',false);

        $pdf->Ln(5);
        $pdf->SetFont('tahoma','',8);
        $pdf->Cell(40,0,'Po Box 2 telp 0388-452666 Fax. 452707 - Situbondo, 68374','0','0','L',false);

        $pdf->Line(9,23,340,23);
        $pdf->Line(9,27,340,27);


        foreach ($isilist as $periode) {}

            $pdf->ln(5);
            $pdf->SetFont('arial', 'B', 10);
            $pdf->Cell(200, 2, strtoupper($periode->nama_lembaga), '0', '0', 'L', false);
            $pdf->Cell(50, 2, '', '0', '0', 'L', false);
            if ($periode->id_bidang == "Bidang DIKTI"){
                $pdf->Cell(50, 2,'Barokah Dosen', '0', '1', 'L', false);
            } else {
                $pdf->Cell(50, 2,'Barokah Guru', '0', '1', 'L', false);
            }
        
            $pdf->ln(2);
            $pdf->SetFont('arial', '', 10);
            $pdf->Cell(200, 2, '', '0', '0', 'L', false);
            $pdf->Cell(50, 2, '', '0', '0', 'L', false);
            $pdf->Cell(50, 2, 'Bulan : '.$periode->bulan.' '.$periode->tahun, '0', '1', 'L', false);

            $pdf->Cell(1,7,'',0,1);
            $pdf->SetFont('arial','B',6);
            // $pdf->SetFillColor(128, 128, 128);
            $pdf->Cell(5,7,'N0',1,0,'C');
            $pdf->Cell(45,7,'NAMA LENGKAP',1,0,'C');
            $pdf->Cell(8,7,'IK',1,0,'C');
            $pdf->Cell(8,7,'IT',1,0,'C');
            $pdf->Cell(8,7,'TMT',1,0,'C');
            $pdf->Cell(5,7,'MP',1,0,'C');
            $pdf->Cell(12,7,'JAM/SKS',1,0,'C');
            $pdf->Cell(10,7,'RANK',1,0,'C');
            $pdf->SetFont('arial','B',5);
            $pdf->Cell(12,7,'MENGAJAR',1,0,'C');
            $pdf->SetFont('arial','B',6);
            $pdf->Cell(10,7,'DTY',1,0,'C');
            $pdf->Cell(12,7,'JAFUNG',1,0,'C');
            $pdf->Cell(17,7,'TRANSPORT',1,0,'C');
            $pdf->Cell(17,7,'KEHADIRAN 15',1,0,'C');
            $pdf->Cell(17,7,'KEHADIRAN 10',1,0,'C');
            $pdf->SetFont('arial','B',6);
            $pdf->Cell(24,7,'KEHADIRAN PIKET',1,0,'C');
            $pdf->Cell(12,7,'TUNKEL',1,0,'C');
            $pdf->Cell(12,7,'TUNANAK',1,0,'C');
            $pdf->SetFont('arial','B',6);
            $pdf->Cell(15,7,'KEHORMATAN',1,0,'C');
            $pdf->SetFont('arial','B',7);
            $pdf->Cell(15,7,'WALKES',1,0,'C');
            $pdf->Cell(15,7,'TAMBAHAN',1,0,'C');
            $pdf->Cell(15,7,'JUMLAH',1,0,'C');
            $pdf->Cell(15,7,'POTONGAN',1,0,'C');
            $pdf->Cell(20,7,'DITERIMA',1,0,'C');
            $pdf->Cell(0,1,'',0,1);
            
            $no = 1;
            
            $jumlah_total = 0;
            $jumlah_mengajar = 0;
            $jumlah_dty = 0;
            $jumlah_jafung = 0;
            $jumlah_kehadiran = 0;
            $jumlah_kehadiran_15 = 0;
            $jumlah_kehadiran_10 = 0;
            $jumlah_kehadiran_piket = 0;
            $jumlah_tunkel = 0;
            $jumlah_tunanak = 0;
            $jumlah_tmp = 0;
            $jumlah_kehormatan = 0;
            $jumlah_walkes = 0;
            $jumlah_bk = 0;
            $jumlah_potong = 0;
            
            $jumlah_semua = 0;
            $jumlah_bk = 0;
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
                    
                } else {
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
                

            $pdf->Cell(1,7,'',0,1);
            $pdf->SetFont('arial','B',5);
            // $pdf->SetFillColor(128, 128, 128);
            $pdf->Cell(5,7,$no++,1,0,'C');
            if($key->status_aktif == "Cuti 50%" or $key->status_aktif == "Cuti 100%"){
            $pdf->SetTextColor(255, 0, 0); // Mengatur warna teks menjadi merah
            $pdf->Cell(45, 7, $key->gelar_depan . ' ' . strtoupper($key->nama_lengkap) . ' ' . $key->gelar_belakang . ' (' . $key->status_aktif . ')', 1, 0, 'L');
            $pdf->SetTextColor(0, 0, 0); // Mengembalikan warna teks ke hitam (opsional)
            } else {
                $pdf->Cell(45,7,$key->gelar_depan.' '.strtoupper($key->nama_lengkap).' '.$key->gelar_belakang,1,0,'L');
            }
            
            // Cek kondisi status aktif
            if ($key->status_aktif == "Cuti 50%" || $key->status_aktif == "Cuti 100%") {
                $pdf->SetTextColor(255, 0, 0); // Mengatur warna teks menjadi merah
            } else {
                $pdf->SetTextColor(0, 0, 0); // Mengembalikan warna teks ke hitam
            }
            $pdf->Cell(8,7,$key->kategori,1,0,'C');
            $pdf->Cell(8,7,$key->ijazah_terakhir,1,0,'C');
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
            
            $pdf->Cell(8, 7, $tmt, 1, 0, 'C');

            $pdf->Cell(5,7,$masa_p,1,0,'C');
            $pdf->Cell(12,7,$key->jumlah_sks,1,0,'C');
            $pdf->Cell(10,7,rupiah($rank),1,0,'C');
            $pdf->Cell(12,7,rupiah($mengajar),1,0,'C');
            $pdf->Cell(10,7,rupiah($dty),1,0,'C');
            $pdf->Cell(12,7,rupiah($jafung),1,0,'C');
            $pdf->Cell(5,7,$key->jumlah_hadir,1,0,'C');
            $pdf->Cell(12,7,rupiah($jml_kehadiran),1,0,'C');
            $pdf->Cell(5,7,$key->jumlah_hadir_15,1,0,'C');
            $pdf->Cell(12,7,rupiah($jml_kehadiran_15),1,0,'C');
            $pdf->Cell(5,7,$key->jumlah_hadir_10,1,0,'C');
            $pdf->Cell(12,7,rupiah($jml_kehadiran_10),1,0,'C');
            $pdf->Cell(4,7,$key->jumlah_hadir_piket,1,0,'C');
            $pdf->Cell(10,7,'x '.rupiah($rank_piket),1,0,'C');
            $pdf->Cell(10,7,rupiah($barokah_piket),1,0,'C');
            $pdf->Cell(12,7,rupiah($tunkel),1,0,'C');
            $pdf->Cell(12,7,rupiah($tunja_anak),1,0,'C');
            $pdf->Cell(15,7,rupiah($kehormatan),1,0,'C');
            $pdf->Cell(15,7,rupiah($tunj_walkes),1,0,'C');
            $pdf->Cell(15,7,rupiah($tambahan),1,0,'C');
            $pdf->Cell(15,7,rupiah($jumlah),1,0,'C');
            $pdf->Cell(15,7,rupiah($potongan),1,0,'C');
            $pdf->Cell(20,7,rupiah($diterima),1,0,'C');
            // Menambahkan baris baru
            $pdf->Cell(0, 0, '', 0, 1);
            $pdf->Cell(0, 0, '', 0, 1);
            
            // Mengembalikan warna teks ke hitam untuk baris berikutnya
            $pdf->SetTextColor(0, 0, 0);
            // $jumlah_tambahan = 0;

            $jumlah_total += $diterima;
            $jumlah_semua += $jumlah;
            $jumlah_mengajar += $mengajar;
            $jumlah_dty += $dty;
            $jumlah_jafung += $jafung;
            $jumlah_kehadiran += $jml_kehadiran;
            $jumlah_kehadiran_15 += $jml_kehadiran_15;
            $jumlah_kehadiran_10 += $jml_kehadiran_10;
            $jumlah_kehadiran_piket += $barokah_piket;
            $jumlah_tunkel += $tunkel;
            $jumlah_tunanak += $tunja_anak;
            $jumlah_kehormatan += $kehormatan;
            $jumlah_walkes += $tunj_walkes;
            $jumlah_bk += $tambahan;
            
            $jumlah_potong += (int)$potongan;
    
    }

        $pdf->ln(8);
        $pdf->Cell(101,7,'',0,0,'C');
        $pdf->Cell(12,7,rupiah($jumlah_mengajar),1,0,'C');
        $pdf->Cell(10,7,rupiah($jumlah_dty),1,0,'C');
        $pdf->Cell(12,7,rupiah($jumlah_jafung),1,0,'C');
        $pdf->Cell(17,7,rupiah($jumlah_kehadiran),1,0,'C');
        $pdf->Cell(17,7,rupiah($jumlah_kehadiran_15),1,0,'C'); //TRANSPORT 15.000
        $pdf->Cell(17,7,rupiah($jumlah_kehadiran_10),1,0,'C'); //TRANSPORT 10.000
        $pdf->Cell(24,7,rupiah($jumlah_kehadiran_piket),1,0,'C'); //PIKET
        $pdf->Cell(12,7,rupiah($jumlah_tunkel),1,0,'C');
        $pdf->Cell(12,7,rupiah($jumlah_tunanak),1,0,'C');
        $pdf->Cell(15,7,rupiah($jumlah_kehormatan),1,0,'C');
        $pdf->Cell(15,7,rupiah($jumlah_walkes),1,0,'C');
        $pdf->Cell(15,7,rupiah($jumlah_bk),1,0,'C');
        $pdf->Cell(15,7,rupiah($jumlah_semua),1,0,'C');
        $pdf->Cell(15,7,rupiah($jumlah_potong),1,0,'C');
        $pdf->Cell(20,7,rupiah($jumlah_total),1,0,'C');
        
        $pdf->ln(40);
        $tgl1=gmdate("d-m-Y");

                    $bln = date('m');
                     switch ($bln) {
                         case '1':
                             $b1 = 'Januari';
                             break;
                         case '2':
                             $b1 = 'Februari';
                             break;
                        case '3':
                            $b1 = 'Maret';
                            break;
                        case '4':
                            $b1 = 'April';
                            break;
                        case '5':
                            $b1 = 'Mei';
                            break;
                        case '6':
                            $b1 = 'Juni';
                            break;
                        case '7':
                            $b1 = 'Juli';
                            break;
                        case '8':
                            $b1 = 'Agustus';
                            break;
                        case '9':
                            $b1 = 'September';
                            break;
                        case '10':
                            $b1 = 'Oktober';
                            break;
                        case '11':
                            $b1 = 'Nopember';
                            break;
                         default:
                             $b1 = 'Desember';
                             break;
                     }
                        $tgl = date('d');
                        $thn = date('Y');

        $pdf->Ln(5);
        $pdf->SetFont('arial','i',5);
        $pdf->Cell(292,-40,' dicetak pada:, '.$tgl." " .$b1." ".$thn,0,0,'R');
        
        // $pdf->Ln(5);
        // $pdf->SetFont('arial','',12);
        // $pdf->Cell(270,-40,'Kepala Bidang,',0,0,'R');
        //  $pdf->SetFont('tahoma','B',12);
        // $pdf->Cell(14,0,'Dr. Maskuri, M.Pd.I.',0,0,'R');

        $pdf->Output();

?>