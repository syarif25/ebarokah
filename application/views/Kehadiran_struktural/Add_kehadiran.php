 <!-- CATATAN IMPLEMENTASI -->
<?php
/*
 * File ini sudah diupdate untuk include:
 * - Data periode dari controller ($periode)
 * - Data pegawai dari controller ($pegawai)  
 * - 3 kolom input: Hadir, Izin, Sakit
 * - Stepper buttons untuk setiap input
 * - Keyboard navigation support
 * by Syarif AK
 */
?>
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)"></a></li>
            <li class="breadcrumb-item">
                <a href="javascript:void(0)">
                    Kehadiran Struktural - <?php echo $periode->bulan." ".$periode->tahun?>
                </a>
            </li>
        </ol>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Kantor <?php echo $periode->nama_lembaga ?></h4>
                    <p class="text-muted mb-0">
                        <i class="fa fa-info-circle"></i> 
                      
                    </p>
                </div>
                
                <form action="#" id="form" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tabel_rek" class="table table-striped table-hover">
                                <thead class="">
                                    <tr>
                                        <th style="width: 40px;">No</th>
                                        <th>Nama Lengkap</th>
                                        <th>Jabatan</th>
                                        <th style="width: 120px;">Lembaga</th>
                                        <th style="width: 160px;" class="text-center">
                                            <i class="fa fa-check-circle"></i> Hadir
                                        </th>
                                        <th style="width: 160px;" class="text-center">
                                            <i class="fa fa-briefcase"></i> Tugas
                                        </th>
                                        <th style="width: 160px;" class="text-center">
                                            <i class="fa fa-file-alt"></i> Izin
                                        </th>
                                        <th style="width: 160px;" class="text-center">
                                            <i class="fa fa-medkit"></i> Sakit
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach($pegawai as $key): 
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td>
                                            <?php echo $key->gelar_depan.' '.ucwords(strtolower($key->nama_lengkap)).' '.$key->gelar_belakang; ?>
                                            <input type="hidden" name="nik_umana[]" value="<?php echo $key->nik ?>">
                                            <input type="hidden" name="id_penempatan[]" value="<?php echo $key->id_penempatan ?>">
                                        </td>
                                        <td><?php echo $key->nama_jabatan; ?></td>
                                        <td><?php echo $key->nama_lembaga; ?></td>
                                        
                                        <!-- HADIR dengan Stepper -->
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <button class="btn btn-outline-secondary" type="button" 
                                                        onclick="decrementInput(this)">−</button>
                                                <input type="number" 
                                                       name="jumlah_hadir[]" 
                                                       class="form-control text-center attendance-input" 
                                                       min="0" 
                                                       step="1" 
                                                       value="0"
                                                       data-field="hadir"
                                                       onchange="validateInput(this)">
                                                <button class="btn btn-outline-secondary" type="button" 
                                                        onclick="incrementInput(this)">+</button>
                                            </div>
                                        </td>
                                        
                                        <!-- TUGAS dengan Stepper -->
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <button class="btn btn-outline-secondary" type="button" 
                                                        onclick="decrementInput(this)">−</button>
                                                <input type="number" 
                                                       name="jumlah_tugas[]" 
                                                       class="form-control text-center attendance-input" 
                                                       min="0" 
                                                       step="1" 
                                                       value="0"
                                                       data-field="tugas"
                                                       onchange="validateInput(this)">
                                                <button class="btn btn-outline-secondary" type="button" 
                                                        onclick="incrementInput(this)">+</button>
                                            </div>
                                        </td>
                                        
                                        <!-- IZIN dengan Stepper -->
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <button class="btn btn-outline-secondary" type="button" 
                                                        onclick="decrementInput(this)">−</button>
                                                <input type="number" 
                                                       name="jumlah_izin[]" 
                                                       class="form-control text-center attendance-input" 
                                                       min="0" 
                                                       step="1" 
                                                       value="0"
                                                       data-field="izin"
                                                       onchange="validateInput(this)">
                                                <button class="btn btn-outline-secondary" type="button" 
                                                        onclick="incrementInput(this)">+</button>
                                            </div>
                                        </td>
                                        
                                        <!-- SAKIT dengan Stepper -->
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <button class="btn btn-outline-secondary" type="button" 
                                                        onclick="decrementInput(this)">−</button>
                                                <input type="number" 
                                                       name="jumlah_sakit[]" 
                                                       class="form-control text-center attendance-input" 
                                                       min="0" 
                                                       step="1" 
                                                       value="0"
                                                       data-field="sakit"
                                                       onchange="validateInput(this)">
                                                <button class="btn btn-outline-secondary" type="button" 
                                                        onclick="incrementInput(this)">+</button>
                                            </div>
                                        </td>
                                    </tr>    
                                    <?php endforeach; ?>
                                    
                                    <!-- Hidden fields untuk periode -->
                                    <input type="hidden" value="<?php echo $periode->id_kehadiran_lembaga ?>" name="id_kehadiran_lembaga">
                                    <input type="hidden" value="<?php echo $periode->bulan ?>" name="bulan">
                                    <input type="hidden" value="<?php echo $periode->tahun ?>" name="tahun">
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Upload PDF -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-danger" role="alert">
                                    <h5 class="alert-heading">
                                        <i class="fa fa-upload"></i> Upload File Absensi (PDF)
                                    </h5>
                                    <hr>
                                    <input type="file" 
                                           name="file" 
                                           id="file-input" 
                                           class="form-control" 
                                           accept="application/pdf"
                                           onchange="checkFileUploaded()">
                                    <small class="form-text text-muted">
                                        File PDF wajib diunggah sebelum menyimpan. Max 10MB.
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="button" 
                                        class="btn btn-primary btn-lg" 
                                        id="btnSave" 
                                        onclick="save()"
                                        disabled>
                                    <i class="fa fa-save"></i> Simpan Kehadiran
                                </button>
                                <a href="<?php echo site_url('kehadiran_struktural')?>" 
                                   class="btn btn-secondary btn-lg">
                                    <i class="fa fa-times"></i> Batal
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>