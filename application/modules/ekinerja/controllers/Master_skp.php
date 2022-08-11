<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_skp extends MY_Controller {
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
        
        $this->load->model('Skp_model');
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
        $is_permit = $this->aauth->control_no_redirect('skp_master_ekinerja_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "Master";
        $this->data['bc_child'] = "Master SKP";
        $perms = "skp_master_ekinerja_perm";
        $comments = "Halaman Dashboard E-Kinerja";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('master_skp', $this->data);
    }
    
    public function ajax_list_skp(){
        $list = $this->Skp_model->get_skp_list();
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        foreach($list as $skp){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $skp->skp;
            $row[] = $skp->waktu;
            //add html for action
            $edit = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" title="Edit" onclick="editSKP('."'".$skp->kd_skp."'".')"><i class="fa fa-edit"></i></button>';
            $delete = '<button type="button" class="btn btn-social-icon btn-danger" title="Hapus" onclick="hapusSKP('."'".$skp->kd_skp."'".')"><i class="fa fa-trash-o"></i></button></div>';
            $row[] = $edit."&nbsp;".$delete;
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Skp_model->count_skp_all(),
                    "recordsFiltered" => $this->Skp_model->count_skp_filtered(),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function do_insert_skp(){
        $is_permit = $this->aauth->control_no_redirect('skp_master_ekinerja_perm');
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
        $this->form_validation->set_rules('skp_nama', 'SKP', 'required|trim');
        $this->form_validation->set_rules('waktu', 'Waktu', 'required|trim');
        
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
            $perms = "skp_master_ekinerja_perm";
            $comments = "Gagal input SKP dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE)  {
            $skp = $this->input->post('skp_nama', TRUE);
            $waktu = $this->input->post('waktu', TRUE);
            
            $data_skp = array(
                'skp' => $skp,
                'waktu' => $waktu
            );
            
            $ins = $this->Skp_model->insert_skp($data_skp);
            //$ins=true;
            if($ins){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Data SKP berhasil ditambahkan'
                );

                // if permitted, do logit
                $perms = "skp_master_ekinerja_perm";
                $comments = "Berhasil menambahkan Data SKP baru dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menambahkan Data SKP, hubungi web administrator.');

                // if permitted, do logit
                $perms = "skp_master_ekinerja_perm";
                $comments = "Gagal menambahkan Data SKP dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function ajax_get_skp_by_id(){
        $kd_skp = $this->input->get('kd_skp',TRUE);
        $old_data = $this->Skp_model->get_skp_by_id($kd_skp);
        
        if(count((array)$old_data) > 0) {
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'data' => $old_data
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "SKP ID tidak ditemukan");
        }
        echo json_encode($res);
    }

    public function do_update_skp(){
        $is_permit = $this->aauth->control_no_redirect('skp_master_ekinerja_perm');
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
        $this->form_validation->set_rules('skp_nama', 'SKP', 'required|trim');
        $this->form_validation->set_rules('waktu', 'Waktu', 'required|trim');
        
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
            $perms = "skp_master_ekinerja_perm";
            $comments = "Gagal update Data SKP dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $kd_skp = $this->input->post('kd_skp',TRUE);
            $skp_nama = $this->input->post('skp_nama', TRUE);
            $waktu = $this->input->post('waktu', TRUE);
            
            $data_skp = array(
                'skp' => $skp_nama,
                'waktu' => $waktu
            );
            
            $update = $this->Skp_model->update_skp($data_skp, $kd_skp);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Data SKP berhasil diubah'
                );

                // if permitted, do logit
                $perms = "skp_master_ekinerja_perm";
                $comments = "Berhasil mengubah Data SKP dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal mengubah Data SKP, hubungi web administrator.');

                // if permitted, do logit
                $perms = "skp_master_ekinerja_perm";
                $comments = "Gagal mengubah Data SKP dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function do_delete_skp(){
        $is_permit = $this->aauth->control_no_redirect('skp_master_ekinerja_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $kd_skp = $this->input->post('kd_skp', TRUE);
        $check_constrain = $this->Skp_model->check_constraint_skp($kd_skp);
        if($check_constrain > 0){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Data sedang digunakan di tabel lain');
            echo json_encode($res);
            exit;
        }
        $delete = $this->Skp_model->delete_skp($kd_skp);
        if($delete){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'SKP berhasil dihapus'
            );
            // if permitted, do logit
            $perms = "skp_master_ekinerja_perm";
            $comments = "Berhasil menghapus skp dengan id = '". $kd_skp ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menghapus data, silakan hubungi web administrator.'
            );
            // if permitted, do logit
            $perms = "skp_master_ekinerja_perm";
            $comments = "Gagal menghapus data dengan ID = '". $kd_skp ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
    
    //import data pegawai dari file excel
    public function do_import_skp(){
        $is_permit = $this->aauth->control_no_redirect('skp_master_ekinerja_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $config['upload_path'] = 'data/file_excel_skp/';
        $config['allowed_types'] = 'xls|xlsx';
        //$config['max_size'] = 1024;
        $config['file_name'] = "dataskp_".date('YmdHis');
        $this->load->library('upload', $config);
        if(!empty($_FILES['file_excel']['name'])){
            if (!$this->upload->do_upload('file_excel')){
                $error = $this->upload->display_errors();
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => false,
                    'messages' => $error
                    );
                
                // if permitted, do logit
                $perms = "skp_master_ekinerja_perm";
                $comments = $error;
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $this->load->library('excel');
                $filedata = $this->upload->data();
                $path = 'data/file_excel_skp/'.$filedata['file_name'];
                
                //mulai insert data pegawai
                $this->db->trans_begin();
                //read file from path
                $objPHPExcel = PHPExcel_IOFactory::load($path);
                //get only the Cell Collection
                $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                $arr_data = array();
                //extract to a PHP readable array format
                foreach ($cell_collection as $cell) {
                    $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                    $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                    $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();

                    //header will/should be in row 1 only. of course this can be modified to suit your need.
                    if ($row == 1) {
                        $header[$row][$column] = $data_value;
                    } else {
                        $arr_data[$row][$column] = $data_value;
                    }
                }

                //send the data in an array format
                $header = $header;
                $values = $arr_data;
                
                if(count((array)$values) > 0){
                    foreach($values as $val){
                        $skp = $val['B'];
                        $waktu = $val['D'];
                        
                        $data_skp = array(
                            'skp' => $skp,
                            'waktu' => $waktu
                        );

                        $this->Skp_model->insert_skp($data_skp);
                    }
                }
                
                if($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => false,
                    'messages' => 'Gagal upload file excel, hubungi web administrator.');

                    // if permitted, do logit
                    $perms = "skp_master_ekinerja_perm";
                    $comments = "Gagal menyimpan data file excel ke database";
                    $this->aauth->logit($perms, current_url(), $comments);
                }else{
                    $this->db->trans_commit();
                    $res = array(
                        'csrfTokenName' => $this->security->get_csrf_token_name(),
                        'csrfHash' => $this->security->get_csrf_hash(),
                        'success' => true,
                        'messages' => 'File Excel berhasil diupload'
                    );

                    // if permitted, do logit
                    $perms = "skp_master_ekinerja_perm";
                    $comments = "Berhasil Upload File Excel";
                    $this->aauth->logit($perms, current_url(), $comments);
                }
            }
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Failed upload file, please contact web administrator.'
            );
            
            // if permitted, do logit
            $perms = "skp_master_ekinerja_perm";
            $comments = $error;
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
}