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
<?php if($multiline==1):?>
<?=$this->render('_gallery', ['directory' => $categorys])?>
<?php else:?>
<?php foreach ($categorys as $category_id=>$category):?>
<div id="files<?=$category_id?>" class="card card-default uploadFiles">
	<div class="card-header">
		<?php if($category['category_type']!=1 && $editable==1):?>
		<span><i class="fa fa-folder-open"></i>&nbsp;&nbsp;<a href="javascript:;" class="js-object-editable"><?=$category['category_name']?></a></span>
		<span class="input-group input-group-sm" style="width: 280px; display:none; float: left;">
			<input type="text" class="form-control" placeholder="修改名称">
			<span class="input-group-append">
				<button type="button" class="btn btn-primary btn-flat js-object-editable-ok" data-key="<?=$category['object_id']?>" data-type="<?=$category['category_type']?>">确定</button>
			</span>
			<span class="input-group-append">
				<button type="button" class="btn btn-secondary btn-flat js-object-editable-cancel">取消</button>
			</span>
		</span>
		<div class="card-tools">
			<button type="button" class="btn btn-tool js-object-delete" data-key="<?=$category['object_id']?>" data-type="<?=$category['category_type']?>"><i class="fa fa-times"></i></button>
		</div>
		<?php else:?>
		<span><i class="fa fa-folder-open"></i>&nbsp;&nbsp;<?=$category['category_name']?></span>
		<?php endif;?>
	</div>
	<div class="card-body" id="row_category_<?=$category_id?>">
		<?=$this->render('_gallery', [
			'directory' => $category['types'],
			'category_id' => $category_id,])?>
	</div>
</div>
<?php endforeach; ?>
<?php endif;?>
