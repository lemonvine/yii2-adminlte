<?php
namespace lemon\repository;

use Yii;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use lemon\components\ExcelValueBinder;
use lemon\models\LogExport;
use PhpOffice\PhpSpreadsheet\Style\Border;

/**
 * 导出到excel
 * shaodd 2019-12-11
 * 
 * 序号：attribute的值为#serial#
 * cell的值为####，表示该单元格不处理，跳过，主要针对head部分
 * 在foot中，如果要用到当前行的行号，在label中用#this#表示，如果要用到数据的最后一行行号，用#data#表示
 *
 */
class ExportExcel
{
	/**
	 * 多个sheet，传入此参数
	 * @var array
	 */
	public $sheets = [];
	
	/**
	 * 单个sheet，直接传入以下参数
	 * @var array
	 */
	public $labels= [];
	public $title= [];
	public $head= [];
	public $body= [];
	public $foot= [];
	public $style= [];
	public $extra = [];
	public $sheet_name= '数据';
	public $multiple= FALSE;
	
	/**
	 * 用于记录日志
	 * @var integer
	 */
	public $export_type = 1;
	
	/**
	 * 实例化此类时，传以下参数，如果有多个sheet，传入数组，每个数组元素包含以下参数
	 * @var array
	 */
	private $sheet = [
		'name' => '',
		'labels' => [],
		'title' => [],
		'head' => [],
		'body' => [],
		'foot' => [],
		'style'=>[],
		'extra'=> [],
		'multiple'=>FALSE,
	];
	
	/**
	 * 单元格的所有属性
	 * @var array
	 */
	private $cell = [
		'attribute'=> '',
		'label'=> '',
		'column'=> '',
		'column2'=> '',
		'cell'=> '',
		'merge'=> '',
		'align'=> 'center',
		'valign'=> 'mid',
		'width'=> 0,
		'height'=> 0,
		'fontsize'=> 0,
		'color'=> '',
		'background'=> '',
		'border'=>'',
		'bold'=>false,
		'wrap'=>false,
		'move'=>true,
		'added'=> '',//merge
		'datatype'=> '',
	];
	/**
	 * excel样式MAP
	 * @var array
	 */
	private $cell_format=[
		'left'=> Alignment::HORIZONTAL_LEFT,
		'center'=> Alignment::HORIZONTAL_CENTER,
		'right'=> Alignment::HORIZONTAL_RIGHT,
		'top'=> Alignment::VERTICAL_TOP,
		'mid'=> Alignment::VERTICAL_CENTER,
		'text'=> NumberFormat::FORMAT_TEXT,
		
	];
	
	private $formatter;
	private $sheet_index = 0;
	private $row_num = 0;
	private $row_height = 22; //标题部分的高度
	private $column_width =12; //默认列宽
	
	/**
	 * 用于记录日志
	 * @var unknown
	 */
	private $file_name;
	private $export_remark;
	
	public function __construct($config = [])
	{
		if (!empty($config)) {
			Yii::configure($this, $config);
		}
		$this->init();
	}
	public function init()
	{
		if ($this->formatter === null) {
			$this->formatter = Yii::$app->getFormatter();
		} elseif (is_array($this->formatter)) {
			$this->formatter = Yii::createObject($this->formatter);
		}
	}
	
