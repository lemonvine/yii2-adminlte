<?php

namespace lemon\repository;

use Yii;
use yii\i18n\Formatter;
use yii\helpers\Html;

/**
 * 
 * @author shaodd
 * 要使用formatter需要在config中的配置
'formatter' => [
    'dateFormat' => 'yyyy-MM-dd',
    'datetimeFormat' => 'yyyy-MM-dd HH:mm:ss',
    'decimalSeparator' => ',',
    'thousandSeparator' => ' ',
    'currencyCode' => 'CNY',
],
 * echo Yii::$app->formatter->asRelativeTime(1463632983) // 几天前 几小时前
 * Yii::$app->formatter->asDatetime(1463606983)  // 2015-6-16 11:51:43
 * Yii::$app->formatter->asDatetime('now')
 * Yii::$app->formatter->asDate(null) // 输出: (未设置)  
 * Yii::$app->formatter->asPercent(0.125, 2) // 输出: 12.50%
 * Yii::$app->formatter->asTimestamp('now') //输出时间戳
 * Yii::$app->formatter->asTime(1412599260) // 14:41:00
 * Yii::$app->formatter->asTime('2014-10-06 12:41:00') // 14:41:00
 * Yii::$app->formatter->asTime('2014-10-06 14:41:00 CEST') // 14:41:00
 * Yii::$app->formatter->asRaw(1463606983) //简单输出输入值
 * Yii::$app->formatter->asText('<h3>hello</h3>') //将字符串中html标签当做字符串输出
 * Yii::$app->formatter->asHtml('<h3>hello</h3>') //作为Html的文档输出
 * Yii::$app->formatter->asNtext("<h3>hello.\nword</h3>") //在字符串中遇到\n可以将它作为换行符实现
 * Yii::$app->formatter->asEmail('cebe@example.com') // 输出: <a href="mailto:cebe@example.com">cebe@example.com</a>
 * Yii::$app->formatter->asParagraphs('hello') // 值会转换成HTML编码的文本段落，用<p>标签包裹；
 * Yii::$app->formatter->asUrl('www.baidu.com') //值会格式化成url的连接
 * Yii::$app->formatter->asImage('my2.jpeg',['alt'=>'图片无法显示']) //图片的链接会转化成<img src='my.jpg'/>
 * Yii::$app->formatter->asBoolean(true) //输出yes
 *
 */
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
	
	public function asStatus($value){
		return Pattern::$CODE['status'][$value]??'';
	}
	
	public function asSta($value){
		return Pattern::$CODE['status'][$value]??'';
	}
	
	public function asFmtd($value, $format='Y-m-d'){
		if(!empty($value)){
			return  date($format, $value);
		}
	}
	
	public function asMoney($value){
		return preg_replace('/(?<=[0-9])(?=(?:[0-9]{3})+(?![0-9]))/', ',', $value);
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
	public function asImage($value, $options = []){
		if ($value === null) {
			return $this->nullDisplay;
		}
		$value = Yii::$app->params['FILE_HTTP_PATH'].$value;
		$options['class']='img-thumbnail img-fluid';
		return Html::img($value, $options);
	}
}

