<?php 
       

        $pdf = new FPDF('L','mm','LEGAL');
        $pdf->AddFont('bookman','','bookman-old-style.php');
        $pdf->AddFont('tahoma','B','tahomabd.php');
        $pdf->AddFont('tahoma','','tahoma.php');
        $pdf->AddFont('bookatik','B','book-antiqua.php');


        // membuat halaman baru
        $pdf->AddPage();
        // setting jenis font yang akan digunakan
        $pdf->SetFont('Times','B',16);
        // mencetak string
      
        $pdf->Image('assets/p2s2.png',10,9,30);
          // Title
        $pdf->Cell(31,0,'','0','0','L',false);
        $pdf->SetFont('tahoma','B',15);
        $pdf->Cell(0,1,'KANTOR KEUANGAN','0','1','L',false);

        $pdf->ln(4);
        $pdf->SetFont('tahoma','B',13);
        $pdf->Cell(31,0,'','0','0','L',false);
        $pdf->Cell(150,1,"PONDOK PESANTREN SALAFIYAH SYAFI'IYAH",'0','1','L',false);
        // $pdf->Cell(80,0,'','0','0','L',false);
        // $pdf->Cell(50,1,"Barokah Dosen",'0','0','L',false);
    
        $pdf->Ln(4);
        $pdf->SetFont('tahoma','',11);
        $pdf->Cell(31,0,'','0','0','L',false);
        $pdf->Cell(150,1,'SUKOREJO SITUBONDO JAWA TIMUR','0','1','L',false);
        // $pdf->Cell(80,0,'','0','0','L',false);
        // $pdf->Cell(50,1,'Fakultas Sains dan Teknologi','0','1','L',false);

       
        // garis (margin kiri, margin atas, lebar, kanan)
        
        $pdf->Ln(4);    
        $pdf->SetFont('tahoma','',8);
        $pdf->Cell(40,0,'Po Box 2 telp 0388-452666 Fax. 452707 - eMail : sentral@salafiyah.net - Situbondo, 68374','0','0','L',false);
        // $pdf->Line(9,7,156,7);

        // $pdf->Line(9,7,340,7);
        $pdf->Line(9,23,340,23);
        $pdf->Line(9,27,340,27);

        foreach ($isilist as $periode) {}
            $pdf->ln(5);
            $pdf->SetFont('arial', 'B', 10);
            $pdf->Cell(200, 2,$periode->nama_lembaga, '0', '0', 'L', false);
            $pdf->Cell(50, 2, '', '0', '0', 'L', false);
            if ($periode->id_bidang == "Bidang DIKTI"){
                $pdf->Cell(50, 2,'Barokah Dosen', '0', '1', 'L', false);
            } else {
                $pdf->Cell(50, 2,'Barokah Guru', '0', '1', 'L', false);
            }
        
            $pdf->ln(2);
            $pdf->SetFont('arial', '', 10);
            $pdf->Cell(200, 2, 'Semester Ganjil - Tahun Akademik 2023/2024', '0', '0', 'L', false);
            $pdf->Cell(50, 2, '', '0', '0', 'L', false);
            $pdf->Cell(50, 2, 'Bulan : '.$periode->bulan.' '.$periode->tahun, '0', '1', 'L', false);
        
        
    
        $pdf->Cell(1,7,'',0,1);
        $pdf->SetFont('arial','B',8);
        // $pdf->SetFillColor(128, 128, 128);
        $pdf->Cell(5,7,'N0',1,0,'C');
        $pdf->Cell(40,7,'NAMA LENGKAP',1,0,'C');
        $pdf->Cell(7,7,'IK',1,0,'C');
        $pdf->Cell(5,7,'IT',1,0,'C');
        $pdf->Cell(10,7,'TMT',1,0,'C');
        $pdf->Cell(10,7,'MP',1,0,'C');
        $pdf->Cell(10,7,'SKS',1,0,'C');
        $pdf->Cell(20,7,'RANK',1,0,'C');
        $pdf->Cell(25,7,'MENGAJAR',1,0,'C');
        $pdf->Cell(25,7,'DTY',1,0,'C');
        $pdf->Cell(25,7,'JAFUNG',1,0,'C');
        $pdf->Cell(35,7,'KEHADIRAN',1,0,'C');
        $pdf->Cell(20,7,'TUNKEL',1,0,'C');
        $pdf->Cell(20,7,'TUN ANAK',1,0,'C');
        $pdf->Cell(22,7,'KEHORMATAN',1,0,'C');
        $pdf->Cell(15,7,'KHUSUS',1,0,'C');
        $pdf->Cell(20,7,'POTONGAN',1,0,'C');
        $pdf->Cell(20,7,'DITERIMA',1,0,'C');
        $pdf->Cell(0,1,'',0,1);
        
        //formula
        $no = 1;
        foreach($isitunkel as $nominaltunkel);
        foreach($isitunj_anak as $nominaltunj_anak);
        foreach($isilist as $key){
            $jml_kehadiran = $key->jumlah_hadir * $key->nominal_transport;
            // $awal  = date_create($key->tmt_dosen);
            // $akhir = date_create(); // waktu sekarang
            // $diff  = date_diff($awal, $akhir );
            // $mp = $diff->y;
            
            $mp = date("Y") - date("Y", strtotime($key->tmt_dosen));
            if($mp == '0' ){
                $masa_p = 0 ;
            }else {
                $masa_p = $mp;
            }
            
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

            $hitung_rank = $this->db->query("select nominal from barokah_pengajar where min_tmp_mengajar <= $mp and max_tmp_mengajar >= $mp and ijazah = '$key->ijazah_terakhir' ")->result();
            foreach($hitung_rank as $nilai_rank) {
                $rank = $nilai_rank->nominal;
            }
            $mengajar = $rank * $key->jumlah_sks;
            

            $hitung_kehormatan = $this->db->query("select nominal from barokah_kehormatan_pengajar where min_masa_pengabdian <= $mp and max_masa_pengabdian >= $mp and kategori = 'Dosen' ")->result();
            
            if(!empty($hitung_kehormatan) and $key->kehormatan == 'Ya') {
                foreach($hitung_kehormatan as $nilai_kehormatan) {
                    $kehormatan = $nilai_kehormatan->nominal;
                }
            } else {
                $kehormatan = 0; // atau dapat juga menghasilkan pesan error atau log error
            }

            $hitung_dty = $this->db->query("SELECT nominal from barokah_pengajar_tetap where kategori = 'Dosen' ")->result();
            
            if(!empty($hitung_dty)) {
                foreach($hitung_dty as $nilai_dty) {
                    $dty = $nilai_dty->nominal;
                }
            } else {
                $dty = 0; // atau dapat juga menghasilkan pesan error atau log error
            }

            $hitung_jafung = $this->db->query("SELECT nominal from barokah_jafung, umana where umana.jabatan_akademik = barokah_jafung.id_barokah_jafung and umana.jabatan_akademik = $key->jabatan_akademik and umana.status_sertifikasi = 'Belum' ")->result();
            
            if(!empty($hitung_jafung)) {
                foreach($hitung_jafung as $nilai_jafung) {
                    $jafung = $nilai_jafung->nominal;
                }
            } else {
                $jafung = 0; // atau dapat juga menghasilkan pesan error atau log error
            }
            
                $hitung_potongan = $this->db->query("SELECT SUM(nominal_potongan) as jumlah from potongan_umana, penempatan where potongan_umana.id_penempatan = penempatan.id_penempatan and potongan_umana.id_penempatan = $key->id_penempatan and potongan_umana.max_periode_potongan >= CURDATE() ")->result();
            
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
            // $tahunKerja = $tahunSekarang - date("Y", strtotime($key->tmt_dosen)); // menghitung masa kerja dalam tahun
            if ($mp >= 3 ) {
                $kenaikanGaji = floor($mp / 3) * 10000; // menghitung jumlah kenaikan gaji
                $tmp_Akhir = $tmp + $kenaikanGaji; // menghitung gaji saat ini
            } else {
                $tmp_Akhir = $tmp; // jika belum waktunya kenaikan gaji, gaji tetap sama dengan awal
            }
            //   ==================================================================================================================  
            
            $diterima = $jml_kehadiran + $tunkel + $tunja_anak + $mengajar + $dty + $jafung + $kehormatan - $potongan;
            $jumlah_total = 0;
            $jumlah_mengajar = 0;
            $jumlah_dty = 0;
            $jumlah_jafung = 0;
            $jumlah_kehadiran = 0;
            $jumlah_tunkel = 0;
            $jumlah_tunanak = 0;
            $jumlah_tmp = 0;
            $jumlah_kehormatan = 0;
            $jumlah_potong = 0;
            $jumlah_total += $diterima;
            $jumlah_mengajar += $mengajar;
            $jumlah_dty += $dty;
            $jumlah_jafung += $jafung;
            $jumlah_kehadiran += $jml_kehadiran;
            $jumlah_tunkel += $tunkel;
            $jumlah_tunanak += $tunja_anak;
            $jumlah_tmp += $tmp_Akhir;
            $jumlah_kehormatan += $kehormatan;
            // $jumlah_bk += $bk;
            $jumlah_potong += (int)$potongan;
            
            $list_potongan = $this->db->query("SELECT id_potongan_umana, nama_potongan, nominal_potongan as jumlah from potongan_umana, penempatan, potongan where potongan_umana.jenis_potongan = potongan.id_potongan and potongan_umana.id_penempatan = penempatan.id_penempatan and potongan_umana.id_penempatan = $key->id_penempatan 
            and potongan_umana.max_periode_potongan >= CURDATE()")->result();
            foreach ($list_potongan as $list){
                ?>
            <input type="hidden" name="" value="<?php echo $key->id_penempatan ?>">
            <input type="hidden" name="id_potongan_umana[]" value="<?php echo $list->id_potongan_umana ?>">
            <input type="hidden" name="" value="<?php echo $list->jumlah ?>">
            <?php };

            $pdf->Cell(1,7,'',0,1);
            $pdf->SetFont('arial','',6);
            $pdf->Cell(5,7,$no++,1,0,'C');
            $pdf->SetFont('arial','',6);
            $pdf->Cell(40,7,$key->gelar_depan.' '.$key->nama_lengkap.' '.$key->gelar_belakang,1,0,'L');
            $pdf->SetFont('arial','',7);
            $pdf->Cell(7,7,$key->kategori,1,0,'C');
            $pdf->Cell(5,7,$key->ijazah_terakhir,1,0,'C');
            $tanggal = date('Y', strtotime($key->tmt_dosen));
            $pdf->Cell(10, 7, $tanggal, 1, 0, 'C');
            $pdf->Cell(10,7,$masa_p,1,0,'C');
            $pdf->Cell(10,7,$key->jumlah_sks,1,0,'C');
            $pdf->Cell(20,7,$rank,1,0,'C');
            $pdf->Cell(25,7,$mengajar,1,0,'C');
            $pdf->Cell(25,7,'200.000',1,0,'C');
            $pdf->Cell(25,7,'200.000',1,0,'C');
            $pdf->Cell(5,7,'17',1,0,'C');
            $pdf->Cell(30,7,'200.000',1,0,'C');
            $pdf->Cell(20,7,'200.000',1,0,'C');
            $pdf->Cell(20,7,'200.000',1,0,'C');
            $pdf->Cell(22,7,'200.000',1,0,'C');
            $pdf->Cell(15,7,'200.000',1,0,'C');
            $pdf->Cell(20,7,'200.000',1,0,'C');
            $pdf->Cell(20,7,'200.000',1,0,'C');
            $pdf->Cell(0,0,'',0,1);
        }
        
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