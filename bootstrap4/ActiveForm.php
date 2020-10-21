<?php

namespace lemon\bootstrap4;

use Yii;
use yii\base\InvalidCallException;
use yii\helpers\Html;
use lemon\widgets\JsBlock;

/**
 * A Bootstrap 4 enhanced version of [[\yii\bootstrap4\ActiveForm]].
 *
 * This class mainly adds the [[layout]] property to choose a Bootstrap 4 form layout.
 * So for example to render a horizontal form you would:
 *
 */
class ActiveForm extends \yii\bootstrap4\ActiveForm
{
	const INPUT_TEXT = 'textInput';
	const INPUT_LIST = 'dropDownList';
	const INPUT_DATE = 'DatePicker';
	public $PJAX = false;
	public $fieldClass = 'lemon\bootstrap4\ActiveField';
	public $submitType = 'submit';
	public function init()
	{
		//$this->fieldClass = "lemon\bootstrap4\ActiveField";
		parent::init();
	}
	
	public function run()
	{
		if (!empty($this->_fields)) {
			throw new InvalidCallException('Each beginField() should have a matching endField() call.');
		}
		$content = ob_get_clean();
		$html = Html::beginForm($this->action, $this->method, $this->options);
		if(isset($this->view->context->referer)){
			$html .= Html::hiddenInput('ref',$this->view->context->referer, ['id'=>'referer_url']);
		}
		$html .= Html::hiddenInput('submit_type', $this->submitType, ['id'=>'submit_type']);
		$html .= $content;
		
		if ($this->enableClientScript) {
			$this->registerClientScript();
		}
		if($this->PJAX){
			$html .= $this->pjaxResponse();
		}
		
		$html .= Html::endForm();
		return $html;
	}
	
	private function pjaxResponse(){
		if($this->view->context->operate==9){
			$Js = <<<JS
bolevine.back2begin("{$this->view->context->message}", "{$this->view->context->referer}");
JS;
			$jsb = JsBlock::begin();
			$html = $jsb->block($Js);
			JsBlock::end();
			return $html;
		}
	}
}
