<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Form</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Penempatan</a></li>
        </ol>
    </div>
    <!-- row -->
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Input Penempatan Umana</h4>
                </div>
                <div class="card-body">
                    <div class="basic-form">
                    <form id="form">
                        <div class="row">
                            <div class="mb-3 col-sm-4 input-success-o">
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
                            <div class="mb-3 col-sm-4 input-success-o">
                                <label class="form-label">Nomor Induk P2S3</label>
                                <input type="text" class="form-control" name="niy" readonly>
                                <span class="help-block text-danger" style="color:red"></span>
                            </div>
                            <div class="mb-3 col-sm-4 input-success-o">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" name="nama_lengkap" readonly>
                            </div>
                            <div class="mb-3 col-sm-4">
                                <label class="form-label">Jabatan/Rank Barokah</label>
                                <!-- <input type="text" class="form-control" name="nik"> -->
                                <select id="" class="form-control rank" name="id_ketentuan">
                                    <option value=""></option>
                                    <?php 
                                    $query = $this->db->get('ketentuan_barokah');
                                    if ($query->num_rows() > 0) {
                                    foreach ($query->result() as $row) {?>
                                    <option value="<?php echo $row->id_ketentuan;?>"><?php echo $row->nama_jabatan.' - '.$row->kategori;?></option>
                                    <?php }
                                    }; ?>
                                </select>
                                <span class="help-block text-danger" style="color:red"></span>
                            </div>
                            <div class="mb-3 col-sm-3 input-success-o">
                                <label class="form-label">Nama Jabatan</label>
                                <input type="text" class="form-control" name="jabatan_lembaga">
                            </div>
                            <div class="mb-3 col-sm-3 input-success-o">
                                <label class="form-label">Lembaga</label>
                                <!-- <input type="text" class="form-control" name="potongan"> -->
                                <select  class="form-control" name="lembaga">
                                    <option value=""></option>
                                    <?php 
                                    $query = $this->db->get('lembaga');
                                    if ($query->num_rows() > 0) {
                                    foreach ($query->result() as $row) {?>
                                    <option value="<?php echo $row->id_lembaga;?>"><?php echo $row->nama_lembaga;?></option>
                                    <?php }
                                    }; ?>
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
                                <label class="form-label">Transport/Kehadiran </label>
                                <select  class="form-control" name="transport">
                                    <option value=""></option>
                                    <?php 
                                    $query = $this->db->get('transport');
                                    if ($query->num_rows() > 0) {
                                    foreach ($query->result() as $row) {?>
                                    <option value="<?php echo $row->id_transport;?>"><?php echo $row->nama_transport." | <b> ".$row->kategori_transport."</b>";?></option>
                                    <?php }
                                    }; ?>
                                </select>
                                <span class="help-block text-danger" style="color:red"></span>
                            </div>
                            
                            <div class=" col-sm-4">
                                <label class="form-label">File SK</label>
                                <div class="input-group mb-3">
                                
                                    <div class="form-file">
                                        <input type="file" name="file_sk" class="form-file-input form-control">
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