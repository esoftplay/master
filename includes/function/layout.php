<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

function image($file, $sizes = '', $attr='')
{
	$path_file= '';
	$path_url	= '';
	if(preg_match('~^(?:ht|f)tps?://~is', $file))
	{
		$tmp = str_replace(_URL, _ROOT, $file);
		if(!preg_match('~^(?:ht|f)tps?://~is', $tmp))
		{
			if(is_file($tmp))
			{
				$path_file = $tmp;
			}else{
				return false;
			}
		}
		$path_url = $file;
	}else{
		if(is_file($file))
		{
			$path_file= $file;
			$path_url	= str_replace(_ROOT, _URL, $file);
		}else
		if(is_file(_ROOT.$file))
		{
			$path_file= _ROOT.$file;
			$path_url	= _URL.$file;
		}else{
			return false;
		}
	}
	if(preg_match('~\.swf$~is', $path_file))
	{
		list($width, $height) = image_size($sizes);
		$width	= $width ? $width : 200;
		$height = $height ? $height : 200;
		$output =	 '<object type="application/x-shockwave-flash" data="'.$path_url.'" width="'.$width.'" height="'.$height.'"'.$attr.'>'."\n";
		$output .= '	<param name="movie" value="'.$path_url.'">'."\n";
		$output .= '	<param name="menu" value="false">'."\n";
		$output .= '	<param name="wmode" value="transparent">'."\n";
		$output .= "</object>";
	}else{
		$sizes2 = image_size($sizes, true);
		if(empty($path_file))
		{
			$sizes = $sizes2;
		}else{
			$sizes1 = getimagesize($path_file);
			$sizes	= array();
			if($sizes1[0] > $sizes2[0])
			{
				if($sizes1[0] > $sizes1[1])
				{
					$sizes[0] = $sizes2[0];
					$sizes[1] = $sizes2[0]*$sizes1[1]/$sizes1[0];
				}else{
					$sizes[1] = $sizes2[1];
					$sizes[0] = $sizes2[1]*$sizes1[0]/$sizes1[1];
				}
			}else{
				$sizes = $sizes1;
			}
		}
		$attr .= $sizes[0] ? ' width="'.$sizes[0].'"' : '';
		$attr .= $sizes[1] ? ' height="'.$sizes[1].'"' : '';
		if (!empty($attr))
		{
			$attr = ' '.$attr;
		}
		$output = '<img src="'.$path_url.'"'.$attr.' />';
	}
	return $output;
}

function image_size($sizes, $in_resize = false)
{
	if(empty($sizes)) return array(0,0);
	if(is_array($sizes))
	{
		$output = array_values($sizes);
		if(!isset($output[1])||!$output[1]) $output[1] = 0;
	}else{
		preg_match('~([0-9]+)[x\*\,]?([0-9]+)?~', $sizes, $match);
		$output[] = $match[1];
		$output[] = (@intval($match[2]) > 0) ? $match[2] : 0;
	}
	if($in_resize && !$output[1]) $output[1] = $output[0];
	return $output;
}

