<?php
use yii\helpers\Html;
use yii\helpers\Url;
use lemon\repository\Undefinitive;

/* @var $this \yii\web\View */
/* @var $content string */
?>

<nav class="main-header navbar navbar-expand navbar-dark navbar-info">
	<ul class="navbar-nav">
		<li class="nav-item">
			<a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
		</li>
	</ul>
	<ul class="navbar-nav ml-auto">
		<?=Undefinitive::mission('headerMenu', ['view'=>$this])?>
		<li class="nav-item">
			<a href="javascript:;" class="nav-link modaldialog" data-title="修改密码" data-area="sm" data-url="<?= Url::toRoute(['/admin/mpwd'])?>" >
				<i class="fa fa-key"></i> 修改密码
			</a>
		</li>
		<li class="nav-item">
			<?= Html::a('<i class="fa fa-sign-out"></i> 退出',['/admin/logout'],['data-method' => 'post', 'class' => 'nav-link']) ?>
		</li>
	</ul>
</nav>
