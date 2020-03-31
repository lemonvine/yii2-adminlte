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
	var PATH_FILETYPE = "<?=$paths['move']?>";
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
</form>
</script>

<script id="template_move_file" type="text/html">
<div class="list-group" id="box_file_moveto">
<?php foreach ($categorys as $category_id=>$category):?>
<p href="javascript:;" class="list-group-item list-group-item-info"><?=$category['category_name']?></p>
<div class="container">
<?php foreach ($category['types'] as $filetype):?>
<a href="javascript:;" class="btn btn-outline-success moveto_btn" data-object="<?=$filetype['object_id']?>" data-type="<?=$filetype['filetype_id']?>"><?=$filetype['filetype_name']?></a>
<?php endforeach; ?>
</div>
<?php endforeach; ?>
</div>
</script>

<script id="template_gallery" type="text/x-handlebars-template">
{{#each this}}
<div class="gallery-thumb col-sm-3 col-md-3 col-lg-2 col-xl-2">
	<div class="file-view">
{{#if_even original 1}}
		<img class="img-thumbnail" data-src="{{file}}" src="{{thumb}}" alt="">
{{else}}
	{{#if_even original 2}}
		<audio controls="controls">
			<source src="{{original}}" type="audio/mpeg" />
		</audio>
	{{else}}
		{{#if_even original 3}}
		<video width="320" height="240" controls>
			<source src="{{original}}" type="video/mp4">
		</video>
		{{else}}
			{{#if_even original 4}}
		<div class="file-pdf img-thumbnail" data-src="{{original}}"></div>
			{{else}}
				{{#if_even original 5}}
		<div class="file-word img-thumbnail" data-src="{{original}}"></div>
				{{else}}
					{{#if_even original 6}}
		<div class="file-excel img-thumbnail" data-src="{{original}}"></div>
					{{else}}
		<div class="file-file img-thumbnail" data-src="{{original}}"></div>
					{{/if_even}}
				{{/if_even}}
			{{/if_even}}
		{{/if_even}}
	{{/if_even}}
{{/if_even}}
		<div class="hover-mask">
{{#if_even original 4}}
			<a href="javascript:;" data-id="{{id}}" class="file-view"><i class="fa fa-eye"></i></a>
{{else}}
	{{#if_even original 5}}
			<a href="javascript:;" data-id="{{id}}" class="file-view"><i class="fa fa-eye"></i></a>
	{{else}}
		{{#if_even original 6}}
			<a href="javascript:;" data-id="{{id}}" class="file-view"><i class="fa fa-eye"></i></a>
		{{/if_even}}
	{{/if_even}}
{{/if_even}}
			<a href="javascript:;" data-id="{{id}}" class="file-replace"><i class="fa fa-refresh"></i></a>
			<a href="javascript:;" data-id="{{id}}" class="file-delete"><i class="fa fa-trash"></i></a>
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
</script>

<?php 
$Js = <<<JS
$(function(){
	initGallery();
});
JS;
$this->registerJs($Js);
		
?>
