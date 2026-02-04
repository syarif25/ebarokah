<div class="container-fluid">
	<div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Master</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Tahun Acuan</a></li>
        </ol>
    </div>
    <!-- row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Tahun Acuan</h4>
                    <button type="button" class="btn btn-rounded btn-info" onclick="add_data()"><span class="btn-icon-start text-info"><i class="fa fa-plus color-info"></i>
                        </span>Tambah Data</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabel_view" class="table table-hover table-responsive-sm" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Bidang</th>
                                    <th>Tahun Acuan</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Bidang</th>
                                    <th>Tahun Acuan</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="modal_form" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Tahun Acuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <form action="#" id="form">
                        <div class="card-body">
                            <input type="hidden" name="id" class="">
                            <div class="basic-form">
                                <label>Bidang</label>
                                <div class="input-group mb-3 input-success-o">
                                    <select name="id_bidang" class="form-control">
                                    <option value="">-- Pilih Bidang --</option>
                                    <option >Bidang DIKTI</option>
                                    <option >Bidang DIKJAR</option>
                                    <option >Bidang DIKJAR-M</option>
                                    <option >Bidang Kepesantrenan dan PU</option>
                                    <option >Bidang KAMTIB</option>
                                    <option>Bidang Usaha</option>
                                    <option >Sekretariat</option>
                                    <option >BPK2M</option>
                                    <option >Bendahara</option>
                                    <option >Pengurus</option>
                                    </select>
                                </div>
                                <span class="help-block text-danger" style="color:red"></span>
                            </div>

                            <div class="basic-form">
                                <label>Tahun Acuan</label>
                                <div class="input-group mb-3 input-success-o">
                                    <input type="number" name="tahun_acuan" class="form-control" placeholder="Contoh: 2026">
                                </div>
                                <span class="help-block text-danger" style="color:red"></span>
                            </div>

                            <div class="basic-form">
                                <label>Keterangan</label>
                                <div class="input-group mb-3 input-success-o">
                                    <input type="text" name="keterangan" class="form-control" placeholder="Keterangan">
                                </div>
                                <span class="help-block text-danger" style="color:red"></span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnSave" onclick="save()">Simpan</button>
            </div>
        </div>
    </div>
</div>
