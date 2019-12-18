<?php
use yii\helpers\Html;
use backend\assets\ModelAsset;
use lemon\web\AdminLteAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AdminLteAsset::register($this);
ModelAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?= Html::csrfMetaTags() ?>
	<title><?= Html::encode($this->title) ?></title>
	<?php $this->head() ?>
</head>
<body class="hold-transition layout-fixed text-sm">
	<div class="wrapper">
		<?php $this->beginBody() ?>
		<section  class="content">
			<?= $content ?>
		</section >
	<?php $this->endBody() ?>
	</div>
<script type="text/javascript">
	function pageInitialize(){
		<?php 
		$message0 = $this->context->message;
		if($message0){
			$message0 = addslashes($message0);
			echo "layer.msg('{$message0}', {time: 2000})";
		}
		?>
	}
</script>
<?php
$Js = <<<JS
	pageInitialize();
JS;
$this->registerJs($Js);
?>
	</body>
</html>
<?php $this->endPage() ?>