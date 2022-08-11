<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rumpun_model extends CI_Model {
    var $column = array('rumpun_id','rumpun_nama','rumpun_nilai');
    var $order = array('rumpun_id' => 'ASC');
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_rumpun_list(){
        $this->db->from('m_rumpun');
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
    
    public function count_rumpun_all(){
        $this->db->from('m_rumpun');

        return $this->db->count_all_results();
    }
    
    public function count_rumpun_filtered(){
        $this->db->from('m_rumpun');
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
    
    public function insert_rumpun($data){
        $insert = $this->db->insert('m_rumpun',$data);
        
        return $insert;
    }
    
    public function get_rumpun_by_id($id){
        $query = $this->db->get_where('m_rumpun', array('rumpun_id' => $id), 1, 0);
        
        return $query->row();
    }
    
    public function update_rumpun($data, $id){
        $update = $this->db->update('m_rumpun', $data, array('rumpun_id' => $id));
        
        return $update;
    }
    
    public function check_constraint_rumpun($rumpun_id){
        $check = $this->db->get_where('m_pegawai', array('rumpun' => $rumpun_id), 1, 0);
        
        return $check->num_rows();
    }
    
    public function delete_rumpun($id){
        $delete = $this->db->delete('m_rumpun', array('rumpun_id' => $id));
        
        return $delete;
    }
}