<?php
namespace lemon\widgets\services;

use Yii;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\imagine\Image;
use lemon\repository\Utility;
use backend\modules\config\models\FiletypeScene;
use backend\modules\config\models\Filetype;
use backend\modules\config\models\FiletypeCategory;
use backend\modules\loan\models\BusinessFiles;
use lemon\repository\Logger;
use yii\base\Component;

class GalleryService extends Component
{
	protected $limit=20;
	protected $thumb_width = 200;
	protected $thumb_height = 150;
	protected $path_http;
	//protected $folder;  //文件目录名(filetype)
	public $keyid;
	
	protected $max=0;
	
	protected $format=[
		1=>[
			'caption'=>'image>',
			'figure'=>1,
			'thumb'=>'',
			'mime'=>'image/gif, image/jpeg, image/tiff, image/tiff-fx, image/png, image/vnd.microsoft.icon, image/bmp, image/tga, application/pdf'
		],
		2=>[
			'caption'=>'audio>',
			'figure'=>2,
			'thumb'=>'/images/file_audio.png',
			'mime'=>'audio/mpeg, audio/m4a'
		],
		3=>[
			'caption'=>'video>',
			'figure'=>2,
			'thumb'=>'/images/file_video.png',
			'mime'=>'video/mp4'
		],
		4=>[
			'caption'=>'pdf>',
			'figure'=>2,
			'thumb'=>'/images/file_pdf.png',
			'mime'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword, application/pdf'
		],
		5=>[
			'caption'=>'word>',
			'figure'=>2,
			'thumb'=>'/images/file_word.png',
			'mime'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword, application/pdf'
		],
		6=>[
			'caption'=>'excel>',
			'figure'=>2,
			'thumb'=>'/images/file_excel.png',
			'mime'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword, application/pdf'
		],
		9=>[
			'caption'=>'file>',
			'figure'=>2,
			'thumb'=>'/images/file.png',
			'mime'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword, application/pdf'
		]
		
	];
	protected $extension = [
		'jpg'=>1, 'gif'=>1, 'png'=>1, 'jpeg'=>1, 'ico'=>1, 'bmp'=>1,
		'mp3'=>2, 'amr'=>2, 'm4v'=>2, 'wav'=>2,
		'mp4'=>3, 'mov'=>3,
		'pdf'=>4,
		'docx'=>5, 'doc'=>5,
		'xls'=>6, 'xlsx'=>6,
		
	];
	
	public function __construct($config = []) {
		Yii::configure($this, $config);
		$this->init();
	}
	
	public function init()
	{
		$this->path_http= Yii::$app->params['FILE_HTTP_PATH'];
	}
	/*
	public function __construct($id, $type) {
		
		$this->object = $id;
		$this->folder = $type;
		
	}*/
	public function paths(){
		$paths = [
			'upload'=>Url::toRoute('/loan/files/upload'),
			'serial'=>Url::toRoute('/loan/files/serial'),
			'del'=>Url::toRoute('/loan/files/delete'),
			'replace'=>Url::toRoute('/loan/files/replace'),
			'move'=>Url::toRoute('/loan/files/move'),
			'book'=>Url::toRoute('/loan/files/book'),
		];
		
		return $paths;
	}
	
	public function save($save_dir='upload', $folder=8888){
		$physical_path= Yii::$app->params['FILE_PHYSICAL_PATH'];
		$save_path = "/".$save_dir. date("/Y/m/") . $this->object . '/';
		
		
		if (!file_exists($physical_path. $save_path)) {
			mkdir($physical_path. $save_path, 0777, true);
		}
		
		$files = UploadedFile::getInstancesByName('dyzcUploadFiles');
		$files_count = count($files);
		if($files_count==0){
			throw new \Exception('文件数量不能为空');
		}
		if($this->limit>0 && $files_count>0 && $files_count>$this->limit){
			throw new \Exception('文件数量超过限制：'.$this->limit);
		}
		$success = [];
		$fail = [];
		foreach ($files as $key => $file){
			try {
				$extension = strtolower($file->extension);
				if (!array_key_exists($extension, $this->extension)) {
					throw new \Exception("暂不支持文件格式{$extension}");
				}
				$up = $folder . '_' . time() .'_'. $this->max.Utility::createRandom(2);
				$doc = $up . '.' . $extension;
				$original = $save_path. $doc;
				
				$save_result = $file->saveAs($physical_path.$original);
				if (!$save_result) {
					throw new \Exception("文件保存失败");
				}
				$config = $this->format[$this->extension[$extension]];
				if($config['figure']==2){
					$thumb = $config['thumb'];
				}
				elseif($config['figure']==1){
					$thumb = $save_path .'thumb_' . $doc;
					Image::thumbnail($physical_path. $original, $this->thumb_width, $this->thumb_height)
					->save($physical_path . $thumb, ['quality' => 40]);
				}
				else{
					$thumb = '';
				}
				
				$success[] = [
					'original'=>$original,
					'thumb' => $thumb,
				];
				
			} catch (\Exception $ex) {
				Logger::writeException($ex);
				$fail[]=$file->name;
			}
		}
		return [
			's'=>$success,
			'f'=>$fail,
		];
		
	}
	
