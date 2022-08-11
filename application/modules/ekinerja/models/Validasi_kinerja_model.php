<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Validasi_kinerja_model extends CI_Model {
    var $column = array('nip','nama_pegawai');
    //var $column_order = array('id_pegawai','nip','nama_pegawai','jml_aktifitas','sudah_validasi','blm_validasi','aktifitas_ditolak');
    //var $order = array('id_pegawai' => 'ASC');
    var $column2 = array('nip','nama_pegawai','rata_rata','total_nilai','persen_nilai');
    var $column_order2 = array('id_pegawai','nip','nama_pegawai','total_nilai','persen_nilai');
    var $order2 = array('id_pegawai' => 'ASC');
    
    var $column3 = array('skp','waktu_efektif','jumlah','point');
    var $column_order3 = array('aktifitas_id','tanggal_aktifitas','skp','is_validasi','waktu_efektif','jumlah','point');
    var $order3 = array('skptahunan_id' => 'ASC');
    
    public function __construct() {
        parent::__construct();
    }
    
    public function count_jml_aktifitas($pegawai_id, $filter_bln){
        if(empty($filter_bln)){
            $curr_month = date('Y-m');
        }else{
            $curr_month = date('Y-m', strtotime($filter_bln));
        }
        $this->db->select('count(t_kinerja_aktifitas.aktifitas_id) as jml_aktifitas');
        $this->db->from('t_kinerja_aktifitas');
        $this->db->where('t_kinerja_aktifitas.id_pegawai',$pegawai_id);
        $this->db->like('t_kinerja_aktifitas.tanggal_aktifitas',$curr_month,'after');
        $query1 = $this->db->get();
        $res = $query1->row();
        $jml_aktifitas = $res->jml_aktifitas;
        
        return $jml_aktifitas;
    }
    
    public function count_sudah_validasi($pegawai_id, $filter_bln){
        if(empty($filter_bln)){
            $curr_month = date('Y-m');
        }else{
            $curr_month = date('Y-m', strtotime($filter_bln));
        }
        $this->db->select('count(t_kinerja_aktifitas.aktifitas_id) as sudah_validasi');
        $this->db->from('t_kinerja_aktifitas');
        $this->db->where('t_kinerja_aktifitas.id_pegawai',$pegawai_id);
        $this->db->where('t_kinerja_aktifitas.is_validasi',1);
        $this->db->like('t_kinerja_aktifitas.tanggal_aktifitas',$curr_month,'after');
        $query1 = $this->db->get();
        $res = $query1->row();
        $sudah_validasi = $res->sudah_validasi;
        
        return $sudah_validasi;
    }
    
    public function count_blm_validasi($pegawai_id, $filter_bln){
        if(empty($filter_bln)){
            $curr_month = date('Y-m');
        }else{
            $curr_month = date('Y-m', strtotime($filter_bln));
        }
        $this->db->select('count(t_kinerja_aktifitas.aktifitas_id) as blm_validasi');
        $this->db->from('t_kinerja_aktifitas');
        $this->db->where('t_kinerja_aktifitas.id_pegawai',$pegawai_id);
        $this->db->where('t_kinerja_aktifitas.is_validasi',0);
        $this->db->like('t_kinerja_aktifitas.tanggal_aktifitas',$curr_month,'after');
        $query1 = $this->db->get();
        $res = $query1->row();
        $blm_validasi = $res->blm_validasi;
        
        return $blm_validasi;
    }
    
    public function count_aktifitas_ditolak($pegawai_id, $filter_bln){
        if(empty($filter_bln)){
            $curr_month = date('Y-m');
        }else{
            $curr_month = date('Y-m', strtotime($filter_bln));
        }
        $this->db->select('count(t_kinerja_aktifitas.aktifitas_id) as aktifitas_ditolak');
        $this->db->from('t_kinerja_aktifitas');
        $this->db->where('t_kinerja_aktifitas.id_pegawai',$pegawai_id);
        $this->db->where('t_kinerja_aktifitas.is_validasi',2);
        $this->db->like('t_kinerja_aktifitas.tanggal_aktifitas',$curr_month,'after');
        $query1 = $this->db->get();
        $res = $query1->row();
        $aktifitas_ditolak = $res->aktifitas_ditolak;
        
        return $aktifitas_ditolak;
    }
    
    public function get_validasi_aktifitas_list($filter_bln, $kordinator_id){
        if(empty($filter_bln)){
            $curr_month = date('Y-m');
        }else{
            $curr_month = date('Y-m', strtotime($filter_bln));
        }
        
        $this->db->from('(SELECT m_pegawai.id_pegawai, m_pegawai.nip, m_pegawai.nama_pegawai
                          FROM m_pegawai
                          JOIN m_bagian ON m_pegawai.bagian = m_bagian.bagian_id
                          WHERE m_pegawai.status_pns ="NON PNS" AND m_pegawai.is_active = 1 AND
                          m_bagian.kordinator_id ='.$kordinator_id.') a');
        
        $i = 0;
        $search_value = $this->input->get('search');
        if($search_value !== false){
            foreach ($this->column as $item){
                ($i==0) ? $this->db->like($item, $search_value['value']) : $this->db->or_like($item, $search_value['value']);
                //$column[$i] = $item;
                $i++;
            }
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
    
    public function count_validasi_aktifitas_all($filter_bln, $kordinator_id){
        if(empty($filter_bln)){
            $curr_month = date('Y-m');
        }else{
            $curr_month = date('Y-m', strtotime($filter_bln));
        }
        
        $this->db->from('(SELECT m_pegawai.id_pegawai, m_pegawai.nip, m_pegawai.nama_pegawai
                          FROM m_pegawai
                          JOIN m_bagian ON m_pegawai.bagian = m_bagian.bagian_id
                          WHERE m_pegawai.status_pns ="NON PNS" AND m_pegawai.is_active = 1 AND
                          m_bagian.kordinator_id ='.$kordinator_id.') a');
//                          ( CASE 
//                                WHEN m_pegawai.pj_cuti IS NULL THEN m_bagian.kordinator_id ='.$kordinator_id.'
//                                    ELSE m_pegawai.pj_cuti = '.$kordinator_id.'
//                           END)) a');

        return $this->db->count_all_results();
    }
    
    public function count_validasi_aktifitas_filtered($filter_bln, $kordinator_id){
        if(empty($filter_bln)){
            $curr_month = date('Y-m');
        }else{
            $curr_month = date('Y-m', strtotime($filter_bln));
        }

        $this->db->from('(SELECT m_pegawai.id_pegawai, m_pegawai.nip, m_pegawai.nama_pegawai
                          FROM m_pegawai
                          JOIN m_bagian ON m_pegawai.bagian = m_bagian.bagian_id
                          WHERE m_pegawai.status_pns ="NON PNS" AND m_pegawai.is_active = 1 AND
                          m_bagian.kordinator_id ='.$kordinator_id.') a');
//                          ( CASE 
//                                WHEN m_pegawai.pj_cuti IS NULL THEN m_bagian.kordinator_id ='.$kordinator_id.'
//                                    ELSE m_pegawai.pj_cuti = '.$kordinator_id.'
//                           END)) a');
        
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
    
    public function get_validasi_prilaku_list($filter_bln, $kordinator_id){
       if(empty($filter_bln)){
            $curr_month = date('Ym');
        }else{
            $curr_month = date('Ym', strtotime($filter_bln));
        }
        $this->db->from('(SELECT m_pegawai.id_pegawai, m_pegawai.nip, m_pegawai.nama_pegawai,
                            (SELECT id_prilakukerja 
                          FROM t_prilakukerja WHERE t_prilakukerja.id_pegawai = m_pegawai.id_pegawai AND t_prilakukerja.bulan like "'.$curr_month.'") as id_prilakukerja,
                          (SELECT (orientasi_pelayanan+integrasi+komitmen+disiplin+kerjasama+kepemimpinan) 
                          FROM t_prilakukerja WHERE t_prilakukerja.id_pegawai = m_pegawai.id_pegawai AND t_prilakukerja.bulan like "'.$curr_month.'") as total_nilai,
                              (SELECT ((orientasi_pelayanan+integrasi+komitmen+disiplin+kerjasama+kepemimpinan)/5) 
                          FROM t_prilakukerja WHERE t_prilakukerja.id_pegawai = m_pegawai.id_pegawai AND t_prilakukerja.bulan like "'.$curr_month.'") as rata_rata,
                              (SELECT (((orientasi_pelayanan+integrasi+komitmen+disiplin+kerjasama+kepemimpinan)/5)*0.1) 
                          FROM t_prilakukerja WHERE t_prilakukerja.id_pegawai = m_pegawai.id_pegawai AND t_prilakukerja.bulan like "'.$curr_month.'") as persen_nilai
                          FROM m_pegawai
                          JOIN m_bagian ON m_pegawai.bagian = m_bagian.bagian_id
                           WHERE m_pegawai.status_pns ="NON PNS" AND m_pegawai.is_active = 1 AND
                           m_bagian.kordinator_id ='.$kordinator_id.') a');
//                          ( CASE 
//                                WHEN m_pegawai.pj_cuti IS NULL THEN m_bagian.kordinator_id ='.$kordinator_id.'
//                                    ELSE m_pegawai.pj_cuti = '.$kordinator_id.'
//                           END)) a');
        
        
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
    
    public function count_validasi_prilaku_all($filter_bln, $kordinator_id){
        if(empty($filter_bln)){
            $curr_month = date('Ym');
        }else{
            $curr_month = date('Ym', strtotime($filter_bln));
        }
        $this->db->from('(SELECT m_pegawai.id_pegawai, m_pegawai.nip, m_pegawai.nama_pegawai,
                            (SELECT id_prilakukerja 
                          FROM t_prilakukerja WHERE t_prilakukerja.id_pegawai = m_pegawai.id_pegawai AND t_prilakukerja.bulan like "'.$curr_month.'") as id_prilakukerja,
                          (SELECT (orientasi_pelayanan+integrasi+komitmen+disiplin+kerjasama+kepemimpinan) 
                          FROM t_prilakukerja WHERE t_prilakukerja.id_pegawai = m_pegawai.id_pegawai AND t_prilakukerja.bulan like "'.$curr_month.'") as total_nilai,
                              (SELECT ((orientasi_pelayanan+integrasi+komitmen+disiplin+kerjasama+kepemimpinan)/5) 
                          FROM t_prilakukerja WHERE t_prilakukerja.id_pegawai = m_pegawai.id_pegawai AND t_prilakukerja.bulan like "'.$curr_month.'") as rata_rata,
                              (SELECT (((orientasi_pelayanan+integrasi+komitmen+disiplin+kerjasama+kepemimpinan)/5)*0.1) 
                          FROM t_prilakukerja WHERE t_prilakukerja.id_pegawai = m_pegawai.id_pegawai AND t_prilakukerja.bulan like "'.$curr_month.'") as persen_nilai
                          FROM m_pegawai
                          JOIN m_bagian ON m_pegawai.bagian = m_bagian.bagian_id
                           WHERE m_pegawai.status_pns ="NON PNS" AND m_pegawai.is_active = 1 AND
                           m_bagian.kordinator_id ='.$kordinator_id.') a');
//                          ( CASE 
//                                WHEN m_pegawai.pj_cuti IS NULL THEN m_bagian.kordinator_id ='.$kordinator_id.'
//                                    ELSE m_pegawai.pj_cuti = '.$kordinator_id.'
//                           END)) a');

        return $this->db->count_all_results();
    }
    
    public function count_validasi_prilaku_filtered($filter_bln, $kordinator_id){
        if(empty($filter_bln)){
            $curr_month = date('Ym');
        }else{
            $curr_month = date('Ym', strtotime($filter_bln));
        }
        $this->db->from('(SELECT m_pegawai.id_pegawai, m_pegawai.nip, m_pegawai.nama_pegawai,
                            (SELECT id_prilakukerja 
                          FROM t_prilakukerja WHERE t_prilakukerja.id_pegawai = m_pegawai.id_pegawai AND t_prilakukerja.bulan like "'.$curr_month.'") as id_prilakukerja,
                          (SELECT (orientasi_pelayanan+integrasi+komitmen+disiplin+kerjasama+kepemimpinan) 
                          FROM t_prilakukerja WHERE t_prilakukerja.id_pegawai = m_pegawai.id_pegawai AND t_prilakukerja.bulan like "'.$curr_month.'") as total_nilai,
                              (SELECT ((orientasi_pelayanan+integrasi+komitmen+disiplin+kerjasama+kepemimpinan)/5) 
                          FROM t_prilakukerja WHERE t_prilakukerja.id_pegawai = m_pegawai.id_pegawai AND t_prilakukerja.bulan like "'.$curr_month.'") as rata_rata,
                              (SELECT (((orientasi_pelayanan+integrasi+komitmen+disiplin+kerjasama+kepemimpinan)/5)*0.1) 
                          FROM t_prilakukerja WHERE t_prilakukerja.id_pegawai = m_pegawai.id_pegawai AND t_prilakukerja.bulan like "'.$curr_month.'") as persen_nilai
                          FROM m_pegawai
                          JOIN m_bagian ON m_pegawai.bagian = m_bagian.bagian_id
                           WHERE m_pegawai.status_pns ="NON PNS" AND m_pegawai.is_active = 1 AND
                           m_bagian.kordinator_id ='.$kordinator_id.') a');
//                          ( CASE 
//                                WHEN m_pegawai.pj_cuti IS NULL THEN m_bagian.kordinator_id ='.$kordinator_id.'
//                                    ELSE m_pegawai.pj_cuti = '.$kordinator_id.'
//                           END)) a');
        
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
    
    public function insert_prilaku($data){
        $insert = $this->db->insert('t_prilakukerja',$data);
        
        return $insert;
    }
    
    public function get_aktifitas_list($id_pegawai, $filter_bln){
        if(empty($filter_bln)){
            $curr_month = date('Y-m');
        }else{
            $curr_month = date('Y-m', strtotime($filter_bln));
        }
        
        $this->db->from('v_aktifitas');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->like('tanggal_aktifitas',$curr_month,'after');
        $sub_query = $this->db->get_compiled_select();
        
        $this->db->from('('.$sub_query.') a');
        
        $i = 0;
        $search_value = $this->input->get('search');
        if($search_value !== false){
            foreach ($this->column3 as $item){
                ($i==0) ? $this->db->like($item, $search_value['value']) : $this->db->or_like($item, $search_value['value']);
                //$column[$i] = $item;
                $i++;
            }
        }
        $order_column = $this->input->get('order');
        if($order_column !== false){
            $this->db->order_by($this->column_order3[$order_column['0']['column']], $order_column['0']['dir']);
        } 
        else if(isset($this->order3)){
            $order = $this->order3;
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
    
    public function count_aktifitas_all($id_pegawai, $filter_bln){
        if(empty($filter_bln)){
            $curr_month = date('Y-m');
        }else{
            $curr_month = date('Y-m', strtotime($filter_bln));
        }
        $this->db->from('v_aktifitas');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->like('tanggal_aktifitas',$curr_month,'after');

        return $this->db->count_all_results();
    }
    
    public function count_aktifitas_filtered($id_pegawai, $filter_bln){
        if(empty($filter_bln)){
            $curr_month = date('Y-m');
        }else{
            $curr_month = date('Y-m', strtotime($filter_bln));
        }
        
        $this->db->from('v_aktifitas');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->like('tanggal_aktifitas',$curr_month,'after');
        $sub_query = $this->db->get_compiled_select();
        
        $this->db->from('('.$sub_query.') a');
        
        $i = 0;
        $search_value = $this->input->get('search');
        if($search_value !== false){
            foreach ($this->column3 as $item){
                ($i==0) ? $this->db->like($item, $search_value['value']) : $this->db->or_like($item, $search_value['value']);
                //$column[$i] = $item;
                $i++;
            }
        }
        
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    public function get_aktifitas_id($id){
        $query = $this->db->get_where('t_kinerja_aktifitas', array('aktifitas_id' => $id), 1, 0);
        
        return $query->row();
    }
    
    public function update_status_validasi($data, $id){
        $update = $this->db->update('t_kinerja_aktifitas', $data, array('aktifitas_id' => $id));
        
        return $update;
    }
    
    public function get_prilaku_id($id){
        $query = $this->db->get_where('t_prilakukerja', array('id_prilakukerja' => $id), 1, 0);
        
        return $query->row();
    }
    
    public function update_prilaku($data, $id){
        $update = $this->db->update('t_prilakukerja', $data, array('id_prilakukerja' => $id));
        
        return $update;
    }
    
    public function update_prilaku_by_id_pegawai($data, $id_pegawai, $curr_month){
        $update = $this->db->update('t_prilakukerja', $data, array('id_pegawai' => $id_pegawai, 'bulan' => $curr_month));
        
        return $update;
    }
    
    public function check_constraint_prilaku($id_pegawai, $curr_month){
        $check = $this->db->get_where('t_prilakukerja', array('id_pegawai' => $id_pegawai, 'bulan' => $curr_month));
        
        return $check->num_rows();
    }
}