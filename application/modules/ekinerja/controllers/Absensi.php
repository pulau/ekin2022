<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Absensi extends MY_Controller {
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
        
        $this->load->model('Absensi_model');
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
        $is_permit = $this->aauth->control_no_redirect('absensi_ekinerja_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "E-Kinerja";
        $this->data['bc_child'] = "Absensi";
        //$this->data['serapan'] = $this->Capaian_kinerja_model->get_serapan_bulan(date('M Y'));
        $perms = "absensi_ekinerja_perm";
        $comments = "Halaman Absensi Kinerja";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('absensi', $this->data);
    }
    
    public function ajax_list_absensi(){
        $filter_bln = $this->input->get('filter_bln',TRUE);
        $list = $this->Absensi_model->get_absensi_list($filter_bln);
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        // print_r($list);
        // die;
        foreach($list as $kehadiran){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $kehadiran->nip;
            $row[] = $kehadiran->nama_pegawai;
            $row[] = $kehadiran->tanpa_alasan;
            $row[] = $kehadiran->terlambat_menit;
            $row[] = $kehadiran->pulang_cepat_menit;
            $row[] = $kehadiran->izin;
            $row[] = $kehadiran->sakit;
            $row[] = $kehadiran->cuti_alasan_penting;
            $row[] = $kehadiran->izin_setengah_hari;
            $row[] = $kehadiran->covid;
            $row[] = $kehadiran->ranapc19;
            $row[] = $kehadiran->cuti_tahunan;
            $row[] = $kehadiran->sakit_srt_dokter;
            $row[] = $kehadiran->cuti_bersalin;
            $row[] = $kehadiran->cuti_besar;
            $row[] = $kehadiran->dinas_luar_akhir;
            $row[] = $kehadiran->dinas_luar_awal;
            $row[] = $kehadiran->tidak_terbaca;
            $row[] = $kehadiran->dinas_luar_penuh;
            $row[] = $kehadiran->cuti_sakit;
            $row[] = $kehadiran->cuti_bersalin_ak3;
            $row[] = $kehadiran->cuti_sakit_ranap_rs;
            //add html for action
            $row[] = '<button type="button" class="btn btn-social-icon btn-info" title="Edit" onclick="editKehadiran('."'".$kehadiran->id_waktukurang."',"."'".$kehadiran->nip."',"."'".$kehadiran->nama_pegawai."'".')"><i class="fa fa-edit"></i></button>';
                      //'<a class="btn btn-icon-only red" title="Hapus" onclick="hapusKehadiran('."'".$kehadiran->id_waktukurang."'".')"><i class="glyphicon glyphicon-trash"></i></a>';
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Absensi_model->count_absensi_all($filter_bln),
                    "recordsFiltered" => $this->Absensi_model->count_absensi_filtered($filter_bln),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function do_insert_absensi(){
        $is_permit = $this->aauth->control_no_redirect('absensi_ekinerja_perm');
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
            $perms = "absensi_ekinerja_perm";
            $comments = "Gagal input kehadiran dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE)  {
            $bulan = date('Y-m',strtotime($this->input->post('bulan', TRUE)));
            $config['upload_path'] = 'data/file_excel_absensi/';
            $config['allowed_types'] = 'xls|xlsx';
            //$config['max_size'] = 1024;
            $config['file_name'] = "dataabsensi_".date('YmdHis');
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
                    $perms = "absensi_ekinerja_perm";
                    $comments = $error;
                    $this->aauth->logit($perms, current_url(), $comments);
                }else{
                    //$this->load->library('excel');
                    $filedata = $this->upload->data();
                    $path = 'data/file_excel_absensi/'.$filedata['file_name'];

                    //mulai insert data pegawai
                    $this->db->trans_begin();
                    //read file from path
                    // $objPHPExcel = PHPExcel_IOFactory::load($path);
                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
                    //get only the Cell Collection
                    $worksheet = $spreadsheet->getActiveSheet()->toArray();
                    for ($i=1; $i < count($worksheet) ; $i++) { 
                        $nip = $worksheet[$i]['2'];
                        $pegawai = $this->Absensi_model->get_pegawai_by_nip($nip);
                        $id_pegawai = isset($pegawai->id_pegawai) ? $pegawai->id_pegawai : '';
                        $absent = $worksheet[$i]['3'];
                        $terlambat = $worksheet[$i]['4'];
                        $plg_cepat = $worksheet[$i]['5'];
                        $izin = $worksheet[$i]['6'];
                        $sakit = $worksheet[$i]['7'];
                        $cuti_alasan_penting = $worksheet[$i]['8'];
                        $izin_setengah_hari = $worksheet[$i]['9'];
                        $isoman = $worksheet[$i]['10'];
                        $ranapc19 = $worksheet[$i]['11'];
                        $cuti_tahunan = $worksheet[$i]['12'];
                        $sakit_srt_dokter = $worksheet[$i]['13'];
                        $cuti_bersalin = $worksheet[$i]['14'];
                        $cuti_besar = $worksheet[$i]['15'];
                        $dinas_luar_akhir = $worksheet[$i]['16'];
                        $dinas_luar_awal = $worksheet[$i]['17'];
                        $tidak_terbaca = $worksheet[$i]['18'];
                        $dinas_luar_penuh = $worksheet[$i]['19'];
                        $cuti_sakit = $worksheet[$i]['20'];
                        $cuti_bersalin_ak3 = $worksheet[$i]['21'];
                        $cuti_sakit_ranap_rs = $worksheet[$i]['22'];
                        

                        $data_kehadiran = array(
                            'id_pegawai' => $id_pegawai,
                            'bulan' => $bulan,
                            'tanpa_alasan' => $absent,
                            'terlambat_menit' => $terlambat,
                            'pulang_cepat_menit' => $plg_cepat,
                            'izin' => $izin,
                            'sakit' => $sakit,
                            'cuti_alasan_penting' => $cuti_alasan_penting,
                            'izin_setengah_hari' => $izin_setengah_hari,
                            'covid' => $isoman,
                            'ranapc19' => $ranapc19,
                            'cuti_tahunan' => $cuti_tahunan,
                            'sakit_srt_dokter' => $sakit_srt_dokter,
                            'cuti_bersalin' => $cuti_bersalin,
                            'cuti_besar' => $cuti_besar,
                            'dinas_luar_akhir' => $dinas_luar_akhir,
                            'dinas_luar_awal' => $dinas_luar_awal,
                            'tidak_terbaca' => $tidak_terbaca,
                            'dinas_luar_penuh' => $dinas_luar_penuh,
                            'cuti_sakit' => $cuti_sakit,
                            'cuti_bersalin_ak3' => $cuti_bersalin_ak3,
                            'cuti_sakit_ranap_rs' => $cuti_sakit_ranap_rs
                        );

                        if(!empty($id_pegawai)){
                            $check_constrain = $this->Absensi_model->count_kehadiran_by_bln($bulan, $id_pegawai);
                            if($check_constrain > 0){
                                $ins = false;
                            }else {
                                $ins = $this->Absensi_model->insert_kehadiran($data_kehadiran);
                                // print_r($data_kehadiran);
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
                        $perms = "absensi_ekinerja_perm";
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
                        //cuti_sakit_ranap
                        // if permitted, do logit
                        $perms = "absensi_ekinerja_perm";
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
                $perms = "absensi_ekinerja_perm";
                $comments = $error;
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function do_update_absensi(){
        $is_permit = $this->aauth->control_no_redirect('absensi_ekinerja_perm');
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
        $this->form_validation->set_rules('tanpa_alasan', 'Tanpa Alasan', 'required|trim');
        $this->form_validation->set_rules('terlambat_menit', 'terlambat menit', 'required|trim');
        $this->form_validation->set_rules('pulang_cepat_menit', 'pulang cepat menit', 'required|trim');
        $this->form_validation->set_rules('izin', 'izin', 'required|trim');
        $this->form_validation->set_rules('sakit', 'sakit', 'required|trim');
        $this->form_validation->set_rules('cuti_alasan_penting', 'cuti_alasan_penting', 'required|trim');
        $this->form_validation->set_rules('izin_setengah_hari', 'izin_setengah_hari', 'required|trim');
        $this->form_validation->set_rules('covid', 'covid', 'required|trim');
        $this->form_validation->set_rules('ranapc19', 'ranapc19', 'required|trim');
        $this->form_validation->set_rules('cuti_tahunan', 'cuti_tahunan', 'required|trim');
        $this->form_validation->set_rules('sakit_srt_dokter', 'sakit_srt_dokter', 'required|trim');
        $this->form_validation->set_rules('cuti_bersalin', 'cuti_bersalin', 'required|trim');
        $this->form_validation->set_rules('cuti_besar', 'cuti_besar', 'required|trim');
        $this->form_validation->set_rules('dinas_luar_akhir', 'dinas_luar_akhir', 'required|trim');
        $this->form_validation->set_rules('dinas_luar_awal', 'dinas_luar_awal', 'required|trim');
        $this->form_validation->set_rules('tidak_terbaca', 'tidak_terbaca', 'required|trim');
        $this->form_validation->set_rules('dinas_luar_penuh', 'dinas_luar_penuh', 'required|trim');
        $this->form_validation->set_rules('cuti_sakit', 'cuti_sakit', 'required|trim');
        $this->form_validation->set_rules('cuti_bersalin_ak3', 'cuti_bersalin_ak3', 'required|trim');
        $this->form_validation->set_rules('cuti_sakit_ranap_rs', 'cuti sakit rawat inap RS', 'required|trim');
        
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
            $perms = "absensi_ekinerja_perm";
            $comments = "Gagal update kehadiran dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);

        }else if ($this->form_validation->run() == TRUE)  {
            
            $id_waktukurang = $this->input->post('id_waktukurang', TRUE);
            $absent = $this->input->post('tanpa_alasan', TRUE);
            $terlambat = $this->input->post('terlambat_menit', TRUE);
            $plg_cepat = $this->input->post('pulang_cepat_menit', TRUE);
            $izin = $this->input->post('izin', TRUE);
            $sakit = $this->input->post('sakit', TRUE);
            $cuti_alasan_penting = $this->input->post('cuti_alasan_penting', TRUE);
            $izin_setengah_hari = $this->input->post('izin_setengah_hari', TRUE);
            $isoman = $this->input->post('covid', TRUE);
            $ranapc19 = $this->input->post('ranapc19', TRUE);
            $cuti_tahunan = $this->input->post('cuti_tahunan', TRUE);
            $sakit_srt_dokter = $this->input->post('sakit_srt_dokter', TRUE);
            $cuti_bersalin = $this->input->post('cuti_bersalin', TRUE);
            $cuti_besar = $this->input->post('cuti_besar', TRUE);
            $dinas_luar_akhir = $this->input->post('dinas_luar_akhir', TRUE);
            $dinas_luar_awal = $this->input->post('dinas_luar_awal', TRUE);
            $tidak_terbaca = $this->input->post('tidak_terbaca', TRUE);
            $dinas_luar_penuh = $this->input->post('dinas_luar_penuh', TRUE);
            $cuti_sakit = $this->input->post('cuti_sakit', TRUE);
            $cuti_bersalin_ak3 = $this->input->post('cuti_bersalin_ak3', TRUE);
            $cuti_sakit_ranap_rs = $this->input->post('cuti_sakit_ranap_rs', TRUE);
            
            $data_kehadiran = array(
                'tanpa_alasan' => $absent,
                'terlambat_menit' => $terlambat,
                'pulang_cepat_menit' => $plg_cepat,
                'izin' => $izin,
                'sakit' => $sakit,
                'cuti_alasan_penting' => $cuti_alasan_penting,
                'izin_setengah_hari' => $izin_setengah_hari,
                'covid' => $isoman,
                'ranapc19' => $ranapc19,
                'cuti_tahunan' => $cuti_tahunan,
                'sakit_srt_dokter' => $sakit_srt_dokter,
                'cuti_bersalin' => $cuti_bersalin,
                'cuti_besar' => $cuti_besar,
                'dinas_luar_akhir' => $dinas_luar_akhir,
                'dinas_luar_awal' => $dinas_luar_awal,
                'tidak_terbaca' => $tidak_terbaca,
                'dinas_luar_penuh' => $dinas_luar_penuh,
                'cuti_sakit' => $cuti_sakit,
                'cuti_bersalin_ak3' => $cuti_bersalin_ak3,
                'cuti_sakit_ranap_rs' => $cuti_sakit_ranap_rs
            );
         
            $ins = $this->Absensi_model->update_kehadiran($data_kehadiran, $id_waktukurang);
            //$ins=true;
            if($ins){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Berhasil Update Absensi'
                );

                // if permitted, do logit
                $perms = "absensi_ekinerja_perm";
                $comments = "Berhasil Update Absensi dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal Update Absensi');

                // if permitted, do logit
                $perms = "absensi_ekinerja_perm";
                $comments = "Gagal Update Absensi dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function ajax_get_kehadiran_by_id(){
        $id_waktukurang = $this->input->get('id_waktukurang',TRUE);
        $old_data = $this->Absensi_model->get_kehadiran_by_id($id_waktukurang);
        
        if(count((array)$old_data) > 0) {
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'data' => $old_data,
                'bulan' => date('M Y',strtotime($old_data->bulan))
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "Waktu kerja tidak ditemukan");
        }
        echo json_encode($res);
    }
}