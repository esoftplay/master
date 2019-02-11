<?php if (!defined('_VALID_BBC')) define('_VALID_BBC', '1');

require_once dirname(dirname(__FILE__)).'/config.php';
require_once _ROOT.'includes/lib/excel/excel.php';
include_once __DIR__.'/phpReport.php';

class phpRollExcel extends phpReport
{
	public $extension = '.xls';
	function __construct( $fileName='', $worksheetName='', $arrHeader=array(), $arrData = array() )
	{
		$tgl	= date('Y-m-d');

		if ( $fileName == '' )		$fileName = 'excelReport'. $tgl . $extension;
		if ( $worksheetName == '' )	$worksheetName = 'sheet ';

		$this->type          = 'excel';
		$this->fileName      = $fileName;
		$this->worksheetName = $worksheetName;
		$this->arrHeader     = $arrHeader;
		$this->arrData       = $arrData;
		// $this->setMaxColumnWidth();
		// $this->setHeaderColor();
	}

	function write()
	{
		$data   = array();
		$sheets = array_chunk($this->arrData, 64999);
		foreach ($sheets as $i => $sheet)
		{
			$data[$this->worksheetName.($i+1)] = array_merge(array($this->arrHeader), $sheet);
		}
		$excel = new excel();
		$excel->create($data)->download($this->fileName);
	}
}
