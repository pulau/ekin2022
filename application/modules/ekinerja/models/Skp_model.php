<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Skp_model extends CI_Model {
    var $column = array('kd_skp','skp','waktu');
    var $order = array('kd_skp' => 'ASC');
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_skp_list(){
        $this->db->from('m_skp');
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
    
    public function count_skp_all(){
        $this->db->from('m_skp');

        return $this->db->count_all_results();
    }
    
    public function count_skp_filtered(){
        $this->db->from('m_skp');
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
    
    public function insert_skp($data){
        $insert = $this->db->insert('m_skp',$data);
        
        return $insert;
    }
    
    public function get_skp_by_id($id){
        $query = $this->db->get_where('m_skp', array('kd_skp' => $id), 1, 0);
        
        return $query->row();
    }
    
    public function update_skp($data, $id){
        $update = $this->db->update('m_skp', $data, array('kd_skp' => $id));
        
        return $update;
    }
    //constraint to skp tahunan
    public function check_constraint_skp($kd_skp){
        $check = $this->db->get_where('v_skptahunan', array('kd_skp' => $kd_skp));
        
        return $check->num_rows();
    }
    
    public function delete_skp($id){
        $delete = $this->db->delete('m_skp', array('kd_skp' => $id));
        
        return $delete;
    }
}