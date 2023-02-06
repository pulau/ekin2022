<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pegawai_model extends CI_Model {
    var $column = array('nip','nama_pegawai','tempattugas_nama');
    var $column_order = array('id_pegawai','nip','nama_pegawai','tempattugas_nama');
    var $order = array('id_pegawai' => 'ASC');
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_pegawai_list(){
        $this->db->from('v_pegawai');
        $this->db->where('is_active',1);
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
    
    public function count_pegawai_all(){
        $this->db->from('v_pegawai');
        $this->db->where('is_active',1);
        $sub_query = $this->db->get_compiled_select();
        $this->db->from('('.$sub_query.') a');

        return $this->db->count_all_results();
    }
    
    public function count_pegawai_filtered(){
        $this->db->from('v_pegawai');
        $this->db->where('is_active',1);
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
            $this->db->order_by($this->column_order[$order_column['0']['column']], $order_column['0']['dir']);
        } 
        else if(isset($this->order)){
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    public function list_statuspegawai(){
        $this->db->from('m_statuspegawai');

        $query = $this->db->get();
        return $query->result();
    }
    
    public function list_pendidikan(){
        $this->db->from('m_pendidikan');

        $query = $this->db->get();
        return $query->result();
    }
    
    public function list_agama(){
        $this->db->from('m_agama');

        $query = $this->db->get();
        return $query->result();
    }
    
    public function list_rumpun(){
        $this->db->from('m_rumpun');

        $query = $this->db->get();
        return $query->result();
    }
    
    public function list_jabatan(){
        $this->db->from('m_jabatan');

        $query = $this->db->get();
        return $query->result();
    }
    
    public function list_bagian(){
        $this->db->from('m_bagian');

        $query = $this->db->get();
        return $query->result();
    }
    
    public function list_pajak(){
        $this->db->from('m_pajak');

        $query = $this->db->get();
        return $query->result();
    }
    
    public function list_pjcuti(){
        $this->db->from('m_pegawai');
        $this->db->where('is_active',1);
	    $this->db->order_by('nama_pegawai','ASC');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function list_tempattugas(){
        $this->db->from('m_tempattugas');

        $query = $this->db->get();
        return $query->result();
    }
    
    public function insert_pegawai($data){
        $insert = $this->db->insert('m_pegawai',$data);
        
        return $insert;
    }
    
    public function get_pegawai_by_id($id){
        $query = $this->db->get_where('v_pegawai', array('id_pegawai' => $id), 1, 0);
        
        return $query->row();
    }
    
    public function get_pegawai_by_nip($nip){
        $query = $this->db->get_where('v_pegawai', array('nip' => $nip), 1, 0);
        
        return $query->row();
    }
    
    public function update_pegawai($data, $id){
        $update = $this->db->update('m_pegawai', $data, array('id_pegawai' => $id));
        
        return $update;
    }
    
    public function update_aauth_users($nip, $nip_old){
        $data = array('nip' => $nip);
        $update = $this->db->update('aauth_users', $data, array('nip' => $nip_old));
        
        return $update;
    }
    
    public function update_pegawai_by_nip($data, $nip){
        $update = $this->db->update('m_pegawai', $data, array('nip' => $nip));
        
        return $update;
    }
    
    public function get_tempattugas_byid($tempattugas){
        $query = $this->db->get_where('m_tempattugas', array('tempattugas_id' => $tempattugas), 1, 0);
        
        return $query->row();
    }
    
    public function count_pegawai_by_nip($nip){
        $query = $this->db->get_where('m_pegawai', array('nip' => $nip), 1, 0);
        
        return $query->num_rows();
    }
}