<script>
  const base_url = "<?= base_url(); ?>";
</script>

<!--  JS vendor -->
<script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
<script src="<?php echo base_url() ?>assets/vendor/chart.js/Chart.bundle.min.js"></script>
<script src="<?php echo base_url() ?>assets/vendor/apexchart/apexchart.js"></script>
<script src="<?php echo base_url() ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/plugins-init/datatables.init.js"></script>
<script src="<?php echo base_url() ?>assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>

<!--  Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
var save_method; // for save method string
var table;

$(function () {
    table = $('#tabel_view').DataTable({
        "ajax": {
            "url": "<?php echo site_url('penempatan_satpam/data_list')?>",
            "type": "POST"
        },
        "columnDefs": [
            { 
                "targets": [ -1 ], // last column
                "orderable": false,
            },
        ],  
        "paging": true,
        "searching": true,
        "ordering": true,
    });

    $('#transport_select').select2({
        dropdownParent: $('#modal_satpam'),
        placeholder: 'Pilih Transport / Kehadiran',
        allowClear: true,
        width: '100%',
        templateResult: function (data) {
            if (!data.id) { return data.text; }
            const parts = data.text.split('|');
            const nama = parts[0]?.trim();
            const kategori = parts[1]?.trim();
            return $('<span><strong>' + nama + '</strong><br><small>' + kategori + '</small></span>');
        }
    });

});

function add_satpam() {
    $('#form')[0].reset(); 
    save_method = 'add';
    $('#nik').val(null).trigger('change');
    $('#transport_select').val(null).trigger('change');
    $('#modal_satpam').modal('show');    
}

$(document).ready(function () {
    $('#nik').select2({
        dropdownParent: $('#modal_satpam'),
        placeholder: 'Pilih NIK',
        allowClear: true,
    });

    $('#nik').on('change', function () {
        var nik = $(this).val();
        console.log('NIK dipilih:', nik);

        if (nik) {
            $.ajax({
                url: base_url + 'penempatan_satpam/get_umana/' + nik,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (data) {
                        $('[name="nama_lengkap"]').val(data.nama_lengkap);
                        $('[name="niy"]').val(data.niy);
                    } else {
                        $('[name="nama_lengkap"]').val('');
                        $('[name="niy"]').val('');
                    }
                },
                error: function () {
                    console.log('Gagal mengambil data UMANA');
                }
            });
        } else {
            $('[name="nama_lengkap"]').val('');
            $('[name="niy"]').val('');
        }
    });
});

function simpan()
{
    $('#btnSave').text('Proses menyimpan...');
    $('#btnSave').attr('disabled', true);
    //kode sebelumnya
    // var url = "<?php echo site_url('penempatan_satpam/ajax_add')?>";
    var formData = new FormData($('#form')[0]);

    var url;
        if(save_method == 'add') {
            url = "<?php echo site_url('penempatan_satpam/ajax_add')?>";
            var pesan = ' Menambah data';
        } else {
            url = "<?php echo site_url('penempatan_satpam/ajax_update')?>";
            var pesan = ' Edit data';
        }

    $.ajax({
        url : url,
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "JSON",
        success: function(data)
        {
            if (data.status) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Data penempatan berhasil disimpan.',
                    icon: 'success',
                    timer: 1800,
                    showConfirmButton: false
                }).then(() => {
                    // Reload tabel dan tutup modal
                    $('#modal_satpam').modal('hide');
                    reload_table();
                });
            } else {
                $('.form-group').removeClass('has-error'); // bersihkan error sebelumnya
                $('.help-block').empty(); // bersihkan pesan error sebelumnya

                for (var i = 0; i < data.inputerror.length; i++) {
                    var inputName = data.inputerror[i];
                    var errorMsg = data.error_string[i];

                    $('[name="'+inputName+'"]').closest('.form-group').addClass('has-error'); 
                    $('[name="'+inputName+'"]').siblings('.help-block').text(errorMsg);
                }
            }
            $('#btnSave').text('Simpan');
            $('#btnSave').attr('disabled', false);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            Swal.fire({
                title: 'Gagal!',
                text: 'Terjadi kesalahan saat menyimpan data.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            $('#btnSave').text('Simpan');
            $('#btnSave').attr('disabled', false);
        }
    }); 
}

