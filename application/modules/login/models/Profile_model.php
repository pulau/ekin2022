<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    
    public function update_username($data, $id){
        $update = $this->db->update('aauth_users', $data, array('id' => $id));
        
        return $update;
    }
    
    public function get_pendidikan_list($pegawai_id){
        $this->db->select('t_pendidikan_pegawai.*,m_pendidikan.pendidikan_nama');
        $this->db->from('t_pendidikan_pegawai');
        $this->db->join('m_pendidikan','t_pendidikan_pegawai.jenjang_pendidikan = m_pendidikan.pendidikan_id');
        $this->db->where('t_pendidikan_pegawai.pegawai_id',$pegawai_id);
        
        $query = $this->db->get();
        
        return $query->result();
    }
    
    public function count_pendidikan_all($pegawai_id){
       $this->db->select('t_pendidikan_pegawai.*,m_pendidikan.pendidikan_nama');
        $this->db->from('t_pendidikan_pegawai');
        $this->db->join('m_pendidikan','t_pendidikan_pegawai.jenjang_pendidikan = m_pendidikan.pendidikan_id');
        $this->db->where('t_pendidikan_pegawai.pegawai_id',$pegawai_id);

        return $this->db->count_all_results();
    }
    
    public function count_pendidikan_filtered($pegawai_id){
        $this->db->select('t_pendidikan_pegawai.*,m_pendidikan.pendidikan_nama');
        $this->db->from('t_pendidikan_pegawai');
        $this->db->join('m_pendidikan','t_pendidikan_pegawai.jenjang_pendidikan = m_pendidikan.pendidikan_id');
        $this->db->where('t_pendidikan_pegawai.pegawai_id',$pegawai_id);
        
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    public function insert_pendidikan($data){
        $insert = $this->db->insert('t_pendidikan_pegawai',$data);
        
        return $insert;
    }
    
    public function get_pendidikan_by_id($id){
        $query = $this->db->get_where('t_pendidikan_pegawai', array('pendidikan_peg_id' => $id), 1, 0);
        
        return $query->row();
    }
    
    public function update_pendidikan($data, $id){
        $update = $this->db->update('t_pendidikan_pegawai', $data, array('pendidikan_peg_id' => $id));
        
        return $update;
    }
    
    public function delete_pendidikan($id){
        $delete = $this->db->delete('t_pendidikan_pegawai', array('pendidikan_peg_id' => $id));
        
        return $delete;
    }

    public function get_pelatihan_list($pegawai_id)
    {
        # code...
        $this->db->select('t_pelatihan_pegawai.*');
        $this->db->from('t_pelatihan_pegawai');
        $this->db->where('t_pelatihan_pegawai.pegawai_id', $pegawai_id);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_pelatihan_all($pegawai_id)
    {
        # code...
        $this->db->select('t_pelatihan_pegawai.*');
        $this->db->from('t_pelatihan_pegawai');
        $this->db->where('t_pelatihan_pegawai.pegawai_id', $pegawai_id);

        return $this->db->count_all_results();
    }

    public function count_pelatihan_filtered($pegawai_id='')
    {
        # code...
        $this->db->select('t_pelatihan_pegawai.*');
        $this->db->from('t_pelatihan_pegawai');
        $this->db->where('t_pelatihan_pegawai.pegawai_id', $pegawai_id);

        $query = $this->db->get();
        return $query->num_rows();
    }

    public function insert_pelatihan($data)
    {
        return $this->db->insert('t_pelatihan_pegawai', $data);
    }

    public function get_pelatihan_by_id($id)
    {
        $query = $this->db->get_where('t_pelatihan_pegawai', array('pelatihan_pegawai_id' => $id), 1,0);
        return $query->row();
    }

    public function update_pelatihan($data, $id)
    {
        $update = $this->db->update('t_pelatihan_pegawai', $data, array('pelatihan_pegawai_id' => $id));
        return $update;
    }

    public function delete_pelatihan($id)
    {
        return $this->db->delete('t_pelatihan_pegawai', array('pelatihan_pegawai_id' => $id));
    }
}