<?php
use yii\helpers\Json;

$form = $this->context->form;
$attribute = $this->context->attribute;
$model = $this->context->model;
$items = $this->context->items;
$handlebar=$this->context->handlebar;
$path_choice=$this->context->path_choice;
$path_upload=$this->context->path_upload;


$album_id = 'album_'.$attribute;

//$handlebar = ArrayHelper::remove($options, 'handlebar', 'handlebar_album');
$options =[
	'class'=>'lemon-album',
	'data-item'=>Json::encode($items),
	'data-dirt'=> $album_id,
	'data-hdbar'=> $handlebar,
];
?>

<div class="col-12">
	<div class="card">
		<div class="card-header">
			<?= $form->field($model, 'gallery')->hiddenInput($options) ?>
			<button type="button" class="btn btn-outline-info album-choice" data-dirt="<?=$album_id?>" data-url="<?=$path_choice?>">选择图片</button>
			<button type="button" class="btn btn-outline-warning album-upload" >上传图片</button>
			<input type="file" class="album-file" multiple data-dirt="<?=$album_id?>"
			data-hdbar="<?=$handlebar?>" data-url="<?=$path_upload?>">
		</div>
		<div class="card-body row" id="<?=$album_id?>">加载中</div> 
	</div>
</div>
