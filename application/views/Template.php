<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="keywords" content="" />
	<meta name="author" content="" />
	<meta name="robots" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1">

	
	<!-- PAGE TITLE HERE -->
	<title>E-Barokah Ponpes Salafiyah Syafi'iyah Sukorejo</title>
	<!-- App favicon -->
    
    <link rel="icon" href="<?php echo base_url();?>assets/p2s2_fav.png" type="image/x-icon">
  <link rel="shortcut icon" href="<?php echo base_url();?>assets/p2s2_fav.png" type="image/x-icon">

	<!-- ================================================= -->
	 <!-- Toastr -->
	 <link rel="stylesheet" href="<?php echo base_url() ?>assets/vendor/toastr/css/toastr.min.css">
	<?php $this->load->view($css); 
    error_reporting(0);
    ?>
	<!-- ================================================== -->
	 <!-- <script>
        var idleTimeout = 900000; // Waktu tunggu dalam milidetik (misalnya, 60 detik)

        var idleTimer = null;

        function resetIdleTimer() {
            clearTimeout(idleTimer);
            idleTimer = setTimeout(logout, idleTimeout);
        }

        function logout() {
            window.location.href = '<?php echo base_url("login/logout"); ?>'; // Ganti dengan URL logout Anda
        }

        // Panggil resetIdleTimer() saat pengguna berinteraksi dengan halaman
        document.addEventListener('mousemove', resetIdleTimer);
        document.addEventListener('keydown', resetIdleTimer);

    </script> -->
