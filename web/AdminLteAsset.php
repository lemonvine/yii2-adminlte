<?php
namespace lemon\web;

use yii\web\AssetBundle;

/**
 * AdminLte AssetBundle
 * @since 0.1
 */
class AdminLteAsset extends AssetBundle
{
	public $sourcePath = '@vendor/lemonvine/yii2-adminlte/dist';
	public $css = [
		'css/adminlte.min.css',
	];
	public $js = [
		'js/adminlte.min.js'
	];
	public $depends = [
		'lemon\web\AssetBundle',
		'yii\web\YiiAsset',
		'yii\bootstrap4\BootstrapAsset',
		'yii\bootstrap4\BootstrapPluginAsset',
	];
}
