<script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
    <script src="<?php echo base_url() ?>assets/vendor/chart.js/Chart.bundle.min.js"></script>
	<!-- Apex Chart -->
	<script src="<?php echo base_url() ?>assets/vendor/apexchart/apexchart.js"></script>
	
    <!-- Datatable -->
    <script src="<?php echo base_url() ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/plugins-init/datatables.init.js"></script>
    <script src="<?php echo base_url() ?>assets/js/jquery.mask.js"></script>

	<script src="<?php echo base_url() ?>assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>

    <script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
	<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>


    <script>
   
    var save_method; //for save method string
    var table;
   
    $(function () {
    $('#inputku').mask('000.000.000.000', {reverse: true});

      // $("#example1").DataTable();
    table =   $('#tabel_view').DataTable({
        "ajax": {
            // "searching": true,
            // "responsive" : true,
            // "processing": true,
            // "serverSide": true,
            "url": "<?php echo site_url('kehadiran/data_list_log')?>",
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

    function add_kehadiran() {
         $('#form')[0].reset(); // reset form on modals
         save_method = 'add';
        $('#modal_add_kehadiran').modal('show');    
    }

  
    function simpan()
    {
        $('#btnSave').text('Proses menyimpan...'); //change button text
        $('#btnSave').attr('disabled',true); //set button disable 
        var url;
      
       
            url = "<?php echo site_url('kehadiran/ajax_add')?>";
            var pesan = ' Menambah data';
       
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
                    notif(pesan);
                    // toastr.success('Sukses Menambahkan data');
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

    function ubah()
    {
        $('#btnUbah').text('Proses menyimpan...'); //change button text
        $('#btnUbah').attr('disabled',true); //set button disable 
        var url;
             
        url = "<?php echo site_url('kehadiran/ajax_update')?>";
        var pesan = ' Edit data';

        var formData = new FormData($('#form_edit')[0]);
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
                    notif(pesan);
                    // toastr.success('Sukses Menambahkan data');
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
                $('#btnUbah').text('save'); //change button text
                $('#btnUbah').attr('disabled',false); //set button enable 
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data');
                $('#btnUbah').text('save'); //change button text
                $('#btnUbah').attr('disabled',false); //set button enable 

            }
        }); 
    }

    function edit(id)
    {
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals


        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('kehadiran/ajax_edit_log')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
            $('[name="id_kehadiran"]').val(data.id_kehadiran);
            $('[name="periode"]').val(data.bulan + data.tahun);
            $('[name="penempatan"]').val(data.id_penempatan);
            $('[name="kehadiran"]').val(data.id_kehadi);
            $('[name="jumlah"]').val(data.jumlah_hadir);
            $('[name="tgl"]').val(data.tgl_input);
            $('#modal_kehadiran').modal('show'); // show bootstrap modal when complete loaded
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }

    $(document).ready(function() {
        $('#select_nik').on('change', function() {
            var nik = $(this).val();
            if (nik != '') {
            $.ajax({
                url: '<?php echo base_url(); ?>Potongan_umana/get_penempatan',
                type: 'post',
                data: {nik: nik},
                dataType: 'json',
                success:function(response) {
                $('#id_penempatan').empty();
                $('#id_penempatan').append('<option value="">-- Pilih Tempat --</option>');
                $.each(response,function(index,data){
                    $('#id_penempatan').append('<option value="'+data.id_penempatan+'">'+data.nama_lembaga+ ' ~ ' +data.nama_jabatan+'</option>');
                });
                }
            });
            }
        });
    });

    function reload_table()
    {
        var pesan = ' Refresh tabel';
        table.ajax.reload(null,false); //reload datatable ajax 
        // notif(pesan);
    }

</script>

<div id="modal_add_kehadiran" class="modal fade bd-example-modal-lg" tabindex="-1" aria-hidden="true" style="display: none;">
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
                            <h4 class="card-title">Add Log Kehadiran</h4>
                        </div>
                        <div class="card-body">
                            <div class="basic-form">
                                <div class="col-12">
                                    <label>Periode</label>
                                    <div class="input-group mb-3 input-success-o">
                                        <select name="id_penempatan" id="select_nik" onblur="validate()" class="form-control select2">
                                                <option value=""></option>
                                                <?php 
                                                $data = $this->db->select('*')
                                                ->from('umana')
                                                ->join('penempatan', 'umana.nik = penempatan.nik', 'left')
                                                ->join('lembaga', 'lembaga.id_lembaga = penempatan.id_lembaga', 'left')
                                                ->get()
                                                ->result();
                                            
                                                    foreach($data as $umana){
                                                ?>
                                                <option value="<?php echo $umana->id_penempatan; ?>"><?php echo $umana->nama_lengkap." - ".$umana->nama_lembaga; ?></option>
                                            <?php } ?>
                                            <option value=""></option>
                                        </select>
                                        <input type="hidden" name="nama">
                                    </div>
                                    <label>Id Penempatan</label>
                                    <div class="input-group mb-3 input-success-o">
                                        <select name="id_penempatan" id="id_penempatan" class="form-control">
                                                <option value="<?php echo $item->id; ?>" <?php echo ($item->id == $selected_value) ? 'selected' : ''; ?>>
                                        </select>
                                        <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                </div>
                               <div class="col-12">
                                    <label>Bulan</label>
                                    <div class="input-group mb-3 input-success-o">
                                        <select name="bulan" class="form-control select2">
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
                                    </div>
                                    <label>Tahun</label>
                                    <div class="input-group mb-3 input-success-o">
                                        <select name="bulan" class="form-control select2">
                                            <option ></option>
                                            <option>2023/2024</option>
                                        </select>
                                    </div>
                                    <label>Jumlah Hadir</label>
                                    <div class="input-group mb-3 input-success-o">
                                        <input type="number" name="jumlah" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="btnSimpan" onclick="simpan()">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal_kehadiran" class="modal fade bd-example-modal-lg" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <form action="#" id="form_edit">
                        <div class="card-header">
                            <h4 class="card-title">Edit Log Kehadiran</h4>
                        </div>
                        <div class="card-body">
                            <div class="basic-form">
                                <div class="col-6">
                                    <label>Periode</label>
                                    <div class="input-group mb-3 input-success-o">
                                        <input type="hidden" name="id_kehadiran">
                                        <input type="hidden" name="tgl">
                                        <input type="text" class="form-control" name="periode">
                                    </div>
                                    <label>Id Penempatan</label>
                                    <div class="input-group mb-3 input-success-o">
                                        <input type="text" class="form-control" name="penempatan">
                                    </div>
                                </div>
                               <div class="col-6">
                                    <label>Id Kehadiran Lembaga</label>
                                    <div class="input-group mb-3 input-success-o">
                                        <input type="text" class="form-control" name="kehadiran">
                                    </div>
                                    <label>Jumlah Hadir</label>
                                    <div class="input-group mb-3 input-success-o">
                                        <input type="text" name="jumlah" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="btnUbah" onclick="ubah()">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>