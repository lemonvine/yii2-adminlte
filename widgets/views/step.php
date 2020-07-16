<?php
	$steps = $this->context->steps;
	$current = $this->context->current;
	$width = $current? 1 / (count($steps)) * ($current - 1) * 100 : 0;
?>

<style>
	ul,li {margin:0;padding:0;}
	.ui-step-wrap {position:relative;width:90%;margin:20px auto 40px auto;}
	.ui-step-wrap .ui-step-bg,.ui-step-wrap .ui-step-progress {height:6px;position:absolute;top:50px;left:0;}
	.ui-step-wrap .ui-step-bg {width:100%;background:#ddd;}
	.ui-step-wrap .ui-step-progress {background:#64BD2E;}
	.ui-step-wrap .ui-step {position:relative;z-index:1;list-style:none;display:flex;}
	.ui-step-wrap .ui-step:after {content:'';display:table;clear:both;}
	.ui-step-wrap .ui-step .ui-step-item {float:left;flex:1;}
	.ui-step-wrap .ui-step .ui-step-item div {text-align:center;color:#625454;}
	.ui-step-wrap .ui-step .ui-step-item .ui-step-item-num {margin-top:18px;}
	.ui-step-wrap .ui-step .ui-step-item .ui-step-item-num span {display:inline-block;width:26px;height:26px;line-height:26px;text-align:center; border-radius:50%;background:#dad9d9;}
	.ui-step-wrap .ui-step .ui-step-item.active .ui-step-item-num span {color:#fff;background:#64BD2E;}
	.ui-step-wrap .ui-step .ui-step-item.action .ui-step-item-num{margin-top:16px}
	.ui-step-wrap .ui-step .ui-step-item.action .ui-step-item-num span {width:32px;height:32px;line-height:32px;}
</style>

<div class="ui-step-wrap">
	<div class="ui-step-bg"></div>
	<div class="ui-step-progress" style="width:<?= $width?>%"></div>
	<ul class="ui-step">
		<?php foreach ($steps as $key=>$step):?>
			<li class="ui-step-item <?=$key <= $current?'active':'' ?> <?=$key== $current?'action':'' ?>"><div class="ui-step-item-title"><?=$step?></div><div class="ui-step-item-num"><span><?=$key?></span></div></li>
		<?php endforeach;?>
	</ul>
</div>