<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
        
        foreach($list as $kehadiran){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $kehadiran->nip;
            $row[] = $kehadiran->nama_pegawai;
            $row[] = $kehadiran->izin;
            $row[] = $kehadiran->sakit;
            $row[] = $kehadiran->cuti_or_bersalin;
            $row[] = $kehadiran->dinas_luar;
            $row[] = $kehadiran->tanpa_alasan;
            $row[] = $kehadiran->pulang_cepat_menit;
            $row[] = $kehadiran->terlambat_menit;
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
                    $this->load->library('excel');
                    $filedata = $this->upload->data();
                    $path = 'data/file_excel_absensi/'.$filedata['file_name'];

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
                            $nip = trim($val['C']);
                            $pegawai = $this->Absensi_model->get_pegawai_by_nip($nip);
                            $id_pegawai = isset($pegawai->id_pegawai) ? $pegawai->id_pegawai : '';
                            $izin = $val['E'];
                            $sakit = $val['D'];
                            $cuti = $val['G'];
                            $dinas_luar = 0;
                            $tanpa_alasan = $val['F'];
                            $pulang_cepat = $val['I'];
                            $terlambat = $val['J'];

                            $data_kehadiran = array(
                                'id_pegawai' => $id_pegawai,
                                'bulan' => $bulan,
                                'izin' => $izin,
                                'sakit' => $sakit,
                                'cuti_or_bersalin' => $cuti,
                                'dinas_luar' => $dinas_luar,
                                'tanpa_alasan' => $tanpa_alasan,
                                'pulang_cepat_menit' => $pulang_cepat,
                                'terlambat_menit' => $terlambat
                            );
                            if(!empty($id_pegawai)){
                                $check_constrain = $this->Absensi_model->count_kehadiran_by_bln($bulan, $id_pegawai);
                                if($check_constrain > 0){
                                    $ins = false;
                                }else {
                                    $ins = $this->Absensi_model->insert_kehadiran($data_kehadiran);
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
        $this->form_validation->set_rules('izin', 'Izin', 'required|trim');
        $this->form_validation->set_rules('sakit', 'Sakit', 'required|trim');
        $this->form_validation->set_rules('cuti_or_bersalin', 'Cuti/Bersalin', 'required|trim');
        $this->form_validation->set_rules('tanpa_alasan', 'Tanpa Alasan', 'required|trim');
        $this->form_validation->set_rules('pulang_cepat', 'Pulang Cepat', 'required|trim');
        $this->form_validation->set_rules('terlambat', 'Terlambat', 'required|trim');
        $this->form_validation->set_rules('dinas_luar', 'Dinas Luar', 'required|trim');
        
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
            $izin = $this->input->post('izin', TRUE);
            $sakit = $this->input->post('sakit', TRUE);
            $cuti = $this->input->post('cuti_or_bersalin', TRUE);
            $id_waktukurang = $this->input->post('id_waktukurang', TRUE);
            $tanpa_alasan = $this->input->post('tanpa_alasan', TRUE);
            $pulang_cepat = $this->input->post('pulang_cepat', TRUE);
            $terlambat = $this->input->post('terlambat', TRUE);
            $dinas_luar = $this->input->post('dinas_luar', TRUE);
            
            $data_kehadiran = array(
                'izin' => $izin,
                'sakit' => $sakit,
                'cuti_or_bersalin' => $cuti,
                'tanpa_alasan' => $tanpa_alasan,
                'pulang_cepat_menit' => $pulang_cepat,
                'terlambat_menit' => $terlambat,
                'dinas_luar' => $dinas_luar
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