<?php 
class CustomPDF extends FPDF {
    function Header() {
        if ($this->PageNo() > 1) {
            $this->Cell(1,5,'',0,1); // Spacer
            $this->SetFont('arial','B',8);
            
            $x = $this->GetX();
            $y = $this->GetY();
            
            // 1. Tall Columns (Height 10)
            $this->Cell(8,10,'No',1,0,'C');
            $this->Cell(90,10,'Nama Satpam',1,0,'C');
            
            // 2. Presence Groups (Top Row, Height 5)
            $x_group = $this->GetX(); // Save X start of groups
            $this->Cell(45,5,'Kehadiran 1 (Siang)',1,0,'C');
            $this->Cell(60,5,'Kehadiran 2 (Shift)',1,0,'C'); // Width 60
            $this->Cell(45,5,'Kehadiran 3 (Dinihari)',1,0,'C'); // Width 45
            
            // 3. Right Tall Columns (Height 10)
            $x_right = $this->GetX(); // Save X start of right cols
            $this->Cell(35,10,'Danru',1,0,'C');
            $this->Cell(40,10,'Total Diterima',1,0,'C');
            
            // 4. Sub-headers (Bottom Row, Height 5)
            $this->SetXY($x_group, $y + 5);
            
            // Group 1
            $this->Cell(10,5,'H',1,0,'C');
            $this->Cell(15,5,'Trns',1,0,'C');
            $this->Cell(20,5,'Jml',1,0,'C');
            
            // Group 2
            $this->Cell(10,5,'S',1,0,'C');
            $this->Cell(20,5,'Rank',1,0,'C');
            $this->Cell(30,5,'Jml',1,0,'C');
            
            // Group 3
            $this->Cell(10,5,'D',1,0,'C');
            $this->Cell(15,5,'Kons',1,0,'C');
            $this->Cell(20,5,'Jml',1,0,'C');
            
            // 5. Reset for Body
            $this->SetXY($x, $y + 10);
        }
    }
}

$pdf = new CustomPDF('L','mm','LEGAL'); // Landscape Legal
$pdf->AddFont('bookman','','bookman-old-style.php');
$pdf->AddFont('tahoma','B','tahomabd.php');
$pdf->AddFont('tahoma','','tahoma.php');

$pdf->SetFont('Times','B',16);
$pdf->AddPage();

// KOP SURAT
$pdf->Image('assets/p2s2.png',10,9,30);
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

$pdf->ln(8);
$pdf->SetFont('arial', 'B', 12);
$pdf->Cell(0, 5, 'RINCIAN BAROKAH SATPAM', 0, 1, 'C');
$pdf->SetFont('arial', '', 11);
$pdf->Cell(0, 5, 'Bulan : '.$header->bulan.' '.$header->tahun, 0, 1, 'C');

$pdf->ln(5);

// HEADER TABEL (Page 1)
$pdf->SetFont('arial','B',9);

$x = $pdf->GetX();
$y = $pdf->GetY();

// 1. Tall Columns (Height 10)
$pdf->Cell(8,10,'No',1,0,'C');
$pdf->Cell(90,10,'Nama Satpam',1,0,'C');

// 2. Presence Groups (Top Row, Height 5)
$x_group = $pdf->GetX(); 
$pdf->Cell(45,5,'Kehadiran 1 (Siang)',1,0,'C');
$pdf->Cell(60,5,'Kehadiran 2 (Shift)',1,0,'C');
$pdf->Cell(45,5,'Kehadiran 3 (Dinihari)',1,0,'C');

// 3. Right Tall Columns (Height 10)
$x_right = $pdf->GetX(); 
$pdf->Cell(35,10,'Danru',1,0,'C');
$pdf->Cell(40,10,'Total Diterima',1,0,'C');

// 4. Sub-headers (Bottom Row, Height 5)
$pdf->SetXY($x_group, $y + 5);

// Group 1
$pdf->Cell(10,5,'H',1,0,'C');
$pdf->Cell(15,5,'Trns',1,0,'C');
$pdf->Cell(20,5,'Jml',1,0,'C');

// Group 2
$pdf->Cell(10,5,'S',1,0,'C');
$pdf->Cell(20,5,'Rank',1,0,'C');
$pdf->Cell(30,5,'Jml',1,0,'C');

