<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Validasi_satpam extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('kehadiran_satpam_model');
        $this->load->helper('Rupiah_helper');
		$this->load->helper('string');
		$this->load->library('Pdf'); 
	}

    public function decrypt_url($string) {
        $key = '874jzceroier38!@#%*bjkdwdw)'; // Ganti dengan kunci enkripsi yang diinginkan
        $string = str_replace(array('-', '_'), array('+', '/'), $string);
        $string = base64_decode($string);
        $string = str_replace($key, '', $string);
        return $string;
    }

	public function koreksi_satpam($id)
{
    $this->Login_model->getsqurity();
    $decrypted_id = $this->decrypt_url($id);

    // 1) Ambil info periode (judul)
    $bulan = $this->db->query("
        SELECT id_kehadiran_lembaga, id_lembaga, bulan, tahun, status, file
        FROM kehadiran_lembaga
        WHERE id_kehadiran_lembaga = ?
        LIMIT 1
    ", [(int)$decrypted_id])->row();

    if (!$bulan) {
        show_error('Periode tidak ditemukan: ' . htmlspecialchars($decrypted_id), 404);
        return;
    }

    // 2) Query normal (LEFT JOIN agar tidak menghilangkan baris)
    $sql = "
        SELECT 
            ks.id_kehadiran_satpam,
            ks.id_kehadiran_lembaga,
            ks.id_satpam,
            ks.bulan,
            ks.tahun,
            ks.jumlah_hari,
            ks.jumlah_shift,
            ks.jumlah_dinihari,
            u.gelar_depan,
            u.nama_lengkap,
            u.gelar_belakang,
            COALESCE(t.nominal_transport, 0) AS nominal_transport
        FROM kehadiran_satpam ks
        LEFT JOIN satpam s     ON s.id_satpam    = ks.id_satpam
        LEFT JOIN umana  u     ON u.nik          = s.nik
        LEFT JOIN transport t  ON t.id_transport = s.id_transport
        WHERE ks.id_kehadiran_lembaga = ?
        ORDER BY u.nama_lengkap ASC, ks.id_kehadiran_satpam ASC
    ";
    $list = $this->db->query($sql, [(string)$decrypted_id])->result();

    // 3) Jika kosong, lakukan “probe” tanpa JOIN
    if (empty($list)) {
        $probe = $this->db->query(
            "SELECT id_kehadiran_satpam, id_satpam, bulan, tahun
             FROM kehadiran_satpam
             WHERE id_kehadiran_lembaga = ?
             ORDER BY id_kehadiran_satpam ASC
             LIMIT 5",
            [(string)$decrypted_id]
        )->result();

        // Jika probe ada hasil, tampilkan bukti ke layar lalu berhenti.
        if (!empty($probe)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'note'        => 'JOIN menghasilkan 0 baris, tetapi tabel kehadiran_satpam berisi data untuk id ini. Ini berarti masalah ada pada JOIN/kolom referensi.',
                'id_decrypt'  => $decrypted_id,
                'sample_rows' => $probe,
                // cek juga apakah kolom referensi terisi:
                'ref_check'   => $this->db->query("
                    SELECT COUNT(*) AS satpam_rows FROM satpam WHERE id_satpam IN (
                        SELECT id_satpam FROM kehadiran_satpam WHERE id_kehadiran_lembaga = ?
                    )
                ", [(string)$decrypted_id])->row(),
            ], JSON_PRETTY_PRINT);
            exit;
        }

        // Kalau probe juga 0 → berarti id salah (decrypt / URL)
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'note'       => 'Tidak ada baris di kehadiran_satpam untuk id ini. Cek id terenkripsi/hasil decrypt.',
            'id_decrypt' => $decrypted_id
        ], JSON_PRETTY_PRINT);
        exit;
    }

    // 4) Bila ada data, kirim ke view seperti biasa
    $isi['id']      = $id;
    $isi['bulan']   = $bulan;
    $isi['isilist'] = $list;
    $this->load->view('Validasi_fullscreen/Validasi_satpam', $isi);
}

