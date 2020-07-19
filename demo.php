<?php
?>

删除按钮
Html::a('删除', 'javascript:;', ['class'=>'confirmdialog', 'title' => '删除', 'data-way' => 'ajax', 'data-word' => '您确定要删除吗？', 'data-url'=>Url::toRoute(['delete', 'id'=>$model->id, 'm'=>$this->context->m])]);

控件加后缀
Utility::addon('万元')

change显示隐藏
$form->field($models['customer'], 'marriage_status')->dropDownList($all_choose['marriage_status'], ['prompt'=>'请选择', 'class'=>'form-control leafblinds',
	'data-mutex'=>1, 'data-map'=>[2=>'#marry', 3=>'#divorce', 4=>'#bereavement']])
<div id="divorce" class="col-12 row">
	 $form->field($models['customer'], 'divorce_date')->datePicker()
</div>
