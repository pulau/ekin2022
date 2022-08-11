<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inpro_model extends CI_Model {
    var $column = array('indikator_id','indikator_program','m_plan_program.program_nama');
    var $order = array('indikator_id' => 'ASC');
    
    public function _constract(){
        parent::__construct();
    }
    
    public function program_list(){
        $this->db->from('m_plan_program');

        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_inpro_list(){
        $this->db->select('m_plan_indikator_program.*,m_plan_program.program_nama AS program_nama');
        $this->db->from('m_plan_indikator_program');
        $this->db->join('m_plan_program','m_plan_indikator_program.program_id = m_plan_program.program_id');
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
    
    public function count_inpro_all(){
        $this->db->from('m_plan_indikator_program');
        $this->db->join('m_plan_program','m_plan_indikator_program.program_id = m_plan_program.program_id');

        return $this->db->count_all_results();
    }
    
    public function count_inpro_filtered(){
        $this->db->from('m_plan_indikator_program');
        $this->db->join('m_plan_program','m_plan_indikator_program.program_id = m_plan_program.program_id');
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
    
    public function insert_inpro($data){
        $insert = $this->db->insert('m_plan_indikator_program',$data);
        
        return $insert;
    }
    
    public function get_inpro_by_id($id){
        $query = $this->db->get_where('m_plan_indikator_program', array('indikator_id' => $id), 1, 0);
        
        return $query->row();
    }
    
    public function update_inpro($data, $id){
        $update = $this->db->update('m_plan_indikator_program', $data, array('indikator_id' => $id));
        
        return $update;
    }
    
    public function check_constraint_inpro($inpro_id){
        $check = $this->db->get_where('m_plan_kegiatan', array('indikator_id' => $inpro_id), 1, 0);
        
        return $check->num_rows();
    }
    
    public function delete_inpro($id){
        $delete = $this->db->delete('m_plan_indikator_program', array('indikator_id' => $id));
        
        return $delete;
    }
}