<?php
$formName		= $_GET['formName'];
$formType		= $_GET['formType'];
$reportType	= $_GET['reportType'];

$formClass	= 'php'. ucfirst(strtolower($formType)) . ucfirst(strtolower($reportType)) . '.php';
if (!preg_match('~/~is', $formClass))
{
	include_once( $formClass );
	$formFile = '';
	if (!empty($_GET['name']))
	{
		if (!defined('_CACHE'))
		{
			define('_CACHE', _ROOT.'images/cache/');
		}
		ini_set('display_errors', 0);
		ini_set('memory_limit', -1);
		set_time_limit(0);
		$name     = str_replace('/', '', $_GET['name']); // pengamanan agar tidak membaca file diluar cache
		$formFile = _CACHE.implode('/', str_split($name, 2)).'.cfg';
		try {
			$json = phpReportRead($formFile);
		} catch (Exception $e) {
			phpReportDownloadCSV($formFile);
		}
	}else{
		$json = $_GET;
	}
	$class = 'php'.$formType.$reportType;
	$obj   = new $class();
	foreach($json AS $var => $val)
	{
		$obj->$var = $val;
	}
	try {
		$obj->write();
	} catch (Exception $e) {
		phpReportDownloadCSV($formFile);
	}
}

function phpReportRead($file = '', $method = 'r')
{
	global $reportType;
	$name = !empty($_GET['title']) ? $_GET['title'] : 'Report';
	switch ($reportType)
	{
		case 'excel':
			$name .= '.xls';
			break;
		case 'html':
			$name .= '.html';
			break;
		case 'pdf':
			$name .= '.pdf';
			break;
	}
	$output = array(
		'fileName'      => $name,
		'worksheetName' => 'sheet ',
		'arrHeader'     => array(),
		'arrData'       => array(),
		'headerColor'   => array(
			'bg'   => 'gray',
			'font' => 'black'
			),
		'maxColumnWidth' => 120,
		'type'           => $reportType
		);
	if (($fp = fopen($file, 'r')) !== FALSE)
	{
		$i = 0;
		while (($row = fgetcsv($fp, 1000, ',')) !== FALSE)
		{
			if (empty($i))
			{
				$i++;
				$output['arrHeader'] = $row;
			}else{
				$output['arrData'][] = $row;
			}
		}
		fclose($fp);
	}
	return $output;
}
function phpReportDownloadCSV($file)
{
	if (!empty($file) && file_exists($file))
	{
		$name = !empty($_GET['title']) ? $_GET['title'] : 'Report';
		$mime = 'text/x-comma-separated-values';
		$data = file_get_contents($file);
		if (@strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
		{
			header('Content-Type: "'.$mime.'"');
			header('Content-Disposition: attachment; filename="'.$name.'.csv"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
			header("Content-Length: ".strlen($data));
		}else{
			header('Content-Type: "'.$mime.'"');
			header('Content-Disposition: attachment; filename="'.$name.'.csv"');
			header("Content-Transfer-Encoding: binary");
			header('Expires: 0');
			header('Pragma: no-cache');
			header("Content-Length: ".strlen($data));
		}
		exit($data);
	}
}