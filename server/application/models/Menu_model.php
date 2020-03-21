<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Menu_model extends MY_Model
{
    const TABLE = 'menu';
    const DB_KEY = 'comic';

    function __construct()
    {
        parent::__construct(self::DB_KEY);
        $this->table = self::TABLE;
    }
    function get_menus($menuIds)
    {
        $this->db->select('id,name,parent_id,url,perms,type,icon,sort');
        $this->db->where_in("id", $menuIds);
        $query = $this->db->get($this->table);
        if ($query === false) {
            return false;
        }
        if ($query->num_rows() > 0) {
            $ret = $query->result_array();
            return $ret;
        }
        return false;
    }
    function has_children($id)
    {
        $this->db->where('parent_id', $id);
        $this->db->from($this->table);
        $count = $this->db->count_all_results();
        return $count > 0;
    }
    function list()
    {

        $this->db->select("id,name,parent_id,perms,url,type,icon,sort,create_time,create_by,update_by,update_time");
        $query = $this->db->get($this->table);
        if ($query === false) {
            return false;
        }
        if ($query->num_rows() > 0) {
            $ret = $query->result_array();
            return $ret;
        }
        return false;
    }
}
