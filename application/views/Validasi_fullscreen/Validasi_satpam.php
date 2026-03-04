<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Validasi Penggajian Satpam</title>
  <link rel="icon" href="<?php echo base_url();?>assets/p2s2_fav.png" type="image/x-icon">
  <link rel="shortcut icon" href="<?php echo base_url();?>assets/p2s2_fav.png" type="image/x-icon">

  <!-- Bootstrap & helpers -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <!-- DataTables (opsional hanya untuk sticky header & print) -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body { background:#f5f5f5; font-size:14px; }
    .table thead th { vertical-align:middle!important; text-align:center; }
    .badge-total { font-size:.9rem; margin-right:.5rem }
    .sticky-actions { position:sticky; bottom:0; background:#fff; z-index:10; border-top:1px solid #e9ecef; padding:.75rem }
    tr.row-anomali { background:#fff3cd!important; } /* highlight opsional */
    .cell-right { text-align:right }
    .cell-center { text-align:center }
  </style>
</head>
<body>

<?php
// --- PRE-CALCULATION FOR HEADER BADGES ---
$sum_transport = 0;
$sum_barokah   = 0;
$sum_dini      = 0;
$sum_danru     = 0;
$sum_grand     = 0;

// Helper / Config
$honorDanru = $honorDanru ?? 100000;
$rank       = 17500;
$konsumsi   = 5000;

if(!empty($isilist)) {
    foreach($isilist as $rowC) {
        $nTrans = (int)$rowC->nominal_transport;
        
        $jTrans = (int)$rowC->jumlah_hari * $nTrans;
        $jBar   = (int)$rowC->jumlah_shift * $rank;
        $jDini  = (int)$rowC->jumlah_dinihari * $konsumsi;
        
        // Danru
        $isDanruC = (isset($rowC->is_danru) && $rowC->is_danru == '1');
        $nDanruC  = $isDanruC ? $honorDanru : 0;
        
        $diterimaC = $jTrans + $jBar + $jDini + $nDanruC;

        $sum_transport += $jTrans;
        $sum_barokah   += $jBar;
        $sum_dini      += $jDini;
        $sum_danru     += $nDanruC;
        $sum_grand     += $diterimaC;
    }
}
?>

<div class="container-fluid my-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Validasi Penggajian Satpam Bulan <?= $bulan->bulan ?> <?= $bulan->tahun ?></h4>
    <div>
      <a class="btn btn-warning" target="_blank" href="<?= site_url('Validasi_satpam/cetak/'.$id) ?>"><i class="fa fa-print mr-1"></i> Cetak</a>
      <a class="btn btn-secondary" href="javascript:history.back()"><i class="fa fa-arrow-left mr-1"></i> Kembali</a>
    </div>
  </div>

  <!-- Ringkasan di atas -->
  <div class="mb-3">
    <span class="badge badge-secondary badge-total">Total Transport: <span id="sumTransport"><?= number_format($sum_transport,0,',','.') ?></span></span>
    <span class="badge badge-secondary badge-total">Total Barokah: <span id="sumBarokah"><?= number_format($sum_barokah,0,',','.') ?></span></span>
    <span class="badge badge-secondary badge-total">Total Dinihari: <span id="sumDini"><?= number_format($sum_dini,0,',','.') ?></span></span>
    <span class="badge badge-secondary badge-total text-white bg-info">Total Danru: <span id="sumDanru"><?= number_format($sum_danru,0,',','.') ?></span></span>
    <span class="badge badge-primary badge-total">Grand Total: <span id="sumGrand"><?= number_format($sum_grand,0,',','.') ?></span></span>
  </div>

  <div class="table-responsive">
    <form id="form">
      <table id="tabel_validasi" class="table table-bordered table-striped" style="width:100%">
        <thead class="thead-light">
          <tr>
            <th rowspan="2" style="width:50px">No</th>
            <th rowspan="2">Nama</th>

            <th colspan="3">Kehadiran 1</th>
            <th colspan="3">Kehadiran 2</th>
            <th colspan="3">Kehadiran 3</th>
            <th rowspan="2" style="width:100px">Danru</th>

            <th rowspan="2">Total</th>
            <th rowspan="2" style="width:70px">Aksi</th>
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
          
          // Helper: ambil nominal Danru (idealny dari controller/db, sementara hardcode 100k ssuai master)
          // Di controller Validasi_satpam.php: $honorDanru = 100000;
          // Kita anggap default 100000 jika is_danru=1
          $honorDanru = $honorDanru ?? 100000; // Fallback jika controller belum kirim 

          // Nilai agregat untuk header di atas (kalau belum disiapkan di controller)
          $total_transport = $total_transport ?? 0;
          $total_jumlah_barokah = $total_jumlah_barokah ?? 0;
          $total_jumlah_konsumsi_dinihari = $total_jumlah_konsumsi_dinihari ?? 0;
          $total_danru = 0;
          $total_diterima = $total_diterima ?? 0;

          foreach ($isilist as $d):
            // Tarif (jika belum dihitung di controller)
            $nominal_transport = (int)$d->nominal_transport;
            $rank = 17500;      // TODO: ambil otomatis bila sudah ada tabel ketentuan
            $konsumsi = 5000;   // TODO: ambil otomatis bila sudah ada tabel ketentuan

            $jumlah_transport = ((int)$d->jumlah_hari) * $nominal_transport;
            $jumlah_barokah   = ((int)$d->jumlah_shift) * $rank;
            $jumlah_konsumsi  = ((int)$d->jumlah_dinihari) * $konsumsi;
            
            // Hitung Danru
            $isDanru = (isset($d->is_danru) && $d->is_danru == '1');
            $nominalDanruRow = $isDanru ? $honorDanru : 0;

            $diterima         = $jumlah_transport + $jumlah_barokah + $jumlah_konsumsi + $nominalDanruRow;

            // Agregat (kalau belum dihitung di controller)
            $total_transport += $jumlah_transport;
            $total_jumlah_barokah += $jumlah_barokah;
            $total_jumlah_konsumsi_dinihari += $jumlah_konsumsi;
            $total_danru += $nominalDanruRow;
            $total_diterima += $diterima;
        ?>
          <tr id="row-<?= $d->id_kehadiran_satpam ?>">
            <!-- hidden untuk submit massal (tetap dipertahankan) -->
            <input type="hidden" name="bulan[]" value="<?= $d->bulan ?>">
            <input type="hidden" name="tahun[]" value="<?= $d->tahun ?>">
            <input type="hidden" name="id_satpam[]" value="<?= $d->id_satpam ?>">
            <input type="hidden" name="id_kehadiran_lembaga" value="<?= $d->id_kehadiran_lembaga ?>">
            <input type="hidden" name="id_kehadiran_satpam[]" value="<?= $d->id_kehadiran_satpam ?>">

            <td class="cell-center"><?= $no++ ?></td>
            <td><?= htmlentities(trim(($d->gelar_depan ?? '').' '.$d->nama_lengkap.' '.($d->gelar_belakang ?? ''))) ?></td>

            <!-- Hari -->
            <td id="hari-<?= $d->id_kehadiran_satpam ?>" class="cell-center"><?= (int)$d->jumlah_hari ?></td>
            <td class="cell-right"><?= number_format($nominal_transport,0,',','.') ?></td>
            <td id="jmltrans-<?= $d->id_kehadiran_satpam ?>" class="cell-right"><?= number_format($jumlah_transport,0,',','.') ?></td>

            <!-- Shift -->
            <td id="shift-<?= $d->id_kehadiran_satpam ?>" class="cell-center"><?= (int)$d->jumlah_shift ?></td>
            <td class="cell-right"><?= number_format($rank,0,',','.') ?></td>
            <td id="jmlbar-<?= $d->id_kehadiran_satpam ?>" class="cell-right"><?= number_format($jumlah_barokah,0,',','.') ?></td>

            <!-- Dinihari -->
            <td id="dini-<?= $d->id_kehadiran_satpam ?>" class="cell-center"><?= (int)$d->jumlah_dinihari ?></td>
            <td class="cell-right"><?= number_format($konsumsi,0,',','.') ?></td>
            <td id="jmlkons-<?= $d->id_kehadiran_satpam ?>" class="cell-right"><?= number_format($jumlah_konsumsi,0,',','.') ?></td>

            <!-- Danru -->
            <td class="cell-right"><?= number_format($nominalDanruRow,0,',','.') ?></td>

            <!-- Total baris -->
            <td id="subtotal-<?= $d->id_kehadiran_satpam ?>" class="cell-right font-weight-bold"><?= number_format($diterima,0,',','.') ?></td>

            <!-- Aksi -->
            <td class="cell-center">
              <button type="button"
                      class="btn btn-sm btn-outline-primary btn-edit"
                      data-id="<?= $d->id_kehadiran_satpam ?>"
                      data-lembaga="<?= $d->id_kehadiran_lembaga ?>"
                      data-hari="<?= (int)$d->jumlah_hari ?>"
                      data-shift="<?= (int)$d->jumlah_shift ?>"
                      data-dini="<?= (int)$d->jumlah_dinihari ?>">
                Edit
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>

        <tfoot>
          <tr>
            <th colspan="4">Total Kehadiran</th>
            <th id="ft-transport" class="cell-right"><?= number_format($total_transport, 0, ',', '.') ?></th>
            <th colspan="2">Total Barokah</th>
            <th id="ft-barokah" class="cell-right"><?= number_format($total_jumlah_barokah, 0, ',', '.') ?></th>
            <th colspan="2">Total Dinihari</th>
            <th id="ft-dini" class="cell-right"><?= number_format($total_jumlah_konsumsi_dinihari, 0, ',', '.') ?></th>
            <th id="ft-danru" class="cell-right"><?= number_format($total_danru, 0, ',', '.') ?></th>
            <th id="ft-grand" class="cell-right font-weight-bold"><?= number_format($total_diterima, 0, ',', '.') ?></th>
            <th></th>
          </tr>
        </tfoot>
      </table>
    </form>
    
    <?php
    $statusPeriode = $bulan->status ?? '';
    $jabatanUser   = $this->session->userdata('jabatan') ?? '';
    ?>

    <div class="sticky-actions d-flex justify-content-end">
    <?php if ($statusPeriode === 'Sudah' && $jabatanUser !== 'SDM'): ?>
        <button type="button" id="btnKirim" class="btn btn-primary">
        <i class="mdi mdi-send mr-1"></i> Kirim
        </button>
    <?php elseif ($jabatanUser === 'SDM'): ?>
        <!-- SDM: tidak tampil tombol apa pun (mengikuti alur lama) -->
    <?php elseif ($statusPeriode === 'Terkirim' && $jabatanUser !== 'AdminLembaga'): ?>
        <button type="button" id="btnSetujui" class="btn btn-success">
        <i class="fa fa-check mr-1"></i> Setujui
        </button>
    <?php else: ?>
        <!-- kondisi lain: tanpa tombol -->
    <?php endif; ?>
    </div>
  </div>
</div>

<!-- Modal edit baris -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Koreksi Kehadiran</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
        <div class="modal-body">
            <input type="hidden" id="edit-id">
            <input type="hidden" id="edit-lembaga">

            <div class="form-group">
                <label>Hadir Hari</label>
                <input type="number" min="0" step="1" id="edit-hari" class="form-control" placeholder="Masukkan jumlah hari">
                <small class="form-text text-muted">Saat ini: <span id="cur-hari">0</span></small>
            </div>

            <div class="form-group">
                <label>Hadir Shift</label>
                <input type="number" min="0" step="1" id="edit-shift" class="form-control" placeholder="Masukkan jumlah shift">
                <small class="form-text text-muted">Saat ini: <span id="cur-shift">0</span></small>
            </div>

            <div class="form-group">
                <label>Hadir Dinihari</label>
                <input type="number" min="0" step="1" id="edit-dini" class="form-control" placeholder="Masukkan jumlah dinihari">
                <small class="form-text text-muted">Saat ini: <span id="cur-dini">0</span></small>
            </div>

            <small class="text-muted d-block">Pastikan angka bulat non-negatif. Batas wajar Hari ≤ 31.</small>
        </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
        <button type="button" id="btnSimpanEdit" class="btn btn-primary">Simpan</button>
      </div>
    </div>
  </div>
</div>

<!-- JS -->
<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<!-- DataTables minimal (untuk sticky header/print jika mau) -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
<!--<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>-->

<script>
  // helper format
   const idL = '<?= $bulan->id_kehadiran_lembaga ?? '' ?>';
function fmt(n){ n = Number(n||0); return n.toLocaleString('id-ID'); }

$(function(){
  // (optional) DataTables
  $('#tabel_validasi').DataTable({
    paging:false, searching:false, ordering:false, info:false, fixedHeader:true,
    dom:'Bfrtip'
  });

  // BUKA MODAL
  $(document).on('click', '.btn-edit', function(){
    const id   = $(this).attr('data-id');        // pakai attr agar tidak kena cache jQuery.data
    const idKL = $(this).attr('data-lembaga');

    // AMBIL NILAI TERKINI LANGSUNG DARI TABEL (SEL)
    const hari  = parseInt($('#hari-'+id).text(), 10)  || 0;
    const shift = parseInt($('#shift-'+id).text(), 10) || 0;
    const dini  = parseInt($('#dini-'+id).text(), 10)  || 0;

    // set hidden
    $('#edit-id').val(id);
    $('#edit-lembaga').val(idKL);

    // tampilkan info "Saat ini"
    $('#cur-hari').text(hari);
    $('#cur-shift').text(shift);
    $('#cur-dini').text(dini);

    // input dikosongkan, placeholder tunjukkan nilai saat ini
    $('#edit-hari').val('').attr('placeholder', `Saat ini: ${hari}`);
    $('#edit-shift').val('').attr('placeholder', `Saat ini: ${shift}`);
    $('#edit-dini').val('').attr('placeholder', `Saat ini: ${dini}`);

    $('#modalEdit').modal('show');
  });

  // reset modal saat ditutup (biar tidak bawa nilai lama)
  $('#modalEdit').on('hidden.bs.modal', function(){
    $('#edit-id,#edit-lembaga').val('');
    $('#edit-hari,#edit-shift,#edit-dini').val('').attr('placeholder','');
    $('#cur-hari,#cur-shift,#cur-dini').text('0');
  });

  // SIMPAN KOREKSI
  $('#btnSimpanEdit').on('click', function(){
    const id   = $('#edit-id').val();
    const idKL = $('#edit-lembaga').val();

    // fallback: jika input kosong → pakai nilai "Saat ini"
    let hari  = $('#edit-hari').val()  === '' ? parseInt($('#cur-hari').text(),10)  : parseInt($('#edit-hari').val(),10);
    let shift = $('#edit-shift').val() === '' ? parseInt($('#cur-shift').text(),10) : parseInt($('#edit-shift').val(),10);
    let dini  = $('#edit-dini').val()  === '' ? parseInt($('#cur-dini').text(),10)  : parseInt($('#edit-dini').val(),10);

    if(hari<0||shift<0||dini<0){ Swal.fire('Periksa!', 'Nilai tidak boleh negatif.', 'warning'); return; }
    if(hari>60){ Swal.fire('Periksa!', 'Jumlah hari tidak wajar (>60).', 'warning'); return; }

    $.ajax({
      url: "<?= site_url('validasi_satpam/update_row') ?>",
      type: "POST",
      dataType: "json",
      data: {
        id_kehadiran_satpam:id,
        id_kehadiran_lembaga:idKL,
        jumlah_hari:hari,
        jumlah_shift:shift,
        jumlah_dinihari:dini
      },
      success: function(res){
        if(!res || !res.status){
          Swal.fire('Gagal', (res && res.message) ? res.message : 'Update gagal.', 'error');
          return;
        }

        // update sel baris
        $('#hari-'+id).text(res.row.jumlah_hari);
        $('#shift-'+id).text(res.row.jumlah_shift);
        $('#dini-'+id).text(res.row.jumlah_dinihari);
        $('#jmltrans-'+id).text(fmt(res.row.jumlah_transport));
        $('#jmlbar-'+id).text(fmt(res.row.jumlah_barokah));
        $('#jmlkons-'+id).text(fmt(res.row.jumlah_konsumsi));
        $('#subtotal-'+id).text(fmt(res.row.subtotal));

        // update footer & ringkasan
        $('#ft-transport, #sumTransport').text(fmt(res.totals.total_transport));
        $('#ft-barokah, #sumBarokah').text(fmt(res.totals.total_barokah));
        $('#ft-dini, #sumDini').text(fmt(res.totals.total_dinihari));
        $('#ft-danru, #sumDanru').text(fmt(res.totals.total_danru));
        $('#ft-grand, #sumGrand').text(fmt(res.totals.grand_total));

        // **sinkronkan data-* tombol** (biar kalau klik lagi, tetap benar)
        const $btn = $('.btn-edit[data-id="'+id+'"]');
        $btn.attr('data-hari',  res.row.jumlah_hari)
            .attr('data-shift', res.row.jumlah_shift)
            .attr('data-dini',  res.row.jumlah_dinihari);

        $('#modalEdit').modal('hide');
        Swal.fire({icon:'success', title:'Tersimpan', timer:1400, showConfirmButton:false});
      },
      error: function(xhr){
        console.error(xhr.responseText);
        Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
      }
    });
  });
});

// KIRIM (ubah status: Sudah -> Terkirim)
    $(document).on('click', '#btnKirim', function(){
        const $btn = $(this);
        if(!idL){ Swal.fire('Error','ID periode tidak ditemukan.','error'); return; }
    
        $btn.prop('disabled', true).text('Mengirim...');
        $.ajax({
            url: "<?= site_url('validasi_satpam/update_kirim') ?>",
            type: "POST",
            dataType: "json",
            data: { id_kehadiran_lembaga: idL }
        })
        .done(function(res){
            if(res && res.status){
            Swal.fire({icon:'success', title:'Terkirim', text: res.message || 'Periode berhasil dikirim.', timer:1400, showConfirmButton:false})
            .then(()=>{ 
                // window.location.reload(); 
                window.location.href = "<?= site_url('Kehadiran_satpam') ?>";
            });
            } else {
            Swal.fire('Gagal', (res && res.message) ? res.message : 'Gagal mengirim periode.','error');
            }
        })
        .fail(function(xhr){
            console.error(xhr.responseText);
            Swal.fire('Error','Gagal menghubungi server.','error');
        })
        .always(function(){
            $btn.prop('disabled', false).html('<i class="mdi mdi-send mr-1"></i> Kirim');
        });
    });

// SETUJUI (finalisasi: tulis total_barokah + update status acc)
    $(document).on('click', '#btnSetujui', function(){
        const $btn = $(this);
        if(!idL){ Swal.fire('Error','ID periode tidak ditemukan.','error'); return; }
    
        $btn.prop('disabled', true).text('Menyetujui...');
        $.ajax({
            url: "<?= site_url('Validasi_satpam/save_data') ?>",
            type: "POST",
            dataType: "json",
            data: { id_kehadiran_lembaga: idL }
        })
        .done(function(res){
            if(res && res.status){
            Swal.fire({icon:'success', title:'Berhasil!', text: res.message || 'Periode disetujui & disimpan.', timer:1500, showConfirmButton:false})
            .then(()=>{ 
                // window.location.reload();
                window.location.href = "<?= site_url('Kehadiran_satpam') ?>"; 
            });
            } else {
            Swal.fire('Gagal', (res && res.message) ? res.message : 'Validasi gagal.','error');
            }
        })
        .fail(function(xhr){
            console.error(xhr.responseText);
            Swal.fire('Error','Gagal menghubungi server.','error');
        })
        .always(function(){
            $btn.prop('disabled', false).html('<i class="fa fa-check mr-1"></i> Setujui');
        });
    });

</script>
</body>
</html>
