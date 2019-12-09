<?php

namespace lemon\bootstrap4;

use Yii;
use yii\base\InvalidCallException;
use yii\helpers\Html;

/**
 * A Bootstrap 4 enhanced version of [[\yii\bootstrap4\ActiveForm]].
 *
 * This class mainly adds the [[layout]] property to choose a Bootstrap 4 form layout.
 * So for example to render a horizontal form you would:
 *
 */
class ActiveForm extends \yii\bootstrap4\ActiveForm
{
	public function init()
	{
		$this->fieldClass = "lemon\bootstrap4\ActiveField";
		parent::init();
	}
	
	public function run()
	{
		if (!empty($this->_fields)) {
			throw new InvalidCallException('Each beginField() should have a matching endField() call.');
		}
		
		$content = ob_get_clean();
		$html = Html::beginForm($this->action, $this->method, $this->options);
		$html .= Html::hiddenInput('back_referer',$this->view->context->referer, ['id'=>'referer_url']);
		$html .= $content;
		
		if ($this->enableClientScript) {
			$this->registerClientScript();
		}
		
		$html .= Html::endForm();
		return $html;
	}
}
