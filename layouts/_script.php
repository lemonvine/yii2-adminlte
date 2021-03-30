<?php
//全局javascript变量
$context = $this->context;
$message = $context->message;
$flag = $context->operate;
$upload = $context->upload_url??'';

if(!empty($message)){
	$message = str_replace(array("\r\n", "\r", "\n"), '', $message);
}
if(!empty($upload)){
	$upload  .= '?env='.\Yii::$app->params['environment'];
}
?>
<script type="text/javascript">
var MESSAGE = "<?=$message?>";
var MESSAGE_FLAG = "<?=$flag?>";
var FILEUPLOADURL = "<?=$upload ?>";
</script>