function image_transform($x,$y,$x1,$y1)
{
	$input_landscape  = ($x > $y) ? true : false;
	$output_landscape = ($x1 > $y1) ? true : false;
	$x2 = $y2 = 0;
	if($input_landscape)
	{
		if($output_landscape)
		{
			$x2 = $x1;
			$y2 = ceil($y/$x*$x2);
		}else{
			$y2 = $y1;
			$x2 = ceil($y/$x*$y2);
		}
	}else{
		if($output_landscape)
		{
			$x2 = $x1;
			$y2 = ceil($y/$x*$x2);
		}else{
			$x2 = $x1;
			$y2 = ceil($y/$x*$x2);
		}
	}
	return array($x2,$y2);
}
function icon($value='edit',$alt='',$extra='')
{
	if (empty($alt))
	{
		$alt = str_replace('-', ' ', $value);
		if (substr($value, 0,3)=='fa-')
		{
			$alt = substr($alt, 3);
		}
		if (substr($alt, -2)==' o')
		{
			$alt = substr($alt, 0, -2);
		}
	}
	if (!empty($extra))
	{
		if (substr($extra, 0,1)!=' ')
		{
			$extra = ' '.$extra;
		}
	}
	if (substr($value, 0,3)=='fa-')
	{
		link_css(_ROOT.'templates/admin/bootstrap/css/font-awesome.min.css');
		return '<i class="fa '.$value.'" title="'.$alt.'"'.$extra.'></i>';
	}
	$class = '';
	$old_icon = array(
		'add'      => 'plus'
	, 'disable'  => 'ban-circle'
	, 'download' => 'cloud-download'
	, 'enable'   => 'ok-circle'
	, 'help'     => 'question-sign'
	, 'image'    => 'picture'
	, 'inactive' => 'eye-close'
	, 'login'    => 'log-in'
	, 'mail'     => 'envelope'
	, 'right'    => 'ok-sign'
	, 'tooltip'  => 'info-sign'
	, 'unknown'  => 'file'
	, 'update'   => 'check'
	, 'upload'   => 'cloud-upload'
	, 'warn'     => 'warning-sign');
	if (isset($old_icon[$value]))
	{
		$class = $old_icon[$value];
	}
	if (empty($class))
	{
		// include dirname(__FILE__).'/../config/icons.php';
		$class = $value;
	}
	return '<span class="glyphicon glyphicon-'.$class.'" title="'.$alt.'"'.$extra.'></span>';
}
/*
EXAMPLE:
echo table(array('Nama' => 'Danang','Alamat' => 'Pringgondani'));
echo table(array(array('Danang','Pringgondani'),array('Widiantoro','Surgo')), array('Nama','Alamat'));
*/
function table($data, $header = array(), $title='')
{
	$output = '';
	if (!empty($data))
	{
		$tHead = '';
		$tBody = '';
		if (!empty($header) && !is_array($header))
		{
			if (empty($title))
			{
				$title = $header;
			}
			$header = array();
		}
		if (!empty($header))
		{
			$tHead = '<thead><tr><th>'.implode('</th><th>', $header).'</th></tr></thead>';
			$rows  = array();
			foreach ($data as $row)
			{
				$rows[] = '<td>'.implode('</td><td>', $row).'</td>';
			}
			$tBody = '<tbody><tr>'.implode('</tr><tr>', $rows).'</tr></tbody>';
		}else{
			foreach ((array)$data as $key => $value)
			{
				if (is_array($value))
				{
					$value = call_user_func(__FUNCTION__, $value);
				}
				$tBody .= '<tr><th>'.$key.'</th><td>'.$value.'</td></tr>';
			}
		}
		$output = '<table class="table table-striped table-bordered table-hover">'.$tHead.$tBody.'</table>';
		if (!empty($title))
		{
			$output = '
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">'.$title.'</h3>
					</div>
					<div class="panel-body">
						'.$output.'
					</div>
				</div>';
		}
	}
	return $output;
}
function tabs($data, $use_cookie = 1, $name='', $maxwidth = false, $r_iframe = array())
{
	$output = '';
	if (!empty($data) && is_array($data))
	{
		if (count($data) < 2)
		{
			$output = current($data);
		}else{
			global $Bbc;
			if (empty($name))
			{
				if (empty($Bbc->mytabs))
				{
					$Bbc->mytabs = 0;
				}
				$name = @$Bbc->mod['name'].'_'.@$Bbc->mod['task'].($Bbc->mytabs++);
			}
			if (!isset($Bbc->tab_is_url))
			{
				$Bbc->tab_is_url = false;
				$load_script     = true;
			}else $load_script = false;
			$r_pane = $r_page = array();$i = 0;
			foreach ($data as $title => $content)
			{
				$div = $name.'_'.$i++;
				$active = $i==1? array(' class="active"',' active') : array('','');
				if(is_url($content))
				{
					if(in_array($content, $r_iframe) || $r_iframe == 'all')
					{
						$r_pane[] = '<li'.$active[0].'><a href="#'.$div.'" rel="'.$content.'" data-toggle="tab">'.$title.'</a></li>';
						$content = '<iframe src="'.$content.'" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0" style="width:100%; height:auto; min-height: 100px"></iframe>';
					}else{
						$r_pane[] = '<li'.$active[0].'><a href="#'.$div.'" rel="'.$content.'" data-toggle="url">'.$title.'</a></li>';
						$Bbc->tab_is_url = true;
						$content = '<div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"><span class="sr-only">Please wait...</span></div></div>';
					}
				}else{
					$r_pane[] = '<li'.$active[0].'><a href="#'.$div.'" data-toggle="tab">'.$title.'</a></li>';
				}
				$r_page[] = '<div id="'.$div.'" class="tab-pane'.$active[1].'">'.$content.'</div>';
			}
			$cls = $maxwidth ? ' nav-justified' : '';
			$output = '<ul class="nav nav-tabs'.$cls.'" cookie="'.$use_cookie.'" >'
							.	implode("\n", $r_pane).'</ul>'
							.	'<div class="tab-content">'.implode("\n", $r_page).'</div>';
			if (($use_cookie || $Bbc->tab_is_url) && $load_script)
			{
				$output .= '<script type="text/javascript">var BS3load_tabs=1;</script>';
			}
		}
	}
	return $output;
}

