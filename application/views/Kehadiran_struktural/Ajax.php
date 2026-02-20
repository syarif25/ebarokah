    <script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
    <script src="<?php echo base_url() ?>assets/vendor/chart.js/Chart.bundle.min.js"></script>
	<!-- Apex Chart -->
	<script src="<?php echo base_url() ?>assets/vendor/apexchart/apexchart.js"></script>
	
    <!-- Datatable -->
    <script src="<?php echo base_url() ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/plugins-init/datatables.init.js"></script>
    <script src="<?php echo base_url() ?>assets/js/jquery.mask.js"></script>

	<script src="<?php echo base_url() ?>assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>

    <!-- Tambahkan ini sebelum script kehadiran.js -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
	<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>

    <script>
   
    var save_method; //for save method string
    var table;

    $(document).ready(function(){
     
     table_item =   $('#tabel2').DataTable({ 
        "paging": false,
        "searching": false,
        "ordering": false,
        "info": true,
        "destroy": true,
        "deferRender": true
      });
   });

    function cekInputFile() {
			var inputFile = document.getElementById("file-input").value;
			if (inputFile == "") {
				document.getElementById("btnSave").disabled = true;
			} else {
				document.getElementById("btnSave").disabled = false;
			}
		}
    
    $(function () {
    $('#inputku').mask('000.000.000.000', {reverse: true});

      // $("#example1").DataTable();
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

    table =   $('#tabel_view').DataTable({
        "orderCellsTop": true,
        "fixedHeader": true,
        "ajax": {
            "url": "<?php echo site_url('kehadiran_struktural/data_list')?>",
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

    function rekap($id) {
        var id = $id;
        window.open("<?php echo site_url('kehadiran_struktural/add/')?>"+id, '_blank');   
    }

    function add_blanko() {
         $('#form')[0].reset(); // reset form on modals
         save_method = 'add';
        $('#modal_kehadiran').modal('show');    
    }

    function save() {
  // Ubah state tombol
  $('#btnSave').text('Proses menyimpan...').prop('disabled', true);

  // Guard klien: file wajib (sinkron dengan controller)
  var fileInput = document.getElementById('file-input');
  if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
    $('#btnSave').text('Simpan').prop('disabled', false);
    Swal.fire({
      icon: 'warning',
      title: 'File belum dipilih',
      text: 'File absensi (PDF) wajib diunggah sebelum menyimpan.'
    });
    return;
  }

  $.ajax({
    url: "<?php echo site_url('kehadiran_struktural/ajax_add')?>",
    type: "POST",
    data: new FormData($('#form')[0]),
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (data) {
      // Kembalikan state tombol
      $('#btnSave').text('Simpan').prop('disabled', false);

      if (data && data.status) {
        Swal.fire({
          icon: 'success',
          title: 'Tersimpan',
          text: data.message || 'Rekap kehadiran berhasil disimpan.'
        }).then(() => {
          // Kembali ke daftar kehadiran
          window.location = "<?php echo site_url('kehadiran')?>";
        });
      } else {
        // Gagal logis/validasi dari server (_do_upload / payload / dll.)
        Swal.fire({
          icon: 'warning',
          title: 'Gagal menyimpan',
          text: (data && data.message) ? data.message : 'Periksa kembali isian form dan file PDF.'
        });
      }
    },
    error: function () {
      // Kembalikan state tombol
      $('#btnSave').text('Simpan').prop('disabled', false);

      // Error jaringan / server
      Swal.fire({
        icon: 'error',
        title: 'Terjadi kesalahan',
        text: 'Gagal menghubungi server. Coba lagi beberapa saat.'
      });
    }
  });
}


    function simpan() {
    $('#btnSave').text('Proses menyimpan...');
    $('#btnSave').attr('disabled', true);

    var url = "<?php echo site_url('kehadiran_struktural/blanko_add')?>";
    var formData = new FormData($('#form')[0]);

    $.ajax({
        url: url,
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "JSON",
        success: function (data) {
            $('#btnSave').text('Simpan');
            $('#btnSave').attr('disabled', false);

            if (data.status) {
                // Jika berhasil membuat blanko
                Swal.fire({
                    title: "Berhasil!",
                    text: "Blanko input barokah berhasil dibuat untuk periode ini.",
                    icon: "success",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK"
                }).then(() => {
                    $('#modal_kehadiran').modal('hide');
                    reload_table();
                });
            } else {
                //  Jika data sudah ada / duplikat
                Swal.fire({
                    title: "Data Sudah Ada",
                    text: "Blanko untuk lembaga dan periode tersebut sudah dibuat sebelumnya.",
                    icon: "warning",
                    confirmButtonColor: "#d33",
                    confirmButtonText: "Tutup"
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $('#btnSave').text('Simpan');
            $('#btnSave').attr('disabled', false);
            Swal.fire({
                title: "Terjadi Kesalahan!",
                text: "Gagal menambah blanko. Coba lagi nanti.",
                icon: "error"
            });
        }
    });
}


    function edit_user(id)
    {
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals


        //Ajax Load data from ajax
        $.ajax({
            url : "<?php echo site_url('Ketentuan_barokah/ajax_edit')?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data)
            {
            $('[name="id_ketentuan"]').val(data.id_ketentuan);
            $('[name="nama_jabatan"]').val(data.nama_jabatan);
            $('[name="kategori"]').val(data.kategori);
            $('[name="barokah"]').val(data.barokah);
            $('#modal_kehadiran').modal('show'); // show bootstrap modal when complete loaded
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }

    function lihat_absen(id)
    {
        $.ajax({
        url : "<?php echo site_url('kehadiran_struktural/get_total_absen')?>/" + id,
        type: "POST",
        dataType: "JSON",
        success: function(data)
        {
                $('#nama_lembaga').text(data.data_elemen.nama_lembaga);
                $('#id_kehadiran_lembaga').val(data.data_elemen.id_kehadiran_lembaga);
                $('#jumlah').text(data.data_elemen.jumlah_total);
                $('#periode').text(data.data_elemen.bulan + ' ' + data.data_elemen.tahun );
                table_item.clear();
                table_item.rows.add($(data.html_item)).draw();
                $('#modal_sudah_trans').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text(''); // Set title to Bootstrap modal title
        },
            error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
        });
    }
    
    function lihat_barokah(id)
    {
        $.ajax({
        url : "<?php echo site_url('kehadiran_struktural/get_absen')?>/" + id,
        type: "POST",
        dataType: "JSON",
        success: function(data)
        {
                $('#nama_lembaga').text(data.data_elemen.nama_lembaga);
                $('#id_kehadiran_lembaga').val(data.data_elemen.id_kehadiran_lembaga);
                $('#jumlah').text(data.data_elemen.jumlah_total);
                $('#periode').text(data.data_elemen.bulan + ' ' + data.data_elemen.tahun );
                table_item.clear();
                table_item.rows.add($(data.html_item)).draw();
                $('#modal_sudah_trans').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text(''); // Set title to Bootstrap modal title
        },
            error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
        });
    }

    function reload_table()
    {
        var pesan = ' Refresh tabel';
        table.ajax.reload(null,false); //reload datatable ajax 
        // notif(pesan);
    }

    $(document).on('click', '.btn-reset', function () {
        const idL = $(this).data('id');
        const status = ($(this).data('status') || '').toLowerCase();

        if (!idL) {
            Swal.fire('Error', 'ID periode tidak ditemukan.', 'error');
            return;
        }

        Swal.fire({
            title: 'Reset data periode ini?',
            html: `
            <div class="text-left">
                <label class="d-block">
                <input type="radio" name="resetmode" value="kehadiran" checked>
                Hapus <b>kehadiran</b> periode ini saja
                </label>
                <label class="d-block mt-2">
                <input type="radio" name="resetmode" value="total">
                Hapus <b>total_barokah</b> periode ini
                </label>
            </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, reset',
            cancelButtonText: 'Batal'
        }).then((res) => {
            if (!res.isConfirmed) return;

            const mode = $('input[name="resetmode"]:checked').val();

            $.ajax({
            url: "<?= site_url('Validasi_struktural/reset_json') ?>",
            type: "POST",
            dataType: "json",
            data: {
                id_kehadiran_lembaga: idL,
                mode: mode
                // kalau pakai CSRF, sertakan token di sini
            },
            beforeSend: function(){
                Swal.showLoading();
            }
            })
            .done(function(rsp){
            if (rsp && rsp.status) {
                Swal.fire({icon:'success', title:'Selesai', text:rsp.message, timer:1400, showConfirmButton:false})
                .then(() => { window.location.href = "<?= site_url('kehadiran_struktural') ?>"; });
            } else {
                Swal.fire('Gagal', (rsp && rsp.message) ? rsp.message : 'Reset gagal.', 'error');
            }
            })
            .fail(function(xhr){
            console.error(xhr.responseText);
            Swal.fire('Error', 'Gagal menghubungi server.', 'error');
            });
        });
    });
	
// ============================================
// UX IMPROVEMENTS: Stepper, Validation, Keyboard Nav
// ============================================

// Stepper Controls
function incrementInput(button) {
    const input = button.previousElementSibling;
    const val = parseInt(input.value) || 0;
    input.value = val + 1;
    validateInput(input);
}

function decrementInput(button) {
    const input = button.nextElementSibling;
    const val = parseInt(input.value) || 0;
    if (val > 0) {
        input.value = val - 1;
        validateInput(input);
    }
}

// Input Validation with Visual Feedback
function validateInput(input) {
    const val = parseInt(input.value) || 0;
    
    // Tidak boleh negatif
    if (val < 0) {
        input.value = 0;
        input.classList.add('is-invalid');
        return;
    }
    
    // Visual feedback
    input.classList.remove('is-invalid');
    if (val > 0) {
        input.classList.add('is-valid');
    } else {
        input.classList.remove('is-valid');
    }
}

// File Upload Check
function checkFileUploaded() {
    const fileInput = document.getElementById('file-input');
    const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
    
    const btnSave = document.getElementById('btnSave');
    btnSave.disabled = !hasFile;
}

// Keyboard Navigation
$(document).ready(function() {
    // Enter key untuk next field
    $('input.attendance-input').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const inputs = $('input.attendance-input:visible');
            const currentIndex = inputs.index(this);
            const nextInput = inputs.eq(currentIndex + 1);
            
            if (nextInput.length) {
                nextInput.focus().select();
            } else {
                // Last field, focus ke upload file
                $('#file-input').focus();
            }
        }
    });
    
    // Auto-select value on focus
    $('input.attendance-input').on('focus', function() {
        $(this).select();
    });
});

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
                            <h4 class="card-title">Tambah Blanko Input Barokah Lembaga</h4>
                        </div>
                        <div class="card-body">
                            <div class="basic-form">
                            <label>Nama Lembaga</label>
                                 <div class="input-group mb-3 input-success-o">
                                 <select name="id_lembaga" class="form-control">
                                    <option value=""></option>
                                    <?php 
                                     
                                    if($this->session->userdata('jabatan') == "AdminLembaga"){
                                        $query = $this->db->get_where('lembaga', ['id_lembaga' => $this->session->userdata('lembaga')]);
                                    } else {
                                    $query = $this->db->get('lembaga');
                                    }
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
                                                    <!--<option></option>-->
                                                    <!--<option >2022/2023</option>-->
                                                    <option >2023/2024</option>
                                                    <option >2024/2025</option>
                                                    <option>2026</option>
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

<div id="modal_sudah_trans" class="modal fade bd-example-modal-lg" tabindex="-1" aria-hidden="true" style="display: none;">
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
                            <h4 class="card-title">Form Rekap Kehadiran Umana</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-3">
                                    <label>Nama Lembaga</label>
                                    <h5 id="nama_lembaga"></h5>
                                    <input type="hidden" id="id_kehadiran_lembaga" name="id_kehadiran_lembaga">
                                </div>
                                <div class="col-3">
                                    <label>Periode</label>
                                    <h5 id="periode"></h5>
                                </div>
                                <!-- <div class="col-3">
                                    <label>Jumlah </label>
                                    <h5 id="jumlah"></h5>
                                </div> -->
                            </div>
                            <br>
                            <form action="#" id="form">
                                <table class="table" id="tabel2" style="min-width: 100%" >
                                    <thead>
                                        <tr>
                                            <td>No</td>
                                            <td>Nama Lengkap</td>
                                            <td>Jabatan</td>
                                            <td>Jumlah Hadir</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    
                                    </tbody>
                                </table>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>