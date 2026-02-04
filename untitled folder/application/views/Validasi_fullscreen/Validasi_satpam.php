<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Validasi Penggajian Satpam</title>
    <link rel="icon" href="<?php echo base_url();?>assets/p2s2_fav.png" type="image/x-icon">
    <link rel="shortcut icon" href="<?php echo base_url();?>assets/p2s2_fav.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            font-size: 14px;
        }
        h4 {
            margin-bottom: 20px;
        }
        table.dataTable {
            white-space: nowrap;
        }
        thead th {
            vertical-align: middle !important;
            text-align: center;
        }
        tfoot th {
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Validasi Penggajian Satpam Bulan <?= $bulan->bulan ?> <?= $bulan->tahun ?></h4>
        <div>
            <button class="btn btn-warning" onclick="window.print()">
                🖨 Cetak
            </button>
            <button class="btn btn-secondary" onclick="window.history.back()">
                ⬅ Kembali
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <form action="#" id="form">
            <table id="tabel_validasi" class="table table-bordered table-striped dt-responsive nowrap" style="width:100%">
                <thead class="thead-light">
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Alamat</th>
                        <th colspan="3">Kehadiran</th>
                        <th colspan="3">Kehadiran</th>
                        <th colspan="3">Kehadiran</th>
                        <th rowspan="2">Total</th>
                    </tr>
                    <tr>
                        <th>Hari</th>
                        <th>Trans</th>
                        <th>Jumlah</th>
                        <th>Shift</th>
                        <th>Rank</th>
                        <th>Jumlah</th>
                        <th>Dini</th>
                        <th>Kons</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $total_transport = 0;
                    $total_jumlah_barokah  = 0;
                    $total_jumlah_konsumsi_dinihari  = 0;
                    $total_diterima   = 0;
            
                    foreach($isilist as $d):
                        $jumlah_transport = $d->jumlah_hari * $d->nominal_transport;
                        $barokah_satpam = 17500; //harus diganti otomatis
                        $jumlah_barokah = $d->jumlah_shift * $barokah_satpam;
                        $konsumsi_dinihari = 5000; //harus diganti otomatis
                        $jumlah_konsumsi_dinihari = $d->jumlah_dinihari * $konsumsi_dinihari;
                        $diterima = $jumlah_transport + $jumlah_barokah + $jumlah_konsumsi_dinihari;

                        $total_transport += $jumlah_transport;
                        $total_jumlah_barokah  += $jumlah_barokah;
                        $total_jumlah_konsumsi_dinihari  += $jumlah_konsumsi_dinihari;
                        $total_diterima   += $diterima;
                    ?>
                    <tr>
                        <input type="hidden" name="bulan[]" value="<?php echo $d->bulan ?>">
                        <input type="hidden" name="tahun[]" value="<?php echo $d->tahun ?>">
                        <input type="hidden" name="id_satpam[]" value="<?php echo $d->id_satpam ?>">
                        <input type="text" name="id_kehadiran_lembaga" value="<?php echo $d->id_kehadiran_lembaga ?>">
                        <input type="hidden" name="id_kehadiran_satpam[]" value="<?php echo $d->id_kehadiran_satpam ?>">
                        <td><?= $no++ ?></td>
                        <td><?= htmlentities($d->gelar_depan.' '.$d->nama_lengkap.' '.$d->gelar_belakang); ?></td>
                        <td class="text-center">
                            <?= $d->jumlah_hari ?>
                            <input type="hidden" name="jumlah_hari[]" value="<?php echo $d->jumlah_hari ?>">
                        </td>
                        <td class="text-right">
                            <?= number_format($d->nominal_transport,0,',','.') ?>
                            <input type="hidden" name="nominal_transport[]" value="<?php echo $d->nominal_transport ?>">
                        </td>
                        <td class="text-right">
                            <?= number_format($jumlah_transport,0,',','.') ?>
                            <input type="hidden" name="jumlah_transport[]" value="<?php echo $jumlah_transport ?>">
                        </td>
                        <td class="text-center">
                            <?= $d->jumlah_shift ?>
                            <input type="hidden" name="jumlah_shift[]" value="<?php echo $d->jumlah_shift ?>">
                        </td>
                        <td class="text-right">
                            <?= number_format($barokah_satpam,0,',','.') ?>
                            <input type="hidden" name="rank[]" value="<?php echo $barokah_satpam ?>">
                        </td>
                        <td class="text-right">
                            <?= number_format($jumlah_barokah,0,',','.') ?>
                            <input type="hidden" name="jumlah_barokah[]" value="<?php echo $jumlah_barokah ?>">
                        </td>
                        <td class="text-center">
                            <?= $d->jumlah_dinihari ?>
                            <input type="hidden" name="jumlah_dinihari[]" value="<?php echo $d->jumlah_dinihari ?>">
                        </td>
                        <td class="text-right">
                            <?= number_format($konsumsi_dinihari,0,',','.') ?>
                            <input type="hidden" name="konsumsi[]" value="<?php echo $konsumsi_dinihari ?>">
                        </td>
                        <td class="text-right">
                            <?= number_format($jumlah_konsumsi_dinihari,0,',','.') ?>
                            <input type="hidden" name="jumlah_konsumsi[]" value="<?php echo $jumlah_konsumsi_dinihari ?>">
                        </td>
                        <td class="text-right font-weight-bold">
                            <?= number_format($diterima,0,',','.') ?>
                            <input type="hidden" name="diterima[]" value="<?php echo $diterima ?>">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4">Total Kehadiran</th>
                        <th><?= number_format($total_transport, 0, ',', '.') ?></th>
                        <th colspan="2">Total Barokah</th>
                        <th><?= number_format($total_jumlah_barokah, 0, ',', '.') ?></th>
                        <th colspan="2">Total Dinihari</th>
                        <th><?= number_format($total_jumlah_konsumsi_dinihari, 0, ',', '.') ?></th>
                        <th class="text-right">
                            <?= number_format($total_diterima, 0, ',', '.') ?>
                            <input type="hidden" name="jumlah_total" value="<?php echo $total_diterima ?>"></td>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </form>
        <div class="d-flex justify-content-end mt-3">
            <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">
                <i class="fa fa-check mr-1"></i> Setujui
            </button>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    $('#tabel_validasi').DataTable({
        paging: false,
        ordering: false,
        searching: false,
        info: false,
        responsive: true,
        language: {
            emptyTable: "Tidak ada data",
        }
    });
});

