<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Validasi_skp extends MY_Controller {
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
        
        $this->load->model('Validasi_skp_model');
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
        $is_permit = $this->aauth->control_no_redirect('validasi_skp_ekinerja_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "E-Kinerja";
        $this->data['bc_child'] = "Validasi SKP Tahunan";
        $perms = "validasi_skp_ekinerja_perm";
        $comments = "Halaman Validasi SKP Tahunan";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('validasi_skp', $this->data);
    }
    
    public function ajax_list_pegawai_skp(){
        $list = $this->Validasi_skp_model->get_pegawai_list($this->data['pegawai']->id_pegawai);
        
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
       
        foreach($list as $pegawai){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $pegawai->nip;
            $row[] = $pegawai->nama_pegawai;
            $row[] = $pegawai->bagian_nama;
            $row[] = $pegawai->jml_skpt;
            //add html for action
            $row[] = '<button type="button" class="btn btn-success" title="Validasi" onclick="listKinerja('."'".$pegawai->id_pegawai."',"."'".$pegawai->nama_pegawai."',".')"><i class="fa fa-edit"></i>Setting</button>';
            // $row[] = '<button type="button" class="btn btn-success" title="Validasi" onclick="listKinerja('."'".$pegawai->id_pegawai."',"."'".$pegawai->nama_pegawai."',".')"><i class="fa fa-edit"></i>Setting</button>';
                  
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Validasi_skp_model->count_pegawai_all($this->data['pegawai']->id_pegawai),
                    "recordsFiltered" => $this->Validasi_skp_model->count_pegawai_filtered($this->data['pegawai']->id_pegawai),
                    "data" => $data,
                    );
        //output to json format
        // print_r($output);
        echo json_encode($output);
    }
    
    public function ajax_list_skpt_pegawai(){
        $id_pegawai = $this->input->get('id_pegawai',TRUE);
        $list = $this->Validasi_skp_model->get_skptahunan_list($id_pegawai);
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        
        foreach($list as $skptahunan){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $skptahunan->skp;
            $row[] = $skptahunan->qty;
            $row[] = $skptahunan->waktu_efektif." Menit";
            $row[] = $skptahunan->waktu_total." Menit";
            //add html for action
            if($skptahunan->is_validate == 0){
            $row[] = '<button type="button" class="btn btn-social-icon btn-info" title="validasi" onclick="validasiSKPTahunan('."'".$skptahunan->skptahunan_id."'".')"><i class="fa fa-edit"></i></button>
                    <button type="button" class="btn btn-social-icon btn-danger" title="Delete" onclick="deleteSKPTahunan('."'".$skptahunan->skptahunan_id."'".')"><i class="fa fa-trash-o"></i></button>';
            }else{
            	$row[] = '<button type="button" class="btn btn-social-icon btn-info" title="validasi" onclick="validasiSKPTahunan('."'".$skptahunan->skptahunan_id."'".')" disabled><i class="fa fa-edit"></i></button>
                    <button type="button" class="btn btn-social-icon btn-danger" title="Delete" onclick="deleteSKPTahunan('."'".$skptahunan->skptahunan_id."'".')"><i class="fa fa-trash-o"></i></button>';
                // $row[] = '<button type="button" class="btn btn-social-icon btn-info" title="Edit" disabled><i class="fa fa-edit"></i></button>
                //     <button type="button" class="btn btn-social-icon btn-danger" title="Delete" disabled><i class="fa fa-trash-o"></i></button>';
            }
            

            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Validasi_skp_model->count_skptahunan_all($id_pegawai),
                    "recordsFiltered" => $this->Validasi_skp_model->count_skptahunan_filtered($id_pegawai),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function do_update_skptahunan(){
        $is_permit = $this->aauth->control_no_redirect('validasi_skp_ekinerja_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $skpt_id = $this->input->post('skptahunan_id',TRUE);

        $check_constrain = $this->Validasi_skp_model->check_constraint_skpt($skpt_id);
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
            'is_validate' => 1
        );

        $update = $this->Validasi_skp_model->update_skpt($data_skptahunan, $skpt_id);
        //$update=true;
        if($update){                
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'SKP Berhasil Divalidasi'
            );

            // if permitted, do logit
            $perms = "validasi_skp_ekinerja_perm";
            $comments = "Berhasil memvalidasi skp tahunan dengan data berikut = '". json_encode($_REQUEST) ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
            'csrfTokenName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
            'success' => false,
            'messages' => 'Gagal memvalidasi skp tahunan, hubungi web administrator.');

            // if permitted, do logit
            $perms = "validasi_skp_ekinerja_perm";
            $comments = "Gagal memvalidasi skp tahunan dengan data berikut = '". json_encode($_REQUEST) ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        echo json_encode($res);
    }
    
    public function do_delete_skpt(){
        $is_permit = $this->aauth->control_no_redirect('validasi_skp_ekinerja_perm');
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
        $check_constrain = $this->Validasi_skp_model->check_constraint_skpt($skptahunan_id);
        if($check_constrain > 0){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Data sedang digunakan di tabel lain');
            echo json_encode($res);
            exit;
        }
        $delete = $this->Validasi_skp_model->delete_skpt($skptahunan_id);
        if($delete){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'SKP Tahunan berhasil dihapus'
            );
            // if permitted, do logit
            $perms = "validasi_skp_ekinerja_perm";
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
            $perms = "validasi_skp_ekinerja_perm";
            $comments = "Gagal menghapus data dengan ID = '". $skptahunan_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
}