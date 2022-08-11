<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Indikator_program extends MY_Controller {
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
        
        $this->load->model('Inpro_model');
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
        $is_permit = $this->aauth->control_no_redirect('indikator_program_master_perencanaan');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "Master Data";
        $this->data['bc_child'] = "Indikator Program";
        $perms = "indikator_program_master_perencanaan";
        $comments = "Halaman Master Program";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('inpro', $this->data);
    }
    
    public function ajax_list_inpro(){
        $list = $this->Inpro_model->get_inpro_list();
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        foreach($list as $inpro){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $inpro->indikator_program;
            $row[] = $inpro->program_nama;
            //add html for action
            $edit = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" title="Edit" onclick="editInpro('."'".$inpro->indikator_id."'".')"><i class="fa fa-edit"></i></button>';
            $delete = '<button type="button" class="btn btn-social-icon btn-danger" title="Hapus" onclick="hapusInpro('."'".$inpro->indikator_id."'".')"><i class="fa fa-trash-o"></i></button></div>';
            $row[] = $edit."&nbsp;".$delete;
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Inpro_model->count_inpro_all(),
                    "recordsFiltered" => $this->Inpro_model->count_inpro_filtered(),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function do_insert_inpro(){
        $is_permit = $this->aauth->control_no_redirect('indikator_program_master_perencanaan');
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
        $this->form_validation->set_rules('program', 'Program', 'required|trim');
        $this->form_validation->set_rules('indikator_program', 'Indikator Program', 'required|trim');
        
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
            $perms = "indikator_program_master_perencanaan";
            $comments = "Gagal input Indikator Program dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE)  {
            $indikator_program = $this->input->post('indikator_program', TRUE);
            $program = $this->input->post('program', TRUE);
            
            $data_inpro = array(
                'indikator_program' => $indikator_program,
                'program_id' => $program,
                'create_by' => $this->data['pegawai']->id_pegawai,
                'create_date' => date('Y-m-d H:i:s'),
                'update_by' => $this->data['pegawai']->id_pegawai,
                'update_date' => date('Y-m-d H:i:s')
            );
            
            $ins = $this->Inpro_model->insert_inpro($data_inpro);
            //$ins=true;
            if($ins){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Data Indikator Program berhasil ditambahkan'
                );

                // if permitted, do logit
                $perms = "indikator_program_master_perencanaan";
                $comments = "Berhasil menambahkan Indikator Program baru dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menambahkan Indikator Program, hubungi web administrator.');

                // if permitted, do logit
                $perms = "indikator_program_master_perencanaan";
                $comments = "Gagal menambahkan Indikator Program dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function ajax_get_inpro_by_id(){
        $inpro_id = $this->input->get('inpro_id',TRUE);
        $old_data = $this->Inpro_model->get_inpro_by_id($inpro_id);
        
        if(count($old_data) > 0) {
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'data' => $old_data
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "Indikator Program ID tidak ditemukan");
        }
        echo json_encode($res);
    }

    public function do_update_inpro(){
        $is_permit = $this->aauth->control_no_redirect('indikator_program_master_perencanaan');
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
        $this->form_validation->set_rules('program', 'Program', 'required|trim');
        $this->form_validation->set_rules('indikator_program', 'Indikator Program', 'required|trim');
        
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
            $perms = "indikator_program_master_perencanaan";
            $comments = "Gagal update Indikator Program dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $inpro_id = $this->input->post('indikator_program_id',TRUE);
            $indikator_program = $this->input->post('indikator_program', TRUE);
            $program = $this->input->post('program', TRUE);
            
            $data_inpro = array(
                'indikator_program' => $indikator_program,
                'program_id' => $program,
                'update_by' => $this->data['pegawai']->id_pegawai,
                'update_date' => date('Y-m-d H:i:s')
            );
            
            $update = $this->Inpro_model->update_inpro($data_inpro, $inpro_id);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Indikator Program berhasil diubah'
                );

                // if permitted, do logit
                $perms = "indikator_program_master_perencanaan";
                $comments = "Berhasil mengubah Indikator Program dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal mengubah Indikator Program, hubungi web administrator.');

                // if permitted, do logit
                $perms = "indikator_program_master_perencanaan";
                $comments = "Gagal mengubah Indikator Program dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function do_delete_inpro(){
        $is_permit = $this->aauth->control_no_redirect('indikator_program_master_perencanaan');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $inpro_id = $this->input->post('inpro_id', TRUE);
        $check_constrain = $this->Inpro_model->check_constraint_inpro($inpro_id);
        if($check_constrain > 0){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Data sedang digunakan di tabel lain');
            echo json_encode($res);
            exit;
        }
        $delete = $this->Inpro_model->delete_inpro($inpro_id);
        if($delete){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'Indikator Program berhasil dihapus'
            );
            // if permitted, do logit
            $perms = "indikator_program_master_perencanaan";
            $comments = "Berhasil menghapus indikator program dengan id = '". $inpro_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menghapus data, silakan hubungi web administrator.'
            );
            // if permitted, do logit
            $perms = "indikator_program_master_perencanaan";
            $comments = "Gagal menghapus indikator program dengan ID = '". $inpro_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
}