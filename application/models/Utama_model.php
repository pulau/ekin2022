<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Utama_model extends CI_Model  {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function get_pegawai_by_nip($nip){
        $query = $this->db->get_where('v_pegawai', array('nip' => $nip), 1, 0);
        
        return $query->row();
    }
    
    public function get_bagian_by_id($id){
        $query = $this->db->get_where('m_bagian', array('bagian_id' => $id), 1, 0);
        
        return $query->row();
    }
    
    function get_list_menu($group_id, $module_id) {
    	$res = $this->db->query("SELECT DISTINCT x.id,x.name,x.definition,x.parent_id,x.url,x.icon,x.module_id,x.urutan_menu 
                                    FROM (SELECT * FROM aauth_perms) x, aauth_perm_to_group y 
                                    WHERE x.id = y.perm_id
                                    AND y.group_id IN(".$group_id.") AND x.module_id = ".$module_id." ORDER BY x.urutan_menu ASC");

    	if($res->num_rows()>0) {
            return $res->result_array();
    	}
    	return FALSE;
    }
}