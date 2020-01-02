<?php
namespace lemon\components;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder;

/**
 * shaodd 2019-12-11 解决导出excel长串数字科学计数法的问题
 * @author shaodd
 *
 */
class ExcelValueBinder extends AdvancedValueBinder {
	public static function dataTypeForValue($pValue) { //只重写dataTypeForValue方法，去掉一些不必要的判断
		if (is_null($pValue)) {
			return DataType::TYPE_NULL;
		} elseif ($pValue instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
			return DataType::TYPE_INLINE;
		} elseif (strlen($pValue) > 1 && $pValue[0] === '=') {
			return DataType::TYPE_FORMULA;
		} elseif (is_bool($pValue)) {
			return DataType::TYPE_BOOL;
		} elseif (is_float($pValue) || is_int($pValue)) {
			return DataType::TYPE_NUMERIC;
		}
		return DataType::TYPE_STRING;
	}
}