<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->driver('cache',array(
            "adapter" => 'memcached',
            "backup" => 'file'
		));
		// $this->cache->memcached->is_supported();
	}
	public function index()
	{
		$this->load->view('welcome_message');
	}
	public function code(){
		$code = $this->input->get_post("code");
		$url = "https://oauth.taobao.com/token";
		$params = array(
			"client_id"=>27544566,
			"client_secret"=>"be8bec0fea9a86cd2d56865e1cdf4c68",
			"grant_type"=>"authorization_code",
			"code"=>$code,
			"redirect_uri"=>"http://server.zhaobaoge.com:8260/welcome/token"
		);

		$ret = http_post_data($url,$params);
		$data = json_decode($ret,true);
		echo $data['access_token'];

	}
	public function token(){

	}
}
