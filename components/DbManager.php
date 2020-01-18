<?php
namespace lemon\components;

/**
 * 继承yii2框架的RBAC权限, 解决表名是写死的的问题
 * @author shaodd
 *
 */
class DbManager extends \yii\rbac\DbManager
{
	public $ruleTable = '{{%auth_rule}}';
	public $roleTable = '{{%auth_role}}';
	public $itemTable = '{{%auth_item}}';
	public $assignmentTable = '{{%auth_assignment}}';
	public $itemChildTable = '{{%auth_item_child}}';
	public $userRoleTable = '{{%auth_user_role}}';
	public $logTable = '{{%log_auth}}';
}

