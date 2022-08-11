<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gaji extends MY_Controller {
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
        
        $this->load->model('Gaji_model');
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
        $is_permit = $this->aauth->control_no_redirect('gaji_master_ekinerja_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "Master Data";
        $this->data['bc_child'] = "Gaji";
        $perms = "gaji_master_ekinerja_perm";
        $comments = "Halaman E-Kinerja Master Gaji";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('gaji', $this->data);
    }
    
    public function ajax_list_gaji(){
        $list = $this->Gaji_model->get_gaji_list();
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        foreach($list as $gaji){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $gaji->masa_kerja;
            $row[] = $gaji->pendidikan_nama;
            $row[] = number_format($gaji->nominal_gaji,2,',','.');
            //add html for action
            $edit = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" title="Edit" onclick="editGaji('."'".$gaji->gaji_id."'".')"><i class="fa fa-edit"></i></button>';
            $delete = '<button type="button" class="btn btn-social-icon btn-danger" title="Hapus" onclick="hapusGaji('."'".$gaji->gaji_id."'".')"><i class="fa fa-trash-o"></i></button></div>';
            $row[] = $edit."&nbsp;".$delete;
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Gaji_model->count_gaji_all(),
                    "recordsFiltered" => $this->Gaji_model->count_gaji_filtered(),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function do_insert_gaji(){
        $is_permit = $this->aauth->control_no_redirect('gaji_master_ekinerja_perm');
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
        $this->form_validation->set_rules('mk_awal', 'Masa Kerja', 'required|trim');
        $this->form_validation->set_rules('mk_akhir', 'Masa Kerja', 'required|trim');
        $this->form_validation->set_rules('pendidikan', 'Pendidikan', 'required|trim');
        $this->form_validation->set_rules('gapok', 'Gaji Pokok', 'required|trim');
        
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
            $perms = "gaji_master_ekinerja_perm";
            $comments = "Gagal input SKP dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE)  {
            $mk_awal = $this->input->post('mk_awal', TRUE);
            $mk_akhir = $this->input->post('mk_akhir', TRUE);
            $pendidikan = $this->input->post('pendidikan', TRUE);
            $gapok = $this->input->post('gapok', TRUE);
            
            $data_gaji = array(
                'pendidikan' => $pendidikan,
                'masa_kerja' => $mk_awal."-".$mk_akhir,
                'nominal_gaji' => $gapok
            );
            
            $ins = $this->Gaji_model->insert_gaji($data_gaji);
            //$ins=true;
            if($ins){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Data Gaji berhasil ditambahkan'
                );

                // if permitted, do logit
                $perms = "gaji_master_ekinerja_perm";
                $comments = "Berhasil menambahkan Data Gaji baru dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menambahkan Data Gaji, hubungi web administrator.');

                // if permitted, do logit
                $perms = "gaji_master_ekinerja_perm";
                $comments = "Gagal menambahkan Data Gaji dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function ajax_get_gaji_by_id(){
        $gaji_id = $this->input->get('gaji_id',TRUE);
        $old_data = $this->Gaji_model->get_gaji_by_id($gaji_id);
        
        if(count((array)$old_data) > 0) {
            $mk = explode('-',$old_data->masa_kerja);
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'mk_awal' => $mk[0],
                'mk_akhir' => $mk[1],
                'data' => $old_data
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "Gaji ID tidak ditemukan");
        }
        echo json_encode($res);
    }
    
    public function do_update_gaji(){
        $is_permit = $this->aauth->control_no_redirect('gaji_master_ekinerja_perm');
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
        $this->form_validation->set_rules('mk_awal', 'Masa Kerja', 'required|trim');
        $this->form_validation->set_rules('mk_akhir', 'Masa Kerja', 'required|trim');
        $this->form_validation->set_rules('pendidikan', 'Pendidikan', 'required|trim');
        $this->form_validation->set_rules('gapok', 'Gaji Pokok', 'required|trim');
        
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
            $perms = "gaji_master_ekinerja_perm";
            $comments = "Gagal update Data Gaji dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $mk_awal = $this->input->post('mk_awal', TRUE);
            $mk_akhir = $this->input->post('mk_akhir', TRUE);
            $pendidikan = $this->input->post('pendidikan', TRUE);
            $gapok = $this->input->post('gapok', TRUE);
            $gaji_id = $this->input->post('gaji_id', TRUE);
            
            $data_gaji = array(
                'pendidikan' => $pendidikan,
                'masa_kerja' => $mk_awal."-".$mk_akhir,
                'nominal_gaji' => $gapok
            );
            
            $update = $this->Gaji_model->update_gaji($data_gaji, $gaji_id);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Data Gaji berhasil diubah'
                );

                // if permitted, do logit
                $perms = "gaji_master_ekinerja_perm";
                $comments = "Berhasil mengubah Data Gaji dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal mengubah Data Gaji, hubungi web administrator.');

                // if permitted, do logit
                $perms = "gaji_master_ekinerja_perm";
                $comments = "Gagal mengubah Data Gaji dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function do_delete_gaji(){
        $is_permit = $this->aauth->control_no_redirect('gaji_master_ekinerja_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $gaji_id = $this->input->post('gaji_id', TRUE);
        $delete = $this->Gaji_model->delete_gaji($gaji_id);
        if($delete){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'Gaji berhasil dihapus'
            );
            // if permitted, do logit
            $perms = "gaji_master_ekinerja_perm";
            $comments = "Berhasil menghapus Gaji dengan id = '". $gaji_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menghapus data, silakan hubungi web administrator.'
            );
            // if permitted, do logit
            $perms = "gaji_master_ekinerja_perm";
            $comments = "Gagal menghapus data dengan ID = '". $gaji_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
}