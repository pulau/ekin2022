<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prestasi_kerja_model extends CI_Model {
    var $column = array('nip','nama_pegawai');
    var $column_order = array('id_pegawai','nip');
    var $order = array('pegawai_id' => 'ASC');
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_serapan_bulan($bulan){
        if(empty($bulan)){
            $curr_month = date('Y-m');
        }else{
            $curr_month = date('Y-m', strtotime($bulan));
        }
        $query = $this->db->get_where('m_penyerapan', array('bulan' => $curr_month), 1, 0);
        
        return $query->row();
    }
    
    public function get_prestasi_pegawai_list($filter_bln){
        $this->load->model('Utama_model');
        $users                = $this->aauth->get_user();
        $pegawai = $this->Utama_model->get_pegawai_by_nip($users->nip);
        //$bagian = !empty($pegawai) ? $pegawai->bagian : "";
        

        $bulan = date('Ym', strtotime($filter_bln));
        $this->db->select('t_prestasi_kerja.*, m_pegawai.nip, m_pegawai.nama_pegawai, m_pegawai.bagian');
        $this->db->from('t_prestasi_kerja');
        $this->db->join('m_pegawai','t_prestasi_kerja.pegawai_id = m_pegawai.id_pegawai');
        $this->db->where('t_prestasi_kerja.bulan', $bulan);
        $this->db->where('m_pegawai.bagian !=', 'Non Aktif');
        $this->db->where('m_pegawai.is_active', 1);
        $sub_query = $this->db->get_compiled_select();
        
        $this->db->from('('.$sub_query.') a');
        $i = 0;
        $search_value = $this->input->get('search');
        if($search_value !== false){
            foreach ($this->column as $item){
                ($i==0) ? $this->db->like($item, $search_value['value']) : $this->db->or_like($item, $search_value['value']);
                //$column[$i] = $item;
                $i++;
            }
        }
        
        $length = $this->input->get('length');
        if($length !== false){
            if($length != -1) {
                $this->db->limit($this->input->get('length'), $this->input->get('start'));
            }
        }
        
        $query = $this->db->get();
        
        return $query->result();
    }
    
    public function get_prestasi_pegawai_all($filter_bln){
        $this->load->model('Utama_model');
        $users                = $this->aauth->get_user();
        $pegawai = $this->Utama_model->get_pegawai_by_nip($users->nip);
        //$bagian = !empty($pegawai) ? $pegawai->bagian : "";

        $bulan = date('Ym', strtotime($filter_bln));
        $this->db->select('t_prestasi_kerja.*, m_pegawai.nip, m_pegawai.nama_pegawai');
        $this->db->from('t_prestasi_kerja');
        $this->db->join('m_pegawai','t_prestasi_kerja.pegawai_id = m_pegawai.id_pegawai');
        $this->db->where('m_pegawai.bagian !=', 'Non Aktif');
        $this->db->where('t_prestasi_kerja.bulan', $bulan);
        $this->db->where('m_pegawai.is_active', 1);
        
        return $this->db->count_all_results();
    }
    
    public function get_prestasi_pegawai_filtered($filter_bln){
        $this->load->model('Utama_model');
        $users                = $this->aauth->get_user();
        $pegawai = $this->Utama_model->get_pegawai_by_nip($users->nip);
        //$bagian = !empty($pegawai) ? $pegawai->bagian : "";

        $bulan = date('Ym', strtotime($filter_bln));
        $this->db->select('t_prestasi_kerja.*, m_pegawai.nip, m_pegawai.nama_pegawai');
        $this->db->from('t_prestasi_kerja');
        $this->db->join('m_pegawai','t_prestasi_kerja.pegawai_id = m_pegawai.id_pegawai');
        $this->db->where('t_prestasi_kerja.bulan', $bulan);
        $this->db->where('m_pegawai.bagian !=', 'Non Aktif');
        $this->db->where('m_pegawai.is_active', 1);
        $sub_query = $this->db->get_compiled_select();
        
        $this->db->from('('.$sub_query.') a');
        $i = 0;
        $search_value = $this->input->get('search');
        if($search_value !== false){
            foreach ($this->column as $item){
                ($i==0) ? $this->db->like($item, $search_value['value']) : $this->db->or_like($item, $search_value['value']);
                //$column[$i] = $item;
                $i++;
            }
        }
        
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    public function get_prestasi_pegawai_excel($filter_bln){
        $this->load->model('Utama_model');
        $users                = $this->aauth->get_user();
        $pegawai = $this->Utama_model->get_pegawai_by_nip($users->nip);
        //$bagian = !empty($pegawai) ? $pegawai->bagian : "";

        $bulan = date('Ym', strtotime($filter_bln));
        $this->db->select('t_prestasi_kerja.*, m_pegawai.nip, m_pegawai.nama_pegawai');
        $this->db->from('t_prestasi_kerja');
        $this->db->join('m_pegawai','t_prestasi_kerja.pegawai_id = m_pegawai.id_pegawai');
        $this->db->where('m_pegawai.bagian !=', 'Non Aktif');
        $this->db->where('t_prestasi_kerja.bulan', $bulan);
        $this->db->where('m_pegawai.is_active', 1);
        
        $query = $this->db->get();
        
        return $query->result();
    }
    
    public function insert_into_prestasikerja($data){
        $insert = $this->db->insert('t_prestasi_kerja',$data);
        
        return $insert;
    }
    
    public function count_capaian_pegawai($bln, $id_pegawai){
        $query = $this->db->get_where('t_prestasi_kerja', array('bulan' => $bln, 'pegawai_id' => $id_pegawai), 1, 0);
        
        return $query->num_rows();
    }
    
    public function update_prestasikerja($data, $bln, $id_pegawai){
        $update = $this->db->update('t_prestasi_kerja', $data, array('bulan' => $bln, 'pegawai_id' => $id_pegawai));
        
        return $update;
    }
    
    public function get_pegawai(){
        $this->db->from('m_pegawai');
        $this->db->where('status_pns', "NON PNS");
        $this->db->where('is_active', 1);
        $query = $this->db->get();
        
        return $query->result();
    }
    
//    public function get_total_poin_aktifitas($pegawai_id, $filter_bln){
//        if(empty($filter_bln)){
//            $curr_month = date('Y-m');
//        }else{
//            $curr_month = date('Y-m', strtotime($filter_bln));
//        }
//        
//        $this->db->select('SUM(point) as point');
//        $this->db->from('t_kinerja_aktifitas');
//        $this->db->where('id_pegawai', $pegawai_id);
//        $this->db->like('tanggal_aktifitas', $curr_month, 'after');
//        $query = $this->db->get();
//        $poin = !empty($query->row()->point) ? $query->row()->point : 0;
//        return $poin;
//    }
//    
//    public function get_total_poin_perilaku($pegawai_id, $filter_bln){
//        if(empty($filter_bln)){
//            $curr_month2 = date('Ym');
//        }else{
//            $curr_month2 = date('Ym', strtotime($filter_bln));
//        }
//        
//        $this->db->select('(((orientasi_pelayanan+integrasi+komitmen+disiplin+kerjasama+kepemimpinan)/5)*0.1) AS point_perilaku');
//        $this->db->from('t_prilakukerja');
//        $this->db->where('id_pegawai', $pegawai_id);
//        $this->db->like('bulan', $curr_month2);
//        $query = $this->db->get();
//        $poin = !empty($query->row()->point_perilaku) ? $query->row()->point_perilaku : 0;
//        return $poin;
//    }
}