	public function out($file_name, $content=''){
		$this->file_name = $file_name;
		$this->export_remark = $content;
		
		Cell::setValueBinder(new ExcelValueBinder());
		$spreadsheet = new Spreadsheet();
		
		$spreadsheet->getProperties()->setCreator('Maarten Balliauw')
		->setLastModifiedBy('Maarten Balliauw')
		->setTitle('Office 2007 XLSX Test Document')
		->setSubject('Office 2007 XLSX Test Document')
		->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
		->setKeywords('office 2007 openxml php')
		->setCategory('Test result file');
		
		$spreadsheet->getDefaultStyle()->getFont()->setName('黑体');//字体
		
		$sheets = $this->sheets;
		$sheet_count = count($sheets);
		if($sheet_count>0){
			$sheet_0 = $spreadsheet->setActiveSheetIndex(0);
			$this->buildSheet($sheet_0, $sheets[0]);
			$sheet_indexs= [];
			for($j=1;$j<$sheet_count; $j++){
				$sheet_name = $sheets[$j]['name'];
				if(in_array($sheet_name, $sheet_indexs)){
					$sheet = $spreadsheet->getSheetByName($sheet_name);
				}
				else{
					$sheet= $spreadsheet->createSheet();
					$sheet_indexs[] = $sheet_name;
					$this->sheet_index ++;
				}
				$this->buildSheet($sheet, $sheets[$j]);
			}
		}
		else{
			$sheet = [
				'name' => $this->sheet_name,
				'labels'=> $this->labels,
				'title'=> $this->title,
				'head'=> $this->head,
				'body'=> $this->body,
				'foot'=> $this->foot,
				'style'=> $this->style,
				'extra'=>$this->extra,
				'multiple'=>$this->multiple,
			];
			
			$sheet_0 = $spreadsheet->setActiveSheetIndex(0);
			$this->buildSheet($sheet_0, $sheet);
			
		}
		//$this->writeLog();
		// Redirect output to a client’s web browser (Xlsx)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.Utility::encodeChinese($file_name).'.xlsx"');
		header('Cache-Control: max-age=0');
		//header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0
		ob_clean(); //解决输出带bom头的问题
		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
		exit;
		//exit('保存成功');
	}
	
