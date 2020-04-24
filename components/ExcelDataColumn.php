<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace lemon\components;


use yii\grid\DataColumn;

/**
 * DataColumn is the default column type for the [[GridView]] widget.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ExcelDataColumn extends DataColumn
{
	public $cell;
	public $merge;
	public $width;
	public $wrap;
}
