
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" />
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
     $(document).ready(function() {
    <?php if($this->session->flashdata('success')) { ?>
        <?php if($this->session->flashdata('action') == 'add') { ?>
            var backgroundColor = "#33cc33"; // Warna latar belakang hijau untuk menambah data
        <?php } elseif($this->session->flashdata('action') == 'delete') { ?>
            var backgroundColor = "#ff3333"; // Warna latar belakang merah untuk menghapus data
        <?php } ?>
        Toastify({
            text: "<?php echo $this->session->flashdata('success'); ?>",
            duration: 3000,
            close: true,
            gravity: "bottom", // Tampilan toast di bagian bawah
            position: "right", // Tampilan toast di sisi kanan
            backgroundColor: backgroundColor, // Warna latar belakang toast
        }).showToast();
    <?php } ?>
});
   
    var save_method; //for save method string
    var table;

    $(function () {
    $('#nominal').mask('000.000.000.000', {reverse: true});
    
    // Mengambil semua inputan pada form
    const formInputs = document.querySelectorAll('input, select');

    
    table =   $('#tabel_view').DataTable({
        "ajax": {
            "url": "<?php echo site_url('Tunjangan_bk/data_list')?>",
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

    function validate() {
        var inputs = [
            { name: "nominal", error: "Nominal harus diisi." },
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

    

    function add_tbk() {
        const divTertentu = document.getElementById('edit');
        edit.style.display = 'none';
        $('#form')[0].reset(); // reset form on modals
        save_method = 'add';
        $('.select2').select2({
            // minimumInputLength: 2,
        placeholder: 'Select an option',
        allowClear: true,
        dropdownParent: $('#modal_tbk')
        });
        $('#modal_tbk').modal('show');
    }

    $(document).ready(function() {
    $('#select_nik').on('change', function() {
        var nik = $(this).val();
        if (nik != '') {
        $.ajax({
            url: '<?php echo base_url(); ?>Tunjangan_bk/get_penempatan',
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

    function save()
    {
        $('#btnSave').text('Proses menyimpan...'); //change button text
        $('#btnSave').attr('disabled',true); //set button disable 
        var url;
        if(save_method == 'add') {
            url = "<?php echo site_url('Tunjangan_bk/ajax_add')?>";
            var pesan = ' Menambah data';
        } else {
            url = "<?php echo site_url('Tunjangan_bk/ajax_update')?>";
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
                    $('#modal_tbk').modal('hide');
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

    function edit_tbk(id)
    {
         const divTertentu = document.getElementById('tambah');
        tambah.style.display = 'none';
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals
        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('Tunjangan_bk/ajax_edit')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
            $('[name="id_tbk"]').val(data.id_tbk);
            $('#nama').html(data.nama_lengkap);
            $('#lembaga').html(data.nama_lembaga);
            $('[name="id_penempatan2"]').val(data.nama_lembaga);
            $('[name="jenis_tbk"]').val(data.jenis_tbk);
            $('[name="nominal"]').val(data.nominal_tbk);
            $('[name="min"]').val(data.min_periode);
            $('[name="max"]').val(data.max_periode);
            $('#modal_tbk').modal('show'); // show bootstrap modal when complete loaded
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


<!-- Modal -->
<div id="modal_tbk" class="modal fade bd-example-modal-lg" tabindex="-1" aria-hidden="true" style="display: none;">
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
                              <h4 class="card-title">Form Tunjangan Beban Kerja</h4>
                          </div>
                          <div class="card-body">
                              <div class="basic-form row ">
                                  <div id="tambah" class="row">
                                        <div class="col-6">
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
                                                    <option value="<?php echo $umana->nik; ?>"><?php echo $umana->gelar_depan.' '.$umana->nama_lengkap.' '.$umana->gelar_depan; ?></option>
                                                <?php } ?>
                                                <option value=""></option>
                                            </select>
                                                <span class="help-block text-danger" style="color:red"></span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label>Lembaga Pengabdian</label>
                                            <div class="input-group mb-3 input-success-o">
                                                <select name="id_penempatan" id="id_penempatan" class="form-control">
                                                    <option value="<?php echo $item->id; ?>" <?php echo ($item->id == $selected_value) ? 'selected' : ''; ?>>
                                                </select>
                                                <span class="help-block text-danger" style="color:red"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="edit">
                                        <div class="col-6">
                                            <input type="hidden" name="id_tbk">
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
                                        <label>Jenis TBK &nbsp;</label> <small id="nominalHelp" class="help-block text-danger">   </small>
                                        <div class="input-group mb-3 input-success-o">
                                          <!-- <input type="text" name="nominal"  id="nominal" > -->
                                          <select name="jenis_tbk" onblur="validate()" class="form-control">
                                          <option value=""></option>
                                          <option>TBK Jumlah Siswa</option>
                                          <option>TBK Mengajar</option>
                                          <option>TBK Walikelas</option>
                                          <option>TBK Jml Prodi</option>
                                          <option>TBK Keahlian</option>
                                          <option>TBK Resiko/Keahlian</option>
                                          <option>TBK Wilayah Kerja</option>
                                          <option>TBK Lain-lain</option>
                                        </select>
                                        </div>
                                    </div>
                                  <div class="col-6">
                                      <label>Nominal &nbsp;</label> <small id="nominalHelp" class="help-block text-danger">   </small>
                                      <div class="input-group mb-3 input-success-o">
                                          <input type="text" name="nominal" class="form-control" id="nominal" onblur="validate()">
                                          <!-- <span class="help-block text-danger" style="color:red"></span> -->
                                          
                                      </div>
                                  </div>
                                    <div class="col-3">
                                      <label>Masa Aktif Mulai dari </label><small id="minHelp" class="help-block text-danger">  </small>
                                      <div class="input-group mb-3 input-success-o">
                                          <input type="date" name="min" id="min" class="form-control" onblur="validate()">
                                          <span class="help-block text-danger" style="color:red"></span>
                                      </div>
                                    </div>
                                    <div class="col-3">
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