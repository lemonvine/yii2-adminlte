<?php
namespace lemon\web;

use yii\web\AssetBundle;

/**
 * Class BootstrapAsset
 * @package lemon\web
 */
class PageAsset extends AssetBundle
{
	public $sourcePath = '@vendor/lemonvine/yii2-adminlte/dist';
	public $css = [
		'css/font-awesome.min.css',
		'css/adminlte.min.css',
	];
	
	public function init()
	{
		parent::init();
	}
	
}
