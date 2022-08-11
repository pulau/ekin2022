<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cpanel_model extends CI_Model {
    public function _constract(){
        parent::__construct();
    }
    
    function get_list_module($group_id) {
        $res = $this->db->query("SELECT DISTINCT x.module_id, z.label, z.modul_icon, z.modul_url, z.modul_urutan, z.modul_kategori 
                                    FROM (SELECT * FROM aauth_perms) x, aauth_perm_to_group y, aauth_modules z 
                                    WHERE x.id = y.perm_id
                                    AND y.group_id IN(".$group_id.")  AND x.module_id = z.id ORDER BY z.modul_urutan ASC");
        
        if($res->num_rows()>0) {
            $list = $res->result_array();
            return $list;
        }
    	return FALSE;
    }
}

