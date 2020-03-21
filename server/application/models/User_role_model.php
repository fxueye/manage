<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_role_model extends MY_Model
{
    const TABLE = 'user_role';
    const DB_KEY = 'comic';

    function __construct()
    {
        parent::__construct(self::DB_KEY);
        $this->table = self::TABLE;
    }

    public function get_role_ids($user_id)
    {
        $ret = array();
        $data = $this->select(array(
            "role_id"
        ), array(
            "user_id" => $user_id
        ), false);
        if ($data) {
            foreach ($data as $d) {
                $ret[] = $d['role_id'];
            }
        }
        return $ret;
    }
    function del_by_user_id($userId)
    {
        return $this->db->delete($this->table, array('user_id' => $userId));
    }
    function del_by_role_id($roleId)
    {
        return $this->db->delete($this->table, array('role_id' => $roleId));
    }
    function save_role_ids($userId, $roleIds, $user)
    {
        // $table = $this->db->dbprefix($this->table);
        $this->start();
        // $this->db->query(sprintf("DELETE FROM %s WHERE user_id='%s'", $table, $userId));
        $this->db->delete($this->table, array("user_id" => $userId));

        foreach ($roleIds as $roleId) {
            // $this->db->query(sprintf("INSERT INTO %s (user_id,role_id,create_by,create_time,update_by,update_time) VALUES (%s,%s,'%s',%s,'%s',%s)", $table, $userId, $roleId, $user['name'], time(), $user['name'], time()));
            $data = array(
                'user_id' => $userId,
                'role_id' => $roleId,
                'create_by' => $user['name'],
                'create_time' => time(),
                'update_by' => $user['name'],
                'update_time' => time()
            );
            $this->add($data);
        }
        return $this->complete();
    }
}
