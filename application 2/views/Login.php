<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="keywords" content="penggajian, barokah, pondok pesantren, aplikasi penggajian, ebarokah, sistem penggajian, payroll system" />
    <meta name="author" content="eBarokah Team" />
    <meta name="robots" content="index, follow" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="eBarokah: Aplikasi Penggajian untuk Pondok Pesantren dengan fitur lengkap dan mudah digunakan" />
    <meta property="og:title" content="eBarokah: Aplikasi Penggajian untuk Pondok Pesantren" />
    <meta property="og:description" content="eBarokah: Solusi Penggajian Terbaik untuk Pondok Pesantren dengan fitur lengkap dan mudah digunakan." />
    <meta property="og:image" content="https://ebarokah.com/images/social-image.png" />
    <meta name="format-detection" content="telephone=no">


	<!-- PAGE TITLE HERE -->
	<title>E-Barokah | Aplikasi Barokah Pondok Pesantren Salafiyah Syafi'iyah Sukorejo</title>
	
	<!-- FAVICONS ICON -->
	<link rel="icon" href="<?php echo base_url();?>assets/p2s2_fav.png" type="image/x-icon">
  <link rel="shortcut icon" href="<?php echo base_url();?>assets/p2s2_fav.png" type="image/x-icon">
    <link href="<?php echo base_url() ?>assets/css/style.css" rel="stylesheet">

</head>

<body class="vh-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
									<div class="text-center mb-3">
										<a href="index.html"><img src="<?php echo base_url() ?>assets/images/logo-full1.jpg" alt=""></a>
									</div>
                                    <h4 class="text-center mb-4">Sign in your account</h4>
                                    <?= $this->session->flashdata('message') ?>
                                    <form action="<?php echo base_url('index.php/Login/aksi_login'); ?>" method="post">
                                        <div class="mb-3">
                                            <label class="mb-1"><strong>Username</strong></label>
                                            <input type="text" name="username" class="form-control" value="<?= set_value('username') ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="mb-1"><strong>Password</strong></label>
                                            <input type="password" name="password" id="password" class="form-control" value="<?= set_value('username') ?>">
                                        </div>
                                         <div class="row d-flex justify-content-between mt-4 mb-2">
                                            <div class="mb-3">
                                               <div class="form-check custom-checkbox ms-1">
													<input type="checkbox" class="form-check-input" id="show-password">
													<label class="form-check-label" for="show-password">Tampilkan Password </label>
												</div>
                                            </div>
                                            <div class="mb-3">
                                                <!-- <a href="page-forgot-password.html">Forgot Password?</a> -->
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary btn-block">Sign Me In</button>
                                        </div>
                                    </form>
                                    <div class="new-account mt-3">
                                        <!-- <p>Don't have an account? <a class="text-primary" href="<?php echo base_url() ?>assets/page-register.html">Sign up</a></p> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>
	<!-- <script src="<?php echo base_url() ?>assets/js/styleSwitcher.js"></script> -->
     <!-- Sweet alert -->
	 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
	 <script>
        const showPasswordCheckbox = document.getElementById('show-password');
        const passwordInput = document.getElementById('password');

        showPasswordCheckbox.addEventListener('change', function () {
        if (this.checked) {
            passwordInput.type = 'text';
        } else {
            passwordInput.type = 'password';
        }
        });
    </script>

    <?php if ($this->session->flashdata('login-failed-1')) : ?>
    <script>
        swal.fire({
            icon: "error",
            title: "Gagal!",
            text: "Login gagal, password salah!"
        })
    </script>
    <?php endif; ?>

    <?php if ($this->session->flashdata('login-failed-2')) : ?>
    <script>
        swal.fire({
            icon: "error",
            title: "Gagal!",
            text: "Login gagal, username salah!"
        })
    </script>
    <?php endif; ?>
    <!--app JS-->
</body>
</html>