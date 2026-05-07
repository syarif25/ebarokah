<?php 
    class CustomPDF extends FPDF {
        private $isFirstPage = true;  
    function Header() {
        if ($this->PageNo() > 1) {
        $this->Cell(1,7,'',0,1);
        $this->SetFont('arial','B',8);
        $this->Cell(10, 7, '', '0', '0', 'L', false); // Margin
        $this->Cell(7,7,'NO',1,0,'C');
        $this->Cell(60,7,'NAMA LENGKAP',1,0,'C');
        $this->Cell(45,7,'NAMA POTONGAN',1,0,'C');
        $this->Cell(40,7,'MASA AKTIF',1,0,'C');
        $this->Cell(30,7,'NOMINAL',1,0,'C');
        $this->Cell(0,7,'',0,1);
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
            $pdf->Cell(50, 2, 'RINCIAN POTONGAN BAROKAH', '0', '0', 'L', false);
            $pdf->ln(5);
            $pdf->SetFont('arial', 'B', 11);
            $pdf->Cell(130, 2, strtoupper($periode->nama_lembaga), '0', '0', 'L', false);
            
            $pdf->SetFont('arial', 'B', 10);
            if (isset($bulan_laporan) && isset($tahun_laporan)) {
                $pdf->Cell(60, 2, 'PERIODE: '.strtoupper($bulan_laporan).' '.$tahun_laporan, '0', '1', 'R', false);
            } else {
                $pdf->Cell(60, 2, '', '0', '1', 'R', false);
            }
            
            $pdf->ln(2);
            $pdf->SetFont('arial', '', 9);
            if ($periode->id_bidang == "Bidang DIKTI"){
                $pdf->Cell(50, 5,'Kategori: Barokah Dosen', '0', '1', 'L', false);
            } else {
                $pdf->Cell(50, 5,'Kategori: Barokah Guru', '0', '1', 'L', false);
            }

            $pdf->Cell(1,7,'',0,1);
            $pdf->SetFont('arial','B',8);
            $pdf->Cell(10, 7, '', '0', '0', 'L', false); // Margin
            $pdf->Cell(7,7,'NO',1,0,'C');
            $pdf->Cell(60,7,'NAMA LENGKAP',1,0,'C');
            $pdf->Cell(45,7,'NAMA POTONGAN',1,0,'C');
            $pdf->Cell(40,7,'MASA AKTIF',1,0,'C');
            $pdf->Cell(30,7,'NOMINAL',1,0,'C');
            $pdf->Cell(0,7,'',0,1);
            
            $no = 1;
            $jumlah_total = 0;
            $pdf->SetFont('arial','',8);
            
            foreach($isilist as $key){
                // Format masa aktif
                $masa_aktif = '-';
                if (!empty($key->min_periode_potongan) && !empty($key->max_periode_potongan)) {
                    $map_bln = ['Jan'=>'Jan','Feb'=>'Feb','Mar'=>'Mar','Apr'=>'Apr','May'=>'Mei','Jun'=>'Jun','Jul'=>'Jul','Aug'=>'Agu','Sep'=>'Sep','Oct'=>'Okt','Nov'=>'Nov','Dec'=>'Des'];
                    $start_bln = $map_bln[date('M', strtotime($key->min_periode_potongan))] ?? date('M', strtotime($key->min_periode_potongan));
                    $end_bln   = $map_bln[date('M', strtotime($key->max_periode_potongan))] ?? date('M', strtotime($key->max_periode_potongan));
                    
                    $start = $start_bln . ' ' . date('Y', strtotime($key->min_periode_potongan));
                    $end   = $end_bln . ' ' . date('Y', strtotime($key->max_periode_potongan));
                    $masa_aktif = $start . ' s.d ' . $end;
                }

                $nama_lengkap = trim(($key->gelar_depan ?? '') . ' ' . $key->nama_lengkap . (isset($key->gelar_belakang) && $key->gelar_belakang ? ', ' . $key->gelar_belakang : ''));

                $pdf->Cell(10, 7, '', '0', '0', 'L', false); // Margin
                $pdf->Cell(7,7,$no++,1,0,'C');
                $pdf->Cell(60,7,$nama_lengkap,1,0,'L');
                $pdf->Cell(45,7,$key->nama_potongan,1,0,'L');
                $pdf->Cell(40,7,$masa_aktif,1,0,'C');
                $pdf->Cell(30,7,rupiah($key->nominal_potongan),1,0,'R');
                $pdf->Ln(7);

                $jumlah_total = $jumlah_total + $key->nominal_potongan;
            }

        $pdf->ln(1);
        $pdf->SetFont('arial','B',9);
        $pdf->Cell(10, 7, '', '0', '0', 'L', false); // Margin
        $pdf->Cell(112,7,'TOTAL',1,0,'R');
        $pdf->Cell(40,7,'',1,0,'C');
        $pdf->Cell(30,7,rupiah($jumlah_total),1,0,'R');
        $pdf->Cell(0,7,'',0,1);
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