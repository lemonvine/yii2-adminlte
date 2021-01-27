<?php
namespace lemon\repository;

use Yii;
use yii\imagine\Image;
use yii\helpers\Json;
use yii\helpers\Html;

class Utility
{
	/**
	 * 生成高强度密码
	 * @param unknown $len
	 * @return string
	 */
	public static function generatePwd($len)
	{
		$digit = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
		$upper = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z'];
		$lower = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l','m', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y','z'];
		$special = ['!', '@','#', '$', '%', '^', '&', '*', '+', '=', '.'];
		
		$password = '';
		$pos = mt_rand(0, 9);
		$password .= $digit[$pos];
		$pos = mt_rand(0, 26);
		$password .= $upper[$pos];
		$pos = mt_rand(0, 26);
		$password .= $lower[$pos];
		$chars = array_merge($digit, $upper, $lower, $special);
		
		$charsLen = count($chars) - 1;
		for($i = 0; $i < $len-3; $i++)
		{
			$pos = mt_rand(0, $charsLen);
			$password .= $chars[$pos];
		}
		$password = str_shuffle($password);
		return $password;
	}
	
	public static function createRandom($len, $chars=null)
	{
		if (is_null($chars)){
			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		}
		mt_srand(10000000*(double)microtime());
		for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++){
			$str .= $chars[mt_rand(0, $lc)];
		}
		return $str;
	}
	
	/**
	 * 获取来源页面地址
	 * @return string
	 */
	public static function getReferer()
	{
		$params = [];
		if (Yii::$app->request->isPost) {
			$params = Yii::$app->request->post();
		}
		if (isset($params['ref'])) {
			$referer = $params['ref'];
		}else{
			$params = Yii::$app->request->queryParams;
			if (isset($params['ref'])) {
				$referer = $params['ref'];
			} else {
				$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
			}
		}
		return $referer;
	}
	
	public static function getPrimary()
	{
		$params = Yii::$app->request->queryParams;
		if (isset($params['primary_url'])) {
			$primary = $params['primary_url'];
		} else {
			$primary = Yii::$app->request->url;
		}
		return $primary;
	}
	
	/**
	 * 获取IP地址
	 *
	 * @return string|unknown
	 */
	public static function getIP()
	{
		if (! empty($_SERVER["HTTP_CLIENT_IP"])) {
			$cip = $_SERVER["HTTP_CLIENT_IP"];
		} elseif (! empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} elseif (! empty($_SERVER["REMOTE_ADDR"])) {
			$cip = $_SERVER["REMOTE_ADDR"];
		} else {
			$cip = "unknown";
		}
		return $cip;
	}
	
	/**
	 * 生成缩略图
	 * @param unknown $basepath
	 * @param unknown $origin
	 * @param unknown $width
	 * @param unknown $height
	 * @return string
	 */
	public static function createThumb($basepath, $origin, $width, $height){
		$dot_pos = strrpos($origin, '.');
		$filename = substr($origin, 0, $dot_pos).'_'.$width.'_'.$height.''.substr($origin, $dot_pos);
		Image::thumbnail($basepath.$origin, $width, $height)
		->save($basepath.$filename, ['quality' => 90]);
		return $filename;
	}
	
	/**
	 * 获取自动增长的顺序号
	 * @param unknown $param
	 * @return string|NULL|\yii\db\false
	 */
	public static function getNextval($param){
		$connection  = Yii::$app->db;
		$sql	 = "SELECT nextval('$param')";
		$command = $connection->createCommand($sql);
		$sequence =  $command->queryScalar();
		return $sequence;
	}
	
	/**
	 * 获取缓存
	 * @param unknown $cache_name
	 * @return mixed|boolean|\yii\caching\Dependency|\yii\caching\false
	 */
	public static function getCache($cache_name){
		$cache = Yii::$app->cache;
		$cache_data = $cache->get($cache_name);
		return $cache_data;
	}
	
	/**
	 * 保存缓存
	 * @param unknown $cache_name
	 * @param unknown $cache_data
	 * @param number $limit
	 */
	public static function setCache($cache_name, $cache_data, $limit=3600){
		$cache = Yii::$app->cache;
		$cache->set($cache_name, $cache_data, $limit);
	}
	
	public static function addon($char, $type='char'){
		return ['append' => ['type'=>$type, 'content' => $char]];
	}
	
	public static function encodeChinese($str){
		$encoded_str = urlencode($str);
		$encoded_str= str_replace("+", "%20", $encoded_str);
		return $encoded_str;
	}
	
	public static function buildGallery($strfile, $isjson=TRUE, $editable=FALSE, $options =[]){
		$html = '';
		$ulOptions = ['class'=>'list-inline upload-gallery m-1'];
		if(!empty($options)){
			$ulOptions = array_merge($ulOptions, $options);
		}
		if(!empty($strfile)){
			if($isjson){
				$medias = Json::decode($strfile, true);
			}else{
				$medias = [$strfile];
			}
			foreach ($medias as $media){
				$view_text = '查看';
				if(is_array($media)){
					if(key_exists('l', $media)){
						$media_src = $media['l'];
						$media_type = $media['t']??'image';
						$media_name = $media['n']??'';
					}else{
						$media_type = $media[0];
						$media_src = $media[1];
						$media_name = $media[2]??'';
					}
					$media_http = self::dehttpPath($media_src);
					
					if($media_type=='image'){
						//$media_html = '<img class="media" src="'.$media_http.'" data-f="'.$media_src.'" data-h="'.$http_path.'" data-p="image" target="image">';
						$media_html = '<img class="media" src="'.$media_http.'" data-f="'.$media_src.'" data-p="image" target="image">';
					}elseif($media_type=='audio'){
						$media_html = '<audio class="media" controls="controls" preload="meta" src="'.$media_http.'" data-f="'.$media_src.'" data-p="audio"></audio>';
					}if($media_type=='video'){
						$media_html = '<video class="media" controls="controls" preload="meta" src="'.$media_http.'" data-f="'.$media_src.'" data-p="video"></video>';
					}else{
						$view_text = '下载';
						if(empty($media_name)){
							$media_name = substr(strrchr($media_src, '.'), 1).'文件';
						}
						$media_html = '<div class="media">'.$media_name.'</div>';
					}
				}else{
					$media_http = self::dehttpPath($media);
					$media_html = '<img class="media" src="'.$media_http.'" data-f="'.$media.'" data-p="image">';
				}
				$media_html .= '<div class="upload-tools">';
				if($editable){
					$media_html .= '<a href="javascript:;" class="delete text-danger">删除</a>';
				}
				$media_html .= '<a href="'.$media_http.'" class="look" target="_blank">'.$view_text.'</a></div>';
				
				$html .= '<li class="upload-show img-thumbnail">'.$media_html.'</li>';
			}
		}
		$gallery = Html::tag('ul', $html, $ulOptions);
		return $gallery;
	}
	
	public static function enhttpPath(){
		
	}
	
	/**
	 * 解析文件路径，
	 * 文件路径有以下两种形式：
	 * 相对路径			/xxx/xxx/xxx.png
	 * 存储空间：相对路径		yun:/xxx/xxx/xxx.docx
	 * 全局参数FILE_HTTP_PATH可为数组或字符串
	 * @param unknown $path
	 * @return string
	 */
	public static function dehttpPath($path){
		$prefix = '';
		if(strpos($path, ':')>0){
			$exps = explode(':', $path);
			$store = $exps[0];
			$dir = $exps[1];
		}else{
			$store = 'local';
			$dir = $path;
		}
		$http_path = Yii::$app->params['FILE_HTTP_PATH'];
		if(is_array($http_path)){
			$prefix = $http_path[$store]??'';
		}else{
			$prefix = $http_path;
		}
		return $prefix.$dir;
	}
}