function edit_satpam(id)
{
    save_method = 'update';
    $('#form')[0].reset(); 
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();

    $.ajax({
        url : "<?php echo site_url('penempatan_satpam/ajax_edit')?>/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            // ID tersembunyi
            $('[name="id_satpam"]').val(data.id_satpam);

            // Select2 untuk NIK
            $('[name="nik"]').val(data.nik).trigger('change');

            // Nama & NIY (kalau hanya tampilan, boleh tetap pakai val biasa)
            $('[name="niy"]').val(data.niy);
            $('[name="nama_lengkap"]').val(data.nama_lengkap);

            // Select2 untuk Transport
            $('[name="transport"]').val(data.id_transport).trigger('change');

            // Input biasa
            $('[name="tgl_mulai"]').val(data.tgl_mulai);
            $('[name="tgl_selesai"]').val(data.tgl_selesai);
            $('[name="status"]').val(data.status);
            $('[name="is_danru"]').val(data.is_danru);

            // Tampilkan modal
            $('#modal_satpam').modal('show');
            $('.modal-title').text('Edit Data Satpam');
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}


function reload_table()
{
    table.ajax.reload(null,false);
}
</script>


<div id="modal_satpam" class="modal fade bd-example-modal-lg" tabindex="-1" aria-hidden="true" style="display: none;">
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Penempatan Satpam</h5><small id="id"></small>
            <button type="button" class="btn-close" data-bs-dismiss="modal">
            </button>
        </div>
        <div class="modal-body">
            <div class="card">
                <form id="form">
                    <div class="row">
                        <div class="mb-3 col-sm-5 input-success-o">
                            <label class="form-label">NIK</label>
                            <select id="nik" class="form-control nik" name="nik">
                                <option value=""></option>
                                <?php 
                                $query = $this->db->get('umana');
                                if ($query->num_rows() > 0) {
                                foreach ($query->result() as $row) {?>
                                <option value="<?php echo $row->nik;?>"><?php echo $row->nik.' '.$row->nama_lengkap;?></option>
                                <?php }
                                } ?>
                            </select>
                            <input type="hidden" name="id_satpam" value="<?php echo $id_satpam; ?>">
                            <span class="help-block text-danger"></span>
                        </div>
                        <div class="mb-3 col-sm-3 input-success-o">
                            <label class="form-label">Nomor Induk P2S3</label>
                            <input type="text" class="form-control" name="niy" readonly>
                            <span class="help-block text-danger"></span>
                        </div>
                        <div class="mb-3 col-sm-4 input-success-o">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama_lengkap" readonly>
                            <span class="help-block text-danger"></span>
                        </div>
                        <div class="mb-3 col-sm-6 input-success-o">
                            <label class="form-label">Transport/Kehadiran</label>
                            <select class="form-control" name="transport" id="transport_select">
                                <option value=""></option>
                                <?php 
                                $query = $this->db->get('transport');
                                if ($query->num_rows() > 0) {
                                    foreach ($query->result() as $row) { ?>
                                        <option value="<?= $row->id_transport; ?>">
                                            <?= $row->nama_transport . " | " . $row->kategori_transport; ?>
                                        </option>
                                <?php } } ?>
                            </select>
                            <span class="help-block text-danger" style="color:red"></span>
                        </div>
                        <div class="mb-3 col-sm-3 input-success-o">
                            <label class="form-label">Mulai Tugas</label>
                            <input type="date" class="form-control" name="tgl_mulai">
                            <span class="help-block text-danger"></span>
                        </div>
                        <div class="mb-3 col-sm-3 input-success-o">
                            <label class="form-label">Akhir Tugas</label>
                            <input type="date" class="form-control" name="tgl_selesai">
                            <span class="help-block text-danger"></span>
                        </div>
                        <div class="mb-3 col-sm-3 input-success-o">
                            <label class="form-label">Status</label>
                            <select  class="form-control" name="status">
                               <option>Aktif</option>
                               <option>Tidak Aktif</option>
                            </select>
                            <span class="help-block text-danger"></span>
                        </div>
                        <div class="mb-3 col-sm-3 input-success-o">
                            <label class="form-label">Posisi Danru</label>
                            <select class="form-control" name="is_danru">
                               <option value="0">Tidak</option>
                               <option value="1">Ya</option>
                            </select>
                            <span class="help-block text-danger"></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">File SK</label>
                            <div class="input-group mb-3">
                                <div class="form-file">
                                    <input type="file" name="file_sk" class="form-file-input form-control">
                                </div>
                            </div>
                            <span class="help-block text-danger" style="color:red"></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Tutup</button>
            <button type="button" class="btn btn-primary" id="btnSave" onclick="simpan()">Simpan Data</button>
        </div>
    </div>
</div>
</div>
