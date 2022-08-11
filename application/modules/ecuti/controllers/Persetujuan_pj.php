<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Persetujuan_pj extends MY_Controller {
    public $data = array();
    
    public function __construct() {
        parent::__construct();
        // Your own constructor code
        $this->load->library("Aauth");
        
        if (!$this->aauth->is_loggedin()) {
        	$this->session->set_flashdata('message_type', 'error');
                $this->session->set_flashdata('messages', 'Please login first.');
                redirect('login');
        }

        $this->load->model('Persetujuan_pj_model');
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
    
    public function index(){
        $is_permit = $this->aauth->control_no_redirect('persetujuan_pj_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "E-Cuti";
        $this->data['bc_child'] = "Persetujuan PJ Cuti";
        $perms = "persetujuan_pj_perm";
        $comments = "Halaman Persetujuan PJ Cuti";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('persetujuan_pj', $this->data);
    }
    
    public function ajax_list_cuti(){
        $filter_bln = $this->input->get('filter_bln',TRUE);
        $pj_id = $this->data['pegawai']->id_pegawai;
        $list = $this->Persetujuan_pj_model->get_cuti_list($filter_bln, $pj_id);
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        foreach($list as $review){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $review->pegawai_nama;
            $row[] = $review->jeniscuti_nama;
            $row[] = date('d-M-Y',strtotime($review->tgl_pengajuan));
            $row[] = date('d-M-Y',strtotime($review->tgl_awal));
            $row[] = date('d-M-Y',strtotime($review->tgl_akhir));
            $row[] = $review->alasan;
            $row[] = $review->pengganti_nama;
            $row[] = $review->review_status == 0 ? "Belum Direview" : "Sudah Direview";
            $row[] = ($review->approval_status == 0 ? "Belum Disetujui" : ($review->approval_status = 1 ? "Disetujui" : "Ditolak"));
            //add html for action
            $row[] = '<button type="button" class="btn btn-social-icon btn-info" title="Menyetujui" onclick="reviewCuti('."'".$review->cuti_id."'".')">Review</button>';
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Persetujuan_pj_model->count_cuti_all($filter_bln, $pj_id),
                    "recordsFiltered" => $this->Persetujuan_pj_model->count_cuti_filtered($filter_bln, $pj_id),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function hitung_hari(){
        $tgl_awal = date('Y-m-d',strtotime($this->input->get('tgl_awal',TRUE)));
        $tgl_akhir = date('Y-m-d',strtotime($this->input->get('tgl_akhir',TRUE)));
        $date1 = new DateTime($tgl_awal);
        $date2 = new DateTime($tgl_akhir);
        $interval = $date1->diff($date2);
        $hari = $interval->d;
        
        $res = array(
            "jml_hari" => $hari,
            "success" => true
        );
        
        echo json_encode($res);
    }
    
    public function hitung_hari_tanpa_weekend(){
        $startDate = $this->input->get('tgl_awal',TRUE);
        $endDate = $this->input->get('tgl_akhir',TRUE);
        $begin = strtotime($startDate);
        $end   = strtotime($endDate);
        if ($begin > $end) {
            $jml_days = 0;
        } else {
            $no_days  = 0;
            while ($begin <= $end) {
                $what_day = date("N", $begin);
                if (!in_array($what_day, [6,7]) ) // 6 and 7 are weekend
                    $no_days++;
                $begin += 86400; // +1 day
            };

            $jml_days = $no_days;
        }
        
        $res = array(
            "jml_hari" => $jml_days,
            "success" => true
        );
        
         echo json_encode($res);
    }
    
    function ajax_get_cuti_by_id(){
        $cuti_id = $this->input->get('cuti_id',TRUE);
        $old_data = $this->Persetujuan_pj_model->get_cuti_by_id($cuti_id);
        $peg_cuti = $this->Persetujuan_pj_model->get_pegawai_by_nip($old_data->nip);
        $sisa_cuti = $this->Persetujuan_pj_model->get_sisa_cuti($old_data->pegawai_id, $peg_cuti->tempat_tugas_ket);
        $tgl_awal = date('d-M-Y', strtotime($old_data->tgl_awal));
        $tgl_akhir = date('d-M-Y', strtotime($old_data->tgl_akhir));
        
        if(count((array)$old_data) > 0) {
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'data' => $old_data,
                'sisa_cuti' => $sisa_cuti,
                'tgl_awal' =>$tgl_awal,
                'tgl_akhir' => $tgl_akhir
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "Bagian ID tidak ditemukan");
        }
        echo json_encode($res);
    }
    
    public function do_terima_cuti(){
        $is_permit = $this->aauth->control_no_redirect('persetujuan_pj_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cuti_id', 'ID Cuti', 'required|trim');
        
        if ($this->form_validation->run() == FALSE)  {
            $error = validation_errors();
            //$this->session->set_flashdata('message_type', 'error');
            //$this->session->set_flashdata('messages', $error);
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $error);

            // if permitted, do logit
            $perms = "persetujuan_pj_perm";
            $comments = "Gagal update Data saat menerima cuti dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $cuti_id = $this->input->post('cuti_id',TRUE);
            
            $data_cuti = array(
                'approve_by_pj' => 1
            );
            
            $update = $this->Persetujuan_pj_model->update_cuti($data_cuti, $cuti_id);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Berhasil menyetujui cuti'
                );

                // if permitted, do logit
                $perms = "persetujuan_pj_perm";
                $comments = "Berhasil menyetujui cuti dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal update Data saat menyetujui cuti, hubungi web administrator.');

                // if permitted, do logit
                $perms = "persetujuan_pj_perm";
                $comments = "Gagal update Data saat menyetujui cuti dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function do_tolak_cuti(){
        $is_permit = $this->aauth->control_no_redirect('persetujuan_pj_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cuti_id', 'ID Cuti', 'required|trim');
        
        if ($this->form_validation->run() == FALSE)  {
            $error = validation_errors();
            //$this->session->set_flashdata('message_type', 'error');
            //$this->session->set_flashdata('messages', $error);
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $error);

            // if permitted, do logit
            $perms = "persetujuan_pj_perm";
            $comments = "Gagal update Data saat menolak cuti dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $cuti_id = $this->input->post('cuti_id',TRUE);
            
            $data_cuti = array(
                'approve_by_pj' => 2
            );
            
            $update = $this->Persetujuan_pj_model->update_cuti($data_cuti, $cuti_id);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Berhasil menolak cuti'
                );

                // if permitted, do logit
                $perms = "persetujuan_pj_perm";
                $comments = "Berhasil menolak cuti dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal update Data saat menolak cuti, hubungi web administrator.');

                // if permitted, do logit
                $perms = "persetujuan_pj_perm";
                $comments = "Gagal update Data saat menolak cuti dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
}