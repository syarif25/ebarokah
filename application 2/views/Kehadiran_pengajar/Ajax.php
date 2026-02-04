<script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
    <script src="<?php echo base_url() ?>assets/vendor/chart.js/Chart.bundle.min.js"></script>
	<!-- Apex Chart -->
	<script src="<?php echo base_url() ?>assets/vendor/apexchart/apexchart.js"></script>
	
    <!-- Datatable -->
    <script src="<?php echo base_url() ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/plugins-init/datatables.init.js"></script>
    <script src="<?php echo base_url() ?>assets/js/jquery.mask.js"></script>

	<script src="<?php echo base_url() ?>assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
	<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>

    <script>
   
    var save_method; //for save method string
    var table;

    $(document).ready(function(){
     
     table_item =   $('#tabel2').DataTable({ 
        "paging": false,
        "searching": false,
        "ordering": false,
        "info": true,
        "destroy": true,
        "deferRender": true
      });
   });

    function cekInputFile() {
			var inputFile = document.getElementById("file-input").value;
			if (inputFile == "") {
				document.getElementById("btnSave").disabled = true;
			} else {
				document.getElementById("btnSave").disabled = false;
			}
		}
    
    $(function () {
    $('#inputku').mask('000.000.000.000', {reverse: true});

      // $("#example1").DataTable();
    table =   $('#tabel_view').DataTable({
        "ajax": {
            // "searching": true,
            // "responsive" : true,
            // "processing": true,
            // "serverSide": true,
            "url": "<?php echo site_url('kehadiran/data_list_pengajar')?>",
            "type": "POST"
        },

        "columnDefs": [
            { 
                "targets": [ -1 ], //last column
                "orderable": false, //set not orderable
            },
        
        ],  
        "paging": true,
        "searching": true,
        "ordering": true,
        });

    });

    function rekap($id) {
        var id = $id;
        window.open("<?php echo site_url('kehadiran/add/')?>"+id, '_blank');   
    }

    function rekap_pengajar($id) {
        var id = $id;
        window.open("<?php echo site_url('kehadiran/add_pengajar/')?>"+id, '_blank');   
    }

    function add_blanko() {
         $('#form')[0].reset(); // reset form on modals
         save_method = 'add_struktural';
        $('#modal_kehadiran').modal('show');    
    }

    function add_blanko_dosen() {
         $('#form')[0].reset(); // reset form on modals
         save_method = 'add_pengajar';
        $('#modal_kehadiran_dosen').modal('show');    
    }

    function save() {
    // ubah state tombol
    $('#btnSave').text('Proses menyimpan...').prop('disabled', true);

    // pastikan file pdf ada
    var fileInput = document.getElementById('file-input');
    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        $('#btnSave').text('Simpan').prop('disabled', false);
        Swal.fire({
        icon: 'warning',
        title: 'File belum dipilih',
        text: 'File absensi (PDF) wajib diunggah sebelum menyimpan.'
        });
        return;
    }

    // kirim data
    $.ajax({
        url: "<?php echo site_url('kehadiran/ajax_add_pengajar')?>",
        type: "POST",
        data: new FormData($('#form')[0]),
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (data) {
        $('#btnSave').text('Simpan').prop('disabled', false);

        if (data && data.status) {
            // sukses dari server
            Swal.fire({
            icon: 'success',
            title: 'Tersimpan',
            text: data.message || 'Rekap kehadiran berhasil disimpan.'
            }).then(() => {
            window.location = "<?php echo site_url('kehadiran/pengajar')?>";
            });
        } else {
            // gagal logis / validasi server
            Swal.fire({
            icon: 'error',
            title: 'Gagal menyimpan',
            text: (data && data.message)
                ? data.message
                : 'Periksa kembali isian form dan file PDF.'
            });
        }
        },
        error: function () {
        $('#btnSave').text('Simpan').prop('disabled', false);
        Swal.fire({
            icon: 'error',
            title: 'Kesalahan server',
            text: 'Gagal menghubungi server. Coba lagi beberapa saat.'
        });
        }
    });
    }


    function simpan()
    {
        $('#btnSave').text('Proses menyimpan...'); //change button text
        $('#btnSave').attr('disabled',true); //set button disable 
        var url;
        if(save_method == 'add') {
            url = "<?php echo site_url('kehadiran/blanko_add')?>";
            var pesan = ' Menambah data';
        } else {
            url = "<?php echo site_url('kehadiran/blanko_update')?>";
            var pesan = ' Edit data';
        }

        var formData = new FormData($('#form')[0]);
        $.ajax({
            url : url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function(data)
            {
                if(data.status) //if success close modal and reload ajax table
                {
                    $('#modal_kehadiran').modal('hide');
                    reload_table();
                    // notif(pesan);
                    toastr.success('Sukses Menambahkan data');
                }
                else
                {
                     toastr.error('Terjadi kesalahan, cek ulang form input blanko');
                    for (var i = 0; i < data.inputerror.length; i++) 
                    {
                        $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                        $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
                    }
                }
                $('#btnSave').text('save'); //change button text
                $('#btnSave').attr('disabled',false); //set button enable 
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data');
                $('#btnSave').text('save'); //change button text
                $('#btnSave').attr('disabled',false); //set button enable 

            }
        }); 
    }

    function edit_user(id)
    {
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals


        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('Ketentuan_barokah/ajax_edit')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
            $('[name="id_ketentuan"]').val(data.id_ketentuan);
            $('[name="nama_jabatan"]').val(data.nama_jabatan);
            $('[name="kategori"]').val(data.kategori);
            $('[name="barokah"]').val(data.barokah);
            $('#modal_kehadiran').modal('show'); // show bootstrap modal when complete loaded
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }

    function lihat_absen(id)
    {
        $.ajax({
        url : "<?php echo site_url('kehadiran/get_total_absen')?>/" + id,
        type: "POST",
        dataType: "JSON",
        success: function(data)
        {
                $('#nama_lembaga').text(data.data_elemen.nama_lembaga);
                $('#id_kehadiran_lembaga').val(data.data_elemen.id_kehadiran_lembaga);
                $('#jumlah').text(data.data_elemen.jumlah_total);
                $('#periode').text(data.data_elemen.bulan + ' ' + data.data_elemen.tahun );
                table_item.clear();
                table_item.rows.add($(data.html_item)).draw();
                $('#modal_sudah_trans').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text(''); // Set title to Bootstrap modal title
        },
            error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
        });
    }
    
    function lihat_barokah(id)
    {
        $.ajax({
        url : "<?php echo site_url('kehadiran/get_absen')?>/" + id,
        type: "POST",
        dataType: "JSON",
        success: function(data)
        {
                $('#nama_lembaga').text(data.data_elemen.nama_lembaga);
                $('#id_kehadiran_lembaga').val(data.data_elemen.id_kehadiran_lembaga);
                $('#jumlah').text(data.data_elemen.jumlah_total);
                $('#periode').text(data.data_elemen.bulan + ' ' + data.data_elemen.tahun );
                table_item.clear();
                table_item.rows.add($(data.html_item)).draw();
                $('#modal_sudah_trans').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text(''); // Set title to Bootstrap modal title
        },
            error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
        });
    }

    function simpan_pengajar()
    {
        $('#btnSave').text('Proses menyimpan...'); //change button text
        $('#btnSave').attr('disabled',true); //set button disable 
        var url;
        if(save_method == 'add_pengajar') {
            url = "<?php echo site_url('kehadiran/blanko_pengajar_add')?>";
            var pesan = ' Menambah data';
        } else {
            url = "<?php echo site_url('kehadiran/blanko_pengajar_update')?>";
            var pesan = ' Edit data';
        }

        var formData = new FormData($('#form_pengajar')[0]);
        $.ajax({
            url : url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function(data)
            {
                if(data.status) //if success close modal and reload ajax table
                {
                    $('#modal_kehadiran_dosen').modal('hide');
                    reload_table();
                    // notif(pesan);
                    toastr.success('Sukses Menambahkan data');
                }
                else
                {
                     toastr.error('Terjadi kesalahan, cek ulang form input blanko');
                    for (var i = 0; i < data.inputerror.length; i++) 
                    {
                        $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                        $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
                    }
                }
                $('#btnSave').text('save'); //change button text
                $('#btnSave').attr('disabled',false); //set button enable 
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data');
                $('#btnSave').text('save'); //change button text
                $('#btnSave').attr('disabled',false); //set button enable 

            }
        }); 
    }

    function reload_table()
    {
        var pesan = ' Refresh tabel';
        table.ajax.reload(null,false); //reload datatable ajax 
        // notif(pesan);
    }

