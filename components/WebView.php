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
	
	private $FLEXBOX = [
		1=>"col-lg-3 col-md-4 col-sm-6 col-xs-6",
		2=>"col-lg-6 col-md-8 col-sm-12 col-xs-12",
		3=>"col-12",
		4=>"col-6",
	];
	
	
	protected function viewItem($label, $value, $flex=1, $class=""){
		if($flex!=9){
			$class = $this->FLEXBOX[$flex];
		}
		$class .=' flex';
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

