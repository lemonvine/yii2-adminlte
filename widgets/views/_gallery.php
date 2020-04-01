<?php 
/* @var $directory Array[Object] */


?>
<div class="row gallery-box">
	<?php foreach ($directory as $folder):?>
	<div class="col-sm-3 col-md-6 col-lg-4 col-xl-2">
		<button type="button" class="btn btn-block <?=($folder['attr']['mininum']>0)?'btn-outline-danger':'btn-outline-primary'?> gallery-button"
		data-attr='<?=json_encode($folder['attr'])?>' data-files='<?=json_encode($folder['files'])?>'>
			<?=$folder['attr']['folder_name']?> <span class="badge bg-purple"><?=count($folder['files'])?count($folder['files']):''?></span>
		</button>
	</div>
	<?php endforeach;?>
	<div class="gallery-upload col-12 m-3 row">
	</div>
</div>