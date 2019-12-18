<?php
namespace lemon\widgets;

use yii\base\Widget;

/**
 */
class Message extends Widget
{
	public $message;
	public $operate;
	
	public function run()
	{
		return $this->render('message');
	}
}
