<?php 
    class CustomPDF extends FPDF {
        private $isFirstPage = true;  
    function Header() {
        if ($this->PageNo() > 1) {
        $this->Cell(1,7,'',0,1);
        $this->SetFont('arial','B',8);
        // $this->SetFillColor(128, 128, 128);
        $this->Cell(5,7,'N0',1,0,'C');
        $this->Cell(60,7,'NAMA LENGKAP',1,0,'C');
        $this->Cell(35,7,'ESELON',1,0,'C');
        $this->Cell(10,7,'TMT',1,0,'C');
        $this->Cell(18,7,'TUNJAB',1,0,'C');
        $this->Cell(5,7,'MP',1,0,'C');
        $this->Cell(18,7,'TMP',1,0,'C');
        $this->Cell(23,7,'KEHADIRAN',1,0,'C');
        $this->Cell(18,7,'TUNKEL',1,0,'C');
        $this->Cell(18,7,'TUN ANAK',1,0,'C');
        $this->Cell(22,7,'KEHORMATAN',1,0,'C');
        $this->Cell(18,7,'TBK',1,0,'C');
        $this->Cell(20,7,'JUMLAH',1,0,'C');
        $this->Cell(20,7,'POTONGAN',1,0,'C');
        $this->Cell(18,7,'DITERIMA',1,0,'C');
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

        $pdf->Image('assets/p2s2.png',10,9,30);
        // Title
        $pdf->Cell(31,0,'','0','0','L',false);
        $pdf->SetFont('tahoma','B',15);
        $pdf->Cell(0,1,'KANTOR BENDAHARA','0','1','L',false);

        $pdf->ln(4);
        $pdf->SetFont('tahoma','B',13);
        $pdf->Cell(31,0,'','0','0','L',false);
        $pdf->Cell(150,1,"PONDOK PESANTREN SALAFIYAH SYAFI'IYAH",'0','1','L',false);

        $pdf->Ln(4);
        $pdf->SetFont('tahoma','',11);
        $pdf->Cell(31,0,'','0','0','L',false);
        $pdf->Cell(150,1,'SUKOREJO SITUBONDO JAWA TIMUR','0','1','L',false);

        $pdf->Ln(4);
        $pdf->SetFont('tahoma','',8);
        $pdf->Cell(40,0,'Po Box 2 telp 0388-452666 Fax. 452707 - eMail : sentral@salafiyah.net - Situbondo, 68374','0','0','L',false);

        $pdf->Line(9,23,340,23);
        $pdf->Line(9,27,340,27);

     
       

        foreach ($isilist as $periode) {}

            $pdf->ln(5);
            $pdf->SetFont('arial', 'B', 10);
            $pdf->Cell(200, 2, strtoupper($periode->nama_lembaga), '0', '0', 'L', false);
            $pdf->Cell(50, 2, '', '0', '0', 'L', false);
            if ($periode->id_bidang == "Bidang DIKTI"){
                $pdf->Cell(50, 2,'Barokah Umana', '0', '1', 'L', false);
            } else {
                $pdf->Cell(50, 2,'Barokah Umana', '0', '1', 'L', false);
            }
        
            $pdf->ln(2);
            $pdf->SetFont('arial', '', 10);
            $pdf->Cell(200, 2, '', '0', '0', 'L', false);
            $pdf->Cell(50, 2, '', '0', '0', 'L', false);
            $pdf->Cell(50, 2, 'Bulan : '.$periode->bulan.' '.$periode->tahun, '0', '1', 'L', false);

            $pdf->Cell(1,7,'',0,1);
            $pdf->SetFont('arial','B',8);
            // $pdf->SetFillColor(128, 128, 128);
            $pdf->Cell(5,7,'N0',1,0,'C');
            $pdf->Cell(60,7,'NAMA LENGKAP',1,0,'C');
            $pdf->Cell(35,7,'ESELON',1,0,'C');
            $pdf->Cell(10,7,'TMT',1,0,'C');
            $pdf->Cell(18,7,'TUNJAB',1,0,'C');
            $pdf->Cell(5,7,'MP',1,0,'C');
            $pdf->Cell(18,7,'TMP',1,0,'C');
            $pdf->Cell(23,7,'KEHADIRAN',1,0,'C');
            $pdf->Cell(18,7,'TUNKEL',1,0,'C');
            $pdf->Cell(18,7,'TUN ANAK',1,0,'C');
            $pdf->Cell(22,7,'KEHORMATAN',1,0,'C');
            $pdf->Cell(18,7,'TBK',1,0,'C');
            $pdf->Cell(20,7,'JUMLAH',1,0,'C');
            $pdf->Cell(20,7,'POTONGAN',1,0,'C');
            $pdf->Cell(18,7,'DITERIMA',1,0,'C');
            $pdf->Cell(0,1,'',0,1);
            
            $no = 1;
            $jumlah_total = 0;
            $jumlah_tunjab = 0;
            $jumlah_kehadiran = 0;
            $jumlah_tunkel = 0;
            $jumlah_tunanak = 0;
            $jumlah_tmp = 0;
            $jumlah_kehormatan = 0;
            $jumlah_tbk = 0;
            $jumlah_potong = 0;
            $jumlah_semua = 0;
            
            foreach($isitunkel as $nominaltunkel);
            foreach($isitunj_anak as $nominaltunj_anak);
            foreach($isilist as $key){
                
                
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
                                            
                    $jml_kehadiran = $key->jumlah_hadir * $key->nominal_transport;
                        
                    // $mp = date("Y") - date("Y", strtotime($key->tmt_struktural))  ;
                    // if($mp == '0' ){
                    //     $masa_p = 0 ;
                    //     }else {
                    //         $masa_p = $mp + 1;
                    //     }
                    // }
                    
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
            $jumlah = $jml_kehadiran + $tunkel + $tunja_anak + $key->barokah + $tmp_Akhir + $kehormatan + $tbk;
                

            $pdf->Cell(1,7,'',0,1);
            $pdf->SetFont('arial','B',7);
            // $pdf->SetFillColor(128, 128, 128);
            $pdf->Cell(5,7,$no++,1,0,'C');
            $pdf->Cell(60,7,$key->gelar_depan.' '.ucwords(strtolower($key->nama_lengkap)).' '.$key->gelar_belakang,1,0,'L');
            $pdf->Cell(35,7,$key->jabatan_lembaga,1,0,'L');
            $pdf->SetFont('arial','B',8);
            $pdf->Cell(10,7,date("Y", strtotime($key->tmt_struktural)),1,0,'C');
            $pdf->Cell(18,7,rupiah($key->barokah),1,0,'C');
            $pdf->Cell(5,7,$masa_p,1,0,'C');
            $pdf->Cell(18,7,rupiah($tmp_Akhir),1,0,'C');
            $pdf->Cell(5,7,$key->jumlah_hadir,1,0,'C');
            $pdf->Cell(18,7,rupiah($jml_kehadiran),1,0,'C');
            $pdf->Cell(18,7,rupiah($tunkel),1,0,'C');
            $pdf->Cell(18,7,rupiah($tunja_anak),1,0,'C');
            $pdf->Cell(22,7,rupiah($kehormatan),1,0,'C');
            $pdf->Cell(18,7,rupiah($tbk),1,0,'C');
            $pdf->Cell(20,7,rupiah($jumlah),1,0,'C');
            $pdf->Cell(20,7,rupiah($potongan),1,0,'C');
            $pdf->Cell(18,7,rupiah($diterima),1,0,'C');
            $pdf->Cell(0,0,'',0,1);
            $pdf->Cell(0,0,'',0,1);
    
        
        

        $jumlah_semua += $jumlah;
        $jumlah_total += $diterima;
        $jumlah_tunjab += $key->barokah;
        $jumlah_kehadiran += $jml_kehadiran;
        $jumlah_tunkel += $tunkel;
        $jumlah_tunanak += $tunja_anak;
        $jumlah_tmp += $tmp_Akhir;
        $jumlah_kehormatan += $kehormatan;
        $jumlah_tbk += $tbk;
        $jumlah_potong += (int)$potongan;
    }

        $pdf->ln(8);
        $pdf->Cell(110,7,'',0,0,'C');
        $pdf->Cell(18,7,rupiah($jumlah_tunjab),1,0,'C');
        $pdf->Cell(23,7,rupiah($jumlah_tmp),1,0,'C');
        $pdf->Cell(23,7,rupiah($jumlah_kehadiran),1,0,'C');
        $pdf->Cell(18,7,rupiah($jumlah_tunkel),1,0,'C');
        $pdf->Cell(18,7,rupiah($jumlah_tunanak),1,0,'C');
        $pdf->Cell(22,7,rupiah($jumlah_kehormatan),1,0,'C');
        $pdf->Cell(18,7,rupiah($jumlah_tbk),1,0,'C');
        $pdf->Cell(20,7,rupiah($jumlah_semua),1,0,'C');
        $pdf->Cell(20,7,rupiah($jumlah_potong),1,0,'C');
        $pdf->Cell(18,7,rupiah($jumlah_total),1,0,'C');
        
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