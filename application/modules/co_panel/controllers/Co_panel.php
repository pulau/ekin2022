<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Co_panel extends MY_Controller {
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
        
        $this->load->model('Cpanel_model');
        $this->load->model('Utama_model');
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
    }
    
    public function index()
    {
        $this->data['modul_list'] = $this->Cpanel_model->get_list_module($this->data['group_arr']);
        // print_r($this->data);
        // if permitted, do logit
        $perms = "Cpanel page";
        $comments = "Halaman Control Panel";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('cpanel', $this->data);
    }
    
}