function save() {
    console.log("=== [DEBUG] Proses simpan dimulai ===");

    const $btn = $('#btnSave');
    $btn.text('Menyimpan...').prop('disabled', true);

    const form = $('#form')[0];
    if (!form) {
        alert("Form tidak ditemukan!");
        console.error("[ERROR] #form tidak ada di DOM.");
        $btn.text('Save').prop('disabled', false);
        return;
    }

    const formData = new FormData(form);
    console.table([...formData.entries()]);

    $.ajax({
        url: "<?php echo site_url('validasi_satpam/save_data') ?>",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (res) {
            console.log("[DEBUG] Respon server:", res);

            if (res.status) {
                console.info("[INFO] Data berhasil disimpan.");
                // ✅ Redirect ke halaman validasi + tampilkan notifikasi
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data gajian satpam berhasil disimpan.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = "<?php echo site_url('validasi') ?>";
                });

            } else if (res.inputerror) {
                console.warn("[WARN] Validasi server gagal.");
                res.inputerror.forEach((field, i) => {
                    $('[name="' + field + '"]').parent().parent().addClass('has-error');
                    $('[name="' + field + '"]').next().text(res.error_string[i]);
                    console.warn(`Field error: ${field} → ${res.error_string[i]}`);
                });
            } else {
                Swal.fire('Gagal', 'Terjadi kesalahan saat validasi data.', 'error');
            }
        },
        error: function (xhr, status, err) {
            console.error("[ERROR] AJAX gagal:", status, err);
            console.log("[DEBUG] Response Text:", xhr.responseText);
            Swal.fire('Error', 'Gagal menyimpan data, coba lagi.', 'error');
        },
        complete: function () {
            $btn.text('Save').prop('disabled', false);
        }
    });
}

</script>
</body>
</html>
