<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Pengajar</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 4 & Icons -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Icon md (Material Design) -->
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

        /* Input override for cleaner look */
        .table-input {
            border: 1px solid transparent;
            background: transparent;
            width: 100%;
            text-align: center;
            font-weight: 600;
            color: inherit;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .table-input:hover { border-color: #cbd5e0; background: #fff; }
        .table-input:focus { 
            border-color: #4299e1; 
            background: #fff; 
            outline: none; 
            box-shadow: 0 0 0 3px rgba(66,153,225,0.2); 
        }

        /* Sticky Footer */
        .sticky-action-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            padding: 1rem 2rem;
            box-shadow: 0 -4px 6px -1px rgba(0,0,0,0.05);
            z-index: 1050;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid var(--border-color);
        }
        .content-spacer { height: 100px; }
        
        /* Utils */
        .cell-right { text-align: right; }
        .cell-center { text-align: center; }
        .text-xs { font-size: 0.75rem; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php
// Prepare global totals
$total_transfer_all = 0;
$total_transport_all = 0;
$total_potongan_all = 0;
$count_pengajar = 0;
$status_pengiriman = "Belum Dikirim"; // Default
$badge_status_class = "badge-warning";
$is_readonly = false; // Default Not Readonly

// Accumulators for Footer
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
$sum_jumlah = 0; // New accumulator
$sum_diterima = 0;

// Init auxiliary data
if(isset($isitunkel)) foreach($isitunkel as $nominaltunkel);
if(isset($isitunj_anak)) foreach($isitunj_anak as $nominaltunj_anak);

if (isset($isilist) && !empty($isilist)) {
    $count_pengajar = count($isilist);
    
    // Check Status for Readonly
    $first_status = reset($isilist)->status;
    $status_pengiriman = $first_status; // Update default status_pengiriman
    
    // Map Status to Badge and set readonly flag
    if($first_status == 'Sudah') {
        $badge_status_class = "badge-danger";
        $status_pengiriman = "Belum Dikirim";
    } elseif($first_status == 'Terkirim') {
        $badge_status_class = "badge-warning";
        $status_pengiriman = "Menunggu Koreksi";
        // Approver (Non-AdminLembaga) can edit/approve
        if ($this->session->userdata('jabatan') != 'AdminLembaga') {
             $is_readonly = false;
        } else {
             $is_readonly = true;
        } 
    } elseif($first_status == 'acc') {
        $badge_status_class = "badge-info";
        $status_pengiriman = "Disetujui / Sedang Proses";
        $is_readonly = true;
    } elseif($first_status == 'Sudah Transfer') { // Adjust based on exact DB string
        $badge_status_class = "badge-success";
        $is_readonly = true;
    }

    foreach($isilist as $key){
        // --- LOGIC: SNAPSHOT VS LIVE CALCULATION ---
        if(isset($is_snapshot) && $is_snapshot) {
            // -- USE SNAPSHOT DATA --
            // Mapping fields from total_barokah_pengajar
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
            $tunja_anak = $key->tun_anak; // Note: Schema uses tun_anak
            $kehormatan = $key->kehormatan;
            $tunj_walkes = $key->walkes; // Schema uses walkes
            $tambahan = $key->khusus; // Schema uses khusus
            $potongan = $key->potongan;
            
            // Aux display vars
            $masa_p = $key->mp;
            $tmt_display = "-"; // Not stored in snapshot, maybe irrelevant or can take from basic data if joined
            
            // Snapshot already has final values
            $diterima = $key->diterima;
            $jumlah = $key->diterima + $key->potongan; // Reverse calculate
            
        } else {
            // -- LIVE CALCULATION (Original Logic) --

        $jml_kehadiran = $key->jumlah_hadir * $key->nominal_transport;
        $nominal_hadir_15 = $key->jumlah_hadir_15 * 15000;
        $nominal_hadir_10 = $key->jumlah_hadir_10 * 10000;
        // Default Values
        $tahun_default = (int)date('Y');
        if (isset($tahun_acuan_map['Default']))      $tahun_default = $tahun_acuan_map['Default'];
        if (isset($tahun_acuan_map['Pengurus']))     $tahun_default = $tahun_acuan_map['Pengurus'];
        if (isset($tahun_acuan_map['Kantor Pusat'])) $tahun_default = $tahun_acuan_map['Kantor Pusat'];

        $tahun_madrasah = isset($tahun_acuan_map['Bidang DIKJAR-M']) ? $tahun_acuan_map['Bidang DIKJAR-M'] : $tahun_default;
        $tahun_sekolah = isset($tahun_acuan_map['Bidang DIKJAR']) ? $tahun_acuan_map['Bidang DIKJAR'] : $tahun_default;
        $tahun_pt = isset($tahun_acuan_map['Bidang DIKTI']) ? $tahun_acuan_map['Bidang DIKTI'] : $tahun_default;
        
        if (($key->kategori == 'GTY' && $key->id_bidang == "Bidang DIKJAR-M") || ($key->kategori == 'GTT' && $key->id_bidang == "Bidang DIKJAR-M")) {
            $mp = $tahun_madrasah - date("Y", strtotime($key->tmt_guru));
            $masa_p = ($mp == 0) ? 0 : $mp;
        } elseif (($key->kategori == 'GTY' && $key->id_bidang == "Bidang DIKJAR") || ($key->kategori == 'GTT' && $key->id_bidang == "Bidang DIKJAR")) {
            $mp = $tahun_sekolah - date("Y", strtotime($key->tmt_guru));
            $masa_p = ($mp == 0) ? 0 : $mp;
        } elseif (($key->kategori == 'DTY' && $key->nama_lembaga == "Ma'had Aly Sukorejo") || ($key->kategori == 'DTT' && $key->nama_lembaga == "Ma'had Aly Sukorejo")) {
            $mp = $tahun_pt - date("Y", strtotime($key->tmt_maif));
            $masa_p = ($mp == 0) ? 0 : $mp;
        }else {
            $mp = $tahun_pt - date("Y", strtotime($key->tmt_dosen));
            $masa_p = ($mp == 0) ? 0 : $mp;
        }
        
        // CALCULATE TMT DISPLAY
        if (($key->kategori == 'DTY' && $key->nama_lembaga != "Ma'had Aly Sukorejo") || ($key->kategori == 'DTT' && $key->nama_lembaga != "Ma'had Aly Sukorejo")) {
            $tmt_display = date("Y", strtotime($key->tmt_dosen));
        } elseif (($key->kategori == 'DTY' && $key->nama_lembaga == "Ma'had Aly Sukorejo") || ($key->kategori == 'DTT' && $key->nama_lembaga == "Ma'had Aly Sukorejo")) {
            $tmt_display = date("Y", strtotime($key->tmt_maif));
        } else {
            $tmt_display = date("Y", strtotime($key->tmt_guru));
        }

        // Tunkel
        if ($key->tunj_kel == "Ya" and $mp >= 2){
            $tunkel = $nominaltunkel->besaran_tunkel;
        } else {
            $tunkel = 0;
        }
        if ($key->status_aktif == "Cuti 50%") { $tunkel *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $tunkel = 0; }

        // Tunj Anak
        if ($key->tunj_anak == "Ya" ){
            $tunja_anak = $nominaltunj_anak->nominal_tunj_anak;
        } else {
            $tunja_anak = 0;
        }
        if ($key->status_aktif == "Cuti 50%") { $tunja_anak *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $tunja_anak = 0; }

        // Walkes
        if ($key->walkes == "Ya" ){
            $tunj_walkes = 100000;
        } else if($key->walkes == "walkes_sklh") {
            $tunj_walkes = 75000;
        } else if($key->walkes == "walkes_amsilati") {
            $tunj_walkes = 25000;
        } else {
            $tunj_walkes = 0;
        }
        if ($key->status_aktif == "Cuti 50%") { $tunj_walkes *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $tunj_walkes = 0; }
        
        // Rank / Honor Mengajar
        $rank = 0; // Default
        if ($key->kategori == 'GTY' or $key->kategori == 'GTT'){
            $hitung_rank = $this->db->query("select nominal from barokah_pengajar where min_tmp_mengajar <= $masa_p and max_tmp_mengajar >= $masa_p and ijazah = '$key->ijazah_terakhir' and kategori = 'Guru' ")->result();
            foreach($hitung_rank as $nilai_rank) { $rank = $nilai_rank->nominal; }
        } else {
            if ($key->id_lembaga == '39'){
                    $hitung_rank = $this->db->query("select nominal from barokah_pengajar where min_tmp_mengajar <= $masa_p and max_tmp_mengajar >= $masa_p and ijazah = '$key->ijazah_terakhir' and kategori = 'Dosen MAIF' ")->result();
                foreach($hitung_rank as $nilai_rank) { $rank = $nilai_rank->nominal; }
            } elseif ($key->id_lembaga == '37') {
                $hitung_rank = $this->db->query("select nominal from barokah_pengajar where min_tmp_mengajar <= $masa_p and max_tmp_mengajar >= $masa_p and ijazah = '$key->ijazah_terakhir' and kategori = 'Dosen FIK' ")->result();
                foreach($hitung_rank as $nilai_rank) { $rank = $nilai_rank->nominal; }
            } else {
                $hitung_rank = $this->db->query("select nominal from barokah_pengajar where min_tmp_mengajar <= $masa_p and max_tmp_mengajar >= $masa_p and ijazah = '$key->ijazah_terakhir' and kategori = 'Dosen UNIB' ")->result();
                foreach($hitung_rank as $nilai_rank) { $rank = $nilai_rank->nominal; }
            }
        }
        
        $mengajar = $rank * $key->jumlah_sks;
        if ($key->status_aktif == "Cuti 50%") { $mengajar *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $mengajar = 0; }
        
        $rank_piket = $rank / 4;
        $barokah_piket = $rank_piket * $key->jumlah_hadir_piket;
        
        // Kehormatan
            $kehormatan = 0;
        if ($key->kategori == 'GTY' || $key->kategori == 'GTT'){
            $hitung_kehormatan = $this->db->query("select nominal from barokah_kehormatan_pengajar where min_masa_pengabdian <= $mp and max_masa_pengabdian >= $mp and kategori = 'Guru' ")->result();
            if(!empty($hitung_kehormatan) and $key->kehormatan == 'Ya') {
                foreach($hitung_kehormatan as $nilai_kehormatan) { $kehormatan = $nilai_kehormatan->nominal; }
            }
        } else {
            $hitung_kehormatan = $this->db->query("select nominal from barokah_kehormatan_pengajar where min_masa_pengabdian <= $mp and max_masa_pengabdian >= $mp and kategori = 'Dosen' ")->result();
            if(!empty($hitung_kehormatan) and $key->kehormatan == 'Ya') {
                foreach($hitung_kehormatan as $nilai_kehormatan) { $kehormatan = $nilai_kehormatan->nominal; }
            }
        }
        if ($key->status_aktif == "Cuti 50%") { $kehormatan *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $kehormatan = 0; }

        // DTY
            $dty = 0;
        if ($key->kategori == 'GTY' and $key->status_sertifikasi == 'Belum' and $masa_p > 2 ){
            $hitung_dty = $this->db->query("SELECT nominal from barokah_pengajar_tetap where kategori = 'Guru' ")->result();
            if(!empty($hitung_dty)) { foreach($hitung_dty as $nilai_dty) { $dty = $nilai_dty->nominal; } }
        } else if ($key->kategori == 'DTY' and $key->status_sertifikasi == 'Belum' and $masa_p > 2) {
            $hitung_dty = $this->db->query("SELECT nominal from barokah_pengajar_tetap where kategori = 'Dosen' ")->result();
            if(!empty($hitung_dty)) { foreach($hitung_dty as $nilai_dty) { $dty = $nilai_dty->nominal; } }
        }
        if ($key->status_aktif == "Cuti 50%") { $dty *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $dty = 0; }
        
        // Jafung
        $jafung = 0;
        if ($key->jabatan_akademik != '' and $key->jafung == 'Ya' ) {
            $hitung_jafung = $this->db->query("SELECT nominal from barokah_jafung, umana where umana.jabatan_akademik = barokah_jafung.id_barokah_jafung and umana.jabatan_akademik = $key->jabatan_akademik  ")->result();
            if(!empty($hitung_jafung)) { foreach($hitung_jafung as $nilai_jafung) { $jafung = $nilai_jafung->nominal; } }
        }
        if ($key->status_aktif == "Cuti 50%") { $jafung *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $jafung = 0; }
        
        // Potongan
            $potongan = 0;
            $hitung_potongan = $this->db->query("SELECT SUM(nominal_potongan) as jumlah from potongan_pengajar, pengajar where potongan_pengajar.id_pengajar = pengajar.id_pengajar and potongan_pengajar.id_pengajar = $key->id_pengajar and potongan_pengajar.max_periode_potongan >= CURDATE() ")->result();
        if(!empty($hitung_potongan)) { foreach($hitung_potongan as $jumlah_potongan) { $potongan = $jumlah_potongan->jumlah; } }
        
        // Tambahan / BK
            $tambahan = 0;
        $hitung_tambahan = $this->db->query("SELECT SUM(nominal_tambahan) as jumlah from barokah_tambahan, pengajar where barokah_tambahan.id_pengajar = pengajar.id_pengajar and barokah_tambahan.id_pengajar = $key->id_pengajar and barokah_tambahan.max_periode_tambahan >= CURDATE() ")->result();
        if(!empty($hitung_tambahan)) { foreach($hitung_tambahan as $jumlah_tambahan) { $tambahan = $jumlah_tambahan->jumlah; } }
        
        // CALCULATE TOTALS FOR LIVE DATA
        $diterima = $barokah_piket + $jml_kehadiran + $nominal_hadir_15 + $nominal_hadir_10 + $tunkel + $tunja_anak + $mengajar + $dty + $jafung + $kehormatan + $tunj_walkes + $tambahan - $potongan;
        $jumlah = $barokah_piket + $jml_kehadiran + $nominal_hadir_15 + $nominal_hadir_10 + $tunkel + $tunja_anak + $mengajar + $dty + $jafung + $kehormatan + $tunj_walkes + $tambahan;
        }
        
        // Accumulate Global Totals
        $total_transfer_all += $diterima;
        $total_transport_all += ($jml_kehadiran + $nominal_hadir_15 + $nominal_hadir_10); // Sum of all transport types
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
        $sum_jumlah += $jumlah; // Accumulate Gross Total
        $sum_diterima += $diterima;
    }
}
?>

<div class="container-fluid px-4 py-4">
    
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="mdi mdi-shield-check-outline mr-2 text-primary"></i>Validasi Kehadiran</h4>
            <div class="d-flex align-items-center text-muted">
                <span class="mr-3"><i class="mdi mdi-calendar mr-1"></i> <?php echo (isset($isilist) && !empty($isilist)) ? reset($isilist)->bulan . ' ' . reset($isilist)->tahun : ''; ?></span>
                <span><i class="mdi mdi-domain mr-1"></i> <?php echo (isset($isilist) && !empty($isilist)) ? reset($isilist)->nama_lembaga : ''; ?></span>
            </div>
        </div>
        <div>
            <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm px-3 rounded-pill font-weight-bold">
                <i class="mdi mdi-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <?php
    // Extract Metadata from first row
    $meta_file = "";
    $meta_id_kl = "";
    $meta_id_lembaga = "";
    if (isset($isilist) && !empty($isilist)) {
        $first_row = reset($isilist);
        $meta_file = $first_row->file;
        $meta_id_kl = $first_row->id_kehadiran_lembaga;
        $meta_id_lembaga = $first_row->id_lembaga;
    }
    ?>

    <!-- Summary Actions (Clean) -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body py-3 px-4 d-flex flex-wrap align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="mr-4 border-right pr-4">
                            <small class="text-uppercase text-muted font-weight-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Status</small>
                            <div class="mt-1"><span class="badge <?php echo $badge_status_class; ?> px-2 py-1"><?php echo $status_pengiriman; ?></span></div>
                        </div>
                        
                        <!-- File Absensi -->
                        <?php if(!empty($meta_file)): ?>
                        <a href="<?php echo base_url() ?>upload/<?php echo $meta_file; ?>" target="_blank" class="btn btn-outline-info btn-sm mr-2 rounded-pill">
                            <i class="mdi mdi-file-document-outline mr-1"></i> File Kehadiran
                        </a>
                        <?php endif; ?>

                        <!-- Cetak -->
                         <a href="<?php echo base_url() ?>Laporan_pengajar/cetak/<?php echo $encrypted_id; ?>" target="_blank" class="btn btn-outline-secondary btn-sm mr-2 rounded-pill">
                            <i class="mdi mdi-printer mr-1"></i> Cetak
                        </a>
                        
                        <!-- Potongan -->
                        <a href="<?php echo base_url() ?>Kehadiran/Cetak_Potongan_pengajar/<?php echo $meta_id_lembaga; ?>" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill">
                            <i class="mdi mdi-content-cut mr-1"></i> Potongan
                        </a>
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
                            <th rowspan="2" class="text-center header-group-input" style="min-width: 80px;">TMT</th>
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
                            <th rowspan="2" class="text-center bg-white" style="width: 50px; border-bottom: 2px solid #e2e8f0;">Aksi</th>
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
                        // Loop through LIST directly (calculations already done above)
                        foreach($isilist as $key){
                            // Re-calculate for this row to get the variables in scope
                            // (This is inefficient but maintains original structure)
                            if(isset($is_snapshot) && $is_snapshot) {
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
                                $masa_p = $key->mp;
                                $tmt_display = "-";
                                $diterima = $key->diterima;
                                $jumlah = $key->diterima + $key->potongan;
                            } else {
                                // Recalculate for display (duplicating logic from above)
                                $jml_kehadiran = $key->jumlah_hadir * $key->nominal_transport;
                                $nominal_hadir_15 = $key->jumlah_hadir_15 * 15000;
                                $nominal_hadir_10 = $key->jumlah_hadir_10 * 10000;
                                
                                $tahun_default = (int)date('Y');
                                if (isset($tahun_acuan_map['Default'])) $tahun_default = $tahun_acuan_map['Default'];
                                if (isset($tahun_acuan_map['Pengurus'])) $tahun_default = $tahun_acuan_map['Pengurus'];
                                if (isset($tahun_acuan_map['Kantor Pusat'])) $tahun_default = $tahun_acuan_map['Kantor Pusat'];
                                
                                $tahun_madrasah = isset($tahun_acuan_map['Bidang DIKJAR-M']) ? $tahun_acuan_map['Bidang DIKJAR-M'] : $tahun_default;
                                $tahun_sekolah = isset($tahun_acuan_map['Bidang DIKJAR']) ? $tahun_acuan_map['Bidang DIKJAR'] : $tahun_default;
                                $tahun_pt = isset($tahun_acuan_map['Bidang DIKTI']) ? $tahun_acuan_map['Bidang DIKTI'] : $tahun_default;
                                
                                if (($key->kategori == 'GTY' && $key->id_bidang == "Bidang DIKJAR-M") || ($key->kategori == 'GTT' && $key->id_bidang == "Bidang DIKJAR-M")) {
                                    $mp = $tahun_madrasah - date("Y", strtotime($key->tmt_guru));
                                    $masa_p = ($mp == 0) ? 0 : $mp;
                                } elseif (($key->kategori == 'GTY' && $key->id_bidang == "Bidang DIKJAR") || ($key->kategori == 'GTT' && $key->id_bidang == "Bidang DIKJAR")) {
                                    $mp = $tahun_sekolah - date("Y", strtotime($key->tmt_guru));
                                    $masa_p = ($mp == 0) ? 0 : $mp;
                                } elseif (($key->kategori == 'DTY' && $key->nama_lembaga == "Ma'had Aly Sukorejo") || ($key->kategori == 'DTT' && $key->nama_lembaga == "Ma'had Aly Sukorejo")) {
                                    $mp = $tahun_pt - date("Y", strtotime($key->tmt_maif));
                                    $masa_p = ($mp == 0) ? 0 : $mp;
                                } else {
                                    $mp = $tahun_pt - date("Y", strtotime($key->tmt_dosen));
                                    $masa_p = ($mp == 0) ? 0 : $mp;
                                }
                                
                                if (($key->kategori == 'DTY' && $key->nama_lembaga != "Ma'had Aly Sukorejo") || ($key->kategori == 'DTT' && $key->nama_lembaga != "Ma'had Aly Sukorejo")) {
                                    $tmt_display = date("Y", strtotime($key->tmt_dosen));
                                } elseif (($key->kategori == 'DTY' && $key->nama_lembaga == "Ma'had Aly Sukorejo") || ($key->kategori == 'DTT' && $key->nama_lembaga == "Ma'had Aly Sukorejo")) {
                                    $tmt_display = date("Y", strtotime($key->tmt_maif));
                                } else {
                                    $tmt_display = date("Y", strtotime($key->tmt_guru));
                                }
                                
                                if ($key->tunj_kel == "Ya" and $mp >= 2) {
                                    $tunkel = $nominaltunkel->besaran_tunkel;
                                } else {
                                    $tunkel = 0;
                                }
                                if ($key->status_aktif == "Cuti 50%") { $tunkel *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $tunkel = 0; }
                                
                                if ($key->tunj_anak == "Ya") {
                                    $tunja_anak = $nominaltunj_anak->nominal_tunj_anak;
                                } else {
                                    $tunja_anak = 0;
                                }
                                if ($key->status_aktif == "Cuti 50%") { $tunja_anak *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $tunja_anak = 0; }
                                
                                if ($key->walkes == "Ya") {
                                    $tunj_walkes = 75000;
                                } else if($key->walkes == "walkes_sklh") {
                                    $tunj_walkes = 50000;
                                } else if($key->walkes == "walkes_amsilati") {
                                    $tunj_walkes = 25000;
                                } else {
                                    $tunj_walkes = 0;
                                }
                                if ($key->status_aktif == "Cuti 50%") { $tunj_walkes *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $tunj_walkes = 0; }
                                
                                $rank = 0;
                                if ($key->kategori == 'GTY' or $key->kategori == 'GTT') {
                                    $hitung_rank = $this->db->query("select nominal from barokah_pengajar where min_tmp_mengajar <= $masa_p and max_tmp_mengajar >= $masa_p and ijazah = '$key->ijazah_terakhir' and kategori = 'Guru' ")->result();
                                    foreach($hitung_rank as $nilai_rank) { $rank = $nilai_rank->nominal; }
                                } else {
                                    if ($key->id_lembaga == '39') {
                                        $hitung_rank = $this->db->query("select nominal from barokah_pengajar where min_tmp_mengajar <= $masa_p and max_tmp_mengajar >= $masa_p and ijazah = '$key->ijazah_terakhir' and kategori = 'Dosen MAIF' ")->result();
                                        foreach($hitung_rank as $nilai_rank) { $rank = $nilai_rank->nominal; }
                                    } elseif ($key->id_lembaga == '37') {
                                        $hitung_rank = $this->db->query("select nominal from barokah_pengajar where min_tmp_mengajar <= $masa_p and max_tmp_mengajar >= $masa_p and ijazah = '$key->ijazah_terakhir' and kategori = 'Dosen FIK' ")->result();
                                        foreach($hitung_rank as $nilai_rank) { $rank = $nilai_rank->nominal; }
                                    } else {
                                        $hitung_rank = $this->db->query("select nominal from barokah_pengajar where min_tmp_mengajar <= $masa_p and max_tmp_mengajar >= $masa_p and ijazah = '$key->ijazah_terakhir' and kategori = 'Dosen UNIB' ")->result();
                                        foreach($hitung_rank as $nilai_rank) { $rank = $nilai_rank->nominal; }
                                    }
                                }
                                
                                $mengajar = $rank * $key->jumlah_sks;
                                if ($key->status_aktif == "Cuti 50%") { $mengajar *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $mengajar = 0; }
                                
                                $rank_piket = $rank / 4;
                                $barokah_piket = $rank_piket * $key->jumlah_hadir_piket;
                                
                                $kehormatan = 0;
                                if ($key->kategori == 'GTY' || $key->kategori == 'GTT') {
                                    $hitung_kehormatan = $this->db->query("select nominal from barokah_kehormatan_pengajar where min_masa_pengabdian <= $mp and max_masa_pengabdian >= $mp and kategori = 'Guru' ")->result();
                                    if(!empty($hitung_kehormatan) and $key->kehormatan == 'Ya') {
                                        foreach($hitung_kehormatan as $nilai_kehormatan) { $kehormatan = $nilai_kehormatan->nominal; }
                                    }
                                } else {
                                    $hitung_kehormatan = $this->db->query("select nominal from barokah_kehormatan_pengajar where min_masa_pengabdian <= $mp and max_masa_pengabdian >= $mp and kategori = 'Dosen' ")->result();
                                    if(!empty($hitung_kehormatan) and $key->kehormatan == 'Ya') {
                                        foreach($hitung_kehormatan as $nilai_kehormatan) { $kehormatan = $nilai_kehormatan->nominal; }
                                    }
                                }
                                if ($key->status_aktif == "Cuti 50%") { $kehormatan *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $kehormatan = 0; }
                                
                                $dty = 0;
                                if ($key->kategori == 'GTY' and $key->status_sertifikasi == 'Belum' and $masa_p > 2) {
                                    $hitung_dty = $this->db->query("SELECT nominal from barokah_pengajar_tetap where kategori = 'Guru' ")->result();
                                    if(!empty($hitung_dty)) { foreach($hitung_dty as $nilai_dty) { $dty = $nilai_dty->nominal; } }
                                } else if ($key->kategori == 'DTY' and $key->status_sertifikasi == 'Belum' and $masa_p > 2) {
                                    $hitung_dty = $this->db->query("SELECT nominal from barokah_pengajar_tetap where kategori = 'Dosen' ")->result();
                                    if(!empty($hitung_dty)) { foreach($hitung_dty as $nilai_dty) { $dty = $nilai_dty->nominal; } }
                                }
                                if ($key->status_aktif == "Cuti 50%") { $dty *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $dty = 0; }
                                
                                $jafung = 0;
                                if ($key->jabatan_akademik != '' and $key->jafung == 'Ya') {
                                    $hitung_jafung = $this->db->query("SELECT nominal from barokah_jafung, umana where umana.jabatan_akademik = barokah_jafung.id_barokah_jafung and umana.jabatan_akademik = $key->jabatan_akademik ")->result();
                                    if(!empty($hitung_jafung)) { foreach($hitung_jafung as $nilai_jafung) { $jafung = $nilai_jafung->nominal; } }
                                }
                                if ($key->status_aktif == "Cuti 50%") { $jafung *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $jafung = 0; }
                                
                                $potongan = 0;
                                $hitung_potongan = $this->db->query("SELECT SUM(nominal_potongan) as jumlah from potongan_pengajar, pengajar where potongan_pengajar.id_pengajar = pengajar.id_pengajar and potongan_pengajar.id_pengajar = $key->id_pengajar and potongan_pengajar.max_periode_potongan >= CURDATE() ")->result();
                                if(!empty($hitung_potongan)) { foreach($hitung_potongan as $jumlah_potongan) { $potongan = $jumlah_potongan->jumlah; } }
                                
                                $tambahan = 0;
                                $hitung_tambahan = $this->db->query("SELECT SUM(nominal_tambahan) as jumlah from barokah_tambahan, pengajar where barokah_tambahan.id_pengajar = pengajar.id_pengajar and barokah_tambahan.id_pengajar = $key->id_pengajar and barokah_tambahan.max_periode_tambahan >= CURDATE() ")->result();
                                if(!empty($hitung_tambahan)) { foreach($hitung_tambahan as $jumlah_tambahan) { $tambahan = $jumlah_tambahan->jumlah; } }
                                
                                $diterima = $barokah_piket + $jml_kehadiran + $nominal_hadir_15 + $nominal_hadir_10 + $tunkel + $tunja_anak + $mengajar + $dty + $jafung + $kehormatan + $tunj_walkes + $tambahan - $potongan;
                                $jumlah = $barokah_piket + $jml_kehadiran + $nominal_hadir_15 + $nominal_hadir_10 + $tunkel + $tunja_anak + $mengajar + $dty + $jafung + $kehormatan + $tunj_walkes + $tambahan;
                            }
                        ?>
                        <!-- Row Data -->
                        <tr id="row-<?php echo $key->id_kehadiran_pengajar; ?>">
                            <td class="cell-center text-muted sticky-col-first bg-white border-right"><?php echo $no++; ?></td>
                            <td class="sticky-col-second bg-white border-right">
                                <div class="font-weight-bold text-dark"><?php echo htmlentities((isset($key->gelar_depan) ? $key->gelar_depan : '').' '.$key->nama_lengkap.' '.(isset($key->gelar_belakang) ? $key->gelar_belakang : '')); ?></div>
                                <span class="badge badge-light border mt-1" style="font-size: 0.7rem;"><?php echo htmlentities($key->kategori); ?></span>
                                <?php if($key->status_aktif == "Cuti 50%" or $key->status_aktif == "Cuti 100%"){ ?>
                                    <span class="badge badge-danger ml-1"><?php echo htmlentities($key->status_aktif); ?></span>
                                <?php } ?>
                                <!-- INPUT HIDDEN HAPUS JIKA TIDAK PERLU, TAPI BIARKAN UNTUK FORM SUBMIT BULK -->
                                <input type="hidden" name="id_pengajar[]" value="<?php echo $key->id_pengajar; ?>">
                                <input type="hidden" name="id_kehadiran_pengajar[]" value="<?php echo $key->id_kehadiran_pengajar; ?>">
                            </td>
                            
                            <!-- Requested Columns: IT, TMT, MP, Jam/SKS, Rank, Mengajar, DTY, Jafung -->
                            <td class="cell-center border-left align-middle"><?php echo htmlentities($key->ijazah_terakhir); ?></td>
                            <td class="cell-center align-middle"><?php echo $tmt_display; ?></td>
                            <td class="cell-center align-middle"><?php echo $masa_p; ?> Thn</td>
                            <td class="cell-center align-middle font-weight-bold"><?php echo htmlentities($key->jumlah_sks); ?></td>
                            <td class="cell-right text-muted align-middle"><?php echo rupiah($rank); ?></td>
                            <td class="cell-right font-weight-bold text-dark align-middle"><?php echo rupiah($mengajar); ?></td>
                            <td class="cell-right align-middle"><?php echo rupiah($dty); ?></td>
                            <td class="cell-right align-middle"><?php echo rupiah($jafung); ?></td>
                            
                            <!-- Kehadiran Breakdown -->
                            <!-- Normal -->
                            <td class="border-left col-input"><input type="number" min="0" name="jumlah_hadir[]" value="<?php echo $key->jumlah_hadir; ?>" class="table-input js-input-hadir" readonly <?php echo $is_readonly ? 'disabled' : ''; ?>></td>
                            <td class="cell-right text-muted align-middle js-nominal-hadir"><?php echo rupiah($jml_kehadiran); ?></td>
                            
                            <!-- 15rb -->
                            <td class="col-input"><input type="number" min="0" name="jumlah_hadir_15[]" value="<?php echo $key->jumlah_hadir_15; ?>" class="table-input text-muted js-input-15" readonly <?php echo $is_readonly ? 'disabled' : ''; ?>></td>
                            <td class="cell-right text-muted align-middle js-nominal-15"><?php echo rupiah($nominal_hadir_15); ?></td>
                            
                            <!-- 10rb -->
                            <td class="col-input"><input type="number" min="0" name="jumlah_hadir_10[]" value="<?php echo $key->jumlah_hadir_10; ?>" class="table-input js-input-10" readonly <?php echo $is_readonly ? 'disabled' : ''; ?>></td>
                            <td class="cell-right text-muted align-middle js-nominal-10"><?php echo rupiah($nominal_hadir_10); ?></td>
                            
                             <!-- Piket -->
                            <td class="col-input"><input type="number" min="0" name="jumlah_hadir_piket[]" value="<?php echo $key->jumlah_hadir_piket; ?>" class="table-input js-input-piket" readonly <?php echo $is_readonly ? 'disabled' : ''; ?>></td>
                            <td class="cell-right text-xs text-muted align-middle"><?php echo rupiah($rank_piket); ?></td>
                            <td class="cell-right text-muted align-middle js-nominal-piket"><?php echo rupiah($barokah_piket); ?></td>
                            
                            <!-- Tunjangan breakdown -->
                            <td class="cell-right border-left align-middle"><?php echo rupiah($tunkel); ?></td>
                            <td class="cell-right align-middle"><?php echo rupiah($tunja_anak); ?></td>
                            <td class="cell-right align-middle"><?php echo rupiah($kehormatan); ?></td>
                            <td class="cell-right align-middle"><?php echo rupiah($tunj_walkes); ?></td>
                            <td class="cell-right align-middle"><?php echo rupiah($tambahan); ?></td>
                            
                            <!-- Potongan & Result -->
                            <td class="cell-right border-left align-middle text-muted js-jumlah"><?php echo rupiah($jumlah); ?></td>
                            <td class="cell-right border-left text-danger align-middle"><?php echo rupiah($potongan); ?></td>
                            <td class="cell-right border-left col-result align-middle js-diterima"><?php echo rupiah($diterima); ?></td>
                            <td class="cell-center align-middle">
                                <?php if(!$is_readonly): ?>
                                <button class="btn btn-outline-primary btn-sm btn-edit" 
                                    data-id="<?php echo $key->id_kehadiran_pengajar; ?>"
                                    data-hadir="<?php echo $key->jumlah_hadir; ?>"
                                    data-hadir15="<?php echo $key->jumlah_hadir_15; ?>"
                                    data-hadir10="<?php echo $key->jumlah_hadir_10; ?>"
                                    data-piket="<?php echo $key->jumlah_hadir_piket; ?>"
                                    data-nama="<?php echo htmlentities($key->nama_lengkap); ?>"
                                ><i class="mdi mdi-pencil"></i></button>
                                <?php else: ?>
                                    <span class="text-muted"><i class="mdi mdi-lock"></i></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <!-- TOTAL ROW -->
                        <tr>
                            <!-- Sticky Cols (No, Nama) -->
                            <td class="sticky-col-first bg-light border-right text-center font-weight-bold"></td>
                            <td class="sticky-col-second bg-light border-right font-weight-bold text-uppercase text-right px-3">Total Seluruh:</td>
                            
                            <!-- Inputs Group (IT, TMT, MP, Jam) - Empty/Merged -->
                            <td colspan="4" class="bg-light border-right border-left"></td>
                            
                            <!-- Rank (No sum displayed usually, or average? Left blank in old view? Old view merged colspans. Let's assume blank or sum if feasible. Old view code: td colspan="8" then sums. 
                            Let's map correctly:
                            Old view: colspan="8" (No, Nama, IT, TMT, MP, Jm, Rank, Mengajar, DTY, Jafung, etc were specific cols).
                            Wait, old view footer starts after colspan="8".
                            Lines 485: <td colspan="8"></td>
                            Line 486: Mengajar
                            Line 487: DTY
                            Line 488: Jafung
                            
                            My Structure cols:
                            1. No
                            2. Nama
                            3. IT
                            4. TMT
                            5. MP
                            6. Jam
                            7. Rank
                            8. Mengajar
                            9. DTY
                            10. Jafung
                            
                            So "colspan=7" covers up to Rank.
                            Then Mengajar is next.
                            -->
                            <td class="text-right border-left bg-light font-weight-bold"></td>
                            <td class="text-right bg-light font-weight-bold"><?php echo rupiah($sum_mengajar); ?></td>
                            <td class="text-right bg-light font-weight-bold"><?php echo rupiah($sum_dty); ?></td>
                            <td class="text-right bg-light font-weight-bold"><?php echo rupiah($sum_jafung); ?></td>

                            <!-- Kehadiran Breakdown -->
                            <!-- Normal (2 cols: Input, Nominal) -->
                            <td class="bg-light border-left"></td>
                            <td class="text-right bg-light font-weight-bold"><?php echo rupiah($sum_jml_kehadiran); ?></td>
                            
                            <!-- 15rb (2 cols) -->
                            <td class="bg-light"></td>
                            <td class="text-right bg-light font-weight-bold"><?php echo rupiah($sum_nominal_hadir_15); ?></td>
                            
                            <!-- 10rb (2 cols) -->
                            <td class="bg-light"></td>
                            <td class="text-right bg-light font-weight-bold"><?php echo rupiah($sum_nominal_hadir_10); ?></td>
                            
                            <!-- Piket (3 cols: Input, Rate, Nominal) -->
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
                            <td class="bg-light"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    
    <div class="content-spacer"></div>

    <!-- Sticky Actions -->
    <div class="sticky-action-bar">
        <div class="d-flex align-items-center justify-content-end w-100">
             <?php 
                // Determine status safely
                $current_status = (!empty($isilist)) ? $isilist[0]->status : '';
                
                // Button Logic
                if(!$is_readonly && $current_status == "Sudah" and $this->session->userdata('jabatan') != 'SDM'){
            ?>
            <button type="button" id="btnKirim" onclick="kirim()" class="btn btn-primary px-4 shadow-sm font-weight-bold" ><span class="btn-icon-start text-white"><i class="mdi mdi-send"></i>
            </span>Kirim Validasi</button> 
            <?php
                } elseif ($this->session->userdata('jabatan') == 'SDM') {
                    // SDM specific buttons if any
                } elseif(!$is_readonly && $current_status == "Terkirim" and $this->session->userdata('jabatan') != 'AdminLembaga' ) {
            ?>
            <button type="button" id="btnSetujui" onclick="save()" class="btn btn-primary px-4 shadow-sm font-weight-bold" ><span class="btn-icon-start text-white"><i class="fa fa-check"></i>
            </span>Setujui</button>
            <?php
                }
            ?>
        </div>
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
        // Init DataTables (Simple functionality for mockup)
        $('#tblValidasi').DataTable({
            paging: false,
            searching: true,
            info: false,
            scrollY: '60vh',
            scrollCollapse: true,
            scrollX: true,
            ordering: false
        });

        // Edit Modal Logic
        $('.btn-edit').click(function(){
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            const hadir = $(this).data('hadir');
            const hadir15 = $(this).data('hadir15');
            const hadir10 = $(this).data('hadir10');
            const piket = $(this).data('piket');

            $('#edit-nama').text(nama);
            $('#edit-id').val(id);
            $('#edit-hadir').val(hadir);
            $('#edit-hadir15').val(hadir15);
            $('#edit-hadir10').val(hadir10);
            $('#edit-piket').val(piket);

            $('#modalEdit').modal('show');
        });

        $('#btnSimpanEdit').click(function(){
            const id = $('#edit-id').val();
            // Parse inputs as integers to remove leading zeros
            const hadir = parseInt($('#edit-hadir').val()) || 0;
            const hadir15 = parseInt($('#edit-hadir15').val()) || 0;
            const hadir10 = parseInt($('#edit-hadir10').val()) || 0;
            const piket = parseInt($('#edit-piket').val()) || 0;

            const btn = $(this);
            // Disable button
            btn.prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: "<?php echo site_url('Validasi_pengajar/update_row'); ?>",
                type: "POST",
                dataType: "json",
                data: {
                    id_kehadiran_pengajar: id,
                    jumlah_hadir: hadir,
                    jumlah_hadir_15: hadir15,
                    jumlah_hadir_10: hadir10,
                    jumlah_hadir_piket: piket
                },
                success: function(res){
                    if(res.status){
                        // Update Table Cells
                        const row = $('#row-' + id);
                        
                        // Update Inputs
                        row.find('.js-input-hadir').val(hadir);
                        row.find('.js-input-15').val(hadir15);
                        row.find('.js-input-10').val(hadir10);
                        row.find('.js-input-piket').val(piket);

                        // Update Calculated Strings
                        row.find('.js-nominal-hadir').text(res.data.jml_kehadiran);
                        row.find('.js-nominal-15').text(res.data.nominal_hadir_15);
                        row.find('.js-nominal-10').text(res.data.nominal_hadir_10);
                        row.find('.js-nominal-piket').text(res.data.barokah_piket);
                        row.find('.js-jumlah').text(res.data.jumlah);
                        row.find('.js-diterima').text(res.data.diterima);
                        
                        // Update Button Data
                        const btnEdit = row.find('.btn-edit');
                        btnEdit.data('hadir', hadir);
                        btnEdit.data('hadir15', hadir15);
                        btnEdit.data('hadir10', hadir10);
                        btnEdit.data('piket', piket);

                        $('#modalEdit').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Data berhasil diperbarui!',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: function(xhr, status, error){
                    console.error(xhr.responseText);
                    let msg = 'Terjadi kesalahan koneksi db.';
                    if(xhr.responseText) msg += '\n\n' + xhr.responseText.substring(0, 100);
                    Swal.fire('Error', msg, 'error');
                },
                complete: function() {
                    // Re-enable button
                    btn.prop('disabled', false).text('Simpan Perubahan');
                }
            });
        });
    });

    function save() {
        Swal.fire({
            title: 'Setujui Validasi?',
            text: "Data akan dikunci (Snapshot). Perubahan master data setelah ini tidak akan mempengaruhi nominal.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Setujui!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Menyimpan Snapshot...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: "<?php echo site_url('Validasi_pengajar/approve'); ?>",
                    type: "POST",
                    dataType: "json",
                    data: {
                        id_kehadiran_lembaga: '<?php echo $id_kehadiran_lembaga; ?>'
                    },
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                showConfirmButton: true
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        Swal.fire('Error', 'Terjadi kesalahan koneksi.', 'error');
                    }
                });
            }
        });
    }

    function kirim() {
        Swal.fire({
            title: 'Kirim Validasi?',
            text: "Pastikan semua data sudah benar. Data yang dikirim tidak dapat diubah lagi oleh operator lembaga.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Kirim!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Mengirim...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: "<?php echo site_url('Validasi_pengajar/kirim_validasi'); ?>",
                    type: "POST",
                    dataType: "json",
                    data: {
                        id_kehadiran_lembaga: '<?php echo $id_kehadiran_lembaga; ?>'
                    },
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                showConfirmButton: true
                            }).then(() => {
                                window.location.href = "<?php echo site_url('Kehadiran/pengajar'); ?>";
                            });
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        Swal.fire('Error', 'Terjadi kesalahan koneksi.', 'error');
                    }
                });
            }
        });
    }
</script>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Koreksi: <span id="edit-nama" class="text-primary"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-id">
                <div class="form-group">
                    <label>Hadir Normal</label>
                    <input type="number" class="form-control" id="edit-hadir" min="0">
                </div>
                <div class="form-group">
                    <label>Hadir 15.000</label>
                    <input type="number" class="form-control" id="edit-hadir15" min="0">
                </div>
                <div class="form-group">
                    <label>Hadir 10.000</label>
                    <input type="number" class="form-control" id="edit-hadir10" min="0">
                </div>
                <div class="form-group">
                    <label>Piket</label>
                    <input type="number" class="form-control" id="edit-piket" min="0">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanEdit">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>