public function update_row()
{
    // pastikan user punya hak koreksi di sini bila perlu
    $id_row   = $this->input->post('id_kehadiran_satpam');
    $id_kl    = $this->input->post('id_kehadiran_lembaga');
    $hari     = (int)$this->input->post('jumlah_hari');
    $shift    = (int)$this->input->post('jumlah_shift');
    $dini     = (int)$this->input->post('jumlah_dinihari');

    if (!$id_row || !$id_kl) {
        echo json_encode(["status"=>false, "message"=>"Parameter tidak lengkap."]); return;
    }
    if ($hari < 0 || $shift < 0 || $dini < 0) {
        echo json_encode(["status"=>false, "message"=>"Angka tidak valid."]); return;
    }

    // transaksi
    $this->db->trans_begin();

    // update kehadiran_satpam
    $this->db->where('id_kehadiran_satpam', $id_row)
             ->update('kehadiran_satpam', [
                 'jumlah_hari'     => $hari,
                 'jumlah_shift'    => $shift,
                 'jumlah_dinihari' => $dini
             ]);

    if ($this->db->affected_rows() === 0 && $this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        echo json_encode(["status"=>false, "message"=>"Gagal menyimpan perbaruan."]); return;
    }

    // Ambil ulang row + tarif supaya bisa hitung jumlah uang baris ini
    $row = $this->db->select('ks.id_kehadiran_satpam, ks.id_satpam, ks.jumlah_hari, ks.jumlah_shift, ks.jumlah_dinihari, s.id_transport, t.nominal_transport')
                    ->from('kehadiran_satpam ks')
                    ->join('satpam s', 's.id_satpam = ks.id_satpam')
                    ->join('transport t', 't.id_transport = s.id_transport')
                    ->where('ks.id_kehadiran_satpam', $id_row)
                    ->get()->row();

    if (!$row) {
        $this->db->trans_rollback();
        echo json_encode(["status"=>false, "message"=>"Data baris tidak ditemukan."]); return;
    }

    // Tarif (sementara tetap konstan; ganti jika sudah ada tabel ketentuan)
    $rank     = 17500;
    $konsumsi = 5000;

    // Hitung ulang untuk baris
    $jumlah_transport = (int)$row->jumlah_hari * (int)$row->nominal_transport;
    $jumlah_barokah   = (int)$row->jumlah_shift * $rank;
    $jumlah_konsumsi  = (int)$row->jumlah_dinihari * $konsumsi;
    $subtotal         = $jumlah_transport + $jumlah_barokah + $jumlah_konsumsi;

    // Hitung ulang total untuk footer (berdasarkan id_kehadiran_lembaga)
    // NB: karena rank & konsumsi konstan, aman dihitung langsung di SQL dengan konstanta
    $tot = $this->db->query("
        SELECT
          SUM(ks.jumlah_hari * t.nominal_transport) AS total_transport,
          SUM(ks.jumlah_shift * ?)                  AS total_barokah,
          SUM(ks.jumlah_dinihari * ?)               AS total_dinihari
        FROM kehadiran_satpam ks
        JOIN satpam s   ON s.id_satpam = ks.id_satpam
        JOIN transport t ON t.id_transport = s.id_transport
        WHERE ks.id_kehadiran_lembaga = ?
    ", [$rank, $konsumsi, $id_kl])->row();

    $grand = ((int)$tot->total_transport) + ((int)$tot->total_barokah) + ((int)$tot->total_dinihari);

    if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        echo json_encode(["status"=>false, "message"=>"Gagal menghitung total."]); return;
    }

    $this->db->trans_commit();

    echo json_encode([
        "status" => true,
        "row" => [
            "id"               => (int)$row->id_kehadiran_satpam,
            "jumlah_hari"      => (int)$row->jumlah_hari,
            "jumlah_shift"     => (int)$row->jumlah_shift,
            "jumlah_dinihari"  => (int)$row->jumlah_dinihari,
            "jumlah_transport" => (int)$jumlah_transport,
            "jumlah_barokah"   => (int)$jumlah_barokah,
            "jumlah_konsumsi"  => (int)$jumlah_konsumsi,
            "subtotal"         => (int)$subtotal
        ],
        "totals" => [
            "total_transport" => (int)$tot->total_transport,
            "total_barokah"   => (int)$tot->total_barokah,
            "total_dinihari"  => (int)$tot->total_dinihari,
            "grand_total"     => (int)$grand
        ]
    ]);
}

