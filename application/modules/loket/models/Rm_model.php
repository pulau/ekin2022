<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rm_model extends CI_Model {
    public function _constract(){
        parent::__construct();
    }
    
    var $column = array('rm_id','tgl_daftar','no_rm','nama_pasien','tgl_lahir');
    var $order = array('rm_id' => 'desc');
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_mr_list($puskesmas){
        $this->db->from('m_rm_pasien');
        $this->db->where('ukpd_id',$puskesmas);
        $sub_query = $this->db->get_compiled_select();
        $this->db->from('('.$sub_query.') a');
        
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
    
    public function count_mr_all($puskesmas){
        $this->db->from('m_rm_pasien');
        $this->db->where('ukpd_id',$puskesmas);
        $sub_query = $this->db->get_compiled_select();
        $this->db->from('('.$sub_query.') a');

        return $this->db->count_all_results();
    }
    
    public function count_mr_filtered($puskesmas){
        $this->db->from('m_rm_pasien');
        $this->db->where('ukpd_id',$puskesmas);
        $sub_query = $this->db->get_compiled_select();
        $this->db->from('('.$sub_query.') a');
        
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
    
    public function insert_mr($data){
        $insert = $this->db->insert('m_rm_pasien',$data);
        
        return $insert;
    }
    
    public function generate_mr_number($puskesmas){
        $default = "000001";
        $date = date('y');
        $prefix = $date.".";
        $sql = "SELECT CAST(MAX(SUBSTR(no_rm,".(strlen($prefix)+1).",".(strlen($default)).")) AS UNSIGNED) nomaksimal
                        FROM m_rm_pasien   
                        WHERE ukpd_id = '".$puskesmas."' AND no_rm LIKE ('".$date.".%')";
        $no_current =  $this->db->query($sql)->result();
        
        $next_rm = $prefix.(isset($no_current[0]->nomaksimal) ? (str_pad($no_current[0]->nomaksimal+1, strlen($default), 0,STR_PAD_LEFT)) : $default);
        
        return $next_rm;
    }
    
    public function get_rm_by_id($id){
        $query = $this->db->get_where('m_rm_pasien', array('rm_id' => $id), 1, 0);
        
        return $query->row();
    }
    
    public function update_rm($data, $id){
        $update = $this->db->update('m_rm_pasien', $data, array('rm_id' => $id));
        
        return $update;
    }
}