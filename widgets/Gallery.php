<?php
namespace lemon\widgets;

use Yii;
use yii\base\Widget;
use lemon\web\AdminLteAsset;
use lemon\widgets\services\GalleryService;

class Gallery extends Widget
{
	public $layout=3; //1只显示初始化部分，2只显示列表（用于ajax刷新），3一次性全部显示
	public $multiline=3;//1单类显示（无边框直接显示），2单类显示（面板显示），3分类显示（多分类）
	public $scene = 0;
	public $keyid = 0;
	public $editable = 0;
	public $category = [];
	public $service_calss ='lemon\widgets\services\GalleryService';
	public $service_init = [];
	public $paths = [];
	public $extensions = [];
	public $accepts = [];
	public $trees = [];
	public $extra = [];
	
	public function init()
	{
		
	}
	
	public function run()
	{
		$this->buildFileTree();
		if(in_array($this->layout, [1,3])){
			$this->registerClientScript();
			return $this->render('gallery');
		}
		else{
			
			return $this->render('gallery2');
		}
	}
	
	protected function registerClientScript()
	{
		AdminLteAsset::loadModule($this->view, ['handlebar', 'viewer', 'gallery', 'icheck', 'ztree', 'sortable']);
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