public function save_data()
{
    $id_kehadiran_lembaga = $this->input->post('id_kehadiran_lembaga', true);
    if (empty($id_kehadiran_lembaga)) {
        echo json_encode(["status" => false, "message" => "id_kehadiran_lembaga kosong."]);
        return;
    }

    // Ambil snapshot kehadiran + tarif dari DB
    $sql = "
        SELECT
            ks.id_kehadiran_satpam,
            ks.id_satpam,
            ks.bulan,
            ks.tahun,
            ks.jumlah_hari,
            ks.jumlah_shift,
            ks.jumlah_dinihari,
            t.nominal_transport
        FROM kehadiran_satpam ks
        JOIN satpam s   ON s.id_satpam = ks.id_satpam
        JOIN transport t ON t.id_transport = s.id_transport
        WHERE ks.id_kehadiran_lembaga = ?
        ORDER BY ks.id_kehadiran_satpam ASC
    ";
    $rowsSrc = $this->db->query($sql, [$id_kehadiran_lembaga])->result();
    if (!$rowsSrc) {
        echo json_encode(["status" => false, "message" => "Tidak ada data kehadiran untuk lembaga ini."]);
        return;
    }

    // TODO: kalau sudah ada tabel ketentuan, ambil dari tabel; untuk sementara hardcode
    $rank     = 17500;
    $konsumsi = 5000;

    $rowsInsert   = [];
    $grand_total  = 0;

    foreach ($rowsSrc as $r) {
        $jHari  = (int)$r->jumlah_hari;
        $jShift = (int)$r->jumlah_shift;
        $jDini  = (int)$r->jumlah_dinihari;
        $nTrans = (int)$r->nominal_transport;

        $jTrans = $jHari  * $nTrans;
        $jBar   = $jShift * $rank;
        $jKons  = $jDini  * $konsumsi;
        $diter  = $jTrans + $jBar + $jKons;

        $grand_total += $diter;

        $rowsInsert[] = [
            'id_total_barokah_satpam' => null,
            'id_satpam'               => (int)$r->id_satpam,
            'id_kehadiran_satpam'     => (int)$r->id_kehadiran_satpam,
            'id_kehadiran_lembaga'    => (int)$id_kehadiran_lembaga,
            'bulan'                   => $r->bulan,
            'tahun'                   => $r->tahun,
            'jumlah_hari'             => $jHari,
            'nominal_transport'       => $nTrans,
            'jumlah_transport'        => $jTrans,
            'jumlah_shift'            => $jShift,
            'rank'                    => $rank,
            'jumlah_barokah'          => $jBar,
            'jumlah_dinihari'         => $jDini,
            'konsumsi'                => $konsumsi,
            'jumlah_konsumsi'         => $jKons,
            'diterima'                => $diter,
        ];
    }

    // Simpan secara transaksional
    $this->db->trans_begin();

    // Bersihkan snapshot lama agar tidak dobel
    $this->db->where('id_kehadiran_lembaga', $id_kehadiran_lembaga)
             ->delete('total_barokah_satpam');

    // Insert batch snapshot baru
    $this->db->insert_batch('total_barokah_satpam', $rowsInsert);

    // Update status & total di header lembaga
    $this->db->where('id_kehadiran_lembaga', $id_kehadiran_lembaga)
             ->update('kehadiran_lembaga', [
                'status'       => 'acc',
                'jumlah_total' => $grand_total,
             ]);

    if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        echo json_encode(["status" => false, "message" => "Gagal menyimpan data (transaksi dibatalkan)."]);
        return;
    }

    $this->db->trans_commit();

    echo json_encode([
        "status"  => true,
        "message" => "Data barokah satpam berhasil disimpan.",
        "total"   => (int)$grand_total
    ]);
}

}