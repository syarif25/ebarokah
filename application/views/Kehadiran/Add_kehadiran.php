 <!-- <div class="content-body"> -->
            <div class="container-fluid">
				<div class="row page-titles">
                    <?php 
                    $kode_lembaga = $this->db->query("SELECT lembaga.id_lembaga, kehadiran_lembaga.id_kehadiran_lembaga, bulan, tahun, nama_lembaga from kehadiran_lembaga, lembaga where kehadiran_lembaga.id_lembaga = lembaga.id_lembaga and kehadiran_lembaga.id_kehadiran_lembaga = '$kode' ")->result();
                    foreach($kode_lembaga as $kode_lemb);
                    ?>
					<ol class="breadcrumb">
						<li class="breadcrumb-item active"><a href="javascript:void(0)"></a></li>
						<li class="breadcrumb-item"><a href="javascript:void(0)">Kehadiran Bulan <?php echo  $kode_lemb->bulan." ".$kode_lemb->tahun?></a></li>
					</ol>
                </div>
                <!-- row -->
                <div class="row">
                   <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Kantor <?php echo $kode_lemb->nama_lembaga ?></h4>
                            </div>
                            <form action="#" id="form" enctype="multipart/form-data">
                                <div class="card-body">
                                    <div class="table-responsive table-scroll">
                                        <table id="tabel_rek" class="table table-striped table-responsive-sm">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Lengkap</th>
                                                    <th>Jabatan</th>
                                                    <th>Alamat</th>
                                                    <th style="width: 100px">Hadir</th>
                                                    <!-- <th style="width: 100px"> Ijin</th>
                                                    <th style="width: 100px">Alpha</th> -->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                
                                                $id = $kode_lemb->id_lembaga;
                                                $no=1;
                                                $list = $this->db->query("SELECT * from penempatan, lembaga, umana, ketentuan_barokah where penempatan.nik = umana.nik and penempatan.id_lembaga = lembaga.id_lembaga and penempatan.id_ketentuan = ketentuan_barokah.id_ketentuan and lembaga.id_lembaga = '$id' AND penempatan.tgl_selesai >= CURDATE()  order by ketentuan_barokah.id_ketentuan asc ")->result();
                                                 foreach($list as $key){ ?>
                                                <tr>
                                                    <td><?php echo $no++; ?><input type="hidden" name="nik_umana[]" value="<?php echo $key->nik ?>"><input type="hidden" name="id_penempatan[]" value="<?php echo $key->id_penempatan ?>"></td>
                                                    <td><?php echo $key->gelar_depan.' '.ucwords(strtolower($key->nama_lengkap)).' '.$key->gelar_belakang; ?></td>
                                                    <td><?php echo $key->nama_jabatan; ?></td>
                                                    <td><?php echo $key->nama_lembaga; ?></td>
                                                    <td>
                                                    <div class="input-group mb-3 input-success-o"><input type="number" name="jumlah_kehadiran[]" onchange="cekInputFile()" class="form-control input-success"></div></td>
                                                    <!-- <td><input type="text" class="form-control"></td>
                                                    <td><input type="text" class="form-control"></td> -->
                                                </tr>    
                                                <?php }?>
                                                <input type="hidden" value="<?php echo $kode_lemb->id_kehadiran_lembaga ?>" name="id_kehadiran_lembaga">
                                                <input type="hidden" value="<?php echo $kode_lemb->bulan ?>" name="bulan">
                                                <input type="hidden" value="<?php echo $kode_lemb->tahun ?>" name="tahun">
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row input-group mb-3 input-danger-o">
                                        <h3 class="text-danger">Upload File Absensi Lembaga (file PDF)</h3>
                                        <input type="file" name="file" id="file-input"  class="form-file-input form-control" accept="application/pdf">
                                        <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                    
                                    <br>
                                    <input type="checkbox" id="cek" onchange="cekInputFile()" > <label for="cek">Sudah Lengkap (Total Presensi + Upload File)</label>
                                    <div class="row">
                                        <div>
                                            <button type="button" class="btn btn-rounded btn-primary" id="btnSave" onclick="save()"><span class="btn-icon-start text-primary"><i class="fa fa-save"></i>
                                            </span>Simpan</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <!-- </div> -->