<?php

namespace lemon\repository;

class HttpHelper{
	public static function http_get($url, $header=[]) { // 模拟获取内容函数
		$curl = curl_init();
		$agent = 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36';
		curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
		curl_setopt($curl, CURLOPT_USERAGENT, $agent); // 模拟用户使用的浏览器
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
		curl_setopt($curl, CURLOPT_HTTPGET, 1); // 发送一个常规的Post请求
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
		curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		$tmpInfo = curl_exec($curl); // 执行操作
		if (curl_errno($curl)) {
			echo 'Errno' . curl_error($curl);
		}
		curl_close($curl); // 关闭CURL会话
		return $tmpInfo; // 返回数据
	}
	
	public static function http_post($url, $post_data) {
		$useragent = 'PHP5 Client 1.0 (curl) ' . phpversion();
		$ch = curl_init();
		$this_header = array("content-type: application/json; charset=UTF-8",'Content-Length: ' . strlen($post_data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this_header);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	
	//数组转url参数
	public static function build_query($query_data, $encoding = false) {  
		$res = '';  
		$count = count ( $query_data );  
		$i = 0;  
		foreach ( $query_data as $k => $v ) {  
			if ($encoding === true) {  
				$v = urlencode ( $v );  
			}  
			if ($i < $count - 1) {  
				$res .= $k . '=' . $v . '&';  
			} else {  
				$res .= $k . '=' . $v;  
			}  
			$i ++;  
		}  
		return $res;  
	}  
} 