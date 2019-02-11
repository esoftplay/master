<?php
if ( ! defined('_VALID_BBC')){
	ob_start('ob_gzhandler');
	header('content-type: text/javascript; charset: UTF-8');
	header('cache-control: must-revalidate');
	$offset = 60 * 60 * 24 * 365;
	$expire = 'expires: ' . gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT';
	header($expire);
	include 'interface.js';
	die();
}

class Interfaces {
	var $is_return = false;
	var $is_meta;
	var $dir;
	var $url;

	function __construct($is_meta, $path)
	{
		global $sys;
		$this->dir = _LIB.$path;
		$this->url = _URL.'includes/lib/'.$path;
		$sys->link_js($this->url.'interface.js', $is_meta);
		$this->is_meta = $is_meta;
		$this->set_return(false);
	}
/*===========================================================
	$array = array(
		'position-1' => array(
											'block-1' => array( 'title' => 'First Block', 'content' => 'this is the content (could be html)')
										,	'block-2' => array( 'title' => 'Second Block', 'content' => 'this is the content (could be html)'))
	,	'position-2' => array(
											'block-3' => array( 'title' => 'Tirth Block', 'content' => 'this is the content (could be html)')
										,	'block-4' => array( 'title' => 'Fourth Block', 'content' => 'this is the content (could be html)'))
	);
/*=========================================================*/
	function sortable($array, $className = 'groupWrapper', $css = 'sortable')
	{
		$id_use = array();// just to make sure that id must unique
		$content = array();
		foreach((array)$array AS $position_id => $r_position) {
			foreach($r_position AS $block_id => $r_block) {
				$valid_data = false;
				if(isset($r_block['title']) && !in_array($block_id, $id_use)) {
					if(!isset($r_block['content'])) $r_block['content'] = '';
					$valid_data = true;
					$id_use[] = $block_id;
				}
				if($valid_data) $content[$position_id][$block_id] = $r_block;
			}
		}
		$output = array(
			'className'	=> $className
		,	'content'	=> $content
		);
		return $this->_show($output, 'sortable', $css);
	}
/*===========================================================
	$array = array(
		'title-1'	=> array('title' => 'Title Window', 'link' => 'http://idwebhost.jc/')
	,	'title-2'	=> array('title' => 'Title Window', 'link' => 'http://fisip.net/')
	);
/*=========================================================*/
	function windows($array=array(), $a_id = 'windowOpen', $css = 'windows')
	{
		$content = array();
		foreach((array)$array AS $key => $value) {
			$value['title'] = isset($value['title']) ? $value['title'] : $key;
			$content[] = array_merge(array('text' => $key), $value);
		}
		$output = array(
			'a_id'	=> $a_id
		,	'content'	=> $content
		,	'imgurl'=> $this->url.'images/'
		);
		return $this->_show($output, 'windows', $css);
	}
/*===========================================================
	$array = array(
		'title-1' => 'imgsrc-1'
	,	'title-2' => 'imgsrc-2'
	);
/*=========================================================*/
	function slideshow($array, $divid = 'slideShow', $params = array(), $css = 'slideshow')
	{
		$content= array();
		$array	= stripslashes_r($array);
		foreach((array)$array AS $key => $value) {
			$content[] = "{src: '$value', caption: '$key'}";
		}
		$images = '['.implode(',', $content).']';
		$def = array(
			'container'				=> $divid
		,	'loader'					=> $this->url.'images/slideshow_loader.gif'
		,	'linksPosition'		=> 'top'
		,	'linksClass'			=> 'pagelinks'
		,	'linksSeparator'	=> ' | '
		,	'fadeDuration'		=> 400
		,	'activeLinkClass'	=> 'activeSlide'
		,	'nextslideClass'	=> 'nextSlide'
		,	'prevslideClass'	=> 'prevSlide'
		,	'captionPosition'	=> 'bottom'
		,	'captionClass'		=> 'slideCaption'
		,	'autoplay'				=> 5
		,	'random'					=> false
		,	'images'					=> $images
		);
		$params['container'] = $divid;
		$output = array(
			'divid'		=> $divid
		,	'param'		=> $this->_set_param($params, $def, 'images')
		);
		return $this->_show($output, 'slideshow', $css);
	}
/*===========================================================
	$array = array(
		'title-1' => 'data-1'
	,	'title-2' => 'data-2'
	);
/*=========================================================*/
	function sorttab($array, $htmlid = 'sorttab', $params = array(), $css = 'sorttab')
	{
		$def = array(
			'accept'			=> 'sortableitem'
		,	'helperclass'	=> 'sortHelper'
		,	'activeclass'	=> 'sortableactive'
		,	'hoverclass'	=> 'sortablehover'
		,	'opacity'			=> '0.8'
		,	'floats'			=> true
		,	'revert'			=> true
		);
		$output = array(
			'htmlid'	=> $htmlid
		,	'param'		=> $this->_set_param($params, $def, 'images')
		,	'content'	=> $array
		);
		return $this->_show($output, 'sorttab', $css);
	}
/*===========================================================
	$array = array(
		'title-1' => array('thumb' => 'imgsrc', 'link'=>'imgsrc')
	,	'title-2' => array('thumb' => 'imgsrc', 'link'=>'imgsrc')
	};
/*=========================================================*/
	function fisheye($array, $divid = 'fisheye', $params = array(), $css = 'fisheye')
	{
		$content = array();
		foreach((array)$array AS $key => $value) {
			$content[] = array_merge(array('title' => $key), $value);
		}
		$def = array(
			'maxWidth'	=> 50
		,	'items'			=> 'a'
		,	'itemsText'	=> 'span'
		,	'container'	=> '.fisheyeContainter'
		,	'itemWidth'	=> 40
		,	'proximity'	=> 90
		,	'halign'		=> 'center'
		);
		$params0 = $params;
		$params['container'] = isset($params['container']) ? $params['container'] : $def['container'];
		if(substr($params['container'], 0, 1) == '.') $params['container'] = ' class="'.substr($params['container'], 1).'"';
		elseif(substr($params['container'], 0, 1) == '#') $params['container'] = ' id="'.substr($params['container'], 1).'"';
		else $params['container'] = ' class="'.$params['container'].'"';
		$output = array(
			'divid'		=> $divid
		,	'content'	=> $content
		,	'params'	=> array_merge($def, $params)
		,	'param'		=> $this->_set_param($params0, $def)
		);
		return $this->_show($output, 'fisheye', $css);
	}
	function drag($text, $divid = 'drag1', $params = array())
	{
		$def = array(
			'snapDistance'=> 10
		,	'frameClass'	=> 'frameClass'// ini adalah nama class saat di drag...
		);
		$output = array(
			'divid'		=> $divid
		,	'content'	=> $text
		,	'params'	=> array_merge($def, $params)
		,	'param'		=> $this->_set_param($params, $def)
		);
		return $this->_show($output, 'drag');
	}
	function tooltip($tagHTML = 'a', $className = 'tooltip', $css = '', $params = array())
	{
		$def = array(
			'className'	=> $className
		,	'position'	=> 'mouse'
		,	'delay'			=> 200
		);
		$params['className'] = $className;
		if($className == 'tooltip') {
			$css = 'tooltip';
		}
		$output = array(
			'tagHTML'		=> $tagHTML
		,	'param'		=> $this->_set_param($params, $def)
		);
		return $this->_show($output, 'tooltip', $css);
	}
/*===========================================================
	$array = array(
		'title-1' => array('thumb' => 'imgsrc', 'image'=>'imgsrc')
	,	'title-2' => array('thumb' => 'imgsrc', 'image'=>'imgsrc')
	};
/*=========================================================*/
	function carousel($array, $divid = 'carousel', $params = array(), $css = 'carousel')
	{
		$content = array();
		foreach((array)$array AS $key => $value) {
			$content[] = array_merge(array('title' => $key), $value);
		}
		$def = array(
			'itemWidth'			=> 110
		,	'itemHeight'		=> 62
		,	'itemMinWidth'	=> 50
		,	'items'					=> 'a'
		,	'reflections'		=> '.5'
		,	'rotationSpeed'	=> 1.8
		);
		$output = array(
			'divid'		=> $divid
		,	'content'	=> $content
		,	'params'	=> array_merge($def, $params)
		,	'param'		=> $this->_set_param($params, $def)
		,	'loaderSRC'=> $this->url.'images/loading.gif'
		,	'closeHTML'=> $this->url.'images/close.jpg'
		);
		return $this->_show($output, 'carousel', $css);
	}
/*===========================================================
	$array = array(
		'title-1' => array('thumb' => 'imgsrc', 'image'=>'imgsrc')
	,	'title-2' => array('thumb' => 'imgsrc', 'image'=>'imgsrc')
	};
/*=========================================================*/
	function imagebox($array, $css = 'imagebox')
	{
		$content = array();
		foreach((array)$array AS $key => $value) {
			$content[] = array_merge(array('title' => $key), $value);
		}
		$output = array(
			'content'	=> $content
		,	'loaderSRC'=> $this->url.'images/loading.gif'
		,	'closeHTML'=> $this->url.'images/close.jpg'
		);
		return $this->_show($output, 'imagebox', $css);
	}
	function accordion($array, $divid = 'myAccordion', $params = array(), $css = 'accordion')
	{
		$content = array();
		foreach((array)$array AS $key => $value) {
			$content[] = array('title' => $key, 'content' => $value);
		}
		$def = array(
			'headerSelector'=> 'dt'
		,	'panelSelector'	=> 'dd'
		,	'activeClass'		=> 'myAccordionActive'
		,	'hoverClass'		=> 'myAccordionHover'
		,	'panelHeight'		=> 200
		,	'speed'					=> 300
		);
		$output = array(
			'divid'		=> $divid
		,	'content'	=> $content
		,	'params'	=> array_merge($def, $params)
		,	'param'		=> $this->_set_param($params, $def)
		);
		return $this->_show($output, 'accordion', $css);
	}
	function _show($output, $template_file = 'default', $css = '')
	{
		global $sys;
		if(!empty($css))
		{
			if(!preg_match('~\.css$~s', $css)) $css .= '.css';
			if(is_file($css)) {
				$css = preg_replace('~^'.addslashes(_ROOT).'~is', _URL, $css);
			} else {
				$css = $this->url.'css/'.$css;
			}
			$sys->link_css($css, $this->is_meta);
		}
		@extract($output);
		if($this->is_return)
		{
			ob_start();
			include $this->dir.'views/'.$template_file.'.php';
			$out = ob_get_contents();
			ob_end_clean();
			return $out;
		}else{
			include $this->dir.'views/'.$template_file.'.php';
			return false;
		}
	}
	function _set_param($prm = array(), $def = array(), $noquote = '')
	{
		if(!empty($noquote))	$noquote = is_array($noquote) ? $noquote : array($noquote);
		else $noquote = array();
		$params = array_merge($def, $prm);
		$arr = array();
		foreach((array)$params AS $key => $value) {
			if(is_array($value)) $value = $this->_set_param($value);
			elseif(in_array($key, $noquote)) $value = $value;
			elseif(is_int($value)) $value = $value;
			elseif(is_bool($value)) $value = $value ? 'true' : 'false';
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