// Group 3
$pdf->Cell(10,5,'D',1,0,'C');
$pdf->Cell(15,5,'Kons',1,0,'C');
$pdf->Cell(20,5,'Jml',1,0,'C');

// 5. Reset for Body
$pdf->SetXY($x, $y + 10);

// ISI DATA
$pdf->SetFont('arial','',9);
$no = 1;

$grand_transport = 0;
$grand_barokah = 0;
$grand_dinihari = 0;
$grand_danru = 0;
$grand_total = 0;

if(!empty($data)):
foreach($data as $row) {
    // DATA ABSOLUT (SNAPSHOT)
    $d_hari      = $row->jumlah_hari;
    $d_ntrans    = $row->nominal_transport;
    $d_jtrans    = $row->jumlah_transport;
    
    $d_shift     = $row->jumlah_shift;
    $d_rank      = $row->rank;
    $d_jbarokah  = $row->jumlah_barokah;
    
    $d_dini      = $row->jumlah_dinihari;
    $d_kons      = $row->konsumsi;
    $d_jdini     = $row->jumlah_konsumsi;
    
    $d_danru     = $row->nominal_danru;
    $d_diterima  = $row->diterima;
    
    // Accumulate
    $grand_transport += $d_jtrans;
    $grand_barokah   += $d_jbarokah;
    $grand_dinihari  += $d_jdini;
    $grand_danru     += $d_danru;
    $grand_total     += $d_diterima;

    $pdf->Cell(8,7,$no++,1,0,'C');
    $pdf->Cell(90,7, $row->gelar_depan.' '.$row->nama_lengkap.' '.$row->gelar_belakang,1,0,'L'); 
    
    // Kehadiran 1
    $pdf->Cell(10,7,$d_hari,1,0,'C');
    $pdf->Cell(15,7,number_format($d_ntrans,0,',','.'),1,0,'R');
    $pdf->Cell(20,7,number_format($d_jtrans,0,',','.'),1,0,'R');
    
    // Kehadiran 2
    $pdf->Cell(10,7,$d_shift,1,0,'C');
    $pdf->Cell(20,7,number_format($d_rank,0,',','.'),1,0,'R');
    $pdf->Cell(30,7,number_format($d_jbarokah,0,',','.'),1,0,'R');
    
    // Kehadiran 3
    $pdf->Cell(10,7,$d_dini,1,0,'C');
    $pdf->Cell(15,7,number_format($d_kons,0,',','.'),1,0,'R');
    $pdf->Cell(20,7,number_format($d_jdini,0,',','.'),1,0,'R');
    
    // Danru
    $pdf->Cell(35,7,number_format($d_danru,0,',','.'),1,0,'R');
    
    // Total
    $pdf->SetFont('arial','B',9);
    $pdf->Cell(40,7,number_format($d_diterima,0,',','.'),1,1,'R');
    $pdf->SetFont('arial','',9);
}
endif;

// FOOTER TOTAL
$pdf->SetFont('arial','B',9);
$pdf->Cell(98,7,'TOTAL',1,0,'C'); // 8 + 90

// Kehadiran 1
$pdf->Cell(10,7,'',1,0,'C');
$pdf->Cell(15,7,'',1,0,'R');
$pdf->Cell(20,7,number_format($grand_transport,0,',','.'),1,0,'R');

// Kehadiran 2
$pdf->Cell(10,7,'',1,0,'C');
$pdf->Cell(20,7,'',1,0,'R');
$pdf->Cell(30,7,number_format($grand_barokah,0,',','.'),1,0,'R');

// Kehadiran 3
$pdf->Cell(10,7,'',1,0,'C');
$pdf->Cell(15,7,'',1,0,'R');
$pdf->Cell(20,7,number_format($grand_dinihari,0,',','.'),1,0,'R');

// Danru
$pdf->Cell(35,7,number_format($grand_danru,0,',','.'),1,0,'R'); 

// Grand Total
$pdf->Cell(40,7,number_format($grand_total,0,',','.'),1,1,'R');

// TTD
$pdf->ln(10);
$pdf->SetFont('arial','',10);
$pdf->Cell(250,5,'Situbondo, '.date('d F Y'),0,1,'R');
$pdf->Cell(250,5,'Bendahara,',0,1,'R');
$pdf->ln(20);
$pdf->SetFont('arial','B',10);
$pdf->Cell(250,5,'( ..................................... )',0,1,'R');


$pdf->Output();
?>
