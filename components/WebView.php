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
	
	
	protected function viewItem($label, $value, $class="col-lg-3 col-md-4 col-sm-6 col-xs-6 flex"){
		return "<div class=\"{$class}\"><label>{$label}：</label><div>{$value}</div></div>";
	}
}

