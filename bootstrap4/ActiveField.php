<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2018
 * @package yii2-widgets
 * @version 3.4.1
 */

namespace lemon\bootstrap4;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use lemon\web\AdminLteAsset;
use yii\helpers\Json;

/**
 * Extends the ActiveField component to handle various bootstrap4 form handle input groups.
 *
 * Example(s):
 * ```php
 *	echo $this->form->field($model, 'email', ['prepend'=>'@ ', addon' => ['class'=>'abc']]);
 *	echo $this->form->field($model, 'phone', ['prepend' => ['type'=>'icon', 'content'=>'phone']]);
 *	echo $this->form->field($model, 'amount_paid', ['append' => ['type'=>'button', 'content'=>'go', 'options'=>['onclick'=>'javascript:;']]);
 * ```
 *
 * @property ActiveForm $form
 *
 * @author Lemon
 * @since  2.0
 */
class ActiveField extends \yii\bootstrap4\ActiveField
{
	public $addon = [];
	public $prepend = [];
	public $append = [];
	public $exclass='';
	
	public $dateOptions = ['save'=>'timestamp', 'format'=>'', 'type'=>'', 'readonly'=>false, 'callback'=>'', 'class' => '', 'btn'=>''];
	
	public function render($content = null){
		$this->buildTemplate();
		return parent::render($content);
	}
	
	public function dropdownList($items, $options = [])
	{
		return \yii\widgets\ActiveField::dropdownList($items, $options);
	}

	public $checkOptions = [
		'class' => ['widget' => 'custom-control-input'],
		'labelOptions' => [
			'class' => ['widget' => '']
		]
	];

	public $radioOptions = [
		'class' => ['widget' => 'custom-control-input'],
		'labelOptions' => [
			'class' => ['widget' => '']
		]
	];
	
	public function checkboxList($items, $options = [])
	{
		if (!isset($options['item'])) {
			$this->template = str_replace("\n{error}", '', $this->template);
			$itemOptions = isset($options['itemOptions']) ? $options['itemOptions'] : [];
			$encode = ArrayHelper::getValue($options, 'encode', true);
			$itemCount = count($items) - 1;
			$error = $this->error()->parts['{error}'];
			if (!array_key_exists('id', $options)) {
				$input_id = Html::getInputId($this->model, $this->attribute);
			}else{
				$input_id = $options['id'];
			}
			$options['item'] = function ($i, $label, $name, $checked, $value) use (
				$itemOptions,
				$encode,
				$itemCount,
				$error,
				$input_id
			) {
				
				$label = $encode ? Html::encode($label) : $label;
				$options = array_merge([
					'id' => $input_id.'_'.$value,
					'value' =>$value,
				], $itemOptions);
				
				if(isset($options['individuals'])){
					$individuals = ArrayHelper::remove($options, 'individuals', []);
					if(isset($individuals[$value])){
						$options = array_merge($options, $individuals[$value]);
					}
				}
				
				$wrapperOptions = ArrayHelper::remove($options, 'wrapperOptions', ['class' => ['icheck-primary', 'd-inline']]);
				if ($this->inline) {
					Html::addCssClass($wrapperOptions, 'custom-control-inline', $wrapperOptions);
				}
				$content = Html::checkbox($name,$checked, $options). "\n" . Html::label($label, $options['id']) . "\n" .($itemCount===$i?$error:'');
				$html = Html::tag('div', $content, $wrapperOptions);
				
				return $html;
			};
		}

		\yii\widgets\ActiveField::checkboxList($items, $options);
		return $this;
	}
	
