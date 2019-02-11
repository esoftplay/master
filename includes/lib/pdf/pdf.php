<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

include_once _LIB.'pdf/class.pdf.php';
include_once _LIB.'pdf/class.ezpdf.php';

class pdf extends Cezpdf {
	function __construct($param)
	{
		parent::__construct($param['paper'], $param['layout']);
	}
}
