<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Input_aktifitas extends MY_Controller {
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
        
        $this->load->model('Input_aktifitas_model');
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
        $is_permit = $this->aauth->control_no_redirect('input_aktifitas_ekinerja_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "E-Kinerja";
        $this->data['bc_child'] = "Input Aktifitas";
        $perms = "input_aktifitas_ekinerja_perm";
        $comments = "Halaman E-Kinerja Input Aktifitas";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('input_aktifitas', $this->data);
    }
    
    public function ajax_list_aktifitas(){
        $filter_tgl = $this->input->get('filter_tgl',TRUE);
        $list = $this->Input_aktifitas_model->get_aktifitas_list($this->data['pegawai']->id_pegawai, $filter_tgl);
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        
        foreach($list as $aktifitas){
            $no++;
            $row = array();
            $row[] = "Aktifitas :".$aktifitas->skp."<br>Uraian :".$aktifitas->uraian."<br>".$aktifitas->jam_mulai." - ".$aktifitas->jam_akhir;
            //$row[] = $aktifitas->is_validasi == 0 ? "<span class='label label-sm label-warning'>Belum Validasi</span>" : "<span class='label label-sm label-info'>Valid</span>";
            $row[] = $aktifitas->is_validasi == 0 ? "<span class='label label-sm label-warning'>Belum Validasi</span>" : ($aktifitas->is_validasi == 2 ? "<span class='label label-sm label-danger'>Ditolak</span>" : "<span class='label label-sm label-info'>Valid</span>");
            $row[] = $aktifitas->waktu_efektif;
            $row[] = $aktifitas->jumlah;
            //$row[] = $aktifitas->point;
            //add html for action
            if($aktifitas->is_validasi == 0 || $aktifitas->is_validasi == 2){
                $edit = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" title="Edit" onclick="editAktifitas('."'".$aktifitas->aktifitas_id."'".')"><i class="fa fa-edit"></i></button>';
                $delete = '<button type="button" class="btn btn-social-icon btn-danger" title="Hapus" onclick="deleteAktifitas('."'".$aktifitas->aktifitas_id."'".')"><i class="fa fa-trash-o"></i></button></div>';
            }else{
                $edit = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" title="Edit" disabled><i class="fa fa-edit"></i></button>';
                $delete = '<button type="button" class="btn btn-social-icon btn-danger" title="Hapus" disabled><i class="fa fa-trash-o"></i></button></div>';
            }
            $row[] = $edit."&nbsp;".$delete;
            
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Input_aktifitas_model->count_aktifitas_all($this->data['pegawai']->id_pegawai, $filter_tgl),
                    "recordsFiltered" => $this->Input_aktifitas_model->count_aktifitas_filtered($this->data['pegawai']->id_pegawai, $filter_tgl),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function ajax_get_skpt_by_id(){
        $skpt_id = $this->input->get('skpt_id', TRUE);
        $skpt_old = $this->Input_aktifitas_model->get_skpt_by_id($skpt_id);
        
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
    
    public function hitung_volume(){
        $starttime = $this->input->get('jam_mulai',TRUE);
        $endtime = $this->input->get('jam_selesai',TRUE);
        $waktu_efektif = $this->input->get('waktu_efektif',TRUE);
        $begin = strtotime($starttime);
        $end   = strtotime($endtime);
        if ($begin > $end) {
            $jml_menit = 0;
        } else {
            $jml_menit  = round(abs($end - $begin) / 60,2);
        }
        
        $volume = floor($jml_menit/$waktu_efektif);
        
        $list_volume = "<option value=0> 0 </option>";
        if($volume > 0){
            for($x = 1; $x <= $volume; $x++){
                $list_volume .= "<option value=".$x."> ".$x." </option>";
            }
        }
        $res = array(
            "list_volume" => $list_volume,
            "success" => true
        );
        
         echo json_encode($res);
    }
    
    public function do_insert_aktifitas(){
        $is_permit = $this->aauth->control_no_redirect('input_aktifitas_ekinerja_perm');
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
        $this->form_validation->set_rules('tgl_aktifitas', 'Tanggal Aktifitas', 'required|trim');
        $this->form_validation->set_rules('skptahunan_id', 'Aktifitas', 'required|trim');
        $this->form_validation->set_rules('waktu_efektif', 'Waktu Efektif', 'required|trim');
        $this->form_validation->set_rules('jam_mulai', 'Jam Mulai', 'required|trim');
        $this->form_validation->set_rules('jam_selesai', 'Jam Selesai', 'required|trim');
        $this->form_validation->set_rules('jumlah', 'Volume', 'required|trim');
        
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
            $perms = "input_aktifitas_ekinerja_perm";
            $comments = "Gagal Menambahkan Aktifitas dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE)  {
            $jam_mulai = $this->input->post('jam_mulai',TRUE);
            $jam_selesai = $this->input->post('jam_selesai',TRUE);
            $tgl_aktifitas = $this->input->post('tgl_aktifitas',TRUE);
            $data_aktifitas = array(
                'id_pegawai' => $this->input->post('id_pegawai',TRUE),
                'skptahunan_id' => $this->input->post('skptahunan_id',TRUE),
                'waktu_efektif' => $this->input->post('waktu_efektif',TRUE),
                'uraian' => $this->input->post('uraian',TRUE),
                'jam_mulai' => date('H:i:s', strtotime($jam_mulai)),
                'jam_akhir' => date('H:i:s', strtotime($jam_selesai)),
                'jumlah' => $this->input->post('jumlah',TRUE),
                'tanggal_aktifitas' => date('Y-m-d', strtotime($tgl_aktifitas)),
                'point' => 0,
                'is_validasi' => 0,
                'create_date' => date('Y-m-d H:i:s')
            );
            
            $ins = $this->Input_aktifitas_model->insert_aktifitas($data_aktifitas);
            //$ins=true;
            if($ins){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Aktifitas berhasil ditambahkan'
                );

                // if permitted, do logit
                $perms = "input_aktifitas_ekinerja_perm";
                $comments = "Berhasil menambahkan Aktifitas dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menambahkan Aktifitas, hubungi web administrator.');

                // if permitted, do logit
                $perms = "input_aktifitas_ekinerja_perm";
                $comments = "Gagal menambahkan Aktifitas dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function ajax_get_aktifitas_by_id(){
        $is_permit = $this->aauth->control_no_redirect('input_aktifitas_ekinerja_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $aktifitas_id = $this->input->get('aktifitas_id', TRUE);
        $aktifitas_old = $this->Input_aktifitas_model->get_aktifitas_by_id($aktifitas_id);
        
        if(count((array)$aktifitas_old) > 0) {
            $begin = strtotime($aktifitas_old->jam_mulai);
            $end   = strtotime($aktifitas_old->jam_akhir);
            if ($begin > $end) {
                $jml_menit = 0;
            } else {
                $jml_menit  = round(abs($end - $begin) / 60,2);
            }

            $volume = floor($jml_menit/$aktifitas_old->waktu_efektif);

            $list_volume = "<option value=0> 0 </option>";
            if($volume > 0){
                for($x = 1; $x <= $volume; $x++){
                    $list_volume .= "<option value=".$x."> ".$x." </option>";
                }
            }
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'data' => $aktifitas_old,
                'tgl_aktifitas' => date('d-m-Y', strtotime($aktifitas_old->tanggal_aktifitas)),
                'jam_mulai' => date('H:i', strtotime($aktifitas_old->jam_mulai)),
                'jam_akhir' => date('H:i', strtotime($aktifitas_old->jam_akhir)),
                'list_volume' => $list_volume
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "Aktifitas tidak ditemukan");
        }
        echo json_encode($res);
    }
    
    public function do_update_aktifitas(){
        $is_permit = $this->aauth->control_no_redirect('input_aktifitas_ekinerja_perm');
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
        $this->form_validation->set_rules('upd_tgl_aktifitas', 'Tanggal Aktifitas', 'required|trim');
        $this->form_validation->set_rules('upd_skptahunan_id', 'Aktifitas', 'required|trim');
        $this->form_validation->set_rules('upd_waktu_efektif', 'Waktu Efektif', 'required|trim');
        $this->form_validation->set_rules('upd_jam_mulai', 'Jam Mulai', 'required|trim');
        $this->form_validation->set_rules('upd_jam_selesai', 'Jam Selesai', 'required|trim');
        $this->form_validation->set_rules('upd_jumlah', 'Volume', 'required|trim');
        
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
            $perms = "input_aktifitas_ekinerja_perm";
            $comments = "Gagal Meng-update Aktifitas dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE)  {
            $aktifitas_id = $this->input->post('upd_aktifitas_id',TRUE);
            $jam_mulai = $this->input->post('upd_jam_mulai',TRUE);
            $jam_selesai = $this->input->post('upd_jam_selesai',TRUE);
            $tgl_aktifitas = $this->input->post('upd_tgl_aktifitas',TRUE);
            $data_aktifitas = array(
                'skptahunan_id' => $this->input->post('upd_skptahunan_id',TRUE),
                'waktu_efektif' => $this->input->post('upd_waktu_efektif',TRUE),
                'uraian' => $this->input->post('upd_uraian',TRUE),
                'jam_mulai' => date('H:i:s', strtotime($jam_mulai)),
                'jam_akhir' => date('H:i:s', strtotime($jam_selesai)),
                'jumlah' => $this->input->post('upd_jumlah',TRUE),
                'tanggal_aktifitas' => date('Y-m-d', strtotime($tgl_aktifitas)),
                'point' => 0,
                'is_validasi' => 0,
                'update_date' => date('Y-m-d H:i:s')
            );
            
            $update = $this->Input_aktifitas_model->update_aktifitas($data_aktifitas, $aktifitas_id);
            //$update=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Aktifitas berhasil diubah'
                );

                // if permitted, do logit
                $perms = "input_aktifitas_ekinerja_perm";
                $comments = "Berhasil mengubah aktifitas dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal mengubah aktifitas, hubungi web administrator.');

                // if permitted, do logit
                $perms = "input_aktifitas_ekinerja_perm";
                $comments = "Gagal mengubah aktifitas dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function do_delete_aktifitas(){
        $is_permit = $this->aauth->control_no_redirect('input_aktifitas_ekinerja_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $aktifitas_id = $this->input->post('aktifitas_id', TRUE);
//        $check_constrain = $this->Input_aktifitas_model->check_constraint_skpt($skptahunan_id);
//        if($check_constrain > 0){
//            $res = array(
//                'csrfTokenName' => $this->security->get_csrf_token_name(),
//                'csrfHash' => $this->security->get_csrf_hash(),
//                'success' => false,
//                'messages' => 'Data sedang digunakan di tabel lain');
//            echo json_encode($res);
//            exit;
//        }
        $delete = $this->Input_aktifitas_model->delete_aktifitas($aktifitas_id);
        if($delete){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'Aktifitas berhasil dihapus'
            );
            // if permitted, do logit
            $perms = "input_aktifitas_ekinerja_perm";
            $comments = "Berhasil menghapus Aktifitas dengan id = '". $aktifitas_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menghapus data, silakan hubungi web administrator.'
            );
            // if permitted, do logit
            $perms = "input_aktifitas_ekinerja_perm";
            $comments = "Gagal menghapus data dengan ID = '". $aktifitas_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
}