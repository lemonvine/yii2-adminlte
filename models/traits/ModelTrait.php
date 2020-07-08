<?php

namespace lemon\models\traits;


/**
 * 
 */
trait ModelTrait
{
	
	/**
	 * 保存model通用方法
	 * @param yii\db\ActiveRecord $model
	 * @param string $msg
	 * @throws \Exception
	 */
	public function savedb(&$model, $msg=''){
		if(!$model->save()){
			$error = array_values($model->getFirstErrors())[0].(!empty($msg)?"[{$msg}]":'');
			throw new \Exception($error);
		}
	}
}