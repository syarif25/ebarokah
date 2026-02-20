 <!-- <div class="content-body"> -->
 <div class="container-fluid">
    <style>
        .table-scroll {
            max-height: 70vh;
            overflow-y: auto;
            position: relative;
            border: 1px solid #eee;
        }
        .table-scroll thead th {
            position: sticky;
            top: 0;
            background-color: #f8f9fa; /* Light gray background */
            z-index: 10;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
            vertical-align: middle !important;
            text-align: center;
        }
        .form-control-sm-custom {
            height: 35px;
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            border-radius: 0.2rem;
            text-align: center;
            font-weight: bold;
        }
        .card-header-custom {
            background-color: #fff;
            border-bottom: 1px solid #eaeaea;
            padding: 1.5rem;
        }
        .lembaga-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.2rem;
        }
        .periode-subtitle {
            font-size: 1rem;
            color: #666;
            font-weight: 500;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }
        /* Grouping columns visual cue */
        .border-left-custom {
            border-left: 2px solid #dee2e6 !important;
        }
        /* Input focus effect */
        .form-control:focus {
            background-color: #e8f0fe;
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }
    </style>

    <div class="row page-titles">
        <?php 
        $kode_lembaga = $this->db->query("SELECT lembaga.id_lembaga, kehadiran_lembaga.id_kehadiran_lembaga, bulan, tahun, nama_lembaga from kehadiran_lembaga, lembaga where kehadiran_lembaga.id_lembaga = lembaga.id_lembaga and kehadiran_lembaga.id_kehadiran_lembaga = '$kode' ")->result();
        foreach($kode_lembaga as $kode_lemb);
        ?>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Kehadiran</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Isi Rekap</a></li>
        </ol>
    </div>
    <!-- row -->
    <div class="row">
       <div class="col-lg-12">
            <div class="card">
                <!-- Custom Beautiful Header -->
                <div class="card-header-custom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="lembaga-title"><i class="fa fa-university text-primary me-2"></i><?php echo $kode_lemb->nama_lembaga ?></h2>
                            <div class="periode-subtitle"><i class="fa fa-calendar me-2"></i>Periode: <span class="text-primary"><?php echo strtoupper($kode_lemb->bulan)." ".$kode_lemb->tahun?></span></div>
                        </div>
                        <div>
                            <!-- Placeholder for future action buttons -->
                        </div>
                    </div>
                </div>
                
                <form action="#" id="form" enctype="multipart/form-data">
                    <div class="card-body p-0"> <!-- Remove padding for full-width table look inside card -->
                        <div class="table-responsive table-scroll">
                            <table id="tabel_rek" class="table table-hover table-bordered mb-0"> <!-- Added table-bordered and mb-0 -->
                                <thead class="text-center">
                                    <tr>
                                        <th style="min-width: 50px;">No</th>
                                        <th style="min-width: 250px; text-align: left; padding-left: 20px;">Nama Lengkap</th>
                                        <th style="min-width: 100px;">Jml SKS</th>
                                        
                                        <!-- Grouping Kehadiran Inputs -->
                                        <th style="min-width: 120px;" class="border-left-custom">
                                            Hadir Normal<br><small class="text-muted">(Domisili)</small>
                                        </th>
                                        <th style="min-width: 120px;">
                                            Hadir 15rb
                                        </th>
                                        <th style="min-width: 120px;">
                                            Hadir 10rb
                                        </th>
                                        <th style="min-width: 120px;">
                                            Jam Piket
                                        </th>
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
                                        $row_class = '';
                                        if ($key->status === 'Cuti 50%') {
                                            $badge = " <span class='badge badge-xs badge-warning ml-2'>Cuti 50%</span>";
                                            $row_class = 'table-warning';
                                        } elseif ($key->status === 'Cuti 100%') {
                                            $badge = " <span class='badge badge-xs badge-danger ml-2'>Cuti 100%</span>";
                                            $row_class = 'table-danger';
                                        }
                                    ?>
                                    <tr class="<?php echo $row_class; ?>">
                                        <td class="text-center"><?php echo $no++; ?>
                                            <input type="hidden" name="nik_umana[]" value="<?php echo $key->nik ?>">
                                            <input type="hidden" name="id_penempatan[]" value="<?php echo $key->id_pengajar ?>">
                                        </td>
                                        <td style="font-weight: 500;">
                                            <?php echo $key->gelar_depan.' '.ucwords(strtolower($key->nama_lengkap)).' '.$key->gelar_belakang . $badge; ?>
                                            <br>
                                            <small class="text-muted"><?php echo $key->kategori ?></small>
                                        </td>
                                        <td class="text-center"><?php echo $key->jumlah_sks; ?></td>
                                        
                                        <!-- Inputs -->
                                        <td class="border-left-custom">
                                            <input type="number" name="jumlah_kehadiran[]" class="form-control form-control-sm-custom input-success calc-trigger" min="0" placeholder="0">
                                        </td>
                                        <td>
                                            <input type="number" name="jumlah_kehadiran_15[]" class="form-control form-control-sm-custom input-success calc-trigger" min="0" placeholder="0">
                                        </td>
                                        <td>
                                            <input type="number" name="jumlah_kehadiran_10[]" class="form-control form-control-sm-custom input-success calc-trigger" min="0" placeholder="0">
                                        </td>
                                        <td>
                                            <input type="number" name="jumlah_kehadiran_piket[]" class="form-control form-control-sm-custom input-success calc-trigger" min="0" placeholder="0">
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="bg-light font-weight-bold">
                                    <tr>
                                        <td colspan="3" class="text-end text-uppercase">Total Kehadiran :</td>
                                        <td class="text-center border-left-custom">
                                            <span id="total_hadir_normal">0</span>
                                        </td>
                                        <td class="text-center">
                                            <span id="total_hadir_15">0</span>
                                        </td>
                                        <td class="text-center">
                                            <span id="total_hadir_10">0</span>
                                        </td>
                                        <td class="text-center">
                                            <span id="total_hadir_piket">0</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <div class="card-footer bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fa fa-cloud-upload text-danger"></i></span>
                                        <input type="file" name="file" id="file-input" class="form-control border-start-0" accept="application/pdf">
                                    </div>
                                    <small class="text-danger mt-1 d-block">*Wajib upload file absensi lembaga (PDF)</small>
                                </div>
                                <div class="col-md-6 text-end">
                                     <input type="hidden" name="id_kehadiran_lembaga" value="<?php echo $kode_lemb->id_kehadiran_lembaga; ?>">
                                    <input type="hidden" name="bulan" value="<?php echo $kode_lemb->bulan; ?>">
                                    <input type="hidden" name="tahun" value="<?php echo $kode_lemb->tahun; ?>">
                                    
                                    <div class="d-flex align-items-center justify-content-end gap-3">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" id="cek" onchange="cekInputFile()">
                                            <label class="form-check-label" for="cek">
                                               Saya menyatakan data sudah lengkap
                                            </label>
                                        </div>
                                        <button type="button" class="btn btn-primary btn-rounded px-4" id="btnSave" onclick="save()">
                                            <i class="fa fa-save me-2"></i>Simpan Validasi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div> <!-- End Card Body -->
                </form>
            </div>
        </div>
    </div>
</div>