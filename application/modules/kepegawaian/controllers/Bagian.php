<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bagian extends MY_Controller {
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
        
        $this->load->model('Bagian_model');
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
        $is_permit = $this->aauth->control_no_redirect('bagian_master_kepegawaian_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "Master Data";
        $this->data['bc_child'] = "Bagian";
        $perms = "bagian_master_kepegawaian_perm";
        $comments = "Halaman Master Data Bagian";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('bagian', $this->data);
    }
    
    public function ajax_list_bagian(){
        $list = $this->Bagian_model->get_bagian_list();
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        foreach($list as $bagian){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $bagian->bagian_nama;
            $row[] = $bagian->koordinator;
            $row[] = $bagian->pj_cuti_nama;
            //add html for action
            $edit = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" title="Edit" onclick="editBagian('."'".$bagian->bagian_id."'".')"><i class="fa fa-edit"></i></button>';
            $delete = '<button type="button" class="btn btn-social-icon btn-danger" title="Hapus" onclick="hapusBagian('."'".$bagian->bagian_id."'".')"><i class="fa fa-trash-o"></i></button></div>';
            $row[] = $edit."&nbsp;".$delete;
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Bagian_model->count_bagian_all(),
                    "recordsFiltered" => $this->Bagian_model->count_bagian_filtered(),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function do_insert_bagian(){
        $is_permit = $this->aauth->control_no_redirect('bagian_master_kepegawaian_perm');
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
        $this->form_validation->set_rules('bagian_nama', 'Nama Bagian', 'required|trim');
        
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
            $perms = "bagian_master_kepegawaian_perm";
            $comments = "Gagal input Bagian dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE)  {
            $bagian_nama = $this->input->post('bagian_nama', TRUE);
            $kordinator = $this->input->post('koordinator', TRUE);
            $pj_cuti = $this->input->post('pj_cuti', TRUE);
            
            $data_bagian = array(
                'bagian_nama' => $bagian_nama,
                'kordinator_id' => $kordinator,
                'pj_cuti' => $pj_cuti
            );
            
            $ins = $this->Bagian_model->insert_bagian($data_bagian);
            //$ins=true;
            if($ins){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Data Bagian berhasil ditambahkan'
                );

                // if permitted, do logit
                $perms = "bagian_master_kepegawaian_perm";
                $comments = "Berhasil menambahkan Data Bagian baru dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menambahkan Data Bagian, hubungi web administrator.');

                // if permitted, do logit
                $perms = "bagian_master_kepegawaian_perm";
                $comments = "Gagal menambahkan Data Bagian dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function ajax_get_bagian_by_id(){
        $bagian_id = $this->input->get('bagian_id',TRUE);
        $old_data = $this->Bagian_model->get_bagian_by_id($bagian_id);
        
        if(count((array)$old_data) > 0) {
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'data' => $old_data
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "Bagian ID tidak ditemukan");
        }
        echo json_encode($res);
    }

    public function do_update_bagian(){
        $is_permit = $this->aauth->control_no_redirect('bagian_master_kepegawaian_perm');
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
        $this->form_validation->set_rules('bagian_nama', 'Nama Bagian', 'required|trim');
        
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
            $perms = "bagian_master_kepegawaian_perm";
            $comments = "Gagal update Data Bagian dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $id_bagian = $this->input->post('bagian_id',TRUE);
            $bagian_nama = $this->input->post('bagian_nama', TRUE);
            $kordinator = $this->input->post('koordinator', TRUE);
            $pj_cuti = $this->input->post('pj_cuti', TRUE);
            
            $data_bagian = array(
                'bagian_nama' => $bagian_nama,
                'kordinator_id' => $kordinator,
                'pj_cuti' => $pj_cuti
            );
            
            $update = $this->Bagian_model->update_bagian($data_bagian, $id_bagian);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Data Bagian berhasil diubah'
                );

                // if permitted, do logit
                $perms = "bagian_master_kepegawaian_perm";
                $comments = "Berhasil mengubah Data Bagian dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal mengubah Data Bagian, hubungi web administrator.');

                // if permitted, do logit
                $perms = "bagian_master_kepegawaian_perm";
                $comments = "Gagal mengubah Data Bagian dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function do_delete_bagian(){
        $is_permit = $this->aauth->control_no_redirect('bagian_master_kepegawaian_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $bagian_id = $this->input->post('bagian_id', TRUE);
        $check_constrain = $this->Bagian_model->check_constraint_bagian($bagian_id);
        if($check_constrain > 0){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Data sedang digunakan di tabel lain');
            echo json_encode($res);
            exit;
        }
        $delete = $this->Bagian_model->delete_bagian($bagian_id);
        if($delete){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'Bagian berhasil dihapus'
            );
            // if permitted, do logit
            $perms = "bagian_master_kepegawaian_perm";
            $comments = "Berhasil menghapus bagian dengan id = '". $bagian_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menghapus data, silakan hubungi web administrator.'
            );
            // if permitted, do logit
            $perms = "bagian_master_kepegawaian_perm";
            $comments = "Gagal menghapus data dengan ID = '". $bagian_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
}