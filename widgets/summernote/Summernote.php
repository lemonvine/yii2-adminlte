<?php

namespace lemon\widgets\summernote;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;
use lemon\web\CodemirrorAsset;
use lemon\web\SummernoteAsset;
use lemon\web\SummernoteLanguageAsset;
use lemon\web\AdminLteAsset;

class Summernote extends InputWidget
{
	/** @var array */
	private $defaultOptions = ['class' => 'form-control'];
	/** @var array */
	private $defaultClientOptions = [
		'lang' => 'zh-CN',
		'height'=>'400',
		'width'=>'100%',
		'toolbar'=>[
			['style', ['style']],
			['font', ['bold', 'italic', 'underline', 'clear']],
			['fontname', ['fontname']],
			['color', ['color']],
			['para', ['ul', 'ol', 'paragraph']],
			['height', ['height']],
			['table', ['table']],
			['insert', [ 'hr']],
			//['view', ['fullscreen']]
		]
	];
	/** @var array */
	public $options = [];
	
	/** @var array */
	public $clientOptions = [];
	
	public function init()
	{
		AdminLteAsset::loadModule($this->getView(), ['bundle']);

		$this->options = array_merge($this->defaultOptions, $this->options);
		$this->clientOptions = array_merge($this->defaultClientOptions, $this->clientOptions);
		parent::init();
	}
	
	public function run()
	{
		$this->registerAssets();
		echo $this->hasModel()
		? Html::activeTextarea($this->model, $this->attribute, $this->options)
		: Html::textarea($this->name, $this->value, $this->options);
		$clientOptions = empty($this->clientOptions)
		? null
		: Json::encode($this->clientOptions);
		
		
		$this->getView()->registerJs('jQuery( "#' . $this->options['id'] . '" ).summernote(' . $clientOptions . ');');
	}
	private function registerAssets()
	{
		$view = $this->getView();
		if (ArrayHelper::getValue($this->clientOptions, 'codemirror')) {
			CodemirrorAsset::register($view);
		}
		
		SummernoteAsset::register($view);
		$language = ArrayHelper::getValue($this->clientOptions, 'lang', null);
		if ($language) {
			SummernoteLanguageAsset::register($view)->language = $language;
		}
	}

}
