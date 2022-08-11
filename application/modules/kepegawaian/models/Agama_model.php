<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agama_model extends CI_Model {
    var $column = array('id_agama','nama_agama');
    var $order = array('id_agama' => 'ASC');
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_agama_list(){
        $this->db->from('m_agama');
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
    
    public function count_agama_all(){
        $this->db->from('m_agama');

        return $this->db->count_all_results();
    }
    
    public function count_agama_filtered(){
        $this->db->from('m_agama');
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
    
    public function insert_agama($data){
        $insert = $this->db->insert('m_agama',$data);
        
        return $insert;
    }
    
    public function get_agama_by_id($id){
        $query = $this->db->get_where('m_agama', array('id_agama' => $id), 1, 0);
        
        return $query->row();
    }
    
    public function update_agama($data, $id){
        $update = $this->db->update('m_agama', $data, array('id_agama' => $id));
        
        return $update;
    }
    
    public function check_constraint_agama($agama_id){
        $check = $this->db->get_where('m_pegawai', array('agama' => $agama_id), 1, 0);
        
        return $check->num_rows();
    }
    
    public function delete_agama($id){
        $delete = $this->db->delete('m_agama', array('id_agama' => $id));
        
        return $delete;
    }
}