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

    <script>
   
    var save_method; //for save method string
    var table;


    $(function () {
    $('#inputku').mask('000.000.000.000', {reverse: true});

      // $("#example1").DataTable();
    table =   $('#tabel_view').DataTable({
        "ajax": {
            "url": "<?php echo site_url('log_kehadiran_lemb/data_list')?>",
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

    function add_tunkel() {
         $('#form')[0].reset(); // reset form on modals
         save_method = 'add';
        $('#modal_log').modal('show');    
    }

    function save()
    {
        $('#btnSave').text('Proses menyimpan...'); //change button text
        $('#btnSave').attr('disabled',true); //set button disable 
        var url;
        if(save_method == 'add') {
            url = "<?php echo site_url('log_kehadiran_lemb/ajax_add')?>";
            var pesan = ' Menambah data';
        } else {
            url = "<?php echo site_url('log_kehadiran_lemb/ajax_update')?>";
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
                    $('#modal_log').modal('hide');
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

    function edit_kehadiran(id)
    {
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals


        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('log_kehadiran_lemb/ajax_edit')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
            $('[name="id_kehadiran_lembaga"]').val(data.id_kehadiran_lembaga);
            $('[name="id_lembaga"]').val(data.id_lembaga);
            $('[name="bulan"]').val(data.bulan);
            $('[name="tahun"]').val(data.tahun);
            $('[name="jumlah_total"]').val(data.jumlah_total);
            $('[name="status"]').val(data.status);
            
            $('#modal_log').modal('show'); // show bootstrap modal when complete loaded
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


<div id="modal_log" class="modal fade bd-example-modal-lg" tabindex="-1" aria-hidden="true" style="display: none;">
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
                            <h4 class="card-title">Form Total Barokah</h4>
                        </div>
                        <div class="card-body">
                            <div class="basic-form">
                                <div class="row">
                                    <div class="col-12">
                                        <label>Periode</label>
                                            <input type="hidden" name="id_kehadiran_lembaga" class="">
                                        <div class="input-group mb-3 input-success-o">
                                            <input type="text" name="bulan" class="form-control">
                                            <input type="text" name="tahun" class="form-control">
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>
                                        
                                        <label>Id Lembaga</label>
                                        <div class="input-group mb-3 input-success-o">
                                            <input type="text" name="id_lembaga" class="form-control">
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>

                                        <label>Jumlah</label>
                                        <div class="input-group mb-3 input-success-o">
                                            <input type="text" name="jumlah_total" class="form-control">
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>
                                        
                                        <label>Status</label>
                                        <div class="input-group mb-3 input-success-o">
                                            <select name="status" class="form-control">
                                                <option value=""></option>
                                                <option>Belum</option>
                                                <option>selesai</option>
                                                <option>Belum</option>
                                                <option>acc</option>
                                                <option>Sudah</option>
                                            </select>
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