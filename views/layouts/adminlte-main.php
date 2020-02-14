<?php
use yii\helpers\Html;
use lemon\widgets\Message;

/* @var $this \yii\web\View */
/* @var $content string */

$this->registerAssetBundle(Yii::$app->params['classname_asset']);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
	<head>
		<meta charset="<?= Yii::$app->charset ?>"/>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<?= Html::csrfMetaTags() ?>
		<title><?= Html::encode($this->title) ?></title>
		<link rel="icon" type="image/x-icon" class="js-site-favicon" href="/favicon.ico">
		<?php $this->head() ?>
	</head>
	<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed text-sm">
		<input type="hidden" id="chain_menu" value="<?=$this->context->m ?>" />
		<div class="wrapper">
		<?php $this->beginBody() ?>
			<?=$this->render('adminlte-header.php')?>
			<?=$this->render('adminlte-left.php')?>
			<?=$this->render('adminlte-content.php', ['content' => $content])?>
		<?php $this->endBody() ?>
		</div>
		<?=Message::widget(['message'=>$this->context->message, 'operate'=>$this->context->operate])?>
	</body>
</html>
<?php $this->endPage() ?>