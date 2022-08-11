<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Review_cuti extends MY_Controller {
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

        $this->load->model('Review_cuti_model');
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
        $is_permit = $this->aauth->control_no_redirect('review_cuti_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "E-Cuti";
        $this->data['bc_child'] = "Review Cuti";
        $perms = "review_cuti_perm";
        $comments = "Halaman Review Cuti";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('review_cuti', $this->data);
    }
    
    public function ajax_list_review(){
        $filter_bln = $this->input->get('filter_bln',TRUE);
        $list = $this->Review_cuti_model->get_review_list($filter_bln);
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
            $row[] = $review->pj_cuti;
            $row[] = $review->approve_by_pj == 0 ? "Belum Disetujui" : ($review->approve_by_pj == 1 ? "Disetujui" : "Ditolak");
            $row[] = $review->review_status == 0 ? "Belum Direview" : ($review->review_status == 1 ? "Disetujui" : "Ditolak");
            if($review->approval_status == 0){
                $btn_setuju = '<button type="button" class="btn btn-social-icon btn-info" title="Menyetujui" onclick="setujuiCuti('."'".$review->cuti_id."'".')"><i class="fa fa-check"></i></button>';
            }else{
                $btn_setuju = '<button type="button" class="btn btn-social-icon btn-info" title="Menyetujui" disabled><i class="fa fa-check"></i></button>';
            }
            $row[] = ($review->approval_status == 0 ? "Belum Disetujui" : ($review->approval_status == 1 ? "Disetujui" : "Ditolak")).'&nbsp'.$btn_setuju;
            //add html for action
            if($review->approve_by_pj == 0){
                $row[] = '<button type="button" class="btn btn-social-icon btn-info" title="Review" onclick="reviewCuti('."'".$review->cuti_id."'".')"><i class="fa fa-pencil-square-o"></i></button>&nbsp;
                        <button type="button" class="btn btn-social-icon btn-danger" title="Hapus" onclick="deleteCuti('."'".$review->cuti_id."'".')"><i class="fa fa-trash-o"></i></button>';
            }else{
                $row[] = '<button type="button" class="btn btn-social-icon btn-info" title="Review" disabled><i class="fa fa-pencil-square-o"></i></button>
                <button type="button" class="btn btn-social-icon btn-danger" title="Hapus" onclick="deleteCuti('."'".$review->cuti_id."'".')"><i class="fa fa-trash-o"></i></button>';
            }
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Review_cuti_model->count_review_all($filter_bln),
                    "recordsFiltered" => $this->Review_cuti_model->count_review_filtered($filter_bln),
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
        $old_data = $this->Review_cuti_model->get_cuti_by_id($cuti_id);
        $peg_cuti = $this->Review_cuti_model->get_pegawai_by_nip($old_data->nip);
        $sisa_cuti = $this->Review_cuti_model->get_sisa_cuti($old_data->pegawai_id, $peg_cuti->tempat_tugas_ket);
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
    
    function set_bagian_koordinator(){
        $pegawai_id = $this->input->get('pegawai_id',TRUE);
        $peg_cuti = $this->Review_cuti_model->get_pegawai_by_id($pegawai_id);
        $sisa_cuti = $this->Review_cuti_model->get_sisa_cuti($peg_cuti->id_pegawai, $peg_cuti->tempat_tugas_ket);
        $bagian = $this->Utama_model->get_bagian_by_id($peg_cuti->bagian);
        $koordinator = isset($peg_cuti->pj_cuti) ? $peg_cuti->pj_cuti : $bagian->pj_cuti;
        
        
        if(!empty($pegawai_id)) {
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'bagian' => $bagian,
                'sisa_cuti' => $sisa_cuti,
                'koordinator' =>$koordinator
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "Id Pegawai tidak ditemukan");
        }
        echo json_encode($res);
    }
    
    public function do_terima_cuti(){
        $is_permit = $this->aauth->control_no_redirect('review_cuti_perm');
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
            $perms = "review_cuti_perm";
            $comments = "Gagal update Data saat menerima cuti dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $cuti_id = $this->input->post('cuti_id',TRUE);
            
            $data_cuti = array(
                'review_status' => 1,
                'reviewed_date' => date('Y-m-d H:i:s'),
                'reviewed_by' => $this->data['users']->id
            );
            
            $update = $this->Review_cuti_model->update_cuti($data_cuti, $cuti_id);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Berhasil me-review cuti'
                );

                // if permitted, do logit
                $perms = "review_cuti_perm";
                $comments = "Berhasil me-review cuti dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal update Data saat menerima cuti, hubungi web administrator.');

                // if permitted, do logit
                $perms = "review_cuti_perm";
                $comments = "Gagal update Data saat menerima cuti dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function do_tolak_cuti(){
        $is_permit = $this->aauth->control_no_redirect('review_cuti_perm');
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
            $perms = "review_cuti_perm";
            $comments = "Gagal update Data saat menolak cuti dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $cuti_id = $this->input->post('cuti_id',TRUE);
            
            $data_cuti = array(
                'review_status' => 2,
                'reviewed_date' => date('Y-m-d H:i:s'),
                'reviewed_by' => $this->data['users']->id
            );
            
            $update = $this->Review_cuti_model->update_cuti($data_cuti, $cuti_id);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Berhasil menolak cuti'
                );

                // if permitted, do logit
                $perms = "review_cuti_perm";
                $comments = "Berhasil menolak cuti dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal update Data saat menolak cuti, hubungi web administrator.');

                // if permitted, do logit
                $perms = "review_cuti_perm";
                $comments = "Gagal update Data saat menolak cuti dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function do_delete_cuti(){
        $is_permit = $this->aauth->control_no_redirect('review_cuti_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $cuti_id = $this->input->post('cuti_id', TRUE);

        $delete = $this->Review_cuti_model->delete_cuti($cuti_id);
        if($delete){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'Data cuti berhasil dihapus'
            );
            // if permitted, do logit
            $perms = "review_cuti_perm";
            $comments = "Berhasil menghapus Cuti dengan id = '". $cuti_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menghapus data, silakan hubungi web administrator.'
            );
            // if permitted, do logit
            $perms = "review_cuti_perm";
            $comments = "Gagal menghapus data dengan ID = '". $cuti_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
    
    public function do_setujui_cuti(){
        $is_permit = $this->aauth->control_no_redirect('review_cuti_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $cuti_id = $this->input->post('cuti_id',TRUE);
        if (empty($cuti_id))  {
            $error = 'Gagal update Data saat menerima cuti, cuti id tidak ditemukan';
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $error);

            // if permitted, do logit
            $perms = "review_cuti_perm";
            $comments = "Gagal update Data saat menerima cuti, cuti id tidak ditemukan";
            $this->aauth->logit($perms, current_url(), $comments);

        }else{
            $data_cuti = array(
                'review_status' => 1,
                'approve_by_tu' => 1,
                'approve_by_pj' => 1,
                'approval_status' => 1,
                'reviewed_date' => date('Y-m-d H:i:s'),
                'approved_date' => date('Y-m-d H:i:s'),
                'reviewed_by' => $this->data['users']->id,
                'approved_by' => 5
            );
            
            $update = $this->Review_cuti_model->update_cuti($data_cuti, $cuti_id);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Berhasil menyetujui cuti'
                );

                // if permitted, do logit
                $perms = "review_cuti_perm";
                $comments = "Berhasil menyetujui cuti dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal update Data saat menerima cuti, hubungi web administrator.');

                // if permitted, do logit
                $perms = "review_cuti_perm";
                $comments = "Gagal update Data saat menerima cuti dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
}