<?php
namespace lemon\components;

/**
 * 继承yii2框架的RBAC权限, 解决表名是写死的的问题
 * @author shaodd
 *
 */
class DbManager2 extends \yii\rbac\DbManager
{
	public $ruleTable = '{{%auth_rule}}';
	public $roleTable = '{{%auth_role}}';
	public $itemTable = '{{%auth_item}}';
	public $assignmentTable = '{{%auth_assignment}}';
	public $itemChildTable = '{{%auth_item_child}}';
	public $userRoleTable = '{{%auth_user_role}}';
	public $userTagTable = '{{%auth_user_tag}}';
	public $logTable = '{{%log_auth}}';
	
	
	public $userTable = '{{%auth_user}}';
	public $tagTable = '{{%auth_user}}';
}

