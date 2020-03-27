<?php 
/* @var $directory Array[Object] */
/* @var $category_id integer */
/* @var $business_id integer */


?>
<div class="row gallery-box">
	<?php foreach ($directory as $folder):?>
	<div class="col-sm-3 col-md-6 col-lg-4 col-xl-2">
		<button type="button" class="btn btn-block <?=($folder['mininum']>0)?'btn-outline-danger':'btn-outline-primary'?> gallery-button" data-json='<?=json_encode($folder)?>'>
			<?=$folder['folder_name']?> <span class="badge bg-purple"><?=count($folder['files'])?count($folder['files']):''?></span>
		</button>
	</div>
	<?php endforeach;?>
	<div class="col-12 gallery-upload m-3 row">
	</div>
</div>