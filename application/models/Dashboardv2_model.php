<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboardv2_model extends CI_Model {
    public function get_laporan_bulanan($awal, $akhir)
{
    $this->db->select("
        tb.kehadiran,
        tb.nominal_kehadiran,
        tb.tunkel,
        tb.tunj_anak,
        tb.tmp,
        tb.kehormatan,
        tb.tbk,
        tb.barokah_khusus,
        tb.potongan,
        tb.diterima,
        tb.timestamp,

        u.nik,
        u.nama_lengkap,
        l.nama_lembaga,
        kb.nama_jabatan
    ");

    $this->db->from('total_barokah tb');

    $this->db->join('kehadiran k', 'k.id_kehadiran = tb.id_kehadiran', 'left');

    $this->db->join(
        'kehadiran_lembaga kl',
        'kl.id_lembaga = k.id_kehadi 
         AND kl.bulan = k.bulan 
         AND kl.tahun = k.tahun',
        'left'
    );

    $this->db->join('penempatan p', 'p.id_penempatan = tb.id_penempatan', 'left');
    $this->db->join('umana u', 'u.nik = p.nik', 'left');
    $this->db->join('lembaga l', 'l.id_lembaga = p.id_lembaga', 'left');
    $this->db->join('ketentuan_barokah kb', 'kb.id_ketentuan = p.id_ketentuan', 'left');

    $this->db->where('DATE(tb.timestamp) >=', $awal);
    $this->db->where('DATE(tb.timestamp) <=', $akhir);

    $this->db->order_by('u.nama_lengkap', 'ASC');

    return $this->db->get()->result();
}
2024-01-11 07:41:38

select umana.nik, nama_lembaga, nama_lengkap, nama_jabatan, tunjab, mp, jumlah_hadir, nominal_kehadiran, tunkel, tmp, total_barokah.kehormatan, tbk, potongan, diterima FROM umana, penempatan, kehadiran_lembaga, kehadiran, lembaga, ketentuan_barokah, total_barokah WHERE umana.nik = penempatan.nik AND penempatan.id_lembaga = lembaga.id_lembaga AND penempatan.id_penempatan = kehadiran.id_penempatan and penempatan.id_ketentuan = ketentuan_barokah.id_ketentuan AND total_barokah.id_penempatan = penempatan.id_penempatan AND total_barokah.timestamp >= '2025-01-18 07:41:38' GROUP BY penempatan.id_penempatan;
}