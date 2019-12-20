<?php

namespace lemon\web;

use yii\web\AssetBundle;

class SummernoteLanguageAsset extends AssetBundle
{
	/** @var string */
	public $language;
	/** @var string */
	public $sourcePath = '@vendor/npm-asset/summernote/lang';
	/** @var array */
	public $depends = [
		'lemon\web\SummernoteAsset'
	];

	/**
	 * @inheritdoc
	 */
	public function registerAssetFiles($view)
	{
		$this->js[] = 'summernote-' . $this->language . '.js';
		parent::registerAssetFiles($view);
	}
}
