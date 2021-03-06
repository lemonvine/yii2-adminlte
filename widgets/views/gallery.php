<?php
/* @var $categorys Array[Object] */
/* @var $business_id integer */
/* @var $editable integer */

$paths = $this->context->paths;
$layout = $this->context->layout;
$multiline = $this->context->multiline;
$keyid = $this->context->keyid;
$categorys = $this->context->category;
$extensions = $this->context->extensions;
$accepts = $this->context->accepts;
?>

<div id="mask"></div>

<?php 
if($layout==3){
	echo $this->render('gallery2');
}?>

<script type="text/javascript">
	var PATH_UPLOAD = "<?=$paths['upload']?>";
	var PATH_REPLACE = "<?=$paths['replace']?>";
	var PATH_SERIAL = "<?=$paths['serial']?>";
	var PATH_MOVE = "<?=$paths['move']?>";
	var PATH_EDIT = "<?=$paths['edit']?>";
	var PATH_PAGEFILE = "<?=$paths['book']?>";
	var PATH_DELFILE = "<?=$paths['del']?>";
	var BUSSINESSID = "<?=$keyid?>";
	var EXTENSION = eval('(<?=json_encode($extensions)?>)');
	var ACCEPT = eval('(<?=json_encode($accepts)?>)');
</script>

<script id="template_upload" type="text/html">
<form id="uploadForm" enctype="multipart/form-data" style="display: none;">
	<input id="btn_file_upload" type="file" name="UploadFiles[]" accept="" multiple />
	<input id="txt_folder" name="folder" value="0" />
	<input id="txt_key" name="key" value="<?=$keyid?>" />
	<input id="txt_id" name="id" value="0" />
	<button id="upload" type="button">upload</button>
	<input type="hidden" id="upload_callback" value="">
</form>
</script>

<script id="template_gallery" type="text/x-handlebars-template">
<div class="gallery-handle col-12">
	<ul class="nav">
		<li class="nav-item"><span class="check-all icheck-primary "><input type="checkbox" id="checkboxall"><label for="checkboxall">&nbsp;全选</label></span></li>
		<li class="nav-item "><span class="move-all" id="button_moveto"><i class="fa fa-mail-forward fa-lg text-success"></i>&nbsp;移动到</span></li>
	</ul>
</div>
<div id="gallery-content" class=" col-12 row">
	{{#each this}}
		<div class="gallery-thumb col-sm-3 col-md-3 col-lg-3 col-xl-2" data-soul="{{json2str this}}">
			<div class="file-view">
				{{#ifeven original 1}}
						<img class="img-thumbnail" data-src="{{file}}" src="{{thumb}}" alt="">
				{{/ifeven}}
				{{#ifeven original 2}}
					<audio controls="controls">
						<source src="{{original}}" type="audio/mpeg" />
					</audio>
				{{/ifeven}}
				{{#ifeven original 3}}
					<video width="320" height="240" controls>
						<source src="{{original}}" type="video/mp4">
					</video>
				{{/ifeven}}
				{{#ifeven original 4}}
					<div class="file-pdf img-thumbnail" data-src="{{original}}"></div>
				{{/ifeven}}
				{{#ifeven original 5}}
					<div class="file-word img-thumbnail" data-src="{{original}}"></div>
				{{/ifeven}}
				{{#ifeven original 6}}
					<div class="file-excel img-thumbnail" data-src="{{original}}"></div>
				{{/ifeven}}
					
				<div class="hover-mask">
					<a href="javascript:;" class="file-pencil" title="修改名称"><i class="fa fa-pencil"></i></a>
					{{#ifeven original 5}}
						<a href="{{original}}" target="_blank" class="file-view"><i class="fa fa-download"></i></a>
					{{else ifeven original 6}}
						<a href="{{original}}" target="_blank" class="file-view"><i class="fa fa-download"></i></a>
					{{else}}
						<a href="{{original}}" target="_blank" class="file-view"><i class="fa fa-eye"></i></a>
					{{/ifeven}}
					<a href="javascript:;" class="file-sort" title="排序"><i class="fa fa-arrows"></i></a>
					<a href="javascript:;" class="file-replace" title="替换"><i class="fa fa-refresh"></i></a>
					<a href="javascript:;" class="file-delete" title="删除"><i class="fa fa-trash"></i></a>
				</div>
				<div class="file-filter icheck-primary"><input type="checkbox" id="checkbox{{file_id}}"><label for="checkbox{{file_id}}"></label></div>
			</div>
			<p class="file-text">{{title}}</p>
		</div>
	{{/each}}
	<div id="gallery-add" class="gallery-add only-edit col-sm-3 col-md-3 col-lg-2 col-xl-2">
		<div class="img-thumbnail">
			<i class="fa fa-upload text-success"></i>
		</div>
	</div>
</div>

</script>

<?php 
$Js = <<<JS
$(function(){
	initGallery();
});
JS;
$this->registerJs($Js);
		
?>
