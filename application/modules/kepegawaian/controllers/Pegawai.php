<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Pegawai extends MY_Controller {
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
        
        $this->load->model('Pegawai_model');
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
        $is_permit = $this->aauth->control_no_redirect('pegawai_kepegawaian_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "Kepegawaian";
        $this->data['bc_child'] = "Pegawai";
        $perms = "pegawai_kepegawaian_perm";
        $comments = "Halaman Data Kepegawaian";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('pegawai', $this->data);
    }
    
    public function ajax_list_pegawai(){
        $list = $this->Pegawai_model->get_pegawai_list();
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        foreach($list as $admin){
            $gd = !empty($admin->gelar_depan) ? $admin->gelar_depan.". &nbsp;" : "";
            $gb = !empty($admin->gelar_belakang) ? ", &nbsp;".$admin->gelar_belakang : "";
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $admin->nip;
            $row[] = $gd.$admin->nama_pegawai.$gb;
            $row[] = $admin->tempattugas_nama;
            //add html for action
            $edit = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" title="Edit" onclick="editPegawai('."'".$admin->id_pegawai."'".')"><i class="fa fa-edit"></i></button>';
            $delete = '<button type="button" class="btn btn-social-icon btn-danger" title="Hapus" onclick="hapusPegawai('."'".$admin->id_pegawai."',"."'".$admin->nip."'".')"><i class="fa fa-trash-o"></i></button>';
            $detail = '<button type="button" class="btn btn-social-icon btn-primary" title="Detail" onclick="detailPegawai('."'".$admin->id_pegawai."'".')"><i class="fa fa-file-text"></i></button></div>';
            $row[] = $edit."&nbsp;".$delete."&nbsp;".$detail;
            
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Pegawai_model->count_pegawai_all(),
                    "recordsFiltered" => $this->Pegawai_model->count_pegawai_filtered(),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function do_insert_pegawai(){
        $is_permit = $this->aauth->control_no_redirect('pegawai_kepegawaian_perm');
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
        $this->form_validation->set_rules('nip', 'NIP', 'required|trim');
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required|trim');
        $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required|trim');
        $this->form_validation->set_rules('status_pns', 'Status PNS', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]|max_length[13]');
        $this->form_validation->set_rules('conf_pass', 'Confirm Password', 'required');
        $this->form_validation->set_rules('email', 'Email Account', 'required|valid_email');
        
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
            $perms = "pegawai_kepegawaian_perm";
            $comments = "Gagal input Data Pegawai dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE)  {
            $nip = $this->input->post('nip',TRUE); // sebagai username untuk login
            $nm_lengkap = $this->input->post('nama_lengkap',TRUE);
            $tempat_lahir = $this->input->post('tempat_lahir',true);
            $tgl_lahir = date('Y-m-d',strtotime($this->input->post('tgl_lahir',true)));
            $jenis_kelamin = $this->input->post('jenis_kelamin',true);
            $no_tlp = $this->input->post('no_tlp',true);
            $email = !empty($this->input->post('email',TRUE)) ? $this->input->post('email',TRUE) : 'default@mail.com';
            $alamat = $this->input->post('alamat',true);
            $agama = $this->input->post('agama',TRUE);
            $jabatan = $this->input->post('jabatan',true);
            $bagian = $this->input->post('bagian',true);
            $pendidikan = $this->input->post('pendidikan',true);
            $status = $this->input->post('status_pernikahan',true);
            $rumpun = $this->input->post('rumpun',true);
            $pajak = $this->input->post('pajak',true);
            $no_ktp = $this->input->post('no_ktp',true);
            $npwp = $this->input->post('no_npwp',true);
            $norek_dki = $this->input->post('no_rek',true);
            $status_pns = $this->input->post('status_pns',true);
            $bpjs_ks = $this->input->post('bpjsks',true);
            $bpjs_jkk = $this->input->post('bpjsjkk',true);
            $bpjs_ijht = $this->input->post('bpjsijht',true);
            $bpjs_jp = $this->input->post('bpjsjp',true);
            $pj_cuti = $this->input->post('pj_cuti',true);
            $tgl_masuk = date('Y-m-d',strtotime($this->input->post('tgl_masuk',true)));
            $tempattugas = $this->input->post('tempattugas',true);
            $tempattugas_ket = $this->input->post('tempattugas_ket',true);
            $nrk = $this->input->post('nrk',TRUE);
            $golongan = $this->input->post('golongan',TRUE);
            $gol = !empty($pangkat) ? $pangkat."/".$golongan : "";
            $gelar_depan = $this->input->post('gelar_depan',TRUE);
            $gelar_belakang = $this->input->post('gelar_belakang',TRUE);
            $passwd = $this->input->post('password',TRUE);
            $path = '';

            $config['upload_path'] = 'data/foto_pegawai/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = 1024;
            $config['file_name'] = $nip;
            $this->load->library('upload', $config);
            if(!empty($_FILES['foto_file']['name'])){
                if (!$this->upload->do_upload('foto_file')){
                    $error = $this->upload->display_errors();
                    $res = array(
                        'csrfTokenName' => $this->security->get_csrf_token_name(),
                        'csrfHash' => $this->security->get_csrf_hash(),
                        'success' => false,
                        'messages' => $error
                        //'messages' => 'Failed upload data sms, please contact web administrator.'
                        );
                    echo json_encode($res);
                    exit;
                }else{
                    $filedata = $this->upload->data();
                    $path = 'data/foto_pegawai/'.$filedata['file_name'];
                }
            }

            //begin to insert with trans begins() function
            // insert user
            $this->db->trans_begin();
            $insertuser = $this->aauth->create_user($email, $passwd, $nm_lengkap, $nip);
            if($insertuser){
                $group = 3; //default group
                // insert group of user
                $ins_togroup = $this->aauth->add_member($insertuser, $group);
                if($ins_togroup){
                    $data_pegawai = array(
                        'nip' => $nip,
                        'nama_pegawai' => $nm_lengkap,
                        'tempat_lahir' => $tempat_lahir,
                        'tgl_lahir' => $tgl_lahir,
                        'jenis_kelamin' => $jenis_kelamin,
                        'no_tlp' => $no_tlp,
                        'email' => $email,
                        'alamat' => $alamat,
                        'agama' => $agama,
                        'jabatan' => $jabatan,
                        'bagian' => $bagian,
                        'pendidikan' => $pendidikan,
                        'status' => $status,
                        'rumpun' => $rumpun,
                        'pajak' => $pajak,
                        'no_ktp' => $no_ktp,
                        'npwp' => $npwp,
                        'norek_dki' => $norek_dki,
                        'status_pns' => $status_pns,
                        'bpjs_ks' => $bpjs_ks,
                        'bpjs_jkk' => $bpjs_jkk,
                        'bpjs_ijht' => $bpjs_ijht,
                        'bpjs_jp' => $bpjs_jp,
                        'pj_cuti' => $pj_cuti,
                        'tgl_masuk' => $tgl_masuk,
                        'is_active' => 1,
                        'tempat_tugas' => $tempattugas,
                        'tempat_tugas_ket' => $tempattugas_ket,
                        'nrk' => $nrk,
                        'golongan' => $gol,
                        'gelar_depan' => $gelar_depan,
                        'gelar_belakang' => $gelar_belakang,
                        'created_by' => $this->data['users']->id,
                        'created_date' => date('Y-m-d H:i:s'),
                        'foto_url' => $path
                    );
                    // insert pegawai
                    $this->Pegawai_model->insert_pegawai($data_pegawai);
                }
            }
            
            if($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Failed insert user to database, please contact web administrator.');

                // if permitted, do logit
                $perms = "pegawai_kepegawaian_perm";
                $comments = "Failed to Create a new user when saving to database with post data = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $this->db->trans_commit();
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'User has been saved to database'
                );

                // if permitted, do logit
                $perms = "pegawai_kepegawaian_perm";
                $comments = "Success to Create a new user with post data = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function ajax_get_pegawai_by_id(){
        $pegawai_id = $this->input->get('pegawai_id',TRUE);
        $old_data = $this->Pegawai_model->get_pegawai_by_id($pegawai_id);
        
        if(count((array)$old_data) > 0) {
            $tgl_lahir = date("d-M-Y",strtotime($old_data->tgl_lahir));
            $tgl_masuk = date("d-M-Y",strtotime($old_data->tgl_masuk));
            
            if(!empty($old_data->golongan)){
                $golongan = explode("/", $old_data->golongan);
                
                $pangkat = $golongan[0];
                $gol = $golongan[1];
            }else{
                $pangkat = "";
                $gol = "";
            }
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'data' => $old_data,
                'tgl_lahir' => $tgl_lahir,
                'tgl_masuk' => $tgl_masuk,
                'pangkat' => $pangkat,
                'golongan' => $gol
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "ID Pegawai tidak ditemukan");
        }
        echo json_encode($res);
    }

    public function do_update_bio(){
        $is_permit = $this->aauth->control_no_redirect('pegawai_kepegawaian_perm');
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
        $this->form_validation->set_rules('upd_nip', 'NIP', 'required|trim');
        $this->form_validation->set_rules('upd_nama_lengkap', 'Nama Lengkap', 'required|trim');
        $this->form_validation->set_rules('upd_status_pns', 'Status PNS', 'required|trim');
        
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
            $perms = "pegawai_kepegawaian_perm";
            $comments = "Gagal update Data Pegawai dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $id_pegawai = $this->input->post('upd_pegawai_id',TRUE);
            $pangkat = $this->input->post('upd_pangkat',TRUE);
            $golongan = $this->input->post('upd_golongan',TRUE);
            $gol = !empty($pangkat) ? $pangkat."/".$golongan : "";
            $nip_old = $this->input->post('upd_nip_old',TRUE);
            $nip = $this->input->post('upd_nip',TRUE);
            $data_pegawai = array(
                'nip' => $nip,
                'nama_pegawai' => $this->input->post('upd_nama_lengkap',TRUE),
                'tempat_lahir' => $this->input->post('upd_tempat_lahir',TRUE),
                'tgl_lahir' => date('Y-m-d',strtotime($this->input->post('upd_tgl_lahir',TRUE))),
                'jenis_kelamin' => $this->input->post('upd_jenis_kelamin',TRUE),
                'no_tlp' => $this->input->post('upd_no_tlp',TRUE),
                'alamat' => $this->input->post('upd_alamat',TRUE),
                'jabatan' => $this->input->post('upd_jabatan',TRUE),
                'bagian' => $this->input->post('upd_bagian',TRUE),
                'pendidikan' => $this->input->post('upd_pendidikan',TRUE),
                'status' => $this->input->post('upd_status_pernikahan',TRUE),
                'rumpun' => $this->input->post('upd_rumpun',TRUE),
                'pajak' => $this->input->post('upd_pajak',TRUE),
                'no_ktp' => $this->input->post('upd_no_ktp',TRUE),
                'npwp' => $this->input->post('upd_no_npwp',TRUE),
                'norek_dki' => $this->input->post('upd_no_rek',TRUE),
                'status_pns' => $this->input->post('upd_status_pns',TRUE),
                'bpjs_ks' => $this->input->post('upd_bpjsks',TRUE),
                'bpjs_jkk' => $this->input->post('upd_bpjsjkk',TRUE),
                'bpjs_ijht' => $this->input->post('upd_bpjsijht',TRUE),
                'bpjs_jp' => $this->input->post('upd_bpjsjp',TRUE),
                'pj_cuti' => $this->input->post('upd_pj_cuti',TRUE),
                'tgl_masuk' => date('Y-m-d',strtotime($this->input->post('upd_tgl_masuk',TRUE))),
                'tempat_tugas' => $this->input->post('upd_tempattugas',TRUE),
                'tempat_tugas_ket' => $this->input->post('upd_tempattugas_ket',TRUE),
                'golongan' => $gol,
                'nrk' => $this->input->post('upd_nrk',TRUE),
                'gelar_depan' => $this->input->post('upd_gelar_depan',TRUE),
                'gelar_belakang' => $this->input->post('upd_gelar_belakang',TRUE),
                'agama' => $this->input->post('upd_agama',TRUE),
                'updated_by' => $this->data['users']->id,
                'updated_date' => date('Y-m-d H:i:s')
            );
            $update = $this->Pegawai_model->update_pegawai($data_pegawai, $id_pegawai);
            $this->Pegawai_model->update_aauth_users($nip, $nip_old);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Data Pegawai berhasil diubah'
                );

                // if permitted, do logit
                $perms = "pegawai_kepegawaian_perm";
                $comments = "Berhasil mengubah Data Pegawai dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal mengubah Data Pegawai, hubungi web administrator.');

                // if permitted, do logit
                $perms = "pegawai_kepegawaian_perm";
                $comments = "Gagal mengubah Data Data Pegawai dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    function get_tempattugas_by_id(){
        $tempattugas = $this->input->get('tempattugas',TRUE);
        $old_data = $this->Pegawai_model->get_tempattugas_byid($tempattugas);
        
        if(count((array)$old_data) > 0) {
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'data' => $old_data
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "Tempat Tugas ID tidak ditemukan");
        }
        echo json_encode($res);
    }
    
    public function do_ubah_password(){
        $is_permit = $this->aauth->control_no_redirect('pegawai_kepegawaian_perm');
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
        $this->form_validation->set_rules('upd_password', 'Password', 'required|min_length[5]|max_length[13]');
        $this->form_validation->set_rules('upd_conf_pass', 'Confirm Password', 'required');
        $this->form_validation->set_rules('upd_email', 'Email Account', 'required|valid_email');
        
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
            $perms = "pegawai_kepegawaian_perm";
            $comments = "Gagal update Password dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $username = $this->input->post('upd_username',TRUE);
            $password = $this->input->post('upd_password',TRUE);
            $email = $this->input->post('upd_email',TRUE);
            $id_pegawai = $this->input->post('upd_idpeg',TRUE);
            $nip = $this->input->post('upd_nippeg',TRUE);
            
            //update password dan email by nip
            $this->aauth->update_user_by_nip($nip, $username, $password, $email);
            //update email ke tabel m_pegawai
            $data_pegawai = array(
                'email' => $email
            );
            $update = $this->Pegawai_model->update_pegawai($data_pegawai, $id_pegawai);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Data Pegawai berhasil diubah'
                );

                // if permitted, do logit
                $perms = "pegawai_kepegawaian_perm";
                $comments = "Berhasil mengubah Data Pegawai dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal mengubah Data Pegawai, hubungi web administrator.');

                // if permitted, do logit
                $perms = "pegawai_kepegawaian_perm";
                $comments = "Gagal mengubah Data Data Pegawai dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    function do_delete_pegawai(){
        $is_permit = $this->aauth->control_no_redirect('pegawai_kepegawaian_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $user_id = $this->input->post('user_id', TRUE);
        $nip = $this->input->post('nip', TRUE);
        $this->aauth->ban_user_bynip($nip);
        $data_pegawai = array(
                'is_active' => 0
            );
        $delete = $this->Pegawai_model->update_pegawai($data_pegawai, $user_id);
        if($delete){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'User berhasil dihapus'
            );
            // if permitted, do logit
            $perms = "pegawai_kepegawaian_perm";
            $comments = "Berhasil menghapus User dengan id = '". $user_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menghapus data, silakan hubungi web administrator.'
            );
            // if permitted, do logit
            $perms = "pegawai_kepegawaian_perm";
            $comments = "Gagal menghapus data dengan ID = '". $user_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }

    public function do_import_pegawai(){
        $is_permit = $this->aauth->control_no_redirect('pegawai_kepegawaian_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $config['upload_path'] = 'data/file_excel_pegawai/';
        $config['allowed_types'] = 'xls|xlsx';
        //$config['max_size'] = 1024;
        $config['file_name'] = "datapegawai_".date('YmdHis');
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
                $perms = "pegawai_kepegawaian_perm";
                $comments = $error;
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $filedata = $this->upload->data();
                $path = 'data/file_excel_pegawai/'.$filedata['file_name'];
                
                //mulai insert data pegawai
                $this->db->trans_begin();
                //read file from path
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
                //get cell collections
                $worksheet = $spreadsheet->getActiveSheet()->toArray();
                // print_r($worksheet);
                // die;
                
                if(count($worksheet) > 0){
                    for($i=1; $i < count($worksheet) ; $i++){
                        $email = !empty($worksheet[$i]['6']) ? $worksheet[$i]['6'] : 'default@mail.com';
                        $passwd = '12345';
                        $nm_lengkap = $worksheet[$i]['1'];
                        $nip = $worksheet[$i]['0'];
                        $group = 3;
                        $tempat_lahir = $worksheet[$i]['2'];
                        $tgl_lahir = date('Y-m-d',strtotime($worksheet[$i]['3']));
                        $jenis_kelamin = $worksheet[$i]['4'];
                        $no_tlp = $worksheet[$i]['5'];
                        $alamat = $worksheet[$i]['7'];
                        $no_ktp = $worksheet[$i]['8'];
                        $npwp = $worksheet[$i]['9'];
                        $status_pns = $worksheet[$i]['10'];
                        $tgl_masuk = date('Y-m-d',strtotime($worksheet[$i]['11']));
                        $gelar_depan = $worksheet[$i]['12'];
                        $gelar_belakang = $worksheet[$i]['13'];
                        $status_kawin = $worksheet[$i]['14'];
                        $pendidikan = $worksheet[$i]['15'];
                        $rumpun_jabatan = $worksheet[$i]['16'];
                        $status_pajak = $worksheet[$i]['17'];
                        $bpjs_kes = $worksheet[$i]['18'];
                        $bpjs_jkk_jkm = $worksheet[$i]['19'];
                        $bpjs_ijht = $worksheet[$i]['20'];
                        $bpjs_jp = $worksheet[$i]['21'];
                        $nrk = $worksheet[$i]['22'];
                        if($nip == "" || $nm_lengkap == ""){
                            $this->db->trans_rollback();
                            $res = array(
                                'csrfTokenName' => $this->security->get_csrf_token_name(),
                                'csrfHash' => $this->security->get_csrf_hash(),
                                'success' => false,
                                'messages' => 'Gagal upload file excel, hubungi web administrator.'
                                );

                            // if permitted, do logit
                            $perms = "pegawai_kepegawaian_perm";
                            $comments = "Gagal menyimpan data file excel ke database";
                            $this->aauth->logit($perms, current_url(), $comments);
                            exit();
                        }else{
                            $data_pegawai = array(
                                        'nip' => $nip,
                                        'nama_pegawai' => $nm_lengkap,
                                        'tempat_lahir' => $tempat_lahir,
                                        'tgl_lahir' => $tgl_lahir,
                                        'jenis_kelamin' => $jenis_kelamin,
                                        'no_tlp' => $no_tlp,
                                        'email' => $email,
                                        'alamat' => $alamat,
                                        'no_ktp' => $no_ktp,
                                        'npwp' => $npwp,
                                        'status_pns' => $status_pns,
                                        'tgl_masuk' => $tgl_masuk,
                                        'is_active' => 1,
                                        'gelar_depan' => $gelar_depan,
                                        'gelar_belakang' => $gelar_belakang,
                                        'status' => $status_kawin,
                                        'pendidikan' => $pendidikan,
                                        'rumpun' => $rumpun_jabatan,
                                        'pajak' => $status_pajak,
                                        'bpjs_ks' =>$bpjs_kes,
                                        'bpjs_jkk' => $bpjs_jkk_jkm,
                                        'bpjs_ijht' => $bpjs_ijht,
                                        'bpjs_jp' => $bpjs_jp,
                                        'nrk'=>$nrk,
                                        'created_by' => $this->data['users']->id,
                                        'created_date' => date('Y-m-d H:i:s')
                                    );
                            
                            $check_constrain = $this->Pegawai_model->count_pegawai_by_nip($nip);
                            if($check_constrain > 0){
                                $this->Pegawai_model->update_pegawai_by_nip($data_pegawai, $nip);
                            }else{
                                $insertuser = $this->aauth->create_user($email, $passwd, $nm_lengkap, $nip);
                                if($insertuser){
                                    $ins_togroup = $this->aauth->add_member($insertuser, $group);
                                    if($ins_togroup){
                                        $this->Pegawai_model->insert_pegawai($data_pegawai);
                                    }
                                }
                            }
                            
                        }
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
                    $perms = "pegawai_kepegawaian_perm";
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
                    $perms = "pegawai_kepegawaian_perm";
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
            $perms = "pegawai_kepegawaian_perm";
            $comments = $error;
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
} // end of class