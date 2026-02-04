<div class="container-fluid">
				
		<div class="row page-titles">
			<ol class="breadcrumb">
				<li class="breadcrumb-item active"><a href="javascript:void(0)">Manajemen</a></li>
				<li class="breadcrumb-item"><a href="javascript:void(0)">Cuti Umana</a></li>
			</ol>
		</div>
		
		<!-- row -->
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title">Daftar Cuti Umana</h4>
						<button type="button" class="btn btn-rounded btn-primary" onclick="add_cuti()">
							<span class="btn-icon-start text-primary"><i class="fa fa-plus color-primary"></i></span>
							Tambah Cuti
						</button>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table id="tabel_cuti" class="display table-striped" style="min-width: 845px">
								<thead>
									<tr>
										<th>No</th>
										<th></th>
										<th>Nama Umana</th>
										<th>Jenis Cuti</th>
										<th>Periode</th>
										<th>Sisa Hari</th>
										<th>Status</th>
										<th>Aksi</th>
									</tr>
								</thead>
								<tbody>
									
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

<!-- Modal Add/Edit -->
<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="modalFormLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalFormLabel">Form Cuti Umana</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form action="#" id="form" class="form-horizontal">
				<div class="modal-body">
					<input type="hidden" value="" name="id_cuti"/>
					
					<div class="form-group row mb-3">
						<label class="col-sm-3 col-form-label">Umana <span class="text-danger">*</span></label>
						<div class="col-sm-9">
							<select class="form-control select2" name="nik" id="nik" style="width: 100%;">
								<option value="">-- Pilih Umana --</option>
							</select>
							<span class="help-block text-danger"></span>
						</div>
					</div>

					<div class="form-group row mb-3">
						<label class="col-sm-3 col-form-label">Jenis Cuti <span class="text-danger">*</span></label>
						<div class="col-sm-9">
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="jenis_cuti" id="cuti50" value="Cuti 50%">
								<label class="form-check-label" for="cuti50">Cuti 50%</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="jenis_cuti" id="cuti100" value="Cuti 100%">
								<label class="form-check-label" for="cuti100">Cuti 100%</label>
							</div>
							<span class="help-block text-danger"></span>
						</div>
					</div>

					<div class="form-group row mb-3">
						<label class="col-sm-3 col-form-label">Tanggal Mulai <span class="text-danger">*</span></label>
						<div class="col-sm-9">
							<input type="date" class="form-control" name="tanggal_mulai" id="tanggal_mulai">
							<span class="help-block text-danger"></span>
						</div>
					</div>

					<div class="form-group row mb-3">
						<label class="col-sm-3 col-form-label">Tanggal Selesai <span class="text-danger">*</span></label>
						<div class="col-sm-9">
							<input type="date" class="form-control" name="tanggal_selesai" id="tanggal_selesai">
							<small class="form-text text-muted">Maksimal 3 bulan (90 hari) dari tanggal mulai</small>
							<span class="help-block text-danger"></span>
							<div id="durasi_info" class="mt-2"></div>
						</div>
					</div>

					<div class="form-group row mb-3">
						<label class="col-sm-3 col-form-label">Keterangan</label>
						<div class="col-sm-9">
							<textarea class="form-control" name="keterangan" rows="3" placeholder="Keterangan tambahan (opsional)"></textarea>
							<span class="help-block text-danger"></span>
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
					<button type="button" onclick="save()" class="btn btn-primary">Simpan</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal View Detail -->
<div class="modal fade" id="modal_view" tabindex="-1" role="dialog" aria-labelledby="modalViewLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalViewLabel">Detail Cuti Umana</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" id="view_content">
				<!-- Content will be loaded here -->
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>
