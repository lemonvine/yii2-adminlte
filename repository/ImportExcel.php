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
				//$val = $sheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
				$val = $sheet->getCellByColumnAndRow($currentColumn, $currentRow)->getFormattedValue();//如果是公式，直接取值不取公式
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
	
	* 使用PHPEXECL导入
	*
	* @paramstring$file文件地址
	* @paramint$sheet工作表sheet(传0则获取第一个sheet)
	* @paramint$columnCnt列数(传0则自动获取最大列)
	* @paramarray$options操作选项
	* arraymergeCells合并单元格数组
	* arrayformula公式数组
	* arrayformat单元格格式数组
	*
	* @returnarray
	* @throwsException
	*/
	
	public function importExecl(string $file='',int $sheet=0,int $columnCnt=0, &$options=[])
	{
		try{
			/*转码*/
			$file=iconv("utf-8","gb2312",$file);
			if(empty($file)OR!file_exists($file)){
				throw new \Exception('文件不存在!');
			}
			
			/**@varXlsx$objRead*/
			$objRead=IOFactory::createReader('Xlsx');
			if(!$objRead->canRead($file)){
				/**@varXls$objRead*/
				$objRead=IOFactory::createReader('Xls');
				if(!$objRead->canRead($file)){
					throw new \Exception('只支持导入Excel文件！');
				}
			}
			
			/*如果不需要获取特殊操作，则只读内容，可以大幅度提升读取Excel效率*/
			empty($options)&&$objRead->setReadDataOnly(true);
			
			/*建立excel对象*/
			$obj=$objRead->load($file);
			/*获取指定的sheet表*/
			
			$currSheet=$obj->getSheet($sheet);
			if(isset($options['mergeCells'])){
				/*读取合并行列*/
				$options['mergeCells']=$currSheet->getMergeCells();
			}
			
			if(0==$columnCnt){
				/*取得最大的列号*/
				$columnH=$currSheet->getHighestColumn();
				
				/*兼容原逻辑，循环时使用的是小于等于*/
				$columnCnt=Coordinate::columnIndexFromString($columnH);
			}
			
			/*获取总行数*/
			
			$rowCnt=$currSheet->getHighestRow();
			$data=[];
			
			/*读取内容*/
			for($_row=1;$_row<=$rowCnt;$_row++){
				$isNull=true;
				for($_column=1;$_column<=$columnCnt;$_column++){
					$cellName=Coordinate::stringFromColumnIndex($_column);
					$cellId=$cellName.$_row;
					$cell=$currSheet->getCell($cellId);
					if(isset($options['format'])){
						/*获取格式*/
						$format=$cell->getStyle()->getNumberFormat()->getFormatCode();
						
						/*记录格式*/
						$options['format'][$_row][$cellName]=$format;
					}
					
					if(isset($options['formula'])){
						/*获取公式，公式均为=号开头数据*/
						$formula=$currSheet->getCell($cellId)->getValue();
						if(0===strpos($formula,'=')){
							$options['formula'][$cellName.$_row]=$formula;
						}
						
					}
					
					if(isset($format)&&'m/d/yyyy'==$format){
						/*日期格式翻转处理*/
						$cell->getStyle()->getNumberFormat()->setFormatCode('yyyy/mm/dd');
					}
					
					$data[$_row][$cellName]=trim($currSheet->getCell($cellId)->getFormattedValue());
					if(!empty($data[$_row][$cellName])){
						$isNull=false;
					}
					
				}
				
				/*判断是否整行数据为空，是的话删除该行数据*/
				if($isNull){
					unset($data[$_row]);
				}
			}
			
			return $data;
			
		}catch(\Exception$e){
			throw $e;
		}
	}
	/**
	
	* Excel导出，TODO 可继续优化  网上看到的，应该有用
	
	*
	
	* @param array $datas   导出数据，格式['A1' => 'XXXX公司报表', 'B1' => '序号']
	
	* @param string $fileName 导出文件名称
	
	* @param array $options  操作选项，例如：
	
	*             bool print   设置打印格式
	
	*             string freezePane 锁定行数，例如表头为第一行，则锁定表头输入A2
	
	*             array setARGB  设置背景色，例如['A1', 'C1']
	
	*             array setWidth  设置宽度，例如['A' => 30, 'C' => 20]
	
	*             bool setBorder 设置单元格边框
	
	*             array mergeCells 设置合并单元格，例如['A1:J1' => 'A1:J1']
	
	*             array formula  设置公式，例如['F2' => '=IF(D2>0,E42/D2,0)']
	
	*             array format   设置格式，整列设置，例如['A' => 'General']
	
	*             array alignCenter 设置居中样式，例如['A1', 'A2']
	
	*             array bold    设置加粗样式，例如['A1', 'A2']
	
	*             string savePath  保存路径，设置后则文件保存到服务器，不通过浏览器下载
	
	*/
	
	function exportExcel(array $datas, string $fileName = '', array $options = []): bool
	
	{
		
		try {
			
			if (empty($datas)) {
				
				return false;
				
			}
			
			set_time_limit(0);
			
			/** @var Spreadsheet $objSpreadsheet */
			
			$objSpreadsheet = app(Spreadsheet::class);
			
			/* 设置默认文字居左，上下居中 */
			
			$styleArray = [
				
				'alignment' => [
					
					'horizontal' => Alignment::HORIZONTAL_LEFT,
					
					'vertical' => Alignment::VERTICAL_CENTER,
					
				],
				
			];
			
			$objSpreadsheet->getDefaultStyle()->applyFromArray($styleArray);
			
			/* 设置Excel Sheet */
			
			$activeSheet = $objSpreadsheet->setActiveSheetIndex(0);
			
			/* 打印设置 */
			
			if (isset($options['print']) && $options['print']) {
				
				/* 设置打印为A4效果 */
				
				$activeSheet->getPageSetup()->setPaperSize(PageSetup:: PAPERSIZE_A4);
				
				/* 设置打印时边距 */
				
				$pValue = 1 / 2.54;
				
				$activeSheet->getPageMargins()->setTop($pValue / 2);
				
				$activeSheet->getPageMargins()->setBottom($pValue * 2);
				
				$activeSheet->getPageMargins()->setLeft($pValue / 2);
				
				$activeSheet->getPageMargins()->setRight($pValue / 2);
				
			}
			
			/* 行数据处理 */
			
			foreach ($datas as $sKey => $sItem) {
				
				/* 默认文本格式 */
				
				$pDataType = DataType::TYPE_STRING;
				
				/* 设置单元格格式 */
				
				if (isset($options['format']) && !empty($options['format'])) {
					
					$colRow = Coordinate::coordinateFromString($sKey);
					
					/* 存在该列格式并且有特殊格式 */
					
					if (isset($options['format'][$colRow[0]]) &&
							
							NumberFormat::FORMAT_GENERAL != $options['format'][$colRow[0]]) {
								
								$activeSheet->getStyle($sKey)->getNumberFormat()
								
								->setFormatCode($options['format'][$colRow[0]]);
								
								if (false !== strpos($options['format'][$colRow[0]], '0.00') &&
										
										is_numeric(str_replace(['￥', ','], '', $sItem))) {
											
											/* 数字格式转换为数字单元格 */
											
											$pDataType = DataType::TYPE_NUMERIC;
											
											$sItem  = str_replace(['￥', ','], '', $sItem);
											
										}
										
							} elseif (is_int($sItem)) {
								
								$pDataType = DataType::TYPE_NUMERIC;
								
							}
							
				}
				
				$activeSheet->setCellValueExplicit($sKey, $sItem, $pDataType);
				
				/* 存在:形式的合并行列，列入A1:B2，则对应合并 */
				
				if (false !== strstr($sKey, ":")) {
					
					$options['mergeCells'][$sKey] = $sKey;
					
				}
				
			}
			
			unset($datas);
			
			/* 设置锁定行 */
			
			if (isset($options['freezePane']) && !empty($options['freezePane'])) {
				
				$activeSheet->freezePane($options['freezePane']);
				
				unset($options['freezePane']);
				
			}
			
			/* 设置宽度 */
			
			if (isset($options['setWidth']) && !empty($options['setWidth'])) {
				
				foreach ($options['setWidth'] as $swKey => $swItem) {
					
					$activeSheet->getColumnDimension($swKey)->setWidth($swItem);
					
				}
				
				unset($options['setWidth']);
				
			}
			
			/* 设置背景色 */
			
			if (isset($options['setARGB']) && !empty($options['setARGB'])) {
				
				foreach ($options['setARGB'] as $sItem) {
					
					$activeSheet->getStyle($sItem)
					
					->getFill()->setFillType(Fill::FILL_SOLID)
					
					->getStartColor()->setARGB(Color::COLOR_YELLOW);
					
				}
				
				unset($options['setARGB']);
				
			}
			
			/* 设置公式 */
			
			if (isset($options['formula']) && !empty($options['formula'])) {
				
				foreach ($options['formula'] as $fKey => $fItem) {
					
					$activeSheet->setCellValue($fKey, $fItem);
					
				}
				
				unset($options['formula']);
				
			}
			
			/* 合并行列处理 */
			
			if (isset($options['mergeCells']) && !empty($options['mergeCells'])) {
				
				$activeSheet->setMergeCells($options['mergeCells']);
				
				unset($options['mergeCells']);
				
			}
			
			/* 设置居中 */
			
			if (isset($options['alignCenter']) && !empty($options['alignCenter'])) {
				
				$styleArray = [
					
					'alignment' => [
						
						'horizontal' => Alignment::HORIZONTAL_CENTER,
						
						'vertical' => Alignment::VERTICAL_CENTER,
						
					],
					
				];
				
				foreach ($options['alignCenter'] as $acItem) {
					
					$activeSheet->getStyle($acItem)->applyFromArray($styleArray);
					
				}
				
				unset($options['alignCenter']);
				
			}
			
			/* 设置加粗 */
			
			if (isset($options['bold']) && !empty($options['bold'])) {
				
				foreach ($options['bold'] as $bItem) {
					
					$activeSheet->getStyle($bItem)->getFont()->setBold(true);
					
				}
				
				unset($options['bold']);
				
			}
			
			/* 设置单元格边框，整个表格设置即可，必须在数据填充后才可以获取到最大行列 */
			
			if (isset($options['setBorder']) && $options['setBorder']) {
				
				$border  = [
					
					'borders' => [
						
						'allBorders' => [
							
							'borderStyle' => Border::BORDER_THIN, // 设置border样式
							
							'color'   => ['argb' => 'FF000000'], // 设置border颜色
							
						],
						
					],
					
				];
				
				$setBorder = 'A1:' . $activeSheet->getHighestColumn() . $activeSheet->getHighestRow();
				
				$activeSheet->getStyle($setBorder)->applyFromArray($border);
				
				unset($options['setBorder']);
				
			}
			
			$fileName = !empty($fileName) ? $fileName : (date('YmdHis') . '.xlsx');
			
			if (!isset($options['savePath'])) {
				
				/* 直接导出Excel，无需保存到本地，输出07Excel文件 */
				
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				
				header(
						
						"Content-Disposition:attachment;filename=" . iconv(
								
								"utf-8", "GB2312//TRANSLIT", $fileName
								
								)
						
						);
				
				header('Cache-Control: max-age=0');//禁止缓存
				
				$savePath = 'php://output';
				
			} else {
				
				$savePath = $options['savePath'];
				
			}
			
			ob_clean();
			
			ob_start();
			
			$objWriter = IOFactory::createWriter($objSpreadsheet, 'Xlsx');
			
			$objWriter->save($savePath);
			
			/* 释放内存 */
			
			$objSpreadsheet->disconnectWorksheets();
			
			unset($objSpreadsheet);
			
			ob_end_flush();
			
			return true;
			
		} catch (Exception $e) {
			
			return false;
			
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