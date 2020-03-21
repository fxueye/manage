<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Role_model extends MY_Model
{
    const TABLE = 'role';
    const DB_KEY = 'comic';

    function __construct()
    {
        parent::__construct(self::DB_KEY);
        $this->table = self::TABLE;
        $this->load->model("role_menu_model", "role_menu");
        $this->load->model("user_role_model", "user_role");
    }
    function delete($id)
    {
        $this->start();
        $this->del($id);
        $this->role_menu->del_by_role_id($id);
        $this->user_role->del_by_role_id($id);
        return $this->complete();
    }
    function list($page = "", $size = "", $name = "")
    {
        $this->db->select("id,name,remark,create_time,create_by,update_by,update_time");
        if ($page != "" && $size != "") {
            $offset = ($page - 1) * $size;
            $this->db->limit($size, $offset);
        }
        if ($name != "") {
            $this->db->like("name", $name);
        }
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
