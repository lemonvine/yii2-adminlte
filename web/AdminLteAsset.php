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
		'css/alt/adminlte.plugins.min.css',
	];
	public $js = [
		'js/adminlte.min.js'
	];
	public $depends = [
		'yii\web\JqueryAsset',
		'lemon\web\AssetBundle',
	];
}
