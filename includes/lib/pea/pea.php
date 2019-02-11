<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

include_once( dirname(__FILE__).'/config.php' );
include_once( _PEA_ROOT.'phpEasyAdmin.php' );

class pea extends phpEasyAdmin
{
	function __construct($str_table)
	{
		parent::__construct($str_table);
	}
}
