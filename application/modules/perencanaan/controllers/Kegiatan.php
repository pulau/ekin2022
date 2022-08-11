<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kegiatan extends MY_Controller {
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
        
        $this->load->model('Kegiatan_model');
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
        $is_permit = $this->aauth->control_no_redirect('kegiatan_master_perencanaan');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "Master Data";
        $this->data['bc_child'] = "Kegiatan";
        $perms = "kegiatan_master_perencanaan";
        $comments = "Halaman Master Kegiatan";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('kegiatan', $this->data);
    }
    
    public function ajax_list_kegiatan(){
        $list = $this->Kegiatan_model->get_kegiatan_list();
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        foreach($list as $inpro){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $inpro->kegiatan_nama;
            $row[] = $inpro->indikator_program;
            //add html for action
            $edit = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" title="Edit" onclick="editKegiatan('."'".$inpro->kegiatan_id."'".')"><i class="fa fa-edit"></i></button>';
            $delete = '<button type="button" class="btn btn-social-icon btn-danger" title="Hapus" onclick="hapusKegiatan('."'".$inpro->kegiatan_id."'".')"><i class="fa fa-trash-o"></i></button></div>';
            $row[] = $edit."&nbsp;".$delete;
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Kegiatan_model->count_kegiatan_all(),
                    "recordsFiltered" => $this->Kegiatan_model->count_kegiatan_filtered(),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function do_insert_kegiatan(){
        $is_permit = $this->aauth->control_no_redirect('kegiatan_master_perencanaan');
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
        $this->form_validation->set_rules('inpro', 'Indikator Program', 'required|trim');
        $this->form_validation->set_rules('kegiatan', 'Kegiatan', 'required|trim');
        
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
            $perms = "kegiatan_master_perencanaan";
            $comments = "Gagal input Kegiatan dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE)  {
            $indikator_program = $this->input->post('inpro', TRUE);
            $kegiatan = $this->input->post('kegiatan', TRUE);
            
            $data_kegiatan = array(
                'kegiatan_nama' => $kegiatan,
                'indikator_id' => $indikator_program,
                'create_by' => $this->data['pegawai']->id_pegawai,
                'create_date' => date('Y-m-d H:i:s'),
                'update_by' => $this->data['pegawai']->id_pegawai,
                'update_date' => date('Y-m-d H:i:s')
            );
            
            $ins = $this->Kegiatan_model->insert_kegiatan($data_kegiatan);
            //$ins=true;
            if($ins){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Kegiatan berhasil ditambahkan'
                );

                // if permitted, do logit
                $perms = "kegiatan_master_perencanaan";
                $comments = "Berhasil menambahkan Kegiatan baru dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menambahkan Kegiatan, hubungi web administrator.');

                // if permitted, do logit
                $perms = "kegiatan_master_perencanaan";
                $comments = "Gagal menambahkan Kegiatan dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function ajax_get_kegiatan_by_id(){
        $kegiatan_id = $this->input->get('kegiatan_id',TRUE);
        $old_data = $this->Kegiatan_model->get_kegiatan_by_id($kegiatan_id);
        $inpro = $this->Kegiatan_model->get_program_id($old_data->indikator_id);
        
        $program_id = $inpro->program_id;
        
        $list_inpro = $this->Kegiatan_model->inpro_list($program_id);
        
        $inpro_opt = '<option value="" selected disabled>-- Pilih Indikator Program --</option>';
        if(count($list_inpro) > 0){
            foreach($list_inpro as $pro){
                $inpro_opt .= "<option value='".$pro->indikator_id."'>".$pro->indikator_program."</option>";
            }
        }
         
        if(count($old_data) > 0) {
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'data' => $old_data,
                "list_inpro" => $inpro_opt,
                'program_id' => $program_id
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "Kegiatan ID tidak ditemukan");
        }
        echo json_encode($res);
    }

    public function do_update_kegiatan(){
        $is_permit = $this->aauth->control_no_redirect('kegiatan_master_perencanaan');
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
        $this->form_validation->set_rules('inpro', 'Indikator Program', 'required|trim');
        $this->form_validation->set_rules('kegiatan', 'Kegiatan', 'required|trim');
        
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
            $perms = "kegiatan_master_perencanaan";
            $comments = "Gagal update Kegiatan dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $kegiatan_id = $this->input->post('kegiatan_id',TRUE);
            $indikator_program = $this->input->post('inpro', TRUE);
            $kegiatan = $this->input->post('kegiatan', TRUE);
            
            $data_kegiatan = array(
                'kegiatan_nama' => $kegiatan,
                'indikator_id' => $indikator_program,
                'update_by' => $this->data['pegawai']->id_pegawai,
                'update_date' => date('Y-m-d H:i:s')
            );
            
            $update = $this->Kegiatan_model->update_kegiatan($data_kegiatan, $kegiatan_id);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Kegiatan berhasil diubah'
                );

                // if permitted, do logit
                $perms = "kegiatan_master_perencanaan";
                $comments = "Berhasil mengubah Kegiatan dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal mengubah Kegiatan, hubungi web administrator.');

                // if permitted, do logit
                $perms = "kegiatan_master_perencanaan";
                $comments = "Gagal mengubah Kegiatan dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function do_delete_kegiatan(){
        $is_permit = $this->aauth->control_no_redirect('kegiatan_master_perencanaan');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $kegiatan_id = $this->input->post('kegiatan_id', TRUE);
        $check_constrain = $this->Kegiatan_model->check_constraint_kegiatan($kegiatan_id);
        if($check_constrain > 0){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Data sedang digunakan di tabel lain');
            echo json_encode($res);
            exit;
        }
        $delete = $this->Kegiatan_model->delete_kegiatan($kegiatan_id);
        if($delete){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'Kegiatan berhasil dihapus'
            );
            // if permitted, do logit
            $perms = "kegiatan_master_perencanaan";
            $comments = "Berhasil menghapus indikator program dengan id = '". $kegiatan_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menghapus data, silakan hubungi web administrator.'
            );
            // if permitted, do logit
            $perms = "kegiatan_master_perencanaan";
            $comments = "Gagal menghapus Kegiatan dengan ID = '". $kegiatan_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
    
    public function set_inpro(){
        $program_id = $this->input->get('program',TRUE);
        
        $list_inpro = $this->Kegiatan_model->inpro_list($program_id);
        
        $inpro = '<option value="" selected disabled>-- Pilih Indikator Program --</option>';
        if(count($list_inpro) > 0){
            foreach($list_inpro as $pro){
                $inpro .= "<option value='".$pro->indikator_id."'>".$pro->indikator_program."</option>";
            }
        }
        $res = array(
            "list_inpro" => $inpro,
            "success" => true
        );
        
         echo json_encode($res);
    }
}