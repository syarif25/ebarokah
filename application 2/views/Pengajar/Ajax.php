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

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    var save_method; //for save method string
    var table;

    $(document).ready(function() {
    $('.potongan').select2();
    });

    $(function () {
        $(".nik").select2();
        $(".trans").select2();
        $(".rank").select2();

      // $("#example1").DataTable();
    table =   $('#tabel_view').DataTable({
        "ajax": {
            "url": "<?php echo site_url('penempatan/data_list_pengajar')?>",
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
        "iDisplayLength": 10,
            "aLengthMenu": [
                [10, 25, 50, 200, 500, -1],
                [10, 25, 50, 200, 500, "All"]
            ],
        });
    });

    function add_ketentuan() {
         $('#form')[0].reset(); // reset form on modals
         save_method = 'add';
        $('#modal_penempatan').modal('show');    
    }

    $('.nik').change(function(){
        var id=$(this).val();
        $.ajax({
            url : "<?php echo base_url('Penempatan/get_umana')?>/" + id,
            method : "POST",
            data : {id: id},
            async : false,
            dataType : 'json',
            success: function(data)
            {
                $('[name="niy"]').val(data.niy);
                $('[name="nama_lengkap"]').val(data.gelar_depan+" "+data.nama_lengkap+", "+data.gelar_belakang);
                $('[name="ijazah_terakhir"]').val(data.ijazah_terakhir);
                $('[name="tmt_guru"]').val(data.tmt_guru);
                $('[name="tmt_dosen"]').val(data.tmt_dosen);
            }
        });
    });

    function save()
    {
        $('#btnSave').text('Proses menyimpan...'); //change button text
        $('#btnSave').attr('disabled',true); //set button disable 
        var url;
        url = "<?php echo site_url('penempatan/pengajar_add')?>";
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
                if(data.status) //if success close modal and reload ajax table
                {
                    // $('#modal_penempatan').modal('hide');
                    window.location = "<?php echo site_url('penempatan/pengajar')?>";
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
                alert('Error adding / update data');
                $('#btnSave').text('save'); //change button text
                $('#btnSave').attr('disabled',false); //set button enable 

            }
        }); 
    }

    function simpan()
    {
        $('#btnSave').text('Proses menyimpan...'); //change button text
        $('#btnSave').attr('disabled',true); //set button disable 
        var url;
        url = "<?php echo site_url('penempatan/ajax_update_pengajar')?>";
        var pesan = ' Merubah data';
       
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
                    $('#modal_penempatan').modal('hide');
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

    function edit_penempatan(id)
    {
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals


        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('penempatan/ajax_edit_pengajar')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
            $('#id').html(data.id_pengajar);
            $('[name="id_pengajar"]').val(data.id_pengajar);
            $('[name="nik"]').val(data.nik);
            $('[name="niy"]').val(data.niy);
            $('[name="nama_lengkap"]').val(data.nama_lengkap);
            $('[name="ijazah_terakhir"]').val(data.ijazah_terakhir);
            $('[name="tmt_guru"]').val(data.tmt_guru);
            $('[name="tmt_dosen"]').val(data.tmt_dosen);
            $('[name="kategori"]').val(data.kategori);
            $('[name="jumlah_sks"]').val(data.jumlah_sks);
            $('[name="id_ketentuan"]').val(data.id_ketentuan);
            $('[name="tmt"]').val(data.tmt_struktural);
            $('[name="lembaga"]').val(data.id_lembaga);
            $('[name="tunkel"]').val(data.tunj_kel);
            $('[name="tunj_anak"]').val(data.tunj_anak);
            $('[name="transport"]').val(data.kategori_trans);
            $('[name="kehormatan"]').val(data.kehormatan);
            $('[name="walikelas"]').val(data.walkes);
            $('[name="jafung"]').val(data.jafung);
            $('[name="tgl_mulai"]').val(data.tgl_mulai);
            $('[name="tgl_selesai"]').val(data.tgl_selesai);
            $('[name="status"]').val(data.status);
            $('#modal_penempatan').modal('show'); // show bootstrap modal when complete loaded
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


<div id="modal_penempatan" class="modal fade bd-example-modal-lg" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Penempatan Struktural</h5> <small id="id"></small>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <form id="form">
                        <div class="row">
                            <div class="mb-3 col-sm-4 input-success-o">
                                <label class="form-label">NIK</label>
                                <!-- <input type="text" class="form-control" name="nik"> -->
                                <select id="" class="form-control" name="nik">
                                    <option value=""></option>
                                    <?php 
                                    $query = $this->db->get('umana');
                                    if ($query->num_rows() > 0) {
                                    foreach ($query->result() as $row) {?>
                                    <option value="<?php echo $row->nik;?>"><?php echo $row->nik.' '.$row->nama_lengkap;?></option>
                                    <?php }
                                    } ?>
                                </select>
                                <input type="hidden" name="id_pengajar">
                            </div>
                            <div class="mb-3 col-sm-4 input-success-o">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" name="nama_lengkap" readonly>
                            </div>
                            <div class="mb-1 col-sm-2 input-info-o">
                                <label class="form-label">Ijazah Terakhir</label>
                                <input type="text" class="form-control" name="ijazah_terakhir" readonly>
                            </div>
                            <div class="mb-2 col-sm-2 input-info-o">
                                <label class="form-label">TMT Guru</label>
                                <input type="text" class="form-control" name="tmt_guru" readonly>
                            </div>
                            <div class="mb-2 col-sm-2 input-info-o">
                                <label class="form-label">TMT Dosen</label>
                                <input type="text" class="form-control" name="tmt_dosen" readonly>
                            </div>
                            <div class="mb-2 col-sm-2 input-success-o">
                                <label class="form-label">Kategori</label>
                                <!-- <input type="text" class="form-control" name="nik"> -->
                                <select id="" class="form-control" name="kategori">
                                    <option value=""></option>
                                    <option value="GTY">Guru Tetap</option>
                                    <option value="GTT">Guru Tidak Tetap</option>
                                    <option value="DTY">Dosen Tetap</option>
                                    <option value="DTT">Dosen Tidak Tetap</option>
                                </select>
                                <span class="help-block text-danger" style="color:red"></span>
                            </div>
                            <div class="mb-2 col-sm-2 input-success-o">
                                <label class="form-label">Jumlah SKS/JAM</label>
                                <input type="number" class="form-control" name="jumlah_sks">
                            </div>
                            <div class="mb-4 col-sm-4 input-success-o">
                                <label class="form-label">Lembaga</label>
                                <!-- <input type="text" class="form-control" name="potongan"> -->
                                <select  class="form-control" name="lembaga">
                                    <option value=""></option>
                                    <?php 
                                        $this->db->where('id_bidang', 'bidang dikti');
                                        $this->db->or_where('id_bidang', 'bidang dikjar');
                                        $this->db->or_where('id_bidang', 'bidang dikjar-M');
                                        $this->db->order_by('nama_lembaga', 'ASC'); 
                                        $query = $this->db->get('lembaga');

                                        if ($query->num_rows() > 0) {
                                            foreach ($query->result() as $row) {?>
                                                <option value="<?php echo $row->id_lembaga;?>"><?php echo $row->nama_lembaga;?></option>
                                            <?php }
                                        }
                                        ?>
                                </select>
                                <span class="help-block text-danger" style="color:red"></span>
                            </div>
                            <div class="mb-3 col-sm-2 input-success-o">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="tgl_mulai">
                            </div>
                            <div class="mb-3 col-sm-2 input-success-o">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" name="tgl_selesai">
                            </div>
                            <div class="mb-3 col-sm-3 input-success-o">
                                <label class="form-label">Tunkel </label>
                                <select name="tunkel" id="" class="form-control">
                                    <option value=""></option>
                                    <option value="Ya">Ya</option>
                                    <option value="Tidak">Tidak</option>
                                </select>
                                <span class="help-block text-danger" style="color:red"></span>
                            </div>
                            <div class="mb-3 col-sm-3 input-success-o">
                                <label class="form-label">Tunj Anak </label>
                                <select name="tunj_anak" id="" class="form-control">
                                    <option value=""></option>
                                    <option value="Ya">Ya</option>
                                    <option value="Tidak">Tidak</option>
                                </select>
                                <span class="help-block text-danger" style="color:red"></span>
                            </div>
                            <div class="mb-3 col-sm-3 input-success-o">
                                <label class="form-label">Kehormatan</label>
                                <!-- <input type="text" class="form-control" name="potongan"> -->
                                <select  class="form-control" name="kehormatan">
                                   <option>Ya</option>
                                   <option>Tidak</option>
                                </select>
                            </div>
                            <div class="mb-3 col-sm-3 input-success-o">
                                <label class="form-label">Wali Kelas</label>
                                <!-- <input type="text" class="form-control" name="potongan"> -->
                                <select  class="form-control" name="walikelas">
                                   <option>Ya</option>
                                   <option>Tidak</option>
                                    <option value="walkes_sklh">Wali Kelas Sekolah</option>
                                </select>
                            </div>
                            <div class="mb-3 col-sm-3 input-success-o">
                                <label class="form-label">Jafung </label>
                                <select name="jafung" id="" class="form-control">
                                    <option value=""></option>
                                    <option value="Ya">Ya</option>
                                    <option value="Tidak">Tidak</option>
                                </select>
                                <span class="help-block text-danger" style="color:red"></span>
                            </div>
                            <div class="mb-3 col-sm-3 input-success-o">
                                <label class="form-label">Status</label>
                                <!-- <input type="text" class="form-control" name="potongan"> -->
                                <select  class="form-control" name="status">
                                   <option>Aktif</option>
                                   <option>Tidak Aktif</option>
                                   <option>Cuti 50%</option>
                                    <option>Cuti 100%</option>
                                </select>
                            </div>
                            <div class="mb-5 col-sm-5 input-success-o">
                                <label class="form-label">Transport/Kehadiran </label>
                                <select id="trans" class="form-control trans" name="transport">
                                    <option value=""></option>
                                    <?php 
                                    $this->db->where('kategori_transport !=', 'struktural');
                                    $this->db->order_by('kategori_transport', 'ASC'); 
                                    $query = $this->db->get('transport');
                                    
                                    if ($query->num_rows() > 0) {
                                        foreach ($query->result() as $row) {
                                            // Format nominal_transport menjadi Rupiah
                                            $nominal_rupiah = number_format($row->nominal_transport, 0, ',', '.');
                                            ?>
                                            <option value="<?php echo $row->id_transport;?>">
                                                <b><?php echo $row->kategori_transport;?> | </b>
                                                <?php echo $row->nama_transport." | Rp ".$nominal_rupiah;?>
                                            </option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>

                                <span class="help-block text-danger" style="color:red"></span>
                            </div>
                            <!--<div class="mb-3 col-sm-9 input-success-o">-->
                            <!--</div>-->
                            <div class="mb-3 col-sm-3 input-success-o">
                                <button type="button" class="btn btn-primary" id="btnSave" onclick="simpan()">Simpan</button>   
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
