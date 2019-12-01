<?php

namespace lemon\bootstrap4;

use Yii;

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
	
}
