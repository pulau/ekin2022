<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Capaian_kinerja_model extends CI_Model {
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
    
    public function get_jumlah_tidak_masuk($bulan, $id_pegawai){
        if(empty($bulan)){
            $curr_month = date('Y-m');
        }else{
            $curr_month = date('Y-m', strtotime($bulan));
        }
        $query = $this->db->get_where('t_waktukurang', array(
                    'bulan' => $curr_month,
                    'id_pegawai' => $id_pegawai
                ), 1, 0);
        
        return $query->row();
    }
    
    public function get_jumlah_hari_kerja($bulan){
        if(empty($bulan)){
            $curr_month = date('Y-m');
        }else{
            $curr_month = date('Y-m', strtotime($bulan));
        }
        $query = $this->db->get_where('m_waktukerja', array(
                    'bulan' => $curr_month
                ), 1, 0);
        
        return $query->row();
    }
    
    public function get_jumlah_capaian($id_pegawai, $filter_bln){
        if(empty($filter_bln)){
            $curr_month = date('Y-m');
        }else{
            $curr_month = date('Y-m', strtotime($filter_bln));
        }
        $this->db->select('SUM(point) as capaian');
        $this->db->from('v_aktifitas');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->like('tanggal_aktifitas',$curr_month,'after');
        $this->db->group_by('id_pegawai');
        $query = $this->db->get();
        return $query->row();
    }
    
    public function get_perilaku($id_pegawai, $filter_bln){
        if(empty($filter_bln)){
            $curr_month = date('Ym');
        }else{
            $curr_month = date('Ym', strtotime($filter_bln));
        }
        $this->db->select('(((orientasi_pelayanan+integrasi+komitmen+disiplin+kerjasama+kepemimpinan)/5)*0.1) as persen_prilaku');
        $this->db->from('t_prilakukerja');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->like('bulan',$curr_month);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function get_aktifitas_list($id_pegawai, $filter_bln){
        if(empty($filter_bln)){
            $curr_month = date('Y-m');
        }else{
            $curr_month = date('Y-m', strtotime($filter_bln));
        }
        $this->db->select('skp, waktu_skp, qty, SUM(jumlah) as volume, SUM(point) as capaian');
        $this->db->from('v_aktifitas');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->like('tanggal_aktifitas',$curr_month,'after');
        $this->db->group_by('kd_skp');
        
        $query = $this->db->get();
        
        return $query->result();
    }
    
    public function count_aktifitas_all($id_pegawai, $filter_bln){
       if(empty($filter_bln)){
            $curr_month = date('Y-m');
        }else{
            $curr_month = date('Y-m', strtotime($filter_bln));
        }
        $this->db->select('skp, waktu_skp, SUM(jumlah) as volume, SUM(point) as capaian');
        $this->db->from('v_aktifitas');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->like('tanggal_aktifitas',$curr_month,'after');
        $this->db->group_by('kd_skp');

        return $this->db->count_all_results();
    }
    
    public function count_aktifitas_filtered($id_pegawai, $filter_bln){
        if(empty($filter_bln)){
            $curr_month = date('Y-m');
        }else{
            $curr_month = date('Y-m', strtotime($filter_bln));
        }
        $this->db->select('skp, waktu_skp, SUM(jumlah) as volume, SUM(point) as capaian');
        $this->db->from('v_aktifitas');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->like('tanggal_aktifitas',$curr_month,'after');
        $this->db->group_by('kd_skp');
        
        $query = $this->db->get();
        return $query->num_rows();
    }
}