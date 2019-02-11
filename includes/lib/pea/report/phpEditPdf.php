<?php

require_once dirname(dirname(__FILE__)).'/config.php';
include_once _PEA_ROOT.'report/phpReport.php';
include_once _PEA_ROOT.'includes/fpdf/fpdf.php';
include_once _PEA_ROOT.'includes/fpdf/mc_table/mc_table.php';

/**
* Class for generate simple excel file, just send array data and array title
* and it class will create an excel file with your array data
* example :
*    $excel = new phpRollExcel( $fileName="report.xls", $worksheetName="report", $arrData );
*    $excel->setMaxColumnWidth(60);
*    $excel->setHeaderColor('yellow', 'black');
*    $excel->write();
*
* @author Ogi Sigit Pornawan <ogi.sigit.p@idwebhost.com>
* @package phpRollExcel
*/
class phpEditPdf extends phpReport
{
    /**
    * Konstruktor: Inisialisasi
	* example:
    * $arrHeader = array('Nama','Umur');
    * $arrData[] = array('ogy', 12);
    * $arrData[] = array('sigit', 54);
    * $arrData[] = array('ogi sigit pornawan testing excel report', 54);
    * $excel = new phpMyExcel( $fileName="report.xls", $worksheetName="report", $arrHeader, $arrData );
	*
    * @access public
    * @param string	$fileName		Nama file excel hasil generate
    * @param string	$worksheetName	Nama worksheet dari Excel
	* @param array	$arrHeader		array header, yang nantinya jadi title di excelnya
	* @param array	$arrHeader		array header, yang nantinya jadi title di excelnya
    */
	function __construct( $fileName='', $worksheetName='', $arrData = array() )
	{
		$tgl	= date("Y-m-d");

		if ( $fileName == '' )		$fileName = "pdfReport". $tgl .".pdf";
		if ( $worksheetName == '' )	$worksheetName = "Pdf Report ". $tgl;

		$this->type				= "pdf";
		$this->fileName			= $fileName;
		$this->worksheetName	= $worksheetName;
		$this->arrData			= $arrData;
		$this->setHeaderColor();
		$this->setMaxColumnWidth(150);
	}

	// ini adalah method overwrite dari phpRollReport
	// argumen merupakan instance dari fpdf
	// untuk menghitung panjang font
	//
	// jika panjang semua kolom melebihi satu halaman
	// panjang kolom di persen, sehingga didapat width proporsional
	function getColumnWidth( $pdf= null )
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
				//$col_len[$col_id][$row_id]	= strlen( $col_content ) + 2;
				$col_len[$col_id][$row_id]	= $pdf->GetStringWidth( $col_content ) + 3 ;
			}
		}

		// mencari panjang maksimum dari semua baris pada tiap kolom
		$totLen	= 0;
		foreach( $col_len as $col_id=>$arr_len )
		{
			$arrMaxLen[$col_id]	= round(max( $arr_len ) + 2);
			$arrMaxLen[$col_id]	= ( $arrMaxLen[$col_id] <= $this->maxColumnWidth )  ? $arrMaxLen[$col_id] : $this->maxColumnWidth;
			$totLen	+= $arrMaxLen[$col_id]; // cari jumlah panjang semua kolom
			//echo "\n";
		}

		$pageWidth	= $pdf->fw;

		// jika panjang semua kolom melebihi satu halaman, dicari lebar proporsional
		if ( $totLen > $pageWidth )
		{
			//echo $totLen . " DFASDSD ". $pageWidth;
			// mencari panjang proporsional tiap kolom pada semua baris
			foreach( $arrMaxLen as $col_id=>$arr_len )
			{
				$arrProporsionalLen[$col_id]	= round($arr_len/$totLen*$pageWidth);
				//echo $arr_len ."\t\t\t". $totLen ."\t\t\t" . $pageWidth . "\t\t\t". $arrProporsionalLen[$col_id] ."              //////\n";
			}
		}
		else
		{
			$arrProporsionalLen	= $arrMaxLen;
		}

		return $arrProporsionalLen;
	}

	function write()
	{

		define('FPDF_FONTPATH',_PEA_ROOT.'includes/fpdf/font/');

		$pdf=new PDF_MC_Table( 'L' );
		$pdf->Open();
		$pdf->AddPage();
		$pdf->SetFont('Arial','',10);

		// dapatkan lebar kolom, diambil dari data terpanjang
		$arrColWidth	= $this->getColumnWidth( $pdf );

		// pr( $arrColWidth );
		// ngeset lebar colom
		// tiap kolom
		$pdf->SetWidths( $arrColWidth );
		$totWidth	= array( array_sum($arrColWidth) );

		// buat data
		// pr($this->arrData, __FILE__.':'.__LINE__);die();
		if ( !empty( $this->arrData ) )
		{
			foreach( $this->arrData as $dataRow )
			{
				if ( count($dataRow) == 1)
				{
					$pdf->SetWidths( $totWidth );
					$pdf->RowHeader( $dataRow );
					$pdf->SetWidths( $arrColWidth );
				}else{
					$datas = array_chunk($dataRow, 2);
					foreach ($datas as $data)
					{
						$pdf->row($data);
					}
					// $pdf->Row( $dataRow );
				}
			}
		}

		$pdf->Ln(); $pdf->Ln();
		$pdf->SetFont('Arial','',8);
		$pdf->write( 3, $this->worksheetName );
		$pdf->Output($this->fileName,'D');

	} // eof write()

} // eof class