function tab_link($r_menu, $def= '', $maxwidth=true)
{
	$cls = $maxwidth ? ' nav-justified' : '';
	$out = '<ul class="nav nav-tabs'.$cls.'" role="tablist">';
	foreach($r_menu AS $data)
	{
		$link = array_values($data);
		$add	= ($link[1]==$def) ? ' class="active"' : '';
		$out .= '<li'.$add.'><a href="'.$link[1].'">'.$link[0].'</a></li>';
	}
	$out .= '</ul>';
	return $out;
}
/*
DO NOT USE THIS FUNCTION TO LOAD TOTAL PAGE MORE THAN $max_nav
OR THE PAGINATION WILL BE WAY TO LONG
*/
function page_ajax($tot_items, $tot_items_perpage, $baseurl, $use_number=false, $id_page='' )
{
	global $Bbc;
	$output   = '';
	$nav      = '';
	$tot_page = intval($tot_items/$tot_items_perpage);
	$tot_page += (($tot_items % $tot_items_perpage) > 0) ? 1 : 0;
	if ($tot_page > 1)
	{
		if ($use_number)
		{
			$nav .= '<ul class="pagination">';
			for ($i=0; $i < $tot_page; $i++)
			{
				$nav .= '<li><a href="#'.$i.'">'.($i+1).'</a></li>';
			}
			$nav .= '</ul>';
		}else{
			$nav .= '<a href="#0" class="page_ajax_more" data-max="'.$tot_page.'">'.icon('fa-angle-double-down').' '.lang('load more').'</a>';
		}
	}
	// Define $id_page
	if (empty($id_page))
	{
		if (empty($Bbc->page_ajax))
		{
			$Bbc->page_ajax = 1;
		}else{
			$Bbc->page_ajax++;
		}
		$id_page = 'page_ajax'.$Bbc->page_ajax;
		$output  = '<div><div id="'.$id_page.'"></div><div class="page_ajax" data-target="#'.$id_page.'" rel="'.$baseurl.'">'.$nav.'</div></div>';
	}else{
		// Use only if target element in the other side
		$output = '<div class="page_ajax" data-target="'.$id_page.'" rel="'.$baseurl.'">'.$nav.'</div>';
	}
	if (empty($Bbc->load_page_script))
	{
		$Bbc->load_page_script = 1;
		$output .= '<script type="text/javascript">var BS3load_page=1;</script>';
	}
	return $output;
}

function page_list($found, $show, $curr=0, $var='', $link='', $maxpage=12, $interval=0, $attr = '' )
{
	$output    = '';
	$totalpage = ceil($found/$show);
	if($totalpage > 1)
	{
		if(intval($interval)==0) $interval = intval($maxpage / 2);
		$link = !empty($link) ? $link : seo_uri($var);
		if(_SEO && _ADMIN == '')
		{
			if(preg_match('~\?mod\=~', $link))
			{
				if(!empty($var))
				{
					$link .= (preg_match('/\?/', $link)) ? '&' : '?';
					$link .= $var.'=';
				}
				$s = '&';
			}else{
				$s = preg_match('~[\?]~s', $link) ? '&' : '/';
				$p = ($s=='&') ? '=' : ',';
				$link .= $s;
				if (!($var=='id' && $p==','))
				{
					$link .= $var.$p;
				}
			}
		}else{
			if(!empty($var))
			{
				$link .= (preg_match('/\?/', $link)) ? '&' : '?';
				$link .= $var.'=';
			}
			$s = '&';
		}
		$output .= '<form action="'.$link.'" method="GET" class="form-inline" role="form" '
						. 'onSubmit="if(parseInt(this.page.value) <= '. $totalpage
						. ' && parseInt(this.page.value) > 0){document.location.href=\''.$link
						. '\'+(parseInt(this.page.value) - 1);}else{alert(\'invalid page number\');}return false;">';
		$output .= '<div class="form-group"><ul class="pagination">';

		if($curr > 0)
		{
			$output .= '<li><a href="'.$link.($curr - 1).'"'.$attr.'>&laquo;</a></li>';
		}
		if(($interval+$curr) > $maxpage)
		{
			$iend   = ($curr + $interval);
			$istart = $iend - $maxpage;
		} else {
			$istart = 0;
			$iend   = $istart + $maxpage;
		}
		if($iend > $totalpage) $iend = $totalpage;
		for ($i = $istart; $i < $iend; $i++)
		{
			$class = ($curr==$i) ? ' class="active"' : '';
			$j = $i + 1;
			$href = $i ? $link.$i : substr($link, 0, strrpos($link, $s));
			$output .= '<li'.$class.'><a href="'.$href.'"'.$attr.'>'.$j.'</a></li>';
		}
		if(($curr + 1) < $totalpage)
		{
			$output .= '<li><a href="'.$link.($curr + 1).'"'.$attr.'> &raquo;</a></li>';
		}

		$output .= '</ul></div>';
		if($totalpage > $maxpage)
		{
			$curr += 1;
			$output .= '
				<div class="form-group">&nbsp;</div>
				<div class="form-group">
					<input type="number" class="form-control input-sm form-control-sm" name="page" maxvalue="'.$totalpage.'" placeholder="Jump To" onClick="this.select();">
					<label>of '.$totalpage.' </label>
				</div>
				<button type="submit" class="btn btn-default btn-secondary btn-sm">Go</button>';
		}
		$output .= '</form>';
	}
	return $output;
}
/*===============================================
 * various browser '../config/agents.php'
 *==============================================*/
