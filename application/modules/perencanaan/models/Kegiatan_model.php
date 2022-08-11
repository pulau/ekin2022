<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kegiatan_model extends CI_Model {
    var $column = array('kegiatan_id','kegiatan_nama','m_plan_indikator_program.indikator_program');
    var $order = array('indikator_id' => 'ASC');
    
    public function _constract(){
        parent::__construct();
    }
    
    public function inpro_list($program_id){
        $this->db->from('m_plan_indikator_program');
        $this->db->where('program_id', $program_id);
        $query = $this->db->get();
        return $query->result();
    }
    
    public function program_list(){
        $this->db->from('m_plan_program');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_kegiatan_list(){
        $this->db->select('m_plan_kegiatan.*,m_plan_indikator_program.indikator_program AS indikator_program');
        $this->db->from('m_plan_kegiatan');
        $this->db->join('m_plan_indikator_program','m_plan_kegiatan.indikator_id = m_plan_indikator_program.indikator_id');
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
    
    public function count_kegiatan_all(){
        $this->db->from('m_plan_kegiatan');
        $this->db->join('m_plan_indikator_program','m_plan_kegiatan.indikator_id = m_plan_indikator_program.indikator_id');

        return $this->db->count_all_results();
    }
    
    public function count_kegiatan_filtered(){
        $this->db->from('m_plan_kegiatan');
        $this->db->join('m_plan_indikator_program','m_plan_kegiatan.indikator_id = m_plan_indikator_program.indikator_id');
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
    
    public function insert_kegiatan($data){
        $insert = $this->db->insert('m_plan_kegiatan',$data);
        
        return $insert;
    }
    
    public function get_kegiatan_by_id($id){
        $query = $this->db->get_where('m_plan_kegiatan', array('kegiatan_id' => $id), 1, 0);
        
        return $query->row();
    }
    
    public function get_program_id($indikator_id){
        $query = $this->db->get_where('m_plan_indikator_program', array('indikator_id' => $indikator_id), 1, 0);
        
        return $query->row();
    }
    
    public function update_kegiatan($data, $id){
        $update = $this->db->update('m_plan_kegiatan', $data, array('kegiatan_id' => $id));
        
        return $update;
    }
    
    public function check_constraint_kegiatan($kegiatan_id){
        $check = $this->db->get_where('m_plan_indikator_renstra', array('kegiatan_id' => $kegiatan_id), 1, 0);
        
        return $check->num_rows();
    }
    
    public function delete_kegiatan($id){
        $delete = $this->db->delete('m_plan_kegiatan', array('kegiatan_id' => $id));
        
        return $delete;
    }
}