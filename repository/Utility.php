<?php
namespace lemon\repository;

use Yii;
use yii\imagine\Image;

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
		if (isset($params['referer_url'])) {
			$referer = $params['referer_url'];
		}else{
			$params = Yii::$app->request->queryParams;
			if (isset($params['referer_url'])) {
				$referer = $params['referer_url'];
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
		->save($basepath.$filename, ['quality' => 100]);
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
	
	public static function addon($char){
		return ['append' => ['type'=>'char', 'content' => $char]];
	}
}