	/**
	 * 单选钮组
	 * {@inheritDoc}
	 * @see \yii\bootstrap4\ActiveField::radioList()
	 */
	public function radioList($items, $options = [])
	{
		if (!isset($options['item'])) {
			$this->template = str_replace("\n{error}", '', $this->template);
			$itemOptions = isset($options['itemOptions']) ? $options['itemOptions'] : [];
			$encode = ArrayHelper::getValue($options, 'encode', true);
			$itemCount = count($items) - 1;
			$error = $this->error()->parts['{error}'];
			if (!array_key_exists('id', $options)) {
				$input_id = Html::getInputId($this->model, $this->attribute);
			}else{
				$input_id = $options['id'];
			}
			$pairId = ArrayHelper::remove($options, 'pair', '');
			$options['item'] = function ($i, $label, $name, $checked, $value) use (
				$itemOptions,
				$encode,
				$itemCount,
				$error,
				$input_id,
				$pairId
			) {
				$label = $encode ? Html::encode($label) : $label;
				$options = array_merge([
					'id' => $input_id.'_'.$value,
					'value' =>$value,
				], $itemOptions);
				if(!empty($pairId)){
					$options['data-pair'] = $pairId;
					$options['data-text'] = $label;
				}
				$wrapperOptions = ArrayHelper::remove($options, 'wrapperOptions', ['class' => ['icheck-primary', 'd-inline']]);
				if ($this->inline) {
					Html::addCssClass($wrapperOptions, 'custom-control-inline', $wrapperOptions);
				}
				$content = Html::radio($name,$checked, $options). "\n" . Html::label($label, $options['id']) . "\n" .($itemCount===$i?$error:'');
				$html = Html::tag('div', $content, $wrapperOptions);
				
				return $html;
			};
		}

		\yii\widgets\ActiveField::radioList($items, $options);
		return $this;
	}
	
	/**
	 * 单选钮和单选钮值组件
	 * 对应两个attribute
	 * @param array $items
	 * @param array $options 含pair,值为$items的value所指的attribute，默认加_name
	 * @return \lemon\bootstrap4\ActiveField
	 */
	public function radioPair($items, $options = []){
		$pairAttribute = ArrayHelper::remove($options, 'pair', $this->attribute.'_name');
		$input_id = Html::getInputId($this->model, $pairAttribute);
		$pairOptions =[
			'class'=>'form-control',
			'id'=>$input_id,
		];
		$hidden = Html::activeHiddenInput($this->model, $pairAttribute, $pairOptions);
		$options['pair'] = $input_id;
		$this->radioList($items, $options);
		$input = $this->parts['{input}'];
		$options = array_merge($this->inputOptions, $options);
		$this->parts['{input}'] = $input.$hidden;
		return $this;
	}
	
	public function datePicker($options=[]){
		$options = array_merge($this->dateOptions, $options);
		if ($this->form->validationStateOn === ActiveForm::VALIDATION_STATE_ON_INPUT) {
			$this->addErrorClassIfNeeded($options);
		}
		$this->addAriaAttributes($options);
		$this->adjustLabelFor($options);
		
		//$this->id = empty($this->id)?Html::getInputId($this->model, $this->attribute) : $this->id;
		$content = $this->renderDatePicker($options, $this->attribute);
		$content = Html::tag('div', $content, ['class'=>'input-group']);
		$this->parts['{input}'] = $content;
		return $this;
		
	}
	
	/**
	 * 取范围值
	 * @param unknown $options
	 * @param unknown $type
	 * @param unknown $items
	 */
	public function fieldRange($options=[], $type=ActiveForm::INPUT_TEXT, $items=[]){
		
		switch ($type){
			case ActiveForm::INPUT_TEXT:
			case ActiveForm::INPUT_LIST:
				$options = array_merge($this->inputOptions, $options);
				break;
			case ActiveForm::INPUT_DATE:
				$options = array_merge($this->dateOptions, $options);
				break;
		}
		
		if ($this->form->validationStateOn === ActiveForm::VALIDATION_STATE_ON_INPUT) {
			$this->addErrorClassIfNeeded($options);
		}
		
		$this->addAriaAttributes($options);
		$this->adjustLabelFor($options);
		
		switch ($type){
			case ActiveForm::INPUT_TEXT:
				$begin = Html::activeTextInput($this->model, $this->attribute.'[0]', $options);
				$end = Html::activeTextInput($this->model, $this->attribute.'[1]', $options);
				break;
			case ActiveForm::INPUT_LIST:
				$begin = Html::activeDropDownList($this->model, $this->attribute.'[0]', $items, $options);
				$end= Html::activeDropDownList($this->model, $this->attribute.'[1]', $items, $options);
				break;
			case ActiveForm::INPUT_DATE:
				$begin = $this->renderDatePicker($options, $this->attribute.'[0]');
				$end= $this->renderDatePicker($options, $this->attribute.'[1]');
				break;
		}
		
		if (empty($this->prepend) || empty($this->append)){
			$addon = $this->generateAddon();
			$begin= strtr($addon, ['{input}'=>$begin]);
			$end= strtr($addon, ['{input}'=>$end]);
		}
		//$connector = Html::tag('div', '', ['class'=>'']);
		$connector= '<div class="bg-default text-primary">↔</div>';
		$content = Html::tag('div', $begin.$connector.$end, ['class'=>'input-group']);
		$this->parts['{input}'] = $content;
		$this->prepend=[];
		$this->append=[];
		return $this;
	}
	
