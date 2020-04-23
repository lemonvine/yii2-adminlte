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
	public $labels = [];
	public $title = [];
	public $header = [];
	public $header_style=[];
	public $body = [];
	public $body_style=[];
	public $sheet_name = '数据';
	public $extra = [];
	
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
		'header' => [],
		'header_style'=>[],
		'body' => [],
		'body_style'=>[],
		'extra'=> [],
	];
	
	/**
	 * 单元格的所有属性
	 * @var array
	 */
	private $cell = [
		'attribute'=> '',
		'label'=> '',
		'column'=> '',
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
		'added'=> '',//merge
		'format'=> 'text',
	];
	/**
	 * excel样式MAP
	 * @var array
	 */
	private $cell_format=[
		'left'=> Alignment::HORIZONTAL_LEFT,
		'center'=> Alignment::HORIZONTAL_CENTER,
		'right'=> Alignment::HORIZONTAL_RIGHT,
		'mid'=> Alignment::VERTICAL_CENTER,
		'text'=> NumberFormat::FORMAT_TEXT,
		
	];
	
	private $formatter;
	private $sheet_index = 0;
	private $row_begin= 9999;
	private $row_num = 0;
	private $heighted_rows = []; //临时变量，title和header中自定义了高度的行
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
				}
				
				$this->buildSheet($sheet, $sheets[$j]);
			}
		}
		else{
			$sheet = [
				'name' => $this->sheet_name,
				'labels'=> $this->labels,
				'title'=> $this->title,
				'header'=> $this->header,
				'header_style'=> $this->header_style,
				'body'=> $this->body,
				'body_style'=> $this->body_style,
				'extra'=>$this->extra,
			];
			
			$sheet_0 = $spreadsheet->setActiveSheetIndex(0);
			$this->buildSheet($sheet_0, $sheet);
			
		}
		
		$this->writeLog();
		// Redirect output to a client’s web browser (Xlsx)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
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
		exit('保存成功');
	}
	
	/**
	 * 构建sheet
	 * @param unknown $sheet
	 * @param unknown $configs
	 */
	protected function buildSheet($sheet, $configs){
		//初始化
		$configs = array_merge($this->sheet, $configs);
		$this->row_num= 0;
		$this->row_begin=9999;
		$this->heighted_rows= [];
		
		//sheet name
		if(empty($configs['name'])){
			$configs['name'] = 'sheet'.($this->sheet_index+1);
		}
		$sheet->setTitle($configs['name']);
		
		$sheet->getDefaultRowDimension()->setRowHeight($this->row_height);
		$sheet->getDefaultColumnDimension()->setWidth($this->column_width);
		
		$configs = $this->initColumns($configs);
		$title = $configs['title'];
		
		foreach ($title as $item){
			$this->fillCell($sheet, $item, true);
		}
		
		$header = $configs['header'];
		$header_begin = $this->row_num +1;//******************
		
		foreach ($header as $key=>$item){
			if(empty($item['cell'])  && !empty($item['column'])){
				$item['cell'] = $item['column'].$header_begin;
			}
			$this->fillCell($sheet, $item, true);
		}
		//title和header高度
		for($i=$this->row_begin; $i<=$this->row_num; $i++){
			if(!in_array($i, $this->heighted_rows)){
				$sheet->getRowDimension($i)->setRowHeight($this->row_height);
			}
		}
		
		//数据入格
		$body = $configs['body'];
		$row_begin = $this->row_num+1;
		$formatter = $this->formatter;
		$body_height = $this->row_height;
		if(isset($configs['body_style']['height'])){
			$body_height = $configs['body_style']['height'];
			unset($configs['body_style']['height']);
		}
		$flag_row_height= true;
		foreach ($header as $c=>$column){
			if(empty($column['column'])){
				continue;
			}
			$attr = $column['attribute'];
			$format = $column['format']??'text';
			$added = $column['added'];
			$col_name = $column['column'];
			
			$i =$row_begin;
			$merge_dealer = '';
			$merge_begin = $row_begin;
			foreach ($body as $r=>$row){
				if($flag_row_height){
					$sheet->getRowDimension($i)->setRowHeight($body_height);
				}
				
				$original = $row[$attr];
				$value = $formatter->format($original, $format);
				if($added=='merge'){
					if($merge_dealer!=$value){
						if($i>($merge_begin+1)){
							$sheet->mergeCells($col_name.$merge_begin.":".$col_name.($i-1));
						}
						$merge_begin= $i;
						$merge_dealer= $value;
					}
				}
				$cell_item = $this->cell;
				$cell_item = array_merge($cell_item, $configs['body_style']);
				
				$cell_item['label'] = $value;
				$cell_item['cell'] = $column['column'].$i;
				
				$this->fillCell($sheet, $cell_item);
				$i++;
			}
			if($added=='merge'){
				if($i>($merge_begin+1)){
					$sheet->mergeCells($col_name.$merge_begin.":".$col_name.($i-1));
				}
			}
			$flag_row_height = false;
		}
		if(count($configs['extra'])>0){
			$extra = $configs['extra'];
			if(isset($extra['freeze'])){
				$sheet->freezePane($extra['freeze']);
			}
		}
		$this->sheet_index ++;
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
		$sheet->getStyle($cell)->getAlignment()->setWrapText(TRUE);
		
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
		if(!empty($item['format'])){
			$format = $item['format'];
			switch ($format){
				case 'money':
					$sheet->getStyle($cell)->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');
					break;
				default:
					break;
			}
		}
		
		if($move_row){
			if($row_num==0){
				$row_num= $sheet->getCell($cell)->getRow();
			}
			if($row_num> $this->row_num){
				$this->row_num = $row_num;
			}
			if($row_num<$this->row_begin){
				$this->row_begin = $row_num;
			}
			//自定义了高度的行，标记到全局数组中
			if(!empty($item['height']) && !in_array($row_num, $this->heighted_rows)){
				$this->heighted_rows[] = $row_num;
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
	 * 完善title和header
	 * @param unknown $configs
	 * @return unknown
	 */
	protected function initColumns($configs)
	{
		foreach ($configs['title'] as &$title){
			//var_dump($title);die;
			$title= array_merge($this->cell, $title);
			
		}
		$labels = $configs['labels'];
		foreach ($configs['header'] as $i=>&$item) {
			if (is_string($item)) {
				$item = $this->createDataColumn($item);
			}
			$header = array_merge($this->cell, $configs['header_style']);
			$item= array_merge($header, $item);
						
			$attr = $item['attribute'];
			//label
			if(!empty($attr) && empty($item['label']) && isset($labels[$attr])){
				$item['label'] = $labels[$attr];
			}
			//column
			if(!empty($attr) && empty($item['column'])){
				$cell = $item['cell']??'';
				if(!empty($cell)){
					$item['column']=preg_replace("/\\d+/",'', $cell);
				}
				else{
					$item['column'] = $this->columnName($i);
				}
			}
		}
		return  $configs;
	}
	
	/**
	 * 解析字符串的header
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

