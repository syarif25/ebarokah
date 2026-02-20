<script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
    <script src="<?php echo base_url() ?>assets/vendor/chart.js/Chart.bundle.min.js"></script>
	<!-- Apex Chart -->
	<script src="<?php echo base_url() ?>assets/vendor/apexchart/apexchart.js"></script>
	
    <!-- Datatable -->
    <script src="<?php echo base_url() ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/plugins-init/datatables.init.js"></script>
    <script src="<?php echo base_url() ?>assets/js/jquery.mask.js"></script>

	<script src="<?php echo base_url() ?>assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
	<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>

    <script>
        // Delegation: aman meski tabel dirender ulang oleh DataTables/AJAX
    $(document).on('click', '.btn-reset', function() {
        const idKL = $(this).data('id');   // id_kehadiran_lembaga mentah (bukan terenkripsi)

        Swal.fire({
        title: 'Reset data?',
        text: 'Pilih jenis reset yang kamu inginkan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Reset total_barokah saja',
        cancelButtonText: 'Batal',
        showDenyButton: true,
        denyButtonText: 'Reset Kehadiran + Total'
        }).then((r) => {
        if (r.isConfirmed) doReset(idKL, 'total');   // hapus total_barokah_satpam saja
        else if (r.isDenied) doReset(idKL, 'all');   // hapus total + set 0 kehadiran_satpam
        });
    });

    function doReset(idKL, scope) {
        Swal.fire({title:'Memproses...', allowOutsideClick:false, didOpen:()=>Swal.showLoading()});
        $.ajax({
        url: "<?= site_url('kehadiran_satpam/reset_json') ?>",
        type: "POST",
        dataType: "json",
        data: { id_kehadiran_lembaga: idKL, scope: scope },
        success: function(res){
            if (res && res.status) {
            Swal.fire({icon:'success', title:'Berhasil', text:res.message||'Reset selesai.', timer:1400, showConfirmButton:false})
                .then(()=> location.reload());
            } else {
            Swal.fire('Gagal', (res && res.message) ? res.message : 'Reset gagal.', 'error');
            }
        },
        error: function(xhr){
            console.error(xhr.responseText);
            Swal.fire('Error', 'Tidak dapat menghubungi server.', 'error');
        }
        });
    }
    var save_method; // for save method string
    var table;

    // Clone the header row for search inputs
    $('#tabel_view thead tr').clone(true).appendTo( '#tabel_view thead' );
    $('#tabel_view thead tr:eq(1) th').each( function (i) {
        var title = $(this).text();
        
        // Skip filter for No and Aksi
        if (title !== 'Aksi' && title !== 'No') {
            $(this).html( '<input type="text" class="form-control form-control-sm" placeholder="'+title+'" />' );
     
            $( 'input', this ).on( 'keyup change', function () {
                if ( table.column(i).search() !== this.value ) {
                    table
                        .column(i)
                        .search( this.value )
                        .draw();
                }
            } );
        } else {
            $(this).html('');
        }
    } );

    $(function () {
        table = $('#tabel_view').DataTable({
            "orderCellsTop": true,
            "fixedHeader": true,
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

    function simpan() {
    $('#btnSimpan').text('Proses menyimpan...').prop('disabled', true);

    $.ajax({
        url: "<?php echo site_url('kehadiran_satpam/blanko_add')?>",
        type: "POST",
        data: new FormData($('#form')[0]),
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (data) {
        $('#btnSimpan').text('Simpan').prop('disabled', false);

        if (data && data.status) {
            Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: data.message || 'Blanko berhasil dibuat.'
            }).then(() => {
            $('#modal_kehadiran').modal('hide');
            reload_table();
            });
        } else {
            Swal.fire({
            icon: 'warning',
            title: 'Tidak bisa membuat blanko',
            text: (data && data.message) ? data.message : 'Periksa kembali isian bulan/tahun/lembaga.'
            });
        }
        },
        error: function () {
        $('#btnSimpan').text('Simpan').prop('disabled', false);
        Swal.fire({
            icon: 'error',
            title: 'Kesalahan Server',
            text: 'Gagal membuat blanko. Coba lagi beberapa saat.'
        });
        }
    });
    }


    function simpan_kehadiran(){
        $('#btnSave').text('Proses menyimpan...').prop('disabled', true);

        // wajib file
        var fileInput = document.getElementById('file-input');
        if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
            $('#btnSave').text('Simpan').prop('disabled', false);
            Swal.fire({icon:'warning',title:'File belum dipilih',text:'File absensi (PDF) wajib diunggah.'});
            return;
        }

        $.ajax({
            url: "<?php echo site_url('kehadiran_satpam/ajax_add_kehadiran')?>",
            type: "POST",
            data: new FormData($('#form')[0]),
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (data) {
            $('#btnSave').text('Simpan').prop('disabled', false);
            if (data && data.status) {
                Swal.fire({icon:'success',title:'Tersimpan',text: data.message || 'Rekap kehadiran berhasil disimpan.'})
                .then(()=>{ window.location = "<?php echo site_url('kehadiran_satpam')?>"; });
            } else {
                Swal.fire({icon:'error',title:'Gagal menyimpan',text: (data && data.message) ? data.message : 'Periksa kembali isian dan file PDF.'});
            }
            },
            error: function(){
            $('#btnSave').text('Simpan').prop('disabled', false);
            Swal.fire({icon:'error',title:'Kesalahan server',text:'Gagal menghubungi server.'});
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
                                    $query = $this->db->get_where('lembaga', 'id_lembaga = 59');
                                    
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
                                                    <option>2025/2026</option>
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