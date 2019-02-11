<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

function delete_block_file()
{
	include_once _SYS.'layout.blocks.php';
	$b = new blockSystem();
	return $b->delete_block_file();
}
