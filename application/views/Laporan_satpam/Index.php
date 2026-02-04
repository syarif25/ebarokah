    <div class="container-fluid">
        <div class="row page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Laporan</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">Satpam</a></li>
            </ol>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Daftar Laporan Satpam</h4>
                        <p class="text-muted mb-0 mt-1"><i class="fas fa-info-circle"></i> Menampilkan daftar barokah yang telah dibayarkan untuk Satpam/Kamtib</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example3" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Bulan</th>
                                    <th>Tahun</th>
                                    <th>Status Laporan</th>
                                    <th class="text-right">Total Pencairan</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no=1; foreach($data_period as $d): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= $d->bulan ?></td>
                                    <td><?= $d->tahun ?></td>
                                    <td>
                                        <?php if($d->status == 'Transfer' || $d->status == 'Selesai'): ?>
                                            <span class="badge badge-success">Final <i class="fa fa-check ms-1"></i></span>
                                        <?php else: ?>
                                             <span class="badge badge-warning"><?= $d->status ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <?= "Rp " . number_format($d->jumlah_total, 0, ',', '.') ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('Laporan_satpam/rincian/'. $d->id_encrypted) ?>" class="btn btn-primary btn-sm">
                                            <i class="fa fa-eye me-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
