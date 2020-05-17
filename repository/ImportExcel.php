<?php
namespace lemon\repository;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ImportExcel{

	/**
	 * 读取excel表格中的数据
	 * @param	string $filePath excel文件路径
	 * @param	integer $start_row 开始的行数
	 * @return   array
	 */
	public function data($file, $start_row = 1,$sheet_index = 0) {
		
		#begin
		$file_type= IOFactory::identify($file);
		$reader=IOFactory::createReader($file_type);
		$reader->setReadDataOnly(true);
		if (!$reader->canRead($file)) {
			throw new \Exception('不支持的excel文件，请另存为新的excel文件重试');
		}
		$excel= $reader->load($file);

		$sheetCount = $excel->getSheetCount();
		$sheetNames = $excel->getSheetNames();

		//获取所有的sheet表格数据
		$emptyRowNum = 0;
		$i = $sheet_index ;
		//超过范围
		if($i > ($sheetCount-1))
		{
			echo 'count error';
			return;
		}

		/**默认读取excel文件中的第一个工作表*/
		$sheet= $excel->getSheet($i);
		
		/**取得最大的列号*/
		$allColumn = $sheet->getHighestColumn();
		$allColumnIndex = Coordinate::columnIndexFromString($allColumn);//转化为数字;
		
		/**取得一共有多少行*/
		$allRow = $sheet->getHighestRow();

		$arr = array();
		for ($currentRow = $start_row; $currentRow <= $allRow; $currentRow++) {
			/**从第A列开始输出*/
			$ifhasZero = false;
			for ($currentColumn = 0; $currentColumn <= $allColumnIndex; $currentColumn++) {
				$val = $sheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
				if($val == '0') $ifhasZero = true;
				$arr[$currentRow][] = trim($val);
			}

			if ($ifhasZero == true) {
				foreach ($arr[$currentRow] as $key => $value) {
					if($value === '') {
						unset($arr[$currentRow][$key]);
					}
				}
			}

			$arr[$currentRow] = $ifhasZero ? $arr[$currentRow] : array_filter($arr[$currentRow]);
			//统计连续空行
			if(empty($arr[$currentRow]) && $emptyRowNum <= 50) {
				$emptyRowNum++ ;
			} else {
				$emptyRowNum = 0;
			}
			//防止坑队友的同事在excel里面弄出很多的空行，陷入很漫长的循环中，设置如果连续超过50个空行就退出循环，返回结果
			//连续50行数据为空，不再读取后面行的数据，防止读满内存
			if($emptyRowNum > 50) {
				break;
			}
		}

		//只返回了第一个sheet的数据
		$returnData = $arr;

		//第一行数据就是空的，为了保留其原始数据，第一行数据就不做array_fiter操作；
		$returnData = $returnData && isset($returnData[$start_row]) && !empty($returnData[$start_row])  ? array_filter($returnData) : $returnData;
		$returnData= $this->array_iconv($returnData);
		return $returnData;
	}
	
	/**
	 * 生成excel数据表
	 * e.g.
	 * $fields = [
	 *	 ['key' => 'province', 'name' => '省', 'required' => false]
	 *	 ['key' => 'city', 'name' => '市', 'required' => true]
	 *	 ['key' => 'district', 'name' => '区/县', 'required' => true]
	 *	 ['key' => 'street', 'name' => '街道', 'required' => true]
	 * ];
	 *
	 * $dataList = [
	 *	 ['province' => 'xx省' , 'city'=>'xx市' , 'district' => 'xx县' ,'street' => 'xx街道'],
	 *	 ['province' => 'xx省' , 'city'=>'xx市' , 'district' => 'xx县' ,'street' => 'xx街道'],
	 *	 ['province' => 'xx省' , 'city'=>'xx市' , 'district' => 'xx县' ,'street' => 'xx街道'],
	 *	 ['province' => 'xx省' , 'city'=>'xx市' , 'district' => 'xx县' ,'street' => 'xx街道'],
	 * ]
	 *
	 * @param	array $fileds 表头数据
	 * @param	array $dataList 导出数据的数组
	 * @param	string $fileName 生成的文件名
	 * @return   mix
	 */
	public static function createExcelFromData($fileds, $dataList = array(),$fileName = 'data') {
		if(!count($fileds || !count($dataList))) {return false;}
		$dataList = array_values($dataList);
		$fieldConfig = array();
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$i = 0;
		foreach ($fileds as $key => $value) {
			$cloumStr = chr(ord("A") + $key);
			$column = $cloumStr . "1";
			//必填字段列表标红
			$required = isset($value['required']) ? $value['required'] : false;
			if ($required == true) {
				//把必填字段标红
				$objPHPExcel->getActiveSheet()->getStyle($column)->getFont()->getColor()->setARGB('FF0000');
			}
			$objPHPExcel->getActiveSheet()->setCellValue($column, $value['name']);
			$i++;
		}

		$filedKeys = array_column($fileds, 'key');
		$list = [];
		ob_start();
		foreach ($dataList as $key => $value) {
			if ($key % 5000 == 0) {
				ob_flush();
				flush();
			}
			$num = $key + 2;
			for ($j = 0; $j < $i; $j++) {
				$cloumStr = chr(ord("A") + $j);
				$column = $cloumStr . $num;
				if(isset($value[$filedKeys[$j]])) {
					$objPHPExcel->getActiveSheet()->setCellValue($column, $value[$filedKeys[$j]]);
				} else {
					$objPHPExcel->getActiveSheet()->setCellValue($column, '');
				}
			}
		}

		//设置必填字段字体颜色
		$objPHPExcel->getActiveSheet()->setTitle('Simple');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		if(php_sapi_name() != 'cli'){
			$fileName = basename($fileName);
			$fileName = iconv("utf-8", "gb2312", $fileName);
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename='.$fileName);
			header('Cache-Control: max-age=0');
			$objWriter->save('php://output'); //文件通过浏览器下载
		}else{

			$dirname = dirname($fileName);
			if ($dirname != '.') {
				//文件路径如果不存在则递归创健
				CommonFun::recursionMkDir($dirname);
			}
			$objWriter->save($fileName); //脚本方式运行，保存在指定目录
			if(!file_exists($fileName)) {
				return false;
			}
			return true;
		}
	}

	/**
	 * 对数据进行编码转换
	 * @param array/string $data 数组
	 * @param string $output 转换后的编码
	 * Created on 2018-6-12
	 */
	private function array_iconv($data, $output='utf-8') {
		$encode_arr = array('UTF-8','ASCII','GBK','GB2312','BIG5','JIS','eucjp-win','sjis-win','EUC-JP');
		if (!is_array($data)) {
			$encoded = mb_detect_encoding($data, $encode_arr);
			if($encoded!=$output){
				$data = mb_convert_encoding($data, $output, $encoded);
			}
		} else {
			foreach ($data as $key=>$val) {
				if(is_array($val)) {
					$data[$key] = self::array_iconv($val, $output);
				} else {
					$encoded = mb_detect_encoding($val, $encode_arr);
					if($encoded!=$output){
						$data[$key] = mb_convert_encoding($val, $output, $encoded);
					}
				}
			}

		}
		return $data;
	}
}