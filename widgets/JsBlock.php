<?php

namespace lemon\widgets;

use yii\helpers\Html;
use yii\base\Widget;


class JsBlock extends Widget{
	
	private $block = null;
	private $need_end=true;
	public function init()
	{
	}
	
	public function run()
	{
		if($this->need_end){
			
		}
	}
	public function block($content){
		$this->block = $content;
		return $this->builder();
	}
	
	private function builder(){
		$options = ['type'=>'text/javascript'];
		$script = Html::beginTag('script', $options);
		if(empty($this->block)){
			$block = ob_get_clean();
			if ($this->renderInPlace) {
				throw new \Exception("not implemented yet ! ");
				// echo $block;
			}
			$block = trim($block) ;
			/*
			 $jsBlockPattern  = '|^<script[^>]*>(.+?)</script>$|is';
			 if(preg_match($jsBlockPattern,$block)){
			 $block =  preg_replace ( $jsBlockPattern , '${1}'  , $block );
			 }
			 */
			$jsBlockPattern  = '|^<script[^>]*>(?P<block_content>.+?)</script>$|is';
			if(preg_match($jsBlockPattern,$block,$matches)){
				$block =  $matches['block_content'];
			}
		}
		else{
			$block = $this->block;
		}
		$script .= $block.Html::endTag('script');
		return $script;
		
	}
}