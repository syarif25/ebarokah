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


        // Header Info (from controller header_info or first row of data)
        // Controller passes $header_info object
        $nama_lembaga = isset($header_info) ? $header_info->nama_lembaga : (isset($data_rincian[0]) ? $data_rincian[0]->nama_lembaga : '-');
        $bulan = isset($header_info) ? $header_info->bulan : (isset($data_rincian[0]) ? $data_rincian[0]->bulan : '-');
        $tahun = isset($header_info) ? $header_info->tahun : (isset($data_rincian[0]) ? $data_rincian[0]->tahun : '-');
        
        $kategori_label = "Barokah Pengajar"; // Generic fallback
        // Try to guess logic from original code if needed, but generic is safe.

            $pdf->ln(5);
            $pdf->SetFont('arial', 'B', 10);
            $pdf->Cell(200, 2, strtoupper($nama_lembaga), '0', '0', 'L', false);
            $pdf->Cell(50, 2, '', '0', '0', 'L', false);
            $pdf->Cell(50, 2, $kategori_label, '0', '1', 'L', false);
        
            $pdf->ln(2);
            $pdf->SetFont('arial', '', 10);
            $pdf->Cell(200, 2, '', '0', '0', 'L', false);
            $pdf->Cell(50, 2, '', '0', '0', 'L', false);
            $pdf->Cell(50, 2, 'Bulan : '.$bulan.' '.$tahun, '0', '1', 'L', false);

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
            
            if (isset($data_rincian) && !empty($data_rincian)) {
                foreach($data_rincian as $key){
                    // --- SNAPSHOT DATA EXTRACTION ---
                    // Reusing logic from Rincian.php
                    
                    $jml_kehadiran = $key->nominal_kehadiran;
                    $jml_kehadiran_15 = $key->nominal_hadir_15;
                    $jml_kehadiran_10 = $key->nominal_hadir_10;
                    
                    $rank = $key->rank;
                    $mengajar = $key->mengajar;
                    $dty = $key->dty;
                    $jafung = $key->jafung;
                    
                    $rank_piket = $key->rank_piket;
                    $barokah_piket = $key->barokah_piket;
                    
                    $tunkel = $key->tunkel;
                    $tunja_anak = $key->tun_anak; 
                    $kehormatan = $key->kehormatan;
                    $tunj_walkes = $key->walkes; 
                    $tambahan = $key->khusus; 
                    $potongan = $key->potongan;
                    
                    $masa_p = $key->mp;

                    // Calculate Totals per Row
                    $diterima = $barokah_piket + $jml_kehadiran + $jml_kehadiran_15 + $jml_kehadiran_10 + $tunkel + $tunja_anak + $mengajar + $dty + $jafung + $kehormatan + $tunj_walkes + $tambahan - $potongan;
                    $jumlah = $barokah_piket + $jml_kehadiran + $jml_kehadiran_15 + $jml_kehadiran_10 + $tunkel + $tunja_anak + $mengajar + $dty + $jafung + $kehormatan + $tunj_walkes + $tambahan;

                    // Output to PDF
                    $pdf->Cell(1,7,'',0,1);
                    $pdf->SetFont('arial','B',5);
                    $pdf->Cell(5,7,$no++,1,0,'C');
                    
                    // Name color logic
                    if($key->status_aktif == "Cuti 50%" or $key->status_aktif == "Cuti 100%"){
                        $pdf->SetTextColor(255, 0, 0); 
                        $nama_dsp = isset($key->nama_lengkap) ? $key->nama_lengkap : '-';
                        if(isset($key->gelar_depan)) $nama_dsp = $key->gelar_depan . ' ' . $nama_dsp;
                        if(isset($key->gelar_belakang)) $nama_dsp = $nama_dsp . ' ' . $key->gelar_belakang;
                        
                        $pdf->Cell(45, 7, strtoupper($nama_dsp) . ' (' . $key->status_aktif . ')', 1, 0, 'L');
                        $pdf->SetTextColor(0, 0, 0); 
                    } else {
                        $nama_dsp = isset($key->nama_lengkap) ? $key->nama_lengkap : '-';
                        if(isset($key->gelar_depan)) $nama_dsp = $key->gelar_depan . ' ' . $nama_dsp;
                        if(isset($key->gelar_belakang)) $nama_dsp = $nama_dsp . ' ' . $key->gelar_belakang;
                        $pdf->Cell(45,7,strtoupper($nama_dsp),1,0,'L');
                    }
                    
                    $pdf->Cell(8,7,$key->kategori,1,0,'C');
                    // IJAZAH
                    $ijazah = isset($key->ijazah_terakhir) ? $key->ijazah_terakhir : '-';
                    $pdf->Cell(8,7,$ijazah,1,0,'C');
                    
                    // TMT (Display logic copied/simplified)
                    $tmt_display = '-';
                    // We don't have perfect TMT logic here without complex checks, 
                    // Use tmt_guru/dosen/maif if available in $key (we joined p.* in model)
                    // Simplified fallback:
                     if (isset($key->tmt_guru) && $key->tmt_guru != '0000-00-00') $tmt_display = date("Y", strtotime($key->tmt_guru));
                     elseif (isset($key->tmt_dosen) && $key->tmt_dosen != '0000-00-00') $tmt_display = date("Y", strtotime($key->tmt_dosen));
                     elseif (isset($key->tmt_maif) && $key->tmt_maif != '0000-00-00') $tmt_display = date("Y", strtotime($key->tmt_maif));
                    
                    $pdf->Cell(8, 7, $tmt_display, 1, 0, 'C');
        
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

        $pdf->Output();

?>
