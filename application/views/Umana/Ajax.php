<script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
    <script src="<?php echo base_url() ?>assets/vendor/chart.js/Chart.bundle.min.js"></script>
	<!-- Apex Chart -->
	<script src="<?php echo base_url() ?>assets/vendor/apexchart/apexchart.js"></script>
	
    <!-- Datatable -->
    <script src="<?php echo base_url() ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/plugins-init/datatables.init.js"></script>

	<script src="<?php echo base_url() ?>assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/jquery.mask.js"></script>
    <script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
	<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>
	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
   
    var save_method; //for save method string
    var table;


    $(function () {
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
                gravity: "Top", // Tampilan toast di bagian bawah
                position: "right", // Tampilan toast di sisi kanan
                backgroundColor: backgroundColor, // Warna latar belakang toast
            }).showToast();
        <?php } ?>
    });

      // $("#example1").DataTable();
      $('.tmt').mask('0000', {reverse: true});
    table =   $('#tabel_view').DataTable({
        "ajax": {
            "url": "<?php echo site_url('umana/data_list')?>",
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

    function add_umana() {
         $('#form')[0].reset(); // reset form on modals
         save_method = 'add';
        $('#modal_umana').modal('show');    
    }

    function impor() {
         $('#form')[0].reset(); // reset form on modals
         save_method = 'add';
        $('#modal_import').modal('show');    
    }

    function save()
    {
        $('#btnSave').text('Proses menyimpan...'); //change button text
        $('#btnSave').attr('disabled',true); //set button disable 
        var url;
        if(save_method == 'add') {
            url = "<?php echo site_url('umana/ajax_add')?>";
            var pesan = ' Menambah data';
        } else {
            url = "<?php echo site_url('umana/ajax_update')?>";
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
                    $('#modal_umana').modal('hide');
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

    function edit_umana(id)
    {
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals


        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('umana/ajax_edit')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
            $('[name="nik1"]').val(data.nik);
            $('[name="nik"]').val(data.nik);
            $('[name="niy"]').val(data.niy);
            $('[name="nidn"]').val(data.nidn);
            $('[name="nama_lengkap"]').val(data.nama_lengkap);
            $('[name="gelar_depan"]').val(data.gelar_depan);
            $('[name="gelar_belakang"]').val(data.gelar_belakang);
            $('[name="jk"]').val(data.jk);
            $('[name="bidang_keahlian"]').val(data.bidang_keahlian);
            $('[name="no_rekening"]').val(data.no_rekening);
            $('[name="atas_nama"]').val(data.atas_nama);
            $('[name="nama_bank"]').val(data.nama_bank);
            $('[name="tgl_lahir"]').val(data.tgl_lahir);
            $('[name="nomor_hp"]').val(data.nomor_hp);
            $('[name="alamat_domisili"]').val(data.alamat_domisili);
            $('[name="tmt_struktural"]').val(data.tmt_struktural);
            $('[name="tmt_guru"]').val(data.tmt_guru);
            $('[name="tmt_dosen"]').val(data.tmt_dosen);
            $('[name="tmt_maif"]').val(data.tmt_maif);
            $('[name="jabatan_akademik"]').val(data.jabatan_akademik);
            $('[name="ijazah_terakhir"]').val(data.ijazah_terakhir);
            $('[name="status_sertifikasi"]').val(data.status_sertifikasi);
            $('[name="status_nikah"]').val(data.status_nikah);
            $('[name="status_aktif"]').val(data.status_aktif);
            $('[name="ikatan_khidmah"]').val(data.ikatan_khidmah);
            $('#modal_umana').modal('show'); // show bootstrap modal when complete loaded
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


<div id="modal_umana" class="modal fade bd-example-modal-xl" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Form Data Diri Umana'</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form id="form">
                                <div class="row">
                                <div class="mb-3 col-sm-12 input-success-o">
                                        <code>* </code> <label class="form-label">Wajib diisi </label> 
                                    </div>
                                    <div class="mb-3 col-sm-6 input-success-o">
                                        <label class="form-label">NIK </label> <code>*</code>
                                        <input type="text" class="form-control" name="nik">
                                        <input type="hidden" name="nik1">
                                        <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    <div class="mb-3 col-sm-6 input-success-o">
                                        <label class="form-label">Nomor Induk P2S3 </label> <code>*</code>
                                        <input type="text" class="form-control" name="niy">
                                        <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    <div class="mb-3 col-sm-6 input-success-o">
                                        <label class="form-label">Nama Lengkap </label> <code>*</code>
                                        <input type="text" class="form-control" name="nama_lengkap">
                                        <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    <div class="mb-3 col-sm-6 input-success-o">
                                        <label class="form-label">Gelar Depan </label>
                                        <input type="text" class="form-control" name="gelar_depan">
                                        <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    <div class="mb-3 col-sm-6 input-success-o">
                                        <label class="form-label">Gelar Belakang </label>
                                        <input type="text" class="form-control" name="gelar_belakang">
                                        <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    <div class="mb-3 col-sm-6 input-success-o">
                                        <label class="form-label">Jenis Kelamin </label> <code>*</code>
                                        <select name="jk" class="form-control">
                                            <option value=""></option>
                                            <option >Laki-laki</option>
                                            <option >Perempuan</option>
                                        </select>
                                     <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    <div class="mb-3 col-sm-6 input-success-o">
                                        <label class="form-label">Tanggal Lahir </label> <code>*</code>
                                        <input type="date" class="form-control" name="tgl_lahir">
                                     <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    <div class="mb-3 col-sm-6 input-success-o">
                                        <label class="form-label">Bidang Keahlian</label>
                                        <input type="text" class="form-control" name="bidang_keahlian">
                                     <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    <div class="mb-3 col-sm-6 input-success-o">
                                        <label class="form-label">Alamat Domisili </label> <code>*</code>
                                        <input type="text" class="form-control" name="alamat_domisili">
                                     <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    <div class="mb-3 col-sm-6 input-success-o">
                                        <label class="form-label">Nomor Hp</label>
                                        <input type="text" class="form-control" name="nomor_hp">
                                     <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    <div class="mb-3 col-sm-6 input-success-o">
                                        <label class="form-label">TMT Struktural</label>
                                        <input type="date" class="form-control" name="tmt_struktural">
                                     <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    <div class="mb-3 col-sm-6 input-success-o">
                                        <label class="form-label">TMT Guru</label>
                                        <input type="date" class="form-control" name="tmt_guru">
                                     <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    <div class="mb-3 col-sm-6 input-success-o">
                                        <label class="form-label">TMT Dosen</label>
                                        <input type="date" class="form-control" name="tmt_dosen">
                                     <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                     <div class="mb-3 col-sm-6 input-success-o">
                                        <label class="form-label">TMT Maif</label>
                                        <input type="date" class="form-control" name="tmt_maif">
                                     <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    <div class="mb-3 col-sm-6 input-success-o">
                                        <label class="form-label">Ijazah Terakhir</label> <code>*</code>
                                        <select name="ijazah_terakhir" class="form-control">
                                            <option value=""></option>
                                            <option >SD</option>
                                            <option >SMP</option>
                                            <option >SLTA/D1</option>
                                            <option >SMK</option>
                                            <option >D2/D3</option>
                                            <option >S1</option>
                                            <option >S2</option>
                                            <option >S3</option>
                                        </select>
                                     <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    <div class="mb-3 col-sm-6 input-info-o">
                                        <label class="form-label">NUPTK / NIDN</label>
                                        <input type="text" class="form-control" name="nidn">
                                     <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    <div class="mb-3 col-sm-6 input-info-o">
                                        <label class="form-label">Jabatan Akademik</label>
                                        <select name="jabatan_akademik" class="form-control">
                                            <option value=""></option>
                                            <?php $data = $this->db->get('barokah_jafung')->result();
                                                    foreach($data as $jafung){
                                                ?>
                                                <option value="<?php echo $jafung->id_barokah_jafung; ?>"><?php echo $jafung->nama_jafung; ?></option>
                                            <?php } ?>
                                        </select>
                                     <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    <div class="mb-3 col-sm-6 input-info-o">
                                        <label class="form-label">Status Sertifikasi</label>
                                        <select name="status_sertifikasi" class="form-control">
                                            <option value=""></option>
                                            <option >Sudah</option>
                                            <option >Belum </option>
                                        </select>
                                     <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    <div class="mb-3 col-sm-6 input-success-o">
                                        <label class="form-label">Status Nikah</label> <code>*</code>
                                        <select name="status_nikah" class="form-control">
                                            <option value=""></option>
                                            <option >Menikah</option>
                                            <option >Belum Menikah</option>
                                        </select>
                                     <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    <div class="mb-3 col-sm-6 input-success-o">
                                        <label class="form-label">Nomor Rekening </label> <code>*</code>
                                        <input type="text" class="form-control" name="no_rekening">
                                     <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                     <div class="mb-3 col-sm-6 input-success-o">
                                        <label class="form-label">Atas Nama </label> <code>*</code>
                                        <input type="text" class="form-control" name="atas_nama">
                                     <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    <div class="mb-3 col-sm-6 input-success-o">
                                        <label class="form-label">Nama Bank </label> <code>*</code>
                                        <select name="nama_bank" class="form-control">
                                            <option value=""></option>
                                            <option >Bank Rakyat Indonesia</option>
                                            <option >Bank Syari'ah Indonesia</option>
                                            <option >Bank Mandiri</option>
                                            <option >Bank Jatim</option> 
                                            <option >Bank BNI</option>
                                            <option >Bank BCA</option>
                                            <option>Bank CIMB</option>
                                            <option>Bank Muamalat</opyion>
                                            <option>Bank BTN</option>
                                            <!--<option>Bank </option>-->
                                        </select>
                                     <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    <div class="mb-3 col-sm-6 input-success-o">
                                        <label class="form-label">Ikatan Khidmah </label> <code>*</code>
                                        <select name="ikatan_khidmah" class="form-control">
                                            <option value=""></option>
                                            <option >Pegawai Tetap </option>
                                            <option >Dosen Tetap </option>
                                            <option >Guru Tetap </option>
                                            <option >Magang</option>
                                        </select>
                                     <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    <div class="mb-3 col-sm-6 input-success-o">
                                        <label class="form-label">Password </label> <code>*</code>
                                        <input name="password" class="form-control">
                                    </div>
                                    <div class="mb-3 col-sm-6 input-success-o">
                                        <label class="form-label">Status </label> <code>*</code>
                                        <select name="status_aktif" class="form-control">
                                            <option value=""></option>
                                            <option >Aktif</option>
                                            <option > Tidak Aktif</option>
                                            <option>Cuti 50%</option>
                                            <option>Cuti 100%</option>
                                        </select>
                                    </div>
                                    
                                </div>
                                <button type="submit" id="btnSave" onclick="save()" class="btn btn-primary">Simpan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal_import" class="modal fade bd-example-modal-xl" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Import Data Umana'</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                        <form method="POST" action="<?= site_url('umana/import') ?>" enctype="multipart/form-data">
                                <div class="row">
                                    <div class=" col-sm-6 ">
                                        <label class="form-label">File Umana</label>
                                        <input type="file" class="form-control" name="file" accept=".xls, .xlsx">
                                        <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                   
                                </div>
                                <button type="submit" onclick="importExcel()" class="btn btn-primary">Simpan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

