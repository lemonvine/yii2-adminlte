<?php

namespace lemon\models;

use Yii;

/**
 * This is the model class for table "{{%log_export}}".
 *
 * @property int $id 编号
 * @property int $export_type 操作类型
 * @property string $export_file 导出文件
 * @property string $export_remark 导出说明
 * @property int $export_user 操作人员
 * @property int $export_time 时间
 * @property string $operate_ip 操作IP
 */
class LogExport extends \yii\db\ActiveRecord
{
	public static function tableName()
	{
		return '{{%log_export}}';
	}

	public function rules()
	{
		return [
			[['export_type', 'export_user', 'export_time'], 'integer'],
			[['export_file'], 'string', 'max' => 64],
			[['export_remark'], 'string', 'max' => 2048],
			[['operate_ip'], 'string', 'max' => 32],
		];
	}

	public function attributeLabels()
	{
		return [
			'id' => '编号',
			'export_type' => '操作类型',
			'export_file' => '导出文件',
			'export_remark' => '导出说明',
			'export_user' => '操作人员',
			'export_time' => '时间',
			'operate_ip' => '操作IP',
		];
	}
}
