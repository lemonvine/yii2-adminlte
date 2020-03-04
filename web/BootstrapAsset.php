<?php
namespace lemon\web;

use Yii;
use yii\web\AssetBundle;

/**
 * Class BootstrapAsset
 * @package lemon\web
 */
class BootstrapAsset extends AssetBundle
{
	public static $path = '@vendor/npm-asset/bootstrap/js/dist';
	
	public $depends = [
		'yii\web\JqueryAsset',
	];
	
	public $js = [
		'util.js',
		'tab.js',
	];

	/**
	 * Initializes the bundle.
	 * Set publish options to copy only necessary files (in this case css and font folders)
	 * @codeCoverageIgnore
	 */
	public function init()
	{
		$this->sourcePath = self::$path;
		parent::init();

		$this->publishOptions['beforeCopy'] = function ($from, $to) {
			return preg_match('%(/|\\\\)(js)%', $from);
		};
	}
	
	//定义按需加载JS方法，注意加载顺序在最后
	public static function addScript($view, $jsfile) {
		$directoryAsset = Yii::$app->assetManager->getPublishedUrl(self::$path);
		$postfix = YII_DEBUG ? '' : '.min';
		$version = Yii::$app->params['admin_version'];
		$view->registerJsFile($directoryAsset.DIRECTORY_SEPARATOR.$jsfile.".js?v=v".$version, ['depends' => self::className()]);
	}
}
