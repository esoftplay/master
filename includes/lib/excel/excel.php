<?php if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

if (!defined('EXCEL_ROOT'))
	define('EXCEL_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);

require_once EXCEL_ROOT.'lib'.DIRECTORY_SEPARATOR.'PHPExcel.php';
class excel_worksheet extends PHPExcel
{
	private $Obj;
	private $SheetNumber;
	private $allArray;
	function __construct($Obj, $SheetNumber)
	{
		$this->Obj         = $Obj;
		$this->SheetNumber = $SheetNumber;
		$this->allArray    = $this->Obj->getSheet($this->SheetNumber)->toArray(null,true,true,true);
	}
	function fetch()
	{
		return $this->allArray;
	}
	public function row($RowNumber)
	{
		if (isset($this->allArray[$RowNumber])) {
			return $this->allArray[$RowNumber];
		}else{
			return array();
		}
	}
}
class excel_workbook extends excel_worksheet
{
	private $Obj;
	function __construct($FilePath)
	{
		try {
			$this->Obj = PHPExcel_IOFactory::load($FilePath);
		} catch(Exception $e) {
			die('Error loading file "'.pathinfo($FilePath,PATHINFO_BASENAME).'": '.$e->getMessage());
		}
	}
	public function sheet($NumberOfSheet = 0)
	{
		return new excel_worksheet($this->Obj, --$NumberOfSheet);
	}
	public function fetch()
	{
		$output = array();
		$sheets = $this->Obj->getSheetNames();
		$datas  = $this->Obj->getAllSheets();
		foreach ($datas as $i => $data) {
			$output[$sheets[$i]] = $data->toArray(null,true,true,true);
		}
		return $output;
	}
}
class excel extends excel_workbook
{
	private $doc = null;
	function __construct() {}
	function read($FilePath)
	{
		return new excel_workbook($FilePath);
	}
	function create($data)
	{
		if (!empty($data) && is_array($data))
		{
			global $_CONFIG;
			$this->doc = null;
			$PHPExcel  = new PHPExcel();
			$PHPExcel->getProperties()->setCreator(@$_CONFIG['site']['url']);
			$PHPExcel->getProperties()->setLastModifiedBy(@$_CONFIG['site']['url']);
			$PHPExcel->getProperties()->setTitle(@$_CONFIG['site']['title']);
			$PHPExcel->getProperties()->setSubject(@$_CONFIG['email']['name']);
			$PHPExcel->getProperties()->setDescription(@$_CONFIG['site']['desc']);
			$i = 0;
			$t = count($data);
			foreach ($data as $title => $sheet)
			{
				$PHPExcel->setActiveSheetIndex($i);
				$PHPExcel->getActiveSheet()->setTitle($title);
				$y = 0;
				foreach ((array)$sheet as $row)
				{
					$y+= 1;
					$x = 0;
					foreach ((array)$row as $column_value)
					{
						$PHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y, $column_value);
						$x+=1;
					}
				}
				$i++;
				if($i < $t)
					$PHPExcel->createSheet();
			}
			$PHPExcel->setActiveSheetIndex(0);
			$this->doc = $PHPExcel;
			return $this;
		}else{
			die('No data to export');
		}
		return $this;
	}
	function download($filename='')
	{
		if (empty($filename)) {
			$filename = 'Excel-'.date('Y-m-d').'.xlsx';
		}
		$is_2007 = preg_match('~\.xlsx$~is', $filename);
		// Redirect output to a client’s web browser
		if ($is_2007) {
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		}else{
			header('Content-Type: application/vnd.ms-excel');
		}
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		if ($is_2007) {
			$objWriter = PHPExcel_IOFactory::createWriter($this->doc, 'Excel2007');
		}else{
			$objWriter = PHPExcel_IOFactory::createWriter($this->doc, 'Excel5');
		}
		$objWriter->save('php://output');
	}
	function save($filename='')
	{
		if (empty($filename)) {
			$filename = _ROOT.'images/cache/Excel-'.date('Y-m-d-H-i-s').'.xlsx';
		}
		$is_2007 = preg_match('~\.xlsx$~is', $filename);
		if ($is_2007) {
			$objWriter = PHPExcel_IOFactory::createWriter($this->doc, 'Excel2007');
		}else{
			$objWriter = PHPExcel_IOFactory::createWriter($this->doc, 'Excel5');
		}
		$path = dirname($filename);
		if (!file_exists($path)) {
			_func('path','create',$path);
		}
		$objWriter->save($filename);
	}
}

/*
///////////////////////
// CREATE FILES
///////////////////////
$data = array(
	'users' => array(
		array('No','First Name','Last Name'),
		array('1','Danang','Widiantoro'),
		array('2','Malaquina','Widiantoro')
		),
	'other' => array(
		array('No','firstname','lastname','status'),
		array('1','Malaquina Aurelia','Widiantoro','Daughter'),
		array('2','Umi','Wafifah','Mommy'),
		array('3','Ichsaniar Bakti','Putra','Pakde')
		)
	);

_lib('excel')->create($data)->download('family.xlsx');									// .xls || .xlsx
_lib('excel')->create($data)->save(_ROOT.'images/cache/family.xlsx');		// .xls || .xlsx
			## OR ##
$obj = _lib('excel');
$obj->create($data);
$obj->download('family.xlsx');														// .xls || .xlsx
$obj->save(_ROOT.'images/cache/family.xlsx');							// .xls || .xlsx

///////////////////////
// READING FILE
///////////////////////
$output = _lib('excel')->read(_ROOT.'images/cache/family.xlsx')->sheet(2)->fetch();
$output =
Array
(
    [1] => Array
        (
            [A] => No
            [B] => firstname
            [C] => lastname
            [D] => status
        )
    [2] => Array
        (
            [A] => 1
            [B] => Malaquina Aurelia
            [C] => Widiantoro
            [D] => Daughter
        )
    [3] => Array
        (
            [A] => 2
            [B] => Umi
            [C] => Wafifah
            [D] => Mommy
        )
    [4] => Array
        (
            [A] => 3
            [B] => Ichsaniar Bakti
            [C] => Putra
            [D] => Pakde
        )
)

*/