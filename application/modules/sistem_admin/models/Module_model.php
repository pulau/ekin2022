<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Module_model extends CI_Model {
    var $column = array('id','name','label','modul_url','modul_urutan','modul_kategori');
    var $order = array('id' => 'ASC');
    public function _constract(){
        parent::__construct();
    }
    
    public function get_modules_list(){
        $this->db->from('aauth_modules');
        $i = 0;
        $search_value = $this->input->get('search');
        if($search_value){
            foreach ($this->column as $item){
                ($i==0) ? $this->db->like($item, $search_value['value']) : $this->db->or_like($item, $search_value['value']);
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
    
    public function count_modules_all(){
        $this->db->from('aauth_modules');

        return $this->db->count_all_results();
    }
    
    public function count_modules_filtered(){
        $this->db->from('aauth_modules');
        $i = 0;
        $search_value = $this->input->get('search');
        if($search_value){
            foreach ($this->column as $item){
                ($i==0) ? $this->db->like($item, $search_value['value']) : $this->db->or_like($item, $search_value['value']);
                $i++;
            }
        }
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    public function insert_module($data){
        $insert = $this->db->insert('aauth_modules',$data);
        
        return $insert;
    }
    
    public function get_module_by_id($id){
        $query = $this->db->get_where('aauth_modules', array('id' => $id), 1, 0);
        
        return $query->row();
    }
    
    public function update_module($data, $id){
        $update = $this->db->update('aauth_modules', $data, array('id' => $id));
        
        return $update;
    }
    
    public function check_constraint_module($id_module){
        $check = $this->db->get_where('aauth_perms', array('module_id' => $id_module), 1, 0);
        
        return $check->num_rows();
    }
    
    public function delete_module($id){
        $delete = $this->db->delete('aauth_modules', array('id' => $id));
        
        return $delete;
    }
}