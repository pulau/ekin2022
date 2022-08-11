<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group_model extends CI_Model {
    var $column = array('id','name','definition');
    var $order = array('id' => 'ASC');
    public function _constract(){
        parent::__construct();
    }
    
    public function get_groups_list(){
        $this->db->from('aauth_groups');
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
    
    public function count_groups_all(){
        $this->db->from('aauth_groups');

        return $this->db->count_all_results();
    }
    
    public function count_groups_filtered(){
        $this->db->from('aauth_groups');
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
    
    public function list_perm_by_group($group_id){
        $this->db->from('aauth_perms');
        $this->db->join('aauth_perm_to_group','aauth_perm_to_group.perm_id = aauth_perms.id');
        $this->db->where('aauth_perm_to_group.group_id',$group_id);
        $query = $this->db->get();
        
        return $query->result();
    }
    
    public function get_list_perm($parent = 0){
        $this->db->from('aauth_perms');
        $this->db->where('parent_id',$parent);
        $query = $this->db->get();
        
        return $query->result();
    }
    
    public function get_group_by_id($id){
        $query = $this->db->get_where('aauth_groups', array('id' => $id), 1, 0);
        
        return $query->row();
    }
    
    public function get_perm_by_group($group_id){
        $query = $this->db->get_where('aauth_perm_to_group', array('group_id' => $group_id));
        return $query->result();
    }
    
    public function delete_group($id){
        $this->delete_perm_group($id);
        
        $this->delete_user_group($id);
      
        $delete = $this->db->delete('aauth_groups', array('id' => $id));
        
        return $delete;
    }
    
    public function delete_perm_group($id){
        $delete = $this->db->delete('aauth_perm_to_group', array('group_id' => $id));
        
        return $delete;
    }
    
    public function delete_user_group($id){
        $delete = $this->db->delete('aauth_user_to_group', array('group_id' => $id));
        
        return $delete;
    }
}