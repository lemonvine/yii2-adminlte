<?php 
/* @var $directory Array[Object] */
/* @var $category_id integer */
/* @var $business_id integer */


?>
<div class="row gallery-box">
	<?php foreach ($directory as $document):?>
	<div class="col-sm-3 col-md-6 col-lg-4 col-xl-2">
		<button type="button" class="btn btn-block btn-outline-primary gallery-button" data-json='<?=json_encode($document)?>'>
			<?=$document['filetype_name']?> <span class="badge bg-purple"><?=count($document['files'])?count($document['files']):''?></span>
		</button>
	</div>
	<?php endforeach;?>
	<div class="col-12 gallery-upload m-3 row">
	</div>
	<div id="card_category<?=$category_id?>_upload" class="js-upload-category" style="display: none;">
		<p class="handle">
			<span class="check"><i class="fa fa-square-o icon-checkbox1"></i><i class="fa fa-check-square-o icon-checkbox2"></i>&nbsp;全选</span>
			<span class="type"><i class="fa fa-th-list icon-leixing"></i>&nbsp;移动到</span>
			<!-- <span class="pdf"><i class="fa fa-file-powerpoint-o"></i>&nbsp;生成PDF</span> -->
		</p>
		<div class="list">
		</div>
		<div class="hide_imgs" style="display: none;">
		</div>
	</div>
</div>