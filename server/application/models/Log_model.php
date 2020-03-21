<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Log_model extends MY_Model{
    const TABLE = 'log';
    const DB_KEY = 'comic';
  
    function __construct() {
        parent::__construct(self::DB_KEY);
        $this->table = self::TABLE;
    }    
    
}