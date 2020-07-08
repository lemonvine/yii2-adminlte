<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2018
 * @package yii2-widgets
 * @version 3.4.1
 */

namespace lemon\bootstrap4;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use lemon\web\AdminLteAsset;

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
			$options['item'] = function ($i, $label, $name, $checked, $value) use (
				$itemOptions,
				$encode,
				$itemCount,
				$error
			) {
				$options = array_merge($this->checkOptions, [
					'label' => $encode ? Html::encode($label) : $label,
					'id' => $name.'_'.$value
				], $itemOptions);
				$wrapperOptions = ArrayHelper::remove($options, 'wrapperOptions', ['class' => ['icheck-primary', 'd-inline']]);
				if ($this->inline) {
					Html::addCssClass($wrapperOptions, 'custom-control-inline');
				}
				$checkboxAttrs = ['id'=>$options['id'],'value'=>$value];
				if(isset($options['individuals'])){
					$individuals = $options['individuals'];
					
					if(isset($individuals[$value])){
						$checkboxAttrs = array_merge($checkboxAttrs, $individuals[$value]);
					}
				}

				$html = Html::beginTag('div', $wrapperOptions) . "\n" .
				Html::checkbox($name,$checked, $checkboxAttrs). "\n" .
				Html::label($options['label'],$options['id']) . "\n" ;
				if ($itemCount === $i) {
					$html .= $error . "\n";
				}
				$html .= Html::endTag('div') . "\n";

				return $html;
			};
		}

		\yii\widgets\ActiveField::checkboxList($items, $options);
		return $this;
	}

	public function radioList($items, $options = [])
	{
		if (!isset($options['item'])) {
			$this->template = str_replace("\n{error}", '', $this->template);
			$itemOptions = isset($options['itemOptions']) ? $options['itemOptions'] : [];
			$encode = ArrayHelper::getValue($options, 'encode', true);
			$itemCount = count($items) - 1;
			$error = $this->error()->parts['{error}'];
			$options['item'] = function ($i, $label, $name, $checked, $value) use (
				$itemOptions,
				$encode,
				$itemCount,
				$error
			) {
				$options = array_merge($this->radioOptions, [
					'label' => $encode ? Html::encode($label) : $label,
					'id' => $name.'_'.$value
				], $itemOptions);
				$wrapperOptions = ArrayHelper::remove($options, 'wrapperOptions', ['class' => ['icheck-primary', 'd-inline']]);
				if ($this->inline) {
					Html::addCssClass($wrapperOptions, 'custom-control-inline');
				}
				$html = Html::beginTag('div', $wrapperOptions) . "\n" .
				Html::radio($name,$checked,['id'=>$options['id'],'value'=>$value]). "\n" .
				Html::label($options['label'],$options['id']) . "\n" ;
				if ($itemCount === $i) {
					$html .= $error . "\n";
				}
				$html .= Html::endTag('div') . "\n";

				return $html;
			};
		}

		parent::radioList($items, $options);
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
		$this->parts['{input}'] = $content;
		return $this;
	}
	
	public function handlebar($options=[]){
		AdminLteAsset::loadModule($this->form->getView(), 'handlebar');
		$attribute = $this->attribute;
		$hidden_id = isset($options['id'])?$options['id']:Html::getInputId($this->model, $attribute);
		$display_id= 'hb_'.$hidden_id;
		$value =  Html::getAttributeValue($this->model, $attribute);
		$hidden = Html::activeHiddenInput($this->model, $attribute, ['value'=>$value, 'id'=>$hidden_id, 'class'=>'handlebar-field', 'data-template'=>$options['template']]);
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
	protected function buildTemplate(){
		if (!empty($this->prepend) || !empty($this->append)){
			$content = $this->generateAddon();
			$group = $this->addon;
			Html::addCssClass($group, 'input-group');
			$content = Html::tag('div', $content, $group);
			$this->template = strtr($this->template, ['{input}'=>$content]);
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
		if (!is_array($config)) {
			$content =  $config;
		}
		else{
			$type = ArrayHelper::getValue($config, 'type', 'char');
			$content = ArrayHelper::getValue($config, 'content', '');
			$options = ArrayHelper::getValue($config, 'options', []);
			$class = ArrayHelper::getValue($config, 'class', '');
			
			switch ($type){
				case 'icon':
					$content =  Html::tag('i', '', ['class'=>"fa fa-$content"]);
					break;
				case 'button':
					$as_button=true;
					$options['class'] = 'btn btn-info';
					$content =  Html::button($content, $options);
					break;
				case 'char':
				default:
					break;
			}
		}
		if(!$as_button){
			$content = Html::tag('span', $content, ['class'=>'input-group-text']);
		}
		
		$content = Html::tag('div', $content, ['class' => "input-group-{$pos} {$class}"]);
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
		else{
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