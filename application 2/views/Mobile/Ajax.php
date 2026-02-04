<script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>
<!-- <script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script> -->
<script src="<?php echo base_url() ?>assets/vendor/chart.js/Chart.bundle.min.js"></script>

<script src="<?php echo base_url() ?>assets/vendor/apexchart/apexchart.js"></script>
<script src="<?php echo base_url() ?>assets/vendor/chart.js/Chart.bundle.min.js"></script>
<script src="<?php echo base_url() ?>assets/vendor/peity/jquery.peity.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/dashboard/dashboard-1.js"></script>

<script src="<?php echo base_url() ?>assets/vendor/owl-carousel/owl.carousel.js"></script>
 <script>
        loadData();
        var idleTimeout = 900000; // Waktu tunggu dalam milidetik (misalnya, 60 detik)

        var idleTimer = null;

        function resetIdleTimer() {
            clearTimeout(idleTimer);
            idleTimer = setTimeout(logout, idleTimeout);
        }

        function logout() {
            window.location.href = '<?php echo base_url("login/logout"); ?>'; // Ganti dengan URL logout Anda
        }

        // Panggil resetIdleTimer() saat pengguna berinteraksi dengan halaman
        document.addEventListener('mousemove', resetIdleTimer);
        document.addEventListener('keydown', resetIdleTimer);

    </script>


