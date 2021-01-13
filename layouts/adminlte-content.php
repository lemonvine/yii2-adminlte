<?php
use lemon\widgets\Alert;
use lemon\bootstrap4\Breadcrumbs;

/* @var $content string */

?>
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0 text-dark"><?=($this->title !== null)?$this->title:'您好' ?></h1>
				</div>
				<div class="col-sm-6">
					<?=Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [], 'navOptions'=>['aria-label' => 'breadcrumb', 'class'=>'float-sm-right']]) ?>
				</div>
			</div>
		</div>
	</section>
	
	<section class="content">
		<div class="container-fluid">
		<?= Alert::widget() ?>
		<?= $content ?>
		</div>
	</section>
</div>
<footer class="main-footer">
	<div class="float-right d-none d-sm-block">
	  <b>Version：</b> <?=\Yii::$app->params['admin_version']?>
	</div>
	<strong>Copyright &copy; <?=date('Y')?> <a href="javascript:void(0)"><?=\Yii::$app->params['copyright']?></a> </strong> 版权所有
</footer>
