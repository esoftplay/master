<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$extensions = array(
	'archive'    => array('tar', 'gz', 'bz', 'zip'),
	'audio'      => array('mp3', 'wav'),
	'code'       => array('htm', 'html', 'css', 'js'),
	'excel'      => array('xls', 'xlsx', 'numbers'),
	'image'      => array('jpg', 'jpeg', 'gif', 'png', 'bmp', 'psd', 'ai', 'wmf', 'tif', 'eps', 'pcx', 'dxf'),
	''           => array('dll', 'exe', 'bat', 'cmd', 'com'),
	'pdf'        => array('pdf'),
	'powerpoint' => array('ppt', 'pptx', 'key'),
	'text'       => array('txt'),
	'video'      => array('dat', 'mp3', '3gp', 'avi', 'mpg', 'mp4', 'wma', 'swf', 'mkv'),
	'word'       => array('doc', 'docx', 'pages')
	);