<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penyerapan extends MY_Controller {
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
        
        $this->load->model('Penyerapan_model');
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
        $is_permit = $this->aauth->control_no_redirect('penyerapan_master_ekinerja_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "Master Data";
        $this->data['bc_child'] = "Penyerapan";
        $perms = "penyerapan_master_ekinerja_perm";
        $comments = "Halaman E-Kinerja Penyerapan";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('penyerapan', $this->data);
    }
    
    public function ajax_list_penyerapan(){
        $list = $this->Penyerapan_model->get_penyerapan_list();
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        foreach($list as $penyerapan){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = date('M Y', strtotime($penyerapan->bulan));
            $row[] = $penyerapan->nilai;
            //add html for action
            $edit = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" title="Edit" onclick="editPenyerapan('."'".$penyerapan->bulan."'".')"><i class="fa fa-edit"></i></button>';
            $delete = '<button type="button" class="btn btn-social-icon btn-danger" title="Hapus" onclick="hapusPenyerapan('."'".$penyerapan->bulan."'".')"><i class="fa fa-trash-o"></i></button></div>';
            $row[] = $edit."&nbsp;".$delete;
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Penyerapan_model->count_penyerapan_all(),
                    "recordsFiltered" => $this->Penyerapan_model->count_penyerapan_filtered(),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function do_insert_penyerapan(){
        $is_permit = $this->aauth->control_no_redirect('penyerapan_master_ekinerja_perm');
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
        $this->form_validation->set_rules('bulan', 'Bulan', 'required|trim');
        $this->form_validation->set_rules('nilai', 'Jumlah Hari', 'required|trim');
        
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
            $perms = "penyerapan_master_ekinerja_perm";
            $comments = "Gagal input penyerapan dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE)  {
            $bulan = date('Y-m',strtotime($this->input->post('bulan', TRUE)));
            $nilai = $this->input->post('nilai', TRUE);
            
            $data_penyerapan = array(
                'bulan' => $bulan,
                'nilai' => $nilai
            );
            
            $check_constrain = $this->Penyerapan_model->check_constraint_penyerapan($bulan);
            if($check_constrain > 0){
                $ins = $this->Penyerapan_model->update_penyerapan($data_penyerapan, $bulan);
            }else{
                $ins = $this->Penyerapan_model->insert_penyerapan($data_penyerapan);
            }
            //$ins=true;
            if($ins){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Penyerapan berhasil ditambahkan'
                );

                // if permitted, do logit
                $perms = "penyerapan_master_ekinerja_perm";
                $comments = "Berhasil menambahkan Penyerapan baru dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menambahkan Penyerapan, hubungi web administrator.');

                // if permitted, do logit
                $perms = "penyerapan_master_ekinerja_perm";
                $comments = "Gagal menambahkan Penyerapan dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function ajax_get_penyerapan_by_bln(){
        $bln = $this->input->get('bln',TRUE);
        $old_data = $this->Penyerapan_model->get_penyerapan_by_bln($bln);
        
        if(count((array)$old_data) > 0) {
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'data' => $old_data,
                'bln' => date('M Y',strtotime($old_data->bulan))
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "Penyerapan kerja tidak ditemukan");
        }
        echo json_encode($res);
    }
    
    public function do_delete_penyerapan(){
        $is_permit = $this->aauth->control_no_redirect('penyerapan_master_ekinerja_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $bln = $this->input->post('bln', TRUE);
        $delete = $this->Penyerapan_model->delete_penyerapan($bln);
        if($delete){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'Penyerapan berhasil dihapus'
            );
            // if permitted, do logit
            $perms = "penyerapan_master_ekinerja_perm";
            $comments = "Berhasil menghapus penyerapan bulan = '". $bln ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menghapus data, silakan hubungi web administrator.'
            );
            // if permitted, do logit
            $perms = "penyerapan_master_ekinerja_perm";
            $comments = "Gagal menghapus data dengan bulan = '". $bln ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
}