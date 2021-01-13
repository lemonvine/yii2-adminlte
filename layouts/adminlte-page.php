<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;

$this->registerAssetBundle("lemon\assets\PageAsset");

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="apple-touch-icon-precomposed" sizes="57x57" href="/images/ipad-logo.png?v=2.4.1">  
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="/images/ipad-logo.png?v=2.4.1">  
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="/images/ipad-logo.png?v=2.4.1">  
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="/images/ipad-logo.png?v=2.4.1">
	<?= Html::csrfMetaTags() ?>
	<title><?= Html::encode($this->title) ?></title>
	<?php $this->head() ?>
</head>
<body style="height: 100%">
	<?php $this->beginBody() ?>
		<?= $content ?>
	<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>