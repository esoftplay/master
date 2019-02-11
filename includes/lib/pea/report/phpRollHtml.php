<?php

require_once dirname(dirname(__FILE__)).'/config.php';
include_once _PEA_ROOT.'report/phpReport.php';

class phpRollHtml extends phpReport
{
	public $extension = '.html';
  var $htmlTable;
	function __construct( $fileName='', $worksheetName='', $arrHeader=array(), $arrData = array() )
	{
		$tgl	= date('Y-m-d');

		if ( $fileName == '' )		$fileName = 'report'. $tgl . $extension;
		if ( $worksheetName == '' )	$worksheetName = 'HTML Report '. $tgl;

		$this->type          = 'html';
		$this->fileName      = $fileName;
		$this->worksheetName = $worksheetName;
		$this->arrHeader     = $arrHeader;
		$this->arrData       = $arrData;
		$this->setMaxColumnWidth();
	}

	function write()
	{
		$out	= '<thead>';

		// buat header
		if ( !empty( $this->arrHeader ) )
		{
			$out	.= '<tr>';
			foreach( $this->arrHeader as $header )
			{
			  $out	.= '<th>'.$header.'</th>';
			}
			$out	.= '</tr>';
		}
		$out .= '</thead>';
		$out .= '<tbody>';

		// buat data
		if ( !empty( $this->arrData ) )
		{
			foreach( $this->arrData as $dataRow )
			{
				$out	.= '<tr>';
				foreach( $dataRow as $data )
				{
					$data = str_replace('src="images/', 'src="'._URL.'images/', $data);
					$out	.= '<td>'.$data.'</td>';
				}
				$out	.= '</tr>';
			}
		}
		$out .= '</tbody>';
		$_URL = _URL;
		$_URI = _URI;
		$out = <<<EOT
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>{$this->worksheetName}</title>

		<!-- Bootstrap CSS -->
		<link href="{$_URL}templates/admin/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
		<script type="text/javascript">var _ROOT="{$_URI}";var _URL="{$_URL}";function _Bbc(a,b){var c="BS3load_func";if(!window[c+"i"]){window[c+"i"]=0};window[c+"i"]++;if(!b){b=c+"i"+window[c+"i"]};if(!window[c]){window[c]=b}else{window[c]+=","+b}window[b]=a;if(typeof BS3!="undefined"){window[b](BS3)}};</script>

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<table class="table table-striped table-bordered table-hover">
			{$out}
		</table>
		<!-- Bootstrap JavaScript -->
		<script src="{$_URL}templates/admin/bootstrap/js/bootstrap.min.js"></script>
	</body>
</html>
EOT;
		if (@strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
		{
			header('Content-Type: "text/html"');
			header('Content-Disposition: attachment; filename="'.$this->fileName.'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
			header("Content-Length: ".strlen($out));
		}
		else
		{
			header('Content-Type: "text/html"');
			header('Content-Disposition: attachment; filename="'.$this->fileName.'"');
			header("Content-Transfer-Encoding: binary");
			header('Expires: 0');
			header('Pragma: no-cache');
			header("Content-Length: ".strlen($out));
		}
		echo $out;
	}
}
/*
$arrHeader = array('Nama','Umur');
$arrData[] = array('ogy', 12);
$arrData[] = array('sigit', 54);
$arrData[] = array('ogi sigit pornawan testing excel report', 54);

$excel = new phpRollExcel( $fileName="report.xls", $worksheetName="report", $arrHeader, $arrData );
$excel->setMaxColumnWidth(60);
$excel->setHeaderColor('yellow', 'black');
$excel->write();
*/