<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permission_model extends CI_Model {
    var $column = array('a.id','a.name','a.definition','a.url','aauth_modules.label','a.urutan_menu','b.name');
    var $column_order = array('id','name','definition','url','parent','module_name','urutan_menu');
    var $order = array('id' => 'ASC');
    public function _constract(){
        parent::__construct();
    }
    
    public function get_perms_list(){
        $this->db->select('a.id,a.name,a.definition,b.name as parent,a.url,a.icon,a.module_id,a.urutan_menu,aauth_modules.label as module_name');
        $this->db->from('aauth_perms a');
        $this->db->join('aauth_modules','a.module_id = aauth_modules.id');
        $this->db->join('aauth_perms b','a.parent_id = b.id','left');
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
    
    public function count_perms_all(){
        $this->db->select('a.id');
        $this->db->from('aauth_perms a');
        $this->db->join('aauth_modules','a.module_id = aauth_modules.id');
        $this->db->join('aauth_perms b','a.parent_id = b.id','left');

        return $this->db->count_all_results();
    }
    
    public function count_perms_filtered(){
        $this->db->select('a.id');
        $this->db->from('aauth_perms a');
        $this->db->join('aauth_modules','a.module_id = aauth_modules.id');
        $this->db->join('aauth_perms b','a.parent_id = b.id','left');
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
    
    public function insert_perm($data){
        $insert = $this->db->insert('aauth_perms',$data);
        
        return $insert;
    }
    
    public function get_perm_by_id($id){
        $query = $this->db->get_where('aauth_perms', array('id' => $id), 1, 0);
        
        return $query->row();
    }
    
    public function update_perm($data, $id){
        $update = $this->db->update('aauth_perms', $data, array('id' => $id));
        
        return $update;
    }
    
    public function check_constraint_perm($perm_id){
        $check = $this->db->get_where('aauth_perm_to_group', array('perm_id' => $perm_id), 1, 0);
        
        return $check->num_rows();
    }
    
    public function delete_perm($id){
        $delete = $this->db->delete('aauth_perms', array('id' => $id));
        
        return $delete;
    }
    
    public function perm_parent_list(){
        $this->db->from('aauth_perms');
        $query = $this->db->get();
        
        return $query->result();
    }
    
    public function module_list(){
        $this->db->from('aauth_modules');
        $query = $this->db->get();
        
        return $query->result();
    }
}