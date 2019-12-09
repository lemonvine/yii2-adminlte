<?php

namespace lemon\bootstrap4;

use yii\web\AssetBundle;

/**
 *
 * @author lemonvine
 */
class BootstrapPluginAsset extends AssetBundle
{
	public $js = [
		//'js/bootstrap.bundle.js',
	];
	public $depends = [
		'yii\web\JqueryAsset',
	];
}