	public function widget($scene){
		$data_types = FiletypeScene::find()->alias('T1')
		->leftJoin(Filetype::tableName() . ' T2', 'T1.filetype_id = T2.id')
		->leftJoin(FiletypeCategory::tableName() . 'T3', 'T1.category_id = T3.id')
		->select(['T1.filetype_id', 'T1.scene_id','T1.apart_id', 'T1.category_id', 'required', 'T3.category_type', 'T3.category_name', 'T2.filetype_name', 'filetype_name', 'upload_type', 'upload_type_key', 'file_accept'])
		->where(['T1.scene_id'=>$scene, 'T2.status'=>8])
		->orderBy(['T3.order_number'=>SORT_ASC, 'T1.order_number'=>SORT_ASC])
		->asArray()->all();
		
		$query_files = BusinessFiles::find()->alias('T1')
		->innerJoin(FiletypeScene::tableName(). ' T2', 'T1.filetype_id = T2.filetype_id')
		->select(['T1.file_id', 'T1.filetype_id', 'T1.object_id', 'T1.thumb_file_path as thumb', 'T1.file_path as file', 'file_serial'])
		->where(['T1.status'=>8, 'T1.business_id'=>$this->keyid, 'T2.scene_id'=>$scene])
		->orderBy(['T1.filetype_id'=>SORT_ASC, 'T1.object_id'=>SORT_ASC, 'T1.file_serial'=>SORT_ASC])
		->asArray();
		$data_files = $query_files->all();
		
		$array_category = [];//目录，键值为category_id
		$array_type = []; //附件类型，键值为file_tyie_id
		
		foreach($data_files as $file){
			$filetype_id = $file['filetype_id'];
			$object_id = $file['object_id'];
			$file['file'] = $this->path_http. $file['file'];
			$file['thumb'] = $this->path_http. $file['thumb'];
			if(empty($object_id)){
				$object_id=0;
			}
			$key = $object_id.'_'.$filetype_id;
			if(!array_key_exists($key, $array_type)){
				$array_type[$key] = [];
			}
			$array_type[$key][] = $file;
			
		}
		
		foreach($data_types as $filetype){
			$category_type = $filetype['category_type'];
			$filetype_id = $filetype['filetype_id'];
			$category_id = $filetype['category_id'];
			
			$objects = $this->getObject($category_type);
			foreach ($objects as $key=>$object){
				if($key!=0){
					$category_id = $key;
				}
				
				if(!array_key_exists($category_id, $array_category)){
					$array_category[$category_id] = [
						'object_id'=>$key,
						'category_name' => $object.$filetype['category_name'],
						'category_type' => $category_type,
						'types' => []
					];
				}
				$index = $key.'_'.$filetype_id;
				if(isset($array_type[$index])){
					$filetype['files'] = $array_type[$index];
					unset($array_type[$index]);
				}else{
					$filetype['files'] = [];
				}
				$filetype['object_id'] = $key;
				$array_category[$category_id]['types'][] = $filetype;
			}
			
		}
		/*
		 if(count($array_type)>0){
		 $array_category[0] = [
		 'category_name' => '异常资料',
		 'types' => []
		 ];
		 }
		 foreach($array_type as $except){
		 $array_category[0]['types'][] = $except;
		 }
		 */
		//var_dump($array_category);die;
		return $array_category;
	}
	
	
	
	private function getObject($type){
		switch ($type){
			case 1:
				return [''];
			case 2:
				if($this->customers==null){
					$query = Customer::find()
					->select(['customer_name', 'customer_id'])
					->where(['business_id'=>$this->business, 'status'=>8 ])
					->asArray()->indexBy('customer_id');
					if($this->object!=0){
						$query->andWhere(['customer_id'=>$this->object]);
					}
					$this->customers = $query->column();
				}
				return $this->customers;
				break;
			case 3:
				if($this->companys==null){
					$query = Company::find()
					->select(['company_name', 'company_id'])
					->where(['business_id'=>$this->business, 'status'=>8 ])
					->asArray()->indexBy('company_id');
					
					if($this->object!=0){
						$query->andWhere(['company_id'=>$this->object]);
					}
					$this->companys= $query->column();
				}
				return $this->companys;
				break;
			default:
				return '';
				break;
		}
	}
}