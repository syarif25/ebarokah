<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends CI_Controller {
    public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Report_model');
        $this->load->helper('Rupiah_helper');
        $this->load->library('user_agent');
	}

	public function index()
	{
        $id = $this->session->userdata('nik');
		$this->Login_model->getsqurity() ;

        $isi['content'] = 'PWA/Dashboard';
		$this->load->view('PWA/Template',$isi);
	}

    public function Kehadiran()
	{
        $isi['content'] = 'PWA/Barokah';
		$this->load->view('PWA/Template',$isi);
	}

    public function Barokah() {
        $this->Login_model->getsqurity();
        $kategori = strtolower($this->input->get('k') ?: 'struktural');
        $isi = ['content' => 'PWA/Detail_barokah', 'kategori' => $kategori];
        $this->load->view('PWA/Template', $isi);
    }
      
    public function dashboard_barokah()
    {
        if (!$this->session->userdata('nik')) {
            if ($this->input->is_ajax_request()) {
                return $this->output->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['ok'=>false,'message'=>'Unauthenticated']));
            } else { return redirect('Login'); }
        }

        $nik = $this->session->userdata('nik');
        $this->load->helper('Rupiah_helper');

        // window: dari awal bulan LALU sampai awal bulan DEPAN (menutup 2 bulan: bulan ini & bulan lalu)
        // window: dari awal bulan lalu (Jan) sampai awal bulan depan (Mar) 
        // Logika: 
        // Gaji Jan cair Feb -> Timestamp Feb -> Bucket Feb.
        // Gaji Dec cair Jan -> Timestamp Jan -> Bucket Jan.
        // Kita perlu bucket Feb (untuk Current) dan Jan (untuk Prev).
        $start = (new DateTime('first day of -1 month 00:00:00'))->format('Y-m-d H:i:s');
        $end   = (new DateTime('first day of +1 month 00:00:00'))->format('Y-m-d H:i:s');

        $profile         = $this->Report_model->get_profile_by_nik($nik);    
        $lembaga_aktif   = $this->Report_model->count_pengabdian_aktif($nik);  
        // total per (y,m)
        $buckets = $this->Report_model->sum_barokah_last_two_months($nik, $start, $end);

        // ambil rincian baris untuk list
        $rows = $this->Report_model->list_barokah_last_two_months($nik, $start, $end);

        // tentukan key bulan ini & bulan lalu
        $now     = new DateTime('now');
        $keyNow  = $now->format('Y-m'); // 2026-02
        $prev    = (clone $now)->modify('first day of last month');
        $keyPrev = $prev->format('Y-m'); // 2026-01

        $totalNow  = isset($buckets[$keyNow])  ? (int)$buckets[$keyNow]['total'] : 0;
        $totalPrev = isset($buckets[$keyPrev]) ? (int)$buckets[$keyPrev]['total'] : 0;

        // label untuk UI: pakai label dari tabel (jika ada), fallback nama bulan default
        // label untuk UI: pakai label dari tabel (jika ada), fallback ke Logic Payroll (Bulan - 1)
        $namaBulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        
        // Fallback Date Objects (Mundur 1 bulan dari key bucket)
        // KeyNow = Feb. Fallback Label = Jan.
        $fallbackNowDate  = (clone $now)->modify('-1 month');
        $fallbackPrevDate = (clone $prev)->modify('-1 month');

        $labelNowBulan  = $buckets[$keyNow]['label_bulan']  ?? $namaBulan[(int)$fallbackNowDate->format('n')]  ?? '';
        $labelNowTahun  = $buckets[$keyNow]['label_tahun']  ?? $fallbackNowDate->format('Y');
        $labelPrevBulan = $buckets[$keyPrev]['label_bulan'] ?? $namaBulan[(int)$fallbackPrevDate->format('n')] ?? '';
        $labelPrevTahun = $buckets[$keyPrev]['label_tahun'] ?? $fallbackPrevDate->format('Y');

        // pisahkan rows jadi 2 kelompok (bulan ini & lalu)
        $rincianNow  = array_values(array_filter($rows,  fn($r) => sprintf('%04d-%02d',$r['y'],$r['m']) === $keyNow));
        $rincianPrev = array_values(array_filter($rows,  fn($r) => sprintf('%04d-%02d',$r['y'],$r['m']) === $keyPrev));

        return $this->output->set_content_type('application/json')
            ->set_output(json_encode([
                'ok' => true,
                'nama_gelar'       => $profile['nama_gelar'] ?? '-',
                'lembaga_aktif'    => (int)$lembaga_aktif,
                'label_bulan_ini'  => $labelNowBulan,
                'label_tahun_ini'  => $labelNowTahun,
                'label_bulan_lalu' => $labelPrevBulan,
                'label_tahun_lalu' => $labelPrevTahun,

                'total_bulan_ini'  => $totalNow,
                'total_bulan_ini_formatted'  => rupiah($totalNow),
                'total_bulan_lalu' => $totalPrev,
                'total_bulan_lalu_formatted' => rupiah($totalPrev),

                'rincian_bulan_ini'  => $rincianNow,   // {lembaga, jenis, nominal, label_bulan, label_tahun}
                'rincian_bulan_lalu' => $rincianPrev
            ]));
    }



    public function dashboard_rincian()
    {
        $this->Login_model->getsqurity();
        $nik = $this->session->userdata('nik');

        $struktural = $this->Report_model->list_struktural_aktif($nik);
        $pengajar   = $this->Report_model->list_pengajar_aktif($nik);

        return $this->output->set_content_type('application/json')
            ->set_output(json_encode([
                'ok'         => true,
                'struktural' => $struktural,     // [{nama_lembaga, jabatan}]
                'pengajar'   => $pengajar        // [{nama_lembaga, status_pengajar}]
            ]));
    }

    public function barokah_data() {
        $this->Login_model->getsqurity();
        $nik = $this->session->userdata('nik');
        $k   = strtolower($this->input->get('k') ?: 'struktural');
        $range = (int)($this->input->get('range') ?: 12);
      
        $start = (new DateTime('first day of -'.($range-1).' month 00:00:00'))->format('Y-m-d H:i:s');
        $end   = (new DateTime('first day of +1 month 00:00:00'))->format('Y-m-d H:i:s');
      
        $series = $this->Report_model->sum_bulanan_by_kategori($nik, $k, $start, $end);
        $this->output->set_content_type('application/json')
          ->set_output(json_encode(['ok'=>true,'kategori'=>$k,'series'=>$series]));
      }
      
      // list per-lembaga untuk bulan terpilih
      public function barokah_detail() {
        $this->Login_model->getsqurity();
        $nik = $this->session->userdata('nik');
        $k = strtolower($this->input->get('k') ?: 'struktural');
        $y = (int)$this->input->get('y');
        $m = (int)$this->input->get('m');
      
        $start = (new DateTime("$y-$m-01 00:00:00"))->format('Y-m-d H:i:s');
        $end   = (new DateTime("$y-$m-01 00:00:00"))->modify('first day of +1 month')->format('Y-m-d H:i:s');
      
        $rows = $this->Report_model->detail_bulanan_by_kategori($nik, $k, $start, $end);
        $this->output->set_content_type('application/json')
          ->set_output(json_encode(['ok'=>true,'rows'=>$rows]));
      }
      
      // modal breakdown (komponen per kategori)
    public function barokah_breakdown() {
        $this->Login_model->getsqurity();
        $nik = $this->session->userdata('nik');
    
        $k   = strtolower($this->input->get('k') ?: 'pengajar');
        $y   = (int)$this->input->get('y');
        $m   = (int)$this->input->get('m');
        $lid = $this->input->get('lid'); // id_lembaga / entitas untuk filter (opsional)

        // Parse special LID format: id_lembaga-bulan-tahun (e.g. "4-9-2025")
        $specM = null;
        $specY = null;
        if (strpos($lid, '-') !== false) {
            $parts = explode('-', $lid);
            if (count($parts) >= 3) {
                $lid   = $parts[0];
                $specM = (int)$parts[1];
                $specY = (int)$parts[2];
            }
        }
    
        $start = (new DateTime("$y-$m-01 00:00:00"))->format('Y-m-d H:i:s');
        $end   = (new DateTime("$y-$m-01 00:00:00"))->modify('first day of +1 month')->format('Y-m-d H:i:s');
    
        $this->load->helper('Rupiah_helper');
        $fmt = fn($n)=>rupiah((int)$n);
    
        switch ($k) {
            case 'struktural':
                $data  = $this->Report_model->breakdown_struktural_by_month($nik, $start, $end, $lid, $specM, $specY);
                $items = $this->Report_model->list_potongan_struktural_by_month($nik, $start, $end, $lid);
                $this->load->helper('Rupiah_helper');
                $fmt = fn($n)=>rupiah((int)$n);
            
                $payload = [
                    // raw (integer)
                    'barokah_pokok'     => (int)$data['barokah_pokok'],
                    'kehadiran_nominal' => (int)$data['kehadiran_nominal'],
                    'kehadiran'         => (int)$data['kehadiran'], // Ini akan berisi "jumlah_hadir_normal" alias count
                    'tunkel'            => (int)$data['tunkel'],
                    'tun_anak'          => (int)$data['tun_anak'],
                    'tmp'               => (int)$data['tmp'],
                    'tbk'               => (int)$data['tbk'],
                    'kehormatan'        => (int)$data['kehormatan'],
                    'potongan'          => (int)$data['potongan'],
                    'total'             => (int)$data['total'],
                    // formatted (string)
                    'f'=>[
                        'barokah_pokok'     => $fmt($data['barokah_pokok']??0),
                        'kehadiran_nominal' => $fmt($data['kehadiran_nominal']??0),
                        'kehadiran'         => (int)($data['kehadiran']??0), // Format angka biasa untuk count
                        'tunkel'            => $fmt($data['tunkel']??0),
                        'tun_anak'          => $fmt($data['tun_anak']??0),
                        'tmp'               => $fmt($data['tmp']??0),
                        'tbk'               => $fmt($data['tbk']??0),
                        'kehormatan'        => $fmt($data['kehormatan']??0),
                        'potongan'          => $fmt($data['potongan']??0),
                        'total'             => $fmt($data['total']??0),
                    ],
                    // daftar item potongan
                    'potongan_items' => array_map(
                        fn($r)=>['nama'=>$r['nama'], 'nominal'=>(int)$r['nominal']],
                        $items
                    ),
                ];
                break;
            
    
            // Report.php (di barokah_breakdown) case 'satpam':
                case 'satpam':
                    $data  = $this->Report_model->breakdown_satpam_by_month($nik, $start, $end, $lid, $specM, $specY);
                    $this->load->helper('Rupiah_helper');
                    $fmt = fn($n)=>rupiah((int)$n);
                
                    $payload = [
                      // raw numbers
                      'k1_total'    => (int)$data['k1_total'],
                      'k2_total'    => (int)$data['k2_total'],
                      'k3_total'    => (int)$data['k3_total'],
                      'j_hari'      => (int)$data['j_hari'],
                      'j_shift'     => (int)$data['j_shift'],
                      'j_dinihari'  => (int)$data['j_dinihari'],
                      'k1_unit'     => (int)$data['k1_unit'],   // nominal/UNIT efektif
                      'k2_unit'     => (int)$data['k2_unit'],
                      'k3_unit'     => (int)$data['k3_unit'],
                      'total'       => (int)$data['total'],
                
                      // formatted strings
                      'f'=>[
                        'k1_total'   => $fmt($data['k1_total']??0),
                        'k2_total'   => $fmt($data['k2_total']??0),
                        'k3_total'   => $fmt($data['k3_total']??0),
                        'k1_unit'    => $fmt($data['k1_unit']??0),
                        'k2_unit'    => $fmt($data['k2_unit']??0),
                        'k3_unit'    => $fmt($data['k3_unit']??0),
                        'total'      => $fmt($data['total']??0),
                      ],
                      'potongan_items' => [], // satpam: tidak ada potongan
                    ];
                break;
                
    
            default: // pengajar
                $data = $this->Report_model->breakdown_pengajar_by_month($nik, $start, $end, $lid, $specM, $specY);
                $items = $this->Report_model->list_potongan_pengajar_by_month($nik, $start, $end, $lid ?? null);
                $payload = [
                    'sks'=>(int)$data['sks'],
                    'per_sks'=>(int)$data['per_sks'],
                    'mengajar'=>(int)$data['mengajar'],
                    'gty_dty'=>(int)$data['gty_dty'],
                    'jafung'=>(int)$data['jafung'],
                    'kehadiran'=>(int)$data['kehadiran'],
                    'jhadir'=>(int)$data['jhadir'],
                    'jhadir15'=>(int)$data['jhadir15'],'nhadir15'=>(int)$data['nhadir15'],
                    'jhadir10'=>(int)$data['jhadir10'],'nhadir10'=>(int)$data['nhadir10'],
                    'walkes'=>(int)$data['walkes'],'kehormatan'=>(int)$data['kehormatan'],
                    'tambahan'=>(int)$data['tambahan'],'piket'=>(int)$data['piket'],
                    'tun_anak'=>(int)$data['tun_anak'],'tunkel'=>(int)$data['tunkel'],'potongan'=>(int)$data['potongan'],
                    'total'=>(int)$data['total'],
                    'f'=>[
                        'per_sks'=>$fmt($data['per_sks']??0),
                        'mengajar'=>$fmt($data['mengajar']??0),
                        'gty_dty'=>$fmt($data['gty_dty']??0),
                        'jafung'=>$fmt($data['jafung']??0),
                        'kehadiran'=>$fmt($data['kehadiran']??0),
                        'nhadir15'=>$fmt($data['nhadir15']??0),
                        'nhadir10'=>$fmt($data['nhadir10']??0),
                        'walkes'=>$fmt($data['walkes']??0),
                        'kehormatan'=>$fmt($data['kehormatan']??0),
                        'tambahan'=>$fmt($data['tambahan']??0),
                        'piket'=>$fmt($data['piket']??0),
                        'tun_anak'=>$fmt($data['tun_anak']??0),
                        'tunkel'=>$fmt($data['tunkel']??0),
                        'potongan'=>$fmt($data['potongan']??0),
                        'total'=>$fmt($data['total']??0),
                    ],
                    'potongan_items' => array_map(function($r){
                        return ['nama' => $r['nama'], 'nominal' => (int)$r['nominal']];
                    }, $items),
                ];
        }
    
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['ok'=>true,'k'=>$k,'y'=>$y,'m'=>$m,'data'=>$payload]));
    }
    


}
