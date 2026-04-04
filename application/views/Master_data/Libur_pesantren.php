<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active"><a href="javascript:void(0)">Master Data</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0)">Libur Pesantren/Nasional</a></li>
    </ol>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Data Pengurangan Libur Kehadiran</h4>
                <button type="button" class="btn btn-primary btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#modalAdd" onclick="add_form()">
                    <i class="fa fa-plus"></i> Tambah Data
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="display" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Bulan</th>
                                <th>Tahun</th>
                                <th>Jumlah Libur</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=1; foreach($libur as $l){ ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo ucfirst($l->bulan); ?></td>
                                <td><?php echo $l->tahun; ?></td>
                                <td><?php echo $l->jumlah_hari; ?> Hari</td>
                                <td><?php echo $l->keterangan; ?></td>
                                <td>
                                    <div class="d-flex">
                                        <button class="btn btn-primary shadow btn-xs sharp me-1" onclick="edit_form('<?php echo $l->id_libur ?>', '<?php echo $l->bulan ?>', '<?php echo $l->tahun ?>', '<?php echo $l->jumlah_hari ?>', '<?php echo $l->keterangan ?>')" data-bs-toggle="modal" data-bs-target="#modalAdd">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        <a href="<?php echo base_url('Libur_pesantren/hapus/'.$l->id_libur) ?>" onclick="return confirm('Yakin ingin menghapus rekap libur ini?')" class="btn btn-danger shadow btn-xs sharp">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>												
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add/Edit -->
<div class="modal fade" id="modalAdd">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Data Libur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo base_url('Libur_pesantren/simpan') ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id_libur" id="id_libur">
                    <div class="mb-3">
                        <label>Bulan Berjalan</label>
                        <select name="bulan" id="bulan" class="form-control default-select" required>
                            <option value="">-- Pilih Bulan --</option>
                            <option value="Januari">Januari</option>
                            <option value="Februari">Februari</option>
                            <option value="Maret">Maret</option>
                            <option value="April">April</option>
                            <option value="Mei">Mei</option>
                            <option value="Juni">Juni</option>
                            <option value="Juli">Juli</option>
                            <option value="Agustus">Agustus</option>
                            <option value="September">September</option>
                            <option value="Oktober">Oktober</option>
                            <option value="November">November</option>
                            <option value="Desember">Desember</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Tahun Berjalan</label>
                        <input type="number" name="tahun" id="tahun" class="form-control" value="<?php echo date('Y') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Jumlah Hari (Di luar hari jumat)</label>
                        <input type="number" name="jumlah_hari" id="jumlah_hari" class="form-control" placeholder="Contoh: 10" required>
                        <small class="text-danger">* Angka libur ini akan otomatis memotong target kehadiran seluruh lembaga bulan tersebut.</small>
                    </div>
                    <div class="mb-3">
                        <label>Keterangan Acara/Libur</label>
                        <input type="text" name="keterangan" id="keterangan" class="form-control" placeholder="Contoh: Libur Hari Raya" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if($this->session->flashdata('pesan')){ ?>
<script>
    document.addEventListener("DOMContentLoaded", function(event) { 
        if(typeof notif === 'function') {
            notif('<?php echo $this->session->flashdata('pesan') ?>');
        }
    });
</script>
<?php } ?>

<script>
function add_form(){
    document.getElementById("modalTitle").innerHTML = "Tambah Data Libur Tambahan";
    document.getElementById("id_libur").value = "";
    document.getElementById("bulan").value = "";
    document.getElementById("tahun").value = "<?php echo date('Y') ?>";
    document.getElementById("jumlah_hari").value = "";
    document.getElementById("keterangan").value = "";
}

function edit_form(id, bulan, tahun, jumlah_hari, keterangan){
    document.getElementById("modalTitle").innerHTML = "Edit Data Libur Tambahan";
    document.getElementById("id_libur").value = id;
    
    // Capitalize bulan string matching option values
    var formattedBulan = bulan.charAt(0).toUpperCase() + bulan.slice(1).toLowerCase();
    document.getElementById("bulan").value = formattedBulan;
    
    document.getElementById("tahun").value = tahun;
    document.getElementById("jumlah_hari").value = jumlah_hari;
    document.getElementById("keterangan").value = keterangan;
}
</script>