</script>


<div id="modal_kehadiran_dosen" class="modal fade bd-example-modal-lg" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <form action="#" id="form_pengajar">
                        <div class="card-header">
                            <h4 class="card-title">Tambah Blanko Input Barokah Guru/Dosen</h4>
                        </div>
                        <div class="card-body">
                            <div class="basic-form">
                            <label>Nama Lembaga</label>
                                 <div class="input-group mb-3 input-success-o">
                                 <input type="hidden" name="kategori_pengajar" value="Pengajar">
                                 <select name="id_lembaga_pengajar" class="form-control">
                                    <option value=""></option>
                                    <?php 
                                     
                                    if($this->session->userdata('jabatan') == "AdminLembaga"){
                                        $query = $this->db->get_where('lembaga', ['id_lembaga' => $this->session->userdata('lembaga')]);
                                    } else {
                                    $query = $this->db->get('lembaga');
                                    }
                                    if ($query->num_rows() > 0) {
                                    foreach ($query->result() as $row) {?>
                                    <option value="<?php echo $row->id_lembaga;?>"><?php echo $row->nama_lembaga;?></option>
                                    <?php }
                                    } ?>
                                    </select>
                                    <span class="help-block text-danger" style="color:red"></span>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <label>Bulan</label>
                                        <input type="hidden" name="bulan" class="">
                                            <div class="input-group mb-3 input-success-o">
                                                <select name="bulan_pengajar" class="form-control">
                                                    <option></option>
                                                    <option >Januari</option>
                                                    <option >Februari</option>
                                                    <option >Maret</option>
                                                    <option >April</option>
                                                    <option >Mei</option>
                                                    <option >Juni</option>
                                                    <option >Juli</option>
                                                    <option >Agustus</option>
                                                    <option >September</option>
                                                    <option >Oktober</option>
                                                    <option >November</option>
                                                    <option >Desember</option>
                                                </select>
                                                <span class="help-block text-danger" style="color:red"></span>
                                            </div>
                                    </div>
                                    <div class="col-6">
                                        <label>Tahun Anggaran</label>
                                        <div class="input-group mb-3 input-success-o">
                                            <select name="tahun_pengajar" class="form-control">
                                                    <!--<option></option>-->
                                                    <option >2023/2024</option>
                                                    <option >2024/2025</option>
                                                    <option >2026</option>
                                            </select>
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="btnSimpan" onclick="simpan_pengajar()">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal_sudah_trans" class="modal fade bd-example-modal-lg" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <form action="#" id="form">
                        <div class="card-header">
                            <h4 class="card-title">Form Rekap Kehadiran Umana</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-3">
                                    <label>Nama Lembaga</label>
                                    <h5 id="nama_lembaga"></h5>
                                    <input type="hidden" id="id_kehadiran_lembaga" name="id_kehadiran_lembaga">
                                </div>
                                <div class="col-3">
                                    <label>Periode</label>
                                    <h5 id="periode"></h5>
                                </div>
                                <!-- <div class="col-3">
                                    <label>Jumlah </label>
                                    <h5 id="jumlah"></h5>
                                </div> -->
                            </div>
                            <br>
                            <form action="#" id="form">
                                <table class="table" id="tabel2" style="min-width: 100%" >
                                    <thead>
                                        <tr>
                                            <td>No</td>
                                            <td>Nama Lengkap</td>
                                            <td>Jabatan</td>
                                            <td>Jumlah Hadir</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    
                                    </tbody>
                                </table>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>