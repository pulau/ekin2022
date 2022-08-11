<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Skp_tahunan extends MY_Controller {
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
        
        $this->load->model('Skp_tahunan_model');
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
        $is_permit = $this->aauth->control_no_redirect('skptahunan_ekinerja_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "E-Kinerja";
        $this->data['bc_child'] = "SKP Tahunan";
        $perms = "skptahunan_ekinerja_perm";
        $comments = "Halaman E-Kinerja SKP Tahunan";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('skp_tahunan', $this->data);
    }
    
    public function ajax_list_skptahunan(){
        $filter_thn = $this->input->get('filter_thn',TRUE);
        $list = $this->Skp_tahunan_model->get_skptahunan_list($this->data['pegawai']->id_pegawai, $filter_thn);
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        
        foreach($list as $skptahunan){
            $qty_per_bulan = $skptahunan->qty / 12;
            $no++;
            $row = array();
            $row[] = $no;
           // $row[] = $skptahunan->nip;
           // $row[] = $skptahunan->bagian_nama;
            $row[] = $skptahunan->skp;
            $row[] = $skptahunan->qty;
            $row[] = $skptahunan->waktu_efektif." Menit";
            $row[] = $skptahunan->waktu_total." Menit";
            $row[] = number_format($qty_per_bulan,1);
            //add html for action
            if($skptahunan->is_validate == 0){
                $edit = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" title="Edit" onclick="editSKPTahunan('."'".$skptahunan->skptahunan_id."'".')"><i class="fa fa-edit"></i></button>';
                $delete = '<button type="button" class="btn btn-social-icon btn-danger" title="Hapus" onclick="deleteSKPTahunan('."'".$skptahunan->skptahunan_id."'".')"><i class="fa fa-trash-o"></i></button></div>';
            }else{
                $edit = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" title="Edit" disabled><i class="fa fa-edit"></i></button>';
                // $delete = '<button type="button" class="btn btn-social-icon btn-danger" title="Hapus" disabled><i class="fa fa-trash-o"></i></button></div>';
                $delete = '<button type="button" class="btn btn-social-icon btn-danger" title="Hapus" onclick="deleteSKPTahunan('."'".$skptahunan->skptahunan_id."'".')"><i class="fa fa-trash-o"></i></button></div>';
            }
            $row[] = $edit."&nbsp;".$delete;
           // if($skptahunan->is_validate == 0){
           // $row[] = '<button class="btn btn-icon-only blue" title="Edit" onclick="editSKPTahunan('."'".$skptahunan->skptahunan_id."'".')"><i class="glyphicon glyphicon-pencil"></i></button>
           //         <button class="btn btn-icon-only red" title="Delete" onclick="deleteSKPTahunan('."'".$skptahunan->skptahunan_id."'".')"><i class="glyphicon glyphicon-trash"></i></button>';
           // }else{
           //     $row[] = '<button class="btn btn-icon-only blue" title="Edit" disabled><i class="glyphicon glyphicon-pencil"></i></button>
           //         <button class="btn btn-icon-only red" title="Delete" disabled><i class="glyphicon glyphicon-trash"></i></button>';
           // }
            
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Skp_tahunan_model->count_skptahunan_all($this->data['pegawai']->id_pegawai, $filter_thn),
                    "recordsFiltered" => $this->Skp_tahunan_model->count_skptahunan_filtered($this->data['pegawai']->id_pegawai, $filter_thn),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function do_insert_skptahunan(){
        $is_permit = $this->aauth->control_no_redirect('skptahunan_ekinerja_perm');
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
        $this->form_validation->set_rules('skp', 'SKP', 'required|trim');
        $this->form_validation->set_rules('kuantitas', 'Kuantitas', 'required|trim');
        
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
            $perms = "skptahunan_ekinerja_perm";
            $comments = "Gagal Menambahkan SKP Tahunan dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE)  {
            $skp = $this->input->post('skp',TRUE);
            $kuantitas = $this->input->post('kuantitas',TRUE);
            $skp_list = $this->Skp_tahunan_model->get_skp_by_id($skp);
            $waktu_efektif = $skp_list->waktu;
            $waktu_total = intval($waktu_efektif) * intval($kuantitas);
            $data_skptahunan = array(
                'id_pegawai' => $this->input->post('id_peg',TRUE),
                'kd_skp' => $skp,
                'bagian_id' => $this->input->post('bagian_id',TRUE),
                'qty' => $kuantitas,
                'kualitas' => 100,
                'waktu_total' => $waktu_total,
                'waktu_efektif' => $waktu_efektif,
                'tahun' => date('Y'),
                'create_date' => date('Y-m-d H:i:s')
            );
            
            $ins = $this->Skp_tahunan_model->insert_skptahunan($data_skptahunan);
            //$ins=true;
            if($ins){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'SKP Tahunan berhasil ditambahkan'
                );

                // if permitted, do logit
                $perms = "skptahunan_ekinerja_perm";
                $comments = "Berhasil menambahkan skp tahunan dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menambahkan skp tahunan, hubungi web administrator.');

                // if permitted, do logit
                $perms = "skptahunan_ekinerja_perm";
                $comments = "Gagal menambahkan skp tahunan dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function ajax_get_skpt_by_id(){
        $is_permit = $this->aauth->control_no_redirect('skptahunan_ekinerja_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $skpt_id = $this->input->get('skpt_id', TRUE);
        $skpt_old = $this->Skp_tahunan_model->get_skpt_by_id($skpt_id);
        
        if(count((array)$skpt_old) > 0) {
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'data' => $skpt_old
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "SKP Tahunan tidak ditemukan");
        }
        echo json_encode($res);
    }
    
    public function do_update_skptahunan(){
        $is_permit = $this->aauth->control_no_redirect('skptahunan_ekinerja_perm');
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
        $this->form_validation->set_rules('upd_skp', 'SKP', 'required|trim');
        $this->form_validation->set_rules('upd_kuantitas', 'Kuantitas', 'required|trim');
        
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
            $perms = "skptahunan_ekinerja_perm";
            $comments = "Gagal Meng-update SKP Tahunan dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE)  {
            $skp = $this->input->post('upd_skp',TRUE);
            $kuantitas = $this->input->post('upd_kuantitas',TRUE);
            $skpt_id = $this->input->post('skptahunan_id',TRUE);
            $skp_list = $this->Skp_tahunan_model->get_skp_by_id($skp);
            $waktu_efektif = $skp_list->waktu;
            $waktu_total = intval($waktu_efektif) * intval($kuantitas);
            
            $check_constrain = $this->Skp_tahunan_model->check_constraint_skpt($skpt_id);
            if($check_constrain > 0){
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => false,
                    'messages' => 'Data sedang digunakan di tabel lain');
                echo json_encode($res);
                exit;
            }
            
            $data_skptahunan = array(
                'kd_skp' => $skp,
                'qty' => $kuantitas,
                'kualitas' => 100,
                'waktu_total' => $waktu_total,
                'waktu_efektif' => $waktu_efektif,
                'update_date' => date('Y-m-d H:i:s')
            );
            
            $update = $this->Skp_tahunan_model->update_skpt($data_skptahunan, $skpt_id);
            //$update=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'SKP Tahunan berhasil diubah'
                );

                // if permitted, do logit
                $perms = "skptahunan_ekinerja_perm";
                $comments = "Berhasil mengubah skp tahunan dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal mengubah skp tahunan, hubungi web administrator.');

                // if permitted, do logit
                $perms = "skptahunan_ekinerja_perm";
                $comments = "Gagal mengubah skp tahunan dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function do_delete_skpt(){
        $is_permit = $this->aauth->control_no_redirect('skptahunan_ekinerja_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $skptahunan_id = $this->input->post('skptahunan_id', TRUE);
        $check_constrain = $this->Skp_tahunan_model->check_constraint_skpt($skptahunan_id);
        if($check_constrain > 0){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Data sedang digunakan di tabel lain');
            echo json_encode($res);
            exit;
        }
        $delete = $this->Skp_tahunan_model->delete_skpt($skptahunan_id);
        if($delete){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'SKP Tahunan berhasil dihapus'
            );
            // if permitted, do logit
            $perms = "skptahunan_ekinerja_perm";
            $comments = "Berhasil menghapus skp tahunan dengan id = '". $skptahunan_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menghapus data, silakan hubungi web administrator.'
            );
            // if permitted, do logit
            $perms = "skptahunan_ekinerja_perm";
            $comments = "Gagal menghapus data dengan ID = '". $skptahunan_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
}