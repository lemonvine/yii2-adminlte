<?php

namespace lemon\models\traits;

use Yii;
use yii\web\UploadedFile;
use lemon\repository\Utility;

/**
 * 
 */
trait ModelTrait
{
	public $save_path = '/uploads/picture/';
	
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
	
	/**
	 *
	 * @param unknown $model
	 * @param unknown $field
	 * @throws \Exception
	 * @return string
	 */
	public function saveImage($model, $field){
		$upload_file = UploadedFile::getInstance($model, $field);
		if ($upload_file) {
			$sys_path = Yii::$app->params['FILE_PHYSICAL_PATH'];
			$doc = Utility::createRandom(20).'.'.$upload_file->extension;
			$save_path = $this->save_path.date('Y/m');
			if (!file_exists($sys_path.$save_path)){
				mkdir($sys_path.$save_path, 0777, true);
			}
			$save_result = $upload_file ->saveAs($sys_path.$save_path.$doc);
			if(!$save_result){
				throw new \Exception("文件保存失败");
			}
			
			return $this->save_path.$doc;
		}
		return '';
	}
}