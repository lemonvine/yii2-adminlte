<?php
use yii\helpers\Html;

/* @var $form_id String */

?>

<?=Html::button('导出', ['class' => 'btn btn-secondary', 'onclick'=>'exportData(this)' ])?>
<script type="text/javascript">
function exportData(obj){
	$('#submit_type').val('export');
	//$('#export_form').submit();
	<?php if(empty($form_id)):?>
	$(obj).parent('form').submit();
	<?php else:?>
	$('#<?=$form_id?>').submit();
	<?php endif;?>
}
</script>