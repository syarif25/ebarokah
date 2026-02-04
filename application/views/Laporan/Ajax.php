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
  loadData();
    var save_method; //for save method string
    var table;


    $(function () {
    $('#inputku').mask('000.000.000.000', {reverse: true});

      // $("#example1").DataTable();
    table =   $('#tabel_view').DataTable({
        "ajax": {
            "url": "<?php echo site_url('laporan/data_list')?>",
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

        table =   $('#tabel_view_umana').DataTable({
        "ajax": {
            "url": "<?php echo site_url('laporan/data_list_umana')?>",
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

    
    function rincian(id) {
        $.ajax({
            url: "<?php echo base_url('laporan/get_data_by_id/'); ?>" + id,
            method: "GET",
            dataType: "json",
            success: function(response) {
                
                // Looping data dan menambahkan baris tabel
            $.each(response.data, function(index, value){
                var year = new Date(value.tmt_struktural).getFullYear();
                var nomorurut = 1;
               
                // mengambil nilai angka dari response AJAX
                var angka = response.data;

                // konversi angka ke format rupiah dengan pemisah ribuan
                var format_rupiah = angka.toLocaleString('id-ID', { style: 'decimal', minimumFractionDigits: 0, maximumFractionDigits: 0, useGrouping: true });

                // menampilkan nilai yang sudah diubah ke format rupiah
                console.log(format_rupiah);

                var row = '<tr>' +
                            '<td>' + nomorurut++ + '</td>' +
                            '<td>' + value.nama_lengkap + '</td>' +
                            '<td>' + value.nama_jabatan + '</td>' +
                            '<td>' + year + '</td>' +
                            '<td>' + value.barokah + '</td>' +
                            '<td>' + value.mp + '</td>' +
                            '<td>' + value.kehadiran + '</td>' +
                            '<td>' + value.nominal_kehadiran + '</td>' +
                            '<td>' + value.tunkel + '</td>' +
                            '<td>' + value.tmp + '</td>' +
                            '<td>' + value.diterima + '</td>' +
                          '</tr>';
                    // nomorurut++;
                    $('#jumlah').text(value.jumlah_total);
                $('#data_table').append(row);
                });
            }

            });
        $('#modal_rincian').modal('show');    
    }
    
    function detail_barokah(id) {
         //Ajax Load data from ajax
         $.ajax({
            url : "<?php echo site_url('laporan/get_detail_barokah_umana/')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
                $('#tunjab').html(data.data.tunjab);
                $('#hadir').html(data.data.jml_hadir);
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
    
        function formatRibuan(angka) {
    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

    function displayData(data) {
        // Bersihkan container sebelum menambahkan data baru
        $('#dataContainer_list').html('');
        // Menggunakan fungsi formatRibuan untuk menambahkan titik pada angka
var angka = 1000000; // Contoh angka
var angkaDiformat = formatRibuan(angka);

        // Loop melalui data dan tambahkan ke dalam container
        data.forEach(function(item) {
            var diterimaDiformat = formatRibuan(item.diterima);
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
            // html += '<span>Rp. &nbsp;</span><h3 class="">' + item.diterima + '</h3>';
            html += '<span>Rp. &nbsp;</span><h3 class="">' + diterimaDiformat + '</h3>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            $('#dataContainer_list').append(html);
        });
    }


    function reload_table()
    {
        table.ajax.reload(null,false); //reload datatable ajax 
    }
    
    function perbaikan() {
        $('#modal_perbaikan').modal('show');    
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
                $('#kehadiran_p').html(data.data.jumlah_hadir_piket);
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

<div id="modal_rincian" class="modal fade bd-example-modal-lg" tabindex="-1" aria-hidden="true" style="display: none;">
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
                            <h4 class="card-title">Rincian Barokah </h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover table-responsive">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Lengkap</th>
                                        <th>Jabatan</th>
                                        <th>TMT</th>
                                        <th>Tunjab</th>
                                        <th>MP</th>
                                        <th colspan="2" >Kehadiran</th>
                                        <th>Tunkel</th>
                                        <th>TMP</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody id="data_table">
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td id="jumlah"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                       
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
                <!-- <h5 class="modal-title text-success">Rincian Barokah</h5> -->
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
                                            <h4 class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="card-title text-success"><i class="fas fa-file-invoice"></i> Rincian Barokah</span>
                                                <!-- <span class="badge badge-primary badge-pill">3</span> -->
                                            </h4>
                                            <ul class="list-group mb-3">
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">Barokah Pokok </h6>
                                                        <!-- <small class="text-muted">Brief description</small> -->
                                                    </div>
                                                    <span class="text-muted" id="tunjab"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">Kehadiran</h6>
                                                        <small class="text-muted">Hadir Normal <strong id="hadir"> </strong> Kali</small>
                                                    </div>
                                                    <span class="text-muted" id="nominal_kehadiran"></span>
                                                </li>
                                                <!--<li class="list-group-item d-flex justify-content-between lh-condensed">-->
                                                <!--    <div>-->
                                                <!--        <h6 class="my-0">Kehadiran 15.000</h6>-->
                                                <!--        <small class="text-muted">Hadir <strong id="kehadiran_15"> </strong> x 15.000</small>-->
                                                <!--    </div>-->
                                                <!--    <span class="text-muted" id="nominal_kehadir_15"></span> -->
                                                <!--</li>-->
                                                <!--<li class="list-group-item d-flex justify-content-between lh-condensed">-->
                                                <!--    <div>-->
                                                <!--        <h6 class="my-0">Kehadiran 10.000</h6>-->
                                                <!--        <small class="text-muted">Hadir <strong id="kehadiran_10"> </strong> x 10.000</small>-->
                                                <!--    </div>-->
                                                <!--   <span class="text-muted" id="nominal_kehadir_10"></span>-->
                                                <!--</li>-->
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">Tunjangan Keluarga</h6>
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
                                                        <h6 class="my-0">Tunjangan Masa Pengabdian</h6>
                                                        <!-- <small class="text-muted">Brief description</small> -->
                                                    </div>
                                                    <span class="text-muted" id="tmp"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">Tunjangan Beban Kerja</h6>
                                                        <!-- <small class="text-muted">Brief description</small> -->
                                                    </div>
                                                    <span class="text-muted" id="tbk"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                    <div>
                                                        <h6 class="my-0">Tunjangan Kehormatan</h6>
                                                        <!-- <small class="text-muted">Brief description</small> -->
                                                    </div>
                                                    <span class="text-muted" id="kehormatan"></span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                                <div>
                                                    <h6 class="my-0">Potongan</h6>
                                                    <!-- <small class="text-muted">Brief description</small> -->
                                                    <div id="dataContainer"> </div>
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
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
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
                                                        <h6 class="my-0">Kehadiran</h6>
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
                                                         <!--<small class="text-muted">Hadir <strong id="kehadiran_p">  </strong> </small>-->
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
