
<!-- Scripts -->
<script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
<script src="<?php echo base_url() ?>assets/vendor/chart.js/Chart.bundle.min.js"></script>
<script src="<?php echo base_url() ?>assets/vendor/apexchart/apexchart.js"></script>
<script src="<?php echo base_url() ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/plugins-init/datatables.init.js"></script>
<script src="<?php echo base_url() ?>assets/js/jquery.mask.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>

<script>
   
    var save_method; //for save method string
    var table;

    $(function () {
        $('#nominal_tambahan').mask('000.000.000.000', {reverse: true});
        // edit.style.display = 'none';
        
        // Mengambil semua inputan pada form
        const formInputs = document.querySelectorAll('input, select');

        //di menu log barokah tambahan
        table =   $('#tabel_view').DataTable({
            "ajax": {
                "url": "<?php echo site_url('barokah_tambahan/data_list')?>",
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
    
        //di menu manajemen pengajar
        table2 =   $('#tabel_view_umana').DataTable({
            "ajax": {
                "url": "<?php echo site_url('barokah_tambahan/data_perumana')?>",
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

    // function reset(){
    $.clearInput = function () {
        $('form').find('input[type=text], input[type=password], input[type=number], input[type=email], textarea').val('');
    };

    function goToRincian(id) {
        var url = 'rincian/' + id; // Menggabungkan parameter id ke URL
        window.location.href = url;
        data_rincian_perumana(id);  
    }

    function validate() {
        var inputs = [
            { name: "nominal_tambahan", error: "Nominal harus diisi." },
            { name: "select_nik", error: "Nama harus diisi." },
            { name: "min", error: "Harus diisi." },
            { name: "max", error: "Harus diisi." }
            
        ];

        for (var i = 0; i < inputs.length; i++) {
            var input = document.getElementById(inputs[i].name);
            var help = document.getElementById(inputs[i].name + "Help");

            if (input.value === "") {
                input.classList.add("is-invalid");
                input.classList.remove("is-valid");
                help.innerText = inputs[i].error;
            } else {
                input.classList.remove("is-invalid");
                input.classList.add("is-valid");
                help.innerText = "";
            }
        }
    }

    function add_barokah() {
        $('#form')[0].reset(); // reset form on modals
        $.clearInput();
        tambah.style.display = 'block';
        edit.style.display = 'none';
        save_method = 'add';
        $('.select2').select2({
            // minimumInputLength: 2,
        placeholder: 'Select an option',
        allowClear: true,
        dropdownParent: $('#modal_barokah')
        });
        $('#modal_barokah').modal('show');
    }

   

    $(document).ready(function() {
        $('#select_nik').on('change', function() {
            var nik = $(this).val();
            if (nik != '') {
            $.ajax({
                url: '<?php echo base_url(); ?>Barokah_tambahan/get_pengajar',
                type: 'post',
                data: {nik: nik},
                dataType: 'json',
                success:function(response) {
                $('#id_pengajar').empty();
                $('#id_pengajar').append('<option value="">-- Pilih Tempat --</option>');
                $.each(response,function(index,data){
                    $('#id_pengajar').append('<option value="'+data.id_pengajar+'">'+data.nama_lembaga+ ' ~ ' +data.id_pengajar+'</option>');
                });
                }
            });
            }
        });
    

    $('#jenis_potongan').change(function(){
            var id=$(this).val();
            $.ajax({
                url : "<?php echo base_url('potongan_umana/get_potongan')?>/" + id,
                method : "POST",
                data : {id: id},
                async : false,
                dataType : 'json',
                success: function(data)
                {
                    $('[name="nominal_tambahan"]').val(data.nominal);
                }
            });
        });

    });

    function edit_potongan(id)
    {
        const divTertentu = document.getElementById('tambah');
        tambah.style.display = 'none';
        edit.style.display = 'block';
        $.clearInput();
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals
        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('barokah_tambahan/ajax_edit')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                $('[name="id"]').val(data.id_barokah_tambahan);
                $('#nama').html(data.nama_lengkap);
                $('#lembaga').html(data.nama_lembaga);
                $('[name="id_pengajar2"]').val(data.id_pengajar);
                $('[name="nama_barokah"]').val(data.nama_barokah);
                $('[name="nominal_tambahan"]').val(data.nominal_tambahan);
                $('[name="min"]').val(data.min_periode_tambahan);
                $('[name="max"]').val(data.max_periode_tambahan);
                $('#modal_barokah').modal('show'); // show bootstrap modal when complete loaded
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
        
    }

    function data_rincian_perumana(id) {
        // var id = 23;
        $('#tabel_view_rincian_umana').DataTable().destroy();
        $('#loadingSpinner').show(); // Menampilkan animasi loading
        table_by_id =   $('#tabel_view_rincian_umana').DataTable({
            "ajax": {
                "url": "<?php echo site_url('barokah_tambahan/data_rincian_perumana/')?>" + id,
                "type": "POST",
                "dataSrc": function (json) {
                // Tangkap total potongan dari respons JSON
                var totalPotongan = json.total_potongan;
                // $('#lembaga').html(data.nama_lembaga);
                // Tampilkan total potongan pada elemen div
                $('#total_potongan').text('Total Potongan : Rp.' + totalPotongan);
                $('#nama_lengkap_detail').text(json.data_umana.nama_lengkap);
                $('#nama_lembaga_detail').text(json.data_umana.nama_lembaga);
                $('#loadingSpinner').hide(); 
                // Kembalikan data untuk DataTable
                return json.data;
                }
            },

            "columnDefs": [
                { 
                    "targets": [ -1 ], //last column
                    "orderable": false, //set not orderable
                },
            
            ],  
            "paging": false,
            "searching": false,
            "ordering": false,
            
            });
            $('#modal_detail_potongan').modal('show');
    }
    

    function save()
    {
        $('#btnSave').text('Proses menyimpan...'); //change button text
        $('#btnSave').attr('disabled',true); //set button disable 
        $('#loadingSpinner').show(); // Menampilkan animasi loading
        var url;
        if(save_method == 'add') {
            url = "<?php echo site_url('barokah_tambahan/ajax_add')?>";
            var pesan = ' Menambah data';
        } else {
            url = "<?php echo site_url('barokah_tambahan/ajax_update')?>";
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
                    if (save_method == 'add'){
                        $('#modal_barokah').modal('hide');
                        reload_table2();
                    } else {
                    $('#modal_barokah').modal('hide');
                    reload_table();
                    reload_table2();
                    reload_table_detail();
                    $('#loadingSpinner').hide(); 
                    }
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

    function reload_table()
    {
        table.ajax.reload(null,false); //reload datatable ajax 
    }

    function reload_table2()
    {
        table2.ajax.reload(null,false); //reload datatable ajax 
    }

    function reload_table_detail()
    {
        var table = $('#modal_detail_potongan').find('#tabel_view_rincian_umana').DataTable();
        table.ajax.reload(null, false); //reload datatable ajax
    }

</script>


<!-- Modal -->
<div id="modal_barokah" class="modal fade bd-example-modal-lg" tabindex="-1" aria-hidden="true" style="display: none;">
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
                              <h4 class="card-title">Form Barokah Tambahan</h4>
                          </div>
                          <div class="card-body">
                              <div class="basic-form row">
                                <div id="tambah" class="row">
                                    <div class="col-12">
                                        <label>Nama Lengkap</label> <small id="select_nikHelp" class="help-block text-danger">  </small>
                                        <div class="input-group mb-3 input-success-o">
                                            <select name="nama" id="select_nik" onblur="validate()" class="form-control select2">
                                                <option value=""></option>
                                                <?php
                                                if($this->session->userdata('jabatan') == 'AdminLembaga'){
                                                    $data = $this->db->query("SELECT * FROM umana, penempatan, lembaga WHERE umana.nik = penempatan.nik AND penempatan.id_lembaga = lembaga.id_lembaga AND lembaga.id_lembaga = '" . $this->session->userdata('lembaga') . "'")->result();
                                                } else {
                                                    $data = $this->db->get('umana')->result();
                                                }
                                                    foreach($data as $umana){
                                                ?>
                                                <option value="<?php echo $umana->nik; ?>"><?php echo $umana->gelar_depan.' '.$umana->nama_lengkap.' '.$umana->gelar_belakang; ?></option>
                                            <?php } ?>
                                            <option value=""></option>
                                        </select>
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label>Lembaga Pengabdian</label>
                                        <div class="input-group mb-3 input-success-o">
                                            <select name="id_pengajar" id="id_pengajar" class="form-control">
                                                <option value="<?php echo $item->id; ?>" <?php echo ($item->id == $selected_value) ? 'selected' : ''; ?>>
                                            </select>
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>
                                    </div>
                                </div>
                                <div id="edit">
                                    <div class="col-6">
                                        <input type="hidden" name="id">
                                        <input type="hidden" name="id_pengajar2">
                                        <label>Nama Lengkap</label> <small id="select_nikHelp" class="help-block text-danger">  </small>
                                        <div class="input-group mb-3 input-success-o">
                                            <h2 id="nama"></h2>
                                        </select>
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label>Lembaga Pengabdian</label>
                                        <h2 id="lembaga"></h2>
                                    </div>
                                </div>

                                  <div class="col-6">
                                        <label>Nama Barokah Tambahan &nbsp;</label> <small id="Help" class="help-block text-danger"></small>
                                        <div class="input-group mb-3 input-success-o">
                                            <input name="nama_barokah" id="nama_barokah"  class="form-control">
                                        </div>
                                  </div>
                                  <div class="col-6">
                                      <label>Nominal &nbsp;</label> <small id="nominal_tambahanHelp" class="help-block text-danger">   </small>
                                      <div class="input-group mb-3 input-success-o">
                                          <input type="text" name="nominal_tambahan" id="nominal_tambahan" onblur="validate()" class="form-control">
                                      </div>
                                  </div>
                                    <div class="col-6">
                                      <label>Masa Aktif Mulai dari </label><small id="minHelp" class="help-block text-danger">  </small>
                                      <div class="input-group mb- input-success-o">
                                          <input type="date" name="min" id="min" class="form-control" onblur="validate()">
                                          <span class="help-block text-danger" style="color:red"></span>
                                      </div>
                                    </div>
                                    <div class="col-6">
                                      <label>Sampai </label> <small id="maxHelp" class="help-block text-danger">  </small>
                                      <div class="input-group mb-3 input-success-o">
                                          <input type="date" name="max" id="max" class="form-control" onblur="validate()" >
                                          <span class="help-block text-danger" style="color:red"></span>
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

<div id="modal_detail_potongan" class="modal fade bd-example-modal-lg" tabindex="-1" aria-hidden="true" style="display: none;">
      <div class="modal-dialog modal-lg" >
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
                              <h4 class="card-title">Form Detail Potongan Barokah</h4> <span id="total_potongan"></span>
                          </div>
                          <div class="card-body">
                          <style>
                                #modal_detail_potongan {
                                z-index: 1000;
                                }
                            
                                #modal_barokah{
                                z-index: 2000;
                                }
                            </style>
                              <div class="basic-form row">
                                <div id="">
                                    <div class="col-6">
                                        <input type="hidden" name="id_detail">
                                        <input type="hidden" name="id_pengajar_detail">
                                        <label>Nama Lengkap</label> <small id="select_nikHelp" class="help-block text-danger">  </small>
                                        <div class="input-group mb-3 input-success-o">
                                            <h2 id="nama_lengkap_detail"></h2>
                                        </select>
                                            <span class="help-block text-danger" style="color:red"></span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label>Lembaga Pengabdian</label>
                                        <h2 id="nama_lembaga_detail"></h2>
                                    </div>
                                </div>
                                </div>
                                <table id="tabel_view_rincian_umana" class="table-hover" width="100%" >
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Jenis Potongan</th>
                                            <th>Nominal</th>
                                            <th>Periode</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                    
                                </table>
                                <div id="loadingSpinner" class="table-loading-overlay" style="display: none;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                          </div>
                          <div class="modal-footer">
                              <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                              <!-- <button type="button" class="btn btn-primary" id="btnSave" onclick="save()">Simpan</button> -->
                          </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
  .table-loading-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 9999;
    background-color: rgba(255, 255, 255, 0.7);
    padding: 10px;
    border-radius: 4px;
  }
</style>