	/**
	 * 显示不需编辑内容
	 * @param array $attributes
	 * @param array $options
	 * @return \lemon\bootstrap4\ActiveField
	 */
	public function display($attributes=[], $options=[]){
		$this->adjustLabelFor($options);
		
		if(count($attributes)==0){
			$attributes[] = $this->attribute;
		}
		$separator = ',';
		if(isset($options['separator'])){
			$separator = $options['separator'];
		}
		$content = '';
		foreach ($attributes as $attr){
			$content .= (empty($content)?'':$separator).Html::getAttributeValue($this->model, $attr);
		}
		$this->template = "{label}<div class=\"flex\">{input}</div>";
		$this->parts['{input}'] = $content;
		return $this;
	}
	
	public function handlebar($options=[]){
		AdminLteAsset::loadModule($this->form->getView(), 'handlebar');
		$attribute = $this->attribute;
		$hidden_id = isset($options['id'])?$options['id']:Html::getInputId($this->model, $attribute);
		$display_id= 'hb_'.$hidden_id;
		$sort = ArrayHelper::remove($options, 'sort', 'asc');
		$value =  Html::getAttributeValue($this->model, $attribute);
		$hidden = Html::activeHiddenInput($this->model, $attribute, ['value'=>$value, 'id'=>$hidden_id, 'class'=>'handlebar-field', 'data-template'=>$options['template'], 'data-sort'=>$sort]);
		$handlebar = Html::tag('div', '', ['id'=>$display_id, 'class'=>'handlebar-info', 'data-hidden'=>$hidden_id]);
		$this->parts['{input}'] = $hidden.$handlebar;
		return $this;
	}
	
	public function combo($items=[], $options=[]){
		AdminLteAsset::loadModule($this->form->getView(), 'combo');
		$options = array_merge($this->inputOptions, $options);
		
		if ($this->form->validationStateOn === ActiveForm::VALIDATION_STATE_ON_INPUT) {
			$this->addErrorClassIfNeeded($options);
		}
		
		$this->addAriaAttributes($options);
		$this->adjustLabelFor($options);
		$options['autocomplete'] = 'off';
		$options['class'] .= ' combo';
		//var_dump($options);die;
		
		$content = Html::activeTextInput($this->model, $this->attribute, $options);
		
		$separator = "\n";
		$li = [];
		$i=1;
		foreach ($items as $index => $item) {
			$li[] = Html::tag('li', $item, ['class'=>'combo-item', 'data-value'=>$index, 'data-index'=>$i]);
			$i++;
		}
		
		$ul = Html::tag('ul', $separator . implode($separator, $li) . $separator, ['class'=>'combo-dropdown']);
		
		$content.=$ul;
		
		$this->parts['{input}'] = $content;
		
		return $this;
	}
	
	/**
	 * 隐藏域
	 * @see \yii\widgets\ActiveField::hiddenInput()
	 */
	public function hiddenInput($options = [])
	{
		$options = array_merge($this->inputOptions, $options);
		$this->parts['{input}'] = Html::activeHiddenInput($this->model, $this->attribute, $options);
		$this->template = '{input}';
		return $this;
	}
	
	public function gallery($options = []){
		$init =['items'=>[], ];
		$options = array_merge($init, $options);
		$items = ArrayHelper::remove($options, 'items');
		$url = ArrayHelper::remove($options, 'url');
		$hidden_id = isset($options['id'])?$options['id']:Html::getInputId($this->model, $this->attribute);
		$display_id= $hidden_id.'-gl';
		$containerOptions = [
			'id'=>$display_id,
			'class'=>'container row',
		];
		$inputOptions =[
			'class'=>'form-control hidden-gallery',
			'data-item'=>Json::encode($items),
		];
		$fieldOptions = ArrayHelper::remove($options, 'fieldOptions', []);
		$inputOptions = array_merge($inputOptions, $fieldOptions);
		$input = Html::activeHiddenInput($this->model, $this->attribute, $inputOptions);
		$container = Html::tag('div', '图片空', $containerOptions);
		$button = Html::button('选择图片', ['type'=>'button', 'class'=>'btn btn-outline-success modaldialog', 'data-url'=>$url, 'data-refer'=>'#'.$hidden_id]);
		$button = Html::tag('div', $button, ['class'=>'text-right']);
		
		$this->parts['{input}'] = $input.$container.$button;
		return $this;
	}
	
