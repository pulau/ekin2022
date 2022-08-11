<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usergroup_model extends CI_Model {
    var $column = array('id','nama_lengkap','username');
    var $order = array('id' => 'ASC');
    public function _constract(){
        parent::__construct();
    }
    
    public function get_users_list(){
        $this->db->from('aauth_users');
        $this->db->where('banned',0);
        $sub_query = $this->db->get_compiled_select();
        $this->db->from('('.$sub_query.') a');
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
    
    public function count_users_all(){
        $this->db->from('aauth_users');
        $this->db->where('banned',0);
        $sub_query = $this->db->get_compiled_select();
        $this->db->from('('.$sub_query.') a');

        return $this->db->count_all_results();
    }
    
    public function count_users_filtered(){
        $this->db->from('aauth_users');
        $this->db->where('banned',0);
        $sub_query = $this->db->get_compiled_select();
        $this->db->from('('.$sub_query.') a');
        
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
    
    public function get_list_group(){
        $this->db->from('aauth_groups');
        $query = $this->db->get();
        
        return $query->result();
    }
    
    public function list_group_by_user($user_id){
        $this->db->from('aauth_groups');
        $this->db->join('aauth_user_to_group','aauth_user_to_group.group_id = aauth_groups.id');
        $this->db->where('aauth_user_to_group.user_id',$user_id);
        $query = $this->db->get();
        
        return $query->result();
    }
    
    public function get_user_by_id($id){
        $query = $this->db->get_where('aauth_users', array('id' => $id), 1, 0);
        
        return $query->row();
    }
    
    public function get_group_by_user($user_id){
        $query = $this->db->get_where('aauth_user_to_group', array('user_id' => $user_id));
        return $query->result();
    }
    
    public function delete_user_group($user_id){
        $delete = $this->db->delete('aauth_user_to_group', array('user_id' => $user_id));
        
        return $delete;
    }
}