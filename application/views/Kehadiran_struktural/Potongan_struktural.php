<?php 
    class CustomPDF extends FPDF {
        private $isFirstPage = true;  

    function Header() {
        // --- WATERMARK: Single Centered Logo (opacity ~8%) ---
        $pageW = $this->GetPageWidth();
        $pageH = $this->GetPageHeight();
        $logoW = 100;
        $logoX = ($pageW - $logoW) / 2;                    // Tengah horizontal
        $logoY = ($pageH - ($logoW * 283/753)) / 2;        // Tengah vertikal (rasio proporsional)
        // Gunakan file PNG yang sudah diedit opacity-nya (8%) via PHP GD
        $this->Image('assets/p2s2_watermark.png', $logoX, $logoY, $logoW);
        // -----------------------------------------------------

        if ($this->PageNo() > 1) {
            $this->Cell(1,7,'',0,1);
            $this->SetFont('arial','B',6);
            $this->Cell(40, 2, '', '0', '0', 'L', false);
            $this->Cell(7,7,'N0',1,0,'C');
            $this->Cell(40,7,'NAMA LENGKAP',1,0,'C');
            $this->Cell(40,7,'NAMA POTONGAN',1,0,'C');
            $this->Cell(40,7,'NOMINAL',1,0,'C');
            $this->Cell(0,1,'',0,1);
        }
        if ($this->PageNo() > 1) {
            $this->setY(25);
        }
    }
    }
        $pdf = new CustomPDF('P','mm','LEGAL');
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

            $pdf->ln(3);
            
            $pdf->SetFont('arial', '', 9);
            $pdf->Cell(0, 5, 'RINCIAN POTONGAN BAROKAH UMANA STRUKTURAL', '0', '1', 'L', false);
            
            // Gunakan $lembaga_info (dikirim terpisah dari controller) agar aman walau $isilist kosong
            $nama_lembaga_header = isset($lembaga_info->nama_lembaga) ? strtoupper($lembaga_info->nama_lembaga) : '';
            $id_bidang_header    = isset($lembaga_info->id_bidang)    ? $lembaga_info->id_bidang : '';
            // $jenis_barokah       = ($id_bidang_header == "Bidang DIKTI") ? 'Barokah Dosen' : 'Barokah Guru';

            // Baris: Nama Lembaga (kiri) | Jenis Barokah (kanan)
            $pdf->SetFont('arial', 'B', 10);
            $pdf->Cell(160, 6, $nama_lembaga_header, '0', '0', 'L', false);
            $pdf->SetFont('arial', '', 9);
            $pdf->Cell(0, 6, 'Barokah Struktural', '0', '1', 'R', false);

            // Baris: Periode (jika ada)
            if (!empty($bulan_laporan) || !empty($tahun_laporan)) {
                $pdf->SetFont('arial', 'I', 9);
                $pdf->Cell(0, 5, 'Periode : ' . ucfirst($bulan_laporan ?? '') . ' ' . ($tahun_laporan ?? ''), '0', '1', 'L', false);
            }

            $pdf->ln(3);
            $pdf->Cell(40, 2, '', '0', '0', 'L', false);
            $pdf->SetFont('arial','B',6);
            // $pdf->SetFillColor(128, 128, 128);
            $pdf->Cell(7,7,'N0',1,0,'C');
            $pdf->Cell(55,7,'NAMA LENGKAP',1,0,'C');
            $pdf->Cell(40,7,'NAMA POTONGAN',1,0,'C');
            $pdf->Cell(40,7,'NOMINAL',1,0,'C');
            $pdf->Cell(0,1,'',0,1);
            
            $no = 1;
            
            $jumlah_total = 0;
            $pdf->ln(7);
            
            foreach($isilist as $key){
            // Gabungkan gelar depan + nama + gelar belakang, format UPPERCASE
            $nama_tampil = strtoupper(implode(' ', array_filter([
                trim($key->gelar_depan ?? ''),
                trim($key->nama_lengkap ?? ''),
                trim($key->gelar_belakang ?? '')
            ])));

            $pdf->Cell(40, 2, '', '0', '0', 'L', false);
            $pdf->Cell(7,7,$no++,1,0,'C');
            $pdf->Cell(55,7,$nama_tampil,1,0,'L');
            $pdf->Cell(40,7,$key->nama_potongan,1,0,'L');
            $pdf->Cell(40,7,rupiah($key->nominal_potongan),1,0,'C');
            $pdf->Cell(0,7,'',0,1);

            $jumlah_total = $jumlah_total + $key->nominal_potongan;
            }

        $pdf->ln(1);
        $pdf->SetFont('arial','B',9);
        $pdf->Cell(87,7,'',0,0,'C');
        $pdf->Cell(40,7,'Total',1,0,'C');
        $pdf->Cell(40,7,rupiah($jumlah_total),1,0,'C');
        $pdf->Cell(0,1,'',0,1);
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

        $pdf->Ln(30);
        $pdf->SetFont('arial','i',5);
        $pdf->Cell(22,-40,' dicetak pada:, '.$tgl." " .$b1." ".$thn,0,0,'R');
        
        // $pdf->Ln(5);
        // $pdf->SetFont('arial','',12);
        // $pdf->Cell(270,-40,'Kepala Bidang,',0,0,'R');
        //  $pdf->SetFont('tahoma','B',12);
        // $pdf->Cell(14,0,'Dr. Maskuri, M.Pd.I.',0,0,'R');

        $pdf->Output();

?>