	public function upload($options=[]){
		$initOptions = ['title'=>'选择文件', 'multi'=>FALSE, 'beforehand'=>FALSE, 'accept'=>'', 'isjson'=>TRUE, 'isbtn'=>FALSE, 'folder'=>''];
		$options = array_merge($initOptions, $options);
		
		$title = ArrayHelper::remove($options, 'title');
		$multi = ArrayHelper::remove($options, 'multi', FALSE);
		$isbtn = ArrayHelper::remove($options, 'isbtn', TRUE);
		$isjson = ArrayHelper::remove($options, 'isjson', TRUE);
		$beforehand = ArrayHelper::remove($options, 'beforehand', FALSE);
		$hidden_id = isset($options['id'])?$options['id']:Html::getInputId($this->model, $this->attribute);
		$value =  Html::getAttributeValue($this->model, $this->attribute);
		$inputOptions =[];
		$fileOptions= [
			'data-f'=>$options['folder'],
		];
		$ulOptions = [
			'class'=>'list-inline upload-gallery m-1',
			'id'=>'gy_'.$hidden_id
		];
		
		if($multi){
			$fileOptions['multiple'] = 'multiple';
		}
		if($beforehand){
			$inputOptions['id'] = $hidden_id;
			$inputOptions['value'] = $value;
			$fileOptions['data-bh'] = 1;
			$input = Html::activeHiddenInput($this->model, $this->attribute, $inputOptions);
			$input .= Html::fileInput('upload', '', $fileOptions);
			$ulOptions['data-for'] = $hidden_id;
		}else{
			$fileOptions['id'] = $hidden_id;
			$input = Html::activeInput('file', $this->model, $this->attribute, $fileOptions);
		}
		
		$content = "<span>$title</span>";
		$content = Html::label($content.$input, '', ['class'=>'btn btn-'.($isbtn?'':'outline-').'info upload-btn mr-1']);
		
		$html = '';
		if(!empty($value)){
			$http_path = Yii::$app->params['FILE_HTTP_PATH'];
			if($isjson){
				$medias = Json::decode($value, true);
			}else{
				$medias = [$value];
			}
			
			foreach ($medias as $media){
				if(is_array($media)){
					$media_type = $media[0];
					$media_src = $media[1];
					if($media_type=='image'){
						$media_html = '<img class="media" src="'.$http_path.$media.'" data-f="'.$media.'" data-h="'.$http_path.'" data-p="image">';
					}elseif($media_type=='audio'){
						$media_html = '<audio class="media" controls="controls" preload="meta" src="'.$http_path.$media_src.'" data-f="'.$media_src.'" data-h="'.$http_path.'" data-p="audio"></audio>';
					}if($media_type=='video'){
						$media_html = '<video class="media" controls="controls" preload="meta" src="'.$http_path.$media_src.'" data-f="'.$media_src.'" data-h="'.$http_path.'" data-p="video"></video>';
					}
				}else{
					$media_html = '<img class="media" src="'.$http_path.$media.'" data-f="'.$media.'" data-h="'.$http_path.'" data-p="image">';
				}
				$html .= '<li class="upload-show img-thumbnail">'.$media_html.'<div class="upload-tools"><a href="javascript:;" class="delete text-danger">删除</a><a href="'.$http_path.$media.'" class="look" target="_blank">查看</a></div></li>';
			}
		}
		$gallery = Html::tag('ul', $html, $ulOptions);
		$this->parts['{input}'] = $content. $gallery;
		
		return $this;
		
	}
	
	
	public function picture($options=[]){
		$initOptions = ['title'=>'选择图片', 'multi'=>FALSE, 'beforehand'=>FALSE, 'accept'=>'', 'isbtn'=>FALSE, 'folder'=>''];
		$options = array_merge($initOptions, $options);
		
		$title = ArrayHelper::remove($options, 'title');
		$multi = ArrayHelper::remove($options, 'multi', FALSE);
		$isbtn = ArrayHelper::remove($options, 'isbtn', TRUE);
		$beforehand = ArrayHelper::remove($options, 'beforehand', FALSE);
		$hidden_id = isset($options['id'])?$options['id']:Html::getInputId($this->model, $this->attribute);
		$value =  Html::getAttributeValue($this->model, $this->attribute);
		$inputOptions =[];
		$fileOptions= [
			'data-f'=>$options['folder'],
		];
		$ulOptions = [
			'class'=>'list-inline upload-gallery m-1',
			'id'=>'gy_'.$hidden_id
		];
		
		if($multi){
			$fileOptions['multiple'] = 'multiple';
		}
		if($beforehand){
			$inputOptions['id'] = $hidden_id;
			$inputOptions['value'] = $value;
			$fileOptions['data-bh'] = 1;
			$input = Html::activeHiddenInput($this->model, $this->attribute, $inputOptions);
			$input .= Html::fileInput('upload', '', $fileOptions);
			$ulOptions['data-for'] = $hidden_id;
		}else{
			$fileOptions['id'] = $hidden_id;
			$input = Html::activeFileInput($this->model, $this->attribute, $fileOptions);
		}
		
		$content = "<span>$title</span>";
		$content = Html::label($content.$input, '', ['class'=>'btn btn-'.($isbtn?'':'outline-').'info upload-btn mr-1']);
		
		$html = '';
		if(!empty($value)){
			$http_path = Yii::$app->params['FILE_HTTP_PATH'];
			$medias = Json::decode($value, true);
			foreach ($medias as $media){
				if(is_array($media)){
					$media_type = $media[0];
					$media_src = $media[1];
					if($media_type=='image'){
						$media_html = '<img class="media" src="'.$http_path.$media.'" data-f="'.$media.'" data-h="'.$http_path.'" data-p="image">';
					}elseif($media_type=='audio'){
						$media_html = '<audio class="media" controls="controls" preload="meta" src="'.$http_path.$media_src.'" data-f="'.$media_src.'" data-h="'.$http_path.'" data-p="audio"></audio>';
					}if($media_type=='video'){
						$media_html = '<video class="media" controls="controls" preload="meta" src="'.$http_path.$media_src.'" data-f="'.$media_src.'" data-h="'.$http_path.'" data-p="video"></video>';
					}
				}else{
					$media_html = '<img class="media" src="'.$http_path.$media.'" data-f="'.$media.'" data-h="'.$http_path.'" data-p="image">';
				}
				$html .= '<li class="upload-show img-thumbnail">'.$media_html.'<div class="upload-tools"><a href="javascript:;" class="delete text-danger">删除</a><a href="'.$http_path.$media.'" class="look" target="_blank">查看</a></div></li>';
			}
		}
		
		//$image = Html::img(empty($value)?'':(Yii::$app->params['FILE_HTTP_PATH'].$value), ['class'=>'upload-show']);
		$gallery = Html::tag('ul', $html, $ulOptions);
		//$this->parts['{input}'] = $content.Html::tag('div', $image, ['class'=>'m-1']);
		$this->parts['{input}'] = $content. $gallery;
		
		return $this;
		
	}
	
