<?php
namespace lemon\models;

class BoleException extends \Exception {
	// HTTP 状态码 404,200
	public $code = 400;
	// 错误具体信息
	public $msg = '参数错误';
	// 自定义的错误码
	public $errorCode = 10000;
	public function __construct($params) {
		// 如果传过来的不是数组则不进行对象初始化
		if (!is_array($params)) {
			return;
		}
		// 判断数组有没有该键值
		if (array_key_exists('code',$params)) {
			$this->code = $params['code'];
		}
		if (array_key_exists('msg',$params)) {
			$this->msg = $params['msg'];
		}
		if (array_key_exists('errorCode',$params)) {
			$this->errorCode = $params['errorCode'];
		}
	}
}