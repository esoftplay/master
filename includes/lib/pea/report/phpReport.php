<?php
class phpReport
{
	var $fileName;
	var $worksheetName;
	var $arrHeader;
	var $arrData;
	var $headerColor;
	var $maxColumnWidth; 	// maximum column width,

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

	function setHeader( $arrHeader )
	{
		$this->arrHeader = $arrHeader;
	}

	function setHeaderColor( $bgcolor = "gray", $fontcolor ="black")
	{
		$this->headerColor['bg']	= $bgcolor;
		$this->headerColor['font'] 	= $fontcolor;
	}

	function setMaxColumnWidth( $maxColumnWidth = 60 )
	{
		$this->maxColumnWidth = $maxColumnWidth;
	}

	function getColumnWidth($col=null)
	{
		// nggabung antara title dan data
		if ( !empty( $this->arrHeader ) )
		{
			$title[0]	= $this->arrHeader;
			$data		= array_merge( $title, $this->arrData );
		}
		else
		{
			$data		= $this->arrData;
		}

		// cari lebar terpanjang dari tiap kolom
		foreach ( $data as $row_id=>$row )
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

} // eof class phpReport

?>