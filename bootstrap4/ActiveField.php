<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2018
 * @package yii2-widgets
 * @version 3.4.1
 */

namespace lemon\bootstrap4;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * Extends the ActiveField component to handle various bootstrap4 form handle input groups.
 *
 * Example(s):
 * ```php
 *    echo $this->form->field($model, 'email', ['prepend'=>'@ ', addon' => ['class'=>'abc']]);
 *    echo $this->form->field($model, 'phone', ['prepend' => ['type'=>'icon', 'content'=>'phone']]);
 *    echo $this->form->field($model, 'amount_paid', ['append' => ['type'=>'button', 'content'=>'go', 'options'=>['onclick'=>'javascript:;']]);
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
	
	
	public function render($content = null){
		$this->buildTemplate();
		return parent::render($content);
	}
	
	public function dropdownList($items, $options = [])
	{
		return \yii\widgets\ActiveField::dropdownList($items, $options);
	}
	
	public function radioList($items, $options = [])
	{
		if ($this->form->validationStateOn === ActiveForm::VALIDATION_STATE_ON_INPUT) {
			$this->addErrorClassIfNeeded($options);
		}
		
		$this->addRoleAttributes($options, 'radiogroup');
		$this->addAriaAttributes($options);
		$this->adjustLabelFor($options);
		//$this->_skipLabelFor = true;
		$this->parts['{input}'] = Html::activeRadioList($this->model, $this->attribute, $items, $options);
		
		return $this;
	}
	
	protected function buildTemplate(){
		$this->generateAddon();
		
	}
	
	/**
	 * Generates the prepend or append  markup
	 */
	protected function generateAddon()
	{
		if (empty($this->prepend) && empty($this->append)){
			return;
		}
		$prepend = $this->renderAddonItem('prepend');
		$append = $this->renderAddonItem('append');
		$content = $prepend . '{input}' . $append;
		$group = $this->addon;
		Html::addCssClass($group, 'input-group');
		
		$content = Html::tag('div', $content, $group);
		$this->template = strtr($this->template, ['{input}'=>$content]);
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
		
		$content = Html::tag('div', $content, ['class' => "input-group-{$pos}"]);
		return $content;
	}
}