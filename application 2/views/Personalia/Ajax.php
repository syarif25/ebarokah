<script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
    <script src="<?php echo base_url() ?>assets/vendor/chart.js/Chart.bundle.min.js"></script>
	<!-- Apex Chart -->
	<script src="<?php echo base_url() ?>assets/vendor/apexchart/apexchart.js"></script>
	
    <!-- Datatable -->
    <script src="<?php echo base_url() ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/plugins-init/datatables.init.js"></script>

	<script src="<?php echo base_url() ?>assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>

    <script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
	<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>

    <script>
   
    var save_method; //for save method string
    var table;


    $(function () {
      // $("#example1").DataTable();
    table =   $('#tabel_view').DataTable({
        "ajax": {
            "url": "<?php echo site_url('personalia/data_list')?>",
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
        scrollX: true,
        fixedHeader: true, // Aktifkan tetapkan header
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
    table_item2 =   $('#tabel_potongan').DataTable({ 
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": false,
            "info": true,
            "destroy": true,
            "deferRender": true
        });

    });

    

    function edit_lampiran() {
         $('#form')[0].reset(); // reset form on modals
         save_method = 'add';
        $('#modal_lampiran').modal('show');    
    }

    function detail_tbk(id)
    {
        $('#form')[0].reset(); // reset form on modals
        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('tunjangan_bk/get_detail_tbk')?>/" + id,
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

    function detail_potongan(id)
    {
        $('#form_potongan')[0].reset(); // reset form on modals
        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('potongan_umana/get_detail_potongan')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
            
            $('#jumlah_potongan').html(data.data_jumlah.jumlah);
            table_item2.clear();
            table_item2.rows.add($(data.html_item)).draw();
            $('#modal_detail_potongan').modal('show'); // show bootstrap modal when complete loaded
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }

    function simpan_lampiran()
    {
        $('#btnSave').text('Proses menyimpan...'); //change button text
        $('#btnSave').attr('disabled',true); //set button disable 
        var url;
        url = "<?php echo site_url('umana/simpan_lampiran')?>";
        var pesan = ' Ubah Lampiran';
        
        var formData = new FormData($('#form_lampiran')[0]);
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
                    $('#modal_lampiran').modal('hide');
                    notif(pesan);
                    
                    // Tampilkan toast notifikasi setelah halaman direload
                    setTimeout(function() {
                        toastr.success('Data berhasil tersimpan.', 'Sukses');
                        // window.location.href = '<?php echo site_url('umana/profil'); ?>';
                    }, 3000); // Tunda selama 3 detik sebelum menampilkan toast
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

    function reload_table()
    {
        table.ajax.reload(null,false); //reload datatable ajax 
    }

</script>

<div id="modal_detail_potongan" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary">Detail Potongan Umana</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <form action="#" id="form_potongan">
                        <div class="card-body">
                            <div class="row">
                                <div class="row">
                                    <div class="col-4">
                                        <h4>Jumlah</h4>
                                    </div>
                                    <div class="col-8"><span>: </span> <span id="jumlah_potongan"></span>
                                    </div>
                                </div>
                                <div class=" row">
                                    <table class="table table-hover table-striped" id="tabel_potongan">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Jenis Potongan</th>
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

<div id="modal_detail_tbk" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary">Detail Tunjangan Umana</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <form action="#" id="form">
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

<div id="modal_lampiran" class="modal fade bd-example-modal-lg" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <form action="#" id="form_lampiran">
                        <div class="card-header">
                            <h4 class="card-title">Form Lampiran</h4> <span class="text-success fs-20">(Format lampiran JPG, JPEG, atau PDF)</span>
                        </div>
                        <div class="card-body">
                            <div class="basic-form row">
                                <div class="col-6">
                                    <label>KTP</label> <code>Kosongkan jika tidak ingin merubah</code>
                                    <input type="hidden" name="nik" class="" >
                                    <div class="input-group mb-3 input-success-o">
                                        <input type="file" name="file_ktp" class="form-control" accept=".pdf,.jpg,.jpeg">
                                        <span class="help-block text-danger" style="color:red"></span>
                                    </div>
<!--                                     
                                    <label>SK Guru</label> <code>Kosongkan jika tidak ingin merubah</code>
                                    <div class="input-group mb-3 input-success-o">
                                        <input type="file" name="sk_guru" class="form-control" accept=".pdf,.jpg,.jpeg">
                                        <span class="help-block text-danger" style="color:red"></span>
                                    </div> -->
                                    <label>Jumlah Anak</label> 
                                    <div class="input-group mb-3 input-success-o">
                                        <input type="number" name="jumlah_anak" class="form-control">
                                        <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label>Kartu Keluarga</label> <code>Kosongkan jika tidak ingin merubah</code>
                                    <div class="input-group mb-3 input-success-o">
                                        <input type="file" name="file_kk" class="form-control" accept=".pdf,.jpg,.jpeg">
                                        <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    
                                    <label>SK Dosen</label> <code>Kosongkan jika tidak ingin merubah</code>
                                    <div class="input-group mb-3 input-success-o">
                                        <input type="file" name="sk_dosen" class="form-control" accept=".pdf,.jpg,.jpeg">
                                        <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    <!-- <label>SK Awal Struktural</label> <br> <code>Kosongkan jika tidak ingin merubah</code>
                                    <div class="input-group mb-3 input-success-o">
                                        <input type="file" name="sk_awal" class="form-control" accept=".pdf,.jpg,.jpeg">
                                        <span class="help-block text-danger" style="color:red"></span>
                                    </div>  -->
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="btnSave" onclick="simpan_lampiran()">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>