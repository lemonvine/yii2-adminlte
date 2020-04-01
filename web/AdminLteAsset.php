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
	
	public static function loadModule($view, $modules){
		$directoryAsset = Yii::$app->assetManager->getPublishedUrl(self::$path);
		$postfix = YII_DEBUG ? '' : '.min';
		$version = Yii::$app->params['admin_version'];
		$depend = 'lemon\web\AdminlteAsset';
		$js = [];
		$css = [];
		$needs = [];
		if(!is_array($modules)){
			$needs[] = $modules;
		}
		else{
			$needs = $modules;
		}
		foreach ($needs as $module){
			switch ($module){
				case 'bootstraptable':
					$css[] = "plugins/bootstrap-table{$postfix}.css?v=v{$version}";
					$js[] = "plugins/bootstrap-table{$postfix}.js?v=v{$version}";
					break;
				case 'icheck':
					$css[] = "plugins/icheck-bootstrap{$postfix}.css?v=v{$version}";
					break;
				case 'handlebar':
					$js[] = "plugins/handlebars{$postfix}.js?v=v{$version}";
					break;
				case 'viewer':
					$css[] = "viewer/viewer{$postfix}.css?v=v{$version}";
					$js[] = "viewer/viewer{$postfix}.js?v=v{$version}";
					break;
				case 'datepicker':
					$js[] = "laydate/laydate{$postfix}.js?v=v{$version}";
					break;
				case 'ztree':
					$css[] = "ztree/css/ztree/ztree{$postfix}.css?v=v{$version}";
					$js[] = "ztree/js/jquery.ztree.all-3.5{$postfix}.js?v=v{$version}";
					break;
				case 'gallery':
					$css[] = "gallery/gallery{$postfix}.css?v=v{$version}";
					$js[] = "plugins/outclick{$postfix}.js?v=v{$version}";
					$js[] = "gallery/gallery{$postfix}.js?v=v{$version}";
					break;
				default:
					if(substr($module,0,2)=='js'){
						$js[] = "{$module}{$postfix}.js?v=v{$version}";
					}
					elseif(substr($module,0,3)=='css'){
						$css[] = "{$module}{$postfix}.js?v=v{$version}";
					}
					break;
			}
		}
		
		foreach ($css as $file){
			$view->registerCssFile($directoryAsset.DIRECTORY_SEPARATOR.$file, ['depends' => $depend]);
		}
		
		foreach ($js as $file){
			$view->registerJsFile($directoryAsset.DIRECTORY_SEPARATOR.$file, ['depends' => $depend]);
		}
		
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
