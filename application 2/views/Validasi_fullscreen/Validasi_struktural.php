<!-- application/views/validasi_fullscreen/validasi_struktural.php -->
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Validasi Struktural - Koreksi Kehadiran</title>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">



  <style>
    body { background:#f8f9fa; font-size:14px; }
    .table thead th { vertical-align:middle!important; text-align:center; }
    .cell-right{ text-align:right }
    .cell-center{ text-align:center }
    .badge-summary{ font-size:.9rem; margin-right:.5rem }
    .sticky-actions{ position:sticky; bottom:0; background:#fff; z-index:10; border-top:1px solid #e9ecef; padding:.75rem }

    

  </style>
</head>
<body>

<div class="container-fluid my-3">
  <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
        Kehadiran Bulan <?php echo isset($periode)? htmlentities($periode->bulan.' '.$periode->tahun) : '-'; ?>
        </h4>
        <div>
        <span class="badge 
        <?= ($periode->status == 'acc') ? 'badge-success' : 
            (($periode->status == 'Terkirim') ? 'badge-warning text-dark' : 'badge-secondary') ?>">
        Status: <?= ucfirst($periode->status ?? 'Belum dikirim') ?>
        </span>
    </div>
    <div>
      <?php if(!empty($periode->file)): ?>
        <a class="btn btn-info text-white" target="_blank"
           href="<?php echo base_url('upload/'.$periode->file); ?>">
          <i class="fa fa-file mr-1"></i> Lihat File Absensi
        </a>
      <?php endif; ?>

      <?php if(!empty($periode->id_kehadiran_lembaga)): ?>
        <a class="btn btn-warning text-dark" target="_blank"
           href="<?php echo base_url('Kehadiran/Cetak/'.$periode->id_kehadiran_lembaga); ?>">
          <i class="fa fa-print mr-1"></i> Cetak
        </a>
      <?php endif; ?>

      <?php if(!empty($periode->id_lembaga)): ?>
        <a class="btn btn-danger text-white" target="_blank"
           href="<?php echo base_url('Kehadiran/Cetak_Potongan_struktural/'.$periode->id_lembaga); ?>">
          <i class="fa fa-scissors mr-1"></i> Potongan
        </a>
      <?php endif; ?>
    </div>
  </div>

  <!-- BADGE RINGKASAN (sementara total dihitung di view ini) -->
  <?php
    $tot_tunjab = $tot_tmp = $tot_kehadiran = $tot_tunkel = $tot_tunjanak = 0;
    $tot_kehormatan = $tot_tbk = $tot_barokah = $tot_potongan = $tot_grand = 0;
  ?>
  <div class="mb-3">
    <span class="badge badge-secondary badge-summary">Total Tunjab: <span id="sumTunjab">0</span></span>
    <span class="badge badge-secondary badge-summary">Total TMP: <span id="sumTMP">0</span></span>
    <span class="badge badge-secondary badge-summary">Total Kehadiran: <span id="sumKehadiran">0</span></span>
    <span class="badge badge-secondary badge-summary">Total Tunkel: <span id="sumTunkel">0</span></span>
    <span class="badge badge-secondary badge-summary">Total Tunj Anak: <span id="sumTunjAnak">0</span></span>
    <span class="badge badge-secondary badge-summary">Total Kehormatan: <span id="sumKehormatan">0</span></span>
    <span class="badge badge-secondary badge-summary">Total TBK: <span id="sumTBK">0</span></span>
    <span class="badge badge-primary badge-summary">Grand Total: <span id="sumGrand">0</span></span>
  </div>

  <!-- TABEL -->
  <div class="table-responsive">
    <form id="form">
      <table class="table table-bordered table-striped" id="tabel_validasi">
        <thead class="thead-light">
          <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Nama Lengkap</th>
            <th rowspan="2">Eselon</th>
            <th rowspan="2">TMT</th>
            <th rowspan="2">Tunjab</th>
            <th rowspan="2">MP</th>
            <th rowspan="2">TMP</th>
            <th colspan="2">Kehadiran</th>
            <th rowspan="2">Tunkel</th>
            <th rowspan="2">Tunj Anak</th>
            <th rowspan="2">Kehormatan</th>
            <th rowspan="2">TBK</th>
            <th rowspan="2">Jumlah Barokah</th>
            <th rowspan="2">Potongan</th>
            <th rowspan="2">Diterima</th>
            <th rowspan="2" style="width:70px">Aksi</th>
          </tr>
          <tr>
            <th>Hari</th>
            <th>Jumlah</th>
          </tr>
        </thead>
        <tbody>
            <?php if(!empty($isilist)): $no=1; foreach($isilist as $row): ?>
            <tr id="row-<?php echo $row->id_kehadiran; ?>">
                <td class="cell-center"><?php echo $no++; ?></td>
                <td><?php echo htmlentities(trim(($row->gelar_depan ?? '').' '.($row->nama_lengkap ?? '').' '.($row->gelar_belakang ?? ''))); ?></td>
                <td><?php echo htmlentities($row->nama_jabatan ?? '-'); ?></td>
                <td class="cell-center">
                <?php echo !empty($row->tmt_struktural) ? htmlentities(date('Y', strtotime($row->tmt_struktural))) : '-'; ?>
                </td>

                <td class="cell-right"><?php echo number_format((int)$row->tunjab,0,',','.'); ?></td>
                <td class="cell-center"><?php echo (int)$row->mp; ?></td>
                <td class="cell-right"><?php echo number_format((int)$row->tmp,0,',','.'); ?></td>

                <td class="cell-center"><?php echo (int)$row->jumlah_hadir; ?></td>
                <td class="cell-right"><?php echo number_format((int)$row->nominal_kehadiran,0,',','.'); ?></td>

                <td class="cell-right"><?php echo number_format((int)$row->tunkel,0,',','.'); ?></td>
                <td class="cell-right"><?php echo number_format((int)$row->tunj_anak,0,',','.'); ?></td>
                <td class="cell-right"><?php echo number_format((int)$row->nilai_kehormatan,0,',','.'); ?></td>
                <td class="cell-right">
                <a href="javascript:void(0)" 
                    class="text-primary link-tbk" 
                    data-id="<?php echo $row->id_penempatan; ?>">
                    <?php echo number_format((int)$row->tbk,0,',','.'); ?>
                </a>
                </td>


                <td class="cell-right font-weight-bold"><?php echo number_format((int)$row->jumlah_barokah,0,',','.'); ?></td>
                <td class="cell-right"><?php echo number_format((int)$row->potongan,0,',','.'); ?></td>
                <td class="cell-right font-weight-bold"><?php echo number_format((int)$row->diterima,0,',','.'); ?></td>

                <td class="cell-center">
                <?php if ($periode->status !== 'acc' && $periode->status !== 'selesai'): ?>
                    <button type="button"
                            class="btn btn-sm btn-outline-primary btn-edit"
                            data-toggle="tooltip"
                            title="Koreksi jumlah kehadiran"
                            data-id="<?= $row->id_kehadiran ?>"
                            data-lembaga="<?= $row->id_kehadiran_lembaga ?>"
                            data-hari="<?= (int)$row->jumlah_hadir ?>">
                    Edit
                    </button>
                <?php else: ?>
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary"
                            disabled
                            title="Data sudah disetujui">
                    Edit
                    </button>
                <?php endif; ?>
                </td>


            </tr>
            <?php endforeach; endif; ?>
            </tbody>


            <tfoot>
                <tr>
                    <th colspan="4"></th>
                    <th class="cell-right" id="ft-tunjab"><?php echo number_format($total_tunjab,0,',','.'); ?></th>
                    <th></th>
                    <th class="cell-right" id="ft-tmp"><?php echo number_format($total_tmp,0,',','.'); ?></th>
                    <th></th>
                    <th class="cell-right" id="ft-kehadiran"><?php echo number_format($total_kehadiran,0,',','.'); ?></th>

                    <!-- INI YANG PENTING -->
                    <th class="cell-right" id="ft-tunkel"><?php echo number_format($total_tunkel,0,',','.'); ?></th>
                    <th class="cell-right" id="ft-tunjanak"><?php echo number_format($total_tunjanak,0,',','.'); ?></th>
                    <th class="cell-right" id="ft-kehormatan"><?php echo number_format($total_kehormatan,0,',','.'); ?></th>

                    <th class="cell-right" id="ft-tbk"><?php echo number_format($total_tbk,0,',','.'); ?></th>
                    <th class="cell-right" id="ft-barokah"><?php echo number_format($total_barokah,0,',','.'); ?></th>
                    <th class="cell-right" id="ft-potongan"><?php echo number_format($total_potongan,0,',','.'); ?></th>
                    <th class="cell-right font-weight-bold" id="ft-grand"><?php echo number_format($grand_total,0,',','.'); ?></th>
                    <th></th>
                </tr>
            </tfoot>

      </table>
    </form>

    <!-- Info untuk operator -->
    <div class="alert alert-info py-2 px-3 small d-flex align-items-center mb-2">
    <i class="fa fa-info-circle mr-2"></i>
    Jika ada koreksi pada <strong>Jumlah Kehadiran</strong>, silakan klik tombol &nbsp;<strong> Edit </strong> &nbsp;di kolom paling kanan.
    </div>


    <?php
    $statusPeriode = $periode->status ?? '';
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

<!-- Modal Detail TBK -->
<div class="modal fade" id="modalTBK" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Tugas Beban Kerja</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <table class="table table-sm table-bordered">
          <thead class="thead-light">
            <tr>
              <th>Nama Kegiatan</th>
              <th class="text-right">Nominal</th>
              <th class="text-center">Periode Maksimum</th>
            </tr>
          </thead>
          <tbody id="tbkBody">
            <tr><td colspan="3" class="text-center text-muted">Memuat...</td></tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Koreksi Kehadiran -->
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
          <label>Jumlah Hadir (Hari)</label>
          <input type="number" min="0" step="1" id="edit-hari" class="form-control" placeholder="Masukkan jumlah hari">
          <small class="form-text text-muted">Saat ini: <span id="cur-hari">0</span></small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
        <button type="button" id="btnSimpanEdit" class="btn btn-primary">Simpan</button>
      </div>
    </div>
  </div>
</div>


<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>


<script>
    $('#tabel_validasi').DataTable({
    paging: false,
    searching: false,
    ordering: false,
    info: false,
    fixedHeader: true
    });

    $(function () {
    $('[data-toggle="tooltip"]').tooltip();
    });
    const idL = '<?= $periode->id_kehadiran_lembaga ?? '' ?>';

    // KIRIM (ubah status: Sudah -> Terkirim)
    $(document).on('click', '#btnKirim', function(){
    const $btn = $(this);
    if(!idL){ Swal.fire('Error','ID periode tidak ditemukan.','error'); return; }

    $btn.prop('disabled', true).text('Mengirim...');
    $.ajax({
        url: "<?= site_url('Validasi_struktural/update_kirim') ?>",
        type: "POST",
        dataType: "json",
        data: { id_kehadiran_lembaga: idL }
    })
    .done(function(res){
        if(res && res.status){
        Swal.fire({icon:'success', title:'Terkirim', text: res.message || 'Periode berhasil dikirim.', timer:1400, showConfirmButton:false})
        .then(()=>{ 
            // window.location.reload(); 
            window.location.href = "<?= site_url('Kehadiran') ?>";
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
        url: "<?= site_url('Validasi_struktural/save_data') ?>",
        type: "POST",
        dataType: "json",
        data: { id_kehadiran_lembaga: idL }
    })
    .done(function(res){
        if(res && res.status){
        Swal.fire({icon:'success', title:'Berhasil!', text: res.message || 'Periode disetujui & disimpan.', timer:1500, showConfirmButton:false})
        .then(()=>{ 
            // window.location.reload();
            window.location.href = "<?= site_url('Kehadiran') ?>"; 
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

    // tampilkan detail TBK
    $(document).on('click', '.link-tbk', function(){
    const id = $(this).data('id');
    $('#tbkBody').html('<tr><td colspan="3" class="text-center text-muted">Memuat...</td></tr>');
    $('#modalTBK').modal('show');

    $.getJSON('<?= site_url('Validasi_struktural/detail_tbk/') ?>' + id, function(data){
        if(!data || data.length === 0){
        $('#tbkBody').html('<tr><td colspan="3" class="text-center text-muted">Tidak ada data TBK aktif.</td></tr>');
        return;
        }

        let rows = '';
        data.forEach(item => {
        rows += `
            <tr>
            <td>${item.nama_kegiatan ?? '-'}</td>
            <td class="text-right">${Number(item.nominal_tbk ?? 0).toLocaleString('id-ID')}</td>
            <td class="text-center">${item.max_periode ?? '-'}</td>
            </tr>`;
        });
        $('#tbkBody').html(rows);
    }).fail(function(){
        $('#tbkBody').html('<tr><td colspan="3" class="text-center text-danger">Gagal memuat data TBK.</td></tr>');
    });
    });

    // === KOREKSI KEHADIRAN ===
$(function() {
  // buka modal
  $(document).on('click', '.btn-edit', function() {
    const id = $(this).data('id');
    const idLembaga = $(this).data('lembaga');
    const hari = $(this).data('hari') || 0;

    $('#edit-id').val(id);
    $('#edit-lembaga').val(idLembaga);
    $('#cur-hari').text(hari);
    $('#edit-hari').val('').attr('placeholder', `Saat ini: ${hari}`);

    $('#modalEdit').modal('show');
  });

  // reset modal
  $('#modalEdit').on('hidden.bs.modal', function(){
    $('#edit-id, #edit-lembaga').val('');
    $('#edit-hari').val('');
    $('#cur-hari').text('0');
  });

  // simpan perubahan
  $('#btnSimpanEdit').on('click', function(){
    const id = $('#edit-id').val();
    const idLembaga = $('#edit-lembaga').val();
    let hari = $('#edit-hari').val() === '' ? parseInt($('#cur-hari').text(), 10) : parseInt($('#edit-hari').val(), 10);

    if(hari < 0){ Swal.fire('Periksa!', 'Nilai tidak boleh negatif.', 'warning'); return; }
    if(hari > 100){ Swal.fire('Periksa!', 'Jumlah hari tidak wajar (>100).', 'warning'); return; }

    $.ajax({
      url: "<?= site_url('Validasi_struktural/update_row') ?>",
      type: "POST",
      dataType: "json",
      data: {
        id_kehadiran: id,
        id_kehadiran_lembaga: idLembaga,
        jumlah_hadir: hari
      },
      success: function(res) {
  if (!res || !res.status) {
    Swal.fire('Gagal', (res && res.message) ? res.message : 'Update gagal.', 'error');
    return;
  }

  // update tampilan baris
  $('#hari-' + id).text(res.row.jumlah_hadir);
  $('#jmlhadir-' + id).text(res.row.nominal_kehadiran.toLocaleString('id-ID'));
  $('#jumlahbarokah-' + id).text(res.row.jumlah_barokah.toLocaleString('id-ID'));
  $('#diterima-' + id).text(res.row.diterima.toLocaleString('id-ID'));

  // update footer & badge (jika kamu kirim totals dari server)
  if (res.totals) {
    $('#ft-kehadiran').text(res.totals.total_kehadiran.toLocaleString('id-ID'));
    $('#ft-barokah').text(res.totals.total_barokah.toLocaleString('id-ID'));
    $('#ft-grand').text(res.totals.grand_total.toLocaleString('id-ID'));

    $('#sumKehadiran').text($('#ft-kehadiran').text());
    $('#sumGrand').text($('#ft-grand').text());
  }

  // sinkronkan data-* tombol untuk klik selanjutnya
  $('.btn-edit[data-id="' + id + '"]').attr('data-hari', res.row.jumlah_hadir);

  // Tutup modal dan reload setelah benar2 tertutup (fallback: Swal.then)
  let reloaded = false;
  const doReload = () => {
    if (!reloaded) { reloaded = true; window.location.reload(); }
  };

  if ($('#modalEdit').hasClass('show')) {
    $('#modalEdit').one('hidden.bs.modal', doReload).modal('hide');
  }

  Swal.fire({
    icon: 'success',
    title: 'Tersimpan',
    timer: 1200,
    showConfirmButton: false
  }).then(doReload);

  // fallback ekstra kalau dua-duanya tidak terpanggil karena alasan lain
  setTimeout(doReload, 2000);
},
error: function(xhr) {
  console.error(xhr.responseText);
  Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
},
      error: function(xhr) {
        console.error(xhr.responseText);
        Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
      }
    });
  });
});


  // sinkronkan badge ringkasan dari nilai footer
  $(function(){
    $('#sumTunjab').text($('#ft-tunjab').text());
    $('#sumTMP').text($('#ft-tmp').text());
    $('#sumKehadiran').text($('#ft-kehadiran').text());
    $('#sumTunkel').text($('#ft-tunkel').text());
    $('#sumTunjAnak').text($('#ft-tunjanak').text());
    $('#sumKehormatan').text($('#ft-kehormatan').text());
    $('#sumTBK').text($('#ft-tbk').text());
    $('#sumGrand').text($('#ft-grand').text());
  });

</script>
</body>
</html>
