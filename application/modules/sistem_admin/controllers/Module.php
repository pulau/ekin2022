<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Module extends MY_Controller {
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
        
        $this->load->model('Module_model');
        $this->load->model('Utama_model');
        $this->data['modul']    = $this->aauth->get_module_id($this->uri->segment(1));
        $this->data['users']    = $this->aauth->get_user();
        $this->data['groups']   = $this->aauth->get_user_groups();
        $this->data['pegawai']  = $this->Utama_model->get_pegawai_by_nip($this->data['users']->nip);
        $bagian                 = !empty($this->data['pegawai']) ? $this->data['pegawai']->bagian : "";
        $this->data['bagian']   = $this->Utama_model->get_bagian_by_id($bagian);
        $groups = "";
        foreach ($this->data['groups'] as $key => $val){
            $groups .= $val->group_id.","; 
        }
        $this->data['group_arr'] = substr_replace($groups, "", -1);
        $this->data['menu_list'] = $this->Utama_model->get_list_menu($this->data['group_arr'], $this->data['modul']);
    }
    
    function index()
    {
        $is_permit = $this->aauth->control_no_redirect('module_management_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        //$this->data['group_list'] = $this->Group_model->get_group_list();
        $this->data['bc_parent'] = "Sys Admin";
        $this->data['bc_child'] = "Module Management";
        $perms = "module_management_perm";
        $comments = "Halaman Module Management";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('module', $this->data);
    }
    
    function ajax_list_module(){
        $list = $this->Module_model->get_modules_list();
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        foreach($list as $module){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $module->name;
            $row[] = "<i class='".$module->modul_icon."'></i> &nbsp;".$module->label;
            $row[] = $module->modul_url;
            $row[] = $module->modul_kategori;
            $row[] = $module->modul_urutan;
            $edit = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" onclick="editModule('."'".$module->id."'".')"><i class="fa fa-edit"></i></button>';
            $delete = '<button type="button" class="btn btn-social-icon btn-danger" onclick="deleteModule('."'".$module->id."'".')"><i class="fa fa-trash-o"></i></button></div>';
            $row[] = $edit."&nbsp;".$delete;
            //add html for action
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Module_model->count_modules_all(),
                    "recordsFiltered" => $this->Module_model->count_modules_filtered(),
                    "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }
    
    public function do_insert_module(){
        $is_permit = $this->aauth->control_no_redirect('module_management_perm');
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
        $this->form_validation->set_rules('module_name', 'Nama Module', 'required|trim');
        $this->form_validation->set_rules('icon', 'Icon', 'required|trim');
        $this->form_validation->set_rules('label', 'Label', 'required|trim');
        $this->form_validation->set_rules('url', 'URL', 'required|trim');
        
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
            $perms = "module_management_perm";
            $comments = "Gagal input Module dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE)  {
            $module = $this->input->post('module_name', TRUE);
            $icon = $this->input->post('icon', TRUE);
            $label = $this->input->post('label', TRUE);
            $url = $this->input->post('url', TRUE);
            $kategori = $this->input->post('kategori', TRUE);
            $urutan = $this->input->post('urutan', TRUE);
            
            $data_module = array(
                'name' => $module,
                'label' => $label,
                'modul_icon' => $icon,
                'modul_url' => $url,
                'modul_urutan' => $urutan,
                'modul_kategori' => $kategori
            );
            
            $ins = $this->Module_model->insert_module($data_module);
            //$ins=true;
            if($ins){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Data Module berhasil ditambahkan'
                );

                // if permitted, do logit
                $perms = "module_management_perm";
                $comments = "Berhasil menambahkan Module baru dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menambahkan Module, hubungi web administrator.');

                // if permitted, do logit
                $perms = "module_management_perm";
                $comments = "Gagal menambahkan Module dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function ajax_get_module_by_id(){
        $id_module = $this->input->get('id_module',TRUE);
        $old_data = $this->Module_model->get_module_by_id($id_module);
        
        if(count((array)$old_data) > 0) {
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'data' => $old_data
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "Module ID tidak ditemukan");
        }
        echo json_encode($res);
    }

    public function do_update_module(){
        $is_permit = $this->aauth->control_no_redirect('module_management_perm');
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
        $this->form_validation->set_rules('upd_module_name', 'Nama Module', 'required|trim');
        $this->form_validation->set_rules('upd_icon', 'Icon', 'required|trim');
        $this->form_validation->set_rules('upd_label', 'Label', 'required|trim');
        $this->form_validation->set_rules('upd_url', 'URL', 'required|trim');
        
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
            $perms = "module_management_perm";
            $comments = "Gagal update Module dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $id_module = $this->input->post('upd_id_module',TRUE);
            $module = $this->input->post('upd_module_name', TRUE);
            $icon = $this->input->post('upd_icon', TRUE);
            $label = $this->input->post('upd_label', TRUE);
            $url = $this->input->post('upd_url', TRUE);
            $kategori = $this->input->post('upd_kategori', TRUE);
            $urutan = $this->input->post('upd_urutan', TRUE);
            
            $data_module = array(
                'name' => $module,
                'label' => $label,
                'modul_icon' => $icon,
                'modul_url' => $url,
                'modul_urutan' => $urutan,
                'modul_kategori' => $kategori
            );
            
            $update = $this->Module_model->update_module($data_module, $id_module);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Module berhasil diubah'
                );

                // if permitted, do logit
                $perms = "module_management_perm";
                $comments = "Berhasil mengubah Module dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal mengubah Module, hubungi web administrator.');

                // if permitted, do logit
                $perms = "module_management_perm";
                $comments = "Gagal mengubah Module dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function do_delete_module(){
        $is_permit = $this->aauth->control_no_redirect('module_management_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $module_id = $this->input->post('module_id', TRUE);
        $check_constrain = $this->Module_model->check_constraint_module($module_id);
        if($check_constrain > 0){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Data sedang digunakan di tabel lain');
            echo json_encode($res);
            exit;
        }
        $delete = $this->Module_model->delete_module($module_id);
        if($delete){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'Module berhasil dihapus'
            );
            // if permitted, do logit
            $perms = "module_management_perm";
            $comments = "Berhasil menghapus module dengan id = '". $module_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menghapus data, silakan hubungi web administrator.'
            );
            // if permitted, do logit
            $perms = "module_management_perm";
            $comments = "Gagal menghapus data dengan ID = '". $module_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
}