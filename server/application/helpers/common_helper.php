<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
/**
 * 成功提示函数
 *
 * @param [type] $url
 *        	[跳转地址]
 * @param [type] $msg
 *        	[提示信息]
 * @return [type] [description]
 */
if (!function_exists('show_success')) {
	function show_success($url, $msg)
	{
		header('Content-Type:text/html;charset=utf-8');
		$url = site_url($url);
		echo "<script type='text/javascript'>alert('$msg');location.href='$url'</script>";
		die();
	}
}
/**
 * 错误提示函数
 *
 * @param [type] $msg
 *        	[提示信息]
 * @return [type] [description]
 */
if (!function_exists('error')) {
	function error($msg)
	{
		header('Content-Type:text/html;charset=utf-8');
		echo "<script type='text/javascript'>alert('$msg');window.history.back();</script>";
		die();
	}
}


if (!function_exists('check_sign')) {
	function check_sign($array_data, $app_key)
	{
		$sign = $array_data["sign"];
		unset($array_data["sign"]);
		if (!$sign)
			return false;
		ksort($array_data);
		log_message(I, json_encode($array_data));
		$str = "";
		foreach ($array_data as $key => $value) {
			$str .= $value;
		}
		$md5 = md5($str . $app_key);
		log_message(I, "sign str:" . $str . $app_key);
		log_message(I, "sign md5:" . $md5);
		if ($sign == $md5)
			return true;
		else
			return false;
	}
}

if (!function_exists('api_on_result')) {
	function api_on_result($code, $msg, $data = "")
	{
		$result_array['code'] = $code;
		$result_array['msg'] = $msg;
		$result_array['data'] = $data;
		echo json_encode($result_array);
	}
}
if (!function_exists('ajax_on_result')) {
	function ajax_on_result($code, $msg, $data = "")
	{
		$result_array['code'] = $code;
		$result_array['msg'] = $msg;
		$result_array['data'] = $data;
		echo json_encode($result_array);
	}
}

if (!function_exists('get_uuid')) {
	function get_uuid($uid, $len = 6)
	{
		$account = ($uid << 24) | (rand(0, 2147483647) | msectime() & 0xffffff);
		$account = substr($account, rand(0, strlen($account) - $len), $len);
		return $account;
	}
}
if (!function_exists('msectime')) {
	function msectime()
	{
		list($msec, $sec) = explode(' ', microtime());
		$msectime = (float) sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
		return $msectime;
	}
}
if (!function_exists('get_rand_int')) {
	function get_rand_int($length)
	{
		$str = null;
		$str_prol = "0123456789";
		$max = strlen($str_prol) - 1;
		for ($i = 0; $i < $length; $i++) {
			$str .= $str_prol[rand(0, $max)];
		}
		return $str;
	}
}
if (!function_exists('get_rand_char')) {
	function get_rand_char($length = 6)
	{
		$str = null;
		$str_prol = "abcdefghijkmnopqrstuvwxyz0123456789";
		$max = strlen($str_prol) - 1;
		for ($i = 0; $i < $length; $i++) {
			$str .= $str_prol[rand(0, $max)];
		}
		return $str;
	}
}
if (!function_exists('get_open_id')) {
	function get_open_id()
	{
		return md5(uniqid() . get_rand_char(32));
	}
}
if (!function_exists('get_token')) {
	function get_token()
	{
		return md5(uniqid() . get_rand_char(32));
	}
}
if (!function_exists('is_empty')) {
	function is_empty($val)
	{
		if (!is_string($val)) return false; //是否是字符串类型    
		if (empty($val)) return false; //是否已设定    
		if ($val == '') return false; //是否为空    
		return true;
	}
}
if (!function_exists('is_number')) {
	function is_number($val)
	{
		if (preg_match("/^[0-9]+$/", $val))
			return true;
		return false;
	}
}
if (!function_exists('is_phone')) {
	function is_phone($val)
	{
		// /^(13[0-9]|14[579]|15[0-3,5-9]|16[6]|17[0135678]|18[0-9]|19[89])\d{8}$/
		if (preg_match("/^(13[0-9]|14[579]|15[0-3,5-9]|16[6]|17[0135678]|18[0-9]|19[89])\d{8}$/", $val))
			return true;
		return false;
	}
}


if (!function_exists('check_char')) {
	function check_char($str)
	{
		if (!preg_match("/^[_a-zA-Z0-9]*$/", $str)) return false;
		return true;
	}
}
if (!function_exists('check_length_between')) {
	function check_length_between($str, $len1, $len2)
	{
		$str = trim($str);
		if (strlen($str) < $len1) return false;
		if (strlen($str) > $len2) return false;
		return true;
	}
}
if (!function_exists('create_header')) {
	function create_header($type = "", $right_str = "", $right_url = "")
	{
		$array = array('type' => $type, 'right_url' => $right_url, 'right_str' => $right_str);
		return $array;
	}
}
if (!function_exists('create_public_data')) {
	function create_public_data($title, $header, $sdk = false)
	{
		$data['title'] = $title;
		$data['header'] = $header;
		$data['sdk'] = $sdk;
		return $data;
	}
}
if (!function_exists('is_qq')) {
	function is_qq($qq)
	{
		if (!preg_match("/[1-9][0-9]{4,}/", $qq)) return false;
		return true;
	}
}
if (!function_exists('is_email')) {
	function is_email($email)
	{
		if (!preg_match("/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/", $email)) return false;
		return true;
	}
}
if (!function_exists('is_date_time')) {
	function is_date_time($date_time)
	{
		if (!preg_match("/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$/", $date_time)) return false;
		return true;
	}
}

