<?php
namespace lemon\web;

use Yii;
use yii\web\AssetBundle;

/**
 * Class BootstrapAsset
 * @package lemon\web
 */
class PageAsset extends AssetBundle
{
	public $sourcePath = '@vendor/lemonvine/yii2-adminlte/dist';
	
	public function init()
	{
		$postfix = YII_DEBUG ? '' : '.min';
		$version = Yii::$app->params['admin_version'];
		
		$this->css[] = "bootstrap/font-awesome{$postfix}.css?v=v{$version}";
		$this->css[] = "bootstrap/adminlte{$postfix}.css?v=v{$version}";
		
		parent::init();
	}
	
}
