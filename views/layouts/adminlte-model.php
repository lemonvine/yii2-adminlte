<?php
use yii\helpers\Html;
use lemon\widgets\Message;
use lemon\web\AdminLteAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AdminLteAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language ?>">
	<head>
		<meta charset="<?= Yii::$app->charset ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?= Html::csrfMetaTags() ?>
		<title><?= Html::encode($this->title) ?></title>
		<?php $this->head() ?>
		<script src="/js/depends.js?v=1.0"></script>
	</head>
	<body class="hold-transition layout-fixed text-sm">
		<div class="wrapper">
			<?php $this->beginBody() ?>
			<section  class="content m-2">
				<?= $content ?>
			</section >
			<?php $this->endBody() ?>
		</div>
		<?=Message::widget(['message'=>$this->context->message, 'operate'=>$this->context->operate])?>
	</body>
</html>
<?php $this->endPage() ?>
