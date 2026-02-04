<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'third_party/fpdf/fpdf.php';
require_once APPPATH.'third_party/phpqrcode/qrlib.php';

class QRCode extends FPDF {
    public function __construct() {
        parent::__construct();
    }

    public function generateQRCode($data, $x, $y, $size) {
        $tempDir = FCPATH.'temp/';
        $fileName = $tempDir.uniqid().'.png';

        QRcode::png($data, $fileName, 'L', $size, 2);
        $this->Image($fileName, $x, $y, $size, $size);
        unlink($fileName);
    }

    
}
