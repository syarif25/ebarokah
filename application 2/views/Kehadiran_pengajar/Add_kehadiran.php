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
                                <h4 class="card-title">Lembaga <?php echo $kode_lemb->nama_lembaga ?></h4>
                            </div>
                            <form action="#" id="form" enctype="multipart/form-data">
                                <div class="card-body">
                                    <div class="table-responsive table-scroll">
                                        <table id="tabel_rek" class="table table-striped table-responsive-sm">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Lengkap</th>
                                                    <th>Jumlah SKS/Jam</th>
                                                    <!-- <th>Alamat</th> -->
                                                    <th style="width: 100px">Hadir Normal <br> <span>(sesuai domisili)</span> </br></th>
                                                    <th style="width: 100px">Hadir 15.000</th>
                                                    <th style="width: 100px">Hadir 10.000</th>
                                                    <th style="width: 100px">Jam <br> Piket</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $id = $kode_lemb->id_lembaga;
                                                $no=1;
                                                $list = $this->db->query("
                                                    SELECT 
                                                        pengajar.*, 
                                                        lembaga.*, 
                                                        umana.*, 
                                                        transport.*
                                                    FROM pengajar
                                                    JOIN umana ON pengajar.nik = umana.nik
                                                    JOIN lembaga ON pengajar.id_lembaga = lembaga.id_lembaga
                                                    JOIN transport ON pengajar.kategori_trans = transport.id_transport
                                                    WHERE 
                                                        lembaga.id_lembaga = $id
                                                        AND pengajar.tgl_mulai <= CURDATE()
                                                        AND (pengajar.tgl_selesai IS NULL OR pengajar.tgl_selesai >= CURDATE())
                                                        AND pengajar.status IN ('Aktif', 'Cuti 50%', 'Cuti 100%')
                                                    ORDER BY umana.nama_lengkap ASC
                                                ")->result();

                                                foreach ($list as $key): 
                                                    // Tentukan badge status
                                                    $badge = '';
                                                    if ($key->status === 'Cuti 50%') {
                                                        $badge = " <span class='badge bg-warning text-dark'>Cuti </span>";
                                                    } elseif ($key->status === 'Cuti 100%') {
                                                        $badge = " <span class='badge bg-danger'>Cuti </span>";
                                                    }
                                                ?>
                                                <tr>
                                                    <td><?php echo $no++; ?>
                                                        <input type="hidden" name="nik_umana[]" value="<?php echo $key->nik ?>">
                                                        <input type="hidden" name="id_penempatan[]" value="<?php echo $key->id_pengajar ?>">
                                                    </td>
                                                    <td>
                                                        <?php echo $key->gelar_depan.' '.ucwords(strtolower($key->nama_lengkap)).' '.$key->gelar_belakang . $badge; ?>
                                                    </td>
                                                    <td><?php echo $key->jumlah_sks; ?></td>
                                                    <td><input type="number" name="jumlah_kehadiran[]" class="form-control input-success"></td>
                                                    <td><input type="number" name="jumlah_kehadiran_15[]" class="form-control input-success"></td>
                                                    <td><input type="number" name="jumlah_kehadiran_10[]" class="form-control input-success"></td>
                                                    <td><input type="number" name="jumlah_kehadiran_piket[]" class="form-control input-success"></td>
                                                </tr>
                                                <?php endforeach; ?>

                                        </table>
                                    </div>
                                    <div class="row input-group mb-3 input-danger-o">
                                        <h3 class="text-danger">Upload File Absensi Lembaga</h3>
                                        <input type="file" name="file" id="file-input"  class="form-file-input form-control" accept="application/pdf">
                                        <span class="help-block text-danger" style="color:red"></span>
                                    </div>
                                        <input type="hidden" name="id_kehadiran_lembaga" value="<?php echo $kode_lemb->id_kehadiran_lembaga; ?>">
                                        <input type="hidden" name="bulan" value="<?php echo $kode_lemb->bulan; ?>">
                                        <input type="hidden" name="tahun" value="<?php echo $kode_lemb->tahun; ?>">
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