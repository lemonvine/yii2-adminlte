<?php
namespace lemon\widgets;

use Yii;
use yii\helpers\Url;
use yii\base\Widget;
use lemon\web\AdminLteAsset;
//use yii\base\View;
use lemon\components\WebView;

class Album extends Widget
{
	/**
	 * @var integer 
	 */
	public $form;
	public $model;
	public $attribute;
	public $items=[];
	public $obid=0;
	public $handlebar;
	public $path_choice;
	public $path_upload;
	
	public function init()
	{
		if(is_null($this->handlebar)){
			$this->handlebar = 'handlebar_album';
		}
		if(is_null($this->path_choice)){
			$this->path_choice = Url::toRoute(['choice', 'id'=>$this->obid]);
		}
		if(is_null($this->path_upload)){
			$this->path_upload = Url::toRoute(['upload', 'id'=>$this->obid]);
		}
		
	}
	
	public function run()
	{
		$this->registerClientScript();
		return $this->render('album');
	}
	
	protected function registerClientScript()
	{
		$script = $this->render('album-img');
		$this->view->registerJs($script, WebView::POS_HDB, $this->handlebar);
		AdminLteAsset::loadModule($this->view, ['album', 'handlebar']);
		//echo Html::script($script, ['id'=>'abcde1']);
		//AdminLteAsset::loadModule($this->view, ['handlebar', 'viewer', 'gallery', 'icheck', 'ztree', 'sortable']);
	}
	
	/**
	 * 构建影印附件的三级目录
	 * {
	 * 	file_category_id: [{
	 * 		"file_type_id": "11",
	 * 		"file_type_name": "周边道路",
	 * 		"filetype_name": "周边道路",
	 * 		"file_category": "11",
	 * 		"file_accept": "1",
	 * 		"required": "required",
	 * 		"files": [{
	 * 			"thumb_url": "http:\/\/localhost\/pic\/bizs\/2018\/09\/10325\/\/thumb-11_15373476761_1.png",
	 * 			"url": "http:\/\/localhost\/pic\/bizs\/2018\/09\/10325\/11_15373476761_1.png",
	 * 			"id": "842437",
	 * 			"serial": "1"
	 * 		}]
	 * 	}]
	 * }
	 * shaodd, 2018-09-20
	 * @param int $business_id
	 * @param int $step
	 * @return array[]
	 */
	protected function buildFileTree()
	{
		$params = [
			'keyid' =>$this->keyid,
		];
		if(count($this->service_init)>0){
			$params = array_merge($params, $this->service_init);
		}
		
		$service = Yii::createObject(
			['class'=>$this->service_calss],
			[$params]
		);
		if(in_array($this->layout, [1,3])){
			$this->paths = $service->paths();
			$this->extensions = $service->extentions();
			$this->accepts= $service->accepts();
		}
		
		if(in_array($this->layout, [2,3])){
			$this->category = $service->widget($this->scene, $this->multiline, $this->extra);
			$this->trees = $service->trees;
		}
		
	}
	
	
	
}