<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Log</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Rekap Total Barokah Umana</a></li>
        </ol>
    </div>
    <!-- row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Rekap Total Barokah Umana</h4>
                </div>
                <div class="card-body">

                    <!-- Filter Periode -->
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Pilih Bulan</label>
                            <select id="filter_bulan" class="form-control nice-select">
                                <option value="">-- Bulan --</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Pilih Tahun</label>
                            <select id="filter_tahun" class="form-control nice-select">
                                <option value="">-- Tahun --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Pilih Lembaga</label>
                            <select id="filter_lembaga" class="nice-select w-100">
                                <option value="">Semua Lembaga</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" id="btn_tampilkan" class="btn btn-primary w-100">
                                <i class="fas fa-search mr-1"></i> Tampilkan
                            </button>
                        </div>
                    </div>

                    <!-- Tabel DataTables -->
                    <div class="table-responsive">
                        <table id="tabel_rekap" class="table table-hover table-responsive-sm" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Umana'</th>
                                    <th>Struktural</th>
                                    <th>Mengajar</th>
                                    <th>Satpam</th>
                                    <th>Kepanitiaan</th>
                                    <th>Jumlah</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-end fw-bold">Total</th>
                                    <th id="footer_struktural" class="text-end fw-bold text-success">-</th>
                                    <th id="footer_mengajar"   class="text-end fw-bold text-success">-</th>
                                    <th id="footer_satpam"     class="text-end fw-bold text-success">-</th>
                                    <th id="footer_kepanitiaan" class="text-end fw-bold text-success">-</th>
                                    <th id="footer_jumlah"     class="text-end fw-bold text-success">-</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<!-- Modal Detail Umana -->
<div class="modal fade" id="modalDetailUmana" tabindex="-1" role="dialog" aria-labelledby="modalDetailUmanaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="modalDetailUmanaLabel">Detail Rincian Barokah Umana'</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-white" id="modalDetailBody">
                <!-- Header Profil -->
                <div id="detailHeaderProfil" class="mb-3"></div>
                
                <!-- Container Komponen -->
                <div id="detailContainerStruktural" class="mb-3"></div>
                <div id="detailContainerMengajar" class="mb-3"></div>
                <div id="detailContainerSatpam" class="mb-3"></div>
                <div id="detailContainerKepanitiaan" class="mb-3"></div>

                <!-- Block Total Keseluruhan -->
                <div id="detailBlockTotal"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
