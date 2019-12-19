<?php
/**
 * AssetBundle.php
 * @author Revin Roman
 */

namespace lemon\web;

use yii\web\AssetBundle as Base;

/**
 * Class AssetBundle
 * @package lemon\web
 */
class AssetBundle extends Base
{
	public $sourcePath = '@vendor/fortawesome/font-awesome';
	
	public $css = [
		'css/fontawesome.min.css',
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
			return preg_match('%(/|\\\\)(webfonts|css)%', $from);
		};
	}
}
