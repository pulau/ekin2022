<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penyerapan_model extends CI_Model {
    var $column = array('penyerapan_id','bulan','nilai');
    var $order = array('penyerapan_id' => 'DESC');
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_penyerapan_list(){
        $this->db->from('m_penyerapan');
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
    
    public function count_penyerapan_all(){
        $this->db->from('m_penyerapan');

        return $this->db->count_all_results();
    }
    
    public function count_penyerapan_filtered(){
        $this->db->from('m_penyerapan');
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
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    public function insert_penyerapan($data){
        $insert = $this->db->insert('m_penyerapan',$data);
        
        return $insert;
    }
    
    public function get_penyerapan_by_bln($bln){
        $query = $this->db->get_where('m_penyerapan', array('bulan' => $bln), 1, 0);
        
        return $query->row();
    }
    
    public function update_penyerapan($data, $bln){
        $update = $this->db->update('m_penyerapan', $data, array('bulan' => $bln));
        
        return $update;
    }
    
    //constraint to skp tahunan
    public function check_constraint_penyerapan($bln){
        $check = $this->db->get_where('m_penyerapan', array('bulan' => $bln));
        
        return $check->num_rows();
    }
    
    public function delete_penyerapan($bln){
        $delete = $this->db->delete('m_penyerapan', array('bulan' => $bln));
        
        return $delete;
    }
}