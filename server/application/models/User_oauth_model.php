<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_oauth_model extends MY_Model{
    const TABLE = 'user_oauth';
    const DB_KEY = 'comic';
  
    function __construct() {
        parent::__construct(self::DB_KEY);
        $this->table = self::TABLE;
    }    
}