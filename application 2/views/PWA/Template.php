<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta
      name="apple-mobile-web-app-status-bar-style"
      content="black-translucent"
    />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover"
    />
    <title>E-Barokah P2S3</title>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/styles/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/styles/style.css" />
    <link rel="icon" href="<?php echo base_url();?>assets/p2s2_fav.png" type="image/x-icon">
    <link rel="shortcut icon" href="<?php echo base_url();?>assets/p2s2_fav.png" type="image/x-icon">
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      type="text/css"
      href="<?php echo base_url() ?>assets/fonts/css/fontawesome-all.min.css"
    />
    <link
      rel="manifest"
      href="<?php echo base_url() ?>assets/_manifest.json"
      data-pwa-version="set_in_manifest_and_pwa_js"
    />
    <link
      rel="apple-touch-icon"
      sizes="180x180"
      href="<?php echo base_url() ?>assets/app/icons/icon-192x192.png"
    />
    
  </head>

  <body class="theme-light">
    <div id="preloader">
      <div class="spinner-border color-highlight" role="status"></div>
    </div>

    <?php $this->load->view($content); ?>
    <script>
    var baseUrl = "<?php echo base_url(); ?>"; 
    </script>
    <script type="text/javascript" src="<?php echo base_url() ?>assets/scripts/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>assets/scripts/custom.js"></script>
  </body>
</html>
