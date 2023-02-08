<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prestasi_pegawai extends MY_Controller {
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
        
        $this->load->model('Prestasi_kerja_model');
        $this->load->model('Capaian_kinerja_model');
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
        $is_permit = $this->aauth->control_no_redirect('prestasi_pegawai_ekinerja_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "E-Kinerja";
        $this->data['bc_child'] = "Prestasi Pegawai";
        //$this->data['serapan'] = $this->Capaian_kinerja_model->get_serapan_bulan(date('M Y'));
        $perms = "prestasi_pegawai_ekinerja_perm";
        $comments = "Halaman Prestasi Kerja Pegawai";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('prestasi_kerja', $this->data);
    }
    
    public function ajax_list_prestasi_pegawai(){
        $filter_bln = $this->input->get('filter_bln',TRUE);
        $list = $this->Prestasi_kerja_model->get_prestasi_pegawai_list($filter_bln);
        $serapan = $this->Capaian_kinerja_model->get_serapan_bulan($filter_bln);
        $persen_serapan = isset($serapan->nilai) ? $serapan->nilai : 0;
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        
        foreach($list as $pegawai){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $pegawai->nip;
            $row[] = $pegawai->nama_pegawai;
            $row[] = number_format($pegawai->total_persen_capaian,2)." %";
            $row[] = $pegawai->persen_capaian70." %" ;
            $row[] = number_format($pegawai->total_poin_perilaku,2)." %";
            $row[] = $persen_serapan." %";
            //$row[] = $pegawai->persen_pengurang." %";
            $row[] = number_format($pegawai->total_capaian_akhir,2)." %";
                 
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Prestasi_kerja_model->get_prestasi_pegawai_all($filter_bln),
                    "recordsFiltered" => $this->Prestasi_kerja_model->get_prestasi_pegawai_filtered($filter_bln),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function export_excel_pegawai($filter_bln){
        $filter_bln = ($filter_bln == "undefined") ? "" : $filter_bln;
        $filter_bln = str_replace('%20', ' ', $filter_bln);
        
       // $filter_bln = $this->input->get('filter_bln',TRUE);
        $list = $this->Prestasi_kerja_model->get_prestasi_pegawai_excel($filter_bln);
        
        $fname="Prestasi_Kerja_pegawai_".date('d_M_Y_H:i:s').".xls";
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=$fname");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        echo'<table border="0" bordercolor="#333333" cellpadding="0" cellspacing="0">';
        $bulan = date("M Y",strtotime($filter_bln));
        echo'<tr><td colspan=5><font size=4><b>Capaian Kinerja Pegawai Bulan '.$bulan.'</b></font></td></tr>';
        echo '</table><br><br>';
        
        echo'<table border="1" bordercolor="#333333" cellpadding="0" cellspacing="0">';
        echo'<tr bgcolor=#b7b7b7>';
        echo "<td style='font-weight:bolder;text-align:center;'>No</td>";
        echo "<td style='font-weight:bolder;text-align:center;'>NIP</td>";
        echo "<td style='font-weight:bolder;text-align:center;'>Nama</td>";
        echo "<td style='font-weight:bolder;text-align:center;'>Nilai Aktifitas</td>";
        echo "<td style='font-weight:bolder;text-align:center;'>Aktifitas (Bobot 70%)</td>";
        echo "<td style='font-weight:bolder;text-align:center;'>Perilaku (Bobot 10%)</td>";
        echo "<td style='font-weight:bolder;text-align:center;'>Serapan (Bobot 20%)</td>";
        //echo "<td style='font-weight:bolder;text-align:center;'>Pengurangan (S/I/A)</td>";
        echo "<td style='font-weight:bolder;text-align:center;'>Total Nilai</td>";
        echo'</tr>';

        if(count((array)$list)>0){
            $no=0;
            $serapan = $this->Capaian_kinerja_model->get_serapan_bulan($filter_bln);
            $point_serapan = isset($serapan->nilai) ? $serapan->nilai : 0;
            foreach($list as $pegawai){
                $no++;
                echo'<tr>';
                echo "<td style='text-align:left;'>".$no."</td>";
                echo "<td style='text-align:left;'>"."'".$pegawai->nip."</td>";
                echo "<td style='text-align:left;'>".$pegawai->nama_pegawai."</td>";
                echo "<td style='text-align:left;'>".number_format($pegawai->total_persen_capaian,2)." %"."</td>";
                $perc_capaian = '<p align="right">'.$pegawai->persen_capaian70." %".'</p>';
                echo "<td style='text-align:left;'>".$perc_capaian."</td>";
                $perc_prilaku = '<p align="right">'.number_format($pegawai->total_poin_perilaku,2)." %".'</p>';
                echo "<td style='text-align:left;'>".$perc_prilaku."</td>";
                echo "<td style='text-align:left;'>".$point_serapan."</td>";
                //echo "<td style='text-align:left;'>".$pegawai->persen_pengurang."</td>";
                $total_nilai = '<p align="right">'.number_format($pegawai->total_capaian_akhir,2)." %".'</p>';
                echo "<td style='text-align:left;'>".$total_nilai."</td>";
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
    
    public function do_sinkronisasi(){
        $is_permit = $this->aauth->control_no_redirect('prestasi_pegawai_ekinerja_perm');
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
        $pegawai_list = $this->Prestasi_kerja_model->get_pegawai();
        $jml_pegawai = sizeof($pegawai_list);
        $jml_hari_kerja = $this->Capaian_kinerja_model->get_jumlah_hari_kerja($filter_bln);
        $hari_kerja = isset($jml_hari_kerja->jml_hari) ? $jml_hari_kerja->jml_hari : 0;
        $menit_per_hari = isset($jml_hari_kerja->menit_per_hari) ? $jml_hari_kerja->menit_per_hari : 0;
        $serapan = $this->Capaian_kinerja_model->get_serapan_bulan($filter_bln);
        $persen_serapan = isset($serapan->nilai) ? $serapan->nilai : 0;
        $i = 0;
        $result = false;
        
        while ($i < $jml_pegawai){
            $id_pegawai = $pegawai_list[$i]->id_pegawai;
            $bulan = date('Ym', strtotime($filter_bln));
            $get_poin_capaian = $this->Capaian_kinerja_model->get_jumlah_capaian($id_pegawai, $filter_bln);
            $get_perilaku = $this->Capaian_kinerja_model->get_perilaku($id_pegawai, $filter_bln);
            $persen_perilaku = isset($get_perilaku->persen_prilaku) ? $get_perilaku->persen_prilaku : 0;
            $nilai_capaian = isset($get_poin_capaian->capaian) ? $get_poin_capaian->capaian : 0;
            
            $waktu_kurang = $this->Capaian_kinerja_model->get_jumlah_tidak_masuk($filter_bln, $id_pegawai);
            $tempat_tugas = $pegawai_list[$i]->tempat_tugas;
            if($tempat_tugas == 8 or $tempat_tugas == 9){
                $menit_kerja = JML_MENIT_SHIFT;
            }else{
                $menit_kerja = intval($hari_kerja) * $menit_per_hari;
            }
            // $menit_izin = $waktu_kurang->izin * $menit_per_hari;
            // print_r($menit_izin);
            // die;
            $menit_alfa = $menit_per_hari*2;
            $menit_tanpa_alasan = isset($waktu_kurang->tanpa_alasan) ? intval($waktu_kurang->tanpa_alasan)*$menit_alfa : 0;
            $menit_terlambat = isset($waktu_kurang->terlambat_menit) ? $waktu_kurang->terlambat_menit : 0;
            $menit_plg_cepat = isset($waktu_kurang->pulang_cepat_menit) ? $waktu_kurang->pulang_cepat_menit : 0;
            $menit_izin = isset($waktu_kurang->izin) ? intval($waktu_kurang->izin)*$menit_per_hari : 0;
            $menit_sakit = isset($waktu_kurang->sakit) ? intval($waktu_kurang->sakit)*$menit_per_hari : 0;
            $ish_pengurangan = $menit_per_hari/2;
            $menit_izin_setengah_hari = isset($waktu_kurang->izin_setengah_hari) ? intval($waktu_kurang->izin_setengah_hari)* $ish_pengurangan : 0;
            $ssd_pengurangan = $menit_per_hari - 60;
            $menit_sakit_srt_dokter = isset($waktu_kurang->sakit_srt_dokter) ? intval($waktu_kurang->sakit_srt_dokter)* $ssd_pengurangan : 0;
            $cs_pengurangan = $menit_per_hari-60;
            $menit_cuti_sakit = isset($waktu_kurang->cuti_sakit) ? intval($waktu_kurang->cuti_sakit)* $cs_pengurangan : 0;
            $total_pengurangan = $menit_tanpa_alasan + $menit_terlambat + $menit_plg_cepat + $menit_izin + $menit_sakit + $menit_izin_setengah_hari + $menit_sakit_srt_dokter + $menit_cuti_sakit;
            $hasil_pengurangan = $menit_kerja - $total_pengurangan;

            $menit_cuti_alasan_penting = isset($waktu_kurang->cuti_alasan_penting) ? intval($waktu_kurang->cuti_alasan_penting)*$menit_per_hari : 0;
            $menit_isoman = isset($waktu_kurang->covid) ? intval($waktu_kurang->covid)*$menit_per_hari : 0;
            $menit_ranapc19 = isset($waktu_kurang->ranapc19) ? intval($waktu_kurang->ranapc19)*$menit_per_hari : 0;
            $menit_cuti_tahunan = isset($waktu_kurang->cuti_tahunan) ? intval($waktu_kurang->cuti_tahunan)* $menit_per_hari : 0;
            
            $cuti_bersalin = isset($waktu_kurang->cuti_bersalin) ? intval($waktu_kurang->cuti_bersalin) : 0;
            $menit_cuti_bersalin = isset($waktu_kurang->cuti_bersalin) ? intval($waktu_kurang->cuti_bersalin)* $menit_per_hari : 0;
            $menit_cuti_besar = isset($waktu_kurang->cuti_besar) ? intval($waktu_kurang->cuti_besar)* $menit_per_hari : 0;
            $menit_dinas_luar_akhir = isset($waktu_kurang->dinas_luar_akhir) ? intval($waktu_kurang->dinas_luar_akhir)* $menit_per_hari : 0;
            $menit_dinas_luar_awal = isset($waktu_kurang->dinas_luar_awal) ? intval($waktu_kurang->dinas_luar_awal)* $menit_per_hari : 0;
            $menit_tidak_terbaca = isset($waktu_kurang->tidak_terbaca) ? intval($waktu_kurang->tidak_terbaca)* $menit_per_hari : 0;
            $menit_dinas_luar_penuh = isset($waktu_kurang->dinas_luar_penuh) ? intval($waktu_kurang->dinas_luar_penuh)* $menit_per_hari : 0;
            $menit_cuti_bersalin_ak3 = isset($waktu_kurang->cuti_bersalin_ak3) ? intval($waktu_kurang->cuti_bersalin_ak3)* $menit_per_hari : 0;
            $menit_cuti_sakit_ranap_rs = isset($waktu_kurang->cuti_sakit_ranap_rs) ? intval($waktu_kurang->cuti_sakit_ranap_rs)* $menit_per_hari : 0;
            $total_penambahan = $menit_cuti_alasan_penting + $menit_isoman + $menit_ranapc19 + $menit_cuti_tahunan + $menit_cuti_bersalin + $menit_cuti_besar + $menit_dinas_luar_akhir + $menit_dinas_luar_awal + $menit_tidak_terbaca + $menit_dinas_luar_penuh + $menit_cuti_bersalin_ak3 + $menit_cuti_sakit_ranap_rs;
            $hasil_penambahan = $nilai_capaian + $total_penambahan;
            $poin_capaian = min($hasil_penambahan,$hasil_pengurangan);

           // $persen_kurang_izin = isset($waktu_kurang->izin) ? intval($waktu_kurang->izin)*2 : 0;
           // $persen_kurang_sakit = isset($waktu_kurang->sakit) ? intval($waktu_kurang->sakit)*1 : 0;
           // $persen_kurang_tnp_alasan = isset($waktu_kurang->tanpa_alasan) ? intval($waktu_kurang->tanpa_alasan)*5 : 0;
           // $persen_kurang = $persen_kurang_izin+$persen_kurang_sakit+$persen_kurang_tnp_alasan;
            if ($poin_capaian == 0){
                $persen_capaian = 0;
                $capaian70 = 0;
            }else{
                $persen_capaian = ($poin_capaian/$menit_kerja)*100;
                //$persen_capaian = $persen_capaian-$persen_kurang;
                $capaian70 = $persen_capaian*0.7;
            }
            
            $capaian70_fix = ($capaian70 >= 70) ? 70 : number_format($capaian70,2);
            $total_nilai = $capaian70_fix+$persen_perilaku+$persen_serapan;

            //jika cuti > 10 persentasi kinerja dibagi 2
            
            if($cuti_bersalin > 10){
                //jumlah hari kerja efektif dibagi 2
                $total_nilai = $total_nilai / 2;
            }else {
                //jika < 10
                $total_nilai;
            }
            
            $data_prestasi = array(
                'bulan' => $bulan,
                'pegawai_id' => $id_pegawai,
                'total_poin_aktifitas' => $nilai_capaian,
                'total_poin_perilaku' => $persen_perilaku,
                'total_penambah' => $total_penambahan,
                'total_capaian_efektif' => $hasil_penambahan,
                'total_jam_kerja' => $menit_kerja,
                'total_pengurang' => $total_pengurangan,
                'batas_maksimal_efektif' => $hasil_pengurangan,
                'poin_capaian' => $poin_capaian,
                'persen_pengurang' => 0,
                'persen_serapan' => $persen_serapan,
                'total_persen_capaian' => $persen_capaian,
                'persen_capaian70' => $capaian70_fix,
                'total_capaian_akhir' => $total_nilai,
                'sync_by' => $this->data['pegawai']->id_pegawai
            );
            
            $check_constrain = $this->Prestasi_kerja_model->count_capaian_pegawai($bulan, $id_pegawai);
            if($check_constrain > 0){
                //$ins = false;
                $ins = $this->Prestasi_kerja_model->update_prestasikerja($data_prestasi, $bulan, $id_pegawai);
            }else {
                $ins = $this->Prestasi_kerja_model->insert_into_prestasikerja($data_prestasi);
            }
            if($ins){
                $result = true;
                $persen = (($i+1)/$jml_pegawai)*100;
               // echo '<script language="javascript">';
               // echo '$("#persen_bar").html('.$persen.');';
               // echo '</script>';
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
            $perms = "prestasi_pegawai_ekinerja_perm";
            $comments = "Berhasil Sinkronisasi prestasi pegawai";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
            'csrfTokenName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
            'success' => false,
            'messages' => 'Gagal Sinkronisasi, hubungi web administrator.');

            // if permitted, do logit
            $perms = "prestasi_pegawai_ekinerja_perm";
            $comments = "Gagal Sinkronisasi prestasi pegawai";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        echo json_encode($res);
    }
}