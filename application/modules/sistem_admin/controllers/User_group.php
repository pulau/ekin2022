<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_group extends MY_Controller {
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
        
        $this->load->model('Usergroup_model');
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
        $is_permit = $this->aauth->control_no_redirect('user_to_group_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        //$this->data['group_list'] = $this->Group_model->get_group_list();
        $this->data['bc_parent'] = "Sys Admin";
        $this->data['bc_child'] = "User to Group Management";
        $perms = "user_to_group_perm";
        $comments = "Halaman User to Group";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('usergroup', $this->data);
    }
    
    function ajax_list_usergroup(){
        $list = $this->Usergroup_model->get_users_list();
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        foreach($list as $user){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $user->nama_lengkap;
            $row[] = $user->username;
            $groups = $this->Usergroup_model->list_group_by_user($user->id);
            $list_group = "<ul>";
            foreach($groups as $group){
                $list_group .= "<li>".$group->name."</li>";
            }
            $list_group .= "</ul>";
            if(count($groups) > 0){
                $row[] = $list_group;
            }else{
                $row[] ="";
            }
            $edit = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" title="Update user group" onclick="editUserGroup('."'".$user->id."'".')"><i class="fa fa-edit"></i></button>';
            $row[] = $edit;
            //add html for action
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Usergroup_model->count_users_all(),
                    "recordsFiltered" => $this->Usergroup_model->count_users_filtered(),
                    "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }
    
    function get_userby_id()
    {
        $user_id = $this->input->get('user_id',TRUE);
        $datauser = $this->Usergroup_model->get_user_by_id($user_id);
        if(count((array)$datauser) > 0){
            $res = array(
                'success' => true,
                'data' => $datauser,
                'messages' => 'Ok'
            );
        }else{
            $res = array(
                'success' => false,
                'messages' => 'User ID not exist'
            );
        }
        
        echo json_encode($res);
    }
    
    function ajax_group_update()
    {
        $user_id = $this->input->get('user_id',TRUE);
        $group = $this->Usergroup_model->get_list_group();
        if(count($group) > 0){
            $user_group = $this->Usergroup_model->get_group_by_user($user_id);
            $group_on = array();
            foreach ($user_group as $group_list){
                $group_on[] = $group_list->group_id;
            }
            foreach ($group as $groulist){
                if (in_array($groulist->id, $group_on)){
                    $checked = true;
                }else{
                    $checked = false;
                }
                $data_valid[] = array('id'=>$groulist->id, 'text'=>$groulist->name, 'state'=>array('selected'=>$checked, 'opened'=>false));
            }
            $success = TRUE;
            $messages = 'Ok';
        }else{
            $success = FALSE;
            $data_valid = '';
            $messages = '<h5 class="text-red">Tidak ada data group</h5>';
        }
        $result = array(
            'success'=>$success,
            'messages'=>$messages,
            'data_valid'=>$data_valid,
        );
        
        echo json_encode($result);
    }
    
    function do_update_usergroup()
    {
        $is_permit = $this->aauth->control_no_redirect('user_to_group_perm');
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
        $this->form_validation->set_rules('username', 'Nama User', 'required');
        
        if ($this->form_validation->run() == FALSE)  {
            $error = validation_errors();
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $error);

            // if permitted, do logit
            $perms = "user_to_group_perm";
            $comments = "Failed to Update user group with errors = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else if ($this->form_validation->run() == TRUE)  {
            $user_id = $this->input->post('user_id',TRUE);
            $groups = $this->input->post('tree_res',TRUE);
            
            $this->Usergroup_model->delete_user_group($user_id);
            $arr_group = explode(',', $groups);
            if(count($arr_group) > 0){
                $hsl = false;
                foreach ($arr_group as $group_id){
                    $ins_usertogroup = $this->aauth->insert_user_to_group($group_id, $user_id);
                    if($ins_usertogroup){
                        $hsl = true;
                    }
                }

                if($hsl == true){
                    $res = array(
                        'csrfTokenName' => $this->security->get_csrf_token_name(),
                        'csrfHash' => $this->security->get_csrf_hash(),
                        'success' => true,
                        'messages' => 'User group berhasil diperbaharui'
                    );

                    // if permitted, do logit
                    $perms = "user_to_group_perm";
                    $comments = "Success to update user group with data = ". json_encode($_REQUEST) ."'.";
                    $this->aauth->logit($perms, current_url(), $comments);
                }else{
                    $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => false,
                    'messages' => 'Failed insert user to group to database, please contact web administrator.');

                    // if permitted, do logit
                    $perms = "user_to_group_perm";
                    $comments = "Failed to Create perm to group when saving to database with post data = '". json_encode($_REQUEST) ."'.";
                    $this->aauth->logit($perms, current_url(), $comments);
                }
            }else{
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Tidak ada group yang dipilih'
                );

                // if permitted, do logit
                $perms = "user_to_group_perm";
                $comments = "Tidak ada group yang dipilih saat update user to group";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
}