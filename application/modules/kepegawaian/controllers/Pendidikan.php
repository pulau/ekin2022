<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pendidikan extends MY_Controller {
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
        
        $this->load->model('Pendidikan_model');
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
        $is_permit = $this->aauth->control_no_redirect('pendidikan_master_kepegawaian_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "Master Data";
        $this->data['bc_child'] = "Jabatan";
        $perms = "pendidikan_master_kepegawaian_perm";
        $comments = "Halaman Master Data Pendidikan";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('pendidikan', $this->data);
    }
    
    public function ajax_list_pendidikan(){
        $list = $this->Pendidikan_model->get_pendidikan_list();
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        foreach($list as $pendidikan){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $pendidikan->pendidikan_nama;
            //add html for action
            $edit = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" title="Edit" onclick="editPendidikan('."'".$pendidikan->pendidikan_id."'".')"><i class="fa fa-edit"></i></button>';
            $delete = '<button type="button" class="btn btn-social-icon btn-danger" title="Hapus" onclick="hapusPendidikan('."'".$pendidikan->pendidikan_id."'".')"><i class="fa fa-trash-o"></i></button></div>';
            $row[] = $edit."&nbsp;".$delete;
            
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Pendidikan_model->count_pendidikan_all(),
                    "recordsFiltered" => $this->Pendidikan_model->count_pendidikan_filtered(),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function do_insert_pendidikan(){
        $is_permit = $this->aauth->control_no_redirect('pendidikan_master_kepegawaian_perm');
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
        $this->form_validation->set_rules('pendidikan_nama', 'Pendidikan', 'required|trim');
        
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
            $perms = "pendidikan_master_kepegawaian_perm";
            $comments = "Gagal input Pendidikan dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE)  {
            $pendidikan_nama = $this->input->post('pendidikan_nama', TRUE);
            
            $data_pendidikan = array(
                'pendidikan_nama' => $pendidikan_nama
            );
            
            $ins = $this->Pendidikan_model->insert_pendidikan($data_pendidikan);
            //$ins=true;
            if($ins){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Data Pendidikan berhasil ditambahkan'
                );

                // if permitted, do logit
                $perms = "pendidikan_master_kepegawaian_perm";
                $comments = "Berhasil menambahkan Data Pendidikan baru dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menambahkan Data Pendidikan, hubungi web administrator.');

                // if permitted, do logit
                $perms = "pendidikan_master_kepegawaian_perm";
                $comments = "Gagal menambahkan Data Pendidikan dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function ajax_get_pendidikan_by_id(){
        $pendidikan_id = $this->input->get('pendidikan_id',TRUE);
        $old_data = $this->Pendidikan_model->get_pendidikan_by_id($pendidikan_id);
        
        if(count((array)$old_data) > 0) {
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'data' => $old_data
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "Pendidikan ID tidak ditemukan");
        }
        echo json_encode($res);
    }

    public function do_update_pendidikan(){
        $is_permit = $this->aauth->control_no_redirect('pendidikan_master_kepegawaian_perm');
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
        $this->form_validation->set_rules('pendidikan_nama', 'Pendidikan', 'required|trim');
        
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
            $perms = "pendidikan_master_kepegawaian_perm";
            $comments = "Gagal update Data Pendidikan dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $pendidikan_id = $this->input->post('pendidikan_id',TRUE);
            $pendidikan_nama = $this->input->post('pendidikan_nama', TRUE);
            
            $data_pendidikan = array(
                'pendidikan_nama' => $pendidikan_nama
            );
            
            $update = $this->Pendidikan_model->update_pendidikan($data_pendidikan, $pendidikan_id);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Data Pendidikan berhasil diubah'
                );

                // if permitted, do logit
                $perms = "pendidikan_master_kepegawaian_perm";
                $comments = "Berhasil mengubah Data Pendidikan dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal mengubah Data Pendidikan, hubungi web administrator.');

                // if permitted, do logit
                $perms = "pendidikan_master_kepegawaian_perm";
                $comments = "Gagal mengubah Data Pendidikan dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function do_delete_pendidikan(){
        $is_permit = $this->aauth->control_no_redirect('pendidikan_master_kepegawaian_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $pendidikan_id = $this->input->post('pendidikan_id', TRUE);
        $check_constrain = $this->Pendidikan_model->check_constraint_pendidikan($pendidikan_id);
        if($check_constrain > 0){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Data sedang digunakan di tabel lain');
            echo json_encode($res);
            exit;
        }
        $delete = $this->Pendidikan_model->delete_pendidikan($pendidikan_id);
        if($delete){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'Pendidikan berhasil dihapus'
            );
            // if permitted, do logit
            $perms = "pendidikan_master_kepegawaian_perm";
            $comments = "Berhasil menghapus pendidikan dengan id = '". $pendidikan_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menghapus data, silakan hubungi web administrator.'
            );
            // if permitted, do logit
            $perms = "pendidikan_master_kepegawaian_perm";
            $comments = "Gagal menghapus data dengan ID = '". $pendidikan_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
}