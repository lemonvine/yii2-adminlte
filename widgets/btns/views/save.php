<?php

use yii\helpers\Html;

/* @var $btn_name String */
/* @var $form_id String */

?>
<?= Html::Button($btn_name, ['id' => 'btn_save', 'class' => 'btn btn-primary', 'onclick'=>'onSaveData()' ]) ?>
<script type="text/javascript">
function onSaveData(){
	var form_id = "#<?=$form_id?>";
	$("#submit_type").val('save');
	var data = $(form_id).data('yiiActiveForm');
	if(data){
		data.validated=true;
		$(form_id).data('yiiActiveForm',data);
	}
	try{
		var _tabs = $("#nav-tabs>li>a.active");
		if(_tabs.length>0){
			var _tabid = $(_tabs[0]).attr('aria-controls');
			var _scroll = $("#"+_tabid).scrollTop();
			bolevine.cookie(bolevine.cookie_name.save_reappear, ""+_tabid+","+_scroll, 2);
		}
		
	}
	catch(ex){
	}
	$(form_id).submit();
}
</script>