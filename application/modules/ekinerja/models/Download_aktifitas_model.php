<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class download_aktifitas_model extends CI_Model {
    var $column = array('tanggal_aktifitas','skp','uraian','jam_mulai','jam_akhir', 'id_pegawai','nama_pegawai', 'bagian_nama');
    var $order = array('tanggal_aktifitas' => 'DESC');
    
    public function __construct() {
        parent::__construct();
    }
    /*
    get kinerja pegawai in table v_aktifitas
    filter by id_pegawai and selected $bulan
    */
    public function get_download_aktifitas_list($id_pegawai,$bulan){
        if(empty($bulan)){
            $curr_month = date('Ym');
        }else{
            $curr_month = date('Ym', strtotime($bulan));
        }
        $this->db->select('tanggal_aktifitas,skp, uraian ,jam_mulai,jam_akhir, id_pegawai,nama_pegawai, bagian_nama');
        $this->db->from('v_aktifitas');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->where('is_validasi', 1);
        $this->db->where('tgl',$curr_month);
        $this->db->order_by('tanggal_aktifitas', 'DESC');
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
    /*
    count jumlah kinerja pegawai in table v_aktifitas
    filter by id_pegawai and selected $bulan
    default $bulan = undefined
    */
    public function count_download_aktifitas_all($id_pegawai,$bulan){
        if(empty($bulan)){
            $curr_month = date('Ym');
        }else{
            $curr_month = date('Ym', strtotime($bulan));
        }
        $this->db->select('tanggal_aktifitas,skp, uraian ,jam_mulai,jam_akhir, id_pegawai,nama_pegawai, bagian_nama');
        $this->db->from('v_aktifitas');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->where('is_validasi', 1);
        $this->db->where('tgl',$curr_month);
        return $this->db->count_all_results();
    }

    /*
    get kinerja pegawai in table v_aktifitas
    filter by id_pegawai and selected $bulan
    default $bulan = undefined
    */
    public function count_download_aktifitas_filtered($id_pegawai,$bulan){
        if(empty($bulan)){
            $curr_month = date('Ym');
        }else{
            $curr_month = date('Ym', strtotime($bulan));
        }
        $this->db->select('tanggal_aktifitas,skp, uraian ,jam_mulai,jam_akhir, id_pegawai,nama_pegawai, bagian_nama');
        $this->db->from('v_aktifitas');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->where('is_validasi', 1);
        $this->db->where('tgl',$curr_month);
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


    public function get_pegawai_by_nip($nip){
        $query = $this->db->get_where('v_pegawai', array('nip' => $nip, 'is_active' => 1), 1, 0);
        
        return $query->row();
    }


    /*
    export kinerja pegawai from table v_aktifitas
    filter by id_pegawai and selected $bulan
    default $bulan = undefined
    */
    public function get_kinerja_pegawai_excel($id_pegawai,$filter_bln){
        $this->load->model('Utama_model');
        $users                = $this->aauth->get_user();
        $pegawai = $this->Utama_model->get_pegawai_by_nip($users->nip);


        if(empty($filter_bln)){
            $curr_month = date('Ym');
        }else{
            $curr_month = date('Ym', strtotime($filter_bln));
        }
        // echo $curr_month; 
        // die;
        // $bulan = date('Ym', strtotime($filter_bln));
        $this->db->select('tanggal_aktifitas,skp, uraian ,jam_mulai,jam_akhir, id_pegawai,nama_pegawai, bagian_nama');
        $this->db->from('v_aktifitas');
        $this->db->where('id_pegawai', $id_pegawai);
        $this->db->where('is_validasi', 1);
        $this->db->where('tgl',$curr_month);
        $this->db->order_by('tanggal_aktifitas', 'DESC');
        $query = $this->db->get();

        return $query->result();
    }
}