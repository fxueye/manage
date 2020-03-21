<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model
{

    protected $CI;
    protected $zeit;
    protected $table;
    static private $DB_INSTANCE = array();

    function __construct($db_key = false)
    {
        parent::__construct();
        $this->CI = &get_instance();
        $this->CI->load->driver('cache', array(
            "adapter" => 'memcached',
            "backup" => 'file'
        ));
        // $this->CI->cache->memcached->is_supported();
        $this->zeit = time();
        if ($db_key) {
            $this->db = $this->get_db_instance($db_key);
        }
    }

    /**
     * 获取数据库对象
     */
    protected function get_db_instance($database)
    {
        if (empty(self::$DB_INSTANCE[$database])) {
            self::$DB_INSTANCE[$database] = $this->CI->load->database($database, true);
        }
        return self::$DB_INSTANCE[$database];
    }

    /**
     * 开始事务
     */
    function start()
    {
        $this->db->trans_start();
    }

    /**
     * 事务提交
     */
    function complete()
    {
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        return true;
    }

    function set_error($code)
    {
        return $this->CI->error->set_error($code);
    }
    function set_cache($key, $data, $expire)
    {
        return $this->cache->save($key, $data, $expire);
    }

    function get_cache($key)
    {
        return $this->cache->get($key);
    }

    function del_cache($key)
    {
        return $this->cache->delete($key);
    }

    function count()
    {
        $count = $this->db->count_all($this->table);
        return $count;
    }
    //重构 mysql 公共方法
    function get($where = null, $is_one = false)
    {
        $query = false;

        if (is_array($where)) {
            $query = $this->db->get_where($this->table, $where);
        } else {
            $query = $this->db->get($this->table);
        }
        if ($query === false) {
            return false;
        }
        if ($query->num_rows() > 0) {
            $ret = $query->result_array();
            return $is_one ? $ret[0] : $ret;
        }
        return false;
    }
    function select($select = null, $where = null, $is_one = false)
    {
        if (is_array($select)) {
            $this->db->select($select);
        }
        return $this->get($where, $is_one);
    }


    function update($where, $data)
    {
        if (is_array($where)) {
            return $this->db->update($this->table, $data, $where);
        } else {
            return $this->db->update($this->table, $data, array('id' => $where));
        }
    }

    function del($where)
    {
        if (is_array($where)) {
            return $this->db->delete($this->table, $where);
        } else {
            return $this->db->delete($this->table, array('id' => $where));
        }
    }

    function add($data)
    {
        $ret = $this->db->insert($this->table, $data);
        $id = $this->db->insert_id();
        $data['id'] = $id;
        if ($ret) {
            return $data;
        }
        return false;
    }
}
