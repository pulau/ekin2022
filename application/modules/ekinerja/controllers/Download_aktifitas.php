<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * download kinerja pegawai
 */

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Download_aktifitas extends MY_Controller {
    
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
        
        $this->load->model('download_aktifitas_model');
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
        // echo "index file";
        // die;
        $is_permit = $this->aauth->control_no_redirect('download_aktifitas_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "Download Aktifitas";
        $this->data['bc_child'] = "Dashboard";
        //$this->data['serapan'] = $this->Capaian_kinerja_model->get_serapan_bulan(date('M Y'));
        $perms = "download_aktifitas_perm";
        $comments = "Halaman download aktifitas Kinerja";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('download_aktifitas', $this->data);
    }
    
    public function ajax_list_Download_aktifitas(){
        $filter_bln = $this->input->get('filter_bln',TRUE);
        // $filter_bln = date('Ym', strtotime($filter_bln));
        $list = $this->download_aktifitas_model->get_download_aktifitas_list($this->data['pegawai']->id_pegawai,$filter_bln);
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;

        foreach($list as $aktifitas){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = date('d F Y', strtotime(str_replace('/', '-', $aktifitas->tanggal_aktifitas)));
            $row[] = date('g:i a', strtotime(str_replace('/', '-', $aktifitas->jam_mulai)));
            $row[] = date('g:i a', strtotime(str_replace('/', '-', $aktifitas->jam_akhir)));
            $row[] = $aktifitas->skp;
            $row[] = $aktifitas->uraian;
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->download_aktifitas_model->count_download_aktifitas_all($this->data['pegawai']->id_pegawai,$filter_bln),
                    "recordsFiltered" => $this->download_aktifitas_model->count_download_aktifitas_filtered($this->data['pegawai']->id_pegawai,$filter_bln),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }


    public function export_excel_pegawai($filter_bln){
        $filter_bln = ($filter_bln == "undefined") ? "" : $filter_bln;
        $filter_bln = str_replace('%20', ' ', $filter_bln);
        
       // $filter_bln = $this->input->get('filter_bln',TRUE);
        $data = $this->download_aktifitas_model->get_kinerja_pegawai_excel($this->data['pegawai']->id_pegawai,$filter_bln);
        $nama_pegawai = $this->data['pegawai']->nama_pegawai;
        $nama_bagian = $this->data['bagian']->bagian_nama;
        $fname="Kinerja_Pegawai_".$nama_pegawai."_".date('M_Y').".xls";
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=$fname");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        echo'<table border="0" bordercolor="#333333" cellpadding="0" cellspacing="0">';
        $kinerja_bulan = date("F Y",strtotime($filter_bln));
        $bulan = date("F",strtotime($filter_bln));
        $tahun = date("Y",strtotime($filter_bln));
        echo'<tr><td style="text-align:center;" colspan=7><font size=4><b>LAPORAN KINERJA PEGAWAI NON PNS</b></font></td></tr>';
        echo'<tr><td style="text-align:center;" colspan=7><font size=4><b>PUSKESMAS KECAMATAN KEPULAUAN SERIBU UTARA</b></font></td></tr>';
        echo'<tr><td style="text-align:center;" colspan=7><font size=4><b>TAHUN '.$tahun.'</b></font></td></tr>';
        echo '</table><br><br>';
        echo '<br/>';
        echo '<br/>';
        echo'<table border="0" bordercolor="#333333" cellpadding="0" cellspacing="0">';
        echo'<tr>
            <td style="text-align:left;" colspan=2><font size=2><b>Nama</b></font></td>
            <td style="text-align:left;" colspan=2><font size=2><b>: '.$nama_pegawai.'</b></font></td>
            </tr>';
        echo'<tr>
            <td style="text-align:left;" colspan=2><font size=2><b>Jabatan / Profesi </b></font></td>
            <td style="text-align:left;" colspan=2><font size=2><b>: '.$nama_bagian.'</b></font></td>
            </tr>';
        echo'<tr>
            <td style="text-align:left;" colspan=2><font size=2><b>Bulan</b></font></td>
            <td style="text-align:left;" colspan=2><font size=2><b>: '.$bulan.'</b></font></td>
            </tr>';
        echo '</table><br><br>';
        
        echo'<table border="1" bordercolor="#333333" cellpadding="0" cellspacing="0">';
        echo'<tr bgcolor=#b7b7b7>';
        echo "<td style='font-weight:bolder;text-align:center;'>No</td>";
        echo "<td style='font-weight:bolder;text-align:center;'>Tanggal</td>";
        echo "<td style='font-weight:bolder;text-align:center;'>Jam Mulai</td>";
        echo "<td style='font-weight:bolder;text-align:center;'>Jam Akhir</td>";
        echo "<td style='font-weight:bolder;text-align:center;' colspan='3'>Uraian</td>";
        echo'</tr>';

        if(count((array)$data)>0){
            $no=0;
            foreach($data as $download){
                $no++;
                echo'<tr>';
                echo "<td style='text-align:left;'>".$no."</td>";
                echo "<td style='text-align:left;'>".date('d F Y', strtotime(str_replace('/', '-', $download->tanggal_aktifitas)))."</td>";
                echo "<td style='text-align:left;'>".date('g:i a', strtotime(str_replace('/', '-', $download->jam_mulai)))."</td>";
                echo "<td style='text-align:left;'>".date('g:i a', strtotime(str_replace('/', '-', $download->jam_akhir)))."</td>";
                echo "<td style='text-align:left;' colspan='3'>".$download->skp." : ".$download->uraian."</td>";
                echo'</tr>';
            }
        }else{
            echo'<tr>';
            echo "<td style='text-align:center;' colspan='8'>No Data</td>";
            echo'</tr>';
        }

        echo'</table><br>';
        echo 'print date : '.date('d F Y g:i a');
    }
}