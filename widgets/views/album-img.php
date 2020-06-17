<?php 
$bath_path = Yii::$app->params['FILE_HTTP_PATH'];

?>
<div class="col-12"></div>
{{#each this}}  
<div class="filtr-item col-sm-6 col-md-46 col-lg-38 col-xl-3">
	<div class="file-cont">
		<div class="file-view">
			<img class="lazy viewer-toggle" src="<?=$bath_path ?>/{{thumb}}" data-src="{{file}}" data-original="{{thumb}}" data-id="{{id}}" data-name="{{name}}">
		</div>
		<p class="file-text">
			<a class="image-del js-image-del" href="javascript:;" data-status="new" data-file="{{file}}">
				<i class="fa fa-trash-o"></i>
			</a>
		</p>
	</div>
</div>
{{/each}}

