<?php
namespace lemon\widgets\services;

use Yii;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\imagine\Image;
use lemon\repository\Utility;
use lemon\repository\Logger;
use yii\base\Component;
/**
 * wishes 想要处理的方法，如上传的文件要经过审批
 * upload_type 1 普通文件 2 固定页码 3 图片转pdf
 * @author shaodd
 *
 */
class GalleryService extends Component
{
	public $user_id =0;
	public $keyid;  //业务编号（工单编号/案件编号）
	
	public $trees =[]; //移动到的目录（ztree）
	
	protected $limit=20;
	protected $thumb_width = 200;
	protected $thumb_height = 150;
	
	protected $path_http;
	//protected $folder;  //文件目录名(filetype)
	
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
	
	public function paths(){
		$paths = [
			'upload'=>Url::toRoute('/site/error'),
			'serial'=>Url::toRoute('/site/error'),
			'del'=>Url::toRoute('/site/error'),
			'replace'=>Url::toRoute('/site/error'),
			'move'=>Url::toRoute('/site/error'),
			'book'=>Url::toRoute('/site/error'),
		];
		
		return $paths;
	}
	public function extentions(){
		return $this->extension;
	}
	
	public function accepts(){
		$accept = [];
		foreach ($this->format as $key=>$item){
			$accept[$key] = $item['mime'];
		}
		return $accept;
	}
	
	public function save($save_dir='upload', $folder=8888){
		$physical_path= Yii::$app->params['FILE_PHYSICAL_PATH'];
		$save_path = "/".$save_dir. date("/Y/m/") . $this->keyid. '/';
		
		
		if (!file_exists($physical_path. $save_path)) {
			mkdir($physical_path. $save_path, 0777, true);
		}
		
		$files = UploadedFile::getInstancesByName('UploadFiles');
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
				$up = $folder . '_' . time() . '_w' . $this->user_id . '_' . $this->max.Utility::createRandom(2);
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
					'name' => $file->name,
					'size' => $file->size,
					'format' => $this->extension[$extension],
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
		return [];
	}
	
	protected function deleteFile($path){
		try{
			$physical_path= Yii::$app->params['FILE_PHYSICAL_PATH'];
			unlink($physical_path.$path);
		}catch (\Exception $ex){
			Logger::writeException($ex);
		}
	}
	
}