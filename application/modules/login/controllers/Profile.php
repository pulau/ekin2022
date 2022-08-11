<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends MY_Controller {
    public $data = array();
    
    function __construct()
    {
         ;
        // Your own constructor code
        $this->load->library("Aauth");
        
        if (!$this->aauth->is_loggedin()) {
            $this->session->set_flashdata('message_type', 'error');
            $this->session->set_flashdata('messages', 'Please login first.');
            redirect('login');
        }
        
        $this->load->model('Profile_model');
        $this->load->model('kepegawaian/Pegawai_model');
        $this->load->model('Utama_model');
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
    }
    
    public function index(){
        $perms = "edit_profile_by_self";
        $comments = "Edit Profile Oleh Pegawai dengan nip".$this->data['users']->nip."";
        $this->aauth->logit($perms, current_url(), $comments);
        $this->data['bc_parent'] = "Dashboard";
        $this->data['bc_child'] = "Profile";
        $this->data['gd'] = !empty($this->data['pegawai']->gelar_depan) ? $this->data['pegawai']->gelar_depan.". &nbsp;" : "";
        $this->data['gb'] = !empty($this->data['pegawai']->gelar_belakang) ? ", &nbsp;".$this->data['pegawai']->gelar_belakang : "";
    	$this->load->view('profile', $this->data);
    }
    
    public function do_update_bio(){
        $this->load->library('form_validation');
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required|trim');
        
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
            $perms = "edit_profile_by_self";
            $comments = "Gagal update profile dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $id_pegawai = $this->data['pegawai']->id_pegawai;
            $nip = $this->data['pegawai']->nip;
            $nm_lengkap = $this->input->post('nama_lengkap',TRUE);
            $gelar_depan = $this->input->post('gelar_depan',TRUE);
            $gelar_belakang = $this->input->post('gelar_belakang',TRUE);
            $agama = $this->input->post('agama',TRUE);
            $tempat_lahir = $this->input->post('tempat_lahir',true);
            $tgl_lahir = date('Y-m-d',strtotime($this->input->post('tgl_lahir',true)));
            $jenis_kelamin = $this->input->post('jenis_kelamin',true);
            $no_tlp = $this->input->post('no_tlp',true);
            $alamat = $this->input->post('alamat',true);
            $no_ktp = $this->input->post('no_ktp',true);
            $npwp = $this->input->post('no_npwp',true);
            $norek_dki = $this->input->post('no_rek',true);
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
            $data_pegawai = array(
                'nip' => $nip,
                'nama_pegawai' => $nm_lengkap,
                'tempat_lahir' => $tempat_lahir,
                'tgl_lahir' => $tgl_lahir,
                'jenis_kelamin' => $jenis_kelamin,
                'no_tlp' => $no_tlp,
                'alamat' => $alamat,
                'agama' => $agama,
                'no_ktp' => $no_ktp,
                'npwp' => $npwp,
                'norek_dki' => $norek_dki,
                'gelar_depan' => $gelar_depan,
                'gelar_belakang' => $gelar_belakang,
                'updated_by' => $this->data['users']->id,
                'updated_date' => date('Y-m-d H:i:s')
            );
            if (!empty($path)){
                $data_pegawai['foto_url'] = $path;
            }
            $update = $this->Pegawai_model->update_pegawai($data_pegawai, $id_pegawai);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Profil anda berhasil diubah'
                );

                // if permitted, do logit
                $perms = "edit_profile_by_self";
                $comments = "Berhasil mengubah Profile dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal mengubah Profile, hubungi web administrator.');

                // if permitted, do logit
                $perms = "data_pegawai_page";
                $comments = "Gagal mengubah Profile dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function do_update_password(){
        $this->load->library('form_validation');
        $this->form_validation->set_rules('upd_email', 'Email', 'required|trim');
        $this->form_validation->set_rules('upd_username', 'Username', 'required|trim');
        
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
            $perms = "edit_profile_by_self";
            $comments = "Gagal update password profile dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $username_new =  $this->input->post('upd_username',TRUE);
            $username_old =  $this->data['users']->username;
            $email =  $this->input->post('upd_email',TRUE);
            $password =  $this->input->post('upd_password',TRUE);
            if(empty($password)){
                $password = FALSE;
            }
            $id_pegawai = $this->data['pegawai']->id_pegawai;
            
            //update username
            if($username_new != $username_old){
                if($this->aauth->user_exist_by_username($username_new)){
                    $res = array(
                        'csrfTokenName' => $this->security->get_csrf_token_name(),
                        'csrfHash' => $this->security->get_csrf_hash(),
                        'success' => false,
                        'messages' => "Username sudah digunakan");
                    echo json_encode($res);
                    exit;
                }
                $data_user = array(
                    'username' => $username_new,
                    'is_upd_username' => 1
                );
                $this->Profile_model->update_username($data_user, $this->data['users']->id);
            }
            
            //update email dan password pada table aauth_users
            $upd1 = $this->aauth->update_user($this->data['users']->id, $this->data['users']->nama_lengkap, $email, $password);
            
            if($upd1){
                //update email ke tabel m_pegawai
                $data_pegawai = array(
                    'email' => $email
                );
                $update = $this->Pegawai_model->update_pegawai($data_pegawai, $id_pegawai);
            }else{
                $update = FALSE;
            }
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Profil anda berhasil diubah'
                );

                // if permitted, do logit
                $perms = "edit_profile_by_self";
                $comments = "Berhasil mengubah Profil dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
                //redirect('login/do_logout');
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal mengubah Data Pegawai, hubungi web administrator.');

                // if permitted, do logit
                $perms = "edit_profile_by_self";
                $comments = "Gagal mengubah Profil dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function ajax_pendidikan_list(){
        $list = $this->Profile_model->get_pendidikan_list($this->data['pegawai']->id_pegawai);
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        
        foreach($list as $pendidikan){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $pendidikan->pendidikan_nama;
            $row[] = $pendidikan->nama_sekolah;
            $row[] = $pendidikan->tahun_masuk;
            $row[] = $pendidikan->tahun_lulus;
            $row[] = !empty($pendidikan->no_ijazah) ? $pendidikan->no_ijazah : "-";
            if(!empty($pendidikan->ijazah_url)){
                $link = "<a href='".site_url($pendidikan->ijazah_url)."' target='_blank'>link</a>";
            }else{
                $link = "tidak ada ijazah";
            }
            $row[] = $link;
            $edit = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" title="Edit" onclick="editPendidikan('."'".$pendidikan->pendidikan_peg_id."'".')"><i class="fa fa-edit"></i></button>';
            $delete = '<button type="button" class="btn btn-social-icon btn-danger" title="Hapus" onclick="hapusPendidikan('."'".$pendidikan->pendidikan_peg_id."'".')"><i class="fa fa-trash-o"></i></button></div>';
            $row[] = $edit."&nbsp;".$delete;
            
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Profile_model->count_pendidikan_all($this->data['pegawai']->id_pegawai),
                    "recordsFiltered" => $this->Profile_model->count_pendidikan_filtered($this->data['pegawai']->id_pegawai),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    function do_tambah_pendidikan(){
        $this->load->library('form_validation');
        $this->form_validation->set_rules('jenjang', 'Jenjang Pendidikan', 'required|trim');
        $this->form_validation->set_rules('nama_sekolah', 'Nama Sekolah', 'required|trim');
        $this->form_validation->set_rules('tahun_masuk', 'Tahun Masuk', 'required|trim');
        $this->form_validation->set_rules('tahun_lulus', 'Tahun Lulus', 'required|trim');
        $this->form_validation->set_rules('no_ijazah', 'No. Ijazah', 'trim');
        
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
            $perms = "edit_profile_by_self";
            $comments = "Gagal tambah pendidikan dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $id_pegawai = $this->data['pegawai']->id_pegawai;
            $nip = $this->data['pegawai']->nip;
            $jenjang = $this->input->post('jenjang',TRUE);
            $nama_sekolah = $this->input->post('nama_sekolah',TRUE);
            $tahun_masuk = $this->input->post('tahun_masuk',TRUE);
            $tahun_lulus = $this->input->post('tahun_lulus',TRUE);
            $no_ijazah = $this->input->post('no_ijazah',true);
           
            $path = '';
            
            $config['upload_path'] = 'data/scan_dokumen/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = 1024;
            $config['file_name'] = $nip."_ijz_".$jenjang;
            $this->load->library('upload', $config);
            
            if(!empty($_FILES['ijazah_file']['name'])){
                if (!$this->upload->do_upload('ijazah_file')){
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
                    $path = 'data/scan_dokumen/'.$filedata['file_name'];
                }
            }
            $data_pendidikan = array(
                'pegawai_id' => $id_pegawai,
                'jenjang_pendidikan' => $jenjang,
                'nama_sekolah' => $nama_sekolah,
                'tahun_masuk' => $tahun_masuk,
                'tahun_lulus' => $tahun_lulus,
                'no_ijazah' => $no_ijazah
            );
            if (!empty($path)){
                $data_pendidikan['ijazah_url'] = $path;
            }
            $update = $this->Profile_model->insert_pendidikan($data_pendidikan);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Data pendidikan berhasil ditambahkan'
                );

                // if permitted, do logit
                $perms = "edit_profile_by_self";
                $comments = "Berhasil menambah data pendidikan dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menambah data pendidikan, hubungi web administrator.');

                // if permitted, do logit
                $perms = "edit_profile_by_self";
                $comments = "Gagal menambah data pendidikan dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function ajax_get_pendidikan_by_id(){
        $id_pendidikan = $this->input->get('id_pendidikan',TRUE);
        $old_data = $this->Profile_model->get_pendidikan_by_id($id_pendidikan);
        
        if(count ((array)$old_data) > 0) {
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'data' => $old_data
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "Pendidikan pegawai tidak ditemukan");
        }
        echo json_encode($res);
    }
    
    function do_update_pendidikan(){
        $this->load->library('form_validation');
        $this->form_validation->set_rules('upd_jenjang', 'Jenjang Pendidikan', 'required|trim');
        $this->form_validation->set_rules('upd_nama_sekolah', 'Nama Sekolah', 'required|trim');
        $this->form_validation->set_rules('upd_tahun_masuk', 'Tahun Masuk', 'required|trim');
        $this->form_validation->set_rules('upd_tahun_lulus', 'Tahun Lulus', 'required|trim');
        $this->form_validation->set_rules('upd_no_ijazah', 'No. Ijazah', 'trim');
        
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
            $perms = "edit_profile_by_self";
            $comments = "Gagal update pendidikan dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $pendidikan_id = $this->input->post('upd_pendidikan_id',TRUE);
            $nip = $this->data['pegawai']->nip;
            $jenjang = $this->input->post('upd_jenjang',TRUE);
            $nama_sekolah = $this->input->post('upd_nama_sekolah',TRUE);
            $tahun_masuk = $this->input->post('upd_tahun_masuk',TRUE);
            $tahun_lulus = $this->input->post('upd_tahun_lulus',TRUE);
            $no_ijazah = $this->input->post('upd_no_ijazah',true);
           
            $path = '';
            
            $config['upload_path'] = 'data/scan_dokumen/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = 1024;
            $config['file_name'] = $nip."_ijz_".$jenjang;
            $this->load->library('upload', $config);
            
            if(!empty($_FILES['upd_ijazah_file']['name'])){
                if (!$this->upload->do_upload('upd_ijazah_file')){
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
                    $path = 'data/scan_dokumen/'.$filedata['file_name'];
                }
            }
            $data_pendidikan = array(
                'jenjang_pendidikan' => $jenjang,
                'nama_sekolah' => $nama_sekolah,
                'tahun_masuk' => $tahun_masuk,
                'tahun_lulus' => $tahun_lulus,
                'no_ijazah' => $no_ijazah
            );
            if (!empty($path)){
                $data_pendidikan['ijazah_url'] = $path;
            }
            $update = $this->Profile_model->update_pendidikan($data_pendidikan, $pendidikan_id);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Data pendidikan berhasil diubah'
                );

                // if permitted, do logit
                $perms = "edit_profile_by_self";
                $comments = "Berhasil mengubah data pendidikan dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal mengubah data pendidikan, hubungi web administrator.');

                // if permitted, do logit
                $perms = "edit_profile_by_self";
                $comments = "Gagal mengubah data pendidikan dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function do_delete_pendidikan_pegawai(){
        $pendidikan_id = $this->input->post('pendidikan_id', TRUE);
        $delete = $this->Profile_model->delete_pendidikan($pendidikan_id);
        if($delete){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'Pendidikan Pegawai berhasil dihapus'
            );
            // if permitted, do logit
            $perms = "edit_profile_by_self";
            $comments = "Berhasil menghapus pendidikan pegawai dengan id = '". $pendidikan_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menghapus data, silakan hubungi web administrator.'
            );
            // if permitted, do logit
            $perms = "edit_profile_by_self";
            $comments = "Gagal menghapus pendidikan pegawai dengan ID = '". $pendidikan_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }

    public function ajax_pelatihan_list()
    {
        $list = $this->Profile_model->get_pelatihan_list($this->data['pegawai']->id_pegawai);
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;

        foreach ($list as $pelatihan) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $pelatihan->nama_pelatihan;
            $row[] = $pelatihan->penyedia_pelatihan;
            $row[] = $pelatihan->waktu_pelatihan;
            $row[] = !empty($pelatihan->no_sertifikat) ? $pelatihan->no_sertifikat : "-";
            if(!empty($pelatihan->sertifikat_url)){
                $link = "<a href='".site_url($pelatihan->sertifikat_url)."' target='_blank'>link</a>";
            }else{
                $link = "tidak ada ijazah";
            }
            $row[] = $link;
            $edit = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" title="Edit" onclick="editPelatihan('."'".$pelatihan->pelatihan_pegawai_id."'".')"><i class="fa fa-edit"></i></button>';
            $delete = '<button type="button" class="btn btn-social-icon btn-danger" title="Hapus" onclick="hapusPelatihan('."'".$pelatihan->pelatihan_pegawai_id."'".')"><i class="fa fa-trash-o"></i></button></div>';
            $row[] = $edit."&nbsp;".$delete;
            
            $data[] = $row;
        }
        $output = array(
            'draw' => $this->input->get('draw'),
            'recordsTotal' => $this->Profile_model->count_pelatihan_all($this->data['pegawai']->id_pegawai),
            'recordsFiltered' => $this->Profile_model->count_pelatihan_filtered($this->data['pegawai']->id_pegawai),
            'data' => $data,
         );
        echo json_encode($output);
    }


    function do_tambah_pelatihan(){
        $this->load->library('form_validation');
        $this->form_validation->set_rules('nama_pelatihan', 'Nama Pelatihan', 'required|trim');
        $this->form_validation->set_rules('penyedia_pelatihan', 'Penyedia Pelatihan', 'required|trim');
        $this->form_validation->set_rules('waktu_pelatihan', 'Waktu Pelatihan', 'required|trim');
        $this->form_validation->set_rules('no_sertifikat', 'No. Sertifikat', 'trim');
        
        if ($this->form_validation->run() == FALSE)  {
            $error = validation_errors();
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $error);

            // if permitted, do logit
            $perms = "edit_profile_by_self";
            $comments = "Gagal tambah pendidikan dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $id_pegawai = $this->data['pegawai']->id_pegawai;
            $nip = $this->data['pegawai']->nip;
            $pelatihan = $this->input->post('nama_pelatihan',TRUE);
            $penyedia_pelatihan = $this->input->post('penyedia_pelatihan',TRUE);
            $waktu_pelatihan = $this->input->post('waktu_pelatihan',TRUE);
            $no_sertifikat = $this->input->post('no_sertifikat',true);
           
            $path = '';
            
            $config['upload_path'] = 'data/scan_dokumen/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = 1024;
            $config['file_name'] = $nip."_srtf_".$waktu_pelatihan;
            $this->load->library('upload', $config);
            
            if(!empty($_FILES['sertifikat_file']['name'])){
                if (!$this->upload->do_upload('sertifikat_file')){
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
                    $path = 'data/scan_dokumen/'.$filedata['file_name'];
                }
            }
            $data_pelatihan = array(
                'pegawai_id' => $id_pegawai,
                'nama_pelatihan' => $pelatihan,
                'penyedia_pelatihan' => $penyedia_pelatihan,
                'waktu_pelatihan' => $waktu_pelatihan,
                'no_sertifikat' => $no_sertifikat,
            );
            if (!empty($path)){
                $data_pelatihan['sertifikat_url'] = $path;
            }
            $update = $this->Profile_model->insert_pelatihan($data_pelatihan);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Data pelatihan berhasil ditambahkan'
                );

                // if permitted, do logit
                $perms = "edit_profile_by_self";
                $comments = "Berhasil menambah data pelatihan dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menambah data pelatihan, hubungi web administrator.');

                // if permitted, do logit
                $perms = "edit_profile_by_self";
                $comments = "Gagal menambah data pelatihan dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }

    public function ajax_get_pelatihan_by_id(){
        $id_pelatihan = $this->input->get('id_pelatihan',TRUE);
        $old_data = $this->Profile_model->get_pelatihan_by_id($id_pelatihan);

        if(count ((array)$old_data) > 0) {
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'data' => $old_data
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "pelatihan pegawai tidak ditemukan");
        }
        echo json_encode($res);
    }

    function do_update_pelatihan(){
        $this->load->library('form_validation');
        $this->form_validation->set_rules('upd_nama_pelatihan', 'Nama Pelatihan', 'required|trim');
        $this->form_validation->set_rules('upd_penyedia_pelatihan', 'Penyedia Pelatihan', 'required|trim');
        $this->form_validation->set_rules('upd_waktu_pelatihan', 'Waktu Pelatihan', 'required|trim');
        $this->form_validation->set_rules('upd_no_sertifikat', 'No. Sertifikat', 'trim');
        
        if ($this->form_validation->run() == FALSE)  {
            $error = validation_errors();
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $error);

            // if permitted, do logit
            $perms = "edit_profile_by_self";
            $comments = "Gagal update pelatihan dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE){
            $pelatihan_id = $this->input->post('upd_pelatihan_id',TRUE);
            $nip = $this->data['pegawai']->nip;
            $pelatihan = $this->input->post('upd_nama_pelatihan',TRUE);
            $penyedia_pelatihan = $this->input->post('upd_penyedia_pelatihan',TRUE);
            $waktu_pelatihan = $this->input->post('upd_waktu_pelatihan',TRUE);
            $no_sertifikat = $this->input->post('upd_no_sertifikat',true);
           
            $path = '';
            
            $config['upload_path'] = 'data/scan_dokumen/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = 1024;
            $config['file_name'] = $nip."_srtf_".$waktu_pelatihan;
            $this->load->library('upload', $config);
            
            if(!empty($_FILES['upd_sertifikat_file']['name'])){
                if (!$this->upload->do_upload('upd_sertifikat_file')){
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
                    $path = 'data/scan_dokumen/'.$filedata['file_name'];
                }
            }
            $data_pelatihan = array(
                'nama_pelatihan' => $pelatihan,
                'penyedia_pelatihan' => $penyedia_pelatihan,
                'waktu_pelatihan' => $waktu_pelatihan,
                'no_sertifikat' => $no_sertifikat,
            );
            if (!empty($path)){
                $data_pelatihan['sertifikat_url'] = $path;
            }
            $update = $this->Profile_model->update_pelatihan($data_pelatihan, $pelatihan_id);
            //$ins=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Data pelatihan berhasil ditambahkan'
                );

                // if permitted, do logit
                $perms = "edit_profile_by_self";
                $comments = "Berhasil mengubah data pelatihan dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal mengubah data pelatihan, hubungi web administrator.');

                // if permitted, do logit
                $perms = "edit_profile_by_self";
                $comments = "Gagal mengubah data pelatihan dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }

    public function do_delete_pelatihan_pegawai(){
        $pelatihan_id = $this->input->post('pelatihan_id', TRUE);
        $delete = $this->Profile_model->delete_pelatihan($pelatihan_id);
        if($delete){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'pelatihan Pegawai berhasil dihapus'
            );
            // if permitted, do logit
            $perms = "edit_profile_by_self";
            $comments = "Berhasil menghapus pelatihan pegawai dengan id = '". $pelatihan_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menghapus data, silakan hubungi web administrator.'
            );
            // if permitted, do logit
            $perms = "edit_profile_by_self";
            $comments = "Gagal menghapus pelatihan pegawai dengan ID = '". $pelatihan_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
}