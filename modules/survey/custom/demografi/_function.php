<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

function survey_demografi_text($d, $i)
{
	global $id;
	$text = lang($d);
	return $text.'<input type="hidden" name="options['.$id.']['.$i.'][question]" value="'.htmlentities($text).'" style="display: none;" />';
}
function survey_demografi_input($i, $text='', $pre_text = 0)
{
	global $id;
	$out = '<input type="text" class="form-control" name="options['.$id.']['.$i.'][0]" value="'.@htmlentities($_POST['options'][$id][$i][0]).'" />';
	if (!empty($text))
	{
		if ($pre_text)
		{
			$out = '<div class="input-group"> <span class="input-group-addon">'.$text.'</span> '.$out.' </div>';
		}else{
			$out = '<div class="input-group"> '.$out.' <span class="input-group-addon">'.$text.'</span> </div>';
		}
	}
	return $out;
}
function survey_demografi_texts($n, $i, $attr='')
{
	global $id;
	$output = array();
	$k = 0;
	for($j=0;$j < $n;$j++)
	{
		$k++;
		$output[] = '<div class="input-group"> <span class="input-group-addon">'.money($k).'.</span> <input type="text" class="form-control" name="options['.$id.']['.$i.']['.$j.']" value="'.@$_POST['options'][$id][$i][$j].'"'.$attr.' /></div>';
	}
	return '<div class="form-group">'.implode('', $output).'</div>';
}
function survey_demografi_rangking($r, $i, $t)
{
	global $id;
	$r = is_array($r) ? $r : explode(';', $r);
	$output = '';
	if(!empty($r))
	{
		$output .= '<table style="width: 100%;">
	<tr style="font-style: italic;font-weight: bold;">
		<td style="width: 5px;">'.lang('No').'</td>
		<td>'.lang($t).'</td>
		<td style="width: 30px;">'.lang('Ranking').'</td>
	</tr>';
		$k = 0;
		foreach($r AS $j => $d)
		{
			$k++;
			if($d == 'text')
			{
				$text = '<input type="text" name="options['.$id.']['.$i.']['.$j.'][text]" value="'.@$_POST['options'][$id][$i][$j]['text'].'" size="40" />';
			}else{
				$text = lang($d);
				$text .= '<input type="hidden" style="display: none;" name="options['.$id.']['.$i.']['.$j.'][text]" value="'.htmlentities($text).'" />';
			}
		$output .= '
	<tr>
		<td>'.$k.'</td>
		<td>'.$text.'</td>
		<td><input type="text" name="options['.$id.']['.$i.']['.$j.'][rank]" value="'.@$_POST['options'][$id][$i][$j]['rank'].'" size="5" style="text-align: center;" /></td>
	</tr>';
		}
		$output .= '</table>';
	}
	return $output;
}
function survey_demogafi_ranking_check($arr, $i)
{
	$i = intval($i);
	if(empty($arr[$i])) return $i;
	else{
		$i++;
		return survey_demogafi_ranking_check($arr, $i);
	}
}
function survey_demografi_select($r, $i)
{
	global $id;
	return '<select name="options['.$id.']['.$i.'][0]" id="options['.$id.']['.$i.']" class="form-control">'.createOption(survey_demografi_array($r), @$_POST['options'][$id][$i][0]).'</select>';
}
function survey_demografi_option($r, $i)
{
	global $id, $sys, $Bbc;
	$output = array();
	$r = is_array($r) ? $r : explode(';', $r);
	if(!empty($r))
	{
		// $sys->link_js($Bbc->mod['url'].'custom/demografi/script.js', false);
		$y = count($r) + 1;
		foreach($r AS $j => $d)
		{
			if(!empty($d))
			{
				$is_text = preg_match('~_text$~is', $d) ? true : false;
				if($is_text)
				{
					$d = preg_replace('~_text$~is', '', $d);
				}
				$text = lang($d);
				$add = (!empty($_POST['options'][$id][$i][0]) && $_POST['options'][$id][$i][0] == $text) ? ' checked="checked"' : '';
				if($is_text)
				{
					$value = !empty($add) ? @$_POST['options'][$id][$i]['text_'.$j] : '';
					$text2 = $text.' : <input type="text" id="options_'.$id.'_'.$i.'_'.$j.'" name="options['.$id.']['.$i.'][text_'.$j.']" class="form-control" value="'.$value.'" />';
				}else $text2 = $text;
				$output[$j] = '<div class="form-group checkbox"><label><input type="radio" name="options['.$id.']['.$i.'][0]" id="options_'.$id.'_'.$i.'_'.$j.'_radio" value="'.$text.'" class="form-control"'.$add.' /> '.$text2.'</label></div>';
			}
		}
	}
	return '<div class="form-inline">'.implode('<br />', $output).'</div>';
}
function survey_demografi_checkbox($r, $i)
{
	global $id, $sys, $Bbc;
	$output = array();
	$r = is_array($r) ? $r : explode(';', $r);
	if(!empty($r))
	{
		// $sys->link_js($Bbc->mod['url'].'custom/demografi/script.js', false);
		foreach($r AS $j => $d)
		{
			if(!empty($d))
			{
				$text = lang($d);
				if($d == 'text')
				{
					$text = @$_POST['options'][$id][$i][$j];
					$text2 = '<input type="text" id="options_'.$id.'_'.$i.'_'.$j.'" class="form-control" value="'.$text.'" />';
				}else $text2 = $text.'';
				$add = (!empty($_POST['options'][$id][$i][$j]) && $_POST['options'][$id][$i][$j] == $text) ? ' checked="checked"' : '';
				$output[$j] = '<div class="form-group checkbox"><label><input type="checkbox" name="options['.$id.']['.$i.']['.$j.']" id="options_'.$id.'_'.$i.'_'.$j.'_checked" value="'.$text.'"'.$add.' /> '.$text2.'</label></div>';
			}
		}
	}
	return implode('', $output);
}
function survey_demografi_array($r)
{
	$output = false;
	$r = is_array($r) ? $r : explode(';', $r);
	foreach($r AS $i => $d)
	{
		$output[$i] = lang($d);
	}
	return $output;
}