if (!function_exists('convert_url_query')) {
	function convert_url_query($query)
	{
		$queryParts = explode('&', $query);
		$params = array();
		foreach ($queryParts as $param) {
			$item = explode('=', $param);
			if (count($item) > 1) {
				$params[$item[0]] = $item[1];
			}
		}
		return $params;
	}
}
if (!function_exists('is_url')) {
	function is_url($url)
	{
		if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url)) {
			return false;
		}
		return true;
	}
}


if (!function_exists('Dlog')) {
	function Dlog($msg)
	{
		log_message(D, $msg);
	}
}

if (!function_exists('http_get_data')) {
	function http_get_data($url, $fields = array())
	{
		if (is_array($fields)) {
			$qry_str = http_build_query($fields);
		} else {
			$qry_str = $fields;
		}
		if (trim($qry_str) != '') {
			$url = $url . '?' . $qry_str;
		}

		$curl = curl_init();
		// 2. 设置选项，包括URL
		curl_setopt($curl, CURLOPT_URL, $url);
		//        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($curl,  CURLOPT_FOLLOWLOCATION, 1); // 302 redirect
		// 3. 执行并获取HTML文档内容

		$data = curl_exec($curl);

		if (curl_errno($curl)) {
			log_message('error', curl_error($curl)); //捕抓异常
		}
		curl_close($curl);

		return $data; // 返回数据
	}
}
if (!function_exists('http_post_data')) {
	function http_post_data($url, $data = array())
	{
		if (is_array($data)) {
			$qry_str = http_build_query($data);
		} else {
			$qry_str = $data;
		}


		$curl = curl_init(); // 启动一个CURL会话
		curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
		//        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
		curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
		curl_setopt($curl, CURLOPT_POSTFIELDS, $qry_str); // Post提交的数据包
		curl_setopt($curl, CURLOPT_TIMEOUT, 10); // 设置超时限制防止死循环
		//curl_setopt($curl, CURLOPT_HEADER, array('Content-Type: application/json')); // 显示返回的Header区域内容

		$tmpInfo = curl_exec($curl); // 执行操作

		//$rescode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  
		if (curl_errno($curl)) {
			log_message('error', curl_error($curl));
		}
		curl_close($curl); // 关闭CURL会话
		return $tmpInfo; // 返回数据
	}
}

if (!function_exists('this_month_begin')) {
	function this_month_begin()
	{
		return strtotime(date("Y-m-01"));
	}
}
if (!function_exists('last_month_begin')) {
	function last_month_begin()
	{
		return strtotime(date("Y-m-01", strtotime(date("Y-m-d", this_month_begin()) . " -1 month")));
	}
}
if (!function_exists('today_begin')) {
	function today_begin()
	{
		return strtotime(date('Y-m-d 00:00:00'));
	}
}
if (!function_exists('today_end')) {
	function today_end()
	{
		return strtotime(date('Y-m-d 23:59:59'));
	}
}
if (!function_exists('is_weixin')) {
	function is_weixin()
	{
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
			return true;
		}
		return false;
	}
}
if (!function_exists('cc_format')) {
	function cc_format($name)
	{
		$temp_array = array();
		for ($i = 0; $i < strlen($name); $i++) {
			$ascii_code = ord($name[$i]);
			if ($ascii_code >= 65 && $ascii_code <= 90) {
				if ($i == 0) {
					$temp_array[] = chr($ascii_code + 32);
				} else {
					$temp_array[] = '_' . chr($ascii_code + 32);
				}
			} else {
				$temp_array[] = $name[$i];
			}
		}
		return implode('', $temp_array);
	}
}
if (!function_exists('download_file')) {
	function download_file($url, $path = "download/", $filename = null)
	{
		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			$file = curl_exec($ch);
			curl_close($ch);
			if ($filename ==  null) {
				$filename = pathinfo($url, PATHINFO_BASENAME);
			}
			$resource = fopen($path . $filename, 'a');
			fwrite($resource, $file);
			fclose($resource);
			return true;
		} catch (Exception $e) {
			log_message(E, "download error:" . $e->getMessage());
			return false;
		}
	}
}
if (!function_exists('jwtIsExist')) {
	function jwtIsExist($headers)
	{
		list($jwt) = sscanf($headers['Authorization'], 'comic %s');
		return $jwt;
	}
}
