<?php

/* @var $directoryAsset string */

$default_avator = "$directoryAsset/img/user2-160x160.jpg";
if(empty($this->context->u->avatar)){
	$avator = $default_avator;
}
else{
	$avator = Yii::$app->params['PICTURE_HTTP'].$this->context->u->avatar;
}
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
	<a href="javascript:;" class="brand-link navbar-info">
		<img src="/favicon.ico" alt="" class="brand-image img-circle elevation-3" style="opacity: .8" />
		<span class="brand-text font-weight-light"><?=\Yii::$app->name ?>管理平台</span>
	</a>
	<div class="sidebar">
		<div class="user-panel mt-3 pb-3 mb-3 d-flex">
			<div class="image">
				<img src="<?= $avator ?>" class="img-circle elevation-2" alt="头像" />
			</div>
			<div class="info">
				<a href="javascript:;" class="d-block"><?=$this->context->u->real_name?></a>
			</div>
		</div>
		<nav class="mt-2">
			<?php
				$menuItems = Yii::$app->user->identity::getAdminMenus();
				echo lemon\widgets\Menu::widget(
					[
						'options' => ['class' => 'nav nav-pills nav-sidebar flex-column nav-child-indent', 'data-widget'=> 'treeview',  'role'=>"menu", 'data-accordion'=>"false"],
						'items' => $menuItems,
					]
				) 
			?>
		</nav>
	</div>
	
</aside>
