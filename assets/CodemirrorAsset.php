<?php

namespace lemon\assets;

use yii\web\AssetBundle;

class CodemirrorAsset extends AssetBundle
{
	public $sourcePath = '@vendor/lemonvine/yii2-adminlte/dist';
	
	/**
	 * @inheritdoc
	 */
	public function init()
	{
		$this->css[] = 'summernote/codemirror.css';
		$this->css[] = 'summernote/monokai.css';

		$this->js[] = 'summernote/codemirror.js';
		$this->js[] = 'summernote/xml.js';

		parent::init();
	}
}
