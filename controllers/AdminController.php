<?php
namespace lemon\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use lemon\repository\Utility;
use yii\helpers\Url;

/*
 * 后台管理员控制器基类
 */
class AdminController extends Controller
{
	public $layout='@vendor/lemonvine/yii2-adminlte/layouts/adminlte-main';
	public $u;
	public $m='';
	public $user_id=0;
	public $operate= 1;
	public $message="";
	public $primary=''; //当前页面首次打开时的路径，用于查询提交
	public $referer=''; //跳转源，用于返回按钮
	public $deliver=[];
	public $submit_type='';
	public $upload_url='';

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
		}
		if ($this->user_id == 0) {
			return;
		}
		$this->referer = Utility::getReferer();
		$this->primary = Utility::getPrimary();
		$params = Yii::$app->request->queryParams;
		if (isset($params['m'])) {
			$this->m = $params['m'];
		}
		$post = Yii::$app->request->post();
		if(isset($post['submit_type'])){
			$this->submit_type=$post['submit_type'];
		}
		$params = Yii::$app->params;
		if(isset($params['FILE_UPLOAD_URL'])){
			$upload_url = $params['FILE_UPLOAD_URL'];
			if(is_array($upload_url)){
				$this->upload_url = $upload_url[$params['FILE_UPLOAD_WHERE']];
			}else{
				$this->upload_url = $upload_url;
			}
		}else{
			$this->upload_url = Url::toRoute(['/config/pictures/upload']);
		}
	}
	
	public function beforeAction($action)
	{
		$this->u = Yii::$app->user->identity;
		if (!$this->u) {
			echo "<script type='text/javascript'>window.location.href='".Url::toRoute(['/site/login'])."';</script>";
			die;
		}
		else{
			return parent::beforeAction($action);
		}
		
	}

	/**
	 * 返回成功json
	 */
	public function success($data='', $ext='')
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		return ['status'=>'202', 'data'=>$data, 'ext'=>$ext];
	}

	/**
	 * 返回失败json
	 * 
	 * @param unknown $mes			
	 */
	public function fail($msg)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		return ['status'=>'401', 'message'=>$msg];
	}

	/**
	 * 内容转为弹出层
	 */
	public function turnToDialog ()
	{
		$this->layout = "@vendor/lemonvine/yii2-adminlte/layouts/adminlte-model";
	}
	
	public function partHtml()
	{
		$this->layout = "@vendor/lemonvine/yii2-adminlte/layouts/pure_html";
	}
	
	/**
	 * 关闭弹出层，无刷新
	 * @param string $msg
	 * @return string
	 */
	public function modelTipsSuccess($msg="操作成功", $icon= 1, $callback='')
	{
		$msg = addslashes($msg);
		if(!empty($callback)){
			if(!strpos($callback,'(') !==false){
				$callback = $callback.'()';
			}
			$callback = "window.parent.$callback";
		}
		
		return "<script type='text/javascript'>window.parent.layer.msg('{$msg}', {icon: '{$icon}',time:1000});window.parent.bolevine.turnoff();$callback;</script>";
	}
	
	/**
	 * 弹出层保存成功，关闭弹出层
	 * @param string $msg
	 * @return string
	 */
	public function modelSaveSuccess($msg="保存成功", $url='', $chain=TRUE)
	{
		$msg = addslashes($msg);
		return "<script type='text/javascript'>window.parent.bolevine.dialogok('{$msg}', '{$url}', '{$chain}');</script>";
	}
	
	/**
	 * 弹出层保存成功，关闭弹出层
	 * @param string $msg
	 * @return string
	 */
	public function modelHandleSuccess($msg="保存成功", $url='')
	{
		$msg = addslashes($msg);
		return "<script type='text/javascript'>window.parent.bolevine.back2begin('{$msg}', '{$url}');</script>";
	}
	
	public function modelCallParent($callback, $msg=""){
		$js = "";
		if(!empty($msg)){
			$js = "window.parent.bolevine.alert(\{message: {$msg}\});";
		}
		$js .= "window.parent.".$callback.";";
		return "<script type='text/javascript'>{$js}</script>";
	}
	
	public function modelClose($msg="操作成功"){
		$msg = addslashes($msg);
		return "<script type='text/javascript'>window.parent.bolevine.turnoff();window.parent.bolevine.alert({message:'{$msg}'});</script>";
	}
	
	public function pjaxSuccess($msg='', $url='')
	{
		$msg = addslashes($msg);
		return "<script type='text/javascript'>layer.msg('{$msg}', {time: 1000},function(){".!empty($url)?"location.href ='{$url}'":""."});</script>";
	}
}