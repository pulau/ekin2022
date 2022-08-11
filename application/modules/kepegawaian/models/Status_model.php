<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Status_model extends CI_Model {
    var $column = array('statuspegawai_id','statuspegawai_nama','nilai');
    var $order = array('statuspegawai_id' => 'ASC');
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_status_list(){
        $this->db->from('m_statuspegawai');
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
    
    public function count_status_all(){
        $this->db->from('m_statuspegawai');

        return $this->db->count_all_results();
    }
    
    public function count_status_filtered(){
        $this->db->from('m_statuspegawai');
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
    
    public function insert_status($data){
        $insert = $this->db->insert('m_statuspegawai',$data);
        
        return $insert;
    }
    
    public function get_status_by_id($id){
        $query = $this->db->get_where('m_statuspegawai', array('statuspegawai_id' => $id), 1, 0);
        
        return $query->row();
    }
    
    public function update_status($data, $id){
        $update = $this->db->update('m_statuspegawai', $data, array('statuspegawai_id' => $id));
        
        return $update;
    }
    
    public function check_constraint_status($statuspegawai_id){
        $check = $this->db->get_where('m_pegawai', array('status' => $statuspegawai_id), 1, 0);
        
        return $check->num_rows();
    }
    
    public function delete_status($id){
        $delete = $this->db->delete('m_statuspegawai', array('statuspegawai_id' => $id));
        
        return $delete;
    }
}