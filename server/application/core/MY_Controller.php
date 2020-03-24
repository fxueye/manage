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
		$this->interface_start_time = microtime(true);
		parent::__construct();
		$this->load->driver('cache', array(
			"adapter" => 'memcached',
			"backup" => 'file'
		));
		$this->ret['code'] = ErrorCode::ERR_OK;
		$this->ret['message'] = '';
		$this->ret['servertime'] = time();
		$this->load->model("log_model");

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
		$exec_time = microtime(true) - $this->interface_start_time;

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

		$output = json_encode($ret);

		log_message("INFO", "output:" . $output);
		print $output;

		if (strtoupper($_SERVER['REQUEST_METHOD']) != 'OPTIONS') {
			$controler = $this->router->fetch_class();
			$method = $this->router->fetch_method();
			$data = array(
				'method' => sprintf("%s/%s", $controler, $method),
				'params' => json_encode($this->params()),
				'time' => $exec_time,
				'ip' => $this->ip,
				'create_by' => isset($this->payload->name) ? $this->payload->name : '',
				'create_time' => time(),
			);
			$this->log_model->add($data);
		}
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
