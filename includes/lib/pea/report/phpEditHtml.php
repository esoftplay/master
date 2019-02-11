<?php

require_once dirname(dirname(__FILE__)).'/config.php';
include_once _PEA_ROOT.'report/phpReport.php';

class phpEditHtml extends phpReport
{
	var $fileName;
	var $worksheetName;
	var $arrData;
	var $maxColumnWidth; 	// maximum column width,
	var $headerColor;

    /**
    * Konstruktor: Inisialisasi
	* example:
	*   $arrData[] = array('Data Siswa');
	*   $arrData[] = array('Nama', 'Ogi Sigit Pornawan');
	*   $arrData[] = array('Umur', 54);
	*   $arrData[] = array('Tgl Lahir', '2002-09-23');
	*
	*   $excel = new phpEditHtml( $fileName="report.html", $worksheetName="report", $arrData );
	*
    * @access public
    * @param string	$fileName		Nama file html hasil generate
    * @param string	$worksheetName	Nama worksheet dari Html
	* @param array	$arrHeader		array header, yang nantinya jadi title di excelnya
    */
	function __construct( $fileName='htmlReport.html', $worksheetName='Html Report', $arrData = array() )
	{
		$tgl	= date("Y-m-d");

		if ( $fileName == '' )		$fileName = "htmlReport". $tgl .".html";
		if ( $worksheetName == '' )	$worksheetName = "Html Report ". $tgl;

		$this->type				= 'html';
		$this->fileName			= $fileName;
		$this->worksheetName	= $worksheetName;
		$this->arrData			= $arrData;
		$this->setMaxColumnWidth();
		$this->setHeaderColor();
	}

	function write()
	{
		// buat data
		$out = '
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">'.$this->worksheetName.'</h3>
			</div>
			<div class="panel-body">';
		if ( !empty( $this->arrData ) )
		{
			// pr($this->arrData, __FILE__.':'.__LINE__);die();
			foreach( $this->arrData as $dataRow )
			{
				// jika jumlah colom lebih dari satu, maka dianggap sebagai bukan header
				if ( count( $dataRow ) > 1 )
				{
					foreach( $dataRow as $i => $data )
					{
						$data = str_replace('src="images/', 'src="'._URL.'images/', $data);
						$out .= ($i%2) ? '<div class="form-control-static">'.$data.'</div></div>' : '<div class="form-group"><label>'.htmlentities($data).'</label>';
					}
				}else{
					foreach( $dataRow as $data )
					{
						$out	.= '<div class="form-group"><label>'.htmlentities($data).'</label><div class="form-control"></div></div>';
					}
				}
			}
		}
		$out .= '
			</div>
		</div>';
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
		{$out}
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
	} // eof write()
} // eof class phpEditHtml