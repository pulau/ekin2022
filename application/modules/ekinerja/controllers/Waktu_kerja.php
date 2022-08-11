<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Waktu_kerja extends MY_Controller {
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
        
        $this->load->model('Waktu_kerja_model');
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
        $is_permit = $this->aauth->control_no_redirect('waktukerja_master_ekinerja_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "Master Data";
        $this->data['bc_child'] = "Waktu Kerja";
        $perms = "waktukerja_master_ekinerja_perm";
        $comments = "Halaman E-Kinerja Waktu Kerja";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('waktu_kerja', $this->data);
    }
    
    public function ajax_list_waktu_kerja(){
        $list = $this->Waktu_kerja_model->get_waktukerja_list();
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        foreach($list as $waktu){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = date('M Y', strtotime($waktu->bulan));
            $row[] = $waktu->jml_hari;
            $row[] = $waktu->menit_per_hari;
            //add html for action
            $edit = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" title="Edit" onclick="editWaktu('."'".$waktu->bulan."'".')"><i class="fa fa-edit"></i></button>';
            $delete = '<button type="button" class="btn btn-social-icon btn-danger" title="Hapus" onclick="hapusWaktu('."'".$waktu->bulan."'".')"><i class="fa fa-trash-o"></i></button></div>';
            $row[] = $edit."&nbsp;".$delete;
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Waktu_kerja_model->count_waktukerja_all(),
                    "recordsFiltered" => $this->Waktu_kerja_model->count_waktukerja_filtered(),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function do_insert_waktu_kerja(){
        $is_permit = $this->aauth->control_no_redirect('waktukerja_master_ekinerja_perm');
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
        $this->form_validation->set_rules('jml_hari', 'Jumlah Hari', 'required|trim');
        $this->form_validation->set_rules('menit_per_hari', 'Menit per Hari', 'required|trim');
        
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
            $perms = "waktukerja_master_ekinerja_perm";
            $comments = "Gagal input waktu kerja dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE)  {
            $bulan = date('Y-m',strtotime($this->input->post('bulan', TRUE)));
            $jml_hari = $this->input->post('jml_hari', TRUE);
            $menit_per_hari = $this->input->post('menit_per_hari', TRUE);
            
            $data_waktukerja = array(
                'bulan' => $bulan,
                'jml_hari' => $jml_hari,
                'menit_per_hari' => $menit_per_hari
            );
            
            $check_constrain = $this->Waktu_kerja_model->check_constraint_waktukerja($bulan);
            if($check_constrain > 0){
                $ins = $this->Waktu_kerja_model->update_waktukerja($data_waktukerja, $bulan);
            }else{
                $ins = $this->Waktu_kerja_model->insert_waktukerja($data_waktukerja);
            }
            //$ins=true;
            if($ins){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Waktu Kerja berhasil ditambahkan'
                );

                // if permitted, do logit
                $perms = "waktukerja_master_ekinerja_perm";
                $comments = "Berhasil menambahkan Waktu Kerja baru dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menambahkan Waktu Kerja, hubungi web administrator.');

                // if permitted, do logit
                $perms = "waktukerja_master_ekinerja_perm";
                $comments = "Gagal menambahkan Waktu Kerja dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function ajax_get_waktukerja_by_bln(){
        $bln = $this->input->get('bln',TRUE);
        $old_data = $this->Waktu_kerja_model->get_waktukerja_by_bln($bln);
        
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
                'messages' => "Waktu kerja tidak ditemukan");
        }
        echo json_encode($res);
    }
    
    public function do_delete_waktu_kerja(){
        $is_permit = $this->aauth->control_no_redirect('waktukerja_master_ekinerja_perm');
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
        $delete = $this->Waktu_kerja_model->delete_waktu_kerja($bln);
        if($delete){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'Waktu Kerja berhasil dihapus'
            );
            // if permitted, do logit
            $perms = "waktukerja_master_ekinerja_perm";
            $comments = "Berhasil menghapus waktu kerja bulan = '". $bln ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menghapus data, silakan hubungi web administrator.'
            );
            // if permitted, do logit
            $perms = "waktukerja_master_ekinerja_perm";
            $comments = "Gagal menghapus data dengan bulan = '". $bln ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
}