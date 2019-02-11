<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

/**
* Class Parent of all report type in phpEasyAdmin
* its class is private, you cannot initialize this class
*
* @author Ogi Sigit Pornawan <ogi.sigit.p@idwebhost.com>
* @package report
*/
class phpEditReport
{
	var $fileName;
	var $worksheetName;
	var $arrData;
	var $maxColumnWidth; 	// maximum column width,
	var $headerColor;

    /**
    * Konstruktor: Inisialisasi
	* Blank method
    */
	function __construct()
	{
	}

	function setFileName( $fileName )
	{
		$this->fileName = $fileName;
	}

	function setWorksheetName( $worksheetName )
	{
		$this->worksheetName = $worksheetName;
	}

	function setData( $arrData )
	{
		$this->arrData = $arrData;
	}

	function setMaxColumnWidth( $maxColumnWidth = 90 )
	{
		$this->maxColumnWidth = $maxColumnWidth;
	}

	function setHeaderColor( $bgcolor = "gray", $fontcolor ="black")
	{
		$this->headerColor['bg']	= $bgcolor;
		$this->headerColor['font'] 	= $fontcolor;
	}

	function headeringExcel( $filename )
	{
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=$filename" );
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");
	}

	function getColumnWidth()
	{
		// cari lebar terpanjang dari tiap kolom
		foreach ( $this->arrData as $row_id=>$row )
		{
			foreach( $row as $col_id=>$col_content )
			{
				$col_len[$col_id][$row_id]	= strlen( $col_content ) + 2;
			}
		}

		foreach( $col_len as $col_id=>$arr_len )
		{
			$arrMaxLen[$col_id]	= max( $arr_len );
			$arrMaxLen[$col_id]	= ( $arrMaxLen[$col_id] <= $this->maxColumnWidth )  ? $arrMaxLen[$col_id] : $this->maxColumnWidth;
		}

		return $arrMaxLen;
	}

	function write()
	{
	}

} // eof class phpRollReport

?>