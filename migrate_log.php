<?php
// Mocking server vars for CLI execution
$_SERVER['SERVER_ADDR'] = '127.0.0.1';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';

define('ENVIRONMENT', 'development');

// Path to system folder
$system_path = 'system';
// Path to application folder
$application_folder = 'application';
// Path to view folder
$view_folder = '';

if (($_temp = realpath($system_path)) !== FALSE) { $system_path = $_temp.DIRECTORY_SEPARATOR; } else { $system_path = str_replace("\\", "/", $system_path); }
define('BASEPATH', $system_path);
define('FCPATH', dirname(__FILE__).'/');
define('SYSDIR', basename(BASEPATH));
if (is_dir($application_folder)) { if (($_temp = realpath($application_folder)) !== FALSE) { $application_folder = $_temp; } else { $application_folder = str_replace("\\", "/", $application_folder); } } else { if ( ! is_dir(BASEPATH.$application_folder.'/')) { header('HTTP/1.1 503 Service Unavailable.', TRUE, 503); echo 'Your application folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF; exit(3); } $application_folder = BASEPATH.$application_folder; }
define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);
if ( ! isset($view_folder[0]) && is_dir(APPPATH.'views/')) { $view_folder = APPPATH.'views'; } elseif (is_dir($view_folder)) { if (($_temp = realpath($view_folder)) !== FALSE) { $view_folder = $_temp; } else { $view_folder = str_replace("\\", "/", $view_folder); } } elseif ( ! is_dir(BASEPATH.$view_folder.'/')) { header('HTTP/1.1 503 Service Unavailable.', TRUE, 503); echo 'Your view folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF; exit(3); }
define('VIEWPATH', $view_folder.DIRECTORY_SEPARATOR);

require_once BASEPATH.'core/CodeIgniter.php';

$CI =& get_instance();
$CI->load->database();

$sql = "
CREATE TABLE IF NOT EXISTS `log_riwayat_barokah` (
  `id_log` int(11) NOT NULL AUTO_INCREMENT,
  `id_kehadiran_lembaga` int(11) NOT NULL,
  `status_aksi` enum('Belum','Sudah','Terkirim','Revisi','acc') NOT NULL,
  `waktu_eksekusi` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_pengguna` int(11) DEFAULT NULL,
  `catatan_log` text,
  PRIMARY KEY (`id_log`),
  KEY `id_kehadiran_lembaga` (`id_kehadiran_lembaga`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

if ($CI->db->query($sql)) {
    echo "Tabel log_riwayat_barokah berhasil dibuat.\n";
} else {
    echo "Gagal membuat tabel. Error: " . $CI->db->error()['message'] . "\n";
}
