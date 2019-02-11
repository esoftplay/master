<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/**
* INI HANYA CONTOH PEMBUATAN CLASS FORM
*/
class FormExample extends Form
{

	function __construct()
	{
		die('Ini hanya class contoh saja, mohon jangan digunakan type "example"');
		$this->type = 'example';
		/* semua input argument di bawah adalah nilai default jd tdk perlu ditambahkan lagi */
		$this->setIsIncludedInReport(true);					// apakah element ini akan ikut didalam reporting 				-> akan mengeksekusi getReportOutput($str_default_value)
		$this->setIsIncludedInSearch(true);					// apakah element ini akan ikut didalam search pencarian	-> di phpEasyAdminLib akan di include kan
		$this->setIsIncludedInSelectQuery(true);		// apakah element ini akan ikut didalam select query 			-> di phpEasyAdminLib akan di include kan
		$this->setIsIncludedInUpdateQuery(true);		// apakah element ini akan ikut didalam update query 							-> akan mengeksekusi getRollUpdateSQL($i=''), getAddAction($db, $Insert_ID), getAddSQL()
		$this->setIsIncludedInDeleteQuery(false);		// apakah element ini akan ikut didalam delete query (roll/edit)	-> akan mengeksekusi getDeleteSQL($ids) sebelum data row dihapus
		$this->setIsNeedDbObject(false);						// apakah perlu object db, supaya bisa execute mysql (akan menjadi $this->db)
	}
	// di eksekusi ketika tumbol save di klik jika $this->isIncludedInUpdateQuery==true
	function getRollUpdateSQL($i='')
	{
		if ($i == '' && !is_numeric($i))
		{
			// UPDATE DARI EDIT FORM
		}else{
			// UPDATE DARI ROLL FORM
		}
	}
	// di eksekusi ketika tombol add di klik jika $this->isIncludedInUpdateQuery==true
	function getAddSQL()
	{
		/*
		gunakan text _INSERT_ID jika ingin di replace menjadi id terbaru dari main table
		dan return dalam bentuk array:
		return array('_PENDING_QUERY', 'INSERT INTO blabla1', 'INSERT INTO blabla2'...);

		return kan array berikut jika ingin menambahkan field saja
		return array('into' => '`nama_field`, ', 'value'=>"'hasil input', ");
		*/
	}
	// di eksekusi ketika tombol delete di klik jika $this->isIncludedInDeleteQuery==true
	function getDeleteSQL($ids)
	{
	}
	// di eksekusi setelah database di masukkan (Form Add saja) jika $this->isIncludedInUpdateQuery==true
	function getAddAction($db, $Insert_ID)
	{
		return '';
	}
	// dieksekusi ketika ingin menampilkan tombol export jika $this->isIncludedInReport==true
	function getReportOutput( $str_value = '' )
	{
		return $str_value;
	}
	// dieksekusi ketika ingin menampilkan output di form jika $this->isPlaintext==true
	function getPlaintexOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		return $this->getReturn($this->getReportOutput( $str_value ));
	}
	// dieksekusi ketika ingin menampilkan output di form jika $this->isPlaintext==false
	function getOutput( $str_value = '', $str_name = '', $str_extra = '' )
	{
		return 'defaultFormOutput';
	}
}