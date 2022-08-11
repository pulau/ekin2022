<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller {
    //public $aauth;
    public function __constract(){
        parent::__construct();
        $this->load->library("Aauth");
        // check login session
        // if not logged in set message_type & message flashdata
        // redirect to login page
        if ($this->aauth->is_loggedin()) {
            redirect('co_panel');
        }
        
    }
    
    public function index(){
        $this->load->library("Aauth");
        if ($this->aauth->is_loggedin()) {
            redirect('co_panel');
        }
        $data = array('title'=>'Login User');
        $this->load->view('login', $data);
    }
    
    public function do_login(){
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'required|max_length[30]');
        $this->form_validation->set_rules('password', 'Password', 'required|max_length[30]');
        
        if ($this->form_validation->run() == FALSE)  {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('messages', validation_errors());
            redirect('login');
        } else if ($this->form_validation->run() == TRUE){
            //echo $this->session->userdata('db_profile');
            $this->load->library('Aauth');
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $remember = $this->input->post('remember');
         
            $login = $this->aauth->login($username, $password, $remember);
            
            if ($login) {
                $perms = "member_login";
                $page = current_url();
                $comments = "Login Success with username : ". $username;
                $this->aauth->logit($perms,$page, $comments);
                redirect('co_panel');
            } else {
                //$this->aauth->print_errors();
                $perms = "member_login";
                $page = current_url();
                $comments = "Login attempt failed with username : ". $username;
                $this->aauth->logit($perms,$page, $comments);

//                $this->session->unset_userdata('db_profile');
                $this->session->set_flashdata('message_type', 'error');
                $this->session->set_flashdata('messages', $this->aauth->get_errors_array());
                redirect('login');
            }
        }
    }
    
    public function do_logout(){
//        $perms = "member_logout";
//        $page = current_url();
//        $comments = "Logout attempt success";
//        $this->aauth->logit($perms, $page, $comments);
        $this->load->library("Aauth");
        $this->aauth->logout();
        redirect('login');
    }
    
}