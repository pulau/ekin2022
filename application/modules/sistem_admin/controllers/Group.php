<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends MY_Controller {
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
        
        $this->load->model('Group_model');
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
        $is_permit = $this->aauth->control_no_redirect('group_management_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        //$this->data['group_list'] = $this->Group_model->get_group_list();
        $this->data['bc_parent'] = "Sys Admin";
        $this->data['bc_child'] = "Group Management";
        $perms = "group_management_perm";
        $comments = "Halaman Group Management";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('group', $this->data);
    }
    
    function ajax_list_group(){
        $list = $this->Group_model->get_groups_list();
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        foreach($list as $group){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $group->name;
            $row[] = $group->definition;
            $perms = $this->Group_model->list_perm_by_group($group->id);
            $list_perm = "<ul>";
            foreach($perms as $perm){
                $list_perm .= "<li>".$perm->name."</li>";
            }
            $list_perm .= "</ul>";
            if(count($perms) > 0){
                $row[] = $list_perm;
            }else{
                $row[] ="";
            }
            $edit = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" onclick="editGroup('."'".$group->id."'".')"><i class="fa fa-edit"></i></button>';
            $delete = '<button type="button" class="btn btn-social-icon btn-danger" onclick="deleteGroup('."'".$group->id."'".')"><i class="fa fa-trash-o"></i></button></div>';
            $row[] = $edit."&nbsp;".$delete;
            //add html for action
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Group_model->count_groups_all(),
                    "recordsFiltered" => $this->Group_model->count_groups_filtered(),
                    "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }
    
    function ajax_perm_create()
    {
        $perm = $this->Group_model->get_list_perm();
        if(count($perm) > 0){
            foreach ($perm as $parent){
                $perm_child = $this->Group_model->get_list_perm($parent->id);
                $childs = array();
                if(count($perm_child) > 0){
                    foreach ($perm_child as $child){
                        $perm_child2 = $this->Group_model->get_list_perm($child->id);
                        $childs2 = array();
                        if(count($perm_child2) > 0){
                            foreach ($perm_child2 as $child2){
                                $childs2[] = array('id'=>$child2->id, 'text'=>$child2->name, 'state'=>array('selected'=>false, 'opened'=>false));
                            }
                        }
                        $childs[] = array('id'=>$child->id, 'text'=>$child->name, 'children'=>$childs2, 'state'=>array('selected'=>false, 'opened'=>false));
                    }
                }
                $data_valid[] = array('id'=>$parent->id, 'text'=>$parent->name, 'children'=>$childs, 'state'=>array('selected'=>false, 'opened'=>false));
                
            }
            $success = TRUE;
            $messages = 'Ok';
        }else{
            $success = FALSE;
            $data_valid = '';
            $messages = '<h5 class="text-red">Tidak ada data permission</h5>';
        }
        $result = array(
            'success'=>$success,
            'messages'=>$messages,
            'data_valid'=>$data_valid,
        );
        
        echo json_encode($result);
    }
    
    function do_create_group()
    {
        $is_permit = $this->aauth->control_no_redirect('group_management_page');
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
        $this->form_validation->set_rules('group_name', 'Group Name', 'required');
        $this->form_validation->set_rules('definition', 'Definition', 'required');
        
        if ($this->form_validation->run() == FALSE)  {
            $error = validation_errors();
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $error);

            // if permitted, do logit
            $perms = "group_management_page";
            $comments = "Failed to Create a new group with errors = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else if ($this->form_validation->run() == TRUE)  {
            $group_name = $this->input->post('group_name',TRUE);
            $definition = $this->input->post('definition',TRUE);
            $perms = $this->input->post('tree_res',TRUE);
            $insertgroup = $this->aauth->create_group($group_name, $definition);
            
            if($insertgroup){
                $arr_perm = explode(',', $perms);
                if(count($arr_perm) > 0){
                    $hsl = false;
                    foreach ($arr_perm as $perm_id){
                        $ins_permtogroup = $this->aauth->insert_perm_to_group($insertgroup, $perm_id);
                        if($ins_permtogroup){
                            $hsl = true;
                        }
                    }
                    
                    if($hsl == true){
                        $res = array(
                            'csrfTokenName' => $this->security->get_csrf_token_name(),
                            'csrfHash' => $this->security->get_csrf_hash(),
                            'success' => true,
                            'messages' => 'Group has been saved to database'
                        );

                        // if permitted, do logit
                        $perms = "group_management_page";
                        $comments = "Success to Create a new group with group_id = '". $insertgroup ."'.";
                        $this->aauth->logit($perms, current_url(), $comments);
                    }else{
                        $res = array(
                        'csrfTokenName' => $this->security->get_csrf_token_name(),
                        'csrfHash' => $this->security->get_csrf_hash(),
                        'success' => false,
                        'messages' => 'Failed insert perm to group to database, please contact web administrator.');

                        // if permitted, do logit
                        $perms = "group_management_page";
                        $comments = "Failed to Create perm to group when saving to database with post data = '". $insertgroup ."'.";
                        $this->aauth->logit($perms, current_url(), $comments);
                    }
                }else{
                    $res = array(
                        'csrfTokenName' => $this->security->get_csrf_token_name(),
                        'csrfHash' => $this->security->get_csrf_hash(),
                        'success' => true,
                        'messages' => 'Group has been saved to database'
                    );

                    // if permitted, do logit
                    $perms = "group_management_page";
                    $comments = "Success to Create a new group with group_id = '". $insertgroup ."'.";
                    $this->aauth->logit($perms, current_url(), $comments);
                }
            }else{
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => false,
                    'messages' => 'Failed insert Group to database, please contact web administrator.');

                // if permitted, do logit
                $perms = "user_management_view";
                $comments = "Failed to Create a new user when saving to database with post data = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    function get_groupby_id()
    {
        $group_id = $this->input->get('group_id',TRUE);
        $datagroup = $this->Group_model->get_group_by_id($group_id);
        if(count((array)$datagroup) > 0){
            $res = array(
                'success' => true,
                'data' => $datagroup,
                'messages' => 'Ok'
            );
        }else{
            $res = array(
                'success' => false,
                'messages' => 'Group ID not exist'
            );
        }
        
        echo json_encode($res);
    }
    
    function ajax_perm_update()
    {
        $group_id = $this->input->get('group_id',TRUE);
        $perm = $this->Group_model->get_list_perm();
        if(count($perm) > 0){
            $perm_group = $this->Group_model->get_perm_by_group($group_id);
            $perm_on = array();
            foreach ($perm_group as $perm_list){
                $perm_on[] = $perm_list->perm_id;
            }
            foreach ($perm as $parent){
                if (in_array($parent->id, $perm_on)){
                    $checked = true;
                }else{
                    $checked = false;
                }
                $perm_child = $this->Group_model->get_list_perm($parent->id);
                $childs = array();
                if(count($perm_child) > 0){
                    foreach ($perm_child as $child){
                        if (in_array($child->id, $perm_on)){
                            $checked1 = true;
                        }else{
                            $checked1 = false;
                        }
                        $perm_child2 = $this->Group_model->get_list_perm($child->id);
                        $childs2 = array();
                        if(count($perm_child2) > 0){
                            foreach ($perm_child2 as $child2){
                                if (in_array($child2->id, $perm_on)){
                                    $checked2 = true;
                                }else{
                                    $checked2 = false;
                                }
                                $childs2[] = array('id'=>$child2->id, 'text'=>$child2->name, 'state'=>array('selected'=>$checked2, 'opened'=>false));
                            }
                        }
                        $childs[] = array('id'=>$child->id, 'text'=>$child->name, 'children'=>$childs2, 'state'=>array('selected'=>$checked1, 'opened'=>false));
                    }
                }
                $data_valid[] = array('id'=>$parent->id, 'text'=>$parent->name, 'children'=>$childs, 'state'=>array('selected'=>$checked, 'opened'=>false));
                
            }
            $success = TRUE;
            $messages = 'Ok';
        }else{
            $success = FALSE;
            $data_valid = '';
            $messages = '<h5 class="text-red">Tidak ada data permission</h5>';
        }
        $result = array(
            'success'=>$success,
            'messages'=>$messages,
            'data_valid'=>$data_valid,
        );
        
        echo json_encode($result);
    }
    
    function do_update_group()
    {
        $is_permit = $this->aauth->control_no_redirect('group_management_page');
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
        $this->form_validation->set_rules('upd_group_name', 'Group Name', 'required');
        $this->form_validation->set_rules('upd_definition', 'Definition', 'required');
        
        if ($this->form_validation->run() == FALSE)  {
            $error = validation_errors();
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $error);

            // if permitted, do logit
            $perms = "group_management_page";
            $comments = "Failed to Update group with errors = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else if ($this->form_validation->run() == TRUE)  {
            $group_id = $this->input->post('upd_group_id',TRUE);
            $group_name = $this->input->post('upd_group_name',TRUE);
            $definition = $this->input->post('upd_definition',TRUE);
            $perms = $this->input->post('tree_res',TRUE);
            $updategroup = $this->aauth->update_group($group_id, $group_name, $definition);
            
            if($updategroup){
                $this->Group_model->delete_perm_group($group_id);
                $arr_perm = explode(',', $perms);
                if(count($arr_perm) > 0){
                    $hsl = false;
                    foreach ($arr_perm as $perm_id){
                        $ins_permtogroup = $this->aauth->insert_perm_to_group($group_id, $perm_id);
                        if($ins_permtogroup){
                            $hsl = true;
                        }
                    }
                    
                    if($hsl == true){
                        $res = array(
                            'csrfTokenName' => $this->security->get_csrf_token_name(),
                            'csrfHash' => $this->security->get_csrf_hash(),
                            'success' => true,
                            'messages' => 'Group has been saved to database'
                        );

                        // if permitted, do logit
                        $perms = "group_management_page";
                        $comments = "Success to update group with group_id = '". $group_id ."'.";
                        $this->aauth->logit($perms, current_url(), $comments);
                    }else{
                        $res = array(
                        'csrfTokenName' => $this->security->get_csrf_token_name(),
                        'csrfHash' => $this->security->get_csrf_hash(),
                        'success' => false,
                        'messages' => 'Failed insert perm to group to database, please contact web administrator.');

                        // if permitted, do logit
                        $perms = "group_management_page";
                        $comments = "Failed to Create perm to group when saving to database with post data = '". $group_id ."'.";
                        $this->aauth->logit($perms, current_url(), $comments);
                    }
                }else{
                    $res = array(
                        'csrfTokenName' => $this->security->get_csrf_token_name(),
                        'csrfHash' => $this->security->get_csrf_hash(),
                        'success' => true,
                        'messages' => 'Group has been saved to database'
                    );

                    // if permitted, do logit
                    $perms = "group_management_page";
                    $comments = "Success to update group with group_id = '". $group_id ."'.";
                    $this->aauth->logit($perms, current_url(), $comments);
                }
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Failed update group to database, please contact web administrator.');

                // if permitted, do logit
                $perms = "user_management_view";
                $comments = "Failed to update group when saving to database with post data = '". $group_id ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    function do_delete_group()
    {
        $is_permit = $this->aauth->control_no_redirect('group_management_page');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $group_id = $this->input->post('group_id', TRUE);
        
        $delete = $this->Group_model->delete_group($group_id);
        if($delete){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'Group berhasil dihapus'
            );
            // if permitted, do logit
            $perms = "group_management_page";
            $comments = "Berhasil menghapus group dengan id = '". $group_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menghapus data, silakan hubungi web administrator.'
            );
            // if permitted, do logit
            $perms = "group_management_page";
            $comments = "Gagal menghapus data dengan ID = '". $group_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
}