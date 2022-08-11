<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Validasi_kinerja extends MY_Controller {
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
        
        $this->load->model('Validasi_kinerja_model');
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
        $is_permit = $this->aauth->control_no_redirect('validasi_kinerja_ekinerja_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "E-Kinerja";
        $this->data['bc_child'] = "Validasi Kinerja";
        $perms = "validasi_kinerja_ekinerja_perm";
        $comments = "Halaman Validasi Kinerja Harian";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('validasi_kinerja', $this->data);
    }
    
    public function ajax_list_validasi_aktifitas(){
        $filter_bln = $this->input->get('filter_bln',TRUE);
        $list = $this->Validasi_kinerja_model->get_validasi_aktifitas_list($filter_bln, $this->data['pegawai']->id_pegawai);
        if(empty($filter_bln)){
            $curr_month = date('Y-m');
        }else{
            $curr_month = date('Y-m', strtotime($filter_bln));
        }
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        foreach($list as $aktifitas){
            $no++;
            $jml_aktifitas = $this->Validasi_kinerja_model->count_jml_aktifitas($aktifitas->id_pegawai, $filter_bln);
            $row = array();
            $row[] = $no;
            $row[] = $aktifitas->nip;
            $row[] = $aktifitas->nama_pegawai;
            $row[] = $jml_aktifitas; //$aktifitas->jml_aktifitas;
            $row[] = $this->Validasi_kinerja_model->count_sudah_validasi($aktifitas->id_pegawai, $filter_bln);;
            $row[] = $this->Validasi_kinerja_model->count_blm_validasi($aktifitas->id_pegawai, $filter_bln);;
            $row[] = $this->Validasi_kinerja_model->count_aktifitas_ditolak($aktifitas->id_pegawai, $filter_bln);;
            //add html for action
            if($jml_aktifitas == 0){
                $row[] = '<button class="btn btn-success" disabled><i class="fa fa-edit"></i> Validasi</button>';
            }else{
                $row[] = '<button class="btn btn-success" title="Validasi" onclick="listAktifitas('."'".$aktifitas->id_pegawai."',"."'".$curr_month."',"."'".$aktifitas->nama_pegawai."'".')"><i class="fa fa-edit"></i> Validasi</button>';
            }
            
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Validasi_kinerja_model->count_validasi_aktifitas_all($filter_bln, $this->data['pegawai']->id_pegawai),
                    "recordsFiltered" => $this->Validasi_kinerja_model->count_validasi_aktifitas_filtered($filter_bln, $this->data['pegawai']->id_pegawai),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function ajax_list_validasi_prilaku(){
        $filter_bln = $this->input->get('filter_bln',TRUE);
        $list = $this->Validasi_kinerja_model->get_validasi_prilaku_list($filter_bln, $this->data['pegawai']->id_pegawai);
        if(empty($filter_bln)){
            $curr_month = date('Ym');
        }else{
            $curr_month = date('Ym', strtotime($filter_bln));
        }
        $month = date('Ym');
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        
        foreach($list as $prilaku){
            $no++;
            $row = array();
            $total_nilai = $prilaku->total_nilai == null ? 0 : ceil($prilaku->total_nilai);
            $rata_rata = $prilaku->rata_rata == null ? 0 : number_format($prilaku->rata_rata,2);
            $persen_nilai = $prilaku->persen_nilai == null ? 0 : number_format($prilaku->persen_nilai,2);
            $row[] = $no;
            $row[] = $prilaku->nip;
            $row[] = $prilaku->nama_pegawai;
            $row[] = $total_nilai == 0 ? "<span class='label label-sm label-warning'>Belum Validasi</span>" : "<span class='label label-sm label-info'>Valid</span>";
            $row[] = $total_nilai." / ".$persen_nilai." %";
            //add html for action
//            if($curr_month !== $month){
//                $row[] = "<span class='label label-sm label-danger'>Close</span>";
//            }else 
                if($total_nilai > 0){
                $row[] = '<button class="btn btn-primary" title="Edit Validasi" onclick="editPrilaku('."'".$prilaku->id_prilakukerja."',"."'".$prilaku->nama_pegawai."'".')"><i class="fa fa-edit"></i> Edit</button>';
            }else{
                $row[] = '<button class="btn btn-success" title="Validasi" onclick="inputPrilaku('."'".$prilaku->id_pegawai."',"."'".$curr_month."',"."'".$prilaku->nama_pegawai."'".')"><i class="fa fa-edit"></i> Validasi</button>';
            }
            
            
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Validasi_kinerja_model->count_validasi_prilaku_all($filter_bln, $this->data['pegawai']->id_pegawai),
                    "recordsFiltered" => $this->Validasi_kinerja_model->count_validasi_prilaku_filtered($filter_bln, $this->data['pegawai']->id_pegawai),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function do_insert_prilaku(){
        $is_permit = $this->aauth->control_no_redirect('validasi_kinerja_ekinerja_perm');
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
        $this->form_validation->set_rules('or_pel', 'Orientasi Pelayanan', 'required|trim');
        $this->form_validation->set_rules('integritas', 'Integritas', 'required|trim');
        $this->form_validation->set_rules('komitmen', 'Komitmen', 'required|trim');
        $this->form_validation->set_rules('disiplin', 'Disiplin', 'required|trim');
        $this->form_validation->set_rules('kerjasama', 'Kerjasama', 'required|trim');
        
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
            $perms = "validasi_kinerja_ekinerja_perm";
            $comments = "Gagal Validasi Prilaku dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
            
        }else if ($this->form_validation->run() == TRUE)  {
            $id_pegawai = $this->input->post('id_pegawai_pri',TRUE);
            $bulan = $this->input->post('bulan_pri',TRUE);
            $data_prilaku = array(
                'orientasi_pelayanan' => $this->input->post('or_pel',TRUE),
                'integrasi' => $this->input->post('integritas',TRUE),
                'komitmen' => $this->input->post('komitmen',TRUE),
                'disiplin' => $this->input->post('disiplin',TRUE),
                'kerjasama' => $this->input->post('kerjasama',TRUE),
                'kepemimpinan' => 0,
                'id_pegawai' => $id_pegawai,
                'bulan' => $bulan
            );  
            $check_constrain = $this->Validasi_kinerja_model->check_constraint_prilaku($id_pegawai, $bulan);
            if($check_constrain > 0){
                $ins = $this->Validasi_kinerja_model->update_prilaku_by_id_pegawai($data_prilaku, $id_pegawai, $bulan);
            }else{
                $ins = $this->Validasi_kinerja_model->insert_prilaku($data_prilaku);
            }
            //$ins=true;
            if($ins){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Berhasil Validasi Prilaku Pegawai'
                );

                // if permitted, do logit
                $perms = "validasi_kinerja_ekinerja_perm";
                $comments = "Berhasil Validasi Prilaku Pegawai dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal Validasi Prilaku Pegawai, hubungi web administrator.');

                // if permitted, do logit
                $perms = "validasi_kinerja_ekinerja_perm";
                $comments = "Gagal Validasi Prilaku Pegawai dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
    
    public function ajax_get_list_aktifitas(){
        $id_pegawai = $this->input->get('id_pegawai',TRUE);
        $filter_bln = $this->input->get('curr_month',TRUE);
        $list = $this->Validasi_kinerja_model->get_aktifitas_list($id_pegawai, $filter_bln);
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        
        foreach($list as $aktifitas){
            $tgl_aktifitas = date('d-M-Y', strtotime($aktifitas->tanggal_aktifitas));
            $jam_mulai = date('H:i', strtotime($aktifitas->jam_mulai));
            $jam_akhir = date('H:i', strtotime($aktifitas->jam_akhir));
            $no++;
            $row = array();
//            $row[] = $no;
            $row[] = '<input type="checkbox" class="aktif" name="aktifitas_valid[]" value="'.$aktifitas->aktifitas_id.'" id="aktifitas_valid_'.$no.'" onclick = "select_one('.$no.');">';
            $row[] = $aktifitas->skp." - ".$aktifitas->waktu_efektif." Menit";
            $row[] = $aktifitas->uraian;
            $row[] = $tgl_aktifitas." ".$jam_mulai." - ".$jam_akhir." (Vol =".$aktifitas->jumlah.")";
            $row[] = $aktifitas->is_validasi == 0 ? "<span class='label label-sm label-warning'>Belum Validasi</span>" : ($aktifitas->is_validasi == 2 ? "<span class='label label-sm label-danger'>Ditolak</span>" : "<span class='label label-sm label-info'>Valid</span>");
            //add html for action
//            if($aktifitas->is_validasi == 0){
//            $row[] = '<button class="btn btn-icon-only blue" title=   "validasi" onclick="validasiAktifitas('."'".$aktifitas->aktifitas_id."'".')"><i class="glyphicon glyphicon-pencil"></i></button>
//                    <button class="btn btn-icon-only btn-warning" title="Tolak" onclick="deleteAktifitas('."'".$aktifitas->aktifitas_id."'".')"><i class="fa fa-times"></i></button>';
//            }else{
//                $row[] = '<button class="btn btn-icon-only blue" title="Edit" disabled><i class="glyphicon glyphicon-pencil"></i></button>
//                    <button class="btn btn-icon-only red" title="Delete" disabled><i class="fa fa-times"></i></button>';
//            }
            
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Validasi_kinerja_model->count_aktifitas_all($id_pegawai, $filter_bln),
                    "recordsFiltered" => $this->Validasi_kinerja_model->count_aktifitas_filtered($id_pegawai, $filter_bln),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function do_validasi_aktifitas(){
        $is_permit = $this->aauth->control_no_redirect('validasi_kinerja_ekinerja_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $aktifitas = $this->input->post('aktifitas_valid[]',TRUE);
        
        if(count($aktifitas) > 0){
            $statusinsert = false;
            
            //start transaction
            //$this->db->trans_begin();
            $i = 0;
            while($i < count($aktifitas)){
                $aktifitas_list = $this->Validasi_kinerja_model->get_aktifitas_id($aktifitas[$i]);
                $point = intval($aktifitas_list->waktu_efektif) * intval($aktifitas_list->jumlah);
//                $point = $aktifitas_list->jumlah;
                $data_valid = array(
                    'point' => $point,
                    'is_validasi' => 1, //disetujui
                    'update_date' => date('Y-m-d H:i:s')
                );
                $update = $this->Validasi_kinerja_model->update_status_validasi($data_valid, $aktifitas[$i]);

                if($update){
                    $statusinsert = true;
                }else{
                    $statusinsert = false;
                }
                $i++;
            }
//            if ($this->db->trans_status() === FALSE){
//                $this->db->trans_rollback();
//            }else{
//                $this->db->trans_commit();
//            }
            if($statusinsert){
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    // 'messages' => 'Berhasil melakukan approval rekon'
                    'messages' => 'Berhasil menvalidasi aktifitas'
                );

                // if permitted, do logit
                $perms = "validasi_kinerja_ekinerja_perm";
                $comments = "Berhasil menvalidasi aktifitas";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => false,
                    'messages' => 'Gagal menvalidasi aktifitas, sialhkan hubungi web administrator.'
                );

                // if permitted, do logit
                $perms = "validasi_kinerja_ekinerja_perm";
                $comments = "Gagal menvalidasi aktifitas";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                 'messages' => 'Tidak ada data yang dipilih untuk di-validasi'
            );

            // if permitted, do logit
            $perms = "validasi_kinerja_ekinerja_perm";
             $comments = "Tidak ada data yang dipilih untuk di-validasi";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
    
    public function do_tolak_aktifitas(){
        $is_permit = $this->aauth->control_no_redirect('validasi_kinerja_ekinerja_perm');
        if(!$is_permit) {
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => $this->lang->line('aauth_error_no_access'));
            echo json_encode($res);
            exit;
        }
        
        $aktifitas = $this->input->post('aktifitas_valid[]',TRUE);
        
        if(count($aktifitas) > 0){
            $statustolak = false;
            
            //start transaction
            //$this->db->trans_begin();
            $i = 0;
            while($i < count($aktifitas)){
                $aktifitas_list = $this->Validasi_kinerja_model->get_aktifitas_id($aktifitas[$i]);
                $point = intval($aktifitas_list->waktu_efektif) * intval($aktifitas_list->jumlah);
                $data_valid = array(
                    'point' => 0,
                    'is_validasi' => 2, //ditolak
                    'update_date' => date('Y-m-d H:i:s')
                );
                $update = $this->Validasi_kinerja_model->update_status_validasi($data_valid, $aktifitas[$i]);

                if($update){
                    $statusinsert = true;
                }else{
                    $statusinsert = false;
                }
                $i++;
            }
//            if ($this->db->trans_status() === FALSE){
//                $this->db->trans_rollback();
//            }else{
//                $this->db->trans_commit();
//            }
            if($statusinsert){
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    // 'messages' => 'Berhasil melakukan approval rekon'
                    'messages' => 'Berhasil menolak aktifitas'
                );

                // if permitted, do logit
                $perms = "validasi_kinerja_ekinerja_perm";
                $comments = "Berhasil menolak aktifitas";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => false,
                    'messages' => 'Gagal menolak aktifitas, sialhkan hubungi web administrator.'
                );

                // if permitted, do logit
                $perms = "validasi_kinerja_ekinerja_perm";
                $comments = "Gagal menolak aktifitas";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }else{
            $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                 'messages' => 'Tidak ada data yang dipilih untuk di-tolak'
            );

            // if permitted, do logit
            $perms = "validasi_kinerja_ekinerja_perm";
             $comments = "Tidak ada data yang dipilih untuk di-tolak";
            $this->aauth->logit($perms, current_url(), $comments);
        }
        
        echo json_encode($res);
    }
    
    public function ajax_get_perilaku_by_id(){
        $perilaku_id = $this->input->get('id_prilaku',TRUE);
        $old_data = $this->Validasi_kinerja_model->get_prilaku_id($perilaku_id);
        
        if(count((array)$old_data) > 0) {
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'data' => $old_data
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "Perilaku tidak ditemukan");
        }
        echo json_encode($res);
    }
    
    public function do_update_prilaku(){
        $is_permit = $this->aauth->control_no_redirect('validasi_kinerja_ekinerja_perm');
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
        $this->form_validation->set_rules('upd_or_pel', 'Orientasi Pelayanan', 'required|trim');
        $this->form_validation->set_rules('upd_integritas', 'Integritas', 'required|trim');
        $this->form_validation->set_rules('upd_komitmen', 'Komitmen', 'required|trim');
        $this->form_validation->set_rules('upd_disiplin', 'Disiplin', 'required|trim');
        $this->form_validation->set_rules('upd_kerjasama', 'Kerjasama', 'required|trim');
        
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
            $perms = "validasi_kinerja_ekinerja_perm";
            $comments = "Gagal Update Prilaku dengan error sebagai berikut = '". validation_errors('', "\n") ."'.";
            $this->aauth->logit($perms, current_url(), $comments);
            
        }else if ($this->form_validation->run() == TRUE)  {
            $di_perilaku = $this->input->post('upd_id_perilaku',TRUE);
            $data_prilaku = array(
                'orientasi_pelayanan' => $this->input->post('upd_or_pel',TRUE),
                'integrasi' => $this->input->post('upd_integritas',TRUE),
                'komitmen' => $this->input->post('upd_komitmen',TRUE),
                'disiplin' => $this->input->post('upd_disiplin',TRUE),
                'kerjasama' => $this->input->post('upd_kerjasama',TRUE),
                'kepemimpinan' => 0
            );  
            
            $ins = $this->Validasi_kinerja_model->update_prilaku($data_prilaku, $di_perilaku);
            //$ins=true;
            if($ins){                
                $res = array(
                    'csrfTokenName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                    'success' => true,
                    'messages' => 'Berhasil Update Prilaku Pegawai'
                );

                // if permitted, do logit
                $perms = "validasi_kinerja_ekinerja_perm";
                $comments = "Berhasil Update Prilaku Pegawai dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }else{
                $res = array(
                'csrfTokenName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                'success' => false,
                'messages' => 'Gagal Update Prilaku Pegawai, hubungi web administrator.');

                // if permitted, do logit
                $perms = "validasi_kinerja_ekinerja_perm";
                $comments = "Gagal Update Prilaku Pegawai dengan data berikut = '". json_encode($_REQUEST) ."'.";
                $this->aauth->logit($perms, current_url(), $comments);
            }
        }
        echo json_encode($res);
    }
}