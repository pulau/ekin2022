<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Validasi_skp_model extends CI_Model {
    var $column = array('nip','nama_pegawai','bagian_nama','jml_skpt');
    var $column_order = array('id_pegawai','nip','nama_pegawai','bagian_nama','jml_skpt');
    var $order = array('id_pegawai' => 'ASC');
    
    var $column2 = array('nip','skp','bagian_nama','qty','kualitas','waktu_efektif','waktu_total');
    var $column_order2 = array('skptahunan_id','nip','skp','bagian_nama','qty','kualitas','waktu_efektif','waktu_total');
    var $order2 = array('skptahunan_id' => 'ASC');
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_pegawai_list($kordinator_id){
        $current_year = date('Y');
//        $res = $this->db->query("");
        $this->db->from('(SELECT m_pegawai.id_pegawai, m_pegawai.nip, m_pegawai.nama_pegawai, m_bagian.bagian_nama,
                          (select count(m_skptahunan.skptahunan_id) FROM m_skptahunan WHERE m_skptahunan.id_pegawai = m_pegawai.id_pegawai AND m_skptahunan.tahun ='.$current_year.') AS jml_skpt
                          FROM m_pegawai
                          JOIN m_bagian ON m_pegawai.bagian = m_bagian.bagian_id
                          WHERE m_pegawai.status_pns ="NON PNS" AND m_pegawai.is_active = 1 AND
                              ( CASE 
                                WHEN m_pegawai.pj_cuti IS NULL THEN m_bagian.kordinator_id ='.$kordinator_id.'
                                    ELSE m_pegawai.pj_cuti = '.$kordinator_id.'
                           END)) a');
        
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
    
    public function count_pegawai_all($kordinator_id){
        $current_year = date('Y');
        $this->db->from('(SELECT m_pegawai.id_pegawai, m_pegawai.nip, m_pegawai.nama_pegawai, m_bagian.bagian_nama,
                          (select count(m_skptahunan.skptahunan_id) FROM m_skptahunan WHERE m_skptahunan.id_pegawai = m_pegawai.id_pegawai AND m_skptahunan.tahun ='.$current_year.') AS jml_skpt
                          FROM m_pegawai
                          JOIN m_bagian ON m_pegawai.bagian = m_bagian.bagian_id
                          WHERE m_pegawai.status_pns ="NON PNS" AND m_pegawai.is_active = 1 AND
                              ( CASE 
                                WHEN m_pegawai.pj_cuti IS NULL THEN m_bagian.kordinator_id ='.$kordinator_id.'
                                    ELSE m_pegawai.pj_cuti = '.$kordinator_id.'
                           END)) a');

        return $this->db->count_all_results();
    }
    
    public function count_pegawai_filtered($kordinator_id){
        $current_year = date('Y');
        $this->db->from('(SELECT m_pegawai.id_pegawai, m_pegawai.nip, m_pegawai.nama_pegawai, m_bagian.bagian_nama,
                          (select count(m_skptahunan.skptahunan_id) FROM m_skptahunan WHERE m_skptahunan.id_pegawai = m_pegawai.id_pegawai AND m_skptahunan.tahun ='.$current_year.') AS jml_skpt
                          FROM m_pegawai
                          JOIN m_bagian ON m_pegawai.bagian = m_bagian.bagian_id
                          WHERE m_pegawai.status_pns ="NON PNS" AND m_pegawai.is_active = 1 AND
                              ( CASE 
                                WHEN m_pegawai.pj_cuti IS NULL THEN m_bagian.kordinator_id ='.$kordinator_id.'
                                    ELSE m_pegawai.pj_cuti = '.$kordinator_id.'
                           END)) a');
        
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
    
    public function get_skptahunan_list($id_pegawai){
        $current_year = date('Y');
        $this->db->from('v_skptahunan');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->where('tahun', $current_year);
        $sub_query = $this->db->get_compiled_select();
        
        $this->db->from('('.$sub_query.') a');
        
        $i = 0;
        $search_value = $this->input->get('search');
        if($search_value !== false){
            foreach ($this->column2 as $item){
                ($i==0) ? $this->db->like($item, $search_value['value']) : $this->db->or_like($item, $search_value['value']);
                //$column[$i] = $item;
                $i++;
            }
        }
        $order_column = $this->input->get('order');
        if($order_column !== false){
            $this->db->order_by($this->column_order2[$order_column['0']['column']], $order_column['0']['dir']);
        } 
        else if(isset($this->order2)){
            $order = $this->order2;
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
    
    public function count_skptahunan_all($id_pegawai){
        $current_year = date('Y');
        $this->db->from('v_skptahunan');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->where('tahun', $current_year);

        return $this->db->count_all_results();
    }
    
    public function count_skptahunan_filtered($id_pegawai){
        $current_year = date('Y');
        $this->db->from('v_skptahunan');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->where('tahun', $current_year);
        $sub_query = $this->db->get_compiled_select();
        
        $this->db->from('('.$sub_query.') a');
        
        $i = 0;
        $search_value = $this->input->get('search');
        if($search_value !== false){
            foreach ($this->column2 as $item){
                ($i==0) ? $this->db->like($item, $search_value['value']) : $this->db->or_like($item, $search_value['value']);
                //$column[$i] = $item;
                $i++;
            }
        }
        
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    public function update_skpt($data, $id){
        $update = $this->db->update('m_skptahunan', $data, array('skptahunan_id' => $id));
        
        return $update;
    }
    
    //constraint to skp tahunan
    public function check_constraint_skpt($skptahunan_id){
        $check = $this->db->get_where('t_kinerja_aktifitas', array('skptahunan_id' => $skptahunan_id));
        
        return $check->num_rows();
    }
    
    public function delete_skpt($id){
        $delete = $this->db->delete('m_skptahunan', array('skptahunan_id' => $id));
        
        return $delete;
    }
}