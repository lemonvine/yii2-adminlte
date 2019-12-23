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

				$html = Html::beginTag('div', $wrapperOptions) . "\n" .
				Html::checkbox($name,$checked,['id'=>$options['id'],'value'=>$value]). "\n" .
				Html::label($options['label'],$options['id']) . "\n" ;
                if ($itemCount === $i) {
                    $html .= $error . "\n";
                }
                $html .= Html::endTag('div') . "\n";

                return $html;
            };
        }

        parent::checkboxList($items, $options);
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
}