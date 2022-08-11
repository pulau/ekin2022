<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kepegawaian extends MY_Controller {
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
        
        $this->load->model('Kepegawaian_model');
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

        /*
        fot dashboard kepegawaian
         */
        $this->load->model('pegawai_model');
        $this->data['total_pegawai']      =   $this->pegawai_model->count_pegawai_all();
    }
    
    function index()
    {
        $is_permit = $this->aauth->control_no_redirect('dashboard_kepegawaian');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "Kepegawaian";
        $this->data['bc_child'] = "Dashboard";
        $perms = "dashboard_kepegawaian";
        $comments = "Halaman Dashboard Kepegawaian";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('kepegawaian', $this->data);
    }
}