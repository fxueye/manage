<?php
defined('BASEPATH') or exit('No direct script access allowed');
class User extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("user_model", "user");
    }
    public function list()
    {
        $name = $this->get_param('name');
        $page = $this->get_param('page', 1);
        $size = $this->get_param('size', 10);
        $count = $this->user->count();
        $users = $this->user->list($page, $size, $name);
        if ($count && $users) {
            $this->ret['total_rows'] = $count;
            $this->ret['users'] = $users;
        } else {
            $this->ret['code'] = ErrorCode::ERR_SEARCH;
        }
    }


    public function del()
    {

        $id = $this->get_param('id');
        if ($id == "" || $id == 1) { //超级管理员 admin  不能删除
            $this->ret['code'] = ErrorCode::ERR_FAIL;
            exit;
        }
        $ret = $this->user->batch_del(array($id));
        if ($ret) {
            $count = $this->user->count();
            $this->ret['total_rows'] = $count;
            $this->ret['id'] = $id;
        } else {
            $this->ret['code'] = ErrorCode::ERR_FAIL;
        }
    }

    public function batch_del()
    {
        $ids = $this->params();
        if (count($ids) == 0) {
            $this->ret['code'] = ErrorCode::ERR_FAIL;
            exit;
        }
        if (in_array("1", $ids)) {
            $this->ret['code'] = ErrorCode::ERR_FAIL;
            exit;
        }
        $ret = $this->user->batch_del($ids);
        if (!$ret) {
            $this->ret['code'] = ErrorCode::ERR_FAIL;
        } else {
            $this->ret['ids'] = $ids;
        }
    }

    public function save()
    {

        $params = $this->params();

        $id = isset($params['id']) ? $params['id'] : "";
        $user = $this->user->get(array('id' => $this->payload->id), true);
        $roleIds = array();
        if (isset($params['role_ids'])) {
            $roleIds = $params['role_ids'];
            unset($params['role_ids']);
        }
        if (isset($params['password'])) {
            $password = $params['password'];
            log_message(I, $password);
            if (strlen($password) < 6) {
                $this->ret['code'] = ErrorCode::ERR_FAIL;
                exit;
            }
            if ($password != "") {
                $params['password'] = md5($password);
            }
        }

        if ($id == "") { //新增
            if (isset($params['id'])) {
                unset($params['id']);
            }

            $params['create_time'] = time();
            $params['update_time'] = time();
            $params['create_by'] = $user['name'];
            $params['update_by'] = $user['name'];
            $params['last_login_time'] = time();

            $ret = $this->user->add($params);
            if ($ret) {
                $id = $ret['id'];
                unset($ret['password']);
                $ret['role_ids'] = $roleIds;
                $this->ret['user'] = $ret;
            } else {
                $this->ret['code'] = ErrorCode::ERR_FAIL;
            }
        } else { //更新

            $params['update_time'] = time();
            $params['update_by'] = $user['name'];
            $ret = $this->user->update($id, $params);
            if ($ret) {

                unset($params['password']);
                $params['role_ids'] = $roleIds;
                $this->ret['user'] = $params;
            }
        }
        if (count($roleIds) > 0) {
            $ret = $this->user->save_role_ids($id, $roleIds, $user);
            if (!$ret) {
                $this->ret['code'] = ErrorCode::ERR_FAIL;
                exit;
            }
        }
        $count = $this->user->count();
        $this->ret['total_rows'] = $count;
    }
}
