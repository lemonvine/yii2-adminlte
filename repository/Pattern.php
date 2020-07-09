<?php

namespace lemon\repository;

/**
 * 统一模板的静态变量
 * @author shaodd
 *
 */
class Pattern{
	
	/**大写字母 小写字母 数字 特殊字符，四种包括三种，长度8~30*/
	public static $RegEx = "/^(?![a-zA-Z]+$)(?![A-Z0-9]+$)(?![A-Z\W_]+$)(?![a-z0-9]+$)(?![a-z\W_]+$)(?![0-9\W_]+$)[a-zA-Z0-9\W_]{8,30}$/";
	
	public static $CODE = [
		'display' => [ 1=>'显示', 0=>'不显示'], //显示状态
		'boolean' => [1=>'是', 0=>'否'], //逻辑是否
		'arealevel' => [1=>'省级', 2=>'市级', 3=>'县级'], //行政级别
		'status' => [ 8=>'正常', 4=>'关闭',], //状态
		'audit' =>[8=>'通过', 4=>'不通过'], //审核结果
	];
	
} 