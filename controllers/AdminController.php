<?php
namespace lemon\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use common\repository\Utility;

/*
 * 后台管理员控制器基类
 */
class AdminController extends Controller
{
	public $appid = 0;
	public $layout = '@vendor/lemonvine/yii2-adminlte/views/layouts/adminlte-main';
	public $u;
	public $user_id = 0;
	public $flag = 0;
	public $message = "";
	public $primary = ''; //当前页面首次打开时的路径，用于查询提交
	public $referer = ''; //跳转源，用于返回按钮
	public $deliver = [];

	/**
	 * 初始化信息
	 * {@inheritDoc}
	 * @see \yii\base\BaseObject::init()
	 */
	public function init ()
	{
		if (!empty(Yii::$app->user->identity)) {
			$this->u = Yii::$app->user->identity;
			$this->user_id = $this->u->id;
			$this->appid = $this->u->appid;
		}
		if ($this->user_id == 0) {
			$this->layout=null;
			return $this->redirect('login/index');
		}
		$this->referer = Utility::getReferer();
		$this->primary = Utility::getPrimary();
	}

	/**
	 * 返回成功json
	 */
	public function success($data)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$ret['status'] = '201';
		$ret['data'] = $data;
		return $ret;
	}

	/**
	 * 返回失败json
	 * 
	 * @param unknown $mes			
	 */
	public function fail($msg)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$ret['status'] = '401';
		$ret['message'] = $msg;
		return $ret;
	}

	/**
	 * 内容转为弹出层
	 */
	public function turnToDialog ()
	{
		$this->layout = "@app/views/layouts/model";
	}
	
	/**
	 * 弹出层保存成功，关闭弹出层
	 * @param string $msg
	 * @return string
	 */
	public function modelSaveSuccess($msg="保存成功")
	{
		$msg = addslashes($msg);
		return "<script type='text/javascript'>window.parent.modelsuccess('{$msg}');</script>";
	}
	
	public function pjaxSuccess($msg='')
	{
		$msg = addslashes($msg);
		return "<script type='text/javascript'>layer.msg('{$msg}', {time: 1000},function(){location.href ='{$this->referer}'});</script>";
	}
}