<?php
namespace lemon\components;

use yii\web\ErrorHandler;
use lemon\models\BoleException;

class ExceptionHandler extends ErrorHandler {
	public function renderException($exception) {
		if (YII_DEBUG) {
			// 如果为开发模式时，可以按照之前的页面渲染异常，因为框架的异常渲染十分详细，方便我们寻找错误
			return parent::renderException($exception);
		} else {
			// 用户不适当的操作导致的异常
			if ($exception instanceof BoleException) {
				$this->code = $exception->code;
				$this->msg = $exception->msg;
				$this->errorCode = $exception->errorCode;
			} else {
				$this->code = 500;
				$this->msg = '系统内部出现错误';
				$this->errorCode = 999;
				// 记录日志
			}
		}
		$data = [
			'code' => $this->code,
			'msg' => $this->msg,
			'errorCode' => $this->errorCode,
		];
		echo json_encode($data);
	}
}
