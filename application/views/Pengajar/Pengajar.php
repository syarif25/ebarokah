<div class="container-fluid">
	<div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Master</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)"></a></li>
        </ol>
    </div>
    <div class="row">
        <!-- Guru Stats -->
        <div class="col-xl-2 col-xxl-2 col-lg-2 col-sm-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-primary text-primary">
                            <i class="la la-users"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1 text-dark">Total Guru</p>
                            <h4 class="mb-0 text-black"><?= $stats['guru'] ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-xxl-2 col-lg-2 col-sm-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-info text-info">
                            <i class="la la-user"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1 text-dark">GTY</p>
                            <h4 class="mb-0 text-black"><?= $stats['GTY'] ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-xxl-2 col-lg-2 col-sm-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-warning text-warning">
                            <i class="la la-user"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1 text-dark">GTT</p>
                            <h4 class="mb-0 text-black"><?= $stats['GTT'] ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dosen Stats -->
        <div class="col-xl-2 col-xxl-2 col-lg-2 col-sm-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-success text-success">
                            <i class="la la-graduation-cap"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1 text-dark">Total Dosen</p>
                            <h4 class="mb-0 text-black"><?= $stats['dosen'] ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-xxl-2 col-lg-2 col-sm-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-info text-info">
                            <i class="la la-user-tie"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1 text-dark">DTY</p>
                            <h4 class="mb-0 text-black"><?= $stats['DTY'] ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-xxl-2 col-lg-2 col-sm-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="me-3 bgl-warning text-warning">
                            <i class="la la-user-tie"></i>
                        </span>
                        <div class="media-body">
                            <p class="mb-1 text-dark">DTT</p>
                            <h4 class="mb-0 text-black"><?= $stats['DTT'] ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Guru dan Dosen</h4>
                    <div class="d-flex align-items-center">
                        <small class="me-3 text-muted">
                            <i class="fa fa-info-circle me-1"></i> 
                            <span class="badge badge-primary light me-1"><i class="fa fa-users"></i></span>Keluarga &nbsp;
                            <span class="badge badge-success light me-1"><i class="fa fa-child"></i></span>Anak &nbsp;
                            <span class="badge badge-warning light me-1"><i class="fa fa-star"></i></span>Kehormatan &nbsp;
                            <span class="badge badge-danger light me-1"><i class="fa fa-certificate"></i></span>Jafung &nbsp;
                            <span class="badge badge-info light me-1"><i class="fa fa-chalkboard-teacher"></i></span>Wali Kelas
                        </small>
                        <a type="button" class="btn btn-rounded btn-info" href="<?php echo base_url() ?>Penempatan/add_pengajar"><span class="btn-icon-start text-info"><i class="fa fa-plus color-info"></i>
                            </span>Tambah Pengajar</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabel_view" class="table table-hover table-responsive-sm" >
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <!-- <th>JK</th> -->
                                    <th>Nama Lengkap</th>
                                    <th>Lembaga</th>
                                    <th>SKS/Jam</th>
                                    <th>Masa Pengabdian</th>
                                    <th>Tunjangan</th>
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