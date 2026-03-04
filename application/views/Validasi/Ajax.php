<script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
    <!-- <script src="<?php echo base_url() ?>assets/vendor/chart.js/Chart.bundle.min.js"></script> -->
	<!-- Apex Chart -->
	<!-- <script src="<?php echo base_url() ?>assets/vendor/apexchart/apexchart.js"></script> -->
	
    <!-- Datatable -->
    <script src="<?php echo base_url() ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/plugins-init/datatables.init.js"></script>

	<!-- <script src="<?php echo base_url() ?>assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script> -->

    <script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
	<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>

    <script>
   
    var save_method; //for save method string
    var table;


    $(function () {
        table =   $('#tabel_view').DataTable({
            "ajax": {
                "url": "<?php echo site_url('validasi/data_list')?>",
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
            scrollY:        true,
            // scrollX:        false,
        });

         table_item =   $('#tabel2').DataTable({ 
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": false,
            "info": true,
            "destroy": true,
            "deferRender": true
        });
    });

    function detail_umana(id)
    {
        $('#form')[0].reset(); // reset form on modals
        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('validasi/ajax_edit')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
            // $('[name="id_tbk"]').val(data.id_tbk);
            $('#nama').html(data.nama_lengkap);
            $('#tunkel').html(data.tunj_kel);
            $('#kehormatan').html(data.kehormatan);
             $('[name="jumlah_hadir"]').val(data.jumlah_hadir);
            $('[name="id_kehadiran"]').val(data.id_kehadiran);
             $('#modal_detail').modal('show'); // show bootstrap modal when complete loaded
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }
    
     function detail_pengajar(id)
    {
        $('#form')[0].reset(); // reset form on modals
        save_method = 'pengajar';
        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('validasi/ajax_edit_pengajar')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
            // $('[name="id_tbk"]').val(data.id_tbk);
            $('#nama').html(data.nama_lengkap);
            $('#tunkel').html(data.tunj_kel);
            $('#kehormatan').html(data.kehormatan);
            $('[name="jumlah_hadir"]').val(data.jumlah_hadir);
            $('[name="jumlah_hadir_15"]').val(data.jumlah_hadir_15);
            $('[name="jumlah_hadir_10"]').val(data.jumlah_hadir_10);
            $('[name="jumlah_hadir_piket"]').val(data.jumlah_hadir_piket);
            $('[name="id_kehadiran"]').val(data.id_kehadiran_pengajar);
             $('#modal_detail').modal('show'); // show bootstrap modal when complete loaded
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }
    
    function detail_tbk(id)
    {
        $('#form')[0].reset(); // reset form on modals
        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('Tunjangan_bk/get_detail_tbk')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
            
            $('#jumlah_tbk').html(data.data_jumlah.jumlah);
            $('#kehormatan').html(data.data_jumlah.kehormatan);
            table_item.clear();
            table_item.rows.add($(data.html_item)).draw();
            $('#modal_detail_tbk').modal('show'); // show bootstrap modal when complete loaded
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }

    function ubah_kehadiran()
    {
        $('#btnubah').text('Proses menyimpan...'); //change button text
        $('#btnubah').attr('disabled',true); //set button disable 
        var url;
        if(save_method == 'pengajar') {
            url = "<?php echo site_url('validasi/ajax_update_pengajar')?>";
            var pesan = 'Merubah Jumlah Kehadiran';
        } else {
            url = "<?php echo site_url('validasi/ajax_update')?>";
            var pesan = ' Merubah Jumlah Kehadiran';
        }
        
        var formData = new FormData($('#form_ubah')[0]);
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
                    $('#modal_detail').modal('hide');
                    location.reload();
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
                $('#btnubah').text('save'); //change button text
                $('#btnubah').attr('disabled',false); //set button enable 
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data');
                $('#btnubah').text('save'); //change button text
                $('#btnubah').attr('disabled',false); //set button enable 

            }
        }); 
    }

    function jumlah(id)
    {
        $.ajax({
            url : "<?php echo site_url('validasi/koreksi')?>/" + id,
            type: "POST",
            dataType: "JSON",
            success: function(data)
            {
                $('[name="id_kehadiran"]').val(id);
                table_item.clear();
                table_item.rows.add($(data.data)).draw();
                $('#modal_barokah').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Test'); // Set title to Bootstrap modal title
            },
            error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
     });
    }

    function save()
    {
        $('#btnSave').text('Proses menyimpan...'); //change button text
        $('#btnSave').attr('disabled',true); //set button disable 
        var url;
            url = "<?php echo site_url('validasi/save_data')?>";
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
                    // $('#modal_barokah').modal('hide');
                    // window.location = "<?php echo site_url('validasi')?>";
                    // reload_table();
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
                 window.location = "<?php echo site_url('validasi')?>";
                 notif(pesan);
                //   alert('Error adding / update data');
                $('#btnSave').text('save'); //change button text
                $('#btnSave').attr('disabled',false); //set button enable 

            }
        }); 
    }
    
    function simpan_pengajar()
    {
        $('#btnSave').text('Proses menyimpan...'); //change button text
        $('#btnSave').attr('disabled',true); //set button disable 
        var url;
            url = "<?php echo site_url('validasi/save_data_pengajar')?>";
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
                    // $('#modal_barokah').modal('hide');
                    // window.location = "<?php echo site_url('validasi')?>";
                    // reload_table();
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
                 window.location = "<?php echo site_url('validasi')?>";
                 notif(pesan);
                 reload_table();
                //   alert('Error adding / update data');
                $('#btnSave').text('save'); //change button text
                $('#btnSave').attr('disabled',false); //set button enable 

            }
        }); 
    }

    function kirim()
        {
            $('#btnSave').text('Proses menyimpan...'); //change button text
            $('#btnSave').attr('disabled',true); //set button disable 
            var url;
            url = "<?php echo site_url('kehadiran/update_kirim')?>";
            var pesan = ' Edit data';
            
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
                        window.location = "<?php echo site_url('kehadiran')?>";
                        // reload_table();
                        notif(pesan);
                    
                    $('#btnSave').text('save'); //change button text
                    $('#btnSave').attr('disabled',false); //set button enable
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    window.location = "<?php echo site_url('kehadiran')?>";
                        // reload_table();
                        notif(pesan);
    
                }
            }); 
        }
    
    function reload_table()
    {
        table.ajax.reload(null,false); //reload datatable ajax 
    }

