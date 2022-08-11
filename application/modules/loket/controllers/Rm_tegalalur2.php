<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rm_tegalalur2 extends MY_Controller {
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
        
        $this->load->model('Rm_model');
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
        $is_permit = $this->aauth->control_no_redirect('rm_tegalalur2_loket_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "Loket";
        $this->data['bc_child'] = "RM PKL Tegal Alur 2";
        $perms = "rm_tegalalur2_loket_perm";
        $comments = "Halaman Loket RM PKL Tegal Alur 2";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('rm_tegalalur2', $this->data);
    }
    
    public function ajax_list_mr(){
        $list = $this->Rm_model->get_mr_list(PKL_TEGALALUR2);
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        foreach($list as $mr){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = date('d-M-Y',strtotime($mr->tgl_daftar));
            $row[] = $mr->no_rm;
            $row[] = $mr->nama_pasien;
            $row[] = date('d-M-Y',strtotime($mr->tgl_lahir));
            //add html for action
            $row[] = '<button type="button" class="btn btn-social-icon btn-info" title="Edit" onclick="editPasien('."'".$mr->rm_id."'".')"><i class="fa fa-edit"></i></button>';
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Rm_model->count_mr_all(PKL_TEGALALUR2),
                    "recordsFiltered" => $this->Rm_model->count_mr_filtered(PKL_TEGALALUR2),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function do_insert_mr(){
        $is_permit = $this->aauth->control_no_redirect('rm_tegalalur2_loket_perm');
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
        $this->form_validation->set_rules('nama_pasien', 'Nama Pasien', 'required|trim');
        $this->form_validation->set_rules('tgl_lahir', 'Tanggal Lahir', 'required|trim');
        
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
            $perms = "rm_tegalalur2_loket_perm";
            $comments = "Gagal input Rekam Medis dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE)  {
            $no_mr = $this->Rm_model->generate_mr_number(PKL_TEGALALUR2);
            
            $data_mr = array(
                'tgl_daftar' => date('Y-m-d H:i:s'),
                'no_rm' => $no_mr,
                'nama_pasien' => $this->input->post('nama_pasien',TRUE),
                'tgl_lahir' => date('Y-m-d',strtotime($this->input->post('tgl_lahir',TRUE))),
                'create_by' => $this->data['users']->id,
                'ukpd_id' => PKL_TEGALALUR2
            );
            
            $ins = $this->Rm_model->insert_mr($data_mr);
            //$ins=true;
            if($ins){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'nama_pasien' => $this->input->post('nama_pasien',TRUE),
                    'no_rm' => $no_mr,
                    'messages' => 'Rekam Medis berhasil ditambahkan'
                );

                // if permitted, do logit
                $perms = "rm_tegalalur2_loket_perm";
                $comments = "Berhasil menambahkan Rekam Medis baru dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menambahkan Rekam Medis, hubungi web administrator.');

                // if permitted, do logit
                $perms = "rm_tegalalur2_loket_perm";
                $comments = "Gagal menambahkan Rekam Medis dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function ajax_get_rm_by_id(){
        $rm_id = $this->input->get('rm_id',TRUE);
        $old_data = $this->Rm_model->get_rm_by_id($rm_id);
        
        if(count($old_data) > 0) {
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'data' => $old_data,
                'tgl_lahir' => date('d-m-Y',strtotime($old_data->tgl_lahir))
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "Rekam Medis tidak ditemukan");
        }
        echo json_encode($res);
    }
    
    public function do_update_mr(){
        $is_permit = $this->aauth->control_no_redirect('rm_tegalalur2_loket_perm');
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
        $this->form_validation->set_rules('upd_no_rm', 'No. Rekam Medis', 'required|trim');
        $this->form_validation->set_rules('upd_nama_pasien', 'Nama Pasien', 'required|trim');
        $this->form_validation->set_rules('upd_tgl_lahir', 'Tanggal Lahir', 'required|trim');
        
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
            $perms = "rm_ptegalalur2_loket_perm";
            $comments = "Gagal update Rekam Medis dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $rm_id = $this->input->post('upd_rm_id',TRUE);
            $nama_pasien = $this->input->post('upd_nama_pasien', TRUE);
            $tgl_lahir = $this->input->post('upd_tgl_lahir', TRUE);
            
            $data_rm = array(  
                'nama_pasien' => $nama_pasien,
                'tgl_lahir' => date('Y-m-d', strtotime($tgl_lahir))
            );
            
            $update = $this->Rm_model->update_rm($data_rm, $rm_id);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Rekam medis berhasil diubah'
                );

                // if permitted, do logit
                $perms = "rm_tegalalur2_loket_perm";
                $comments = "Berhasil mengubah Rekam Medis dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal mengubah Rekam Medis, hubungi web administrator.');

                // if permitted, do logit
                $perms = "rm_tegalalur2_loket_perm";
                $comments = "Gagal mengubah Rekam Medis dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
}