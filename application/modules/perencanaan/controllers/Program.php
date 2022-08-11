<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Program extends MY_Controller {
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
        
        $this->load->model('Program_model');
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
        $is_permit = $this->aauth->control_no_redirect('program_master_perencanaan');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "Master Data";
        $this->data['bc_child'] = "Program";
        $perms = "program_master_perencanaan";
        $comments = "Halaman Master Program";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('program', $this->data);
    }
    
    public function ajax_list_program(){
        $list = $this->Program_model->get_program_list();
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        foreach($list as $program){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $program->program_nama;
            //add html for action
            $edit = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" title="Edit" onclick="editProgram('."'".$program->program_id."'".')"><i class="fa fa-edit"></i></button>';
            $delete = '<button type="button" class="btn btn-social-icon btn-danger" title="Hapus" onclick="hapusProgram('."'".$program->program_id."'".')"><i class="fa fa-trash-o"></i></button></div>';
            $row[] = $edit."&nbsp;".$delete;
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Program_model->count_program_all(),
                    "recordsFiltered" => $this->Program_model->count_program_filtered(),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function do_insert_program(){
        $is_permit = $this->aauth->control_no_redirect('program_master_perencanaan');
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
        $this->form_validation->set_rules('program_nama', 'Program', 'required|trim');
        
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
            $perms = "program_master_perencanaan";
            $comments = "Gagal input Program dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE)  {
            $program_nama = $this->input->post('program_nama', TRUE);
            
            $data_program = array(
                'program_nama' => $program_nama,
                'create_by' => $this->data['pegawai']->id_pegawai,
                'create_date' => date('Y-m-d H:i:s'),
                'update_by' => $this->data['pegawai']->id_pegawai,
                'update_date' => date('Y-m-d H:i:s')
            );
            
            $ins = $this->Program_model->insert_program($data_program);
            //$ins=true;
            if($ins){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Data Program berhasil ditambahkan'
                );

                // if permitted, do logit
                $perms = "program_master_perencanaan";
                $comments = "Berhasil menambahkan Program baru dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menambahkan Program, hubungi web administrator.');

                // if permitted, do logit
                $perms = "program_master_perencanaan";
                $comments = "Gagal menambahkan Program dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function ajax_get_program_by_id(){
        $program_id = $this->input->get('program_id',TRUE);
        $old_data = $this->Program_model->get_program_by_id($program_id);
        
        if(count($old_data) > 0) {
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'data' => $old_data
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "Program ID tidak ditemukan");
        }
        echo json_encode($res);
    }

    public function do_update_program(){
        $is_permit = $this->aauth->control_no_redirect('program_master_perencanaan');
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
        $this->form_validation->set_rules('program_nama', 'Program', 'required|trim');
        
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
            $perms = "program_master_perencanaan";
            $comments = "Gagal update Program dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $program_id = $this->input->post('program_id',TRUE);
            $program_nama = $this->input->post('program_nama', TRUE);
            
            $data_program = array(
                'program_nama' => $program_nama,
                'update_by' => $this->data['pegawai']->id_pegawai,
                'update_date' => date('Y-m-d H:i:s')
            );
            
            $update = $this->Program_model->update_program($data_program, $program_id);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Data Program berhasil diubah'
                );

                // if permitted, do logit
                $perms = "program_master_perencanaan";
                $comments = "Berhasil mengubah Program dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal mengubah Program, hubungi web administrator.');

                // if permitted, do logit
                $perms = "program_master_perencanaan";
                $comments = "Gagal mengubah Program dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function do_delete_program(){
        $is_permit = $this->aauth->control_no_redirect('program_master_perencanaan');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $program_id = $this->input->post('program_id', TRUE);
        $check_constrain = $this->Program_model->check_constraint_program($program_id);
        if($check_constrain > 0){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Data sedang digunakan di tabel lain');
            echo json_encode($res);
            exit;
        }
        $delete = $this->Program_model->delete_program($program_id);
        if($delete){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'Program berhasil dihapus'
            );
            // if permitted, do logit
            $perms = "program_master_perencanaan";
            $comments = "Berhasil menghapus program dengan id = '". $program_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menghapus data, silakan hubungi web administrator.'
            );
            // if permitted, do logit
            $perms = "program_master_perencanaan";
            $comments = "Gagal menghapus data dengan ID = '". $program_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
}