	/**
	 * 下划线输入框
	 * options事例如下:
	 * ['label'=>'文字', 'addon'=>'㎡、', 'width'=>50, 'class'=>'auto-area', 'date'=>[]]
	 * label 前缀文字
	 * addon 后缀文字
	 * width 宽度
	 * class 类样式
	 * date 控件为日期
	 * @param array $options
	 * @return \lemon\bootstrap4\ActiveField
	 */
	public function miniFrom($options=[]){
		$inputOptions = ['class' => 'lineinput'];
		$addon = ArrayHelper::remove($options, 'addon', '');
		$class = ArrayHelper::remove($options, 'class', '');
		if(!empty($class)){
			$inputOptions['class'] .= ' '. $class;
		}
		if(isset($options['label'])){
			$label = ArrayHelper::remove($options, 'label', '');
		}else{
			$attribute = Html::getAttributeName($this->attribute);
			$label = Html::encode($this->model->getAttributeLabel($attribute));
		}
		
		if(isset($options['width'])){
			$width = ArrayHelper::remove($options, 'width', 100);
			$inputOptions['style']='width: '.$width.'px';
		}
		if(isset($options['data'])){
			foreach ($options['data'] as $key=>$val){
				$inputOptions['data-'.$key] = $val;
			}
		}
		if(isset($options['date'])){
			$inputOptions['class'] .= ' js-laydate';
			$inputOptions['data-save'] = 'string';
			$inputOptions['data-hidden'] = '';
			$inputOptions['data-type'] = '';
			$inputOptions['data-format'] = '';
		}
		
		$input = Html::activeInput('text', $this->model, $this->attribute, $inputOptions);
		
		$this->parts['{label}'] = $label;
		$this->parts['{input}'] = $input;
		$this->parts['{addon}'] = $addon;
		$this->template = '{label}{input}{addon}';
		return $this;
	}
	protected function buildTemplate(){
		if (!empty($this->prepend) || !empty($this->append)){
			$content = $this->generateAddon();
			$group = $this->addon;
			Html::addCssClass($group, 'input-group');
			$content = Html::tag('div', $content, $group);
			$this->template = strtr($this->template, ['{input}'=>$content]);
		}
		if(!empty($this->exclass)){
			$this->options['class'] .= ' '.$this->exclass;
		}
	}
	
