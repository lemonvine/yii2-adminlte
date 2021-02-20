<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

$this->registerAssetBundle(Yii::$app->params['classname_asset']);

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
		<?=$this->render('_script.php')?>
	</head>
	<body class="hold-transition layout-fixed text-sm">
		<div class="wrapper">
			<?php $this->beginBody() ?>
			<section  class="content m-2">
				<?=$content?>
			</section >
			<?php $this->endBody() ?>
		</div>
	</body>
</html>
<?php $this->endPage() ?>
