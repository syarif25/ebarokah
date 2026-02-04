<div class="container-fluid">
	<div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Payroll</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)"></a>
                <a href="<?php base_url() ?> upload/bsi.xlsb" class="btn btn-sm btn-success text-white"><i class="fas fa-download mr-1" ></i> BSI</a> 
                <a href="<?php base_url() ?> upload/BRI.xls" class="btn btn-sm btn-primary text-white"><i class="fas fa-download mr-1" ></i> BRI</a>
                <a href="<?php base_url() ?> upload/BNI.xlsm" class="btn btn-sm btn-secondary text-white"><i class="fas fa-download mr-1" ></i> BNI</a>
            </li>
        </ol>
    </div>
    <!-- row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Barokah Perlembaga</h4>
                   
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabel_view" class="table table-hover table-responsive-sm" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Lembaga</th>
                                    <th>Kategori</th>
                                    <th>Periode</th>
                                    <th>Jumlah Nominal</th>
                                    <th>Status WA</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                                <tr class="filters">
                                    <th></th>
                                    <th><input type="text" class="form-control form-control-sm column-filter" placeholder="Cari lembaga..." data-column="1"></th>
                                    <th>
                                        <select class="form-control form-control-sm column-filter" data-column="2">
                                            <option value="">Semua Kategori</option>
                                            <option value="Struktural">Struktural</option>
                                            <option value="Satpam">Satpam</option>
                                            <option value="Pengajar">Pengajar</option>
                                        </select>
                                    </th>
                                    <th><input type="text" class="form-control form-control-sm column-filter" placeholder="Cari periode..." data-column="3"></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Lembaga</th>
                                    <th>Kategori</th>
                                    <th>Periode</th>
                                    <th>Jumlah Nominal</th>
                                    <th>Status WA</th>
                                    <th>Status</th>
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