<?php
namespace lemon\components;

use yii\web\View;
use yii\helpers\Html;

/**
 * 继承View,增加常用代码归置
 * @author shaodd
 *
 */
class WebView extends View
{
	
	const POS_HDB = 6;
	protected $WHOLE_ROW=['options'=>['class'=>'form-group col-12 flex']];
	
	
	protected function viewItem($label, $value, $class="col-lg-3 col-md-4 col-sm-6 col-xs-6 flex"){
		return "<div class=\"{$class}\"><label>{$label}：</label><div>{$value}</div></div>";
	}
	
	protected function renderBodyEndHtml($ajaxMode){
		$content = parent::renderBodyEndHtml($ajaxMode);
		if (!empty($this->js[self::POS_HDB])) {
			$handlebar = $this->js[self::POS_HDB];
			$lines = [];
			foreach ($handlebar as $key=>$item){
				$lines[] = Html::script($item, ['id'=>$key, 'type'=>'text/x-handlebars-template']);
			}
			
			$content .= implode("\n", $lines);
		}
		return $content;
	}
	
}

