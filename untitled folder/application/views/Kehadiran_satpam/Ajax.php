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
    var save_method; // for save method string
    var table;

    $(function () {
        table = $('#tabel_view').DataTable({
            "ajax": {
                "url": "<?php echo site_url('kehadiran_satpam/data_list')?>",
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


    });

    function add_blanko() {
         $('#form')[0].reset(); // reset form on modals
         save_method = 'add';
        $('#modal_kehadiran').modal('show');    
    }

    function simpan()
    {
        $('#btnSave').text('Proses menyimpan...'); //change button text
        $('#btnSave').attr('disabled',true); //set button disable 
        var url;
        if(save_method == 'add') {
            url = "<?php echo site_url('kehadiran_satpam/blanko_add')?>";
            var pesan = ' Menambah data';
        } else {
            url = "<?php echo site_url('kehadiran_satpam/blanko_update')?>";
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

    function simpan_kehadiran()
    {
        $('#btnSave').text('Proses menyimpan...'); //change button text
        $('#btnSave').attr('disabled',true); //set button disable 
        var url;
        url = "<?php echo site_url('kehadiran_satpam/ajax_add_kehadiran')?>";
        var pesan = ' Tambah data';
        
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
                    notif(pesan);
                
                $('#btnSave').text('save'); //change button text
                $('#btnSave').attr('disabled',false); //set button enable
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                    // reload_table();
                    notif(pesan);

            }
        }); 
    }

    function reload_table()
    {
        table.ajax.reload(null,false);
    }
</script>

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
                    <form action="#" id="form">
                        <div class="card-header">
                            <h4 class="card-title">Tambah Blanko Input Barokah Satpam</h4>
                        </div>
                        <div class="card-body">
                            <div class="basic-form">
                            <label>Nama Lembaga</label>
                                 <div class="input-group mb-3 input-success-o">
                                 <select name="id_lembaga" class="form-control">
                                    <option value=""></option>
                                    <?php 
                                    $query = $this->db->get_where('lembaga', 'id_lembaga = 58');
                                    
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
                                        <!--<input type="hidden" name="bulan" class="">-->
                                            <div class="input-group mb-3 input-success-o">
                                                <select name="bulan" class="form-control">
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
                                            <select name="tahun" class="form-control">
                                                    <option >2023/2024</option>
                                                    <option >2024/2025</option>
                                            </select>
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>
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