<script>
   
   var save_method; //for save method string
   var table;
   
   function edit_pwd(id)
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
            $('[name="nik"]').val(data.nik);
            $('#modal_password').modal('show'); // show bootstrap modal when complete loaded
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
        url = "<?php echo site_url('umana/ganti_password')?>";
        var pesan = ' Ubah Password';
        
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
                    $('#modal_password').modal('hide');
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

   
   function detail_barokah(id) {
        //  $('#form')[0].reset(); // reset form on modals
        //  save_method = 'add';
        // $('#modal_detail').modal('show');    
        navigator.vibrate(60);
         //Ajax Load data from ajax
         $.ajax({
            url : "<?php echo site_url('laporan/get_detail_barokah_umana/')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                 $('#tunjab').html(data.data.tunjab);
                $('#hadir').html(data.data.kehadiran);
                $('#nominal_kehadiran').html(data.data.nominal_kehadiran);
                $('#tunkel').html(data.data.tunkel);
                $('#tunj_anak').html(data.data.tunj_anak);
                $('#tmp').html(data.data.tmp);
                $('#tbk').html(data.data.tbk);
                $('#kehormatan').html(data.data.kehormatan);
                $('#potongan').html(data.data.potongan);
                $('#diterima').html(data.data.diterima);

                $('#dataContainer').empty(); // Menghapus konten sebelumnya di dalam elemen dataContainer

                $.each(data.list, function(index, value) {
                    var div = '<div><small>  ~ ' + value.nama_potongan + ' :</small> ' + value.nominal + '</div>';
                    // div += '<div><strong>Nominal:</strong> ' + value.nominal + '</div>';
                    $('#dataContainer').append(div);
                });
            $('#modal_detail').modal('show'); // show bootstrap modal when complete loaded
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }
    
     function detail_barokah_pengajar(id) {
        //  $('#form')[0].reset(); // reset form on modals
        //  save_method = 'add';
        // $('#modal_detail').modal('show');    
navigator.vibrate(60);
         //Ajax Load data from ajax
         $.ajax({
            url : "<?php echo site_url('laporan/get_detail_barokah_pengajar/')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                $('#sks').html(data.data.jumlah_sks);
                $('#rank').html(data.data.rank);
                $('#mengajar').html(data.data.mengajar);
                $('#dty').html(data.data.dty);
                $('#jafung').html(data.data.jafung);
                $('#kehadiran').html(data.data.jumlah_hadir);
                $('#nominal_kehadir').html(data.data.nominal_kehadiran);
                $('#kehadiran_15').html(data.data.jumlah_hadir_15);
                $('#nominal_kehadir_15').html(data.data.nominal_kehadiran_15);
                $('#kehadiran_10').html(data.data.jumlah_hadir_10);
                $('#nominal_kehadir_10').html(data.data.nominal_kehadiran_10);
                $('#tungkel').html(data.data.tunkel);
                $('#tun_anak').html(data.data.tun_anak);
                 $('#bpiket').html(data.data.barokah_piket);
                $('#btambahan').html(data.data.khusus);
                $('#walkes').html(data.data.walkes);
                $('#bkehormatan').html(data.data.kehormatan);
                $('#bpotongan').html(data.data.potongan);
                $('#bditerima').html(data.data.diterima);

                $('#dataContainer2').empty(); // Menghapus konten sebelumnya di dalam elemen dataContainer2

                $.each(data.list, function(index, value) {
                    var div = '<div><small>  ~ ' + value.nama_potongan + ' :</small> ' + value.nominal + '</div>';
                    // div += '<div><strong>Nominal:</strong> ' + value.nominal + '</div>';
                    $('#dataContainer2').append(div);
                });
            $('#modal_detail_pengajar').modal('show'); // show bootstrap modal when complete loaded
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }
    
    function loadData() {
        $.ajax({
            url: '<?php echo base_url("Laporan/get_kehadiran_data"); ?>', // Ganti your_controller dengan nama controller Anda
            type: 'GET',
            success: function(data) {
                data = JSON.parse(data); // Konversi data ke dalam format JSON jika perlu
                displayData(data);
            },
            error: function() {
                alert('Terjadi kesalahan saat memuat data.');
            }
            
        });
    }

    function displayData(data) {
        // Bersihkan container sebelum menambahkan data baru
        $('#dataContainer_list').html('');

        // Loop melalui data dan tambahkan ke dalam container
        data.forEach(function(item) {
            var html = '<div class="col-xl-12">';
            html += '<div class="row">';
            html += '<div class="col-12">';
            html += '<div class="card draggable-handle draggable" tabindex="0">';
            html += '<div class="card-body">';
            html += '<div class="d-flex justify-content-between mb-2">';
            html += '<span class="sub-title">';
            html += '<svg class="me-2" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">';
            html += '<circle cx="5" cy="5" r="5" fill="#09BD3C"/>';
            html += '</svg>';
            html += 'Sukses';
            html += '</span>';
            html += '</div>';
            html += '<div class="row">';
            html += '<h4 class="fs-18 font-w500 mt-2">' + item.nama_lembaga + '</h4>';
            html += '<h3 class="text-secondary">' + item.nama_jabatan + '</h3>';
            html += '<p class="mb-0 fs-16">' + item.bulan + ', ' + item.tahun + '</p>';
            html += '</div>';
            html += '<br>';
            html += '<div class="row justify-content-between align-items-center kanban-user">';
            html += '<ul class="users col-6">';
            html += '<a href="#" onclick="detail_barokah(' + item.id_total_barokah + ')" class="btn btn-outline-primary btn-rounded mb-2"><i class="fas fa-file-invoice"></i> Rincian</a>';
            html += '</ul>';
            html += '<div class="col-6 d-flex justify-content-end ml-3">';
            html += '<span>Rp. &nbsp;</span><h3 class="">' + item.diterima + '</h3>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            $('#dataContainer_list').append(html);
        });
    }
    
    function perbaikan() {
        $('#modal_perbaikan').modal('show');    
    }

</script>

<div id="modal_perbaikan" class="modal fade bd-example-modal-lg" tabindex="-1" aria-hidden="true" style="display: none;">
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
                            <h4 class="card-title"></h4>
                        </div>
                        <h1>Mohon Maaf, Sedang dalam Perbaikan</h1>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal_detail" class="modal fade bd-example-modal-lg" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="card-title text-success"><i class="fas fa-file-invoice"></i> Rincian Barokah</span>
                                                <!-- <span class="badge badge-primary badge-pill">3</span> -->
                                            </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <!-- <div class="card"> -->
                    <form action="#" id="form">
                        <div class="row">
                            <!-- <div class="col-xl-8"> -->
                                <!-- <div class="card"> -->
                                    <div class="card-body">
                                        <div class="col-lg-12 order-lg-2 mb-1">
                                            
                                            <ul class="list-group mb-3">
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">Barokah Pokok </h6>
                                                        <!-- <small class="text-muted">Brief description</small> -->
                                                    </div>
                                                    <span class="text-muted" id="tunjab">900.000</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">Kehadiran</h6>
                                                        <small class="text-muted">Hadir <strong id="hadir"> </strong> Kali</small>
                                                    </div>
                                                    <span class="text-muted" id="nominal_kehadiran"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">Barokah Keluarga</h6>
                                                        <!-- <small class="text-muted">Brief description</small> -->
                                                    </div>
                                                    <span class="text-muted" id="tunkel"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">Tunjangan Anak</h6>
                                                        <!-- <small class="text-muted">Brief description</small> -->
                                                    </div>
                                                    <span class="text-muted" id="tunj_anak"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">Barokah Masa Pengabdian</h6>
                                                        <!-- <small class="text-muted">Brief description</small> -->
                                                    </div>
                                                    <span class="text-muted" id="tmp"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">Barokah Beban Kerja</h6>
                                                        <!-- <small class="text-muted">Brief description</small> -->
                                                    </div>
                                                    <span class="text-muted" id="tbk"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">Barokah Kehormatan</h6>
                                                        <!-- <small class="text-muted">Brief description</small> -->
                                                    </div>
                                                    <span class="text-muted" id="kehormatan"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                <div>
                                                    <h6 class="my-0">Potongan</h6>
                                                    <div id="dataContainer"> </div>
                                                    <!-- <small class="text-muted">Brief description</small> -->
                                                </div>
                                                <span class="text-muted" id="potongan"></span>
                                            </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <h5>Total (Rp)</h5>
                                                    <strong id="diterima"></strong>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                <!-- </div> -->
                            <!-- </div> -->
                            <!-- <div class="col-xl-4">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <div class="profile-blog">
                                                    <h5 class="text-primary d-inline">Detail Umana</h5><br>
                                                    <img src="<?php echo base_url(); ?>assets/1.jpeg" alt="" class="rounded " style="height: 100px;">
                                                    <h4><a href="post-details.html" class="text-black"><?php echo $periode->gelar_depan.' '.$periode->nama_lengkap.' '.$periode->gelar_belakang ?></a></h4>
                                                    <p class="mb-0">Jabatan &nbsp; : <strong> <?php echo $periode->nama_jabatan ?></strong></p>
                                                    <p class="mb-0">Lembaga : <strong> <?php echo $periode->nama_lembaga ?></strong></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </form>
                <!-- </div> -->
            </div>
        </div>
    </div>
</div>

<div id="modal_detail_pengajar" class="modal fade bd-example-modal-lg" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="d-flex justify-content-between align-items-center mb-0">
                                                <span class="card-title text-success"><i class="fas fa-file-invoice"></i> Rincian Barokah</span>
                                                <!-- <span class="badge badge-primary badge-pill">3</span> -->
                                            </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <!-- <div class="card"> -->
                    <form action="#" id="form">
                        <div class="row">
                            <!-- <div class="col-xl-8"> -->
                                <!-- <div class="card"> -->
                                    <div class="card-body">
                                        <div class="col-lg-12 order-lg-2 mb-1">
                                            
                                            <ul class="list-group mb-0">
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">Mengajar </h6>
                                                        <small class="text-muted"> <strong id="sks"> </strong> SKS/JAM x <strong id="rank"> </strong></small>
                                                    </div>
                                                    <span class="text-muted" id="mengajar"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">GTY/DTY</h6>
                                                        <!-- <small class="text-muted">Brief description</small> -->
                                                    </div>
                                                    <span class="text-muted" id="dty"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">Jafung</h6>
                                                        <!-- <small class="text-muted">Brief description</small> -->
                                                    </div>
                                                    <span class="text-muted" id="jafung"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">Kehadiran Normal</h6>
                                                        <small class="text-muted">Hadir <strong id="kehadiran"> </strong> Kali</small>
                                                    </div>
                                                    <span class="text-muted" id="nominal_kehadir"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">Kehadiran 15.000</h6>
                                                        <small class="text-muted">Hadir <strong id="kehadiran_15"> </strong> x 15.000</small>
                                                    </div>
                                                    <span class="text-muted" id="nominal_kehadir_15"></span> 
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">Kehadiran 10.000</h6>
                                                        <small class="text-muted">Hadir <strong id="kehadiran_10"> </strong> x 10.000</small>
                                                    </div>
                                                   <span class="text-muted" id="nominal_kehadir_10"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">Barokah Keluarga</h6>
                                                        <!-- <small class="text-muted">Brief description</small> -->
                                                    </div>
                                                    <span class="text-muted" id="tungkel"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">Tunjangan Anak</h6>
                                                        <!-- <small class="text-muted">Brief description</small> -->
                                                    </div>
                                                    <span class="text-muted" id="tun_anak"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">Barokah Wali Kelas</h6>
                                                        <!-- <small class="text-muted">Brief description</small> -->
                                                    </div>
                                                    <span class="text-muted" id="walkes"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">Barokah Kehormatan</h6>
                                                        <!-- <small class="text-muted">Brief description</small> -->
                                                    </div>
                                                    <span class="text-muted" id="bkehormatan"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">Barokah Tambahan</h6>
                                                        <!-- <small class="text-muted">Brief description</small> -->
                                                    </div>
                                                    <span class="text-muted" id="btambahan"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">Barokah Piket</h6>
                                                         <small class="text-muted"></small> 
                                                    </div>
                                                    <span class="text-muted" id="bpiket"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                <div>
                                                    <h6 class="my-0">Potongan</h6>
                                                    <div id="dataContainer2"> </div>
                                                    <!-- <small class="text-muted">Brief description</small> -->
                                                </div>
                                                <span class="text-muted" id="bpotongan"></span>
                                            </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <h5>Total (Rp)</h5>
                                                    <strong id="bditerima"></strong>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                <!-- </div> -->
                            <!-- </div> -->
                            <!-- <div class="col-xl-4">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <div class="profile-blog">
                                                    <h5 class="text-primary d-inline">Detail Umana</h5><br>
                                                    <img src="<?php echo base_url(); ?>assets/1.jpeg" alt="" class="rounded " style="height: 100px;">
                                                    <h4><a href="post-details.html" class="text-black"><?php echo $periode->gelar_depan.' '.$periode->nama_lengkap.' '.$periode->gelar_belakang ?></a></h4>
                                                    <p class="mb-0">Jabatan &nbsp; : <strong> <?php echo $periode->nama_jabatan ?></strong></p>
                                                    <p class="mb-0">Lembaga : <strong> <?php echo $periode->nama_lembaga ?></strong></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </form>
                <!-- </div> -->
            </div>
        </div>
    </div>
</div>

<div id="modal_password" class="modal fade bd-example-modal-lg" tabindex="-1" aria-hidden="true" style="display: none;">
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
                            <h4 class="card-title">Ganti Password</h4>
                        </div>
                        <div class="card-body">
                            <div class="basic-form">
                                <label>Password Baru &nbsp;&nbsp;&nbsp;</label>
                                <div class="input-group mb-3 input-success-o">
                                    <input type="hidden" name="nik">
                                    <input type="text" name="password" class="form-control">
                                    <span class="help-block text-danger" style="color:red"></span>
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