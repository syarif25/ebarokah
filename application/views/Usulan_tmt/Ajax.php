<script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
<script src="<?php echo base_url() ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/plugins-init/datatables.init.js"></script>
<script src="<?php echo base_url() ?>assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>

<script>
var table;

$(document).ready(function() {
    table = $('#tabel_view').DataTable({
        "ajax": {
            "url": "<?php echo site_url('Usulan_tmt/data_list'); ?>",
            "type": "POST"
        },
        "columnDefs": [
            { "targets": [ -1, -2, -3 ], "orderable": false }
        ],
        "paging": true,
        "searching": true,
        "ordering": true
    });
});

function add_usulan() {
    $('#form')[0].reset();
    $('#id_usulan').val('');
    $('.badge').text('-');
    $('select, input').removeClass('bg-warning-light border-warning text-dark font-weight-bold');
    $('.modal-title').text('Form Baru Perubahan Komponen Barokah');
    $('#lampiran_hint').addClass('d-none');
    $('#id_penempatan').html('<option value="">Sedang memuat data...</option>');
    
    // Load options untuk pegawai di lembaga ini
    $.ajax({
        url: '<?php echo site_url('Usulan_tmt/get_pegawai_options'); ?>',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            let html = '<option value="">-- Pilih Pegawai --</option>';
            data.forEach(function(item) {
                let currTmo = item.tmt_struktural ? item.tmt_struktural : 'Belum Terdaftar';
                let currMp  = item.mp ? item.mp : 0;
                
                // Set data-attr untuk old value info
                html += `<option value="${item.id_penempatan}" 
                        data-tmt="${currTmo}" 
                        data-mp="${currMp}"
                        data-tunkel="${item.tunj_kel}"
                        data-tanak="${item.tunj_anak}"
                        data-tmp="${item.tunj_mp}"
                        data-hormat="${item.kehormatan}"
                        >${item.nama_lengkap} (TMT: ${currTmo})</option>`;
            });
            $('#id_penempatan').html(html);
            $('#id_penempatan').select2({
                dropdownParent: $('#modal_usulan')
            });
            $('#modal_usulan').modal('show');
        }
    });
}

