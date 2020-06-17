<?php
$element_id=$this->context->elid;
$path_choice=$this->context->path_choice;
$path_upload=$this->context->path_upload;
?>
<div class="col-12">
	<div class="card">
		<div class="card-header">
			<button type="button" class="btn btn-outline-info album-choice" data-dirt="<?=$element_id?>" data-url="<?=$path_choice?>">选择图片</button>
			<button type="button" class="btn btn-outline-warning album-upload" >上传图片</button>
			<input type="file" class="album-file d-none" data-dirt="<?=$element_id?>" data-url="<?=$path_upload?>">
		</div>
		<div class="card-body row" id="<?=$element_id?>">加载中</div> 
	</div>
</div>