	/**
	 * Generates the prepend or append  markup
	 */
	protected function generateAddon()
	{
		if (empty($this->prepend) && empty($this->append)){
			return '{input}';
		}
		$prepend = $this->renderAddonItem('prepend');
		$append = $this->renderAddonItem('append');
		$content = $prepend . '{input}' . $append;
		return $content;
	}
	
	/**
	 * Renders an addon item based on its configuration
	 * @param string $pos
	 * @return void|string
	 */
	protected function renderAddonItem($pos)
	{
		$config = $this->$pos;
		if (empty($config)) {
			return;
		}
		$as_button = false;
		$addonOptions = [
			'class' => "input-group-{$pos} "
		];
		if (!is_array($config)) {
			$content =  $config;
		}
		else{
			$type = ArrayHelper::getValue($config, 'type', 'char');
			$content = ArrayHelper::getValue($config, 'content', '');
			$options = ArrayHelper::getValue($config, 'options', []);
			$class = ArrayHelper::getValue($config, 'class', '');
			$addonOptions['class'] .= $class;
			
			switch ($type){
				case 'button':
					$as_button=true;
					$options['class'] = 'btn btn-info';
					$content =  Html::button($content, $options);
					break;
				case 'icon':
					$content =  Html::tag('i', '', ['class'=>"fa fa-$content"]);
				case 'char':
				default:
					$addonOptions=array_merge($addonOptions, $options);
					break;
			}
		}
		if(!$as_button){
			$content = Html::tag('span', $content, ['class'=>'input-group-text']);
		}
		
		$content = Html::tag('div', $content, $addonOptions);
		return $content;
	}
	
	protected function renderDatePicker($options, $attribute){
		AdminLteAsset::loadModule($this->form->getView(), 'datepicker');
		$hidden_id = isset($options['id'])?$options['id']:Html::getInputId($this->model, $attribute);
		$display_id= 'dt_'.$hidden_id;
		$value =  Html::getAttributeValue($this->model, $attribute);
		$hidden_options = array_merge(['value'=>$value, 'id'=>$hidden_id], $options['options']??['class'=>'form-control']);
		$hidden = Html::activeHiddenInput($this->model, $attribute, $hidden_options);
		$display_value = '';
		if($options['btn']=='long' && $value==1){
			$display_value = '2099-12-31';
		}
		elseif(!empty($value) && $options['save']=='timestamp'){
			switch (empty($options['format'])?$options['type']:'format'){
				case '':
				case 'date':
					$display_value = date('Y-m-d', $value);
					break;
				case 'format':
					$display_value = date($this->formatTurner($options['format']), $value);
					break;
				case 'year':
					$display_value = date('Y', $value);
					break;
				case 'month':
					$display_value = date('Y-m', $value);
					break;
				case 'time':
					$display_value = date('h:i:s', $value);
					break;
				case 'datetime':
					$display_value = date('Y-m-d h:i:s', $value);
					break;
			}
		}
		elseif($value!="0"){
			$display_value= $value;
		}
		$display = Html::input('text', '', $display_value, ['class'=>'form-control js-laydate '.$options['class'], 'id'=>$display_id, 'data-type'=>$options['type'], 'data-save'=>$options['save'],
			'data-callback'=>$options['callback'], 'data-format'=>$options['format'], 'data-hidden'=>$hidden_id, 'data-btn'=>$options['btn']??'','readonly'=>$options['readonly']]);
		
		$connector= '<div class="input-group-append"><div class="input-group-text text-primary"><i class="fa fa-calendar"></i></div></div>';
		return $hidden.$display.$connector;
	}
	
	private function formatTurner($format){
		$chars = ['yyyy'=>'Y', 'MM'=>'m', 'dd'=>'d', 'HH'=>'H', 'mm'=>'i', 'ss'=>'s'];
		foreach ($chars as $key=>$value){
			$format = str_replace($key, $value, $format);
		}
		return $format;
	}
}