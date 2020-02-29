<?php
namespace lemon\web;

use Yii;
use yii\web\AssetBundle;
use yii\web\View;

/**
 * AdminLte AssetBundle
 * @since 0.1
 */
class AdminLteAsset extends AssetBundle
{
	public static $path = '@vendor/lemonvine/yii2-adminlte/dist';
	
	public $depends = [
		'yii\web\JqueryAsset',
	];
	
	public function init()
	{
		$this->sourcePath = self::$path;
		$postfix = YII_DEBUG ? '' : '.min';
		$version = Yii::$app->params['admin_version'];
		
		$this->js[] = "js/adminlte{$postfix}.js?v=v{$version}";
		$this->js[] = "layer/layer{$postfix}.js?v=v{$version}";
		$this->js[] = "js/adminv{$postfix}.js?v=v{$version}";
		//$this->js[] = "plugins/popper.min{$postfix}.js?v=v{$version}";
		
		$this->css[] = "css/font-awesome{$postfix}.css?v=v{$version}";
		$this->css[] = "css/adminlte{$postfix}.css?v=v{$version}";
		
		parent::init();
	}
	//定义按需加载JS方法，注意加载顺序在最后
	public static function preScript($view, $jsfile) {
		$directoryAsset = Yii::$app->assetManager->getPublishedUrl(self::$path);
		$postfix = YII_DEBUG ? '' : '.min';
		$version = Yii::$app->params['admin_version'];
		$view->registerJsFile($directoryAsset.DIRECTORY_SEPARATOR.$jsfile.$postfix.".js?v=v".$version, ['position' => View::POS_HEAD]);
	}
	
	//定义按需加载JS方法，注意加载顺序在最后
	public static function addScript($view, $jsfile) {
		$directoryAsset = Yii::$app->assetManager->getPublishedUrl(self::$path);
		$postfix = YII_DEBUG ? '' : '.min';
		$version = Yii::$app->params['admin_version'];
		$view->registerJsFile($directoryAsset.DIRECTORY_SEPARATOR.$jsfile.$postfix.".js?v=v".$version, ['depends' => self::className()]);
	}
	
	//定义按需加载css方法，注意加载顺序在最后
	public static function addCss($view, $cssfile) {
		$directoryAsset = Yii::$app->assetManager->getPublishedUrl(self::$path);
		$postfix = YII_DEBUG ? '' : '.min';
		$version = Yii::$app->params['admin_version'];
		$view->registerCssFile($directoryAsset.DIRECTORY_SEPARATOR.$cssfile.$postfix.".css?v=v".$version, ['depends' => self::className()]);
	}
	
	public static function BootstrapTable($view){
		$directoryAsset = Yii::$app->assetManager->getPublishedUrl(self::$path);
		$postfix = YII_DEBUG ? '' : '.min';
		$version = Yii::$app->params['admin_version'];
		$view->registerCssFile($directoryAsset.DIRECTORY_SEPARATOR."plugins/bootstrap-table{$postfix}.css?v=v{$version}", ['depends' => self::className()]);
		$view->registerJsFile($directoryAsset.DIRECTORY_SEPARATOR."plugins/bootstrap-table{$postfix}.js?v=v{$version}", ['depends' => self::className()]);
	}
	
	public static function BootstrapICheck($view){
		$directoryAsset = Yii::$app->assetManager->getPublishedUrl(self::$path);
		$postfix = YII_DEBUG ? '' : '.min';
		$version = Yii::$app->params['admin_version'];
		$view->registerCssFile($directoryAsset.DIRECTORY_SEPARATOR."plugins/icheck-bootstrap{$postfix}.css?v=v{$version}", ['depends' => self::className()]);
	}
	
	public static function Handlebars($view){
		$directoryAsset = Yii::$app->assetManager->getPublishedUrl(self::$path);
		$postfix = YII_DEBUG ? '' : '.min';
		$version = Yii::$app->params['admin_version'];
		$view->registerJsFile($directoryAsset.DIRECTORY_SEPARATOR."plugins/handlebars{$postfix}.js?v=v{$version}", ['depends' => self::className()]);
	}
	
	public static function ImageView($view){
		$directoryAsset = Yii::$app->assetManager->getPublishedUrl(self::$path);
		$postfix = YII_DEBUG ? '' : '.min';
		$version = Yii::$app->params['admin_version'];
		$view->registerCssFile($directoryAsset.DIRECTORY_SEPARATOR."viewer/viewer{$postfix}.css?v=v{$version}", ['depends' => self::className()]);
		$view->registerJsFile($directoryAsset.DIRECTORY_SEPARATOR."viewer/viewer{$postfix}.js?v=v{$version}", ['depends' => self::className()]);
	}
	
	public static function DatePicker($view){
		$directoryAsset = Yii::$app->assetManager->getPublishedUrl(self::$path);
		$postfix = YII_DEBUG ? '' : '.min';
		$version = Yii::$app->params['admin_version'];
		$view->registerJsFile($directoryAsset.DIRECTORY_SEPARATOR."laydate/laydate{$postfix}.js?v=v{$version}", ['depends' => self::className()]);
	}
	
	public static function Ztree($view){
		$directoryAsset = Yii::$app->assetManager->getPublishedUrl(self::$path);
		$postfix = YII_DEBUG ? '' : '.min';
		$version = Yii::$app->params['admin_version'];
		$view->registerCssFile($directoryAsset.DIRECTORY_SEPARATOR."ztree/css/ztree/ztree{$postfix}.css?v=v{$version}", ['depends' => self::className()]);
		$view->registerJsFile($directoryAsset.DIRECTORY_SEPARATOR."ztree/js/jquery.ztree.all-3.5{$postfix}.js?v=v{$version}", ['depends' => self::className()]);
	}
}
