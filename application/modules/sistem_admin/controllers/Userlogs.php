<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Userlogs extends MY_Controller {
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
        
        $this->load->model('Userlogs_model');
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
        $is_permit = $this->aauth->control_no_redirect('userlogs_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        //$this->data['group_list'] = $this->Group_model->get_group_list();
        $this->data['bc_parent'] = "Sys Admin";
        $this->data['bc_child'] = "User Logs";
        $perms = "userlogs_perm";
        $comments = "Halaman User Logs";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('userlogs', $this->data);
    }
    
    function ajax_list_userlogs(){
        $list = $this->Userlogs_model->get_userlogs_list();
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        foreach($list as $log){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $log->perms;
            $row[] = $log->dateactivity;
            $row[] = $log->page;
            $row[] = $log->ipaddr;
            $row[] = $log->nama_user;
            $detail = '<div class="text-center"><button type="button" title="detail" class="btn btn-social-icon btn-info" onclick="detailLog('."'".$log->log_id."'".')"><i class="fa fa-file-text-o"></i></button>';
            $row[] = $detail;
            //add html for action
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Userlogs_model->count_userlogs_all(),
                    "recordsFiltered" => $this->Userlogs_model->count_userlogs_filtered(),
                    "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }
    
    function ajax_get_userlog_detail(){
        $log_id = $this->input->get('log_id',TRUE);
        $old_data = $this->Userlogs_model->get_userlog_by_id($log_id);
        
        if(count($old_data) > 0) {
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'data' => $old_data
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "User log ID tidak ditemukan");
        }
        echo json_encode($res);
    }
}