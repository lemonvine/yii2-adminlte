<?php
//use Yii;
use lemon\repository\Utility;
use lemon\repository\Undefinitive;

if(empty($this->context->u->avatar)){
	$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/lemonvine/yii2-adminlte/dist');
	$default_avator = "$directoryAsset/img/user2-160x160.jpg";
	$avator = $default_avator;
}
else{
	$avator = Utility::dehttpPath($this->context->u->avatar);
}
$user_name = $this->context->u->realname??'';

?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
	<a href="javascript:;" class="brand-link navbar-info">
		<img src="<?=Yii::$app->params['admin_logo'] ?>" alt="" class="brand-image img-circle elevation-3" style="opacity: .8" />
		<span class="brand-text font-weight-light"><?=\Yii::$app->params['admin_name']?></span>
	</a>
	<div class="sidebar">
		<div class="user-panel mt-3 pb-3 mb-3 d-flex">
			<div class="image">
				<img src="<?=$avator?>" class="img-circle elevation-2" alt="头像" />
			</div>
			<div class="info">
				<a href="javascript:;" class="d-block"><?=$user_name?></a>
			</div>
		</div>
		<nav class="mt-2">
			<?=Undefinitive::adminMenus()?>
		</nav>
	</div>
</aside>