	/**
	 * 构建sheet
	 * @param unknown $sheet
	 * @param unknown $configs
	 */
	protected function buildSheet($sheet, $configs){
		//初始化
		$this->row_num = 0;
		$configs = array_merge($this->sheet, $configs);
		
		//sheet名称
		if(empty($configs['name'])){
			$configs['name'] = $this->sheet_name.($this->sheet_index);
		}
		$sheet->setTitle($configs['name']);
		
		//默认高度
		$sheet->getDefaultRowDimension()->setRowHeight($this->row_height);
		$sheet->getDefaultColumnDimension()->setWidth($this->column_width);
		
		/**********************************标题**********************************/
		$title = $configs['title'];
		foreach ($title as $item){
			$item= array_merge($this->cell, $item);
			$this->fillCell($sheet, $item, true);
		}
		
		$blocks=[];
		if($configs['multiple']){
			foreach ($configs['head'] as $index=>$block){
				$blocks[]=[
					'head'=>$configs['head'][$index],
					'body'=>$configs['body'][$index],
					'foot'=>$configs['foot'][$index]??[],
				];
			}
		}
		else{
			$blocks[]=[
				'head'=>$configs['head'],
				'body'=>$configs['body'],
				'foot'=>$configs['foot'],
			];
		}
		
		foreach ($blocks as $index=>$block){
			/**********************************表头**********************************/
			if($configs['multiple']){
				$this->row_num = 0;
			}
			$head = $block['head'];
			if(count($head)>0){
				$labels = $configs['labels'];
				$begin = $this->row_num +1;
				$default = $this->cell;
				if(isset($configs['style']['head']) && count($configs['style']['head'])>0){
					$default= array_merge($default, $configs['style']['head']);
				}
				foreach ($head as $key=>$item){
					//$begin = $this->row_num;
					if (is_string($item)) {
						$item = $this->createDataColumn($item);
					}
					$item= array_merge($default, $item);
					
					$attr = $item['attribute'];
					
					if(!empty($attr) && empty($item['label']) && isset($labels[$attr])){//label
						$item['label'] = $labels[$attr];
					}
					
					if(!empty($attr) && empty($item['column'])){//column
						$cell = $item['cell']??'';
						if(!empty($cell)){
							$item['column']=preg_replace("/\\d+/",'', $cell);
						}
						else{
							$item['column'] = $this->columnName($key);
						}
					}
					if(empty($item['cell'])  && !empty($item['column'])){
						$item['cell'] = $item['column'].$begin;
					}
					$head[$key]=$item;
					$item['datatype']='';
					if($item['cell']!='####'){
						$this->fillCell($sheet, $item, true);
					}
					
				}
			}
			
			/**********************************标题部分的空行**********************************/
			//title和head高度,未设置的高度的行，默认高度
			
			/**********************************数据**********************************/
			//数据入格
			$body = $block['body'];
			if(count($body)>0){
				$begin = $this->row_num+1;
				$formatter = $this->formatter;
				$body_height = $this->row_height;
				if(isset($configs['style']['body']['height'])){
					$body_height = $configs['style']['body']['height'];
					unset($configs['style']['body']['height']);
				}
				
				$default = $this->cell;
				if(isset($configs['style']['body']) && count($configs['style']['body'])>0){
					$default= array_merge($default, $configs['style']['body']);
				}
				$flag_row_height= true;
				foreach ($head as $c=>$column){
					if(empty($column['attribute'])){
						continue;
					}
					$attr = $column['attribute'];
					$format = $column['format']??'';
					$added = $column['added'];
					$col_name = $column['column'];
					$border = $column['border'];
					$merge = $column['column2'];
					$datatype = $column['datatype'];
					
					$i =$begin;
					$merge_dealer = '';
					$merge_begin = $begin;
					foreach ($body as $r=>$row){
						if($flag_row_height){
							$sheet->getRowDimension($i)->setRowHeight($body_height);
						}
						
						if($attr=='#serial#'){
							$value = $i-$begin+1;
						}
						else{
							if(empty($format)){
								$value = $row[$attr];
							}
							else{
								$value = $formatter->format($row[$attr], $format);
							}
							
							if($added=='merge'){
								if($merge_dealer!=$value){
									if($i>($merge_begin+1)){
										$sheet->mergeCells($col_name.$merge_begin.":".$col_name.($i-1));
									}
									$merge_begin= $i;
									$merge_dealer= $value;
								}
							}
						}
						
						$item = $default;
						$item['label'] = $value;
						$item['cell'] = $column['column'].$i;
						if(!empty($column['column2'])){
							$item['merge'] = $column['column2'].$i;
						}
						$item['border'] = $border;
						$item['datatype'] = $datatype;
						$this->fillCell($sheet, $item);
						$i++;
					}
					if($added=='merge'){
						if($i>($merge_begin+1)){
							$sheet->mergeCells($col_name.$merge_begin.":".$col_name.($i-1));
						}
					}
					$flag_row_height = false;
				}
			}
			
			/**********************************底部**********************************/
			$foot = $block['foot'];
			if(count($foot)>0){
				$this->row_num += count($body);
				$begin= $this->row_num+1;
				$default = $this->cell;
				if(isset($configs['style']['foot']) && count($configs['style']['foot'])>0){
					$default= array_merge($default, $configs['style']['foot']);
				}
				
				for($i=0; $i<count($foot);$i++){
					$row = $foot[$i];
					foreach ($row as $item){
						$item= array_merge($default, $item);
						if(empty($item['cell']) && !empty($item['column'])){
							$item['cell'] = $item['column'].($begin+$i);
							if(!empty($item['merge'])){
								$item['merge'] = $item['merge'].($begin+$i);
							}
						}
						$item['label']= str_replace('#this#', $begin+$i, $item['label']);
						$item['label']= str_replace('#data#', $begin-1, $item['label']);
						$this->fillCell($sheet, $item);
					}
				}
			}
		}
		
		
		
		/**********************************特殊处理**********************************/
		if(count($configs['extra'])>0){
			$extra = $configs['extra'];
			if(isset($extra['freeze'])){
				$sheet->freezePane($extra['freeze']);
			}
		}
	}
	
