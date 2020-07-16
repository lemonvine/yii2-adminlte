<?php
namespace lemon\widgets;

use yii\base\Widget;
/**
 * 横向时间轴
 */
class Step extends Widget
{
	public $steps;
	public $current;
	
	public function run()
	{
		return $this->render('step');
	}
}