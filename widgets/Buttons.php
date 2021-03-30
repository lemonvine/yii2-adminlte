<?php
namespace lemon\widgets;

use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class Buttons extends Widget
{
	public $options = [];
	public $fixed = true;
	public $right = false;
	public $key_id = 0;
	
	public static $form_id = 'main_form';
	
	public static $btn_css ='';
	public static $edit_params = ['target'=>'dialog', 'title'=>'编辑', 'color'=>'primary', 'size'=>'md', 'url'=>'', 'isbtn'=>false];
	private $submit_type = false;
	private $view_dir = "@lemon/widgets/btns/views/";
	
	public function init()
	{
		$options = $this->options;
		$tag = ArrayHelper::remove($options, 'tag', 'div');
		$options['class'] = $this->getClass();
		echo Html::beginTag($tag, $options);
	}
	
	public function run()
	{
		
		echo Html::endTag(ArrayHelper::remove($this->options, 'tag', 'div'));
	}
	
	public static function Button($config){
		$params = ['title'=>'确定', 'color'=>'primary', 'isbtn'=>true, 'class'=>'', 'id'=>'', 'data'=>[]];
		$params = array_merge($params, $config);
		$class = ($params['isbtn']?'btn btn-':static::$btn_css).$params['color'];
		if(!empty($params['class'])){
			$class .= ' '.$params['class'];
		}
		$options = [
			'class'=>$class,
		];
		if(!empty($params['id'])){
			$options['id'] = $params['id'];
		}
		if(count($params['data'])>0){
			foreach ($params['data'] as $key=>$data){
				$options['data-'.$key] = $data;
			}
		}
		
		return Html::Button($params['title'], $options);
	}
	
	/**
	 * 后退
	 * @param array $config
	 * @return string
	 */
	public static function BackButton($config=[]){
		$params = ['title'=>'返回', 'id'=>'btn_back', 'color'=>'default', 'isbtn'=>true];
		$params = array_merge($params, $config);
		$class = ($params['isbtn']?'btn btn-':static::$btn_css).$params['color'];
		return Html::Button($params['title'], ['class' => $class, 'id'=>$params['id'], 'onclick'=>'bolevine.forward()']);
	}
	
	/**
	 * 提交，提交前会确认
	 * @param array $config
	 * @return string
	 */
	public static function SubmitButton($config=[]){
		$params = ['title'=>'提交', 'word'=>'确定要提交吗', 'id'=>'btn_submit', 'color'=>'warning', 'isbtn'=>true, 'form'=>'#main_form', 'method'=>'post', 'url'=>'', 'display'=>true];
		$params = array_merge($params, $config);
		
		$class = ($params['isbtn']?'btn btn-':static::$btn_css).$params['color'];
		$options = [
			'id' => $params['id'],
			'class' => $class . ' ceremonial',
			'data-form' => $params['form'],
			'data-method' => $params['method'],
			'data-word' => $params['word'],
		];
		if(!$params['display']){
			$options['style']='display:none;';
		}
		if(!$params['url']){
			$options['data-url']=$params['url'];
		}
		
		return Html::Button($params['title'], $options);
	}
	
	/**
	 * 直接提交/保存
	 * @param array $config
	 * @return string
	 */
	public static function OkButton($config=[]){
		$params = ['title'=>'确定', 'color'=>'success', 'isbtn'=>true];
		$params = array_merge($params, $config);
		$class = ($params['isbtn']?'btn btn-':static::$btn_css).$params['color'];
		return Html::submitButton($params['title'], ['id' => 'btn_ok', 'class' => $class]);
	}
	
	/**
	 * 取消
	 * @param array $config
	 * @return string
	 */
	public static function CancelButton($config=[]){
		$params = ['title'=>'取消', 'color'=>'default', 'isbtn'=>true];
		$params = array_merge($params, $config);
		$class = ($params['isbtn']?'btn btn-':static::$btn_css).$params['color'];
		return Html::Button($params['title'], ['id' => 'btn_cancel', 'class' => $class, 'onclick'=>'window.parent.bolevine.turnoff()']);
	}
	
	/**
	 * 弹框
	 * @param array $config
	 * @return string
	 */
	public static function DialogButton($config=[]){
		$params = ['title'=>'保存', 'url'=>'', 'isbtn'=>true, 'color'=>'info', 'size'=>'md', 'badge'=>'', 'class'=>''];
		$params = array_merge($params, $config);
		$badge = '';
		if(!empty($params['badge'])){
			$badge = '<span class="badge badge-pill badge-danger">'.$params['badge'].'</span>';
		}
		$options = [
			'class'=>$params['class'].($params['isbtn']?'btn btn-':static::$btn_css).$params['color'].' modaldialog',
			'data-title' => $params['title'],
			'data-area' => $params['size'],
			'data-url' => $params['url'],
		];
		if(isset($config['id'])){
			$options['id']=$config['id'];
		}
		return Html::a($params['title'].$badge, 'javascript:;', $options);
	}
	
	/**
	 * 页面跳转
	 * @param array $config
	 * @return string
	 */
	public static function GoButton($config=[]){
		$params = ['title'=>'保存', 'url'=>'', 'isbtn'=>true, 'color'=>'info', 'target'=>'_self', 'class'=>''];
		$params = array_merge($params, $config);
		
		$class = ($params['isbtn']?'btn btn-':static::$btn_css).$params['color'];
		if(!empty($params['class'])){
			$class .= ' '.$params['class'];
		}
		return Html::a($params['title'],  $params['url']??'javascript:;', ['class' => $class, 'target'=>$params['target']]);
	}
	
	/**
	 * 导出
	 * @param array $config
	 */
	public static function ExportButton($config=[]){
		$params = ['title'=>'导出', 'form_id'=>self::$form_id, 'color'=>'secondary'];
		$params = array_merge($params, $config);
		return Html::button($params['title'], ['class' => 'btn btn-'.$params['color'], 'onclick'=>"bolevine.export('#".$params['form_id']."')" ]);
	}
	
	/**
	 * 查询按钮组
	 * @param array $items
	 * @param array $config
	 * @return string
	 */
	public static function SearchButtons($items=[], $config=[]){
		$default_params = ['title'=>'确定', 'color'=>'primary', 'url'=>'', 'isbtn'=>true, ];
		$btn_params = ['target'=>'', 'btnval' => '', 'click'=>''];
		$search_params = ['title'=>'查询', 'color'=>'primary', 'form'=>'#main_form', 'target'=>'submit', 'isbtn'=>true, 'btnval' => 'search', 'click'=>"bolevine.search('#main_form')"];
		
		$content = '';
		$search_params = array_merge($search_params, $config);
		$items[] = $search_params;
		foreach ($items as $btn){
			$params = array_merge($btn_params, $btn);
			if($params['target']=='submit'){
				$content .= self::OkButton($params);
			}elseif($params['target']=='search'){
				$class = ($params['isbtn']?'btn btn-':static::$btn_css).$params['color'];
				$content .= Html::Button($params['title'], ['class' => $class, 'value'=>$params['btnval'], 'onclick'=>$params['click']]);
			}elseif($params['target']=='dialog'){
				$content .= self::DialogButton($params);
			}elseif($params['target']=='func'){
				$params = array_merge($default_params, $params);
				$class = ($params['isbtn']?'btn btn-':static::$btn_css).$params['color'];
				$content .= Html::Button($params['title'], ['class' => $class, 'value'=>$params['btnval'], 'onclick'=>$params['click']]);
			}elseif($params['target']=='raw'){
				$content .= $params['raw'];
			}elseif($params['target']=='back'){
				$content .= self::BackButton($params);
			}elseif($params['target']=='export'){
				$content .= self::ExportButton($params);
			}else{
				$content .= self::GoButton($params);
			}
		}
		
		$content = Html::tag('div', $content, ['class' => "form-group btns-line pull-right ml-auto"]);
		
		return $content;
	}
	
	/**
	 * 弹框按钮组
	 * @param array $items
	 * @return string
	 */
	public static function DialogButtons($items=[]){
		$default_params = ['title'=>'确定', 'color'=>'primary', 'url'=>'', 'isbtn'=>true, ];
		$btn_params = ['target'=>'model', 'btnval' => '', 'click'=>'', 'raw'=>''];
		$content = '';
		
		foreach ($items as $btn){
			$params = array_merge($btn_params, $btn);
			if($params['target']=='ok'){
				$content .= self::OkButton($params);
			}elseif($params['target']=='cancel'){
				$content .= self::CancelButton($params);
			}elseif($params['target']=='back'){
				$content .= self::BackButton($params);
			}elseif($params['target']=='button'){
				$options = ['class'=>'btn btn-'.$params['color']];
				if(isset($params['id'])){
					$options['id'] = $params['id'];
				}
				if(!empty($params['btnval'])){
					$options['value'] = $params['btnval'];
				}
				if(!empty($params['click'])){
					$options['onclick'] = $params['click'];
				}
				$content .= Html::Button($params['title'], $options);
			}elseif($params['target']=='func'){
				$params = array_merge($default_params, $params);
				$class = ($params['isbtn']?'btn btn-':static::$btn_css).$params['color'];
				$content .= Html::Button($params['title'], ['class' => $class, 'value'=>$params['btnval'], 'onclick'=>$params['click']]);
			}elseif($params['target']=='raw'){
				$content .= $params['raw'];
			}else{
				$content .= self::GoButton($params);
			}
		}
		
		$content = Html::tag('div', $content, ['class' => "form-group col-12 text-center fixed-bottom btns-line"]);
		
		return $content;
	}
	
	public static function SearchButton($config=[]){
		return self::SearchButtons();
	}
	
	/**
	 * 新增和查询按钮组合
	 * @param array $config
	 * @return string
	 */
	public static function SearchAddButton($config=[]){
		$add_params = [
			'title'=> ArrayHelper::remove($config, 'title', '添加'),
			'url'=> Url::toRoute(ArrayHelper::remove($config, 'url', ['create'])),
			'isbtn' => ArrayHelper::remove($config, 'isbtn', true	)
		];
		if(isset($config['target'])){
			$add_params['target'] = ArrayHelper::remove($config, 'target');
		}
		if(isset($config['size'])){
			$add_params['size'] = ArrayHelper::remove($config, 'size');
		}
		$back= ArrayHelper::remove($config, 'back', false);
		
		$add_params= array_merge(static::$edit_params, $add_params);
		$btns = [];
		if($back){
			$btns[] = ['target'=>'back'];
		}
		$extras = ArrayHelper::remove($config, 'items', []);
		if(count($extras)>0){
			$btns = array_merge($btns, $extras);
		}
		$btns[] = $add_params;
		return self::SearchButtons($btns, $config);
	}
	
	/**
	 * 查询和导出按钮组合
	 * @param array $config
	 * @return string
	 */
	public static function SearchExportButton($config=[]){
		$export = ArrayHelper::remove($config, 'export', []);
		$export['target'] = 'export';
		$back= ArrayHelper::remove($config, 'back', false);
		
		$btns = [];
		if($back){
			$btns[] = ['target'=>'back'];
		}
		$extras = ArrayHelper::remove($config, 'items', []);
		if(count($extras)>0){
			$btns = array_merge($btns, $extras);
		}
		$config['target']='search';
		$btns[] = $export;
		return self::SearchButtons($btns, $config);
	}
	
	/**
	 * 弹框的确定取消按钮
	 * @return string
	 */
	public static function OkCancel(){
		$btns = [];
		$btns[] = ['target'=>'cancel', 'title'=>'取消'];
		$btns[] = ['target'=>'ok', 'title'=>'确定'];
		return self::DialogButtons($btns);
	}
	
	public static function Moves(){
		$btns = [
			['target'=>'button', 'title'=>'上一步', 'color'=>'default', 'id'=>'btn_last'],
			['target'=>'button', 'title'=>'下一步', 'color'=>'success', 'id'=>'btn_next'],
			['target'=>'button', 'title'=>'确 &nbsp;&nbsp;定', 'color'=>'primary', 'id'=>'btn_ok'],
		];
		return self::DialogButtons($btns);
		
	}
	
	/**
	 * 编辑按钮
	 * @param array $config
	 * @return string
	 */
	public static function EditDialog($config=[]){
		$params= array_merge(static::$edit_params, $config);
		return self::DialogButton($params);
	}
	
	/**
	 * 删除按钮
	 * @param array $config
	 * 'title'=>'删除',
	 * 'words'=>'您确定要删除此项吗？',
	 * 'isbtn'=>false,
	 * 'color'=>'danger',
	 * 'data'=>[],
	 * 'callback'=>'',
	 * way ajax page, dialog
	 * @return string
	 */
	public static function DeleteButton($config=[]){
		$params = ['title'=>'删除', 'words'=>'您确定要删除此项吗？', 'isbtn'=>false, 'color'=>'danger', 'data'=>[], 'callback'=>'', 'way'=>'ajax'];
		$params = array_merge($params, $config);
		$data = isset($params['data'])?json_encode($params['data']):'';
		return Html::a($params['title'], 'javascript:;', ['class'=>($params['isbtn']?'btn btn-':static::$btn_css).$params['color'].' confirmdialog', 'data-word'=>$params['words'], 'data-way'=>$params['way'], 'data-data'=>$data,'data-url'=>$params['url'], 'data-callback'=>$params['callback']]);
	}
	
	public static function Preservation($config=[]){
		$params = ['title'=>'保存', 'id'=>'btn_save', 'isbtn'=>true, 'color'=>'success', 'url'=>'', 'ready'=>'', 'callback'=>'', 'form'=>'#main_form', 'method'=>'post', 'submit'=>'save', 'display'=>true];
		$params = array_merge($params, $config);
		
		$options = [
			'id' => $params['id'],
			'class' => ($params['isbtn']?'btn btn-':static::$btn_css).$params['color']. ' preservation',
			'data-f' => $params['form'],
			'data-m' => $params['method'],
			'data-s' => $params['submit'],
			'data-c' => $params['callback'],
			'data-u' => $params['url'],
			'data-r' => $params['ready'],
		];
		if(!$params['display']){
			$options['style']='display:none;';
		}
		
		return Html::Button($params['title'], $options);
	}
	
	public static function AlertButton($config=[]){
		$params = ['title'=>'保存', 'html'=>'', 'isbtn'=>true, 'color'=>'info', 'size'=>'sm'];
		$params = array_merge($params, $config);
		
		return Html::a($params['title'], 'javascript:;', ['class'=>($params['isbtn']?'btn btn-':static::$btn_css).$params['color'].' modaldialog', 'data-title' => $params['title'], 'data-area' => $params['size'],
			'data-html'=>$params['html']]);
	}

	
	//AJAX调用按钮
	public static function AsynchButton($config=[]){
		$params = ['title'=>'保存', 'html'=>'', 'isbtn'=>true, 'color'=>'info', 'data'=>[], 'callback'=>'', 'type'=>'post'];
		$params = array_merge($params, $config);
		
		if($params['data']){
			$data = json_encode($params['data']);
		}
		else{
			$data = '';
		}
		return Html::a($params['title'], 'javascript:;', ['class'=>($params['isbtn']?'btn btn-':static::$btn_css).$params['color'].' asynchtrace', 'data-type'=>$params['type'], 'data-data'=>$data,
			'data-url'=>$params['url'], 'data-callback'=>$params['callback']]);
	}
	public function btn($config=[]){
		echo self::Button($config);
	}
	
	public function back(){
		echo self::BackButton();
	}
	
	public function cancel(){
		echo self::CancelButton();
	}
	
	public function ok(){
		echo self::OkButton();
	}
	
	public function go($config=[]){
		$config['isbtn']=true;
		echo self::GoButton($config);
	}
	
	public function export($config=[]){
		echo self::ExportButton($config);
	}
	
	public function search(){
		echo self::SearchButton(['unique'=>false, 'form_id'=>self::$form_id]);
	}
	
	
	/**
	 * 弹出对话框
	 * @param unknown $title
	 * @param unknown $url
	 * @param string $color
	 * @param string $size
	 */
	public function dialog($config=[]){
		$config['isbtn']=true;
		echo self::DialogButton($config);
	}
	
	public function preservate($config=[]){
		echo self::Preservation($config);
	}
	
	/**
	 * 弹出提示按钮
	 * @param unknown $title
	 * @param unknown $url
	 * @param string $color
	 * @param string $size
	 */
	public function alert($config=[]){
		$config['isbtn']=true;
		echo self::AlertButton($config);
	}
	
	public function save($config=[]){
		echo self::Preservation($config);
	}
	
	public function submit($config=[]){
		echo self::SubmitButton($config);
	}
	
	public function delete($config=[]){
		echo self::DeleteButton($config);
	}
	
	
	private function getClass(){
		$class = $this->fixed?'form-btns':'form-group btns-line';
		if($this->right){
			$class .= ' text-right ml-auto';
		}
		$options = $this->options;
		if (isset($options['class'])) {
			$class = $options['class'];
		}
		return $class;
	}
}

