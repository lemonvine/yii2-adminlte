<?php
namespace lemon\components;

use yii\web\View;

/**
 * 继承View,增加常用代码归置
 * @author shaodd
 *
 */
class WebView extends View
{
	protected $WHOLE_ROW=['options'=>['class'=>'form-group col-12 flex']];
}

