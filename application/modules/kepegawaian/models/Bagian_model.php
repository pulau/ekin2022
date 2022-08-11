<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bagian_model extends CI_Model {
    var $column = array('bagian_id','bagian_nama','a.nama_pegawai','b.nama_pegawai');
    var $column_order = array('bagian_id','bagian_nama','koordinator','pj_cuti_nama');
    var $order = array('bagian_id' => 'ASC');
    
    public function _constract(){
        parent::__construct();
    }
    
    public function get_bagian_list(){
        $this->db->select('m_bagian.*,a.nama_pegawai as koordinator, b.nama_pegawai as pj_cuti_nama');
        $this->db->from('m_bagian');
        $this->db->join('m_pegawai a','m_bagian.kordinator_id = a.id_pegawai','left');
        $this->db->join('m_pegawai b','m_bagian.pj_cuti = b.id_pegawai','left');
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
    
    public function count_bagian_all(){
        $this->db->select('m_bagian.*,a.nama_pegawai as koordinator, b.nama_pegawai as pj_cuti_nama');
        $this->db->from('m_bagian');
        $this->db->join('m_pegawai a','m_bagian.kordinator_id = a.id_pegawai','left');
        $this->db->join('m_pegawai b','m_bagian.pj_cuti = b.id_pegawai','left');

        return $this->db->count_all_results();
    }
    
    public function count_bagian_filtered(){
        $this->db->select('m_bagian.*,a.nama_pegawai as koordinator, b.nama_pegawai as pj_cuti_nama');
        $this->db->from('m_bagian');
        $this->db->join('m_pegawai a','m_bagian.kordinator_id = a.id_pegawai','left');
        $this->db->join('m_pegawai b','m_bagian.pj_cuti = b.id_pegawai','left');
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
            $this->db->order_by($this->column_order[$order_column['0']['column']], $order_column['0']['dir']);
        } 
        else if(isset($this->order)){
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    public function insert_bagian($data){
        $insert = $this->db->insert('m_bagian',$data);
        
        return $insert;
    }
    
    public function get_bagian_by_id($id){
        $query = $this->db->get_where('m_bagian', array('bagian_id' => $id), 1, 0);
        
        return $query->row();
    }
    
    public function update_bagian($data, $id){
        $update = $this->db->update('m_bagian', $data, array('bagian_id' => $id));
        
        return $update;
    }
    
    public function delete_bagian($id){
        $delete = $this->db->delete('m_bagian', array('bagian_id' => $id));
        
        return $delete;
    }
    
    public function check_constraint_bagian($bagian_id){
        $check = $this->db->get_where('m_pegawai', array('bagian' => $bagian_id), 1, 0);
        
        return $check->num_rows();
    }
    
    public function list_pegawai(){
        $this->db->select('id_pegawai,nama_pegawai');
        $this->db->from('m_pegawai');
        $this->db->where('is_active',1);
        $query = $this->db->get();
        return $query->result();
    }
}