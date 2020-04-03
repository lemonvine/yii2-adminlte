<?php

/* @var $categorys Array[Object] */
/* @var $business_id integer */
/* @var $editable integer */

$paths = $this->context->paths;
$layout = $this->context->layout;
$multiline = $this->context->multiline;
$keyid = $this->context->keyid;
$categorys = $this->context->category;
$trees = $this->context->trees;

?>
<?php
if($multiline==1):
	echo $this->render('_gallery', ['directory' => $categorys, 'index'=>1]);
else:
	foreach ($categorys as $category_id=>$category):
?>
<div id="files<?=$category_id?>" class="card card-default uploadFiles">
	<div class="card-header">
		<span><i class="fa fa-book"></i>&nbsp;&nbsp;<?=$category['category_name']?></span>
	</div>
	<div class="card-body">
		<?=$this->render('_gallery', [
			'directory' => $category['types'],
			'index' => $category_id,])?>
	</div>
</div>
<?php endforeach; ?>
<?php endif;?>

<script type="text/javascript">
	var MOVETO_TREE = '<?=json_encode($trees)?>';
</script>
