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
            "url": "<?php echo site_url('log_barokah/data_list_pengajar')?>",
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
            url = "<?php echo site_url('log_barokah/ajax_add')?>";
            var pesan = ' Menambah data';
        } else {
            url = "<?php echo site_url('log_barokah/ajax_update')?>";
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

    function edit_potongan(id)
    {
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals


        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('log_barokah/ajax_edit')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
            $('[name="id_total_barokah"]').val(data.id_total_barokah);
            $('[name="id_penempatan"]').val(data.id_penempatan);
            $('[name="bulan"]').val(data.bulan);
            $('[name="tahun"]').val(data.tahun);
            $('[name="tunjab"]').val(data.tunjab);
            $('[name="mp"]').val(data.mp);
            $('[name="kehadiran"]').val(data.kehadiran);
            $('[name="nominal"]').val(data.nominal_kehadiran);
            $('[name="tunkel"]').val(data.tunkel);
            $('[name="tunjanak"]').val(data.tunj_anak);
            $('[name="tmp"]').val(data.tmp);
            $('[name="kehormatan"]').val(data.kehormatan);
            $('[name="tbk"]').val(data.tbk);
            $('[name="potongan"]').val(data.potongan);
            $('[name="diterima"]').val(data.diterima);
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
                                    <div class="col-6">
                                        <label>Periode</label>
                                            <input type="hidden" name="id_total_barokah" class="">
                                        <div class="input-group mb-3 input-success-o">
                                            <input type="text" name="bulan" class="form-control">
                                            <input type="text" name="tahun" class="form-control">
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>
                                        
                                        <label>Tunjab</label>
                                        <div class="input-group mb-3 input-success-o">
                                            <input type="text" name="tunjab" class="form-control">
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>

                                        <label>MP</label>
                                        <div class="input-group mb-3 input-success-o">
                                            <input type="text" name="mp" class="form-control">
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>
                                        
                                        <label>Kehadiran</label>
                                        <div class="input-group mb-3 input-success-o">
                                            <input type="text" name="kehadiran" class="form-control">
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>

                                        <label>Nominal</label>
                                        <div class="input-group mb-3 input-success-o">
                                            <input type="text" name="nominal" class="form-control">
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>

                                        <label>Tunkel</label>
                                        <div class="input-group mb-3 input-success-o">
                                            <input type="text" name="tunkel" class="form-control">
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>

                                        <label>Tunj Anak</label>
                                        <div class="input-group mb-3 input-success-o">
                                            <input type="text" name="tunjanak" class="form-control">
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>

                                    </div>
                                    <div class="col-6">
                                       
                                        <label>TMP</label>
                                        <div class="input-group mb-3 input-success-o">
                                            <input type="text" name="tmp" class="form-control">
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>

                                        <label>Kehormatan</label>
                                        <div class="input-group mb-3 input-success-o">
                                            <input type="text" name="kehormatan" class="form-control">
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>

                                        <label>TBK</label>
                                        <div class="input-group mb-3 input-success-o">
                                            <input type="text" name="tbk" class="form-control">
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>

                                        <label>Potongan</label>
                                        <div class="input-group mb-3 input-success-o">
                                            <input type="text" name="potongan" class="form-control">
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>

                                        <label>Diterima</label>
                                        <div class="input-group mb-3 input-success-o">
                                            <input type="text" name="diterima" class="form-control">
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>

                                        <label>Status</label>
                                        <div class="input-group mb-3 input-success-o">
                                            <input type="text" name="status" class="form-control">
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