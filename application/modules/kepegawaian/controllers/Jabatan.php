<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jabatan extends MY_Controller {
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
        
        $this->load->model('Jabatan_model');
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
        $is_permit = $this->aauth->control_no_redirect('jabatan_master_kepegawaian_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "Master Data";
        $this->data['bc_child'] = "Jabatan";
        $perms = "jabatan_master_kepegawaian_perm";
        $comments = "Halaman Master Data Jabatan";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('jabatan', $this->data);
    }
    
    public function ajax_list_jabatan(){
        $list = $this->Jabatan_model->get_jabatan_list();
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        foreach($list as $jabatan){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $jabatan->nama_jabatan;
            //add html for action
            $edit = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" title="Edit" onclick="editJabatan('."'".$jabatan->id_jabatan."'".')"><i class="fa fa-edit"></i></button>';
            $delete = '<button type="button" class="btn btn-social-icon btn-danger" title="Hapus" onclick="hapusJabatan('."'".$jabatan->id_jabatan."'".')"><i class="fa fa-trash-o"></i></button></div>';
            $row[] = $edit."&nbsp;".$delete;
            
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Jabatan_model->count_jabatan_all(),
                    "recordsFiltered" => $this->Jabatan_model->count_jabatan_filtered(),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function do_insert_jabatan(){
        $is_permit = $this->aauth->control_no_redirect('jabatan_master_kepegawaian_perm');
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
        $this->form_validation->set_rules('jabatan_nama', 'Jabatan', 'required|trim');
        
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
            $perms = "jabatan_master_kepegawaian_perm";
            $comments = "Gagal input Jabatan dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE)  {
            $nama_jabatan = $this->input->post('jabatan_nama', TRUE);
            
            $data_jabatan = array(
                'nama_jabatan' => $nama_jabatan
            );
            
            $ins = $this->Jabatan_model->insert_jabatan($data_jabatan);
            //$ins=true;
            if($ins){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Data Jabatan berhasil ditambahkan'
                );

                // if permitted, do logit
                $perms = "jabatan_master_kepegawaian_perm";
                $comments = "Berhasil menambahkan Data Jabatan baru dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menambahkan Data Jabatan, hubungi web administrator.');

                // if permitted, do logit
                $perms = "jabatan_master_kepegawaian_perm";
                $comments = "Gagal menambahkan Data Jabatan dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function ajax_get_jabatan_by_id(){
        $id_jabatan = $this->input->get('id_jabatan',TRUE);
        $old_data = $this->Jabatan_model->get_jabatan_by_id($id_jabatan);
        
        if(count((array)$old_data) > 0) {
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'data' => $old_data
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "Jabatan ID tidak ditemukan");
        }
        echo json_encode($res);
    }

    public function do_update_jabatan(){
        $is_permit = $this->aauth->control_no_redirect('jabatan_master_kepegawaian_perm');
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
        $this->form_validation->set_rules('jabatan_nama', 'Jabatan', 'required|trim');
        
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
            $perms = "jabatan_master_kepegawaian_perm";
            $comments = "Gagal update Data Jabatan dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $id_jabatan = $this->input->post('jabatan_id',TRUE);
            $nama_jabatan = $this->input->post('jabatan_nama', TRUE);
            
            $data_jabatan = array(
                'nama_jabatan' => $nama_jabatan
            );
            
            $update = $this->Jabatan_model->update_jabatan($data_jabatan, $id_jabatan);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Data Jabatan berhasil diubah'
                );

                // if permitted, do logit
                $perms = "jabatan_master_kepegawaian_perm";
                $comments = "Berhasil mengubah Data Jabatan dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal mengubah Data Jabatan, hubungi web administrator.');

                // if permitted, do logit
                $perms = "jabatan_master_kepegawaian_perm";
                $comments = "Gagal mengubah Data Jabatan dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function do_delete_jabatan(){
        $is_permit = $this->aauth->control_no_redirect('jabatan_master_kepegawaian_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $jabatan_id = $this->input->post('jabatan_id', TRUE);
        $check_constrain = $this->Jabatan_model->check_constraint_jabatan($jabatan_id);
        if($check_constrain > 0){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Data sedang digunakan di tabel lain');
            echo json_encode($res);
            exit;
        }
        $delete = $this->Jabatan_model->delete_jabatan($jabatan_id);
        if($delete){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'Jabatan berhasil dihapus'
            );
            // if permitted, do logit
            $perms = "jabatan_master_kepegawaian_perm";
            $comments = "Berhasil menghapus jabatan dengan id = '". $jabatan_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menghapus data, silakan hubungi web administrator.'
            );
            // if permitted, do logit
            $perms = "jabatan_master_kepegawaian_perm";
            $comments = "Gagal menghapus data dengan ID = '". $jabatan_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
}