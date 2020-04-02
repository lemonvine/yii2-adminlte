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

/**
 * 导出到excel
 * shaodd 2019-12-11
 * 
 *
 */
class ExportExcel
{
	private $cells = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'];
	private $cell_format=['text'=>NumberFormat::FORMAT_TEXT];
	
	public $export_type = 1;
	/**
	 * 是否为自定义抬头
	 * @var string
	 */
	public $caption_diy = false;
	
	/**
	 * 抬头数组
	 * @var array
	 * 
	 * 
	 * Custome Caption example
	 * [
	 * 	['cell'=>'A1', 'merge'=>'A2', 'label'=>'name'],
	 * 	['cell'=>'B1', 'merge'=>'D1', 'label'=>'birthday'],
	 * 	['cell'=>'B2', 'merge'=>'', 'label'=>'year'],
	 * 	['cell'=>'C2', 'merge'=>'', 'label'=>'month'],
	 * 	['cell'=>'D2', 'merge'=>'', 'label'=>'day'],
	 * ]
			
	 */
	public $caption = [];
	public $columns = [];
	public $begin_row = 2;
	public $height = 20;
	
	/**
	 * 列宽度、列单元格格式、列相邻同值合并
	 * ['A'=>['width'=>15, 'format'=>'', 'merge'=>true], 'N'=>['width'=>40]]
	 * @var array
	 */
	public $style = [];
	public $merge = [];
	public $formatter;
	public $sheet_name = '数据';
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
		$this->initColumns();
	}
	
	public function out($data, $file_name, $content=''){
		$this->file_name = $file_name;
		$this->export_remark = $content;
		$row_number = count($data);
		$col_number = count($this->columns);
		Cell::setValueBinder(new ExcelValueBinder());
		$spreadsheet = new Spreadsheet();
		
		$spreadsheet->getProperties()->setCreator('Maarten Balliauw')
		->setLastModifiedBy('Maarten Balliauw')
		->setTitle('Office 2007 XLSX Test Document')
		->setSubject('Office 2007 XLSX Test Document')
		->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
		->setKeywords('office 2007 openxml php')
		->setCategory('Test result file');
		
		$sheet = $spreadsheet->setActiveSheetIndex(0);
		//$sheet->getDefaultRowDimension()->setRowHeight(20);
		//$sheet->getDefaultColumnDimension()->setWidth(12);
		
		if($this->caption_diy){
			foreach ($this->caption as $key=>$cell){
				$sheet->setCellValue($cell['cell'], $cell['label']);
				if(!empty($cell['merge'])){
					$sheet->mergeCells($cell['cell'].":".$cell['merge']);
					if(substr($cell['cell'],0,1)==substr($cell['merge'], 0, 1)){
						$sheet->getStyle($cell['cell'])->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
					}
				}
				if(!empty($cell['center'])){
					if($cell['merge']){
						$sheet->mergeCells($cell['cell'].":".$cell['merge']);
					}
					$sheet->getStyle($cell['cell'])->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
					$sheet->getStyle($cell['cell'])->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
				}
				//$sheet->getStyle($cell['cell'])->getFill()->setFillType(Fill::FILL_SOLID);
				//$sheet->getStyle($cell['cell'])->getFill()->getStartColor()->setARGB("AA4F81BD");
				//$sheet->getStyle($cell['cell'])->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
				//$sheet->getStyle($cell['cell'])->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			}
		}
		else{
			foreach ($this->columns as $k=>$item){
				$sheet->setCellValue($this->cells[$k].'1', $item['label']);
			}
			$row_title = 'A1:'.$this->cells[$col_number-1].'1';
			//$sheet->getStyle($row_title)->getFill()->setFillType(Fill::FILL_SOLID);
			//$sheet->getStyle($row_title)->getFill()->getStartColor()->setARGB("AA4F81BD");
			//$sheet->getStyle($row_title)->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
			//$sheet->getStyle($row_title)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);//水平方向上两端对齐
		}
		$i =$this->begin_row;
		for($m=1;$m<$i;$m++){
			$height = $this->titleHeight?$this->titleHeight:'25';
			$sheet->getRowDimension($m)->setRowHeight($height);
		}
		
		//数据入格
		foreach ($data as $key=>$row){
			$sheet->getRowDimension($i)->setRowHeight($this->height);
			foreach ($this->columns as $k=>$item){
				$val = $this->formatter->format($row[$item['attribute']], $item['format']);
				$r = $this->cells[$k];
				$sheet->setCellValue($r.$i, $val);
			}
			$i++;
		}
		
		foreach ($this->style as $key =>$col){
			$coldimen = $sheet->getColumnDimension($key);
			if(key_exists('width', $col)){
				$coldimen->setWidth($col['width']);
			}
			if(key_exists('format', $col)){
				$sheet->getStyle($key.$this->begin_row.':'.$key.$i)->getNumberFormat()->setFormatCode($this->cell_format[$col['format']]);
			}
			if(key_exists('center', $col)){
				$sheet->getStyle($key.$this->begin_row.':'.$key.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
				$sheet->getStyle($key.$this->begin_row.':'.$key.$i)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
			}
			if(key_exists('merge', $col)){//值相同的合并单元格
				$composer = '#b@n';
				$merge = 0;
				for($j=$this->begin_row; $j<=$i+1; $j++){
					$val = $sheet->getCell($key.$j)->getValue();
					if($composer!=$val){
						if($merge>0){
							$sheet->mergeCells($key.$merge.":".$key.($j-1));
							echo $key.$merge.":".$key.$j;
							$sheet->getStyle($key.$merge)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
						}
						$merge = $j;
						$composer = $val;
					}
				}
			}
		}
		$sheet->setTitle($this->sheet_name);
		
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
	
	protected function initColumns()
	{
		foreach ($this->columns as $i => $column) {
			if (is_string($column)) {
				$column = $this->createDataColumn($column);
			} else {
				$default_caption = $this->caption_diy?'':($this->caption[$column['attribute']]??'');
				$column = array_merge([
					'attribute' => '',
					'format' => 'text',
					'label' => $default_caption,
				], $column);
			}
			$this->columns[$i] = $column;
		}
	}
	protected function createDataColumn($text)
	{
		if (!preg_match('/^([^:]+)(:(\w*))?(:(.*))?$/', $text, $matches)) {
			throw new \Exception('The column must be specified in the format of "attribute", "attribute:format" or "attribute:format:label"');
		}
		return [
			'attribute' => $matches[1],
			'format' => isset($matches[3]) ? $matches[3] : 'text',
			'label' => $this->caption_diy?'':(isset($matches[5]) ? $matches[5] : (isset($this->caption[$matches[1]])?'':'')),
		];
	}
}

