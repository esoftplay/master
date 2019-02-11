<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

include_once( _PEA_ROOT . 'basic.inc.php' );
include_once( _PEA_ROOT . 'phpFormAdmin.php' );
class phpEasyAdmin extends oDebug
{
	var $isLoaded;
	var $table;
	var $type;
	var $db;
	function __construct( $str_table )
	{
		global $sys;
		$this->table = $str_table;
		$this->setDB();
	}

	function setDB( $db = '' )
	{
		if ( $db == '' )
		{
			global $db;
		}
		$this->db = $db;
	}

	function initRoll( $str_sql_condition = '', $str_table_id='id', $arr_files_field=array(), $arr_folder=array())
	{
		include_once( _PEA_ROOT . 'phpEasyAdminLib.php' );
		include_once( _PEA_ROOT . 'phpRollAdmin.php' );
		$this->type = 'roll';
		if( count( $arr_files_field ) != count( $arr_folder ) )
			$this->debug(1, 'phpEasyAdmin Error, jumlah array files_field tidak sama dengan arr_folder');
		$i = 0;
		$arrFolder = array();
		foreach( $arr_files_field as $field )
		{
			$arrFolder[$field]	= $arr_folder[$i];
			$i++;
		}

		$this->roll	= new phpRollAdmin( $this->table, $str_sql_condition, $str_table_id, $arrFolder);
		$this->roll->setDB( $this->db );
	}

	function initAdd($str_table_id='id')
	{
		include_once( _PEA_ROOT . 'phpEasyAdminLib.php' );
		include_once( _PEA_ROOT . 'phpAddAdmin.php' );
		$this->type = 'add';
		$this->add	= new phpAddAdmin( $this->table, $str_table_id);
		$this->add->setDB( $this->db );
	}

	function initEdit( $str_sql_condition = '', $str_table_id='id')
	{
		include_once( _PEA_ROOT . 'phpEasyAdminLib.php' );
		if(empty($str_sql_condition))
		{
			include_once( _PEA_ROOT . 'phpAddAdmin.php' );
			$this->type = 'add';
			$this->edit	= new phpAddAdmin( $this->table, $str_table_id);
			$this->edit->setDB( $this->db );
		}else{
			include_once( _PEA_ROOT . 'phpEditAdmin.php' );
			$this->type = 'edit';
			$this->edit	= new phpEditAdmin( $this->table, $str_table_id);
			$this->edit->setDB( $this->db );
			$this->edit->setSqlCondition( $str_sql_condition );
		}
	}

	function initSearch( $str_sql_condition = '1' )
	{
		include_once( _PEA_ROOT . 'phpEasyAdminLib.php' );
		include_once( _PEA_ROOT . 'phpSearchAdmin.php' );
		$this->type = 'search';
		$this->search	= new phpSearchAdmin( $this->table, $str_sql_condition);
		$this->search->setDB( $this->db );
	}
}