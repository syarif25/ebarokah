<script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
<script src="<?php echo base_url();?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url();?>assets/js/plugins-init/datatables.init.js"></script>
<script src="<?php echo base_url() ?>assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>

<script type="text/javascript">
var save_method;
var table;

$(document).ready(function() {
    table = $('#tabel_view').DataTable({ 
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            "url": "<?php echo site_url('Master_prodi/data_list')?>",
            "type": "POST"
        },
        "columnDefs": [
        { 
            "targets": [ -1 ],
            "orderable": false,
        },
        ],
    });
});

function add_prodi()
{
    save_method = 'add';
    $('#form')[0].reset();
    $('.form-control').removeClass('is-invalid');
    $('.help-block').empty();
    $('#modal_form').modal('show');
    $('.modal-title').text('Tambah Prodi');
}

function edit_prodi(id)
{
    save_method = 'update';
    $('#form')[0].reset();
    $('.form-control').removeClass('is-invalid');
    $('.help-block').empty();

    $.ajax({
        url : "<?php echo site_url('Master_prodi/ajax_edit')?>/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            $('[name="id_prodi"]').val(data.id_prodi);
            $('[name="id_lembaga"]').val(data.id_lembaga);
            $('[name="nama_prodi"]').val(data.nama_prodi);
            
            $('#modal_form').modal('show');
            $('.modal-title').text('Edit Prodi');
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}

function save()
{
    $('#btnSave').text('Menyimpan...');
    $('#btnSave').attr('disabled',true);
    var url;

    if(save_method == 'add') {
        url = "<?php echo site_url('Master_prodi/ajax_add')?>";
    } else {
        url = "<?php echo site_url('Master_prodi/ajax_update')?>";
    }

    $.ajax({
        url : url,
        type: "POST",
        data: $('#form').serialize(),
        dataType: "JSON",
        success: function(data)
        {
            if(data.status)
            {
                $('#modal_form').modal('hide');
                table.ajax.reload(null,false);
            }
            else
            {
                for (var i = 0; i < data.inputerror.length; i++) 
                {
                    $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error');
                    $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]);
                }
            }
            $('#btnSave').text('Simpan');
            $('#btnSave').attr('disabled',false);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error adding / update data');
            $('#btnSave').text('Simpan');
            $('#btnSave').attr('disabled',false);
        }
    });
}

function hapus_prodi(id)
{
    if(confirm('Apakah Anda yakin ingin menghapus data Prodi ini?'))
    {
        $.ajax({
            url : "<?php echo site_url('Master_prodi/ajax_delete')?>/"+id,
            type: "POST",
            dataType: "JSON",
            success: function(data)
            {
                $('#modal_form').modal('hide');
                table.ajax.reload(null,false);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error deleting data');
            }
        });
    }
}
</script>
