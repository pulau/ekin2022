<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengajuan_cuti extends MY_Controller {
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
        
        $this->load->model('Pengajuan_cuti_model');
        $this->load->model('Utama_model');
        $this->data['modul'] = $this->aauth->get_module_id($this->uri->segment(1));
        $this->data['users']                = $this->aauth->get_user();
        $this->data['groups']               = $this->aauth->get_user_groups();
        $this->data['pegawai'] = $this->Utama_model->get_pegawai_by_nip($this->data['users']->nip);
        $bagian = !empty($this->data['pegawai']) ? $this->data['pegawai']->bagian : "";
        $this->data['bagian'] = $this->Utama_model->get_bagian_by_id($bagian);
        //$this->data['pegawai'] = $this->Pengajuan_cuti_model->get_pegawai_by_nip($this->data['users']->nip);
        $this->data['sisa_cuti'] = $this->Pengajuan_cuti_model->get_sisa_cuti($this->data['pegawai']->id_pegawai,$this->data['pegawai']->tempat_tugas_ket);
        //$this->data['bagian'] = $this->Pengajuan_cuti_model->get_bagian_by_id($this->data['pegawai']->bagian);
        $groups = "";
        foreach ($this->data['groups'] as $key => $val){
            $groups .= $val->group_id.","; 
        }
        $this->data['group_arr'] = substr_replace($groups, "", -1);
        $this->data['menu_list'] = $this->Utama_model->get_list_menu($this->data['group_arr'], $this->data['modul']);
    }
    
    function index()
    {
        $is_permit = $this->aauth->control_no_redirect('pengajuan_cuti_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "E-Cuti";
        $this->data['bc_child'] = "Pengajuan Cuti";
        $perms = "pengajuan_cuti_perm";
        $comments = "Halaman Pengajuan Cuti";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('pengajuan_cuti', $this->data);
    }
    
    public function ajax_list_pengajuan(){
        $filter_bln = $this->input->get('filter_bln',TRUE);
        $list = $this->Pengajuan_cuti_model->get_pengajuan_list($this->data['users']->nip, $filter_bln);
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        
        foreach($list as $pengajuan){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $pengajuan->jeniscuti_nama;
            $row[] = date('d-M-Y H:i:s', strtotime($pengajuan->tgl_pengajuan));
            $row[] = date('d-M-Y',strtotime($pengajuan->tgl_awal))." s/d ".date('d-M-Y',strtotime($pengajuan->tgl_akhir));
            $row[] = $pengajuan->alasan;
            $row[] = $pengajuan->pengganti_nama;
//            $row[] = $pengajuan->review_status == 0 ? "Belum Direview" : "Sudah Direview";
            $row[] = ($pengajuan->approval_status == 0 ? "Belum Disetujui" : ($pengajuan->approval_status = 1 ? "Disetujui" : "Ditolak"));
            //add html for action
            if($pengajuan->review_status == 0 && $pengajuan->approval_status == 0){
                $row[] = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" title="Edit" onclick="editPengajuan('."'".$pengajuan->cuti_id."'".')"><i class="fa fa-edit"></i></button>
                        <button type="button" class="btn btn-social-icon btn-danger" title="Hapus" onclick="deleteCuti('."'".$pengajuan->cuti_id."'".')"><i class="fa fa-trash-o"></i></button></div>';
            }else{
                $row[] = '<div class="text-center"><button type="button" class="btn btn-social-icon btn-info" title="Edit" disabled><i class="fa fa-edit"></i></button>
                        <button type="button" class="btn btn-social-icon btn-danger" title="Hapus" disabled><i class="fa fa-trash-o"></i></button></div>';
            }
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Pengajuan_cuti_model->count_pengajuan_all($this->data['users']->nip, $filter_bln),
                    "recordsFiltered" => $this->Pengajuan_cuti_model->count_pengajuan_filtered($this->data['users']->nip, $filter_bln),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function hitung_hari(){
        $tgl_awal = date('Y-m-d',strtotime($this->input->get('tgl_awal',TRUE)));
        $tgl_akhir = date('Y-m-d',strtotime($this->input->get('tgl_akhir',TRUE)));
        $date1 = new DateTime($tgl_awal);
        $date2 = new DateTime($tgl_akhir);
        $interval = $date1->diff($date2);
        $hari = $interval->d;
        
        $res = array(
            "jml_hari" => $hari,
            "success" => true
        );
        
        echo json_encode($res);
    }
    
    public function hitung_hari_tanpa_weekend(){
        $startDate = $this->input->get('tgl_awal',TRUE);
        $endDate = $this->input->get('tgl_akhir',TRUE);
        $begin = strtotime($startDate);
        $end   = strtotime($endDate);
        if ($begin > $end) {
            $jml_days = 0;
        } else {
            $no_days  = 0;
            while ($begin <= $end) {
                $what_day = date("N", $begin);
                if (!in_array($what_day, [6,7]) ) // 6 and 7 are weekend
                    $no_days++;
                $begin += 86400; // +1 day
            };

            $jml_days = $no_days;
        }
        
        $res = array(
            "jml_hari" => $jml_days,
            "success" => true
        );
        
         echo json_encode($res);
    }
    
    public function hitung_hari_explode(){
        $tgl_cuti = $this->input->get('tgl_cuti',TRUE);
        $date = explode(',',$tgl_cuti);
        $hari = count($date);
        $start_date = $date[0];
        $end_date = end($date);
        
        $res = array(
            "jml_hari" => $hari,
            "tgl_awal" => $start_date,
            "tgl_akhir" => $end_date,
            "success" => true
        );
        
        echo json_encode($res);
    }
    
    public function do_insert_cuti(){
        $is_permit = $this->aauth->control_no_redirect('pengajuan_cuti_perm');
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
        //$this->form_validation->set_rules('nama_pegawai', 'Nama Pegawai', 'required|trim');
        $this->form_validation->set_rules('bagian_id', 'Bagian', 'required|trim');
        $this->form_validation->set_rules('jenis_cuti', 'Jenis Cuti', 'required|trim');
        $this->form_validation->set_rules('tgl_awal', 'Tanggal Rencana Cuti', 'required|trim');
        $this->form_validation->set_rules('tgl_akhir', 'Tanggal Rencana Cuti', 'required|trim');
        $this->form_validation->set_rules('tgl_cuti', 'Tanggal Rencana Cuti', 'required|trim');
        $this->form_validation->set_rules('jml_hari', 'Jumlah Hari', 'required|trim');
        $this->form_validation->set_rules('pengganti', 'Pengganti', 'required|trim');
        $this->form_validation->set_rules('alasan', 'Alasan', 'required|trim');
        
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
            $perms = "pengajuan_cuti_perm";
            $comments = "Gagal mengajukan Cuti dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE)  {
            //apabila diinput dari halaman review cuti
            $input_by_reviewer = $this->input->post('input_by_review',TRUE);
            if(isset($input_by_reviewer)){
                $stat_value = 1;
            }else{
                $stat_value = 0;
            }
            
            $data_cuti = array(
                'pegawai_id' => $this->input->post('pegawai_id',TRUE),
                'bagian_id' => $this->input->post('bagian_id',TRUE),
                'jenis_cuti' => $this->input->post('jenis_cuti',TRUE),
                'ket_cuti' => $this->input->post('ket_cuti',TRUE),
                'tgl_awal' => date('Y-m-d',strtotime($this->input->post('tgl_awal',TRUE))),
                'tgl_akhir' => date('Y-m-d',strtotime($this->input->post('tgl_akhir',TRUE))),
                'jml_hari' => $this->input->post('jml_hari',TRUE),
                'no_tlp' => $this->input->post('no_tlp',TRUE),
                'kota_cuti' => $this->input->post('kota_cuti',TRUE),
                'alasan' => $this->input->post('alasan',TRUE),
                'kordinator_id' => $this->input->post('kordinator',TRUE),
                'pengganti_id' => $this->input->post('pengganti',TRUE),
                'tgl_pengajuan' => date('Y-m-d H:i:s'),
                'review_status' => $stat_value,
                'approval_status' => $stat_value,
                'approve_by_pj' => $stat_value,
                'approve_by_tu' => $stat_value
            );
            
            $ins = $this->Pengajuan_cuti_model->insert_cuti($data_cuti);
            //$ins=true;
            if($ins){
                $ins_detail = $this->insert_detail_cuti($ins, $this->input->post('tgl_cuti',TRUE));
                if($ins_detail){                
                    $res = array(
                        'csrfTokenName' => $this->security->get_csrf_token_name(),
                        'csrfHash' => $this->security->get_csrf_hash(),
                        'success' => true,
                        'messages' => 'Data Cuti berhasil ditambahkan'
                    );

                    // if permitted, do logit
                    $perms = "pengajuan_cuti_perm";
                    $comments = "Berhasil mengajukan cuti baru dengan data berikut = '". json_encode($_REQUEST) ."'.";
                    $this->aauth->logit($perms, current_url(), $comments);
                }else{
                    $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => false,
                    'messages' => 'Gagal mengajukan cuti, hubungi web administrator.');

                    // if permitted, do logit
                    $perms = "pengajuan_cuti_perm";
                    $comments = "Gagal mengajukan cuti dengan data berikut = '". json_encode($_REQUEST) ."'.";
                    $this->aauth->logit($perms, current_url(), $comments);
                }
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal mengajukan cuti, hubungi web administrator.');

                // if permitted, do logit
                $perms = "pengajuan_cuti_perm";
                $comments = "Gagal mengajukan cuti dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function insert_detail_cuti($cuti_id, $tgl_cuti){
        $tgl_cuti = explode(',', $tgl_cuti);
        $status = false;
        if(count($tgl_cuti) > 0){
            foreach ($tgl_cuti as $tgl){
                $detail_cuti = array(
                    "cuti_id" => $cuti_id,
                    "tgl_cuti" => date('Y-m-d',strtotime($tgl))
                );
                $ins = $this->Pengajuan_cuti_model->insert_cuti_detail($detail_cuti);
                if($ins){
                    $status = true;
                }
            }
        }else{
            $status = false;
        }
        
        return $status;
    }
    
    public function ajax_get_cuti_by_id(){
        $cuti_id = $this->input->get('cuti_id',TRUE);
        $old_data = $this->Pengajuan_cuti_model->get_cuti_by_id($cuti_id);
   
        if(count((array)$old_data) > 0) {
            $tgl_awal = date('d-m-Y',strtotime($old_data->tgl_awal));
            $tgl_akhir = date('d-m-Y',strtotime($old_data->tgl_akhir));
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'data' => $old_data,
                'tgl_awal' => $tgl_awal,
                'tgl_akhir' => $tgl_akhir
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "Cuti ID tidak ditemukan");
        }
        echo json_encode($res);
    }
    
    public function do_update_cuti(){
        $is_permit = $this->aauth->control_no_redirect('pengajuan_cuti_perm');
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
        $this->form_validation->set_rules('pegawai_id', 'Nama Pegawai', 'required|trim');
        $this->form_validation->set_rules('bagian_id', 'Bagian', 'required|trim');
        $this->form_validation->set_rules('jenis_cuti', 'Jenis Cuti', 'required|trim');
        $this->form_validation->set_rules('tgl_awal', 'Tanggal Rencana Cuti', 'required|trim');
        $this->form_validation->set_rules('tgl_akhir', 'Tanggal Rencana Cuti', 'required|trim');
        $this->form_validation->set_rules('jml_hari', 'Jumlah Hari', 'required|trim');
        $this->form_validation->set_rules('pengganti', 'Pengganti', 'required|trim');
        $this->form_validation->set_rules('alasan', 'Alasan', 'required|trim');
        
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
            $perms = "pengajuan_cuti_perm";
            $comments = "Gagal Meng-update Pengajuan Cuti dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE)  {
            $cuti_id = $this->input->post('cuti_id',TRUE);
            
            $data_cuti = array(
                'pegawai_id' => $this->input->post('pegawai_id',TRUE),
                'bagian_id' => $this->input->post('bagian_id',TRUE),
                'jenis_cuti' => $this->input->post('jenis_cuti',TRUE),
                'ket_cuti' => $this->input->post('ket_cuti',TRUE),
                'tgl_awal' => date('Y-m-d',strtotime($this->input->post('tgl_awal',TRUE))),
                'tgl_akhir' => date('Y-m-d',strtotime($this->input->post('tgl_akhir',TRUE))),
                'jml_hari' => $this->input->post('jml_hari',TRUE),
                'no_tlp' => $this->input->post('no_tlp',TRUE),
                'kota_cuti' => $this->input->post('kota_cuti',TRUE),
                'alasan' => $this->input->post('alasan',TRUE),
                'kordinator_id' => $this->input->post('kordinator',TRUE),
                'pengganti_id' => $this->input->post('pengganti',TRUE),
                'tgl_pengajuan' => date('Y-m-d H:i:s'),
                'review_status' => 0,
                'approval_status' => 0
            );
            
            $update = $this->Pengajuan_cuti_model->update_cuti($data_cuti, $cuti_id);
            //$update=true;
            if($update){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Pengajuan Cuti berhasil diubah'
                );

                // if permitted, do logit
                $perms = "pengajuan_cuti_perm";
                $comments = "Berhasil mengubah pengajuan cuti dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal mengubah pengajuan cuti, hubungi web administrator.');

                // if permitted, do logit
                $perms = "pengajuan_cuti_perm";
                $comments = "Gagal mengubah pengajuan cuti dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function do_delete_cuti(){
        $is_permit = $this->aauth->control_no_redirect('pengajuan_cuti_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $cuti_id = $this->input->post('cuti_id', TRUE);

        $delete = $this->Pengajuan_cuti_model->delete_cuti($cuti_id);
        if($delete){
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => true,
                'messages' => 'Data cuti berhasil dihapus'
            );
            // if permitted, do logit
            $perms = "pengajuan_cuti_perm";
            $comments = "Berhasil menghapus Cuti dengan id = '". $cuti_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal menghapus data, silakan hubungi web administrator.'
            );
            // if permitted, do logit
            $perms = "pengajuan_cuti_perm";
            $comments = "Gagal menghapus data dengan ID = '". $cuti_id ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
}