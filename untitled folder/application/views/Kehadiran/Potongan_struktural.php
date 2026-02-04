<?php 
    class CustomPDF extends FPDF {
        private $isFirstPage = true;  
    function Header() {
        if ($this->PageNo() > 1) {
        $this->Cell(1,7,'',0,1);
        $this->SetFont('arial','B',6);
        // $this->SetFillColor(128, 128, 128);
        $this->Cell(40, 2, '', '0', '0', 'L', false);
        $this->Cell(7,7,'N0',1,0,'C');
        $this->Cell(40,7,'NAMA LENGKAP',1,0,'C');
        $this->Cell(40,7,'NAMA POTONGAN',1,0,'C');
        $this->Cell(40,7,'NOMINAL',1,0,'C');
        $this->Cell(0,1,'',0,1);
        }
        if ($this->PageNo() > 1) {
            // $this->SetFont('arial', 'I', 8);
            // $this->Cell(0, 10, 'Elemen ini hanya ditampilkan pada lembar kedua dan seterusnya', 0, 1, 'C');
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

            $pdf->ln(5);
            
            $pdf->SetFont('arial', '', 10);
            // $pdf->Cell(200, 2, '', '0', '0', 'L', false);
            $pdf->Cell(50, 2, 'RINCIAN POTONGAN BAROKAH UMANA STRUKTURAL', '0', '0', 'L', false);
            $pdf->ln(5);
            $pdf->SetFont('arial', 'B', 10);
            $pdf->Cell(200, 2, strtoupper($periode->nama_lembaga), '0', '0', 'L', false);
            $pdf->Cell(50, 2, '', '0', '0', 'L', false);
            if ($periode->id_bidang == "Bidang DIKTI"){
                $pdf->Cell(50, 2,'Barokah Dosen', '0', '1', 'L', false);
            } else {
                $pdf->Cell(50, 2,'Barokah Guru', '0', '1', 'L', false);
            }
        
            
            // $pdf->Cell(50, 2, 'Bulan : '.$periode->bulan.' '.$periode->tahun, '0', '1', 'L', false);

            $pdf->Cell(1,7,'',0,1);
            $pdf->Cell(40, 2, '', '0', '0', 'L', false);
            $pdf->SetFont('arial','B',6);
            // $pdf->SetFillColor(128, 128, 128);
            $pdf->Cell(7,7,'N0',1,0,'C');
            $pdf->Cell(40,7,'NAMA LENGKAP',1,0,'C');
            $pdf->Cell(40,7,'NAMA POTONGAN',1,0,'C');
            $pdf->Cell(40,7,'NOMINAL',1,0,'C');
            $pdf->Cell(0,1,'',0,1);
            
            $no = 1;
            
            $jumlah_total = 0;
            $pdf->ln(7);
            
            foreach($isilist as $key){
            $pdf->Cell(40, 2, '', '0', '0', 'L', false);
            $pdf->Cell(7,7,$no++,1,0,'C');
            $pdf->Cell(40,7,$key->nama_lengkap,1,0,'L');
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