</head>
<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
		<div class="lds-ripple">
			<div></div>
			<div></div>
		</div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <!--**********************************
            Nav header start
        ***********************************-->
		<div class="nav-header">
            <a href="#" class="brand-logo">
				<svg class="logo-abbr" width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
					<rect class="react-logo" width="60" height="60" rx="30" fill="#00A15D"/>
					<mask id="mask0" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="60" height="60">
					<rect class="react-logo" width="60" height="60" rx="30" fill="#00A15D"/>
					</mask>
					<g mask="url(#mask0)">
					<path d="M130 51.3929L126.5 45.2109C123 38.9626 116 26.6981 109 23.1017C102 19.5715 95 24.875 88 29.3002C81 33.6591 74 37.3053 67 39.0124C60 40.7857 53 40.7857 46 36.3606C39 32.0017 32 23.0519 25 17.7981C18 12.4448 11 10.7874 4 16.9197C-3 23.0519 -10 37.3053 -17 40.7857C-24 44.2662 -31 37.3053 -34.5 33.7088L-38 30.1786V62H-34.5C-31 62 -24 62 -17 62C-10 62 -3 62 4 62C11 62 18 62 25 62C32 62 39 62 46 62C53 62 60 62 67 62C74 62 81 62 88 62C95 62 102 62 109 62C116 62 123 62 126.5 62H130V51.3929Z" fill="url(#paint0_linear)"/>
					<path d="M-54 55.7741L-50.5 50.9799C-47 46.1343 -40 36.623 -33 33.8339C-26 31.0962 -19 35.2092 -12 38.641C-5 42.0213 2 44.849 9 46.1728C16 47.5481 23 47.5481 30 44.1164C37 40.736 44 33.7954 51 29.721C58 25.5694 65 24.2841 72 29.0398C79 33.7954 86 44.849 93 47.5481C100 50.2473 107 44.849 110.5 42.0599L114 39.3222V64H110.5C107 64 100 64 93 64C86 64 79 64 72 64C65 64 58 64 51 64C44 64 37 64 30 64C23 64 16 64 9 64C2 64 -5 64 -12 64C-19 64 -26 64 -33 64C-40 64 -47 64 -50.5 64H-54V55.7741Z" fill="url(#paint1_linear)"/>
					</g>
					<defs>
					<linearGradient id="paint0_linear" x1="46" y1="13" x2="46" y2="62" gradientUnits="userSpaceOnUse">
					<stop  offset="0" stop-color="#23D58A"/>
					<stop offset="1" stop-color="#00A15D"/>
					</linearGradient>
					<linearGradient id="paint1_linear" x1="30" y1="26" x2="30" y2="64" gradientUnits="userSpaceOnUse">
					<stop  offset="0" stop-color="#FFED4B"/>
					<stop offset="1" stop-color="#FF8C4B"/>
					</linearGradient>
					</defs>
				</svg>

				<div class="brand-title">
					<h2 class="">E-Barokah</h2>
					<span class="brand-sub-title">Ponpes Salafiyah Syafi'iyah Sukorejo</span>
				</div>
            </a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <!--**********************************
            Nav header end
        ***********************************-->
		
		<!--**********************************
            Chat box start
        ***********************************-->
		
		<!--**********************************
            Chat box End
        ***********************************-->
		
		<!--**********************************
            Header start
        ***********************************-->
        <div class="header border-bottom">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
							<div class="dashboard_bar">
                                <!-- Dashboard -->
                            </div>
                        </div>
                        <ul class="navbar-nav header-right">
							
							
							<li class="nav-item dropdown header-profile">
                                <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                                    <?php if ($this->session->userdata('jabatan') == 'umana'){ ?>
                                        <?php if ($this->session->userdata('jk') != 'Laki-laki'){ ?>
                                        <img src="<?php echo base_url() ?>assets/putri.jpg" width="20" alt=""/>
                                        <?php } else { ?>
                                        <img src="<?php echo base_url() ?>assets/cowok.png" width="20" alt=""/>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <?php if ($this->session->userdata('foto') == '1'){ ?>
                                        <img src="<?php echo base_url() ?>assets/putri.jpg" width="20" alt=""/>
                                        <?php } else { ?>
                                        <img src="<?php echo base_url() ?>assets/cowok.png" width="20" alt=""/>
                                        <?php } 
                                        }?>
									<div class="header-info ms-3">
										<span class="fs-18 font-w500 mb-2"><?php echo $this->session->userdata('username'); ?></span>
										<small class="fs-12 font-w400"><?php echo $this->session->userdata('jabatan'); ?></small>
									</div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="<?php echo base_url() ?>login/logout" class="dropdown-item ai-icon">
                                        <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                        <span class="ms-2">Logout </span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
				</nav>
			</div>
		</div>
        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Sidebar start
        ***********************************-->
        <div class="dlabnav">
            <div class="dlabnav-scroll">
				<ul class="metismenu" id="menu">
                    
                    <?php if($this->session->userdata('jabatan') == 'SuperAdmin'){ ?>
                    <li><a class="" href="<?php echo base_url()?>Dashboard/petugas" aria-expanded="false">
							<i class="fas fa-home"></i>
							<span class="nav-text">Dashboard</span>
						</a>
                    </li>
                    <li><a class="has-arrow" href="javascript:void()">
					<i class="fas fa-chart-pie"></i>
						<span class="nav-text">Dashboard New</span>
					</a>
                        <ul aria-expanded="false">
                            <li><a href="<?php echo base_url() ?>Dashboard_struktural">Struktural</a></li>
                            <li><a href="<?php echo base_url() ?>Dashboard_pengajar">Pengajar</a></li>
                            <li><a href="<?php echo base_url() ?>Dashboard_satpam">Satpam</a></li>
                        </ul>
                    </li>
					<li><a class="has-arrow" href="javascript:void()">
						<i class="fas fa-info-circle"></i>
							<span class="nav-text">Master Data</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="<?php echo base_url() ?>Pengguna">Pengguna</a></li>
							<li><a href="<?php echo base_url() ?>Umana">Umana'</a></li>
                            <li><a href="<?php echo base_url() ?>Lembaga">Lembaga</a></li>
                            <li><a href="<?php echo base_url() ?>Tahun_acuan">Tahun Acuan</a></li>
							<li><a href="<?php echo base_url() ?>Ketentuan_barokah">Ketentuan Tunjab</a></li>
							 <li><a href="<?php echo base_url() ?>Potongan">Jenis Potongan</a></li> 
							<li><a href="<?php echo base_url() ?>Tunkel">Tunkel</a></li>
							<!-- <li><a href="#">Masa Pengabdian</a></li> -->
							<!--<li><a href="<?php echo base_url() ?>Potongan">Potongan Barokah</a></li>-->
                             <li><a href="<?php echo base_url() ?>Tunj_anak">Tunj Anak</a></li>
							<li><a href="<?php echo base_url() ?>Transport">Transport</a></li>
                           <li><a href="<?php echo base_url() ?>Barokah_pengajar">Barokah Pengajar</a></li>
                           <li><a href="<?php echo base_url() ?>Kehormatan">Kehormatan Struktural</a></li>
                            <li><a href="<?php echo base_url() ?>Kehormatan_pengajar">Kehormatan Pengajar</a></li>
                        </ul>
                    </li>
                    <li><a class="has-arrow" href="javascript:void()">
						<i class="fas fa-users"></i>
							<span class="nav-text">Manajemen Umana'</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="<?php echo base_url() ?>Penempatan">Penempatan Umana'</a></li>
							<li><a href="<?php echo base_url() ?>Potongan_umana">Potongan Barokah</a></li>
							 <li><a href="<?php echo base_url() ?>Tunjangan_bk">Tunjangan Beban Kerja</a></li>
                        </ul>
                    </li>
					<li><a class="has-arrow" href="javascript:void()">
						<i class="fas fa-chalkboard-teacher"></i>
							<span class="nav-text">Manajemen Pengajar</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="<?php echo base_url() ?>Penempatan/pengajar">Penempatan Guru/Dosen</a></li>
                            <li><a href="<?php echo base_url() ?>Potongan_pengajar">Potongan Barokah</a></li>
                            <li><a href="<?php echo base_url() ?>Barokah_tambahan">Barokah Tambahan</a></li>
                        </ul>
                    </li>
                    <li><a class="has-arrow" href="javascript:void()">
						<i class="fas fa-shield-alt"></i>
							<span class="nav-text">Manajemen Satpam</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="<?php echo base_url() ?>Penempatan_satpam">Penempatan Satpam</a></li>
							<li><a href="<?php echo base_url() ?>Potongan_umana">Potongan Barokah</a></li>
                        </ul>
                    </li>
					<li><a class="has-arrow" href="javascript:void()">
						<i class="fas fa-user-check"></i>
							<span class="nav-text">kehadiran</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="<?php echo base_url() ?>kehadiran_struktural">Struktural</a></li>
							<li><a href="<?php echo base_url() ?>kehadiran/pengajar">Pengajar</a></li>
                            <li><a href="<?php echo base_url() ?>kehadiran_satpam">Satpam</a></li>
                        </ul>
                    </li>
					<li><a class="" href="<?php echo base_url()?>Validasi" aria-expanded="false">
							<i class="fas fa-list-alt"></i>
							<span class="nav-text">Validasi Barokah</span>
						</a>
                    </li>
                    <li><a class="" href="<?php echo base_url() ?>payroll" aria-expanded="false">
							<i class="fas fa-cash-register"></i>
							<span class="nav-text">PayRoll</span>
						</a>
                    </li>
					<li><a class="has-arrow" href="javascript:void()">
						<i class="fas fa-chart-line"></i>
							<span class="nav-text">Laporan</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="<?php echo base_url() ?>Laporan_lembaga/per_bulan">Per Bulan</a></li>
                            <li><a href="<?php echo base_url() ?>Laporan_lembaga">Per Lembaga</a></li>
                            <li><a href="<?php echo base_url() ?>Laporan_satpam/index">Laporan Satpam</a></li>
							<li><a href="<?php echo base_url() ?>Laporan/per_umana">Per Umana</a></li>
                        </ul>
                    </li>
                    <li><a class="has-arrow" href="javascript:void()">
						<i class="fas fa-chart-line"></i>
							<span class="nav-text">Log</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="<?php echo base_url() ?>Potongan_umana/per_umana">Potongan Umana</a></li>
							<li><a href="<?php echo base_url() ?>Log_barokah">Total Barokah</a></li>
							<li><a href="<?php echo base_url() ?>Log_barokah/pengajar">Total Barokah Pengajar</a></li>
							<li><a href="<?php echo base_url() ?>Kehadiran/kehadiran_log">Kehadiran</a></li>
							<li><a href="<?php echo base_url() ?>Log_kehadiran_lemb">Kehadiran Lembaga</a></li>
							
                        </ul>
                    </li>
                     <li><a class="" href="<?php echo base_url()?>personalia" aria-expanded="false">
							<i class="fas fa-user-check"></i>
							<span class="nav-text">Personalia</span>
						</a>
                    </li>
                    <li><a class="" href="<?php echo base_url()?>Lembaga/backupDatabase" aria-expanded="false">
							<i class="fas fa-download"></i>
							<span class="nav-text">Backup DB</span>
						</a>
                    </li>
                    <!--<li><a class="" href="<?php echo base_url()?>Umana/profil" aria-expanded="false">-->
                    <!--        <i class="fas fa-user-check"></i>-->
                    <!--        <span class="nav-text">Data Diri Umana</span>-->
                    <!--    </a>-->
                    <!--</li>-->
                    <?php }else if ($this->session->userdata('jabatan') == 'AdminLembaga' and $this->session->userdata('tenaga_pengajar') == 'Ya'){ ?>
                    <!-- ********************** LEMBAGA ******************************************** -->
                    <li><a class="" href="<?php echo base_url()?>Dashboard/lembaga" aria-expanded="false">
							<i class="fas fa-home"></i>
							<span class="nav-text">Dashboard</span>
						</a>
                    </li>
                    <li><a class="has-arrow" href="javascript:void()">
						<i class="fas fa-user-check"></i>
							<span class="nav-text">kehadiran</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="<?php echo base_url() ?>kehadiran_struktural">Struktural</a></li>
							<li><a href="<?php echo base_url() ?>kehadiran/pengajar">Pengajar</a></li>
                        </ul>
                    </li>
                    <li><a class="has-arrow" href="javascript:void()">
						<i class="fas fa-user-check"></i>
							<span class="nav-text">Potongan</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="<?php echo base_url() ?>Potongan_umana">Struktural</a></li>
							<li><a href="<?php echo base_url() ?>Potongan_pengajar">Pengajar</a></li>
                        </ul>
                    </li>
                    <li><a class="" href="<?php echo base_url()?>Penempatan/Pengajar" aria-expanded="false">
							<i class="fas fa-list-alt"></i>
							<span class="nav-text">Beban SKS/Jam Mengajar</span>
						</a>
                    </li>
                    <li><a class="" href="<?php echo base_url()?>Tunjangan_bk" aria-expanded="false">
							<i class="fas fa-list-alt"></i>
							<span class="nav-text">Tunjangan Beban Kerja</span>
						</a>
                    </li>
                    <li><a class="" href="<?php echo base_url()?>Barokah_tambahan" aria-expanded="false">
							<i class="fas fa-list-alt"></i>
							<span class="nav-text">Barokah Tambahan</span>
						</a>
                    </li>
                    <?php }else if ($this->session->userdata('jabatan') == 'AdminLembaga' and $this->session->userdata('tenaga_pengajar') != 'Ya'){ ?>
                    <!-- ********************** LEMBAGA ******************************************** -->
                    <li><a class="" href="<?php echo base_url()?>Dashboard/lembaga" aria-expanded="false">
							<i class="fas fa-home"></i>
							<span class="nav-text">Dashboard</span>
						</a>
                    </li>
                    <li><a class="has-arrow" href="javascript:void()">
						<i class="fas fa-user-check"></i>
							<span class="nav-text">kehadiran</span>
						</a>
                        <ul aria-expanded="false">
                             <?php if ($this->session->userdata('username') == 'Satpam') { ?>
                                <li><a href="<?php echo base_url('kehadiran_satpam'); ?>">Satpam</a></li>
                            <?php } else { ?>
                                <li><a href="<?php echo base_url() ?>kehadiran_struktural">Struktural</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <li><a class="has-arrow" href="javascript:void()">
						<i class="fas fa-user-check"></i>
							<span class="nav-text">Potongan</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="<?php echo base_url() ?>Potongan_umana">Struktural</a></li>
							<!--<li><a href="<?php echo base_url() ?>Potongan_pengajar">Pengajar</a></li>-->
                        </ul>
                    </li>
                    <li><a class="" href="<?php echo base_url()?>Tunjangan_bk" aria-expanded="false">
							<i class="fas fa-list-alt"></i>
							<span class="nav-text">Tunjangan Beban Kerja</span>
						</a>
                    </li>
	<!-- START MENU LAPORAN SATPAM -->
					<?php if ($this->session->userdata('lembaga') == 59) { ?>
                    <li><a class="has-arrow" href="javascript:void()">
						<i class="fas fa-chart-line"></i>
							<span class="nav-text">Laporan</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="<?php echo base_url() ?>Laporan_satpam/index">Laporan Satpam</a></li>
                        </ul>
                    </li>
					<?php } ?>
					<!-- END MENU LAPORAN SATPAM -->
					
                     <!-- ********************** EVALUASI ******************************************** -->
                    <?php }else if ($this->session->userdata('jabatan') == 'Evaluasi'){ ?>
                    <!-- ********************** EVALUASI ******************************************** -->
                    <li><a class="" href="<?php echo base_url()?>Dashboard/petugas" aria-expanded="false">
							<i class="fas fa-home"></i>
							<span class="nav-text">Dashboard</span>
						</a>
                    </li>
                    <li><a class="has-arrow" href="javascript:void()">
						<i class="fas fa-info-circle"></i>
							<span class="nav-text">Master Data</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="<?php echo base_url() ?>Pengguna">Pengguna</a></li>
							<li><a href="<?php echo base_url() ?>Umana">Umana'</a></li>
                            <li><a href="<?php echo base_url() ?>Lembaga">Lembaga</a></li>
							<li><a href="<?php echo base_url() ?>Ketentuan_barokah">Ketentuan Tunjab</a></li>
							 <!--<li><a href="<?php echo base_url() ?>Potongan">Jenis Potongan</a></li> -->
							<li><a href="<?php echo base_url() ?>Tunkel">Tunkel</a></li>
							<!-- <li><a href="#">Masa Pengabdian</a></li> -->
							<li><a href="<?php echo base_url() ?>Kehormatan">Kehormatan</a></li>
                            <!--<li><a href="<?php echo base_url() ?>Potongan">Potongan Barokah</a></li>-->
							<li><a href="<?php echo base_url() ?>Transport">Transport</a></li>
                        </ul>
                    </li>
					<li><a class="has-arrow" href="javascript:void()">
						<i class="fas fa-info-circle"></i>
							<span class="nav-text">Manajemen Umana'</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="<?php echo base_url() ?>Penempatan">Penempatan Umana'</a></li>
							<li><a href="<?php echo base_url() ?>Potongan_umana">Potongan Barokah</a></li>
							 <li><a href="<?php echo base_url() ?>Tunjangan_bk">Tunjangan Beban Kerja</a></li>
                        </ul>
                    </li>
                    <li><a class="" href="<?php echo base_url()?>Validasi" aria-expanded="false">
							<i class="fas fa-list-alt"></i>
							<span class="nav-text">Validasi Barokah</span>
						</a>
                    </li>
                    <li><a class="has-arrow" href="javascript:void()">
						<i class="fas fa-chart-line"></i>
							<span class="nav-text">Laporan</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="<?php echo base_url() ?>Laporan_lembaga/per_bulan">Per Bulan</a></li>
                            <li><a href="<?php echo base_url() ?>Laporan_lembaga">Per Lembaga</a></li>
							<li><a href="<?php echo base_url() ?>Laporan/per_umana">Per Umana</a></li>
                        </ul>
                    </li>
                    <?php }else if ($this->session->userdata('jabatan') == 'Kasir'){ ?>
                    <!-- ********************** KASIR ******************************************** -->
                    <li><a class="" href="<?php echo base_url()?>Dashboard/petugas" aria-expanded="false">
							<i class="fas fa-home"></i>
							<span class="nav-text">Dashboard</span>
						</a>
                    </li>
                    <li><a class="" href="<?php echo base_url() ?>payroll" aria-expanded="false">
							<i class="fas fa-cash-register"></i>
							<span class="nav-text">PayRoll</span>
						</a>
                    </li>
                    <li><a class="has-arrow" href="javascript:void()">
						<i class="fas fa-chart-line"></i>
							<span class="nav-text">Laporan</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="<?php echo base_url() ?>Laporan_lembaga/per_bulan">Per Bulan</a></li>
                            <li><a href="<?php echo base_url() ?>Laporan_lembaga">Per Lembaga</a></li>
							<li><a href="<?php echo base_url() ?>Laporan/per_umana">Per Umana</a></li>
                        </ul>
                    </li>
                    <?php }else if ($this->session->userdata('jabatan') == 'SDM'){ ?>
                    <!-- ********************** KASIR ******************************************** -->
                    <li><a class="" href="<?php echo base_url()?>Dashboard/petugas" aria-expanded="false">
							<i class="fas fa-home"></i>
							<span class="nav-text">Dashboard</span>
						</a>
                    </li>
                    <li><a class="has-arrow" href="javascript:void()">
						<i class="fas fa-user-check"></i>
							<span class="nav-text">kehadiran</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="<?php echo base_url() ?>kehadiran_struktural">Struktural</a></li>
							<li><a href="<?php echo base_url() ?>kehadiran/pengajar">Pengajar</a></li>
                        </ul>
                    </li>
                    <?php } else { ?>
                    <!-- ********************** Pimpinan ******************************************** -->
                    <li><a class="" href="<?php echo base_url()?>Dashboard" aria-expanded="false">
							<i class="fas fa-home"></i>
							<span class="nav-text">Dashboard</span>
						</a>
                    </li>
                    <li><a class="" href="<?php echo base_url()?>Laporan/kehadiran" aria-expanded="false">
                            <i class="fas fa-wallet"></i>
                            <span class="nav-text">Kehadiran</span>
                        </a>
                    </li>
                    <li><a class="" href="<?php echo base_url()?>Umana/profil" aria-expanded="false">
                            <i class="fas fa-user-check"></i>
                            <span class="nav-text">Data Diri Umana</span>
                        </a>
                    </li>
                    <?php } ?>
                </ul>
			

				
				<div class="copyright">
					<p><strong>Bendahara P2S3</strong> © 2023 All Rights Reserved</p>
					<p class="fs-12">Made with <span class="heart"></span></p>
				</div>
			</div>
        </div>
        <!--**********************************
            Sidebar end
        ***********************************-->
		
		<!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <!-- row -->
			<?php $this->load->view($content); ?>
        </div>
        <!--**********************************
            Content body end
        ***********************************-->
		


		
        <!--**********************************
            Footer start
        ***********************************-->
        <div class="footer">
            <div class="copyright">
                <p>Copyright ©  &amp; Developed by <a href="">Bendahara P2S3</a> 2023</p>
            </div>
        </div>
        <!--**********************************
            Footer end
        ***********************************-->

		<!--**********************************
           Support ticket button start
        ***********************************-->
		
        <!--**********************************
           Support ticket button end
        ***********************************-->


	</div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Global Vendor -->
    <script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
	<script src="<?php echo base_url() ?>assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
	<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>
    
    <?php $this->load->view($ajax); ?>
   <!-- Toastr -->
   <script src="<?php echo base_url() ?>assets/vendor/toastr/js/toastr.min.js"></script>

<!-- All init script -->
<script src="<?php echo base_url() ?>assets/js/plugins-init/toastr-init.js"></script>
	<script>
			function notif(pesan) {
                toastr.success(pesan, "Berhasil", {
                    positionClass: "toast-top-right",
                    timeOut: 5e3,
                    closeButton: !0,
                    debug: !1,
                    newestOnTop: !0,
                    progressBar: !0,
                    preventDuplicates: !0,
                    onclick: null,
                    showDuration: "300",
                    hideDuration: "1000",
                    extendedTimeOut: "1000",
                    showEasing: "swing",
                    hideEasing: "linear",
                    showMethod: "fadeIn",
                    hideMethod: "fadeOut",
                    tapToDismiss: !1
                })
            };		
	</script>

</body>
</html>