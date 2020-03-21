<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Log_model extends MY_Model
{
    const TABLE = 'log';
    const DB_KEY = 'comic';

    function __construct()
    {
        parent::__construct(self::DB_KEY);
        $this->table = self::TABLE;
    }
    function list($page, $size, $name = "")
    {
        $offset = ($page - 1) * $size;
        $this->db->select("id,method,params,time,ip,create_by,create_time");
        $this->db->limit($size, $offset);
        if ($name != "") {
            $this->db->like("create_by", $name);
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
