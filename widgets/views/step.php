<?php
	$steps = $this->context->steps;
	$current = $this->context->current;
	$width = 1 / (count($steps)) * 100;
	$progress_width = $current? $width * ($current - 0.5) : 0;
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
	<div id="ui-step-progress" class="ui-step-progress" style="width:<?= $progress_width?>%"></div>
	<ul id="ui-step" class="ui-step">
		<?php foreach ($steps as $key=>$step):?>
			<li id="progress_<?=$key ?>" class="ui-step-item <?=$key <= $current?'active':'' ?> <?=$key== $current?'action':'' ?>"><div class="ui-step-item-title"><?=$step?></div><div class="ui-step-item-num"><span><?=$key?></span></div></li>
		<?php endforeach;?>
	</ul>
</div>
<script>
var progressMove = function(num){
	var _list = $("#ui-step>li");
	$(_list).removeClass('active').removeClass('action');
	var _left = $("#ui-step-progress").offset().left;
	var _pos = $("#progress_"+num+" span").offset().left;
	$("#ui-step-progress").css('width', _pos-_left+5);
	for(var i=1, l=_list.length+1; i<l; i++ ){
		if(i<num){
			$("#progress_"+i).addClass('active');
		}else if(i==num){
			
			$("#progress_"+i).addClass('active action');
		}else{
			break;
		}
	}
}
</script>