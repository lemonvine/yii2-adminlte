<?php
namespace lemon\components;

use yii\web\View;

/**
 * 继承yii2框架的RBAC权限, 解决表名是写死的的问题
 * @author shaodd
 *
 */
class WebView extends View
{
	protected $WHOLE_ROW=['options'=>['class'=>'form-group col-12 flex']];
}

