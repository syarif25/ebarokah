<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengajar</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 4 & Icons -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.4.55/css/materialdesignicons.min.css">
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.bootstrap4.min.css">

    <style>
        :root {
            /* Premium Palette */
            --bg-body: #f4f6f8;
            --text-main: #2d3748;
            --text-muted: #718096;
            --border-color: #e2e8f0;
            
            /* Brand Colors for Columns */
            --col-input-bg: #fffbf0; /* Soft warm for inputs */
            --col-input-border: #fefcbf;
            --col-calc-bg: #ebf8ff; /* Soft blue for calcs */
            --col-result-bg: #f0fff4; /* Soft green for result */
            --col-result-text: #2f855a;
            
            /* Header */
            --header-bg: #1a202c;
            --header-text: #fff;
        }

        body { 
            background-color: var(--bg-body); 
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            color: var(--text-main);
            -webkit-font-smoothing: antialiased;
        }

        h4 { font-weight: 700; letter-spacing: -0.5px; color: #1a202c; }
        .card { border: none; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1), 0 1px 2px 0 rgba(0,0,0,0.06); border-radius: 8px; }
        
        /* Badges Redesign */
        .badge-premium {
            padding: 0.6em 1em;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
            letter-spacing: 0.3px;
            border: 1px solid transparent;
        }
        .badge-premium.primary { background: #ebf4ff; color: #4299e1; border-color: #bee3f8; }
        .badge-premium.success { background: #f0fff4; color: #38a169; border-color: #c6f6d5; }
        .badge-premium.warning { background: #fffaf0; color: #dd6b20; border-color: #fbd38d; }
        .badge-premium.info { background: #e6fffa; color: #319795; border-color: #b2f5ea; }

        /* Table Styling */
        table.table-custom {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }
        table.table-custom thead th {
            background-color: #fff;
            color: #4a5568;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 12px 10px;
            border-bottom: 2px solid var(--border-color);
            border-top: none;
            vertical-align: middle !important;
        }
        
        /* Custom Column Headers */
        th.header-group-input { background-color: #fffae0 !important; color: #975a16; border-bottom-color: #f6e05e !important; }
        th.header-group-calc { background-color: #e6fffa !important; color: #285e61; border-bottom-color: #81e6d9 !important; }
        th.header-group-result { background-color: #f0fff4 !important; color: #22543d; border-bottom-color: #9ae6b4 !important; }

        table.table-custom tbody td, table.table-custom tfoot td {
            padding: 12px 10px;
            border-color: var(--border-color);
            vertical-align: middle;
            color: #4a5568;
            font-weight: 500;
        }
        
        table.table-custom tfoot td {
            font-weight: 700;
            background-color: #fafafa;
            border-top: 2px solid #cbd5e0 !important;
        }

        /* Column Highlights */
        .col-input { background-color: var(--col-input-bg); }
        .col-calc { background-color: var(--col-calc-bg); }
        .col-result { background-color: var(--col-result-bg); color: var(--col-result-text); font-weight: 700; }

        /* Row Hover */
        table.table-custom tbody tr:hover td {
            background-color: #f7fafc;
            color: #1a202c;
        }

        /* Utils */
        .cell-right { text-align: right; }
        .cell-center { text-align: center; }
        .text-xs { font-size: 0.75rem; }
    </style>
</head>
<body>

<?php
// --- CALCULATION LOGIC (SNAPSHOT MODE) ---
// Since this is a Report View, we assume all data comes from Snapshot Table (total_barokah_pengajar)
// The Controller passes $data_rincian (renamed to $isilist for compatibility if needed, or we use $data_rincian directly)

if(!isset($data_rincian) && isset($isilist)) {
    $data_rincian = $isilist; // Fallback compatibility
}

// Global Totals
$total_transfer_all = 0;
$total_transport_all = 0;
$total_potongan_all = 0;
$count_pengajar = 0;

// Footer Accumulators
$sum_rank = 0;
$sum_mengajar = 0;
$sum_dty = 0;
$sum_jafung = 0;
$sum_jml_kehadiran = 0;
$sum_nominal_hadir_15 = 0;
$sum_nominal_hadir_10 = 0;
$sum_rank_piket = 0;
$sum_barokah_piket = 0;
$sum_tunkel = 0;
$sum_tunja_anak = 0;
$sum_kehormatan = 0;
$sum_tunj_walkes = 0;
$sum_tambahan = 0;
$sum_potongan = 0;
$sum_jumlah = 0; 
$sum_diterima = 0;

$processed_data = [];

if (isset($data_rincian) && !empty($data_rincian)) {
    $count_pengajar = count($data_rincian);

    foreach($data_rincian as $key){
        // -- USE SNAPSHOT DATA DIRECTLY --
        $jml_kehadiran = $key->nominal_kehadiran;
        $nominal_hadir_15 = $key->nominal_hadir_15;
        $nominal_hadir_10 = $key->nominal_hadir_10;
        
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
        
        // Aux display vars
        $masa_p = $key->mp;
        // TMT logic: use existing snapshot fields if available, otherwise fallback/empty
        // snapshot table has 'mp', but not 'tmt'. We just display mp.
        
        // TOTAL ROWS
        $total_transport_row = $jml_kehadiran + $nominal_hadir_15 + $nominal_hadir_10;
        
        $diterima = $barokah_piket + $jml_kehadiran + $nominal_hadir_15 + $nominal_hadir_10 + $tunkel + $tunja_anak + $mengajar + $dty + $jafung + $kehormatan + $tunj_walkes + $tambahan - $potongan;
        $jumlah = $barokah_piket + $jml_kehadiran + $nominal_hadir_15 + $nominal_hadir_10 + $tunkel + $tunja_anak + $mengajar + $dty + $jafung + $kehormatan + $tunj_walkes + $tambahan;
        
        // Accumulate Global Totals
        $total_transfer_all += $diterima;
        $total_transport_all += $total_transport_row; 
        $total_potongan_all += $potongan;

        // Accumulate Footer Sums
        $sum_rank += $rank;
        $sum_mengajar += $mengajar;
        $sum_dty += $dty;
        $sum_jafung += $jafung;
        $sum_jml_kehadiran += $jml_kehadiran;
        $sum_nominal_hadir_15 += $nominal_hadir_15;
        $sum_nominal_hadir_10 += $nominal_hadir_10;
        $sum_rank_piket += $rank_piket;
        $sum_barokah_piket += $barokah_piket;
        $sum_tunkel += $tunkel;
        $sum_tunja_anak += $tunja_anak;
        $sum_kehormatan += $kehormatan;
        $sum_tunj_walkes += $tunj_walkes;
        $sum_tambahan += $tambahan;
        $sum_potongan += $potongan;
        $sum_jumlah += $jumlah; 
        $sum_diterima += $diterima;

        // Store Processed Data
        $processed_data[] = [
            'key' => $key, 
            'calc' => [
                'masa_p' => $masa_p,
                'tunkel' => $tunkel,
                'tunja_anak' => $tunja_anak,
                'tunj_walkes' => $tunj_walkes,
                'rank' => $rank,
                'mengajar' => $mengajar,
                'dty' => $dty,
                'jafung' => $jafung,
                'kehormatan' => $kehormatan,
                'potongan' => $potongan,
                'tambahan' => $tambahan,
                'jumlah' => $jumlah, 
                'rank_piket' => $rank_piket,
                'barokah_piket' => $barokah_piket,
                'jml_kehadiran' => $jml_kehadiran,
                'nominal_hadir_15' => $nominal_hadir_15,
                'nominal_hadir_10' => $nominal_hadir_10,
                'diterima' => $diterima
            ]
        ];
    }
}
?>

<div class="container-fluid px-4 py-4">
    
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="mdi mdi-checkbox-multiple-marked-circle-outline mr-2 text-success"></i>Laporan Kehadiran Pengajar (Final)</h4>
            <div class="d-flex align-items-center text-muted">
                <!-- Using header_info from controller -->
                <span class="mr-3"><i class="mdi mdi-calendar mr-1"></i> <?php echo (isset($header_info)) ? $header_info->bulan . ' ' . $header_info->tahun : ''; ?></span>
                <span><i class="mdi mdi-domain mr-1"></i> <?php echo (isset($header_info)) ? $header_info->nama_lembaga : ''; ?></span>
            </div>
        </div>
        <div>
            <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm px-3 rounded-pill font-weight-bold">
                <i class="mdi mdi-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Stats Cards (Read Only) -->
     <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 py-2 border-left-success shadow-sm" style="border-left: 4px solid #48bb78 !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Transfer</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo rupiah($total_transfer_all); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 py-2 border-left-info shadow-sm" style="border-left: 4px solid #4299e1 !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Transport</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo rupiah($total_transport_all); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 py-2 border-left-danger shadow-sm" style="border-left: 4px solid #f56565 !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Potongan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo rupiah($total_potongan_all); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cut fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 py-2 border-left-warning shadow-sm" style="border-left: 4px solid #ed8936 !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Pengajar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $count_pengajar; ?> Orang</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table -->
    <div class="card overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="tblValidasi" class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <!-- Fixed ID/Name -->
                            <th rowspan="2" class="text-center bg-white sticky-col-first" style="width: 40px; border-bottom: 2px solid #e2e8f0;">No</th>
                            <th rowspan="2" class="bg-white sticky-col-second" style="min-width: 250px; border-bottom: 2px solid #e2e8f0;">Nama Pengajar</th>
                            
                            <!-- Metric Groups (Requested specific ordering) -->
                            <th rowspan="2" class="text-center border-left header-group-input" style="min-width: 60px;">IT</th>
                            <th rowspan="2" class="text-center header-group-input" style="min-width: 50px;">MP</th>
                            <th rowspan="2" class="text-center header-group-input" style="min-width: 70px;">Jam/SKS</th>
                            <th rowspan="2" class="text-center header-group-calc text-muted" style="min-width: 90px;">Rank</th>
                            <th rowspan="2" class="text-center header-group-calc" style="min-width: 100px;">Mengajar</th>
                            <th rowspan="2" class="text-center header-group-calc" style="min-width: 100px;">DTY</th>
                            <th rowspan="2" class="text-center header-group-calc" style="min-width: 100px;">Jafung</th>
                            
                            <!-- Kehadiran Breakdown -->
                            <th colspan="2" class="text-center border-left header-group-input">Hadir Normal</th>
                            <th colspan="2" class="text-center header-group-input">Hadir 15rb</th>
                            <th colspan="2" class="text-center header-group-input">Hadir 10rb</th>
                            <th colspan="3" class="text-center header-group-input">Piket</th>
                            
                            <!-- Tunjangan -->
                            <th colspan="5" class="text-center border-left header-group-calc">Tunjangan Lain</th>
                            
                            <th rowspan="2" class="text-center header-group-calc text-muted" style="min-width: 100px; border-bottom: 2px solid #e2e8f0;">Jumlah</th>
                            <th rowspan="2" class="text-center border-left text-danger" style="min-width: 100px; border-bottom: 2px solid #e2e8f0;">Potongan</th>
                            <th rowspan="2" class="text-right border-left header-group-result" style="min-width: 120px; border-bottom: 2px solid #e2e8f0;">Diterima</th>
                        </tr>
                        <tr>
                            <!-- Subheaders Kehadiran -->
                            <th class="text-center border-left col-input-head" style="width:60px; font-size: 11px;">Jml</th>
                            <th class="text-right col-input-head" style="font-size: 11px;">Nominal</th>
                            
                            <th class="text-center col-input-head" style="width:60px; font-size: 11px;">Jml</th>
                            <th class="text-right col-input-head" style="font-size: 11px;">Nominal</th>
                            
                            <th class="text-center col-input-head" style="width:60px; font-size: 11px;">Jml</th>
                            <th class="text-right col-input-head" style="font-size: 11px;">Nominal</th>
                            
                            <th class="text-center col-input-head" style="width:60px; font-size: 11px;">Jml</th>
                            <th class="text-right col-input-head" style="font-size: 11px;">Rate</th>
                            <th class="text-right col-input-head" style="font-size: 11px;">Nominal</th>
                            
                            <!-- Subheaders Tunjangan -->
                            <th class="text-right border-left col-calc-head" style="font-size: 11px;">Tunkel</th>
                            <th class="text-right col-calc-head" style="font-size: 11px;">Anak</th>
                            <th class="text-right col-calc-head" style="font-size: 11px;">Kehormatan</th>
                            <th class="text-right col-calc-head" style="font-size: 11px;">Walkes</th>
                            <th class="text-right col-calc-head" style="font-size: 11px;">Tambahan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        // Loop through PRE-CALCULATED data
                        foreach($processed_data as $row){
                            $key = $row['key'];
                            $calc = $row['calc'];
                        ?>
                        <tr>
                            <td class="cell-center text-muted sticky-col-first bg-white border-right"><?php echo $no++; ?></td>
                            <td class="sticky-col-second bg-white border-right">
                                <div class="font-weight-bold text-dark"><?php echo htmlentities((isset($key->gelar_depan) ? $key->gelar_depan : '').' '.$key->nama_lengkap.' '.(isset($key->gelar_belakang) ? $key->gelar_belakang : '')); ?></div>
                                <span class="badge badge-light border mt-1" style="font-size: 0.7rem;"><?php echo htmlentities($key->kategori); ?></span>
                            </td>
                            
                            <!-- Requested Columns -->
                            <td class="cell-center border-left align-middle"><?php echo (isset($key->ijazah_terakhir)) ? htmlentities($key->ijazah_terakhir) : '-'; ?></td>
                            <td class="cell-center align-middle"><?php echo $calc['masa_p']; ?> Thn</td>
                            <td class="cell-center align-middle font-weight-bold"><?php echo htmlentities($key->jumlah_sks); ?></td>
                            <td class="cell-right text-muted align-middle"><?php echo rupiah($calc['rank']); ?></td>
                            <td class="cell-right font-weight-bold text-dark align-middle"><?php echo rupiah($calc['mengajar']); ?></td>
                            <td class="cell-right align-middle"><?php echo rupiah($calc['dty']); ?></td>
                            <td class="cell-right align-middle"><?php echo rupiah($calc['jafung']); ?></td>
                            
                            <!-- Kehadiran Breakdown -->
                            <!-- Normal -->
                            <td class="cell-center border-left col-input"><?php echo $key->jumlah_hadir; ?></td>
                            <td class="cell-right text-muted align-middle"><?php echo rupiah($calc['jml_kehadiran']); ?></td>
                            
                            <!-- 15rb -->
                            <td class="cell-center col-input"><?php echo $key->jumlah_hadir_15; ?></td>
                            <td class="cell-right text-muted align-middle"><?php echo rupiah($calc['nominal_hadir_15']); ?></td>
                            
                            <!-- 10rb -->
                            <td class="cell-center col-input"><?php echo $key->jumlah_hadir_10; ?></td>
                            <td class="cell-right text-muted align-middle"><?php echo rupiah($calc['nominal_hadir_10']); ?></td>
                            
                             <!-- Piket -->
                            <td class="cell-center col-input"><?php echo $key->jumlah_hadir_piket; ?></td>
                            <td class="cell-right text-xs text-muted align-middle"><?php echo rupiah($calc['rank_piket']); ?></td>
                            <td class="cell-right text-muted align-middle"><?php echo rupiah($calc['barokah_piket']); ?></td>
                            
                            <!-- Tunjangan breakdown -->
                            <td class="cell-right border-left align-middle"><?php echo rupiah($calc['tunkel']); ?></td>
                            <td class="cell-right align-middle"><?php echo rupiah($calc['tunja_anak']); ?></td>
                            <td class="cell-right align-middle"><?php echo rupiah($calc['kehormatan']); ?></td>
                            <td class="cell-right align-middle"><?php echo rupiah($calc['tunj_walkes']); ?></td>
                            <td class="cell-right align-middle"><?php echo rupiah($calc['tambahan']); ?></td>
                            
                            <!-- Potongan & Result -->
                            <td class="cell-right border-left align-middle text-muted"><?php echo rupiah($calc['jumlah']); ?></td>
                            <td class="cell-right border-left text-danger align-middle"><?php echo rupiah($calc['potongan']); ?></td>
                            <td class="cell-right border-left col-result align-middle"><?php echo rupiah($calc['diterima']); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <!-- TOTAL ROW -->
                        <tr>
                            <!-- Sticky Cols (No, Nama) -->
                            <td class="sticky-col-first bg-light border-right text-center font-weight-bold"></td>
                            <td class="sticky-col-second bg-light border-right font-weight-bold text-uppercase text-right px-3">Total Seluruh:</td>
                            
                            <!-- Inputs Group (IT, MP, Jam) - Empty/Merged -->
                            <td colspan="3" class="bg-light border-right border-left"></td>
                            
                            <td class="text-right border-left bg-light font-weight-bold"></td>
                            <td class="text-right bg-light font-weight-bold"><?php echo rupiah($sum_mengajar); ?></td>
                            <td class="text-right bg-light font-weight-bold"><?php echo rupiah($sum_dty); ?></td>
                            <td class="text-right bg-light font-weight-bold"><?php echo rupiah($sum_jafung); ?></td>

                            <!-- Kehadiran Breakdown -->
                            <!-- Normal -->
                            <td class="bg-light border-left"></td>
                            <td class="text-right bg-light font-weight-bold"><?php echo rupiah($sum_jml_kehadiran); ?></td>
                            
                            <!-- 15rb -->
                            <td class="bg-light"></td>
                            <td class="text-right bg-light font-weight-bold"><?php echo rupiah($sum_nominal_hadir_15); ?></td>
                            
                            <!-- 10rb -->
                            <td class="bg-light"></td>
                            <td class="text-right bg-light font-weight-bold"><?php echo rupiah($sum_nominal_hadir_10); ?></td>
                            
                             <!-- Piket -->
                            <td class="bg-light"></td>
                            <td class="bg-light"></td>
                            <td class="text-right bg-light font-weight-bold"><?php echo rupiah($sum_barokah_piket); ?></td>
                            
                            <!-- Tunjangan -->
                            <td class="text-right border-left bg-light font-weight-bold"><?php echo rupiah($sum_tunkel); ?></td>
                            <td class="text-right bg-light font-weight-bold"><?php echo rupiah($sum_tunja_anak); ?></td>
                            <td class="text-right bg-light font-weight-bold"><?php echo rupiah($sum_kehormatan); ?></td>
                            <td class="text-right bg-light font-weight-bold"><?php echo rupiah($sum_tunj_walkes); ?></td>
                            <td class="text-right bg-light font-weight-bold"><?php echo rupiah($sum_tambahan); ?></td>
                            
                            <!-- Potongan & Result -->
                            <td class="text-right border-left bg-light font-weight-bold text-muted"><?php echo rupiah($sum_jumlah); ?></td>
                            <td class="text-right border-left bg-light text-danger font-weight-bold"><?php echo rupiah($sum_potongan); ?></td>
                            <td class="text-right border-left bg-light text-success font-weight-bold"><?php echo rupiah($sum_diterima); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    
    <div style="height: 100px;"></div>

    <!-- Sticky Actions for Report (Only Cetak, maybe Potongan) -->
    <div style="position: fixed; bottom: 0; left: 0; right: 0; background: #fff; padding: 1rem 2rem; box-shadow: 0 -4px 6px -1px rgba(0,0,0,0.05); z-index: 1050; display: flex; align-items: center; justify-content: flex-end; border-top: 1px solid #e2e8f0;">
        <?php if(!empty($meta_file)): ?>
        <a href="<?php echo base_url() ?>upload/<?php echo $meta_file; ?>" target="_blank" class="btn btn-outline-info btn-sm mr-2 rounded-pill">
            <i class="mdi mdi-file-document-outline mr-1"></i> File Kehadiran
        </a>
        <?php endif; ?>
        
        <a href="<?php echo base_url('Laporan_pengajar/cetak/'.$encrypted_id); ?>" target="_blank" class="btn btn-info px-4 shadow-sm font-weight-bold mr-2"><i class="mdi mdi-printer mr-1"></i> Cetak Laporan</a>
        
        <?php if (isset($meta_id_lembaga)): ?>
         <a href="<?php echo base_url('Kehadiran/Cetak_Potongan_pengajar/'.$meta_id_lembaga); ?>" target="_blank" class="btn btn-danger px-4 shadow-sm font-weight-bold">
            <i class="mdi mdi-content-cut mr-1"></i> Cetak Potongan
        </a>
        <?php endif; ?>
    </div>

</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tblValidasi').DataTable({
            paging: false,
            searching: true,
            info: false,
            scrollY: '60vh',
            scrollCollapse: true,
            scrollX: true,
            ordering: false
        });
    });
</script>
</body>
</html>
