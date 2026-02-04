<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Barokah - <?php echo isset($data_rincian[0]) ? htmlentities($data_rincian[0]->nama_lembaga) : 'Lembaga'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
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
            width: 100px;
            height: 100px;
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
            margin: 8px 0;
        }
        
        .report-title h3 {
            font-size: 13pt;
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
        
        .report-info .left,
        .report-info .right {
            width: 45%;
        }
        
        .report-info table {
            width: 100%;
            border: none;
        }
        
        .report-info td {
            padding: 2px 5px;
            border: none;
        }
        
        .report-info td:first-child {
            width: 35%;
            font-weight: 600;
        }
        
        .info-section {
            margin: 15px 0;
        }
        
        .info-section table {
            width: 100%;
            margin-bottom: 10px;
        }
        
        .info-section td {
            padding: 3px 0;
        }
        
        .info-section td:first-child {
            width: 150px;
            font-weight: bold;
        }
        
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
        }
        
        table.data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        
        table.data-table td.text-center {
            text-align: center;
        }
        
        table.data-table td.text-right {
            text-align: right;
        }
        
        table.data-table tfoot td {
            font-weight: bold;
            background-color: #f8f8f8;
        }
        
        .summary-box {
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
        }
        
        .summary-item {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            margin: 0 5px;
            text-align: center;
        }
        
        .summary-item strong {
            display: block;
            font-size: 9pt;
            color: #666;
            margin-bottom: 5px;
        }
        
        .summary-item span {
            display: block;
            font-size: 12pt;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 20px;
            text-align: right;
        }
        
        .signature {
            display: inline-block;
            text-align: center;
            margin-top: 60px;
        }
        
        .signature-line {
            width: 200px;
            border-top: 1px solid #000;
            margin-top: 50px;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
            
            @page {
                size: landscape;
                margin: 15mm;
            }
        }
        
        .print-button {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12pt;
        }
        
        .print-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php
    // Calculate totals before using them
    $total_umana = isset($data_rincian) ? count($data_rincian) : 0;
    $total_barokah = 0;
    if (isset($data_rincian) && count($data_rincian) > 0) {
        foreach ($data_rincian as $data) {
            $total_barokah += $data->diterima;
        }
    }
    $rata_rata = $total_umana > 0 ? $total_barokah / $total_umana : 0;
    ?>
    
    <button class="print-button no-print" onclick="window.print()"><i class="fas fa-print"></i> Cetak</button>
    
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
        <h3>LAPORAN BAROKAH PER LEMBAGA</h3>
    </div>
    
    <div class="report-info">
        <div class="left">
            <table>
                <tr>
                    <td>Lembaga</td>
                    <td>: <?php echo isset($data_rincian[0]) ? htmlentities($data_rincian[0]->nama_lembaga) : '-'; ?></td>
                </tr>
                <tr>
                    <td>Periode</td>
                    <td>: <?php echo isset($data_rincian[0]) ? htmlentities($data_rincian[0]->bulan . ' ' . $data_rincian[0]->tahun) : '-'; ?></td>
                </tr>
            </table>
        </div>
        <div class="right">
            <table>
                <tr>
                    <td>Jumlah Umana</td>
                    <td>: <?php echo $total_umana; ?> Orang</td>
                </tr>
                <tr>
                    <td>Total Barokah</td>
                    <td>: Rp <?php echo number_format($total_barokah, 0, ',', '.'); ?></td>
                </tr>
            </table>
        </div>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Nama Lengkap</th>
                <th rowspan="2">Jabatan</th>
                <th rowspan="2">TMT</th>
                <th rowspan="2">Tunjab</th>
                <th rowspan="2">MP</th>
                <th colspan="2">Kehadiran</th>
                <th rowspan="2">Tunkel</th>
                <th rowspan="2">Tunj Anak</th>
                <th rowspan="2">TBK</th>
                <th rowspan="2">TMP</th>
                <th rowspan="2">Kehormatan</th>
                <th rowspan="2">Potongan</th>
                <th rowspan="2">Diterima</th>
            </tr>
            <tr>
                <th>Hari</th>
                <th>Nominal</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $total_tunjab = 0;
            $total_hadir_nominal = 0;
            $total_tunkel = 0;
            $total_tunj_anak = 0;
            $total_tbk = 0;
            $total_tmp = 0;
            $total_kehormatan = 0;
            $total_potongan = 0;
            
            if (isset($data_rincian) && !empty($data_rincian)) {
                foreach ($data_rincian as $data) {
                    // Build nama lengkap with gelar
                    $nama_lengkap = '';
                    if (!empty($data->gelar_depan)) {
                        $nama_lengkap .= $data->gelar_depan . ' ';
                    }
                    $nama_lengkap .= $data->nama_lengkap;
                    if (!empty($data->gelar_belakang)) {
                        $nama_lengkap .= ', ' . $data->gelar_belakang;
                    }
                    
                    // Calculate totals
                    $total_tunjab += $data->barokah;
                    $total_hadir_nominal += $data->nominal_kehadiran;
                    $total_tunkel += $data->tunkel;
                    $total_tunj_anak += $data->tunj_anak;
                    $total_tbk += $data->tbk;
                    $total_tmp += $data->tmp;
                    $total_kehormatan += $data->kehormatan;
                    $total_potongan += $data->potongan;
            ?>
            <tr>
                <td class="text-center"><?php echo $no++; ?></td>
                <td><?php echo htmlentities($nama_lengkap); ?></td>
                <td><?php echo htmlentities($data->nama_jabatan); ?></td>
                <td class="text-center"><?php echo htmlentities(substr($data->tmt_struktural, 0, 4)); ?></td>
                <td class="text-right"><?php echo rupiah($data->barokah); ?></td>
                <td class="text-center"><?php echo htmlentities($data->mp); ?></td>
                <td class="text-center"><?php echo htmlentities($data->jumlah_hadir); ?></td>
                <td class="text-right"><?php echo rupiah($data->nominal_kehadiran); ?></td>
                <td class="text-right"><?php echo rupiah($data->tunkel); ?></td>
                <td class="text-right"><?php echo rupiah($data->tunj_anak); ?></td>
                <td class="text-right"><?php echo rupiah($data->tbk); ?></td>
                <td class="text-right"><?php echo rupiah($data->tmp); ?></td>
                <td class="text-right"><?php echo rupiah($data->kehormatan); ?></td>
                <td class="text-right"><?php echo rupiah($data->potongan); ?></td>
                <td class="text-right"><strong><?php echo rupiah($data->diterima); ?></strong></td>
            </tr>
            <?php 
                }
            } else {
            ?>
            <tr>
                <td colspan="15" class="text-center">Tidak ada data</td>
            </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-center"><strong>TOTAL</strong></td>
                <td class="text-right"><?php echo rupiah($total_tunjab); ?></td>
                <td></td>
                <td></td>
                <td class="text-right"><?php echo rupiah($total_hadir_nominal); ?></td>
                <td class="text-right"><?php echo rupiah($total_tunkel); ?></td>
                <td class="text-right"><?php echo rupiah($total_tunj_anak); ?></td>
                <td class="text-right"><?php echo rupiah($total_tbk); ?></td>
                <td class="text-right"><?php echo rupiah($total_tmp); ?></td>
                <td class="text-right"><?php echo rupiah($total_kehormatan); ?></td>
                <td class="text-right"><?php echo rupiah($total_potongan); ?></td>
                <td class="text-right"><strong><?php echo rupiah($total_barokah); ?></strong></td>
            </tr>
        </tfoot>
    </table>
    
    <div class="footer">
        <p>Dicetak pada: <?php echo date('d F Y'); ?></p>
        <!-- <div class="signature">
            <p>Mengetahui,</p>
            <div class="signature-line"></div>
            <p>( ....................... )</p>
        </div> -->
    </div>
    
    <script>
        // Auto print on page load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
