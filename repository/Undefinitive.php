<?php
namespace lemon\repository;

use Yii;
use lemon\widgets\Menu;
use yii\helpers\Url;

class Undefinitive {
	
	/**
	 *
	 * 获取后台用户的菜单列表
	 */
	public static function adminMenus($refresh=true){
		$uid = Yii::$app->user->identity->id;
		$cache_name = 'DYZC_MENU_F6DiVB3Hf'.$uid;
		$cache_menu = Utility::getCache($cache_name);
		if($cache_menu && !$refresh){
			return $cache_menu;
		}
		$auth_rule = Yii::createObject(Yii::$app->params['classname_rule']);
		$auth_assignment = Yii::createObject(Yii::$app->params['classname_assignment']);
		if($uid==1){
			$data = $auth_rule::find()->where(['is_display'=>1])
			->select(['display_name label', 'id', 'icon', 'uri_path url', 'parent_id', 'name'])
			->orderBy("parent_id, sort_number")->asArray()->indexBy('id')->all();
		}
		else{
			$data = $auth_rule::find()->innerJoin($auth_assignment::tableName(), 'id = rule_id')
			->where(['is_display'=>1, 'user_id'=>$uid])
			->select(['display_name label', 'id', 'icon', 'uri_path url', 'parent_id', 'name'])
			->orderBy("parent_id, sort_number")->asArray()->indexBy('id')->all();
		}
		if(count($data)>0){
			foreach($data as $k=>$t){
				if($t['parent_id']!=0){
					$t['url'] = [$t['url'], 'm'=>$t['name']];
					$parent = $t['parent_id'];
					unset($t['parent_id']);
					unset($t['id']);
					$data[$parent]['items'][]=$t;
					unset($data[$k]);
				}
				else{
					$data[$k]['url'] = 'javascript:;';
					unset($data[$k]['parent_id']);
					unset($data[$k]['id']);
				}
			}
			$html = Menu::widget([
				'options' => ['class' => 'nav nav-pills nav-sidebar flex-column nav-child-indent', 'data-widget'=> 'treeview',  'role'=>"menu", 'data-accordion'=>"false"],
				'items' => $data,
			]);
			Utility::setCache($cache_name, $html, 60*60);
			return $html;
		}
		else{
			$data = [['label' => '未授权', 'icon' => 'minus-circle', 'url' => ['/site/noauth']]];
			return "<script type='text/javascript'>document.location.href='".Url::toRoute(['/site/noauth'])."'</script>";
		}
	}
	
	public static function mission($action, $params=[]){
		$mission = Yii::createObject(Yii::$app->params['classname_mission']);
		return $mission::$action($params);
	}
}

