<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Satpam Fullscreen</title>
  <link rel="icon" href="<?php echo base_url();?>assets/p2s2_fav.png" type="image/x-icon">
  <link rel="shortcut icon" href="<?php echo base_url();?>assets/p2s2_fav.png" type="image/x-icon">

  <!-- Bootstrap & helpers -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <style>
    body { background:#f5f5f5; font-size:14px; }
    .table thead th { vertical-align:middle!important; text-align:center; }
    .badge-total { font-size:.9rem; margin-right:.5rem }
    .sticky-actions { position:sticky; bottom:0; background:#fff; z-index:10; border-top:1px solid #e9ecef; padding:.75rem }
    .cell-right { text-align:right }
    .cell-center { text-align:center }
    .font-weight-bold { font-weight: bold; }
  </style>
</head>
<body>

<?php
// Calculate totals for badges
$sum_transport = 0;
$sum_barokah   = 0;
$sum_dini      = 0;
$sum_danru     = 0;
$sum_grand     = 0;

if(!empty($isilist)) {
    foreach($isilist as $row) {
        $sum_transport += $row->jumlah_transport;
        $sum_barokah   += $row->jumlah_barokah;
        $sum_dini      += $row->jumlah_konsumsi;
        $sum_danru     += $row->nominal_danru;
        $sum_grand     += $row->diterima;
    }
}
?>

<div class="container-fluid my-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Laporan Barokah Satpam Bulan <?php echo $header->bulan . ' ' . $header->tahun; ?></h4>
    <div>
        <!-- Link Cetak -->
      <a class="btn btn-warning" target="_blank" href="<?php echo base_url('Laporan_satpam/cetak/'.$id_enkripsi); ?>"><i class="fa fa-print mr-1"></i> Cetak</a>
      <!-- Link Kembali -->
      <a class="btn btn-secondary" href="<?php echo base_url('Laporan_satpam'); ?>"><i class="fa fa-arrow-left mr-1"></i> Kembali</a>
    </div>
  </div>

  <!-- Ringkasan di atas -->
  <div class="mb-3">
    <span class="badge badge-secondary badge-total">Total Transport: <span><?php echo number_format($sum_transport,0,',','.'); ?></span></span>
    <span class="badge badge-secondary badge-total">Total Barokah: <span><?php echo number_format($sum_barokah,0,',','.'); ?></span></span>
    <span class="badge badge-secondary badge-total">Total Dinihari: <span><?php echo number_format($sum_dini,0,',','.'); ?></span></span>
    <span class="badge badge-info badge-total text-white">Total Danru: <span><?php echo number_format($sum_danru,0,',','.'); ?></span></span>
    <span class="badge badge-primary badge-total">Grand Total: <span><?php echo number_format($sum_grand,0,',','.'); ?></span></span>
  </div>

  <div class="table-responsive bg-white p-3 shadow-sm rounded">
      <table id="tabel_laporan" class="table table-bordered table-striped" style="width:100%">
        <thead class="thead-light">
          <tr>
            <th rowspan="2" style="width:50px">No</th>
            <th rowspan="2">Nama</th>

            <th colspan="3">Kehadiran 1</th>
            <th colspan="3">Kehadiran 2</th>
            <th colspan="3">Kehadiran 3</th>
            <th rowspan="2" style="width:100px">Danru</th>

            <th rowspan="2">Total</th>
          </tr>
          <tr>
            <th>Hari</th><th>Trans</th><th>Jumlah</th>
            <th>Shift</th><th>Rank</th><th>Jumlah</th>
            <th>Dini</th><th>Kons</th><th>Jumlah</th>
          </tr>
        </thead>

        <tbody>
        <?php
          $no = 1;
          if(!empty($isilist)):
          foreach ($isilist as $d):
        ?>
          <tr>
            <td class="cell-center"><?php echo $no++; ?></td>
            <td><?php echo htmlentities($d->nama_lengkap); ?></td>

            <!-- Hari -->
            <td class="cell-center"><?php echo $d->jumlah_hari; ?></td>
            <td class="cell-right"><?php echo number_format($d->nominal_transport,0,',','.'); ?></td>
            <td class="cell-right"><?php echo number_format($d->jumlah_transport,0,',','.'); ?></td>

            <!-- Shift -->
            <td class="cell-center"><?php echo $d->jumlah_shift; ?></td>
            <td class="cell-right"><?php echo number_format($d->rank,0,',','.'); ?></td>
            <td class="cell-right"><?php echo number_format($d->jumlah_barokah,0,',','.'); ?></td>

            <!-- Dinihari -->
            <td class="cell-center"><?php echo $d->jumlah_dinihari; ?></td>
            <td class="cell-right"><?php echo number_format($d->konsumsi,0,',','.'); ?></td>
            <td class="cell-right"><?php echo number_format($d->jumlah_konsumsi,0,',','.'); ?></td>

            <!-- Danru -->
            <td class="cell-right"><?php echo number_format($d->nominal_danru,0,',','.'); ?></td>

            <!-- Total baris -->
            <td class="cell-right font-weight-bold" style="background-color: #e2e6ea;"><?php echo number_format($d->diterima,0,',','.'); ?></td>
          </tr>
        <?php endforeach; else: ?>
            <tr><td colspan="13" class="text-center">Data tidak ditemukan</td></tr>
        <?php endif; ?>
        </tbody>

        <tfoot>
          <tr style="background-color: #f8f9fa;">
            <th colspan="4" class="text-right">Total Kehadiran</th>
            <th class="cell-right"><?php echo number_format($sum_transport, 0, ',', '.'); ?></th>
            <th colspan="2" class="text-right">Total Barokah</th>
            <th class="cell-right"><?php echo number_format($sum_barokah, 0, ',', '.'); ?></th>
            <th colspan="2" class="text-right">Total Dinihari</th>
            <th class="cell-right"><?php echo number_format($sum_dini, 0, ',', '.'); ?></th>
            <th class="cell-right"><?php echo number_format($sum_danru, 0, ',', '.'); ?></th>
            <th class="cell-right font-weight-bold" style="background-color: #e2e6ea;"><?php echo number_format($sum_grand, 0, ',', '.'); ?></th>
          </tr>
        </tfoot>
      </table>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
