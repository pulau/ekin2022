<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permission extends MY_Controller {
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
        
        $this->load->model('Permission_model');
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
        $is_permit = $this->aauth->control_no_redirect('permission_management_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        //$this->data['group_list'] = $this->Group_model->get_group_list();
        $this->data['bc_parent'] = "Sys Admin";
        $this->data['bc_child'] = "Permission Management";
        $perms = "permission_management_perm";
        $comments = "Halaman Permission Management";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('permission', $this->data);
    }
    
    function ajax_list_perm(){
        $list = $this->Permission_model->get_perms_list();
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        foreach($list as $perm){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $perm->name;
            $row[] = "<i class='".$perm->icon."'></i> &nbsp;".$perm->definition;
            $row[] = $perm->url;
            $row[] = !empty($perm->parent) ? $perm->parent : "-";
            $row[] = $perm->module_name;
            $row[] = $perm->urutan_menu;
            $edit = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" onclick="editPerm('."'".$perm->id."'".')"><i class="fa fa-edit"></i></button>';
            $delete = '<button type="button" class="btn btn-social-icon btn-danger" onclick="deletePerm('."'".$perm->id."'".')"><i class="fa fa-trash-o"></i></button></div>';
            $row[] = $edit."&nbsp;".$delete;
            //add html for action
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Permission_model->count_perms_all(),
                    "recordsFiltered" => $this->Permission_model->count_perms_filtered(),
                    "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }
    
    public function do_insert_perm(){
        $is_permit = $this->aauth->control_no_redirect('permission_management_perm');
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
        $this->form_validation->set_rules('perm_name', 'Nama Permission', 'required|trim');
        $this->form_validation->set_rules('icon', 'Icon', 'required|trim');
        $this->form_validation->set_rules('definition', 'Definition', 'required|trim');
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
            $perms = "permission_management_perm";
            $comments = "Gagal input Permission dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE)  {
            $perm = $this->input->post('perm_name', TRUE);
            $icon = $this->input->post('icon', TRUE);
            $definition = $this->input->post('definition', TRUE);
            $url = $this->input->post('url', TRUE);
            $parent_id = $this->input->post('parent', TRUE);
            $module_id = $this->input->post('module', TRUE);
            $urutan = $this->input->post('urutan', TRUE);
            
            $data_perm = array(
                'name' => $perm,
                'definition' => $definition,
                'parent_id' => $parent_id,
                'url' => $url,
                'icon' => $icon,
                'module_id' => $module_id,
                'urutan_menu' => $urutan
            );
            
            $ins = $this->Permission_model->insert_perm($data_perm);
            //$ins=true;
            if($ins){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Data Permission berhasil ditambahkan'
                );

                // if permitted, do logit
                $perms = "permission_management_perm";
                $comments = "Berhasil menambahkan Permission baru dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menambahkan Permission, hubungi web administrator.');

                // if permitted, do logit
                $perms = "permission_management_perm";
                $comments = "Gagal menambahkan Permission dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function ajax_get_permission_by_id(){
        $id_perm = $this->input->get('id_perm',TRUE);
        $old_data = $this->Permission_model->get_perm_by_id($id_perm);
        
        if(count((array)$old_data) > 0) {
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'data' => $old_data
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "Permission ID tidak ditemukan");
        }
        echo json_encode($res);
    }

    public function do_update_perm(){
        $is_permit = $this->aauth->control_no_redirect('permission_management_perm');
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
        $this->form_validation->set_rules('upd_perm_name', 'Nama Permission', 'required|trim');
        $this->form_validation->set_rules('upd_icon', 'Icon', 'required|trim');
        $this->form_validation->set_rules('upd_definition', 'Definition', 'required|trim');
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
            $perms = "permission_management_perm";
            $comments = "Gagal update Permission dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $id_perm = $this->input->post('upd_id_perm',TRUE);
            $perm = $this->input->post('upd_perm_name', TRUE);
            $icon = $this->input->post('upd_icon', TRUE);
            $definition = $this->input->post('upd_definition', TRUE);
            $url = $this->input->post('upd_url', TRUE);
            $parent = $this->input->post('upd_parent', TRUE);
            $module = $this->input->post('upd_module', TRUE);
            $urutan = $this->input->post('upd_urutan', TRUE);
            
            $data_perm = array(
                'name' => $perm,
                'definition' => $definition,
                'parent_id' => $parent,
                'url' => $url,
                'icon' => $icon,
                'module_id' => $module,
                'urutan_menu' => $urutan
            );
            
            $update = $this->Permission_model->update_perm($data_perm, $id_perm);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Permission berhasil diubah'
                );

                // if permitted, do logit
                $perms = "permission_management_perm";
                $comments = "Berhasil mengubah Permission dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal mengubah Permission, hubungi web administrator.');

                // if permitted, do logit
                $perms = "permission_management_perm";
                $comments = "Gagal mengubah Permission dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function do_delete_perm(){
        $is_permit = $this->aauth->control_no_redirect('permission_management_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $perm_id = $this->input->post('perm_id', TRUE);
        $check_constrain = $this->Permission_model->check_constraint_perm($perm_id);
        if($check_constrain > 0){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Data berelasi dengan tabel perm to group');
            echo json_encode($res);
            exit;
        }
        $delete = $this->Permission_model->delete_perm($perm_id);
        if($delete){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'Permission berhasil dihapus'
            );
            // if permitted, do logit
            $perms = "permission_management_perm";
            $comments = "Berhasil menghapus permission dengan id = '". $perm_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menghapus data, silakan hubungi web administrator.'
            );
            // if permitted, do logit
            $perms = "permission_management_perm";
            $comments = "Gagal menghapus data permission dengan ID = '". $perm_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
}