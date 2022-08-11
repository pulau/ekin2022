<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rumpun_jabatan extends MY_Controller {
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
        
        $this->load->model('Rumpun_model');
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
        $is_permit = $this->aauth->control_no_redirect('rumpun_master_kepegawaian_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "Master Data";
        $this->data['bc_child'] = "Jabatan";
        $perms = "rumpun_master_kepegawaian_perm";
        $comments = "Halaman Master Data Rumpun Jabatan";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('rumpun', $this->data);
    }
    
    public function ajax_list_rumpun(){
        $list = $this->Rumpun_model->get_rumpun_list();
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        foreach($list as $rumpun){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $rumpun->rumpun_nama;
            $row[] = $rumpun->rumpun_nilai;
            //add html for action
            $edit = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" title="Edit" onclick="editRumpun('."'".$rumpun->rumpun_id."'".')"><i class="fa fa-edit"></i></button>';
            $delete = '<button type="button" class="btn btn-social-icon btn-danger" title="Hapus" onclick="hapusRumpun('."'".$rumpun->rumpun_id."'".')"><i class="fa fa-trash-o"></i></button></div>';
            $row[] = $edit."&nbsp;".$delete;
            
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Rumpun_model->count_rumpun_all(),
                    "recordsFiltered" => $this->Rumpun_model->count_rumpun_filtered(),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function do_insert_rumpun(){
        $is_permit = $this->aauth->control_no_redirect('rumpun_master_kepegawaian_perm');
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
        $this->form_validation->set_rules('rumpun_nama', 'Rumpun', 'required|trim');
        $this->form_validation->set_rules('rumpun_nilai', 'Nilai', 'required|trim');
        
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
            $perms = "rumpun_master_kepegawaian_perm";
            $comments = "Gagal input Rumpun dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE)  {
            $rumpun_nama = $this->input->post('rumpun_nama', TRUE);
            $nilai = $this->input->post('rumpun_nilai', TRUE);
            
            $data_rumpun = array(
                'rumpun_nama' => $rumpun_nama,
                'rumpun_nilai' => $nilai
            );
            
            $ins = $this->Rumpun_model->insert_rumpun($data_rumpun);
            //$ins=true;
            if($ins){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Data Rumpun berhasil ditambahkan'
                );

                // if permitted, do logit
                $perms = "rumpun_master_kepegawaian_perm";
                $comments = "Berhasil menambahkan Data Rumpun baru dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menambahkan Data Rumpun, hubungi web administrator.');

                // if permitted, do logit
                $perms = "rumpun_master_kepegawaian_perm";
                $comments = "Gagal menambahkan Data Rumpun dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function ajax_get_rumpun_by_id(){
        $rumpun_id = $this->input->get('rumpun_id',TRUE);
        $old_data = $this->Rumpun_model->get_rumpun_by_id($rumpun_id);
        
        if(count((array)$old_data) > 0) {
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'data' => $old_data
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "Rumpun ID tidak ditemukan");
        }
        echo json_encode($res);
    }

    public function do_update_rumpun(){
        $is_permit = $this->aauth->control_no_redirect('rumpun_master_kepegawaian_perm');
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
        $this->form_validation->set_rules('rumpun_nama', 'Rumpun', 'required|trim');
        $this->form_validation->set_rules('rumpun_nilai', 'Nilai', 'required|trim');
        
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
            $perms = "rumpun_master_kepegawaian_perm";
            $comments = "Gagal update Data Rumpun dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $rumpun_id = $this->input->post('rumpun_id',TRUE);
            $rumpun_nama = $this->input->post('rumpun_nama', TRUE);
            $nilai = $this->input->post('rumpun_nilai', TRUE);
            
            $data_rumpun = array(
                'rumpun_nama' => $rumpun_nama,
                'rumpun_nilai' => $nilai
            );
            
            $update = $this->Rumpun_model->update_rumpun($data_rumpun, $rumpun_id);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Data Rumpun berhasil diubah'
                );

                // if permitted, do logit
                $perms = "rumpun_master_kepegawaian_perm";
                $comments = "Berhasil mengubah Data Rumpun dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal mengubah Data Rumpun, hubungi web administrator.');

                // if permitted, do logit
                $perms = "rumpun_master_kepegawaian_perm";
                $comments = "Gagal mengubah Data Rumpun dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function do_delete_rumpun(){
        $is_permit = $this->aauth->control_no_redirect('rumpun_master_kepegawaian_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $rumpun_id = $this->input->post('rumpun_id', TRUE);
        $check_constrain = $this->Rumpun_model->check_constraint_rumpun($rumpun_id);
        if($check_constrain > 0){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Data sedang digunakan di tabel lain');
            echo json_encode($res);
            exit;
        }
        $delete = $this->Rumpun_model->delete_rumpun($rumpun_id);
        if($delete){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'Rumpun berhasil dihapus'
            );
            // if permitted, do logit
            $perms = "rumpun_master_kepegawaian_perm";
            $comments = "Berhasil menghapus rumpun dengan id = '". $rumpun_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menghapus data, silakan hubungi web administrator.'
            );
            // if permitted, do logit
            $perms = "rumpun_master_kepegawaian_perm";
            $comments = "Gagal menghapus data dengan ID = '". $rumpun_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
}