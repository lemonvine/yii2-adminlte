<?php
namespace lemon\web;

use Yii;
use yii\web\View;
use yii\web\AssetBundle;

/**
 * AdminLte AssetBundle
 * @since 0.1
 */
class AdminLteAsset extends AssetBundle
{
	public static $path = '@vendor/lemonvine/yii2-adminlte/dist';
	
	
	public $depends = [
		'yii\web\JqueryAsset',
	];
	
	public function init()
	{
		$this->sourcePath = self::$path;
		$postfix = YII_DEBUG ? '' : '.min';
		$version = Yii::$app->params['admin_version'];
		
		$this->js[] = "bootstrap/adminlte{$postfix}.js?v=v{$version}";
		$this->js[] = "layui/layer{$postfix}.js?v=v{$version}";
		$this->js[] = "js/adminv{$postfix}.js?v=v{$version}";
		
		$this->css[] = "bootstrap/font-awesome{$postfix}.css?v=v{$version}";
		$this->css[] = "bootstrap/adminlte{$postfix}.css?v=v{$version}";
		$this->css[] = "css/adminv{$postfix}.css?v=v{$version}";
		
		parent::init();
	}
	
	public static function loadModule($view, $modules, $options=[]){
		$directoryAsset = Yii::$app->assetManager->getPublishedUrl(self::$path);
		$postfix = YII_DEBUG ? '' : '.min';
		$version = Yii::$app->params['admin_version'];
		$depend = 'lemon\web\AdminlteAsset';
		$js = [];
		$css = [];
		$needs = [];
		if(!is_array($modules)){
			$needs[] = $modules;
		}
		else{
			$needs = $modules;
		}
		foreach ($needs as $module){
			switch ($module){
				case 'datepicker':
					$js[] = "layui/laydate";
					break;
				case 'handlebar':
					$js[] = "plugins/handlebars";
					break;
				case 'bundle':
					$js[] = "bootstrap/bootstrap.bundle";
					break;
				case 'combo':
					$css[] = "plugins/combo";
					$js[] = "plugins/combo";
					break;
					break;
				case 'icheck':
					$css[] = "plugins/icheck-bootstrap";
					$js[] = "plugins/icheck-bootstrap";
					break;
				case 'ztree':
					$css[] = "ztree/css/ztree/ztree";
					$js[] = "ztree/js/jquery.ztree.all-3.5";
					break;
				case 'gallery':
					$css[] = "gallery/gallery";
					$js[] = "gallery/gallery";
					break;
				case 'echarts':
					$js[] = "plugins/echarts";
					break;
				case 'bootstraptable':
					$css[] = "plugins/bootstrap-table";
					$js[] = "plugins/bootstrap-table";
					break;
				case 'viewer':
					$css[] = "viewer/viewer";
					$js[] = "viewer/viewer";
					break;
				case 'jqueryui':
					$js[] = "plugins/jquery-ui";
				case 'sortable':
					$js[] = "plugins/sortable";
					break;
				case 'lazyload':
					$js[] = "plugins/jquery.lazyload";
					$js[] = "plugins/scrollspy";
					break;
				case 'album':
					$js[] = "gallery/album";
					$css[] = "gallery/album";
					break;
				case 'layer':
					$js[] = "layui/layer";
					break;
				case 'layui':
					$css[] = "layui/layui";
					$js[] = "layui/layui";
					$js[] = "layui/modules/form";
					break;
				case 'cropper':
					$css[] = "plugins/cropper";
					$js[] = "plugins/cropper";
					break;
				default:
					if(substr($module,0,2)=='js'){
						$js[] = "{$module}";
					}
					elseif(substr($module,0,3)=='css'){
						$css[] = "{$module}";
					}
					break;
			}
		}
		if(count($options)==0){
			$options = ['depends' => $depend];
		}
		else{
			$maps= [
				'pos_head' => View::POS_HEAD
			];
			foreach ($options as $key=>$option){
				if(array_key_exists($option, $maps)){
					$options[$key] = $maps[$option];
				}
			}
		}
		
		$asset = new AdminLteAsset();
		foreach ($css as $file){
			$url = $directoryAsset.DIRECTORY_SEPARATOR.$file.$postfix.'.css?v=v'.$version;
			$view->registerCssFile($directoryAsset.DIRECTORY_SEPARATOR.$file.$postfix.'.css?v=v'.$version, $options);
		}
		
		foreach ($js as $file){
			$url = $directoryAsset.DIRECTORY_SEPARATOR.$file.$postfix.'.js?v=v'.$version;
			$view->registerJsFile($directoryAsset.DIRECTORY_SEPARATOR.$file.$postfix.'.js?v=v'.$version, $options);
		}
		
	}
}
