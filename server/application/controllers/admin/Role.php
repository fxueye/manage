<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Role extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("role_model", "role");
        $this->load->model("user_model", "user");
        $this->load->model("role_menu_model", "role_menu");
    }
    public function list()
    {
        $name = $this->get_param('name', '');
        $page = $this->get_param('page', '');
        $size = $this->get_param('size', '');
        $count = $this->role->count();
        $roles = $this->role->list($page, $size, $name);
        if ($count && $roles) {
            $this->ret['total_rows'] = $count;
            $this->ret['roles'] = $roles;
        } else {
            $this->ret['code'] = ErrorCode::ERR_SEARCH;
        }
    }
    public function del()
    {

        $id = $this->get_param('id');
        if ($id == '1') {
            $this->ret['code'] = ErrorCode::ERR_FAIL;
            exit;
        }
        $ret = $this->role->delete($id);
        if ($ret) {
            $count = $this->role->count();
            $this->ret['total_rows'] = $count;
            $this->ret['id'] = $id;
        } else {
            $this->ret['code'] = ErrorCode::ERR_FAIL;
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

            $ret = $this->role->add($params);
            if ($ret) {
                $this->ret['role'] = $ret;
            } else {
                $this->ret['code'] = ErrorCode::ERR_FAIL;
            }
        } else { //更新
            $params['update_time'] = time();
            $params['update_by'] = $user['name'];
            $ret = $this->role->update($id, $params);
            if ($ret) {
                $this->ret['role'] = $params;
            }
        }
        $count = $this->user->count();
        $this->ret['total_rows'] = $count;
    }

    public function menu_ids()
    {
        $roleId = $this->get_param('role_id');
        $menuIds = $this->role_menu->get_menu_ids($roleId);
        $this->ret["menu_ids"] = $menuIds;
    }
    public function save_role_menus()
    {
        $roleId = $this->get_param('role_id');
        $menuIds = $this->get_param('menu_ids');
        $user = $this->user->get(array('id' => $this->payload->id), true);
        if ($roleId == '') {
            $this->ret['code'] = ErrorCode::ERR_FAIL;
            exit;
        }
        $ret = $this->role_menu->save_menu_ids($roleId, $menuIds, $user);
        if (!$ret) {
            $this->ret['code'] = ErrorCode::ERR_DATABASE;
        }
    }
}
