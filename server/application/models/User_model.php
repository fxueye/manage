<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends MY_Model
{
    const TABLE = 'user';
    const DB_KEY = 'comic';

    function __construct()
    {
        parent::__construct(self::DB_KEY);
        $this->table = self::TABLE;
        $this->load->model("user_role_model", "user_role");
    }

    function login($name, $password)
    {
        $user = $this->get(array(
            'name' => $name,
            'password' => $password
        ), true);
        return $user;
    }
    function batch_del($ids)
    {
        $this->start();
        $this->db->where_in('id', $ids);
        $this->db->delete($this->table);
        foreach ($ids as $id) {
            $this->user_role->del_by_user_id($id);
        }
        return $this->complete();
    }
    function list($page, $size, $name = "")
    {
        $offset = ($page - 1) * $size;
        $this->db->select("id,name,email,mobile,status,create_time,create_by,update_by,update_time,last_login_ip,last_login_time");
        $this->db->limit($size, $offset);
        if ($name != "") {
            $this->db->like("name", $name);
        }
        $query = $this->db->get($this->table);
        if ($query === false) {
            return false;
        }
        if ($query->num_rows() > 0) {
            $ret = $query->result_array();
            $count = count($ret);
            for ($i = 0; $i < $count; $i++) {
                $r = &$ret[$i];
                $role_ids = $this->get_role_ids($r['id']);
                $r['role_ids'] = $role_ids ? $role_ids : array();
            }
            return $ret;
        }
        return false;
    }


    public function get_role_ids($userId)
    {
        $roleIds = $this->user_role->get_role_ids($userId);
        if ($roleIds) {
            return $roleIds;
        }
        return false;
    }
    public function save_role_ids($userId, $roleIds, $user)
    {
        return $this->user_role->save_role_ids($userId, $roleIds, $user);
    }
}
