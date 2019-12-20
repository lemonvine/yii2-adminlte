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
		'css/font-awesome.min.css',
		'css/adminlte.min.css',
		'css/alt/adminlte.plugins.min.css',
		'viewer/viewer.css',
	];
	public $js = [
		'js/adminlte.min.js',
		'layer/layer.js',
		//'js/handlebars-v4.0.5.js',
		'laydate/laydate.js',
		"viewer/viewer.js?v=3.9.0",
		'js/adminv.js?v=1',
		//'js/common.js?v=1.0',
	];
	public $depends = [
		'yii\web\JqueryAsset',
		//'lemon\web\AssetBundle',
	];
	
	//定义按需加载JS方法，注意加载顺序在最后
	public static function addScript($view, $jsfile) {
		$view->registerJsFile($jsfile, ['depends' => self::className()]);
	}
	
	//定义按需加载css方法，注意加载顺序在最后
	public static function addCss($view, $cssfile) {
		$view->registerCssFile($cssfile, ['depends' => self::className()]);
	}
}
