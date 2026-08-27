<div class="container-fluid">
	<div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Master</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Master Prodi</a></li>
        </ol>
    </div>
    <!-- row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Program Studi</h4>
                    <button type="button" class="btn btn-rounded btn-info" onclick="add_prodi()"><span class="btn-icon-start text-info"><i class="fa fa-plus color-info"></i>
                        </span>Tambah Prodi</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabel_view" class="table table-hover table-responsive-sm" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Fakultas</th>
                                    <th>Program Studi</th>
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
<div class="modal fade" id="modal_form">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Program Studi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <form action="#" id="form" class="form-horizontal">
                    <input type="hidden" value="" name="id_prodi"/> 
                    <div class="form-body">
                        
                        <div class="form-group row mb-2">
                            <label class="col-sm-4 col-form-label">Fakultas</label>
                            <div class="col-sm-8">
                                <select name="id_lembaga" class="default-select form-control wide">
                                    <option value="">-- Pilih Fakultas --</option>
                                    <?php foreach ($fakultas as $fk): ?>
                                    <option value="<?php echo $fk->id_lembaga; ?>"><?php echo $fk->nama_lembaga; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="help-block text-danger"></span>
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <label class="col-sm-4 col-form-label">Nama Prodi</label>
                            <div class="col-sm-8">
                                <input name="nama_prodi" class="form-control" type="text" placeholder="Masukkan Nama Prodi">
                                <span class="help-block text-danger"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btnSave" onclick="save()">Simpan</button>
            </div>
        </div>
    </div>
</div>
