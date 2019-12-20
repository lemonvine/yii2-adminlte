<?php
namespace lemon\web;

use yii\web\AssetBundle;

/**
 * Class BootstrapAsset
 * @package lemon\web
 */
class BootstrapAsset extends AssetBundle
{
	public $sourcePath = '@vendor/npm-asset/bootstrap/js/dist';
	
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
		parent::init();

		$this->publishOptions['beforeCopy'] = function ($from, $to) {
			return preg_match('%(/|\\\\)(js)%', $from);
		};
	}
}
