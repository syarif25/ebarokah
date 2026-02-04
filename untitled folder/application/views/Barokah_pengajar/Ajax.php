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
    $(document).ready(function() {
        Toastify({
            text: "<?php echo $this->session->flashdata('success'); ?>",
            duration: 3000,
            close: true,
            gravity: "top", // Tampilan toast di bagian bawah
            position: "right", // Tampilan toast di sisi kanan
            backgroundColor: "#33cc33", // Warna latar belakang toast
        }).showToast();
    });
   
    var save_method; //for save method string
    var table;


    $(function () {
    $('#inputku').mask('000.000.000.000', {reverse: true});

      // $("#example1").DataTable();
    table =   $('#tabel_view').DataTable({
        "ajax": {
            "url": "<?php echo site_url('barokah_pengajar/data_list')?>",
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

    function add_ketentuan() {
         $('#form')[0].reset(); // reset form on modals
         save_method = 'add';
        $('#modal_ketentuan').modal('show');    
    }

    function save()
    {
        $('#btnSave').text('Proses menyimpan...'); //change button text
        $('#btnSave').attr('disabled',true); //set button disable 
        var url;
        if(save_method == 'add') {
            url = "<?php echo site_url('barokah_pengajar/ajax_add')?>";
            var pesan = ' Menambah data';
        } else {
            url = "<?php echo site_url('barokah_pengajar/ajax_update')?>";
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
                    $('#modal_ketentuan').modal('hide');
                    reload_table();
                    notif(pesan);
                }
                else
                {
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

    function edit_barokah(id)
    {
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals


        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('barokah_pengajar/ajax_edit')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
            $('[name="id_barokah_pengajar"]').val(data.id_barokah_pengajar);
            $('[name="ijazah"]').val(data.ijazah);
            $('[name="kategori"]').val(data.kategori);
            $('[name="nominal"]').val(data.nominal);
            $('[name="min_tmp_mengajar"]').val(data.min_tmp_mengajar);
            $('[name="max_tmp_mengajar"]').val(data.max_tmp_mengajar);
            $('#modal_ketentuan').modal('show'); // show bootstrap modal when complete loaded
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }

    function reload_table()
    {
        table.ajax.reload(null,false); //reload datatable ajax 
    }

</script>


<div id="modal_ketentuan" class="modal fade bd-example-modal-lg" tabindex="-1" aria-hidden="true" style="display: none;">
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
                            <h4 class="card-title">Form Ketentuan Barokah</h4>
                        </div>
                        <div class="card-body">
                            <div class="basic-form">
                            <label>Kategori</label>
                                <div class="input-group mb-3 input-success-o">
                                    <select name="kategori" class="form-control">
                                        <option value=""></option>
                                        <option >Dosen UNIB</option>
                                        <option >Dosen Pasca</option>
                                        <option >Dosen MAIF</option>
                                        <option >Dosen FIK</option>
                                        <option >Guru</option>
                                    </select>
                                    <span class="help-block text-danger" style="color:red"></span>
                                </div>
                                
                                <div class="row">
                                    <div class="col-4">
                                        <label>Gol</label>
                                        <input type="hidden" name="id_barokah_pengajar" class="">
                                        <div class="input-group mb-3 input-success-o">
                                            <input type="text" name="golongan" class="form-control">
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <label>Min TMP</label>
                                        <div class="input-group mb-3 input-success-o">
                                            <input type="number" name="min_tmp_mengajar" class="form-control">
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <label>Max TMP</label>
                                        <div class="input-group mb-3 input-success-o">
                                            <input type="number" name="max_tmp_mengajar" class="form-control">
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <label>Ijazah</label>
                                        <div class="input-group mb-3 input-success-o">
                                            <select name="ijazah" class="form-control" name="" id="">
                                                <option value=""></option>
                                                <option>SLTA/D1</option>
                                                <option>D2/D3</option>
                                                <option>S1</option>
                                                <option>S2</option>
                                                <option>S3</option>
                                            </select>
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label>Barokah</label>
                                        <div class="input-group mb-3 input-success-o">
                                            <input type="text" name="nominal" class="form-control" id="inputku">
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="btnSave" onclick="save()">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>