function edit_usulan(id) {
    $('#form')[0].reset();
    $('#id_usulan').val(id);
    $('.badge').text('-');
    $('select, input').removeClass('bg-warning-light border-warning text-dark font-weight-bold');
    $('.modal-title').text('Form Revisi Komponen Barokah');
    $('#lampiran_hint').removeClass('d-none'); // Tampilkan pesan opsional lampiran
    $('#id_penempatan').html('<option value="">Sedang memuat data...</option>');

    // Ambil data spesifik yg mau di edit
    $.ajax({
        url: "<?php echo site_url('Usulan_tmt/ajax_edit/') ?>" + id,
        type: "GET",
        dataType: "JSON",
        success: function(usulan) {
            if(usulan.status === false) {
                 Swal.fire('Error', 'Data Usulan tidak ditemukan.', 'error'); return;
            }

            // Load ulang dropdown pegawai agar bisa dipilih
            $.ajax({
                url: '<?php echo site_url('Usulan_tmt/get_pegawai_options'); ?>',
                type: 'GET',
                dataType: 'json',
                success: function(dataPegawai) {
                    let html = '';
                    dataPegawai.forEach(function(item) {
                        let currTmo = item.tmt_struktural ? item.tmt_struktural : 'Belum Terdaftar';
                        let currMp  = item.mp ? item.mp : 0;
                        html += `<option value="${item.id_penempatan}" 
                                data-tmt="${currTmo}" 
                                data-mp="${currMp}"
                                data-tunkel="${item.tunj_kel}"
                                data-tanak="${item.tunj_anak}"
                                data-tmp="${item.tunj_mp}"
                                data-hormat="${item.kehormatan}"
                                >${item.nama_lengkap} (TMT: ${currTmo})</option>`;
                    });
                    $('#id_penempatan').html(html);
                    
                    // Assign Value Detail
                    $('#id_penempatan').val(usulan.id_penempatan);
                    $('#id_penempatan').select2({ dropdownParent: $('#modal_usulan') });

                    // Simpan data lama referensi TMT untuk badge (memicu trigger change manual)
                    let selected = $('#id_penempatan').find('option:selected');
                    let oldTmt = selected.data('tmt');
                    let oldTunkel   = selected.data('tunkel') || 'Tidak';
                    let oldTanak    = selected.data('tanak') || 'Tidak';
                    let oldTmp      = selected.data('tmp') || 'Tidak';
                    let oldHormat   = selected.data('hormat') || 'Tidak';
                    
                    $('#badge_tunj_kel').text('Saat ini: ' + oldTunkel);
                    $('#badge_tunj_anak').text('Saat ini: ' + oldTanak);
                    $('#badge_tunj_mp').text('Saat ini: ' + oldTmp);
                    $('#badge_kehormatan').text('Saat ini: ' + oldHormat);
                    
                    $('#tmt_baru').data('old_val', oldTmt);
                    $('#tunj_kel_baru').data('old_val', oldTunkel);
                    $('#tunj_anak_baru').data('old_val', oldTanak);
                    $('#tunj_mp_baru').data('old_val', oldTmp);
                    $('#kehormatan_baru').data('old_val', oldHormat);

                    // Isi Value yang diusulkan oleh Maker sblmnya
                    $('#tmt_baru').val(usulan.tmt_baru);
                    $('#tunj_kel_baru').val(usulan.tunj_kel_baru);
                    $('#tunj_anak_baru').val(usulan.tunj_anak_baru);
                    $('#tunj_mp_baru').val(usulan.tunj_mp_baru);
                    $('#kehormatan_baru').val(usulan.kehormatan_baru);
                    $('[name="keterangan_usulan"]').val(usulan.keterangan_usulan);

                    checkChangeHighlight();

                    $('#modal_usulan').modal('show');
                }
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            Swal.fire('Error', 'Gagal memanggil data rekaman dari server', 'error');
        }
    });

}


// Logic untuk update label komponen lama saat pegawai dipilih dan Autofill Data
$(document).on('change', '#id_penempatan', function() {
    let selected = $(this).find('option:selected');
    if (selected.val() != '') {
        // TMT Baru disinkronkan dgn TMT lama secara default
        let oldTmt = selected.data('tmt');
        if(oldTmt && oldTmt !== 'Belum Terdaftar') {
             $('#tmt_baru').val(oldTmt);
        } else {
             $('#tmt_baru').val('');
        }
        
        // Ambil data lama
        let oldTunkel   = selected.data('tunkel') || 'Tidak';
        let oldTanak    = selected.data('tanak') || 'Tidak';
        let oldTmp      = selected.data('tmp') || 'Tidak';
        let oldHormat   = selected.data('hormat') || 'Tidak';

        // Set info label Badge
        $('#badge_tunj_kel').text('Saat ini: ' + oldTunkel);
        $('#badge_tunj_anak').text('Saat ini: ' + oldTanak);
        $('#badge_tunj_mp').text('Saat ini: ' + oldTmp);
        $('#badge_kehormatan').text('Saat ini: ' + oldHormat);
        
        // Simpan data lama sebagai referensi untuk checking perubahan later
        $('#tmt_baru').data('old_val', oldTmt);
        $('#tunj_kel_baru').data('old_val', oldTunkel);
        $('#tunj_anak_baru').data('old_val', oldTanak);
        $('#tunj_mp_baru').data('old_val', oldTmp);
        $('#kehormatan_baru').data('old_val', oldHormat);

        // Autofill pilihan default dengan data lama
        $('#tunj_kel_baru').val(oldTunkel);
        $('#tunj_anak_baru').val(oldTanak);
        $('#tunj_mp_baru').val(oldTmp);
        $('#kehormatan_baru').val(oldHormat);
        
        // Reset warna semua dropdown
        checkChangeHighlight();
    } else {
        $('#form')[0].reset();
        $('.badge').text('-');
        $('select, input').removeClass('bg-warning-light border-warning');
    }
});

// Event listener form jika isian berubah dari nilai *default*
$(document).on('change', '#tmt_baru, #tunj_kel_baru, #tunj_anak_baru, #tunj_mp_baru, #kehormatan_baru', function() {
    checkChangeHighlight();
});

function checkChangeHighlight() {
    let elements = ['#tmt_baru', '#tunj_kel_baru', '#tunj_anak_baru', '#tunj_mp_baru', '#kehormatan_baru'];
    elements.forEach(function(el) {
        let currentVal = $(el).val();
        let oldVal = $(el).data('old_val');
        
        // Tambahkan highlight warna kuning/jingga jika nilai berbeda dgn Saat ini
        if (currentVal !== oldVal) {
            $(el).addClass('bg-warning-light border-warning text-dark font-weight-bold');
        } else {
            $(el).removeClass('bg-warning-light border-warning text-dark font-weight-bold');
        }
    });
}

function hapus_usulan(id) {
    Swal.fire({
        title: 'Hapus/Batalkan Usulan?',
        text: "Usulan yang masih pending dapat ditarik kembali.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "<?php echo site_url('Usulan_tmt/ajax_hapus/'); ?>" + id,
                type: "POST",
                dataType: "JSON",
                success: function(data) {
                    if(data.status) {
                        Swal.fire('Terhapus!', data.message, 'success');
                        table.ajax.reload(null, false);
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    Swal.fire('Error!', 'Gagal memproses ke server.', 'error');
                }
            });
        }
    })
}

function save() {
    $('#btnSave').text('Mengunggah...').prop('disabled', true);
    
    var formData = new FormData($('#form')[0]);
    var isEdit = $('#id_usulan').val() !== '';
    var saveUrl = isEdit ? "<?php echo site_url('Usulan_tmt/ajax_update'); ?>" : "<?php echo site_url('Usulan_tmt/ajax_add'); ?>";

    // Validasi paksa khusus tambah baru (harus lampirkan berkas)
    if(!isEdit && document.getElementById('file_dokumen').files.length === 0) {
         $('#btnSave').text('Kirim Usulan').prop('disabled', false);
         Swal.fire('Peringatan', 'Lampiran Dokumen PDF/JPG/PNG Wajib Diisi untuk Usulan Baru.', 'warning');
         return;
    }
    
    $.ajax({
        url: saveUrl,
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(data) {
            $('#btnSave').text('Kirim Usulan').prop('disabled', false);
            if (data.status) {
                Swal.fire('Berhasil', data.message, 'success').then(() => {
                    $('#modal_usulan').modal('hide');
                    table.ajax.reload(null, false);
                });
            } else {
                Swal.fire('Peringatan', data.message, 'warning');
            }
        },
        error: function(xhr) {
            $('#btnSave').text('Kirim Usulan').prop('disabled', false);
            console.error(xhr.responseText);
            Swal.fire('Error', 'Gagal mengirim usulan. Pastikan format divalidasi.', 'error');
        }
    });
}
</script>

