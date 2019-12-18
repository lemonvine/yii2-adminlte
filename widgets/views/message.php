<?php 

$message = $this->context->message;
$flag = $this->context->operate;
if(!empty($message)){
	$message = str_replace(array("\r\n", "\r", "\n"), '', $message);
}
?>
<script type="text/javascript">
	var MESSAGE = "<?=$message?>";
	var MESSAGE_FLAG = "<?=$flag?>";
</script>