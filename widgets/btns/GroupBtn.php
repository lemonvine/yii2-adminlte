<?php
namespace lemon\widgets\btns;

use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class GroupBtn extends Widget
{
	public $options = [];
	public $fixed = true;
	public $right = false;
	public $form_id = 'main_form';
	public $key_id = 0;
	
	private $submit_type = false;
	private $view_dir = "@lemon/widgets/btns/views/";
	public $target = "";
	public $url = 'create';
	public $m = '';
	
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
	
	public function back(){
		echo Html::Button('返回', ['class' => 'btn btn-info', 'onclick'=>'bolevine.forward()']);
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
	
	public function alert($title, $url, $btn_color='info', $size='sm'){
		echo Html::a($title, 'javascript:;', ['class' => 'btn btn-'.$btn_color.' modaldialog', 'data-title' => $title, 'data-area' => $size,
			'data-url' => $url]);
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

