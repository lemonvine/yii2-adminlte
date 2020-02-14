<?php

namespace lemon\web;

use yii\web\AssetBundle;

class SummernoteAsset extends AssetBundle
{
	
	/** @var string */
	public $sourcePath = '@vendor/npm-asset/summernote/dist';
	
	/** @var array */
	public $depends = [
		'lemon\web\AdminlteAsset',
	    'lemon\web\BootstrapAsset',
	    'lemon\web\CodemirrorAsset',
	];
	/**
	 * @inheritdoc
	 */
	public function init()
	{
		
		$postfix = YII_DEBUG ? '' : '.min';
		$this->css[] = 'summernote-bs4.css';
		$this->js[] = 'summernote-bs4' . $postfix . '.js';
		parent::init();
	}
	
	
}
