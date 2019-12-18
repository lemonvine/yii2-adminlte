<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */
?>

<nav class="main-header navbar navbar-expand navbar-dark navbar-info">
	<ul class="navbar-nav">
		<li class="nav-item">
			<a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
		</li>
	</ul>
	<ul class="navbar-nav ml-auto">
		<li class="nav-item messages-menu">
			<a href="javascript:;" target="_blank">
				<i class="fa fa-envelope-o"></i>
				<span class="label label-success"></span>
			</a>
		</li>
		<li class="nav-item">
			<a href="javascript:;" onclick="resetPassword()" class="nav-link" >
				<i class="fa fa-key"></i> 修改密码
			</a>
		</li>
		<li class="nav-item">
			<?= Html::a('<i class="fa fa-sign-out"></i> 退出',['/site/logout'],['data-method' => 'post', 'class' => 'nav-link']) ?>
		</li>
	</ul>
</nav>
<script type="text/javascript">
var layerResetPassword;
function resetPassword(){
	layerResetPassword = layer.open({
		type: 2,
		title: "修改密码",
		area: ['640px', '430px'],
		resize: false,
		move: false,
		content: "<?= Url::toRoute(['/auth/user/updatepwd'])?>"
	});
}
function closeResetPassword(){
	layer.close(layerResetPassword);
	layer.alert('密码修改成功，请重新登陆',{area: ['420px', '260px']},function(){
		$.post("<?= Url::toRoute(['/site/logout'])?>");
	});
}
</script>

