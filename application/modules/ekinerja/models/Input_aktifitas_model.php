<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Input_aktifitas_model extends CI_Model {
    var $column = array('skp','waktu_efektif','jumlah');
    var $column_order = array('skp','is_validasi','waktu_efektif','jumlah');
    var $order = array('skptahunan_id' => 'ASC');
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_aktifitas_list($id_pegawai, $filter_tgl){
        if(empty($filter_tgl)){
            $curr_month = date('Y-m-d');
        }else{
            $curr_month = date('Y-m-d', strtotime($filter_tgl));
        }
        
        $this->db->from('v_aktifitas');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->like('tanggal_aktifitas',$curr_month);
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
            $this->db->order_by($this->column_order[$order_column['0']['column']], $order_column['0']['dir']);
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
    
    public function count_aktifitas_all($id_pegawai, $filter_tgl){
        if(empty($filter_tgl)){
            $curr_month = date('Y-m-d');
        }else{
            $curr_month = date('Y-m-d', strtotime($filter_tgl));
        }
        
        $this->db->from('v_aktifitas');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->like('tanggal_aktifitas',$curr_month);

        return $this->db->count_all_results();
    }
    
    public function count_aktifitas_filtered($id_pegawai, $filter_tgl){
        if(empty($filter_tgl)){
            $curr_month = date('Y-m-d');
        }else{
            $curr_month = date('Y-m-d', strtotime($filter_tgl));
        }
        
        $this->db->from('v_aktifitas');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->like('tanggal_aktifitas',$curr_month);
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
    
    function get_pegawai_by_nip($nip){
        $query = $this->db->get_where('m_pegawai', array('nip' => $nip), 1, 0);
        
        return $query->row();
    }
    
    public function get_bagian_by_id($id){
        $query = $this->db->get_where('m_bagian', array('bagian_id' => $id), 1, 0);
        
        return $query->row();
    }
    
    public function list_skptahunan($nip){
        $current_year = date('Y');
        $this->db->from('v_skptahunan');
        $this->db->where('tahun', $current_year);
        $this->db->where('nip', $nip);
        $this->db->where('is_validate', 1);
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_skp_by_id($id){
        $query = $this->db->get_where('m_skp', array('kd_skp' => $id), 1, 0);
        
        return $query->row();
    }
    
    public function insert_aktifitas($data){
        $insert = $this->db->insert('t_kinerja_aktifitas',$data);
        
        return $insert;
    }
    
    public function get_skpt_by_id($id){
        $query = $this->db->get_where('v_skptahunan', array('skptahunan_id' => $id), 1, 0);
        
        return $query->row();
    }
    
    public function get_aktifitas_by_id($id){
        $query = $this->db->get_where('v_aktifitas', array('aktifitas_id' => $id), 1, 0);
        
        return $query->row();
    }
    
    public function update_aktifitas($data, $id){
        $update = $this->db->update('t_kinerja_aktifitas', $data, array('aktifitas_id' => $id));
        
        return $update;
    }
    
    public function delete_aktifitas($id){
        $delete = $this->db->delete('t_kinerja_aktifitas', array('aktifitas_id' => $id));
        
        return $delete;
    }
}