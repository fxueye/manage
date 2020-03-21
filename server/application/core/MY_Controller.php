<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class MY_Controller extends CI_Controller
{
	protected $ret = array();
	protected $interface_start_time = 0;
	protected $payload;
	protected $ip;
	function __construct()
	{
		parent::__construct();
		$this->load->driver('cache', array(
			"adapter" => 'memcached',
			"backup" => 'file'
		));
		$this->ret['code'] = ErrorCode::ERR_OK;
		$this->ret['message'] = '';
		$this->ret['servertime'] = time();
		$this->interface_start_time = microtime(TRUE);

		//跨域设置
		header("Access-Control-Allow-Origin:*");
		header("Access-Control-Allow-Methods:*");
		header("Access-Control-Allow-Headers:Content-Type,XFILENAME,XFILECATEGORY,XFILESIZE,Authorization");
		if (strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS') {
			exit;
		}
		// $controler = $this->router->fetch_class();
		$method = $this->router->fetch_method();
		$headers = $this->input->request_headers();
		$this->ip = $this->input->ip_address();
		log_message(E, json_encode($headers));
		if ($method !== 'login') {
			if (isset($headers['Authorization'])) {
				$jwt = jwtIsExist($headers);
				// log_message(I, $jwt);
				try {

					$this->payload = $this->validate_timestamp($jwt);
					if (!$this->payload) {
						throw new Exception("time out!");
					}
					// log_message(I, $this->payload);  
				} catch (Exception $e) {
					// log_message(E, json_encode($e));
					$this->ret['code'] = ErrorCode::ERR_TOKEN;
					exit;
				}
			}
		}
	}
	function __destruct()
	{
		$exec_time = microtime(TRUE) - $this->interface_start_time;
		if (isset($this->ret['code']) && $this->ret['code'] != ErrorCode::ERR_OK) {
			$ret = array(
				'code' => $this->ret['code'],
				'msg' => ErrorCode::error_msg($this->ret['code']),
				'data' => array(
					'servertime' => time(),
					'exec_time' => $exec_time,
				)
			);
		} else {
			$code = $this->ret['code'];
			$message = ErrorCode::error_msg($this->ret['code']);
			unset($this->ret['code'], $this->ret['msg']);
			$ret = array(
				'code' => $code,
				'msg' => $message,
				'data' => $this->ret
			);
		}

		$output = $this->json_xencode($ret);
		log_message("INFO", "output:" . $output);
		print $output;
	}
	public function get_param($index, $default = "")
	{
		$data = $this->params();
		if (isset($data[$index])) {
			return $data[$index];
		}
		return $default;
	}
	public function params()
	{
		$body = file_get_contents('php://input');
		$data = json_decode($body, true);
		return $data;
	}

	public function validate_timestamp($token)
	{
		$token = $this->validate_token($token);
		if ($token != false && (now() - $token->timestamp < $this->config->item('token_timeout'))) {
			return $token;
		}
		return false;
	}

	public function validate_token($token)
	{

		return JWT::decode($token, $this->config->item('jwt_key'), array($this->config->item('jwt_algorithm')));
	}

	public function generateToken($data)
	{

		return JWT::encode($data, $this->config->item('jwt_key'), $this->config->item('jwt_algorithm'));
	}

	public function json_xencode($value, $options = 0, $unescapee_unicode = true)
	{
		$v = json_encode($value, $options);
		if ($unescapee_unicode) {
			$v = $this->unicode_encode($v);
			$v = preg_replace('/\\\\\//', '/', $v);
		}
		return $v;
	}

	public function unicode_encode($str)
	{
		return preg_replace_callback("/\\\\u([0-9a-zA-Z]{4})/", array($this, "encode_callback"), $str);
	}

	public function encode_callback($matches)
	{
		return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UTF-16");
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
	function cache_clean()
	{
		return $this->cache->clean();
	}
}

class Weixin_Controller extends CI_Controller
{
	protected $wechat;
	protected $type = 1;
	public function __construct()
	{
		parent::__construct();


		$this->load->driver('cache', array(
			"adapter" => 'memcached',
			"backup" => 'file'
		));

		$this->load->library("CI_Wechat");
		$this->wechat = new CI_Wechat();
		$this->wechat->logcallback = 'Dlog';
		$this->wechat->debug = true;
		$options = $this->config->item('wechat');
		$this->type = $options['type'];
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

	function clean_cache()
	{
		return $this->cache->clean();
	}

	protected function redirect($uri, $method = 'auto', $code = NULL)
	{
		if (!preg_match('#^(\w+:)?//#i', $uri)) {
			$uri = base_url() . $uri;
		}
		redirect($uri, $method, $code);
	}
}


class Wxmp_Controller extends Weixin_Controller
{
	public function __construct()
	{
		parent::__construct();

		if (!isset($_SESSION)) {
			session_start();
		}
		if (!is_weixin()) {
			log_message(D, "请用微信打开！");
			// exit;
		}

		$user = $this->session->userdata('user');
		if (!$user) {
			$current_url = current_url();
			log_message(D, "current_url:" . $current_url);
			set_cookie("wx_source_url", $current_url, 7200);
			$this->redirect('wxmp/oauth');
		}
	}
	protected function error($msg, $url = "", $second = 3)
	{
		$data["title"] = $this->lang->line("error");
		$data["header"]  = create_header($this->lang->line("error"));
		$data["msg"] = $msg;
		$data["url"] = $url;
		$data["second"] = $second;
		//TODO
		$this->load->view("admin/error", $data);
	}
	protected function success($msg, $url = "", $second = 3)
	{
		$data["title"] = $this->lang->line("success");
		$data["header"]  = create_header($this->lang->line("success"));
		$data["msg"] = $msg;
		$data["url"] = $url;
		$data["second"] = $second;
		//TODO
		$this->load->view("admin/success", $data);
	}
}
