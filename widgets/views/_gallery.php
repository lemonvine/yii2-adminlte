<?php 
/* @var $directory Array[Object] */
/* @var $index integer */


?>
<div class="row gallery-box">
	<?php foreach ($directory as $folder):?>
	<div class="col-sm-3 col-md-6 col-lg-4 col-xl-2">
		<button type="button" class="btn btn-block <?=($folder['attr']['mininum']>0)?'btn-outline-danger':'btn-outline-primary'?> gallery-button"
		data-index='<?=$index?>' data-attr='<?=json_encode($folder['attr'])?>' data-files='<?=json_encode($folder['files'])?>'>
			<?=$folder['attr']['folder_name']?> <span class="badge bg-purple"><?=count($folder['files'])?count($folder['files']):''?></span>
		</button>
	</div>
	<?php endforeach;?>
	<div id="gallery-upload<?=$index?>" class="gallery-upload col-12 m-3 row">
	</div>
</div>