</script>


<div id="modal_barokah" class="modal fade bd-example-modal-xl" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
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
                            <h4 class="card-title">Jumlah Barokah Kantor DIKTI</h4> 
                            <a href="<?php echo base_url() ?>assets/kehadiran.pdf" target="_blank" class="btn btn-secondary me-1 px-3"><i class="fa fa-file m-0"></i> Lihat File Absensi</a>
                        </div>
                        <div class="card-body">
                            <input type="text" name="id_kehadiran">
                           <div class="row">
                            <table id="tabel_koreksi" class="table table-bordered table-striped verticle-middle table-responsive-sm">
                                <thead>
                                    <tr >
                                        <th>No</th>
                                        <th>Nama Lengkap</th>
                                        <th>Jabatan</th>
                                        <th>TMT</th>
                                        <th>Tunjab</th>
                                        <th>MP</th>
                                        <th colspan="2" class="text-center">Kehadiran</th>
                                        <th>Tunkel</th>
                                        <th>TMP</th>
                                        <th>Diterima</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                           </div>
                           <form action="#" id="form">
                            <!-- <?php foreach ($list as $data) {?>
                            <input type="text" name="nik_umana[]" value="<?php echo $data->nama_lengkap ?>">
                            <input type="text" name="nik_umana[]" value="<?php echo $data->nama_lengkap ?>">
                            <?php } ?> -->
                           </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Kembali</button>
                            <button type="button" class="btn btn-primary" id="btnSave" onclick=""><span class="btn-icon-start text-primary"><i class="fa fa-check"></i>
                                </span>Setujui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="modal_detail" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary">Detail Umana</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <form action="#" id="form_ubah">
                        <div class="card-body">
                            <div class="row">
                                <div class="row">
                                    <div class="col-4">
                                        <h5 class="f-w-500">Nama  
                                        </h5>
                                    </div>
                                    <div class="col-8"><span>: </span><span id="nama"> </span>
                                    </div>
                                </div>
                                <div class=" row">
                                    <div class=" col-4">
                                        <h5 class="f-w-500">Tunkel</h5>
                                    </div>
                                    <div class=" col-8"><span>:</span> <span id="tunkel"></span>
                                    </div>
                                </div>
                                <div class=" row">
                                    <div class=" col-4">
                                        <h5 class="f-w-500">Kehormatan</h5>
                                    </div>
                                    <div class=" col-8"><span>: </span><span id="kehormatan"></span>
                                    </div>
                                </div>
                                
                                <div class=" row">
                                    <div class=" col-4">
                                        <h5 class="f-w-500">
                                        </h5>
                                    </div>
                                    <div class=" col-8"><code>* Kosongkan jika tidak ada perubahan</code>
                                    </div>
                                </div>
                                <div class=" row">
                                    <div class=" col-4">
                                        <h5 class="f-w-500">Revisi Kehadiran
                                        </h5>
                                    </div>
                                    <div class=" col-8 input-info-o">
                                        <input type="hidden" name="id_kehadiran" class="form-control" name="kehadiran">
                                        <input type="number" class="form-control" name="jumlah_hadir">
                                    </div>
                                </div>
                                <div class=" row">
                                    <div class=" col-4">
                                        <h5 class="f-w-500">Revisi Kehadiran 15.000
                                        </h5>
                                    </div>
                                    <div class=" col-8 input-info-o">
                                        <input type="number" class="form-control" name="jumlah_hadir_15">
                                    </div>
                                </div>
                                <div class=" row">
                                    <div class=" col-4">
                                        <h5 class="f-w-500">Revisi Kehadiran 10.000
                                        </h5>
                                    </div>
                                    <div class=" col-8 input-info-o">
                                        <input type="number" class="form-control" name="jumlah_hadir_10">
                                    </div>
                                </div>
                                <div class=" row">
                                    <div class=" col-4">
                                        <h5 class="f-w-500">Revisi Kehadiran Piket
                                        </h5>
                                    </div>
                                    <div class=" col-8 input-info-o">
                                        <input type="number" class="form-control" name="jumlah_hadir_piket">
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Kembali</button>
                            <button type="submit" class="btn btn-primary" id="btnubah" onclick="ubah_kehadiran()"><span class="btn-icon-start text-primary"><i class="fa fa-check"></i>
                                </span>Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal_detail_tbk" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary">Detail TBK Umana</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <form action="#" id="form_ubah">
                        <!-- <div class="card-header">
                            <h4 class="card-title text-primary">Detail Umana</h4> 
                            <a href="<?php echo base_url() ?>assets/kehadiran.pdf" target="_blank" class="btn btn-secondary me-1 px-3"><i class="fa fa-file m-0"></i> Lihat File Absensi</a>
                        </div> -->
                        <div class="card-body">
                            <div class="row">
                                <div class="row">
                                    <div class="col-4">
                                        <h4>Jumlah</h4>
                                    </div>
                                    <div class="col-8"><span>: </span> <span id="jumlah_tbk"></span>
                                    </div>
                                </div>
                                <div class=" row">
                                    <table class="table table-hover table-striped" id="tabel2">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Jenis TBK</th>
                                                <th>Nominal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                           
                                        </tbody>
                                    </table>
                                </div>
                                
                                
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Kembali</button>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>