	/**
	 * 填充单元格
	 * @param unknown $sheet
	 * @param unknown $item
	 * @param string $move_row
	 * @throws Exception
	 */
	private function fillCell($sheet, $item, $move_row=false){
		$cell = $item['cell'];
		if(empty($cell)){
			return;
		}
		$label = $item['label']??'';
		$merge = $item['merge']??'';
		try{
			$sheet->setCellValue($cell, $label);
		}catch(\Exception $ex){
			throw  $ex;
		}
		
		if(!empty($merge)){
			$sheet->mergeCells($cell.":".$merge);
		}
		
		$sheet->getStyle($cell)->getAlignment()->setHorizontal($this->cell_format[$item['align']]);
		$sheet->getStyle($cell)->getAlignment()->setVertical($this->cell_format[$item['valign']]);
		
		if(!empty($item['color'])){
			$sheet->getStyle($cell)->getFont()->getColor()->setARGB($item['color']);
		}
		if(!empty($item['background'])){
			$sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID);
			$sheet->getStyle($cell)->getFill()->getStartColor()->setARGB($item['background']);//AA4F81BD
		}
		
		$row_num = 0;
		$col_num = 0;
		if(!empty($item['height'])){
			$row_num= $sheet->getCell($cell)->getRow();
			$sheet->getRowDimension($row_num)->setRowHeight($item['height']);
		}
		if(!empty($item['width'])){
			$col_num= $sheet->getCell($cell)->getColumn();
			$sheet->getColumnDimension($col_num)->setWidth($item['width']);
		}
		if(!empty($item['fontsize'])){
			$sheet->getStyle($cell)->getFont()->setSize($item['fontsize']);
		}
		if(!empty($item['border'])){
			$color = new Color();
			$color->setRGB($item['border']);
			if(!empty($merge)){
				$sheet->getStyle($cell.":".$merge)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor($color);
			}
			else{
				$sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor($color);
			}
			
		}
		if($item['bold']){
			if(!empty($merge)){
				$sheet->getStyle($cell.":".$merge)->getFont()->setBold(true);
			}
			else{
				$sheet->getStyle($cell)->getFont()->setBold(true);
			}
		}
		if($item['wrap']){
			if(!empty($merge)){
				$sheet->getStyle($cell.":".$merge)->getAlignment()->setWrapText(true);
			}
			else{
				$sheet->getStyle($cell)->getAlignment()->setWrapText(true);
			}
		}
		if(!empty($item['datatype'])){
			$format = $item['datatype'];
			switch ($format){
				case 'money':
					$sheet->getStyle($cell)->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
					break;
				default:
					break;
			}
		}
		
		if($move_row && $item['move']){
			if($row_num==0){
				$row_num= $sheet->getCell($cell)->getRow();
			}
			if($row_num> $this->row_num){
				$this->row_num = $row_num;
			}
		}
	}
	
	/**
	 * 获得excel列名
	 * @param unknown $i
	 * @return string
	 */
	protected function columnName($i){
		$first = floor($i/26);
		$second = $i%26;
		return ($first==0?'':chr($first+64)).(chr($second+65));
	}
	
	/**
	 * 解析字符串的head
	 * @param unknown $text
	 * @throws \Exception
	 * @return unknown[]|string[]
	 */
	protected function createDataColumn($text)
	{
		if (!preg_match('/^([^:]+)(:(\w*))?(:(.*))?$/', $text, $matches)) {
			throw new \Exception('The column must be specified in the format of "attribute", "attribute:format" or "attribute:format:label"');
		}
		return [
			'attribute' => $matches[1],
			'format' => isset($matches[3]) ? $matches[3] : 'text',
			'label' => isset($matches[5]) ? $matches[5] : '',
		];
	}
	
	protected function writeLog(){
		$export_user = Yii::$app->user->identity->id;
		$model = new LogExport();
		$model->export_file = $this->file_name;
		$model->export_type = $this->export_type;
		$model->export_time = time();
		$model->export_user = $export_user;
		$model->export_remark = $this->export_remark;
		//$model->operate_ip = Helpers::
		if(!$model->save()){
			$error = array_values($model->getFirstErrors())[0];
			throw new \Exception($error . '[D01]');
		}
	}
}

