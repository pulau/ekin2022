<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Waktu_kerja_model extends CI_Model {
    var $column = array('waktukerja_id','bulan','jml_hari','menit_per_hari');
    var $order = array('waktukerja_id' => 'DESC');
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_waktukerja_list(){
        $this->db->from('m_waktukerja');
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
    
    public function count_waktukerja_all(){
        $this->db->from('m_waktukerja');

        return $this->db->count_all_results();
    }
    
    public function count_waktukerja_filtered(){
        $this->db->from('m_waktukerja');
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
    
    public function insert_waktukerja($data){
        $insert = $this->db->insert('m_waktukerja',$data);
        
        return $insert;
    }
    
    public function get_waktukerja_by_bln($bln){
        $query = $this->db->get_where('m_waktukerja', array('bulan' => $bln), 1, 0);
        
        return $query->row();
    }
    
    public function update_waktukerja($data, $bln){
        $update = $this->db->update('m_waktukerja', $data, array('bulan' => $bln));
        
        return $update;
    }
    
    //constraint to skp tahunan
    public function check_constraint_waktukerja($bln){
        $check = $this->db->get_where('m_waktukerja', array('bulan' => $bln));
        
        return $check->num_rows();
    }
    
    public function delete_waktu_kerja($bln){
        $delete = $this->db->delete('m_waktukerja', array('bulan' => $bln));
        
        return $delete;
    }
}