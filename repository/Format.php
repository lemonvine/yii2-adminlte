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
	
	public function asFmtd($value, $format='Y-m-d'){
		if(!empty($value)){
			return  date($format, $value);
		}
	}
	public function friendly($value)
	{
		$limit = time() - $value;
		$r = "很久前";
		if($limit>=0){
			if($limit>172800 && $limit<1552000){
				$r = date('m-d');
			}
			elseif($limit>=1552000) {
				$r = date('Y-m-d');
			}elseif($limit < 300) {
				$r = '刚刚';
			}elseif($limit < 3600) {
				$r = floor($limit / 60) . '分钟前';
			}elseif($limit < 28800) {
				$r = floor($limit / 3600) . '小时前';
			}
			else{
				$today = strtotime(date('Y-m-d')) - $value;
				if($today<0){
					$r = floor($limit / 3600) . '小时前';
				}elseif($today<86400){
					$r='昨天';
				}
				else{
					$r='前天';
				}
				
			}
			
		}
		else{
			$r = floor(-$limit / 86400) . '天后';
		}
		
		return $r;
	}
	public function asInt($value){
		return (int)$value;
	}
	
}

