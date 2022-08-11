<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Persetujuan_pj_model extends CI_Model {
    var $column = array('alasan','jeniscuti_nama','pengganti_nama','pegawai_nama');
    var $column_order = array('cuti_id','jeniscuti_nama','tgl_pengajuan','tgl_awal','tgl_akhir','alasan','pengganti_nama','review_status','approval_status');
    var $order = array('pegawai_nama' => 'ASC');
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_cuti_list($filter_bln, $pj_id){
        $this->db->from('v_cutipegawai');
        
        if(!empty($filter_bln)){
            $this->db->like('tgl_awal', date('Y-m', strtotime($filter_bln)),'after');
        }
        $this->db->where('kordinator_id', $pj_id);
        $this->db->where('approve_by_pj', 0);
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
    
    public function count_cuti_all($filter_bln, $pj_id){
        $this->db->from('v_cutipegawai');
        
        if(!empty($filter_bln)){
            $this->db->like('tgl_awal', date('Y-m', strtotime($filter_bln)),'after');
        }
        $this->db->where('kordinator_id', $pj_id);
        $this->db->where('approve_by_pj', 0);

        return $this->db->count_all_results();
    }
    
    public function count_cuti_filtered($filter_bln, $pj_id){
        $this->db->from('v_cutipegawai');
        
        if(!empty($filter_bln)){
            $this->db->like('tgl_awal', date('Y-m', strtotime($filter_bln)),'after');
        }
        $this->db->where('kordinator_id', $pj_id);
        $this->db->where('approve_by_pj', 0);
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
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    function get_pegawai_by_nip($nip){
        $query = $this->db->get_where('m_pegawai', array('nip' => $nip), 1, 0);
        
        return $query->row();
    }
    
    public function get_bagian_by_id($id){
        $query = $this->db->get_where('m_bagian', array('bagian_id' => $id), 1, 0);
        
        return $query->row();
    }
    
    function get_sisa_cuti($pegawai_id, $tempattugas){
        $current_year = date('Y');
        $this->db->select('SUM(jml_hari) AS jml');
        $this->db->from('t_cuti');
        $this->db->where('pegawai_id',$pegawai_id);
        $this->db->where('jenis_cuti',CUTI_TAHUNAN);
        $this->db->where('approval_status',1);
        $this->db->like('tgl_awal',$current_year,'before');
        $query2 = $this->db->get();
        $res = $query2->row();
        $jml_cuti = $res->jml;
        $jatah_cuti_per_tahun = $this->get_jumlah_cuti($tempattugas);
        $sisa_cuti = $jatah_cuti_per_tahun - $jml_cuti;
        
        return $sisa_cuti;
    }
    
    function get_jumlah_cuti($tempattugas){
        if($tempattugas == "KECAMATAN"){
            $query = $this->db->get_where('p_sysconf', array('sysconf_nama' => 'jatah_cuti_per_tahun_pkc'), 1, 0);
            $res = $query->row();
            $total_cuti = $res->sysconf_value;
        }else if($tempattugas == "KELURAHAN"){
            $query = $this->db->get_where('p_sysconf', array('sysconf_nama' => 'jatah_cuti_per_tahun_pkl'), 1, 0);
            $res = $query->row();
            $total_cuti = $res->sysconf_value;
        }else{
            $total_cuti = 0;
        }
        
        return $total_cuti;
    }
    
    public function list_jeniscuti(){
        $this->db->from('m_jeniscuti');

        $query = $this->db->get();
        return $query->result();
    }
    
    public function list_pegawai($id_pegawai){
        $this->db->select('id_pegawai,nama_pegawai');
        $this->db->from('m_pegawai');
        $this->db->where('id_pegawai <>', $id_pegawai);
        $query = $this->db->get();
        return $query->result();
    }
    
    public function insert_cuti($data){
        $insert = $this->db->insert('t_cuti',$data);
        
        return $insert;
    }
    
    public function get_cuti_by_id($id){
        $query = $this->db->get_where('v_cutipegawai', array('cuti_id' => $id), 1, 0);
        
        return $query->row();
    }
    
    public function update_cuti($data, $id){
        $update = $this->db->update('t_cuti', $data, array('cuti_id' => $id));
        
        return $update;
    }
}