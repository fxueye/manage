<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Menu extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("menu_model", "menu");
        $this->load->model("user_model", "user");
    }
    public function list()
    {
        $menus = $this->menu->list();
        if ($menus) {
            $this->ret['menus'] = $menus;
        } else {
            $this->ret['code'] = ErrorCode::ERR_SEARCH;
        }
    }
    public function del()
    {

        $id = $this->get_param('id');
        if (!$this->menu->has_children($id)) {
            $ret = $this->menu->del($id);
            if ($ret) {
                $this->ret['id'] = $id;
            } else {
                $this->ret['code'] = ErrorCode::ERR_FAIL;
            }
        } else {
            $this->ret['code'] = ErrorCode::ERR_HAS_CHILDREN;
        }
    }

    public function save()
    {
        $params = $this->params();
        $id = isset($params['id']) ? $params['id'] : "";
        $user = $this->user->get(array('id' => $this->payload->id), true);
        if ($id == "") { //新增
            if (isset($params['id'])) {
                unset($params['id']);
            }
            $params['create_time'] = time();
            $params['update_time'] = time();
            $params['create_by'] = $user['name'];
            $params['update_by'] = $user['name'];

            $ret = $this->menu->add($params);
            if ($ret) {
                $this->ret['menu'] = $ret;
            } else {
                $this->ret['code'] = ErrorCode::ERR_FAIL;
            }
        } else { //更新
            $params['update_time'] = time();
            $params['update_by'] = $user['name'];
            $ret = $this->menu->update($id, $params);
            if ($ret) {
                $this->ret['menu'] = $params;
            }
        }
    }
}
