<?php
?>

删除按钮
Html::a('删除', 'javascript:;', ['class'=>'confirmdialog', 'title' => '删除', 'data-way' => 'ajax', 'data-word' => '您确定要删除吗？', 'data-url'=>Url::toRoute(['delete', 'id'=>$model->id, 'm'=>$this->context->m])]);

控件加后缀
Utility::addon('万元')