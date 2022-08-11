<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gaji_model extends CI_Model {
    var $column_sort = array('gaji_id','masa_kerja','pendidikan_nama','nominal_gaji');
    var $column = array('masa_kerja','a.pendidikan_nama','nominal_gaji');
    var $order = array('gaji_id' => 'DESC');
    
    public function __construct() {
        parent::__construct();
    }
    
    public function list_pendidikan(){
        $this->db->from('m_pendidikan');

        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_gaji_list(){
        $this->db->select('m_gaji.*,a.pendidikan_nama as pendidikan_nama');
        $this->db->from('m_gaji');
        $this->db->join('m_pendidikan a','m_gaji.pendidikan = a.pendidikan_id');
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
            $this->db->order_by($this->column_sort[$order_column['0']['column']], $order_column['0']['dir']);
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
    
    public function count_gaji_all(){
        $this->db->select('m_gaji.*,a.pendidikan_nama as pendidikan_nama');
        $this->db->from('m_gaji');
        $this->db->join('m_pendidikan a','m_gaji.pendidikan = a.pendidikan_id');

        return $this->db->count_all_results();
    }
    
    public function count_gaji_filtered(){
        $this->db->select('m_gaji.*,a.pendidikan_nama as pendidikan_nama');
        $this->db->from('m_gaji');
        $this->db->join('m_pendidikan a','m_gaji.pendidikan = a.pendidikan_id');
        $i = 0;
        $search_value = $this->input->get('search');
        if($search_value !== false){
            foreach ($this->column as $item){
                ($i==0) ? $this->db->like($item, $search_value['value']) : $this->db->or_like($item, $search_value['value']);
                //$column[$i] = $item;
                
                $i++;
            }
        }
//        $order_column = $this->input->get('order');
//        if($order_column !== false){
//            $this->db->order_by($this->column_sort[$order_column['0']['column']], $order_column['0']['dir']);
//        } 
//        else if(isset($this->order)){
//            $order = $this->order;
//            $this->db->order_by(key($order), $order[key($order)]);
//        }
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    public function insert_gaji($data){
        $insert = $this->db->insert('m_gaji',$data);
        
        return $insert;
    }
    
    public function get_gaji_by_id($gaji_id){
        $query = $this->db->get_where('m_gaji', array('gaji_id' => $gaji_id), 1, 0);
        
        return $query->row();
    }
    
    public function update_gaji($data, $gaji_id){
        $update = $this->db->update('m_gaji', $data, array('gaji_id' => $gaji_id));
        
        return $update;
    }
    
    public function delete_gaji($gaji_id){
        $delete = $this->db->delete('m_gaji', array('gaji_id' => $gaji_id));
        
        return $delete;
    }
}