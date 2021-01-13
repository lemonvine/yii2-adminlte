<?php

namespace lemon\assets;

use Yii;
use yii\web\AssetBundle;

class SummernoteAsset extends AssetBundle
{
	
	public $sourcePath = '@vendor/lemonvine/yii2-adminlte/dist';
	
	public $depends = [
		'lemon\assets\AdminlteAsset',
		'lemon\assets\CodemirrorAsset',
	];
	
	public function init()
	{
		$postfix = YII_DEBUG ? '' : '.min';
		$version = Yii::$app->params['admin_version'];
		$this->js[] = "summernote/summernote-bs4{$postfix}.js?v=v{$version}";
		$this->css[] = "summernote/summernote-bs4{$postfix}.css?v=v{$version}";
		parent::init();
	}
	
}
