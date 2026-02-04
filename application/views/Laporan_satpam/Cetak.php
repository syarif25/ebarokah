<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Barokah Satpam - <?php echo isset($header) ? htmlentities($header->nama_lembaga) : 'Lembaga'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt; /* Agak diperkecil karena kolom banyak */
            line-height: 1.3;
            color: #000;
            padding: 15px;
        }
        
        .header {
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 3px solid #000;
        }
        
        .header-container {
            display: flex;
            align-items: center;
            padding: 5px 0;
        }
        
        .header-logo {
            width: 80px;
            height: 80px;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .header-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .header-content {
            flex: 1;
            text-align: center;
        }
        
        .header-content h1 {
            font-size: 16pt;
            font-weight: bold;
            margin: 0 0 2px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .header-content h2 {
            font-size: 13pt;
            font-weight: bold;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        
        .header-content .address {
            font-size: 8.5pt;
            margin: 0;
            line-height: 1.4;
            color: #000;
        }
        
        .report-title {
            text-align: center;
            margin: 15px 0;
        }
        
        .report-title h3 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            text-decoration: underline;
            text-transform: uppercase;
        }
        
        .report-info {
            display: flex;
            justify-content: space-between;
            margin: 8px 0 12px 0;
            font-size: 10pt;
        }
        
        .report-info .left {
            width: 60%;
        }
        .report-info .right {
            width: 40%;
            text-align: right;
        }
        
        .report-info table {
            width: 100%;
            border: none;
        }
        
        .report-info td {
            padding: 2px 5px;
            border: none;
        }
        
        /* Table Style */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 9pt;
        }
        
        table.data-table th,
        table.data-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
            vertical-align: middle;
        }
        
        table.data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        
        table.data-table td.text-center { text-align: center; }
        table.data-table td.text-right { text-align: right; }
        
        table.data-table tfoot td {
            font-weight: bold;
            background-color: #f8f8f8;
        }
        
        /* Helpers */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9pt;
        }
        
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            @page {
                size: landscape;
                margin: 10mm;
            }
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12pt;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 9999;
        }
        
        .print-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php
    $total_personil = isset($data) ? count($data) : 0;
    $total_all = 0;
    
    // Hitung grand total untuk info header
    if (isset($data) && count($data) > 0) {
        foreach ($data as $d) {
            $total_all += $d->diterima;
        }
    }
    ?>
    
    <button class="print-button no-print" onclick="window.print()"><i class="fas fa-print"></i> Cetak Laporan</button>
    
    <div class="header">
        <div class="header-container">
            <div class="header-logo">
                <img src="<?php echo base_url('assets/p2s2.png'); ?>" alt="Logo">
            </div>
            <div class="header-content">
                <h1>KANTOR BENDAHARA</h1>
                <h2>PONDOK PESANTREN SALAFIYAH SYAFI'IYAH</h2>
                <p class="address">Po Box 2 telp 0388-452666 Fax. 452707 - eMail: sentral@salafiyah.net - Situbondo, 68374</p>
            </div>
        </div>
    </div>
    
    <div class="report-title">
        <h3>LAPORAN BAROKAH SATPAM</h3>
    </div>
    
    <div class="report-info">
        <div class="left">
            <table style="width: auto;">
                <tr>
                    <td width="100">Unit/Bagian</td>
                    <td>: <strong><?php echo isset($header) ? htmlentities($header->nama_lembaga) : '-'; ?></strong></td>
                </tr>
                <tr>
                    <td>Periode</td>
                    <td>: <?php echo isset($header) ? htmlentities($header->bulan . ' ' . $header->tahun) : '-'; ?></td>
                </tr>
            </table>
        </div>
        <div class="right">
            <table style="width: auto; float: right;">
                <tr>
                    <td class="text-right">Total Personil</td>
                    <td>: <strong><?php echo $total_personil; ?> Orang</strong></td>
                </tr>
                <tr>
                    <td class="text-right">Total Pencairan</td>
                    <td>: <strong><?php echo rupiah($total_all); ?></strong></td>
                </tr>
            </table>
        </div>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" width="5%">No</th>
                <th rowspan="2">Nama Lengkap</th>
                <th colspan="3">Transport</th>
                <th colspan="3">Barokah (Shift)</th>
                <th colspan="3">Dinihari</th>
                <th rowspan="2" width="10%">Tunj. Danru</th>
                <th rowspan="2" width="12%">Total Diterima</th>
            </tr>
            <tr>
                <!-- Transport -->
                <th width="5%">Hari</th>
                <th width="10%">@ Nominal</th>
                <th width="10%">Jumlah</th>
                
                <!-- Barokah -->
                <th width="5%">Shift</th>
                <th width="10%">@ Nominal</th>
                <th width="10%">Jumlah</th>
                
                <!-- Dinihari -->
                <th width="5%">Kali</th>
                <th width="10%">@ Nominal</th>
                <th width="10%">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $sum_hari = 0; $sum_trans_jml = 0;
            $sum_shift = 0; $sum_bar_jml = 0;
            $sum_dini = 0; $sum_dini_jml = 0;
            $sum_danru_jml = 0;
            $grand_total = 0;
            
            if (isset($data) && !empty($data)) {
                foreach ($data as $d) {
                    // Update totals
                    $sum_hari += $d->jumlah_hari;
                    $sum_trans_jml += $d->jumlah_transport;
                    $sum_shift += $d->jumlah_shift;
                    $sum_bar_jml += $d->jumlah_barokah;
                    $sum_dini += $d->jumlah_dinihari;
                    $sum_dini_jml += $d->jumlah_konsumsi;
                    $sum_danru_jml += $d->nominal_danru;
                    $grand_total += $d->diterima;
            ?>
            <tr>
                <td class="text-center"><?php echo $no++; ?></td>
                <td>
                    <?php 
                        $nama = $d->nama_lengkap;
                        if(!empty($d->gelar_depan)) $nama = $d->gelar_depan . ' ' . $nama;
                        if(!empty($d->gelar_belakang)) $nama = $nama . ', ' . $d->gelar_belakang;
                        echo htmlentities($nama);
                    ?>
                </td>
                
                <!-- Transport -->
                <td class="text-center"><?php echo $d->jumlah_hari; ?></td>
                <td class="text-right"><?php echo rupiah($d->nominal_transport); ?></td>
                <td class="text-right"><?php echo rupiah($d->jumlah_transport); ?></td>
                
                <!-- Barokah -->
                <td class="text-center"><?php echo $d->jumlah_shift; ?></td>
                <td class="text-right"><?php echo rupiah($d->rank); ?></td>
                <td class="text-right"><?php echo rupiah($d->jumlah_barokah); ?></td>
                
                <!-- Dinihari -->
                <td class="text-center"><?php echo $d->jumlah_dinihari; ?></td>
                <td class="text-right"><?php echo rupiah($d->konsumsi); ?></td>
                <td class="text-right"><?php echo rupiah($d->jumlah_konsumsi); ?></td>

                <!-- Danru -->
                <td class="text-right"><?php echo rupiah($d->nominal_danru); ?></td>
                
                <td class="text-right font-bold"><?php echo rupiah($d->diterima); ?></td>
            </tr>
            <?php 
                }
            } else {
            ?>
            <tr>
                <td colspan="12" class="text-center">Data tidak ditemukan</td>
            </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-center"><strong>TOTAL</strong></td>
                
                <!-- Transport -->
                <td class="text-center"><strong><?php echo $sum_hari; ?></strong></td>
                <td></td>
                <td class="text-right"><strong><?php echo rupiah($sum_trans_jml); ?></strong></td>
                
                <!-- Barokah -->
                <td class="text-center"><strong><?php echo $sum_shift; ?></strong></td>
                <td></td>
                <td class="text-right"><strong><?php echo rupiah($sum_bar_jml); ?></strong></td>
                
                <!-- Dinihari -->
                <td class="text-center"><strong><?php echo $sum_dini; ?></strong></td>
                <td></td>
                <td class="text-right"><strong><?php echo rupiah($sum_dini_jml); ?></strong></td>
                
                <!-- Danru -->
                <td class="text-right"><strong><?php echo rupiah($sum_danru_jml); ?></strong></td>

                <td class="text-right"><strong><?php echo rupiah($grand_total); ?></strong></td>
            </tr>
        </tfoot>
    </table>
    
    <div class="footer">
        <p><i>Dicetak oleh sistem e-Barokah pada tanggal: <?php echo date('d F Y H:i'); ?></i></p>
    </div>
</body>
</html>
