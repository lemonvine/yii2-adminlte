<?php
namespace lemon\components;

/**
 * 继承yii2框架的RBAC权限, 解决表名是写死的的问题
 * @author shaodd
 *
 */
class DbManager extends \yii\rbac\DbManager
{
	public $itemTable = 'dyzc_auth_item';
	/**
	 * @var string the name of the table storing authorization item hierarchy. Defaults to "auth_item_child".
	 */
	public $itemChildTable = 'dyzc_auth_item_child';
	/**
	 * @var string the name of the table storing authorization item assignments. Defaults to "auth_assignment".
	 */
	public $assignmentTable = 'dyzc_auth_assignment';
	/**
	 * @var string the name of the table storing rules. Defaults to "auth_rule".
	 */
	public $ruleTable = 'dyzc_auth_rule';
}

