<?php

namespace lemon\repository;

use yii\i18n\Formatter;

class Format extends Formatter
{
	public $nullDisplay = '';
	
	// 文字省略
	public function asOmit($value, $s1){
		$len = mb_strlen($value);
		if($len > ($s1+1)){
			return mb_substr($value, 0, $s1).'...';
		}
		else{
			return $value;
		}
	}
	
	public function asPattern($value, $s1){
		return Pattern::$CODE[$s1][$value]??'';
	}
}

