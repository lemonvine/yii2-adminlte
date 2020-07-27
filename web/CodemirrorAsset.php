<?php

namespace lemon\web;

use yii\web\AssetBundle;

class CodemirrorAsset extends AssetBundle
{
	/** @var string */
	public $sourcePath = '@vendor/npm-asset/codemirror';
	
	public $publishOptions = [
		'only' => [
			'lib/codemirror.css',
			'theme/monokai.css',
			'lib/codemirror.js',
			'mode/xml/xml.js',
		]
	];
	/**
	 * @inheritdoc
	 */
	public function init()
	{
		$this->css[] = 'lib/codemirror.css';
		$this->css[] = 'theme/monokai.css';

		$this->js[] = 'lib/codemirror.js';
		$this->js[] = 'mode/xml/xml.js';

		parent::init();
	}
}
