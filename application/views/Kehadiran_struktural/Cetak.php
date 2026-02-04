<?php 
    class CustomPDF extends FPDF {
        private $isFirstPage = true;  
    function Header() {
        if ($this->PageNo() > 1) {
            $this->Cell(1,7,'',0,1);
            $this->SetFont('arial','B',6);
            
            // Optimized widths for legal landscape (Total ~335mm)
            $this->Cell(6,7,'NO',1,0,'C');
            $this->Cell(57,7,'NAMA LENGKAP',1,0,'C'); // Reduced from 60
            $this->Cell(32,7,'ESELON',1,0,'C'); // Reduced from 35
            $this->Cell(10,7,'TMT',1,0,'C');
            $this->Cell(19,7,'TUNJAB',1,0,'C');
            $this->Cell(7,7,'MP',1,0,'C');
            $this->Cell(19,7,'TMP',1,0,'C');
            
            // Kehadiran columns (Total 34mm)
            $this->Cell(6,7,'W',1,0,'C');
            $this->Cell(6,7,'H',1,0,'C');
            $this->Cell(6,7,'T',1,0,'C'); // New Column
            $this->Cell(5,7,'I',1,0,'C');
            $this->Cell(5,7,'S',1,0,'C');
            $this->Cell(6,7,'%',1,0,'C');
            
            $this->Cell(19,7,'Kehadiran',1,0,'C');
            $this->Cell(19,7,'Tunkel',1,0,'C');
            $this->Cell(19,7,'Tun Anak',1,0,'C');
            $this->Cell(20,7,'Kehormatan',1,0,'C');
            $this->Cell(18,7,'TBK',1,0,'C');
            $this->Cell(20,7,'Jumlah',1,0,'C');
            $this->Cell(20,7,'Potongan',1,0,'C');
            $this->Cell(19,7,'Diterima',1,0,'C');
            $this->Ln();
        }
        if ($this->PageNo() > 1) {
            $this->setY(25); // Reduced from 32 to close the gap
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
            $pdf->SetFont('arial','B',6);
            
            // Optimized widths for legal landscape (Total ~335mm)
            $pdf->Cell(6,7,'NO',1,0,'C');
            $pdf->Cell(57,7,'NAMA LENGKAP',1,0,'C'); // Reduced
            $pdf->Cell(32,7,'ESELON',1,0,'C'); // Reduced
            $pdf->Cell(10,7,'TMT',1,0,'C');
            $pdf->Cell(19,7,'TUNJAB',1,0,'C');
            $pdf->Cell(7,7,'MP',1,0,'C');
            $pdf->Cell(19,7,'TMP',1,0,'C');
            
            // Kehadiran columns
            $pdf->Cell(6,7,'W',1,0,'C');
            $pdf->Cell(6,7,'H',1,0,'C');
            $pdf->Cell(6,7,'T',1,0,'C'); // New
            $pdf->Cell(5,7,'I',1,0,'C');
            $pdf->Cell(5,7,'S',1,0,'C');
            $pdf->Cell(6,7,'%',1,0,'C');
            
            $pdf->Cell(19,7,'Kehadiran',1,0,'C');
            $pdf->Cell(19,7,'Tunkel',1,0,'C');
            $pdf->Cell(19,7,'Tun Anak',1,0,'C');
            $pdf->Cell(20,7,'Kehormatan',1,0,'C');
            $pdf->Cell(18,7,'TBK',1,0,'C');
            $pdf->Cell(20,7,'Jumlah',1,0,'C');
            $pdf->Cell(20,7,'Potongan',1,0,'C');
            $pdf->Cell(19,7,'Diterima',1,0,'C');
            // $pdf->Ln();
            
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
            
            // Load dynamic year config from DB
            $config_tahun_query = $this->db->get('pengaturan_tahun_acuan');
            $tahun_acuan_map = [];
            if ($config_tahun_query->num_rows() > 0) {
                foreach ($config_tahun_query->result() as $cfg) {
                    $tahun_acuan_map[trim($cfg->id_bidang)] = (int)$cfg->tahun_acuan;
                }
            }
            $tahun_default = isset($tahun_acuan_map['Pengurus']) ? $tahun_acuan_map['Pengurus'] : (int)date('Y');
            
            foreach($isilist as $key){
                // Get tahun acuan dynamically
                $id_bidang_key = trim($key->id_bidang ?? '');
                $tahun_skrg = isset($tahun_acuan_map[$id_bidang_key]) ? $tahun_acuan_map[$id_bidang_key] : $tahun_default;
                
                // Calculate MP
                $mp = $tahun_skrg - date("Y", strtotime($key->tmt_struktural));
                $masa_p = max(0, $mp);
                                            
                    $jml_kehadiran = $key->jumlah_hadir * $key->nominal_transport;
                    
                    // Calculate wajib hadir & percentage
                    $bulan_int = is_numeric($periode->bulan) ? (int)$periode->bulan : date('n', strtotime($periode->bulan));
                    $tahun_int = (int)$periode->tahun;
                    $total_hari = (int)date('t', mktime(0, 0, 0, $bulan_int, 1, $tahun_int));
                    
                    $jumlah_jumat = 0;
                    $hari_pertama = mktime(0, 0, 0, $bulan_int, 1, $tahun_int);
                    $nama_hari = date('N', $hari_pertama);
                    $jumat_pertama = ($nama_hari <= 5) ? (5 - $nama_hari + 1) : (12 - $nama_hari + 1);
                    for ($tgl = $jumat_pertama; $tgl <= $total_hari; $tgl += 7) {
                        $jumlah_jumat++;
                    }
                    $hari_kerja = $total_hari - $jumlah_jumat;
                    $wajib_hadir_bulanan = round(($hari_kerja / 6) * ($key->wajib_hadir ?? 0));
                    
                    $izin = (int)($key->jumlah_izin ?? 0);
                    $sakit = (int)($key->jumlah_sakit ?? 0);
                    $tugas = (int)($key->jumlah_tugas ?? 0); // Include Tugas
                    
                    // Logic Update: Hadir, Tugas, Sakit = 100%. Izin = 25%.
                    $kehadiran_efektif = ($key->jumlah_hadir * 1) + ($tugas * 1) + ($sakit * 1) + ($izin * 0.25);
                    $persentase = ($wajib_hadir_bulanan > 0) ? (int)round(($kehadiran_efektif / $wajib_hadir_bulanan) * 100) : 0;
                        
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
            $pdf->Cell(6,7,$no++,1,0,'C');
            $pdf->Cell(57,7,$key->gelar_depan.' '.ucwords(strtolower($key->nama_lengkap)).' '.$key->gelar_belakang,1,0,'L');
            $pdf->Cell(32,7,$key->nama_jabatan,1,0,'L'); // Match Validasi (nama_jabatan)
            $pdf->SetFont('arial','B',7);
            $pdf->Cell(10,7,date("Y", strtotime($key->tmt_struktural)),1,0,'C');
            $pdf->Cell(19,7,rupiah($key->barokah),1,0,'C');
            $pdf->Cell(7,7,$masa_p,1,0,'C');
            $pdf->Cell(19,7,rupiah($tmp_Akhir),1,0,'C');
            
            // Kehadiran columns
            $pdf->Cell(6,7,$wajib_hadir_bulanan,1,0,'C');
            $pdf->Cell(6,7,$key->jumlah_hadir,1,0,'C');
            $pdf->Cell(6,7,$key->jumlah_tugas ?? 0,1,0,'C'); // New T
            $pdf->Cell(5,7,$izin,1,0,'C');
            $pdf->Cell(5,7,$sakit,1,0,'C');
            $pdf->Cell(6,7,$persentase,1,0,'C');
            
            $pdf->Cell(19,7,rupiah($jml_kehadiran),1,0,'C');
            $pdf->Cell(19,7,rupiah($tunkel),1,0,'C');
            $pdf->Cell(19,7,rupiah($tunja_anak),1,0,'C');
            $pdf->Cell(20,7,rupiah($kehormatan),1,0,'C');
            $pdf->Cell(18,7,rupiah($tbk),1,0,'C');
            $pdf->Cell(20,7,rupiah($jumlah),1,0,'C');
            $pdf->Cell(20,7,rupiah($potongan),1,0,'C');
            $pdf->Cell(19,7,rupiah($diterima),1,0,'C');
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
        
        // Total Row - Aligned with Body Columns
        // 1. Spacer (NO + NAMA + ESELON + TMT) = 6 + 57 + 32 + 10 = 105 (Calculated)
        $pdf->SetFont('arial','B',7);
        $pdf->Cell(105,7,'TOTAL',1,0,'C');
        
        // 2. TUNJAB (19)
        $pdf->Cell(19,7,rupiah($jumlah_tunjab),1,0,'C');
        
        // 3. MP (7) - Empty
        $pdf->Cell(7,7,'',1,0,'C');
        
        // 4. TMP (19)
        $pdf->Cell(19,7,rupiah($jumlah_tmp),1,0,'C');
        
        // 5. Kehadiran Breakdown (W+H+T+I+S+%)
        $pdf->Cell(6,7,'',1,0,'C'); // W
        $pdf->Cell(6,7,'',1,0,'C'); // H
        $pdf->Cell(6,7,'',1,0,'C'); // T - New
        $pdf->Cell(5,7,'',1,0,'C'); // I
        $pdf->Cell(5,7,'',1,0,'C'); // S
        $pdf->Cell(6,7,'',1,0,'C'); // %
        
        // 6. KHD.Rp (19)
        $pdf->Cell(19,7,rupiah($jumlah_kehadiran),1,0,'C');
        
        // 7. TUNKEL (19)
        $pdf->Cell(19,7,rupiah($jumlah_tunkel),1,0,'C');
        
        // 8. TUN ANAK (19)
        $pdf->Cell(19,7,rupiah($jumlah_tunanak),1,0,'C');
        
        // 9. KEHORMATAN (20)
        $pdf->Cell(20,7,rupiah($jumlah_kehormatan),1,0,'C');
        
        // 10. TBK (18)
        $pdf->Cell(18,7,rupiah($jumlah_tbk),1,0,'C');
        
        // 11. JUMLAH (20)
        $pdf->Cell(20,7,rupiah($jumlah_semua),1,0,'C');
        
        // 12. POTONGAN (20)
        $pdf->Cell(20,7,rupiah($jumlah_potong),1,0,'C');
        
        // 13. DITERIMA (19)
        $pdf->Cell(19,7,rupiah($jumlah_total),1,0,'C');
        
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