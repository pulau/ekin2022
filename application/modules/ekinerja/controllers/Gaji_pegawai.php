<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * gaji pegawai
 */

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Gaji_pegawai extends MY_Controller
{
	public $data = array();

	function __construct()
	{
		parent::__construct();
        // Your own constructor code
        $this->load->library("Aauth");
        
        if (!$this->aauth->is_loggedin()) {
        	$this->session->set_flashdata('message_type', 'error');
                $this->session->set_flashdata('messages', 'Please login first.');
                redirect('login');
        }
        
        $this->load->model('Gaji_pegawai_model');
        $this->load->model('Utama_model');
        $this->data['modul'] = $this->aauth->get_module_id($this->uri->segment(1));
        $this->data['users']                = $this->aauth->get_user();
        $this->data['groups']               = $this->aauth->get_user_groups();
        $this->data['pegawai'] = $this->Utama_model->get_pegawai_by_nip($this->data['users']->nip);
        $bagian = !empty($this->data['pegawai']) ? $this->data['pegawai']->bagian : "";
        $this->data['bagian'] = $this->Utama_model->get_bagian_by_id($bagian);
        $groups = "";
        foreach ($this->data['groups'] as $key => $val){
            $groups .= $val->group_id.","; 
        }
        $this->data['group_arr'] = substr_replace($groups, "", -1);
        $this->data['menu_list'] = $this->Utama_model->get_list_menu($this->data['group_arr'], $this->data['modul']);
	}

	function index()
	{
		$is_permit = $this->aauth->control_no_redirect('gaji_pegawai_ekinerja_perm');
		if (!$is_permit) {
			$this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
			redirect('no_permission');
		}

		$this->data['bc_parent']	= "Gaji Pegawai";
		$this->data['bc_child']		= "Dashboard";
		$perms = 'gaji_pegawai_ekinerja_perm';
		$comments = "Halaman Dashboard Gaji Pegawai";
		$this->aauth->logit($perms, current_url(), $comments);
		$this->load->view('gaji_pegawai', $this->data);

	}

	public function ajax_list_gaji_pegawai(){
        $filter_bln = $this->input->get('filter_bln',TRUE);
        $list = $this->Gaji_pegawai_model->get_gaji_pegawai_list($filter_bln);
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        
        foreach($list as $gaji){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $gaji->nip;
            $row[] = $gaji->nama_pegawai;
            $row[] = $gaji->masakerja." Tahun";
            $row[] = "Rp. ".number_format($gaji->gapok,2,',','.');
            $row[] = "Rp. ".number_format($gaji->tun_susi,2,',','.');
            $row[] = "Rp. ".number_format($gaji->tun_anak,2,',','.');
            $row[] = "Rp. ".number_format($gaji->transport,2,',','.');
            $row[] = "Rp. ".number_format($gaji->jumlah,2,',','.');
            $row[] = "Rp. ".number_format($gaji->pph21,2,',','.');
            $row[] = "Rp. ".number_format($gaji->terima_gaji,2,',','.');
            
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Gaji_pegawai_model->get_gaji_pegawai_all($filter_bln),
                    "recordsFiltered" => $this->Gaji_pegawai_model->count_gaji_pegawai_filtered($filter_bln),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }

    public function do_sinkronisasi(){
    	# cek permission
        $is_permit = $this->aauth->control_no_redirect('gaji_pegawai_ekinerja_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        $filter_bln = $this->input->post('bln_sinkronisasi',TRUE);
        $pegawai_list = $this->Gaji_pegawai_model->get_pegawai_nonpns();
        $jml_pegawai = sizeof($pegawai_list);
        $i = 0;
        $result = false;
        
        while ($i < $jml_pegawai){
            $id_pegawai = $pegawai_list[$i]->id_pegawai;
            $bulan = date('Ym', strtotime($filter_bln));
            // $bulan = '202007'; //comment this 
            $tahun = date('Y');

            $get_gapok = $this->Gaji_pegawai_model->get_gaji_pegawai_by_id($bulan,$id_pegawai);
            foreach ($get_gapok as $key) {
            	$nip = $key->nip;
            	$nama_pegawai =  $key->nama_pegawai;
            	$tgl_masuk =  $key->tgl_masuk;
            	// $pendidikan = $key->pendidikan_nama;
            	$nominal_gaji = $key->nominal_gaji;
            	$statuspegawai = $key->statuspegawai_nama ;

            }
            # hitung masa kerja pegawai
            $masakerja = $tahun - date('Y', strtotime($tgl_masuk));

            # hitung gapok, tunjangan istri, tunjangan anak, total gaji
            if ($statuspegawai == 'K1') {
    		# ambil tunjangan istri x 10% dari gaji
    		$tunjangan_susi = $nominal_gaji * 0.1;
	    	}elseif ($statuspegawai == 'K2') {
	    		# ambil tunjangan istri x 10%
	    		# ambil tunjangan 1 anak x 2%
	    		$tunjangan_susi = $nominal_gaji * 0.1;
	    		$tunjangan_anak = $nominal_gaji * 0.02;
	    	}elseif ($statuspegawai == 'K3') {
	    		# ambil tunjangan istri x 10%
	    		# ambil tunjangan 2 anak x 4%
	    		$tunjangan_susi = $nominal_gaji * 0.1;
	    		$tunjangan_anak = $nominal_gaji * 0.04;
	    	}else{
	    		#if statuspegawai = K0
	    		#ambil gapok x 1
	    		$nominal_gaji;
	    	}
	    	$susi = isset($tunjangan_susi) ? $tunjangan_susi : 0;
	    	$anak = isset($tunjangan_anak) ? $tunjangan_anak : 0;
	    	$transport = $nominal_gaji * 0.2;
	    	$total = $nominal_gaji + $susi + $anak + $transport;
	    	$total_gaji = round($total);

	    	# hitung pph21 disini
	    	/**
	    	
	    		TODO:
	    		- hitung pph 21
	    		- Second todo item
	    		=IF(Y89<=50000000;(Y89*5%);IF(Y89<=250000000;(2500000+(Y89-50000000)*15%);IF(Y89<=500000000;(2500000+3750000+(Y89-300000000)*30%))))
				=IF(J38<=50000000;(J38*5%);IF(J38<=250000000;(2500000+(J38-50000000)*15%);IF(J38<=500000000;(2500000+3750000+(J38-300000000)*30%))))
				jika PKP pertahun <= 50.000.000 	= PKP * 5%
					JIKA PKP <= 250.000.000			= 2.500.000 + (PKP - 50.000.000)*15%
						JIKA PKP <= 500.000.000		= 2.500.000 + 3.750.000 + (PKP- 300.000.000) * 30%
	    	
	    	 */
	    	$pajak = $this->Gaji_pegawai_model->get_pajak_pegawai_by_id($id_pegawai);
	    	foreach ($pajak as $key) {
	    		$ptkp = $key->ptkp;
	    	}
	    	
	    	# penghasilan bruto :
	    	# gaji pokok = $nominal_gaji
	    	# transport = $transport
	    	# TKD = ?
	    	# Premi jaminan kematian = $nominal_gaji * 0.04
	    	//get prestai pegawai
            $prestasi_kerja = 0;
	    	$data_prestasi = $this->Gaji_pegawai_model->get_capaian_pegawai($id_pegawai, $bulan);
	    	foreach ($data_prestasi as $key) {
	    		$prestasi_kerja = $key->total_capaian_akhir;
	    	}
	    	//get rumpun pegawai
	    	$rumpun_pegawai = $this->Gaji_pegawai_model->get_rumpun_pegawai($id_pegawai);
	    	foreach ($rumpun_pegawai as $key) {
	    		$rumpun = $key->rumpun_nilai;
	    	}
	    	// $prestasi = $prestasi_kerja/100;
	    	$prestasi = 100/100;	//comment this
	    	$tkd = $nominal_gaji * $rumpun * $prestasi;
	    	// $tkd = $nominal_gaji * 0.528668864 * $prestasi;	//comment this
	    	$premi_kematian = $nominal_gaji * 0.04;
	    	$bruto = $nominal_gaji + $transport + $tkd + $premi_kematian;

			# Pengurangan :
			# jika penghasilan bruto * 5% >=500.000 = 500.000
			# 	else penghasilan bruto * 5%   	
			if ($bruto * 0.05 >= 500000) {
				# code...
				$pengurangan = 500000;
			} else {
				# code...
				$pengurangan = $bruto * 0.05;
			}
			
			$neto_bulan = $bruto - $pengurangan;
			$neto_tahun = $neto_bulan * 12;
			if ($neto_tahun - $ptkp < 0) {
				$pkp = 0;
			} else {
				$pkp = $neto_tahun - $ptkp;
			}
			
			if ($pkp <= 50000000) {
				$pph21_tahun = $pkp * 0.05;
			}elseif ($pkp <= 250000000) {
				$pph21_tahun = 2500000 + ($pkp - 50000000) * 0.15;
			}elseif ($pkp <= 500000000) {
				$pph21_tahun = 2500000 + 3750000 + ($pkp - 300000000) * 0.3;
			}

			$pph21 = $pph21_tahun / 12;
			

            $data_gaji = array(
            	'bulan' => $bulan,
            	'pegawai_id' => $id_pegawai,
            	'masakerja' => $masakerja,
            	// 'tkd' => round($tkd),
            	// 'premi' => round($premi_kematian),
            	// 'bruto' => round($bruto),
            	'gapok' => round($nominal_gaji),
            	'tun_susi' => round($susi),
            	'tun_anak' => round($anak),
            	'transport' => round($transport),
            	'jumlah' => round($total_gaji),
            	'pph21' => round($pph21),
            	'terima_gaji' => round($total_gaji - $pph21),
            	'sync_by' => $this->data['pegawai']->id_pegawai

            );
            // echo "<pre>";
            // print_r($data_gaji);

            $check_constrain = $this->Gaji_pegawai_model->count_gaji_pegawai_all($id_pegawai, $bulan);
            if ($check_constrain > 0) {
            	# do update
            	$ins = $this->Gaji_pegawai_model->update_gaji_pegawai($data_gaji, $bulan, $id_pegawai);
            } else {
            	# do insert
            	$ins = $this->Gaji_pegawai_model->insert_into_gaji_pegawai($data_gaji);
            }

            if ($ins) {
            	# code...
            	$result = true;
            }
            $i++;
        }
        
        if($result){                
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'Sinkronisasi Berhasil'
            );

            // if permitted, do logit
            $perms = "gaji_pegawai_ekinerja_perm";
            $comments = "Berhasil Sinkronisasi gaji pegawai";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
            'csrfTokenName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
            'success' => false,
            'messages' => 'Gagal Sinkronisasi, hubungi web administrator.');

            // if permitted, do logit
            $perms = "gaji_pegawai_ekinerja_perm";
            $comments = "Gagal Sinkronisasi gaji pegawai";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        echo json_encode($res);
    }

    public function export_gaji_pegawai($filter_bln){
        $filter_bln = ($filter_bln == "undefined") ? "" : $filter_bln;
        $filter_bln = str_replace('%20', ' ', $filter_bln);
        
       // $filter_bln = $this->input->get('filter_bln',TRUE);
        $data = $this->Gaji_pegawai_model->get_gaji_pegawai_excel($filter_bln);
        
        $fname="Gaji_pegawai_".date('d_M_Y_H:i:s').".xls";
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=$fname");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        echo'<table border="0" bordercolor="#333333" cellpadding="0" cellspacing="0">';
        $bulan = date("M Y",strtotime($filter_bln));
        echo'<tr><td colspan=5><font size=4><b>Gaji Pegawai Bulan '.$bulan.'</b></font></td></tr>';
        echo '</table><br><br>';
        
        echo'<table border="1" bordercolor="#333333" cellpadding="0" cellspacing="0">';
        echo'<tr bgcolor=#b7b7b7>';
        echo "<td style='font-weight:bolder;text-align:center;'>No</td>";
        echo "<td style='font-weight:bolder;text-align:center;'>NIP</td>";
        echo "<td style='font-weight:bolder;text-align:center;'>Nama</td>";
        echo "<td style='font-weight:bolder;text-align:center;'>Masa Kerja</td>";
        echo "<td style='font-weight:bolder;text-align:center;'>Gaji Pokok</td>";
        echo "<td style='font-weight:bolder;text-align:center;'>Tunj. Suami/Istri</td>";
        echo "<td style='font-weight:bolder;text-align:center;'>Tunj. Anak</td>";
        echo "<td style='font-weight:bolder;text-align:center;'>Transport</td>";
        echo "<td style='font-weight:bolder;text-align:center;'>Jumlah</td>";
        echo "<td style='font-weight:bolder;text-align:center;'>PPh 21</td>";
        echo "<td style='font-weight:bolder;text-align:center;'>Total Terima</td>";
        echo'</tr>';

        if(count((array)$data)>0){
            $no=0;
            foreach($data as $gaji){
                $no++;
                echo'<tr>';
                echo "<td style='text-align:left;'>".$no."</td>";
                echo "<td style='text-align:left;'>"."'".$gaji->nip."</td>";
                echo "<td style='text-align:left;'>".$gaji->nama_pegawai."</td>";
                echo "<td style='text-align:left;'>".$gaji->masakerja." Tahun"."</td>";
                echo "<td style='text-align:left;'>"."Rp. ".number_format($gaji->gapok,2,",",".")."</td>";
                echo "<td style='text-align:left;'>"."Rp. ".number_format($gaji->tun_susi,2,",",".")."</td>";
                echo "<td style='text-align:left;'>"."Rp. ".number_format($gaji->tun_anak,2,",",".")."</td>";
                echo "<td style='text-align:left;'>"."Rp. ".number_format($gaji->transport,2,",",".")."</td>";
                echo "<td style='text-align:left;'>"."Rp. ".number_format($gaji->jumlah,2,",",".")."</td>";
                echo "<td style='text-align:left;'>"."Rp. ".number_format($gaji->pph21,2,",",".")."</td>";
                echo "<td style='text-align:left;'>"."Rp. ".number_format($gaji->terima_gaji,2,",",".")."</td>";
                echo'</tr>';
            }
        }else{
            echo'<tr>';
            echo "<td style='text-align:center;' colspan='8'>No Data</td>";
            echo'</tr>';
        }

        echo'</table><br>';
        echo 'print date : '.date('d-M-Y H:i:s');
    }

}