function browser($browser = '', $version = '', $math = '=')
{
	if(empty($browser))
	{
		return true;
	}else{
		$b = _class('agent');
		if(!$b->is_browser())
		{
			return false;
		}else
		if(stristr($b->browser(), $browser))
		{
			if(empty($version))
			{
				return true;
			}else{
				$v = $b->version();
				switch($math)
				{
					case '>': if($version >  $v){ return true;} break;
					case '<': if($version <  $v){ return true;} break;
					case '=>':
					case '>=':if($version >= $v){ return true;} break;
					case '=<':
					case '<=':if($version <= $v){ return true;} break;
					case '=': if($version == $v){ return true;} break;
					case 'like': if( stristr($version, $v)){ return true;} break;
					default: return false;break;
				}
			}
		}
	}
	return false;
}
function link_js($file, $is_meta = true, $browser = '', $version = '', $math = '=')
{
	if(browser($browser, $version, $math))
	{
		global $sys, $Bbc;
		$file = _mst($file);
		if (is_url($file))
		{
			$js = $file;
		}else
		if(is_file($sys->template_dir.'js/'.$file))
		{
			$js = $sys->template_url.'js/'.$file;
		}else
		if (is_file($file))
		{
			$js = str_replace(_ROOT, _URL, $file);
		}else
		if (is_file(_ROOT.$file))
		{
			$js = _URL.$file;
		}else
		if (is_file(str_replace(_URL, _ROOT, $file)))
		{
			$js = $file;
		}else{
			$js = $Bbc->mod['url'].$file;
		}
		$sys->link_js($js, $is_meta);
		return true;
	}else{
		return false;
	}
}
function link_css($file, $is_meta = true, $browser = '', $version = '', $math = '=')
{
	if(browser($browser, $version, $math))
	{
		global $sys, $Bbc;
		$file = _mst($file);
		if(is_file($sys->template_dir.'css/'.$file))
			$css = $sys->template_url.'css/'.$file;
		else
		if (is_file($file))
			$css = str_replace(_ROOT, _URL, $file);
		else
		if (is_file(_ROOT.$file))
			$css = _URL.$file;
		else
		if (is_file(str_replace(_URL, _ROOT, $file)))
			$css = $file;
		else $css = $Bbc->mod['url'].$file;
		$sys->link_css($css, $is_meta);
		return true;
	}else{
		return false;
	}
}
function total($i, $singular = 'item', $plural='')
{
	$i = intval($i);
	$plural = !empty($plural) ? $plural : $singular.'s';
	$output = ($i > 1) ? $i.' '.lang($plural) : $i.' '.lang($singular);
	return $output;
}

function tip($title, $text, $position='bottom'/*top|right|bottom|left*/)
{
	global $sys;
	return $sys->tip_text($title, $text, $position);
}

function help($text, $position='top'/*top|right|bottom|left*/, $icon='question-sign')
{
	global $sys;
	return $sys->tip_tool($text, $icon, $position);
}
function msg($Msg, $title='info' /*success|info|warning|danger*/)
{
	global $sys;
	return $sys->msg($Msg,$title);
}
// display message
function explain($Msg, $title='')
{
	$out = '
	<div class="alert alert-warning alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert">
			<span aria-hidden="true">&times;</span>
			<span class="sr-only">Close</span>
		</button>
		<strong>'.$title.'</strong> '.$Msg.'
	</div>';
	return $out;
}

