<script type="text/javascript">

var save_method;
var table;

$(document).ready(function() {
	
	// Initialize Select2
	$('.select2').select2({
		dropdownParent: $('#modal_form')
	});
	
	// Initialize DataTable
	table = $('#tabel_cuti').DataTable({ 
		"processing": true,
		"serverSide": true,
		"order": [],
		"ajax": {
			"url": "<?php echo site_url('cuti/data_list')?>",
			"type": "POST"
		},
		"columnDefs": [
			{ 
				"targets": [ 0, 1, 7 ],
				"orderable": false,
			},
		],
		"language": {
			"processing": "Memuat data...",
			"lengthMenu": "Tampilkan _MENU_ data per halaman",
			"zeroRecords": "Data tidak ditemukan",
			"info": "Menampilkan halaman _PAGE_ dari _PAGES_",
			"infoEmpty": "Tidak ada data yang tersedia",
			"infoFiltered": "(difilter dari _MAX_ total data)",
			"search": "Cari:",
			"paginate": {
				"first": "Pertama",
				"last": "Terakhir",
				"next": "Selanjutnya",
				"previous": "Sebelumnya"
			}
		}
	});

	// Load umana dropdown
	load_umana_dropdown();

	// Date validation
	$('#tanggal_mulai, #tanggal_selesai').on('change', function() {
		calculate_duration();
	});

});

function load_umana_dropdown() {
	$.ajax({
		url: "<?php echo site_url('cuti/get_umana_available')?>",
		type: "GET",
		dataType: "JSON",
		success: function(data) {
			var options = '<option value="">-- Pilih Umana --</option>';
			$.each(data, function(i, item) {
				var nama = '';
				if(item.gelar_depan) nama += item.gelar_depan + ' ';
				nama += item.nama_lengkap;
				if(item.gelar_belakang) nama += ' ' + item.gelar_belakang;
				options += '<option value="'+item.nik+'">'+nama+' ('+item.nik+')</option>';
			});
			$('#nik').html(options);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert('Error loading umana data');
		}
	});
}

function calculate_duration() {
	var start = $('#tanggal_mulai').val();
	var end = $('#tanggal_selesai').val();
	
	if(start && end) {
		var startDate = new Date(start);
		var endDate = new Date(end);
		var diffTime = Math.abs(endDate - startDate);
		var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
		
		var infoHtml = '';
		if(diffDays > 90) {
			infoHtml = '<div class="alert alert-danger alert-dismissible fade show"><strong>Peringatan!</strong> Durasi cuti melebihi batas maksimal 3 bulan (90 hari). Durasi: '+diffDays+' hari</div>';
		} else if(diffDays > 0) {
			infoHtml = '<div class="alert alert-info alert-dismissible fade show"><i class="fa fa-info-circle"></i> Durasi cuti: <strong>'+diffDays+' hari</strong></div>';
		} else {
			infoHtml = '<div class="alert alert-warning alert-dismissible fade show"><strong>Peringatan!</strong> Tanggal selesai harus lebih besar dari tanggal mulai</div>';
		}
		$('#durasi_info').html(infoHtml);
	} else {
		$('#durasi_info').html('');
	}
}

function add_cuti() {
	save_method = 'add';
	$('#form')[0].reset();
	$('.form-group').removeClass('has-error');
	$('.help-block').empty();
	$('#durasi_info').html('');
	$('#modal_form').modal('show');
	$('#modalFormLabel').text('Tambah Cuti Umana');
	
	// Reload umana dropdown
	load_umana_dropdown();
	
	// Set min date to today
	var today = new Date().toISOString().split('T')[0];
	$('#tanggal_mulai').attr('min', today);
}

function edit_cuti(id) {
	save_method = 'update';
	$('#form')[0].reset();
	$('.form-group').removeClass('has-error');
	$('.help-block').empty();
	$('#durasi_info').html('');

	$.ajax({
		url: "<?php echo site_url('cuti/ajax_edit/')?>/" + id,
		type: "GET",
		dataType: "JSON",
		success: function(data) {
			$('[name="id_cuti"]').val(data.id_cuti);
			$('[name="nik"]').val(data.nik).trigger('change');
			$('[name="jenis_cuti"][value="'+data.jenis_cuti+'"]').prop('checked', true);
			$('[name="tanggal_mulai"]').val(data.tanggal_mulai);
			$('[name="tanggal_selesai"]').val(data.tanggal_selesai);
			$('[name="keterangan"]').val(data.keterangan);
			
			calculate_duration();
			
			$('#modal_form').modal('show');
			$('#modalFormLabel').text('Edit Cuti Umana');
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert('Error get data from ajax');
		}
	});
}

