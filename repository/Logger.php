<?php
namespace lemon\repository;

use Yii;

/**
 * 记录日志
 * @author shaodd
 *
 */
class Logger
{
	public static function writeException(\Exception $ex)
	{
		try {
			$data['user'] = Yii::$app->user->identity->id??0;
			$data['url'] = Yii::$app->request->url;
			$data['file'] = $ex->getFile();
			$data['line'] = $ex->getLine();
			$data['code'] = $ex->getCode();
			$data['error_info'] = $ex->getMessage();
			//$data['trace'] = $ex->getTrace();
			$data['post_data'] = http_build_query($_POST);

			$log = new \yii\log\FileTarget;

			$log_path = Yii::$app->params['LOG_PHYSICAL_PATH'] . '/' . date("Y-m") . '/admin';
			if (!file_exists($log_path)) {
				mkdir($log_path, 0777, true);
			}

			$log->logFile = $log_path . '/' . 'exception_' . date("d") . '.log';

			$log->messages[] = [$data, 2, 'application', microtime(true)];

			$log->export();
		} catch (\Exception $exp) {

		}
	}
	
	/**
	 * 记录单日日志文件
	 * shaodd, 2019-03-06
	 * @param unknown $tag 标识，每个标识一个文件
	 * @param unknown $data　记录的数据，可以为数组
	 * @param unknown $msg　日志说明
	 */
	public static function writeLog2($tag, $data, $msg=null)
	{
		try {
			$log_path = Yii::$app->params['LOG_PHYSICAL_PATH'] . '/' . date("Y-m") . '/admin';
			if (!file_exists($log_path)) {
				$res = mkdir(iconv("UTF-8", "GBK", $log_path), 0777, true);
			}
			$log_file = $log_path . '/' . $tag . '_' . date('d') . '.log';
			$fp = fopen($log_file, "a");
			$out = date('Y-m-d H:is') . "\r\n";
			if ($msg != null) {
				$out .= $msg . "\r\n";
			}
			$out .= var_export($data, true) . "\r\n";

			fwrite($fp, $out);
			fclose($fp);
		} catch (\Exception $exp) {
			//echo $exp->getMessage();
		}
	}
	
	public static function writeHandlerException(){
		try{
			$error = \Yii::$app->errorHandler->exception;
			self::writeException($error);
			return $error->getMessage();
		}
		catch(\Exception $exp){
			return '发现目前还未知的错误';
		}
	}
}

