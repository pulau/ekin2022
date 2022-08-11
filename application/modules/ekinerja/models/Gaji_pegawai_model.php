<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



/*
=IF(Y89<=50000000;(Y89*5%);IF(Y89<=250000000;(2500000+(Y89-50000000)*15%);IF(Y89<=500000000;(2500000+3750000+(Y89-300000000)*30%))))
=IF(J38<=50000000;(J38*5%);IF(J38<=250000000;(2500000+(J38-50000000)*15%);IF(J38<=500000000;(2500000+3750000+(J38-300000000)*30%))))
jika PKP pertahun <= 50.000.000 	= PKP * 5%
	JIKA PKP <= 250.000.000			= 2.500.000 + (PKP - 50.000.000)*15%
		JIKA PKP <= 500.000.000		= 2.500.000 + 3750000 + (PKP- 300.000.000) * 30%
 */

class Gaji_pegawai_model extends CI_Model {

	var $column = array('gaji_pegawai_id','nip','nama_pegawai','masakerja','gapok','tun_susi','tun_anak','jumlah','pph21','terima_gaji');
    var $order = array('gaji_pegawai_id' => 'DESC');

	public function __construct()
	{
		parent::__construct();
		
	}
	
	/*
		ambil semua gaji pegawai dari tabel t_gaji_pegawai
	 */
	public function get_gaji_pegawai_list($bulan)
	{
		if(empty($bulan)){
            $curr_month = date('Ym');
        }else{
            $curr_month = date('Ym', strtotime($bulan));
        }
        $this->db->select('t_gaji_pegawai.*,m_pegawai.nip,m_pegawai.nama_pegawai');
        $this->db->from('t_gaji_pegawai');
        $this->db->join('m_pegawai','t_gaji_pegawai.pegawai_id = m_pegawai.id_pegawai','inner');
        $this->db->where('t_gaji_pegawai.bulan', $curr_month);
        $this->db->where('m_pegawai.is_active', 1);
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

	public function get_gaji_pegawai_all($bulan){
        if(empty($bulan)){
            $curr_month = date('Ym');
        }else{
            $curr_month = date('Ym', strtotime($bulan));
        }
        $this->db->select('t_gaji_pegawai.*,m_pegawai.nip,m_pegawai.nama_pegawai');
        $this->db->from('t_gaji_pegawai');
        $this->db->join('m_pegawai','t_gaji_pegawai.pegawai_id = m_pegawai.id_pegawai','inner');
        $this->db->where('t_gaji_pegawai.bulan', $curr_month);
        $this->db->where('m_pegawai.is_active', 1);

        return $this->db->count_all_results();
    }

    public function count_gaji_pegawai_filtered($bulan){
        if(empty($bulan)){
            $curr_month = date('Ym');
        }else{
            $curr_month = date('Ym', strtotime($bulan));
        }
        $this->db->select('t_gaji_pegawai.*,m_pegawai.nip,m_pegawai.nama_pegawai');
        $this->db->from('t_gaji_pegawai');
        $this->db->join('m_pegawai','t_gaji_pegawai.pegawai_id = m_pegawai.id_pegawai','inner');
        $this->db->where('t_gaji_pegawai.bulan', $curr_month);
        $this->db->where('m_pegawai.is_active', 1);
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
	/*
	return of array gaji pokok by pergub 21
	 */
	public function get_gaji_pegawai_by_id($bulan = null, $pegawai_id)
	{
		# gaji pergub 21(masa kerja -> pendidikan -> status KO) * status perkawinan
		if (empty($bulan)) {
			$curr_month = date('Y-m');
		} else {
			$curr_month = date('Y-m', strtotime($bulan));
		}
		// get nominal gaji pegawai dengan pendidikan
		$this->db->select('m_pegawai.nip, m_pegawai.nama_pegawai, m_pegawai.tgl_masuk, m_pendidikan.pendidikan_nama, m_gaji.nominal_gaji, m_statuspegawai.statuspegawai_nama');
		$this->db->from('m_pendidikan');
		$this->db->join('m_pegawai', 'm_pegawai.pendidikan = m_pendidikan.pendidikan_id', 'inner');
		$this->db->join('m_statuspegawai', 'm_pegawai.status = m_statuspegawai.statuspegawai_id', 'inner');
		$this->db->join('m_gaji', 'm_gaji.pendidikan = m_pendidikan.pendidikan_id', 'inner');
		$this->db->where('m_pegawai.id_pegawai', $pegawai_id);
		$this->db->where('status_pns', 'NON PNS');
		$this->db->where('is_active', 1);
		$query = $this->db->get();
		return $query->result();
	}

	/*
	hitung jumlah gaji pegawai
	 */
	public function count_gaji_pegawai_all($id_pegawai, $bulan)
	{
		$data = array('bulan' => $bulan, 'pegawai_id' => $id_pegawai );
		$query = $this->db->get_where('t_gaji_pegawai', $data, 1, 0);
		return $query->num_rows();
	}
	/*
	untuk menghitung pajak pegawai
	 */
	public function get_pajak_pegawai_by_id($id_pegawai)
	{
		# code...
		$this->db->select('m_pegawai.*, m_pajak.pajak_nama, m_pajak.ptkp');
		$this->db->from('m_pajak');
		$this->db->join('m_pegawai', 'm_pegawai.pajak = m_pajak.pajak_id');
		$this->db->where('m_pegawai.id_pegawai', $id_pegawai);
		$query = $this->db->get();
		return $query->result();
	}
	/*
	untuk menghitung tkd dilihat dari capaian kinerjanya perbulan
	 */
	public function get_capaian_pegawai($id_pegawai, $bulan)
	{
		$this->db->select('t_prestasi_kerja.*');	
		$this->db->from('t_prestasi_kerja');	
		$this->db->where('t_prestasi_kerja.pegawai_id', $id_pegawai);
		$this->db->where('t_prestasi_kerja.bulan', $bulan);
		$query = $this->db->get();
		return $query->result();
	}

	/*
	untuk menghitung tdk dilihat dari rumpun jabatannya
	 */
	public function get_rumpun_pegawai($id_pegawai)
	{
		$this->db->select('m_rumpun.rumpun_nilai');
		$this->db->from('m_rumpun');
		$this->db->join('m_pegawai', 'm_pegawai.rumpun = m_rumpun.rumpun_id');
		$this->db->where('m_pegawai.id_pegawai', $id_pegawai);
		$query = $this->db->get();
		return $query->result();
	}
	/*
		ambil semua pegawai non pns
	 */
	public function get_pegawai_nonpns(){
        $this->db->from('m_pegawai');
        $this->db->where('status_pns', "NON PNS");
        $this->db->where('is_active', 1);
        $query = $this->db->get();
        
        return $query->result();
    }

    /*
    insert data gaji 
     */
    public function insert_into_gaji_pegawai($data)
    {
    	return $this->db->insert('t_gaji_pegawai', $data);
    }

    /*
    update data gaji
     */
    public function update_gaji_pegawai($data, $bulan, $id_pegawai)
    {
    	$condition = array('bulan' => $bulan, 'pegawai_id' => $id_pegawai);
    	return $this->db->update('t_gaji_pegawai', $data, $condition);
    }

    public function get_gaji_pegawai_excel($filter_bln){
        $bulan = date('Ym', strtotime($filter_bln));
        $this->db->select('t_gaji_pegawai.*,m_pegawai.nip,m_pegawai.nama_pegawai');
        $this->db->from('t_gaji_pegawai');
        $this->db->join('m_pegawai','t_gaji_pegawai.pegawai_id = m_pegawai.id_pegawai','inner');
        $this->db->where('t_gaji_pegawai.bulan', $bulan);
        $this->db->where('m_pegawai.is_active', 1);
        
        $query = $this->db->get();
        
        return $query->result();
    }
}

/* End of file Gaji_pegawai_model.php */
/* Location: ./application/models/Gaji_pegawai_model.php */