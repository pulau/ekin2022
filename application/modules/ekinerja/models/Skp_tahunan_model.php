<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Skp_tahunan_model extends CI_Model {
    var $column = array('nip','skp','qty','waktu_efektif','waktu_total');
    var $column_order = array('skptahunan_id','skp','qty','waktu_efektif','waktu_total','qty');
    var $order = array('skptahunan_id' => 'ASC');
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_skptahunan_list($id_pegawai, $current_year){
        $this->db->from('v_skptahunan');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->where('tahun', $current_year);
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
    
    public function count_skptahunan_all($id_pegawai, $current_year){
        $this->db->from('v_skptahunan');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->where('tahun', $current_year);

        return $this->db->count_all_results();
    }
    
    public function count_skptahunan_filtered($id_pegawai, $current_year){
        $this->db->from('v_skptahunan');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->where('tahun', $current_year);
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
    
    public function list_skp(){
        $this->db->from('m_skp');

        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_skp_by_id($id){
        $query = $this->db->get_where('m_skp', array('kd_skp' => $id), 1, 0);
        
        return $query->row();
    }
    
    public function insert_skptahunan($data){
        $insert = $this->db->insert('m_skptahunan',$data);
        
        return $insert;
    }
    
    public function get_skpt_by_id($id){
        // $query = $this->db->get_where('v_skptahunan', array('skptahunan_id' => $id, 'is_validate' => 1), 1, 0);
        $query = $this->db->get_where('v_skptahunan', array('skptahunan_id' => $id), 1, 0);
        
        return $query->row();
    }
    
    public function update_skpt($data, $id){
        $update = $this->db->update('m_skptahunan', $data, array('skptahunan_id' => $id));
        
        return $update;
    }
    
    //constraint to skp tahunan
    public function check_constraint_skpt($skptahunan_id){
        $check = $this->db->get_where('t_kinerja_aktifitas', array('skptahunan_id' => $skptahunan_id));
        
        return $check->num_rows();
    }
    
    public function delete_skpt($id){
        $delete = $this->db->delete('m_skptahunan', array('skptahunan_id' => $id));
        
        return $delete;
    }
}