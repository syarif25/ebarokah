<?php 
    class CustomPDF extends FPDF {
        private $isFirstPage = true;  

    function Header() {
        // --- WATERMARK: Single Centered Logo (opacity ~8%) ---
        $pageW = $this->GetPageWidth();
        $pageH = $this->GetPageHeight();
        $logoW = 100; // Lebar logo watermark
        $logoX = ($pageW - $logoW) / 2; // Tepat tengah horizontal
        $logoY = ($pageH - ($logoW * 283/753)) / 2; // Tepat tengah vertikal (rasio proporsional)
        // Gunakan file PNG yang sudah diedit opacity-nya (8%) via PHP GD
        $this->Image('assets/p2s2_watermark.png', $logoX, $logoY, $logoW);
        // -----------------------------------------------------

        if ($this->PageNo() > 1) {
            $this->Cell(1,7,'',0,1);
            $this->SetFont('arial','B',6);
            
            // Optimized widths for legal landscape (Total ~335mm)
            $this->Cell(6,7,'NO',1,0,'C');
            $this->Cell(53,7,'NAMA LENGKAP',1,0,'C'); 
            $this->Cell(32,7,'ESELON',1,0,'C'); 
            $this->Cell(10,7,'TMT',1,0,'C');
            $this->Cell(19,7,'TUNJAB',1,0,'C');
            $this->Cell(7,7,'MP',1,0,'C');
            $this->Cell(19,7,'TMP',1,0,'C');
            
            $this->Cell(6,7,'W',1,0,'C');
            $this->Cell(6,7,'H',1,0,'C');
            $this->Cell(6,7,'T',1,0,'C'); 
            $this->Cell(5,7,'I',1,0,'C');
            $this->Cell(5,7,'S',1,0,'C');
            $this->Cell(10,7,'%',1,0,'C');
            
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
            $pdf->Cell(53,7,'NAMA LENGKAP',1,0,'C'); // Reduced
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
            $pdf->Cell(10,7,'%',1,0,'C');
            
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
            
            foreach($isilist as $key){
            $pdf->Cell(1,7,'',0,1);
            $pdf->SetFont('arial','B',7);
            
            // Cek indisipliner untuk warna baris
            $fill = false;
            if (!empty($key->is_warning)) {
                $pdf->SetFillColor(255, 243, 205); // Warna kuning ala bg-warning Bootstrap
                $fill = true;
            }

            $pdf->Cell(6,7,$no++,1,0,'C', $fill);
            $nama_full = strtoupper(implode(' ', array_filter([
                trim($key->gelar_depan ?? ''),
                trim($key->nama_lengkap ?? ''),
                trim($key->gelar_belakang ?? '')
            ])));
            $pdf->Cell(53,7,$nama_full,1,0,'L', $fill);
            $pdf->Cell(32,7,$key->nama_jabatan,1,0,'L', $fill);
            $pdf->SetFont('arial','B',7);
            $pdf->Cell(10,7,date("Y", strtotime($key->tmt_struktural)),1,0,'C', $fill);
            $pdf->Cell(19,7,rupiah($key->tunjab),1,0,'C', $fill);
            $pdf->Cell(7,7,$key->mp,1,0,'C', $fill);
            $pdf->Cell(19,7,rupiah($key->tmp),1,0,'C', $fill);
            
            // Kehadiran columns
            $pdf->Cell(6,7,$key->wajib_hadir_bulanan,1,0,'C', $fill);
            $pdf->Cell(6,7,$key->jumlah_hadir,1,0,'C', $fill);
            $pdf->Cell(6,7,$key->jumlah_tugas ?? 0,1,0,'C', $fill); // New T
            $pdf->Cell(5,7,$key->jumlah_izin,1,0,'C', $fill);
            $pdf->Cell(5,7,$key->jumlah_sakit,1,0,'C', $fill);
            $pdf->Cell(10,7,$key->persentase_kehadiran,1,0,'C', $fill);
            
            $pdf->Cell(19,7,rupiah($key->nominal_kehadiran),1,0,'C', $fill);
            $pdf->Cell(19,7,rupiah($key->tunkel),1,0,'C', $fill);
            $pdf->Cell(19,7,rupiah($key->tunj_anak),1,0,'C', $fill);
            $pdf->Cell(20,7,rupiah($key->nilai_kehormatan),1,0,'C', $fill);
            $pdf->Cell(18,7,rupiah($key->tbk),1,0,'C', $fill);
            $pdf->Cell(20,7,rupiah($key->jumlah_barokah),1,0,'C', $fill);
            $pdf->Cell(20,7,rupiah($key->potongan),1,0,'C', $fill);
            $pdf->Cell(19,7,rupiah($key->diterima),1,0,'C', $fill);
            $pdf->Cell(0,0,'',0,1);
            $pdf->Cell(0,0,'',0,1);
        }

        $pdf->ln(8);
        
        // Total Row - Aligned with Body Columns
        // 1. Spacer (NO + NAMA + ESELON + TMT) = 6 + 53 + 32 + 10 = 101 (Calculated)
        $pdf->SetFont('arial','B',7);
        $pdf->Cell(101,7,'TOTAL',1,0,'C');
        
        // 2. TUNJAB (19)
        $pdf->Cell(19,7,rupiah($totals['total_tunjab']),1,0,'C');
        
        // 3. MP (7) - Empty
        $pdf->Cell(7,7,'',1,0,'C');
        
        // 4. TMP (19)
        $pdf->Cell(19,7,rupiah($totals['total_tmp']),1,0,'C');
        
        // 5. Kehadiran Breakdown (W+H+T+I+S+%)
        $pdf->Cell(6,7,'',1,0,'C'); // W
        $pdf->Cell(6,7,'',1,0,'C'); // H
        $pdf->Cell(6,7,'',1,0,'C'); // T - New
        $pdf->Cell(5,7,'',1,0,'C'); // I
        $pdf->Cell(5,7,'',1,0,'C'); // S
        $pdf->Cell(10,7,'',1,0,'C'); // %
        
        // 6. KHD.Rp (19)
        $pdf->Cell(19,7,rupiah($totals['total_kehadiran']),1,0,'C');
        
        // 7. TUNKEL (19)
        $pdf->Cell(19,7,rupiah($totals['total_tunkel']),1,0,'C');
        
        // 8. TUN ANAK (19)
        $pdf->Cell(19,7,rupiah($totals['total_tunjanak']),1,0,'C');
        
        // 9. KEHORMATAN (20)
        $pdf->Cell(20,7,rupiah($totals['total_kehormatan']),1,0,'C');
        
        // 10. TBK (18)
        $pdf->Cell(18,7,rupiah($totals['total_tbk']),1,0,'C');
        
        // 11. JUMLAH (20)
        $pdf->Cell(20,7,rupiah($totals['total_barokah']),1,0,'C');
        
        // 12. POTONGAN (20)
        $pdf->Cell(20,7,rupiah($totals['total_potongan']),1,0,'C');
        
        // 13. DITERIMA (19)
        $pdf->Cell(19,7,rupiah($totals['grand_total']),1,0,'C');
        
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