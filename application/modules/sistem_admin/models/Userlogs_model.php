<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Userlogs_model extends CI_Model {
    var $column = array('a.log_id','a.perms','a.dateactivity','a.page','a.ipaddr','b.nama_lengkap');
    var $column_order = array('log_id','perms','dateactivity','page','ipaddr','nama_user');
    var $order = array('log_id' => 'DESC');
    
    public function _constract(){
        parent::__construct();
    }
    
    public function get_userlogs_list(){
        $this->db->select('a.log_id,a.perms,a.dateactivity,a.page,a.comments,a.ipaddr,b.nama_lengkap as nama_user');
        $this->db->from('aauth_userslog a');
        $this->db->join('aauth_users b','a.user_id = b.id','left');
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
    
    public function count_userlogs_all(){
        $this->db->select('a.log_id');
        $this->db->from('aauth_userslog a');
        $this->db->join('aauth_users b','a.user_id = b.id','left');

        return $this->db->count_all_results();
    }
    
    public function count_userlogs_filtered(){
        $this->db->select('a.log_id');
        $this->db->from('aauth_userslog a');
        $this->db->join('aauth_users b','a.user_id = b.id','left');
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
    
    public function get_userlog_by_id($id){
        $this->db->select('a.log_id,a.perms,a.dateactivity,a.page,a.comments,a.ipaddr,b.nama_lengkap as nama_user');
        $this->db->from('aauth_userslog a');
        $this->db->where('log_id', $id);
        $this->db->join('aauth_users b','a.user_id = b.id','left');
//        $query = $this->db->get_where('aauth_userslog', array('log_id' => $id), 1, 0);
        $query = $this->db->get();
        return $query->row();
    }
}