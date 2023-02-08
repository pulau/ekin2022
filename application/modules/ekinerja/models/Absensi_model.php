<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi_model extends CI_Model {
    var $column = array('id_waktukurang', 'id_pegawai', 'bulan', 'tanpa_alasan', 'terlambat_menit', 'pulang_cepat_menit', 'izin', 'sakit', 'cuti_alasan_penting', 'izin_setengah_hari', 'covid', 'ranapc19', 'cuti_tahunan', 'sakit_srt_dokter', 'cuti_bersalin', 'cuti_besar', 'dinas_luar_akhir', 'dinas_luar_awal', 'tidak_terbaca', 'dinas_luar_penuh', 'cuti_sakit', 'cuti_bersalin_ak3', 'cuti_sakit_ranap_rs');
    var $order = array('id_waktukurang' => 'DESC');
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_absensi_list($bulan){
        if(empty($bulan)){
            $curr_month = date('Y-m');
        }else{
            $curr_month = date('Y-m', strtotime($bulan));
        }
        $this->db->select('t_waktukurang.*,m_pegawai.nip,m_pegawai.nama_pegawai');
        $this->db->from('t_waktukurang');
        $this->db->join('m_pegawai','t_waktukurang.id_pegawai = m_pegawai.id_pegawai');
        $this->db->where('t_waktukurang.bulan', $curr_month);
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
        $order_column = $this->input->get('order');
        if($order_column !== false){
            $this->db->order_by($this->column[$order_column['0']['column']], $order_column['0']['dir']);
        } 
        else if(isset($this->order)){
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
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
    
    public function count_absensi_all($bulan){
        if(empty($bulan)){
            $curr_month = date('Y-m');
        }else{
            $curr_month = date('Y-m', strtotime($bulan));
        }
        $this->db->select('t_waktukurang.*,m_pegawai.nip,m_pegawai.nama_pegawai');
        $this->db->from('t_waktukurang');
        $this->db->join('m_pegawai','t_waktukurang.id_pegawai = m_pegawai.id_pegawai');
        $this->db->where('t_waktukurang.bulan', $curr_month);
        $this->db->where('m_pegawai.is_active', 1);

        return $this->db->count_all_results();
    }
    
    public function count_absensi_filtered($bulan){
        if(empty($bulan)){
            $curr_month = date('Y-m');
        }else{
            $curr_month = date('Y-m', strtotime($bulan));
        }
        $this->db->select('t_waktukurang.*,m_pegawai.nip,m_pegawai.nama_pegawai');
        $this->db->from('t_waktukurang');
        $this->db->join('m_pegawai','t_waktukurang.id_pegawai = m_pegawai.id_pegawai');
        $this->db->where('t_waktukurang.bulan', $curr_month);
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
    
    public function get_pegawai_by_nip($nip){
        $query = $this->db->get_where('v_pegawai', array('nip' => $nip, 'is_active' => 1), 1, 0);
        
        return $query->row();
    }
    
    public function update_kehadiran($data, $id_waktukurang){
        $update = $this->db->update('t_waktukurang', $data, array('id_waktukurang' => $id_waktukurang));
        
        return $update;
    }
    
    public function get_kehadiran_by_id($id_waktukurang){
        $query = $this->db->get_where('t_waktukurang', array('id_waktukurang' => $id_waktukurang), 1, 0);
        
        return $query->row();
    }
    
     public function insert_kehadiran($data){
        $insert = $this->db->insert('t_waktukurang',$data);
        
        return $insert;
    }
    
    public function count_kehadiran_by_bln($bln, $id_pegawai){
        $query = $this->db->get_where('t_waktukurang', array('bulan' => $bln, 'id_pegawai' => $id_pegawai), 1, 0);
        
        return $query->num_rows();
    }
}