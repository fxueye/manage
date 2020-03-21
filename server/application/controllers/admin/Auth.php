<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Auth extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("user_model", "user");
        $this->load->model("menu_model", "menu");

        $this->load->model("role_menu_model", "role_menu");
        $this->load->model("user_role_model", "user_role");
    }
    public function login()
    {
        try {
            $name = trim($this->get_param('name', ''));
            $password = trim($this->get_param('password', ''));
            $user = $this->user->login($name, md5($password));
            if ($user) {
                $payload = array();
                $payload['id'] = $user['id'];
                $payload['timestamp'] = time();
                $payload['name'] = $user['name'];

                $this->user->update($user['id'], array(
                    "last_login_ip" => $this->ip,
                    "last_login_time" => time(),
                ));
                $this->ret['token'] = $this->generateToken($payload);
            } else {
                $this->ret['code'] = ErrorCode::ERR_NO_USER;
                exit;
            }
        } catch (Exception $e) {
            $this->ret['code'] = ErrorCode::ERR_FAIL;
        }
    }
    public function user()
    {
        $user = $this->user->get(array(
            'id' => $this->payload->id
        ), true);
        unset($user['password']);
        $this->ret['user'] = $user;
    }
    public function permission()
    {
        $user_id = $this->payload->id;
        $roleIds = $this->user_role->get_role_ids($user_id);
        $menuIds = $this->role_menu->get_menu_ids($roleIds);
        $permission = $this->menu->get_menus($menuIds);
        $this->ret["permission"] = $permission;
    }
}