function createOption($arr, $select='')
{
	$output = '';
	$valueiskey	= $check_first = false;
	foreach((array)$arr AS $key => $dt){
		if(is_array($dt)){
			list($value, $caption) = array_values($dt);
			if(empty($caption)) $caption = $value;
		}else{
			if(!$check_first) {
				if((is_numeric($key) && $key != 0)
				|| (is_string($key) && !is_numeric($key))) {
					$valueiskey = true;
				}
				$check_first = true;
			}
			if(empty($dt) && !empty($key)) $dt = $key;
			$value = $valueiskey ? $key : $dt;
			$caption = $dt;
		}
		if(isset($select)){
			if(is_array($select)) $selected = (in_array($value, $select)) ? ' selected="selected"':'';
			else    $selected = ($value==$select) ? ' selected="selected"':'';
		}else{
			$selected = '';
		}
		$output .= "<option value=\"$value\"$selected>$caption</option>";
	}
	return $output;
}

function debug()
{
	global $db;
	$output = func_get_args();
	$files  = array();
	foreach(debug_backtrace() AS $dt)
	{
		if(!empty($dt['file']))
		{
			if (!empty($dt['file']))
			{
				$dt['file'] = $db->fixPath($dt['file']);
				if ($dt['file'] != 'includes/system/layout.modules.php')
				{
					$files[] = $dt['file'].':'.$dt['line'];
				}else{
					break;
				}
			}
		}
	}
	$output[] = $files;
	call_user_func_array('pr', $output);
}

function is_checked($data, $value = '1', $def_checked = false)
{
	if(isset($value))
	{
		$output = ($data==$value) ? ' checked="checked"' : '';
	}else{
		$output = ($def_checked) ? ' checked="checked"' : '';
	}
	return $output;
}
function rating($value, $table='', $table_id='', $string_voter = 'voter', $string_db = '')
{
	$total_voters = $grade = 0;
	if(!empty($value))
	{
		$r = explode(',', $value);
		$total_voters = array_sum($r);
		foreach($r AS $i => $voters)
		{
			$grade += $voters * ($i + 1);
		}
		$grade = ($grade > 0) ? round($grade / $total_voters, 1) : 0;
		$grade = floor($grade * 2) / 2;
	}
	ob_start();
	if (empty($table_id) || empty($table) ||
		!empty($_SESSION['bbc_rating'][$table][$table_id]))
	{
		echo '<span class="rating">';
		for ($i=0; $i < 5; $i++)
		{
			$c = $grade <= $i ? '-o' : ($grade < ($i+1) ? '-half-o' : '');
			echo icon('fa-star'.$c);
		}
		echo '&nbsp;'.items($total_voters, $string_voter).'</span>';
	}else{
		icon('fa-star');
		$token = array(
			'table' => $table,
			'voter' => $string_voter,
			'db'    => $string_db,
			);
		?>
		<input type="number" name="rating" class="rating" value="<?php echo $grade; ?>" data-id="<?php echo $table_id; ?>" data-token="<?php echo encode(json_encode($token)); ?>" data-append="&nbsp;<?php echo items($total_voters, $string_voter); ?>" style="display: none;" />
		<?php
		link_js('templates/admin/bootstrap/js/rating.js', false);
	}
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
function output_json($array)
{
	$output = '{}';
	if (!empty($array))
	{
		if (is_object($array))
		{
			$array = (array)$array;
		}
		if (!is_array($array))
		{
			$output = $array;
		}else{
			if (defined('JSON_PRETTY_PRINT'))
			{
				$output = json_encode($array, JSON_PRETTY_PRINT);
			}else{
				$output = json_encode($array);
			}
		}
	}
	header('content-type: application/json; charset: UTF-8');
	header('cache-control: must-revalidate');
	header('expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
	echo $output;
	exit();
}
function tpl_scan($path='', $template='')
{
	$output = array();
	if (empty($path))
	{
		$bt   = debug_backtrace();
		$path = dirname($bt[0]['file']);
	}
	$path = str_replace(_ROOT, '', $path);
	$path.= substr($path, -1)!='/' ? '/':'';
	$path = _mst($path);
	_func('path');
	$r = path_list(_ROOT.$path);
	foreach ($r as $f)
	{
		if (preg_match('~^(.*?)\.html\.php$~', $f, $m))
		{
			$output[] = $m[1];
		}
	}
	if (empty($template))
	{
		$template = config('template');
	}
	$r = path_list(_ROOT.'templates/'.$template.'/'.$path);
	foreach ($r as $f)
	{
		if (preg_match('~^(.*?)\.html\.php$~', $f, $m))
		{
			$output[] = $m[1];
		}
	}
	$output = array_unique($output);
	sort($output);
	return $output;
}