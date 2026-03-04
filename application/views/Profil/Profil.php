<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Umana</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">Profile</a></li>
        </ol>
    </div>
    <!-- row -->
    
    <div class="row">
        <div class="col-xl-3">
            <div class="row">
                <!--<div class="col-xl-12">-->
                <!--    <div class="card">-->
                <!--        <div class="card-body">-->
                <!--            <div class="profile-statistics">-->
                <!--                <div class="text-center">-->
                <!--                    <div class="row">-->
                <!--                    <h5 class="text-primary d-inline">- </h5>-->
                <!--                        <div class="col">-->
                <!--                        <img src="<?php echo base_url(); ?>assets/cowok.png" alt="" class="img-fluid rounded " style="height: 120px;">-->
                <!--                        </div>-->
                <!--                    </div>-->
                                    
                <!--                </div>-->
                                
                <!--            </div>-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</div>-->
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header border-0 pb-0">
                            <h4 class="card-title">Riwayat Pengabdian</h4>
                        </div>
                        <div class="card-body">
                            <div id="DZ_W_TimeLine11" class="widget-timeline dlab-scroll style-1 height370 ps ps--active-y">
                                <ul class="timeline">
                                    <?php 
                                // Daftar kelas warna Bootstrap yang tersedia
		                        $colorClasses = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];

                                $nik = $this->session->userdata('nik');
                                
                                $data = $this->db->query("select penempatan.tgl_mulai, penempatan.tgl_selesai, nama_jabatan, jabatan_lembaga, nama_lembaga from penempatan, lembaga, umana, ketentuan_barokah where penempatan.id_lembaga = 
                                lembaga.id_lembaga and penempatan.nik = umana.nik and penempatan.id_ketentuan = ketentuan_barokah.id_ketentuan and umana.nik = $nik")->result();
                                foreach($data as $posisi){
                                    $randomColor = $colorClasses[rand(0, count($colorClasses) - 1)];
                                ?>
                                    <li>
                                        <div class="timeline-badge <?php echo $randomColor ?>"></div>
                                        <a class="timeline-panel text-muted" href="#">
                                            <span><?php echo date_lengkap($posisi->tgl_mulai).' / '.date_lengkap($posisi->tgl_selesai) ?></span>
                                            <h6 class="mb-0"><span><?php echo $posisi->nama_jabatan.' '.$posisi->jabatan_lembaga; ?>
                                            <br><strong class="text-<?php echo $randomColor ?>"><?php echo $posisi->nama_lembaga ?></strong>.</h6>
                                        </a>
                                    </li>
                                    <?php } ?>
                                   
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="card border-secondary">
                <div class="card-body ">
                    <div class="profile-blog">
                         <?php 
                        $nik = $this->session->userdata('nik');
                        $data = $this->db->get_where('umana', array('nik' => $nik))->result();
                        // $data = $this->db->query("select * from umana where nik = $nik ")->result();
                        foreach($data as $umana);
                        ?>
                        <h5 class="text-primary d-inline">Personal Information</h5>
                        <hr>
                        <!-- <img src="images/profile/1.jpg" alt="" class="img-fluid mt-4 mb-4 "> -->
                        <h4><a href="#" class="text-black"><?php echo $umana->gelar_depan.' '.$umana->nama_lengkap.' '.$umana->gelar_belakang; ?></a></h4>
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <div class="row mb-2">
                                    <div class="col-5">
                                        <h5 class="f-w-500">NIK </h5>
                                    </div>
                                    <div class="col-5"><span>: <?php echo $umana->nik; ?></span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5">
                                        <h5 class="f-w-500">Alamat Domisili </h5>
                                    </div>
                                    <div class="col-5"><span>: <?php echo $umana->alamat_domisili; ?></span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5">
                                        <h5 class="f-w-500">TMT Struktural </h5>
                                    </div>
                                    <div class="col-5"><span>: <?php if($umana->tmt_struktural == '0000-00-00')
                                    { echo "-"; }else {
                                    $tmt_struktur = date("Y", strtotime($umana->tmt_struktural)); echo $tmt_struktur; } ?></span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5">
                                        <h5 class="f-w-500">NIDN/NUPTK </h5>
                                    </div>
                                    <div class="col-5"><span>: <?php echo $umana->nidn; ?></span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5">
                                        <h5 class="f-w-500">Status Sertifikasi </h5>
                                    </div>
                                    <div class="col-5"><span>: <?php echo $umana->status_sertifikasi; ?></span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5">
                                        <h5 class="f-w-500">Status Pernikahan</h5>
                                    </div>
                                    <div class="col-5"><span>: <?php echo $umana->status_nikah; ?></span>
                                    </div>
                                </div>
                               
                            </div>
                            <div class="col-6">
                                <div class="row mb-2">
                                    <div class="col-5">
                                        <h5 class="f-w-500">NIY </h5>
                                    </div>
                                    <div class="col-7"><span>: <?php echo $umana->niy; ?></span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5">
                                        <h5 class="f-w-500">Nomor Hp </h5>
                                    </div>
                                    <div class="col-5"><span>: <?php echo $umana->nomor_hp; ?></span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5">
                                        <h5 class="f-w-500">Pend. Terakhir </h5>
                                    </div>
                                    <div class="col-5"><span>: <?php echo $umana->ijazah_terakhir; ?></span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5">
                                        <h5 class="f-w-500">TMT Guru </h5>
                                    </div>
                                    <div class="col-5"><span>: <?php if($umana->tmt_guru == '0000-00-00')
                                    { echo "-"; }else {
                                    date("Y", strtotime($umana->tmt_guru)); echo $tmt_guru; }?></span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5">
                                        <h5 class="f-w-500">TMT Dosen </h5>
                                    </div>
                                    <div class="col-5"><span>: <?php if($umana->tmt_dosen == '0000-00-00')
                                    { echo "-"; }else {$tmt_dosen = date("Y", strtotime($umana->tmt_dosen)); echo $tmt_dosen;} ?></span>
                                    </div>
                                </div>
                                <!--<div class="row mb-2">-->
                                <!--    <div class="col-5">-->
                                <!--        <h5 class="f-w-500">Jabatan Akademik </h5>-->
                                <!--    </div>-->
                                <!--    <div class="col-5"><span>: <?php echo $umana->jabatan_akademik; ?></span>-->
                                <!--    </div>-->
                                <!--</div>-->
                                <div class="row mb-2">
                                    <div class="col-5">
                                        <h5 class="f-w-500">Nomor rekening </h5>
                                    </div>
                                    <div class="col-5"><span>: <?php echo $umana->no_rekening; ?></span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5">
                                        <h5 class="f-w-500">Nama Bank </h5>
                                    </div>
                                    <div class="col-5"><span>: <?php echo $umana->nama_bank; ?></span>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <!-- <div class="">
                            <div class="profile-personal-info">
                                <div class="row mb-2">
                                    <div class="col-sm-3 col-5">
                                        <h5 class="f-w-500">Year Experience </h5>
                                    </div>
                                    <div class="col-sm-9 col-7"><span>07 Year Experiences</span>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <a href="#" onclick="edit_pwd(<?php echo $umana->nik ?>) " class="btn btn-primary light btn-rounded"><i class="fas fa-key scale5 me-3"></i>Edit Password</a>
                        <a href="#" class="btn btn-secondary  btn-rounded disabled"><i class="fas fa-user scale5 me-3"></i>Edit Data Diri</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
