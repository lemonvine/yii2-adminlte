<?php
namespace lemon\widgets\btns;

use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class GroupBtn extends Widget
{
	public $options = [];
	public $fixed = true;
	public $right = false;
	public $form_id = 'main_form';
	public $key_id = 0;
	
	public static $btn_css ='';
	private $submit_type = false;
	private $view_dir = "@lemon/widgets/btns/views/";
	
	public function init()
	{
		$options = $this->options;
		$tag = ArrayHelper::remove($options, 'tag', 'div');
		$options['class'] = $this->getClass();
		echo Html::beginTag($tag, $options);
	}
	
	public function run()
	{
		if($this->submit_type){
			echo Html::hiddenInput('submit_type', 'submit', ['id'=>'submit_type']);
		}
		echo Html::endTag(ArrayHelper::remove($this->options, 'tag', 'div'));
	}
	
	public static function BackButton($isbtn=true){
		return Html::Button('返回', ['class' => ($isbtn?'btn btn-':static::$btn_css).'default', 'onclick'=>'bolevine.forward()']);
	}
	
	public static function OkButton($isbtn=true){
		return Html::submitButton('确定', ['id' => 'btn_submit', 'class' => ($isbtn?'btn btn-':'').'success']);
	}
	
	public static function CancelButton($isbtn=true){
		return Html::Button('取消', ['class' => ($isbtn?'btn btn-':'').'default', 'onclick'=>'window.parent.bolevine.turnoff()']);
	}
	
	public static function SearchButton(){
		$button = Html::submitButton('查询', ['class' => 'btn btn-primary']);
		$content = Html::tag('div', $button, ['class' => "form-group pull-right btns-line ml-auto"]);
		return $content;
	}
	
	public static function SearchAddButton($config=[]){
		$params = ['target'=>'model', 'title'=>'添加', 'url'=>['create']];
		$params = array_merge($params, $config);
		$button = Html::submitButton('查询', ['class' => 'btn btn-primary']);
		$url = Url::toRoute($params['url']);
		if($params['target']=='model'){
			$button .= Html::a($params['title'], 'javascript:;', ['id' => 'btn_create', 'class' => 'btn btn-success modaldialog', 'data-url' => $url, 'data-title' => $params['title']]);
		}
		elseif($params['target']=="page"){
			$button .= Html::a($params['title'], $url, ['class' => 'btn btn-success']);
		}
		$content = Html::tag('div', $button, ['class' => "form-group pull-right btns-line ml-auto"]);
		return $content;
	}
	
	public static function GoButton($config=[]){
		$params = ['title'=>'保存', 'url'=>'', 'isbtn'=>true, 'color'=>'info'];
		$params = array_merge($params, $config);
		
		return Html::a($params['title'],  $params['url'], ['class' => ($params['isbtn']?'btn btn-':static::$btn_css).$params['color']]);
	}
	
	public static function DialogButton($config=[]){
		$params = ['title'=>'保存', 'url'=>'', 'isbtn'=>true, 'color'=>'info', 'size'=>'sm'];
		$params = array_merge($params, $config);
		return Html::a($params['title'], 'javascript:;', 
			['class'=>($params['isbtn']?'btn btn-':static::$btn_css).$params['color'].' modaldialog', 'data-title' => $params['title'],
			'data-area' => $params['size'], 'data-url' => $params['url']]);
	}
	
	public static function AlertButton($config=[]){
		$params = ['title'=>'保存', 'html'=>'', 'isbtn'=>true, 'color'=>'info', 'size'=>'sm'];
		$params = array_merge($params, $config);
		
		return Html::a($params['title'], 'javascript:;', ['class'=>($params['isbtn']?'btn btn-':static::$btn_css).$params['color'].' modaldialog', 'data-title' => $params['title'], 'data-area' => $params['size'],
			'data-html'=>$params['html']]);
	}
	
	public static function AsynchButton($config=[]){
		$params = ['title'=>'保存', 'html'=>'', 'isbtn'=>true, 'color'=>'info', 'data'=>[], 'callback'=>'', 'type'=>'post'];
		$params = array_merge($params, $config);
		
		if($params['data']){
			$data = json_encode($params['data']);
		}
		else{
			$data = '';
		}
		return Html::a($params['title'], 'javascript:;', ['class'=>($params['isbtn']?'btn btn-':static::$btn_css).$params['color'].' asynchtrace', 'data-type'=>$params['type'], 'data-data'=>$data,
			'data-url'=>$params['url'], 'data-callback'=>$params['callback']]);
	}
	
	public function back(){
		echo self::BackButton();
	}
	
	public function cancel(){
		echo self::CancelButton();
	}
	
	public function ok(){
		echo self::OkButton();
	}
	
	public function go($config=[]){
		$config['isbtn']=true;
		echo self::GoButton($config);
	}
	
	/**
	 * 弹出对话框
	 * @param unknown $title
	 * @param unknown $url
	 * @param string $color
	 * @param string $size
	 */
	public function dialog($config=[]){
		$config['isbtn']=true;
		echo self::DialogButton($config);
	}
	
	/**
	 * 弹出提示按钮
	 * @param unknown $title
	 * @param unknown $url
	 * @param string $color
	 * @param string $size
	 */
	public function alert($config=[]){
		$config['isbtn']=true;
		echo self::AlertButton($config);
	}
	
	public function save($config=[]){
		$this->submit_type = true;
		$params = ['btn_name'=>'保存', 'form_id'=>$this->form_id];
		$params = array_merge($params, $config);
		echo $this->render($this->view_dir.'save', $params);
	}
	
	public function submit($config=[]){
		$this->submit_type = true;
		$params = ['btn_name'=>'提交', 'form_id'=>$this->form_id, 'confirm'=>'您确定要提交吗？'];
		$params = array_merge($params, $config);
		echo $this->render($this->view_dir.'submit', $params);
	}
	
	public function export($config=[]){
		$this->submit_type = true;
		$params = ['form_id'=>$this->form_id];
		$params = array_merge($params, $config);
		echo $this->render($this->view_dir.'export', $params);
	}
	
	private function getClass(){
		$class = $this->fixed?'form-btns':'btns-line';
		if($this->right){
			$class .= ' text-right';
		}
		$options = $this->options;
		if (isset($options['class'])) {
			$class = $options['class'];
		}
		return $class;
	}
}

