<?php
if ( ! defined('_VALID_BBC')){
	ob_start('ob_gzhandler');
	header('content-type: text/javascript; charset: UTF-8');
	header('cache-control: must-revalidate');
	$offset = 60 * 60 * 24 * 365;
	$expire = 'expires: ' . gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT';
	header($expire);
	include 'gui.js';
	die();
}

class Gui extends Jquery {
	var $is_return = false;
	var $dir;
	var $url;

	function __construct($is_meta, $path)
	{
		parent::__construct();
		global $sys;
		$this->dir = _LIB.$path;
		$this->url = _URL.'includes/lib/'.$path;
		$sys->link_css($this->url.'theme/ui.all.css', $is_meta);
		$sys->link_js($this->url.'gui.js', $is_meta);
		$this->set_return(false);
	}
	function draggable()
	{
	}
	function droppable()
	{
	}
	function resizable()
	{
	}
	function selectable()
	{
	}
	function sortable()
	{
	}
/*===========================================================
	$array = array(
		'title-1' => 'content-1'
	,	'title-2' => 'content-2'
	};
/*=========================================================*/
	function tabs($array, $divid = 'tabs', $params = array())
	{
		$content = array();
		foreach((array)$array AS $key => $value) {
			$content[] = array('title' => $key, 'content' => $value);
		}

		$def_params = array(
			'cookie'	=> array(
				'expires'	=> 10,
				'name'		=> 'tabs-'.$_GET['mod'],
			),
		);
		$param = $this->_set_param($params, $def_params);
		$output = array(
			'divid'		=> $divid
		,	'content'	=> $content
		,	'param'		=> $param
		);
		$this->plugin('cookie');
		return $this->_show($output, 'tabs');
	}
	function accordion($array, $divid = 'accordion', $params = array('header'=>'h3', 'alwaysOpen' => false))
	{
		$content = array();
		foreach((array)$array AS $key => $value) {
			$content[] = array('title' => $key, 'content' => $value);
		}
		$param = $this->_set_param($params);
		$output = array(
			'divid'		=> $divid
		,	'content'	=> $content
		,	'param'		=> $param
		);
		return $this->_show($output, 'accordion');
	}
/*===========================================================
	$output = array(
		'title'		=> 'List Email Template'
	,	'content'	=> 'Lorem ipsum dolor sit amet'
	,	'button'	=> 'Open Dialog'
	);
/*=========================================================*/
	function dialog($array, $divid = 'dialog', $params = array())
	{
		$content = array();
		$button = isset($array['button']);

		// set content...
		$tmp = array_values($array);
		$content = array();
		$content['title'] = isset($array['title']) ? $array['title'] : $tmp[0];
		$content['content'] = isset($array['content']) ? $array['content'] : $tmp[1];
		$content['content'] = !empty($content['content']) ? $content['content'] : $tmp[0];

		// set default params
		$def_params = array(
			'bgiframe'=> true
		,	'height'	=> 140
		,	'autoOpen'=> $button ? false : true
		);
		$param = $this->_set_param($params, $def_params);

		$output = array(
			'divid'		=> $divid
		,	'content'	=> $content
		,	'param'		=> $param
		);
		if($button){
			$output['button'] = !empty($array['button']) ? $array['button'] : 'Open Dialog';
			return $this->_show($output, 'dialog_button');
		}else{
			return $this->_show($output, 'dialog');
		}
	}
	function datepicker($array = array(), $divid = 'datepicker', $params = array())
	{
		// set content...
		$tmp = array_values($array);
		$content = array();
		$content['title']		= isset($array['title']) ? $array['title'] : @$tmp[0];
		$content['content'] = isset($array['content']) ? $array['content'] : @$tmp[1];
		$content['content'] = !empty($content['content']) ? $content['content'] : @$tmp[0];
		// set default params
		$def_params = array(
			'dateFormat'			=> 'yy-mm-dd'
		,	'changeMonth'			=> true
		,	'changeYear'			=> true
#		,	'minDate'					=> -20
#		,	'maxDate'					=> '+1M +10D'
#		,	'numberOfMonths'	=> 3
#		,	'showOn'					=> 'button'
#		,	'buttonImage'			=> 'images/calendar.gif'
#		,	'buttonImageOnly'	=> true
#		,	'showButtonPanel'	=> true
		);
		$param = $this->_set_param($params, $def_params);
		$output = array(
			'divid'		=> $divid
		,	'content'	=> $content
		,	'param'		=> $param
		);
		return $this->_show($output, 'datepicker');
	}
	function general()
	{
	}
	function show_hide()
	{
	}
	function _show($output, $template_file = 'default')
	{
		@extract($output);
		if($this->is_return) {
			ob_start();
			include $this->dir.'views/'.$template_file.'.html';
			$out = ob_get_contents();
			ob_end_clean();
			return $out;
		}else{
			include $this->dir.'views/'.$template_file.'.html';
			return false;
		}
	}
	function _set_param($prm = array(), $def = array())
	{
		$params = array_merge($def, $prm);
		$arr = array();
		foreach((array)$params AS $key => $value) {
			if(is_bool($value)) $value = $value ? 'true' : 'false';
			elseif(is_int($value)) $value = $value;
			elseif (is_array($value)) $value = $this->_set_param($value);
			else $value = '"'.$value.'"';
			$arr[] = $key.': '.$value;
		}
		$output = implode(', ', $arr);
		$output = !empty($output) ? '{'.$output.'}' : '';
		return $output;
	}
	function set_return($bool)
	{
		$this->is_return = $bool;
	}
}

?>