<div class="container-fluid">
				
				<div class="row page-titles">
					<ol class="breadcrumb">
						<li class="breadcrumb-item active"><a href="javascript:void(0)">-</a></li>
						<li class="breadcrumb-item"><a href="javascript:void(0)">Personalia</a></li>
					</ol>
                </div>
                <!-- row -->


                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <?php if ($this->session->userdata('jabatan') == 'AdminLembaga') { ?>
                                <h4 class="card-title">Daftar Personalia <?php echo $nama_lembaga; ?></h4>
                                <?php } else { ?>
                                     <h4 class="card-title">Daftar Personalia </h4>
                                <?php } ?>
                                <!-- <button type="button" class="btn btn-rounded btn-info" onclick="add_pengguna()"><span class="btn-icon-start text-info"><i class="fa fa-plus color-info"></i>
                                    </span>Tambah Pengguna</button> -->
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-container">
                                    <table id="tabel_view" class="table table-striped table-responsive-sm" >
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Lengkap</th>
                                                <th>Jabatan</th>
                                                <th>lembaga</th>
                                                <th>Tunkel</th>
                                                <th>Tunj Anak</th>
                                                <th>Kehormatan</th>
                                                <th>Sertifikasi</th>
                                                <th>Tunjangan</th>
                                                <th>Potongan</th>
                                                <!--<th>KTP</th>-->
                                                <!--<th>KK</th>-->
                                                <!--<th>SK</th>-->
                                                <!--<th>Jml Anak</th>-->
                                                <!--<th>Aksi</th>  -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td>No</td>
                                                <td>Nama Lengkap</td>
                                                <td>Jabatan</td>
                                                <td>Lembaga</td>
                                                <td>Tunkel</td>
                                                <td>Tunj Anak</td>
                                                <td>Kehormatan</td>
                                                <td>Sertifikasi</td>
                                                <td>Tunjangan</td>
                                                <td>Potongan</td>
                                                <!--<td>KTP</td>-->
                                                <!--<td>KK</td>-->
                                                <!--<td>SK</td>-->
                                                <!--<td>Jml Anak</td>-->
                                                <!--<td>Aksi</td> -->
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
				</div>
            </div>