<!-- <div class="content-body"> -->
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)"></a></li> 
            <!-- <?php foreach($isilist as $periode){} ?> -->
            <li class="breadcrumb-item"><a href="javascript:void(0)">Kehadiran Tahun Anggaran <?php echo $periode->tahun; ?></a></li>
        </ol>
    </div>
    <!-- row -->
    <div class="row">
        <div class="col-lg-12">
						<div class="card">
							<div class="card-header">
            					<div class="col-4 text-center">
            						<a class="btn btn-danger btn-lg btn-rounded mb-2" href="<?php echo base_url()?>Laporan/kehadiran_pengajar"> Struktural</a>
            						</div>
            						<div class="col-4">
            							<a class="btn light btn-warning btn-lg btn-rounded mb-2" href="<?php echo base_url()?>Laporan/kehadiran_pengajar" > Pengajar</a>
            						</div>
                                    <div class="col-4">
            							<a class="btn light btn-info btn-lg btn-rounded mb-2" href="<?php echo base_url()?>Laporan/kehadiran_pengajar" > Lainnya</a>
            						</div>
            					</div>
            				</div>
						</div>
					</div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Riwayat Transaksi</h4>
                    <a type="button" href="#" onclick="#" class="btn btn-info dark px-3" >
                        <i class="fa fa-filter"></i> Filter<b class="caret m-l-5"></b>
                    </a>
                </div>
                <div class="card-body">
                    <div id="dataContainer_list"></div>
                    <!--<div class="table-responsive">-->
                    <!--    <table class="table table-responsive-md">-->
                    <!--        <thead>-->
                    <!--            <tr>-->
                    <!--                <th><strong>Lembaga</strong></th>-->
                    <!--                <th><strong>Jumlah</strong></th>-->
                    <!--                <th><strong>Status</strong></th>-->
                    <!--                <th>#</th>-->
                    <!--            </tr>-->
                    <!--        </thead>-->
                    <!--        <tbody>-->
                              
                                <!--<tr>-->
                                    <!-- <td><strong><?php echo $no++; ?></strong></td> -->
           <!--                         <td><strong><?php echo htmlentities($key->nama_lembaga); ?></strong> <br> <?php echo htmlentities($key->bulan." ".$key->tahun); ?></td>-->
           <!--                         <td><?php echo htmlentities(rupiah($jumlah_barokah)); ?></td>-->
           <!--                         <td><div class="d-flex align-items-center"><i class="fa fa-circle text-success me-1"></i> Sukses</div></td>-->
           <!--                         <td>-->
           <!--                             <a type="button" href="#" onclick="detail_barokah(<?php echo $key->id_total_barokah_pengajar; ?>)" class="btn btn-primary light px-3" >-->
											<!--<i class="fa fa-eye"></i> Detail<b class="caret m-l-5"></b>-->
           <!--                             </a>-->
           <!--                         </td>-->
           <!--                     </tr>-->
                                
                        <!--    </tbody>-->
                        <!--</table>-->
                    <!--</div>-->
                </div>
            </div>
        </div>
    </div>

   
</div>
<!-- </div> -->