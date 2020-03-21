<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Log extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function list()
    {
        $name = $this->get_param('name');
        $page = $this->get_param('page', 1);
        $size = $this->get_param('size', 10);
        $count = $this->log_model->count();
        $users = $this->log_model->list($page, $size, $name);
        if ($count && $users) {
            $this->ret['total_rows'] = $count;
            $this->ret['logs'] = $users;
        } else {
            $this->ret['code'] = ErrorCode::ERR_SEARCH;
        }
    }
}