function view_cuti(id) {
	$.ajax({
		url: "<?php echo site_url('cuti/ajax_edit/')?>/" + id,
		type: "GET",
		dataType: "JSON",
		success: function(data) {
			var nama = '';
			if(data.gelar_depan) nama += data.gelar_depan + ' ';
			nama += data.nama_lengkap;
			if(data.gelar_belakang) nama += ' ' + data.gelar_belakang;
			
			var jenisBadge = data.jenis_cuti == 'Cuti 100%' ? 
				'<span class="badge badge-danger">'+data.jenis_cuti+'</span>' : 
				'<span class="badge badge-warning">'+data.jenis_cuti+'</span>';
			
			var statusBadge = '';
			if(data.status == 'Aktif') statusBadge = '<span class="badge badge-primary">Aktif</span>';
			else if(data.status == 'Selesai') statusBadge = '<span class="badge badge-success">Selesai</span>';
			else statusBadge = '<span class="badge badge-danger">Dibatalkan</span>';
			
			var content = `
				<div class="row">
					<div class="col-md-6">
						<table class="table table-borderless">
							<tr>
								<td width="40%"><strong>Nama Umana</strong></td>
								<td>: ${nama}</td>
							</tr>
							<tr>
								<td><strong>Jenis Cuti</strong></td>
								<td>: ${jenisBadge}</td>
							</tr>
							<tr>
								<td><strong>Status</strong></td>
								<td>: ${statusBadge}</td>
							</tr>
						</table>
					</div>
					<div class="col-md-6">
						<table class="table table-borderless">
							<tr>
								<td width="40%"><strong>Tanggal Mulai</strong></td>
								<td>: ${data.tanggal_mulai}</td>
							</tr>
							<tr>
								<td><strong>Tanggal Selesai</strong></td>
								<td>: ${data.tanggal_selesai}</td>
							</tr>
							<tr>
								<td><strong>Keterangan</strong></td>
								<td>: ${data.keterangan || '-'}</td>
							</tr>
						</table>
					</div>
				</div>
			`;
			
			$('#view_content').html(content);
			$('#modal_view').modal('show');
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert('Error get data from ajax');
		}
	});
}

function save() {
	$('#btnSave').text('Menyimpan...');
	$('#btnSave').attr('disabled', true);
	var url;

	if(save_method == 'add') {
		url = "<?php echo site_url('cuti/ajax_add')?>";
	} else {
		url = "<?php echo site_url('cuti/ajax_update')?>";
	}

	$.ajax({
		url: url,
		type: "POST",
		data: $('#form').serialize(),
		dataType: "JSON",
		success: function(data) {
			if(data.status) {
				$('#modal_form').modal('hide');
				reload_table();
				
				toastr.success('Data berhasil disimpan', 'Sukses', {
					timeOut: 3000,
					closeButton: true,
					progressBar: true,
					positionClass: "toast-top-right"
				});
			} else {
				for (var i = 0; i < data.inputerror.length; i++) {
					$('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error');
					$('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]);
				}
			}
			$('#btnSave').text('Simpan');
			$('#btnSave').attr('disabled', false);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			alert('Error adding / updating data');
			$('#btnSave').text('Simpan');
			$('#btnSave').attr('disabled', false);
		}
	});
}

function selesaikan_cuti(id) {
	Swal.fire({
		title: 'Konfirmasi',
		text: "Apakah Anda yakin ingin menyelesaikan cuti ini?",
		icon: 'question',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Ya, Selesaikan!',
		cancelButtonText: 'Batal'
	}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				url: "<?php echo site_url('cuti/ajax_selesaikan/')?>/" + id,
				type: "POST",
				dataType: "JSON",
				success: function(data) {
					if(data.status) {
						reload_table();
						Swal.fire('Berhasil!', data.message, 'success');
					} else {
						Swal.fire('Gagal!', data.message, 'error');
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					Swal.fire('Error!', 'Terjadi kesalahan saat memproses data', 'error');
				}
			});
		}
	});
}

function batalkan_cuti(id) {
	Swal.fire({
		title: 'Konfirmasi',
		text: "Apakah Anda yakin ingin membatalkan cuti ini?",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#d33',
		cancelButtonColor: '#3085d6',
		confirmButtonText: 'Ya, Batalkan!',
		cancelButtonText: 'Tidak'
	}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				url: "<?php echo site_url('cuti/ajax_batalkan/')?>/" + id,
				type: "POST",
				dataType: "JSON",
				success: function(data) {
					if(data.status) {
						reload_table();
						Swal.fire('Berhasil!', data.message, 'success');
					} else {
						Swal.fire('Gagal!', data.message, 'error');
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					Swal.fire('Error!', 'Terjadi kesalahan saat memproses data', 'error');
				}
			});
		}
	});
}

function reload_table() {
	table.ajax.reload(null, false);
	load_umana_dropdown(); // Reload dropdown juga
}

</script>
