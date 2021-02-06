<?php 

use yii\helpers\Url;

$message = $this->context->message;
$flag = $this->context->operate;
if(!empty($message)){
	$message = str_replace(array("\r\n", "\r", "\n"), '', $message);
}
?>
<script type="text/javascript">
	var MESSAGE = "<?=$message?>";
	var MESSAGE_FLAG = "<?=$flag?>";
	var URL_UPLOAD = "<?=Url::toRoute(['/config/pictures/upload']) ?>";
	var FILE_UPLOAD_URL = "<?= Yii::$app->params['FILE_UPLOAD_URL'] ?>";
</script>