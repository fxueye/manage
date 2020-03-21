<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Role_menu_model extends MY_Model
{
    const TABLE = 'role_menu';
    const DB_KEY = 'comic';

    function __construct()
    {
        parent::__construct(self::DB_KEY);
        $this->table = self::TABLE;
    }
    function get_menu_ids($roleIds)
    {
        $ret = array();
        $this->db->select('menu_id');
        if (is_array($roleIds)) {
            $this->db->where_in('role_id', $roleIds);
        } else {
            $this->db->where('role_id', $roleIds);
        }
        $query = $this->db->get($this->table);
        if ($query === false) {
            return false;
        }
        if ($query->num_rows() > 0) {
            $data = $query->result_array();
            if ($data) {
                foreach ($data as $value) {
                    if (!in_array($value['menu_id'], $ret)) {
                        $ret[] = $value['menu_id'];
                    }
                }
            }
        }

        return $ret;
    }
    function del_by_role_id($roleId)
    {
        return $this->db->delete($this->table, array('role_id' => $roleId));
    }
    function save_menu_ids($role_id, $menu_ids, $user)
    {
        $table = $this->db->dbprefix($this->table);
        $this->start();
        $this->db->query(sprintf("DELETE FROM %s WHERE role_id='%s'", $table, $role_id));

        foreach ($menu_ids as $menu_id) {
            $this->db->query(sprintf("INSERT INTO %s (role_id,menu_id,create_by,create_time,update_by,update_time) VALUES (%s,%s,'%s',%s,'%s',%s)", $table, $role_id, $menu_id, $user['name'], time(), $user['name'], time()));
        }
        return $this->complete();
    }
}
