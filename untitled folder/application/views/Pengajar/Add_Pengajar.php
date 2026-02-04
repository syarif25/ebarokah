<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Form</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Pengajar</a></li>
        </ol>
    </div>
    <!-- row -->
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Input Penempatan Pengajar </h4>
                </div>
                <div class="card-body">
                    <div class="basic-form">
                    <form id="form">
                        <div class="row">
                            <div class="mb-3 col-sm-3 input-success-o">
                                <label class="form-label">NIK</label>
                                <!-- <input type="text" class="form-control" name="nik"> -->
                                <select id="nik" class="form-control nik" name="nik">
                                    <option value=""></option>
                                    <?php 
                                    $query = $this->db->get('umana');
                                    if ($query->num_rows() > 0) {
                                    foreach ($query->result() as $row) {?>
                                    <option value="<?php echo $row->nik;?>"><?php echo $row->nik.' '.$row->nama_lengkap;?></option>
                                    <?php }
                                    } ?>
                                </select>
                                <input type="hidden" name="id_penempatan">
                            </div>
                            <div class="mb-3 col-sm-3 input-info-o">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" name="nama_lengkap" readonly>
                            </div>
                            <div class="mb-1 col-sm-2 input-info-o">
                                <label class="form-label">Ijazah Terakhir</label>
                                <input type="text" class="form-control" name="ijazah_terakhir" readonly>
                            </div>
                            <div class="mb-2 col-sm-2 input-info-o">
                                <label class="form-label">TMT Guru</label>
                                <input type="text" class="form-control" name="tmt_guru" readonly>
                            </div>
                            <div class="mb-2 col-sm-2 input-info-o">
                                <label class="form-label">TMT Dosen</label>
                                <input type="text" class="form-control" name="tmt_dosen" readonly>
                            </div>
                            <div class="mb-2 col-sm-2 input-success-o">
                                <label class="form-label">Kategori</label>
                                <!-- <input type="text" class="form-control" name="nik"> -->
                                <select id="" class="form-control" name="kategori">
                                    <option value=""></option>
                                    <option value="GTY">Guru Tetap</option>
                                    <option value="GTT">Guru Tidak Tetap</option>
                                    <option value="DTY">Dosen Tetap</option>
                                    <option value="DTT">Dosen Tidak Tetap</option>
                                </select>
                                <span class="help-block text-danger" style="color:red"></span>
                            </div>
                            <div class="mb-2 col-sm-2 input-success-o">
                                <label class="form-label">Jumlah SKS/ Jam Mengajar</label>
                                <input type="number" class="form-control" name="jumlah_sks">
                            </div>
                            <div class="mb-4 col-sm-4 input-success-o">
                                <label class="form-label">Lembaga</label>
                                <!-- <input type="text" class="form-control" name="potongan"> -->
                                <select  class="form-control" name="lembaga">
                                    <option value=""></option>
                                    <?php 
                                        $this->db->where('id_bidang', 'bidang dikti');
                                        $this->db->or_where('id_bidang', 'bidang dikjar');
                                         $this->db->or_where('id_bidang', 'bidang dikjar-M');
                                        $this->db->order_by('nama_lembaga', 'ASC'); 
                                        $query = $this->db->get('lembaga');

                                        if ($query->num_rows() > 0) {
                                            foreach ($query->result() as $row) {?>
                                                <option value="<?php echo $row->id_lembaga;?>"><?php echo $row->nama_lembaga;?></option>
                                            <?php }
                                        }
                                        ?>
                                </select>
                                <span class="help-block text-danger" style="color:red"></span>
                            </div>
                            <div class="mb-3 col-sm-2 input-success-o">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="tgl_mulai">
                            </div>
                            <div class="mb-3 col-sm-2 input-success-o">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" name="tgl_selesai">
                            </div>
                            <div class="mb-3 col-sm-2 input-success-o">
                                <label class="form-label">Tunkel </label>
                                <select name="tunkel" id="" class="form-control">
                                    <option value=""></option>
                                    <option value="Ya">Ya</option>
                                    <option value="Tidak">Tidak</option>
                                </select>
                                <span class="help-block text-danger" style="color:red"></span>
                            </div>
                            <div class="mb-3 col-sm-2 input-success-o">
                                <label class="form-label">Tunjangan Anak </label>
                                <select name="tunj_anak" id="" class="form-control">
                                    <option value=""></option>
                                    <option value="Ya">Ya</option>
                                    <option value="Tidak">Tidak</option>
                                </select>
                                <span class="help-block text-danger" style="color:red"></span>
                            </div>
                            <div class="mb-3 col-sm-2 input-success-o">
                                <label class="form-label">Kehormatan </label>
                                <select name="kehormatan" id="" class="form-control">
                                    <option value=""></option>
                                    <option value="Ya">Ya</option>
                                    <option value="Tidak">Tidak</option>
                                </select>
                                <span class="help-block text-danger" style="color:red"></span>
                            </div>
                            <div class="mb-3 col-sm-3 input-success-o">
                                <label class="form-label">Wali Kelas</label>
                                <!-- <input type="text" class="form-control" name="potongan"> -->
                                <select  class="form-control" name="walikelas">
                                   <option>Ya</option>
                                   <option>Tidak</option>
                                   <option value="walkes_sklh">Wali Kelas Sekolah</option>
                                </select>
                            </div>
                            <div class="mb-3 col-sm-3 input-success-o">
                                <label class="form-label">Jafung </label>
                                <select name="jafung" id="" class="form-control">
                                    <option value=""></option>
                                    <option value="Ya">Ya</option>
                                    <option value="Tidak">Tidak</option>
                                </select>
                                <span class="help-block text-danger" style="color:red"></span>
                            </div>
                            <div class="mb-5 col-sm-5 input-success-o">
                                <label class="form-label">Transport/Kehadiran </label>
                                <select id="" class="form-control" name="transport">
                                    <option value=""></option>
                                    <?php 
                                    $this->db->where('kategori_transport !=', 'struktural');
                                    $this->db->order_by('kategori_transport', 'ASC'); 
                                    $query = $this->db->get('transport');
                                    
                                    if ($query->num_rows() > 0) {
                                        foreach ($query->result() as $row) {
                                            // Format nominal_transport menjadi Rupiah
                                            $nominal_rupiah = number_format($row->nominal_transport, 0, ',', '.');
                                            ?>
                                            <option value="<?php echo $row->id_transport;?>">
                                                <b><?php echo $row->kategori_transport;?> | </b>
                                                <?php echo $row->nama_transport." | Rp ".$nominal_rupiah;?>
                                            </option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>

                                <span class="help-block text-danger" style="color:red"></span>
                            </div>
                            
                            <div class=" col-sm-4">
                                <label class="form-label">File SK (PDF)</label>
                                <div class="input-group mb-3">
                                    <div class="form-file">
                                        <input type="file" name="file" class="form-file-input form-control">
                                    </div>
                                </div>
                                <span class="help-block text-danger" style="color:red"></span>
                            </div>

                            <div class="mb-3 col-sm-3 input-success-o">
                                <label class="form-label">Status</label>
                                <!-- <input type="text" class="form-control" name="potongan"> -->
                                <select class="form-control" name="status">
                                   <option>Aktif</option>
                                   <option>Tidak Aktif</option>
                                </select>
                                <span class="help-block text-danger" style="color:red"></span>
                            </div>
                            <div class="mb-3 col-sm-9 input-success-o">
                            </div>
                            <div class="mb-3 col-sm-3 input-success-o">
                                <button type="button" class="btn btn-primary" id="btnSave" onclick="save()">Simpan</button>   
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>