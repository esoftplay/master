<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

/**
//* Example How to read CSV file:
$arr = _class('csv', '/path/of/file.csv')->fetch();

//* Example How to write CSV file:
$data = array(
	array('No','firstname','lastname','status'),
	array('1','Danang','Widiantoro','Me'),
	array('2','Malaquina Aurelia','Widiantoro','Daughter'),
	array('3','Umi','Wafifah','Mommy'),
	array('4','Ichsaniar Bakti','Putra','Pakde')
	);
_class('csv', $data)->save('/path/of/new/file.csv');

//* Example How to Download Array to csv file:
_class('csv', $data)->download('downloaded_name.csv');
*/
class csv
{
	private $rows;
	private $colom;
	private $delimiter;
	function __construct($filepath_or_array = '')
	{
		$this->clear();
		$this->setDelimiter(',');
		if (!empty($filepath_or_array))
		{
			if (is_array($filepath_or_array))
			{
				$this->addData($filepath_or_array);
			}else{
				if (file_exists($filepath_or_array))
				{
					$this->read($filepath_or_array);
				}
			}
		}
	}
	function setDelimiter($char)
	{
		$this->delimiter = $char;
	}
	/*
	SAMPLE:
	$data = array(
		array('No','firstname','lastname','status'),
		array('1','Danang','Widiantoro','Me'),
		array('2','Malaquina Aurelia','Widiantoro','Daughter'),
		array('3','Umi','Wafifah','Mommy'),
		array('4','Ichsaniar Bakti','Putra','Pakde')
		);
	*/
	function addData($data)
	{
		$output = false;
		if (!empty($data) && is_array($data))
		{
			foreach ($data as $row)
			{
				$ok = $this->addRow($row);
				if (!$output && $ok)
				{
					$output = $ok;
				}
			}
		}
		return $output;
	}
	function setData($data)
	{
		$output = false;
		if (!empty($data) && is_array($data))
		{
			$this->clear();
			$output = $this->addData($data);
		}
		return $output;
	}
	/*
	SAMPLE:
	$row = array('1','Danang','Widiantoro','Me');
	*/
	function addRow($row)
	{
		$output = false;
		if (!empty($row) && is_array($row))
		{
			$cols = count($row);
			$rows = array();
			if (empty($this->colom))
			{
				$this->colom = $cols;
			}
			if ($cols > $this->colom)
			{
				$rows = array_slice($row, 0, $this->colom);
			}else
			if ($cols < $this->colom)
			{
				$rows = array_pad($row, $this->colom, '');
			}else{
				$rows = array_values($row);
			}
			if (!empty($rows))
			{
				$this->rows[] = $rows;
				$output       = true;
			}
		}
		return $output;
	}
	function fetch()
	{
		return $this->rows;
	}
	function read($filePath, $mode = 'r')
	{
		$output = false;
		if (($handle = fopen($filePath, $mode)) !== FALSE)
		{
			while (($row = fgetcsv($handle, 1000, $this->delimiter)) !== FALSE)
			{
				$ok = $this->addRow($row);
				if (!$output && $ok)
				{
					$output = $ok;
				}
			}
			fclose($handle);
		}
		return $output;
	}
	function save($filePath, $mode = 'w')
	{
		// pr($this->rows, __FILE__.':'.__LINE__);
		if (($fp = fopen($filePath, $mode)) !== FALSE)
		{
			foreach ($this->rows as $row)
			{
				// pr(array_values($row), __FILE__.':'.__LINE__);
				fputcsv($fp, $row, $this->delimiter);
			}
			fclose($fp);
		}
	}
	function download($fileName, $is_exit = true)
	{
		$tmpFile = _CACHE.'csv'.time().'.cfg';
		$this->save($tmpFile);
		_func('download', 'file', $fileName, $tmpFile, false);
		@unlink($tmpFile);
		if ($is_exit)
		{
			die();
		}
	}
	function clear()
	{
		$this->rows  = array();
		$this->colom = 0;
	}
}