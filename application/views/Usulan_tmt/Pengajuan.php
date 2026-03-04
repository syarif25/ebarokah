<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Usulan</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Perubahan SK / TMT</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Riwayat Usulan Perubahan Komponen Barokah</h4>
                    <button type="button" class="btn btn-rounded btn-info" onclick="add_usulan()">
                        <span class="btn-icon-start text-info"><i class="fa fa-plus color-info"></i></span>
                        Buat Usulan
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabel_view" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pegawai</th>
                                    <th>Rincian Usulan Perubahan</th>
                                    <th>Dokumen Lampiran</th>
                                    <th>Status Persetujuan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL USULAN -->
<div id="modal_usulan" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Pengajuan Perubahan Komponen Barokah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form" enctype="multipart/form-data">
                    <input type="hidden" name="id_usulan" id="id_usulan">
                    <div class="card bg-light p-3 mb-3">
                        <small class="text-dark">
                            <strong>Penting:</strong> Usulan perubahan komponen Barokah/SK ini ditujukan kepada <b>Bagian Evaluasi (Kantor Bendahara Pesantren)</b>. Perubahan akan aktif setelah data usulan diverifikasi dan di-ACC.
                        </small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-w600">Pilih Umana <span class="text-danger">*</span></label>
                            <select id="id_penempatan" name="id_penempatan" class="form-control select2" style="width: 100%;" required>
                                <option value="">Sedang memuat data...</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-w600">Terhitung Mulai Tanggal (TMT) <span class="text-danger">*</span></label>
                            <input type="date" id="tmt_baru" name="tmt_baru" class="form-control" required>
                        </div>
                    </div>
                    
                    <hr>
                    <h6 class="mb-3">Pengajuan Perubahan Komponen Barokah </h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-w600">Tunjangan Keluarga <span id="badge_tunj_kel" class="badge badge-sm badge-secondary ms-2">-</span></label>
                            <select id="tunj_kel_baru" name="tunj_kel_baru" class="form-control">
                                <option value="Ya">Ya (Menerima)</option>
                                <option value="Tidak">Tidak Menerima</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-w600">Tunjangan Anak <span id="badge_tunj_anak" class="badge badge-sm badge-secondary ms-2">-</span></label>
                            <select id="tunj_anak_baru" name="tunj_anak_baru" class="form-control">
                                <option value="Ya">Ya (Menerima)</option>
                                <option value="Tidak">Tidak Menerima</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-w600">Tunjangan MP <span id="badge_tunj_mp" class="badge badge-sm badge-secondary ms-2">-</span></label>
                            <select id="tunj_mp_baru" name="tunj_mp_baru" class="form-control">
                                <option value="Ya">Ya (Menerima)</option>
                                <option value="Tidak">Tidak Menerima</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-w600">Kehormatan <span id="badge_kehormatan" class="badge badge-sm badge-secondary ms-2">-</span></label>
                            <select id="kehormatan_baru" name="kehormatan_baru" class="form-control">
                                <option value="Ya">Ya (Menerima)</option>
                                <option value="Tidak">Tidak Menerima</option>
                            </select>
                        </div>
                    </div>
                    
                    <hr>
                    <div class="mb-3">
                        <label class="form-label font-w600">Upload Lampiran Bukti / SK Fisik <span class="text-danger">*</span></label>
                        <input type="file" name="file_dokumen" id="file_dokumen" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-danger d-block">File format PDF/JPG/PNG. Maksimal 5MB.</small>
                        <small id="lampiran_hint" class="text-warning d-none"> Kosongkan jika tidak ingin mengubah dokumen lampiran lama.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label font-w600">Keterangan / Alasan Tambahan</label>
                        <textarea name="keterangan_usulan" class="form-control" rows="4" placeholder="Keterangan Tambahan"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Tutup Lengkapi Nanti</button>
                <button type="button" class="btn btn-primary" id="btnSave" onclick="save()"><i class="fa fa-paper-plane mr-2"></i> Kirim Usulan</button>
            </div>
        </div>
    </div>
</div>
