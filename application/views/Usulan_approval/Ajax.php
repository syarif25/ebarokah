<script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
<!-- Datatable -->
<script src="<?php echo base_url() ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/plugins-init/datatables.init.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>

<script>
$(document).ready(function() {
    load_antrean();
    load_riwayat();
});

function load_antrean() {
    $('#tabel_antrean').DataTable({
        "processing": true,
        "serverSide": false,
        "destroy": true,
        "ajax": {
            "url": "<?php echo site_url('Usulan_approval/data_list_pending')?>",
            "type": "POST"
        }
    });
}

function load_riwayat() {
    $('#tabel_riwayat').DataTable({
        "processing": true,
        "serverSide": false,
        "destroy": true,
        "ajax": {
            "url": "<?php echo site_url('Usulan_approval/data_list_history')?>",
            "type": "POST"
        },
        "order": [[ 0, "desc" ]] // Sortir agar keputusan terbaru di atas
    });
}

function acc_usulan(id) {
    Swal.fire({
        title: 'Verifikasi & Setujui?',
        text: 'Aksi ini akan LANGSUNG MENIMPA (Overwrite) Data Master Struktural Pegawai yang bersangkutan. Lanjutkan?',
        icon: 'question',
        input: 'textarea',
        inputLabel: 'Catatan Persetujuan (Opsional)',
        inputPlaceholder: 'Tinggalkan catatan singkat...',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Setujui!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "<?php echo site_url('Usulan_approval/ajax_acc/') ?>" + id,
                type: "POST",
                data: { catatan_reviewer: result.value || '' },
                dataType: "JSON",
                success: function(data) {
                    if(data.status) {
                        Swal.fire('Disetujui!', data.message, 'success');
                        load_antrean();
                        load_riwayat();
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire('Error', 'Kesalahan koneksi ke server.', 'error');
                }
            });
        }
    });
}

function reject_usulan(id) {
    Swal.fire({
        title: 'Tolak Usulan?',
        text: '.',
        icon: 'warning',
        input: 'textarea',
        inputLabel: 'Alasan Penolakan',
        inputPlaceholder: 'Lampiran kurang jelas, atau tidak sesuai SK...',
        inputValidator: (value) => {
            if (!value) {
                return 'Alasan penolakan tidak boleh kosong!'
            }
        },
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Tolak'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "<?php echo site_url('Usulan_approval/ajax_reject/') ?>" + id,
                type: "POST",
                data: { catatan_reviewer: result.value },
                dataType: "JSON",
                success: function(data) {
                    if(data.status) {
                        Swal.fire('Ditolak!', data.message, 'success');
                        load_antrean();
                        load_riwayat();
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire('Error', 'Kesalahan koneksi ke server.', 'error');
                }
            });
        }
    });
}
</script>
