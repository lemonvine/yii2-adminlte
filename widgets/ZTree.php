<?php

namespace lemon\widgets;

use yii\base\Widget;
use lemon\web\AdminLteAsset;

class ZTree extends Widget
{
	private $_setting = '{}';
	private $_nodes = '[]';
	private $id;

	public function init()
	{
		if (is_null($this->id))
			$this->id = $this->getId();
	}
	
	public function run()
	{
		echo '<ul id="'.$this->id.'" class="ztree"></ul>';
		$view = $this->getView();
		AdminLteAsset::loadModule($view, 'ztree');

		$js = '$.fn.zTree.init($("#'.$this->id.'"), '.$this->_setting.', '.$this->_nodes.');';
		$view->registerJs($js);
	}

	public function setSetting($value)
	{
		if (is_array($value))
			$value = json_encode($value, JSON_UNESCAPED_UNICODE);

		$this->_setting = $value;
	}

	public function setNodes($value)
	{
		if (is_array($value))
			$value = json_encode($value, JSON_UNESCAPED_UNICODE);

		$this->_nodes = $value;
	}
}