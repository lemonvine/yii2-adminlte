<?php
/* @var $categorys Array[Object] */
/* @var $business_id integer */
/* @var $editable integer */

$paths = $this->context->paths;
$layout = $this->context->layout;
$multiline = $this->context->multiline;
$keyid = $this->context->keyid;
$categorys = $this->context->category;

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

<script id="template_pictures_data" type="text/x-handlebars-template">
{{#each this}}
<i id="hid_file_{{file_id}}" data-title="{{title}}" data-file="{{file_path}}" data-thumb="{{thumb_file_path}}" data-id="{{file_id}}" data-serial="{{serial}}"></i>
{{/each}}
</script>
<script id="template_gallery" type="text/x-handlebars-template">
	{{#each this}}
		<div class="gallery-thumb col-sm-3 col-md-3 col-lg-2 col-xl-2">
			<div class="file-view">
				
				{{#if video}}
					<video  width="100%" controls>
						<source src="{{file}}"  type="video/mp4">
					</video>
				{{/if}}

				{{#if audio}}
					<audio controls="controls">
						<source src="{{file}}" type="audio/mpeg" />
					</audio>
					<span class="audio-file"></span>
				{{/if}}
				
				{{#if word}}
					<section>
						<a href='https://view.officeapps.live.com/op/view.aspx?src={{file}}' target="_blank">预览文档</a>
						<br>
						<a href="{{file}}" target="_blank">下载文档</a> 
					</section>
					<img src="/images/word.jpg">
				{{/if}}

				{{#if pdf}}
					<a href='{{file}}' target="_blank">下载</a>
				{{/if}}

				<img class="img-thumbnail" data-src="{{file}}" src="{{thumb}}" data-name="{{name}}" data-id="{{id}}" alt="">
			</div>
			<p class="file-text">{{name}}</p>
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
