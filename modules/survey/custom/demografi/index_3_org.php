<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

include_once $Bbc->mod['root'].'custom/demografi/_function.php';
$arr = array();
foreach((array)$sess['index_2'][$id]['ids'] AS $i => $d)
{
	switch($i)
	{
		case 1:
		case 2:
		case 3:
		case 4:
		case 5:
		case 6:
		case 7:
		case 8:
		case 9:
		case 10:
		case 12:
		case 13:
		case 14:
		case 15:
			$arr[$i] = array(
				'question'=> $d['question']
			,	'answer'	=> @$d[0]
			);
		break;
		case 11:
		case 17:
		case 18:
		case 19:
			$question = @$d['question'];
			unset($d['question']);
			$arr[$i] = array(
				'question'=> $question
			,	'answer'	=> implode(', ', (array)$d)
			);
		break;
		case 16:
			$question = @$d['question'];
			unset($d['question']);
			$arr[$i] = array(
				'question'=> $question
			,	'answer'	=> implode(' ', (array)$d)
			);
		break;
		case 20:
		case 21:
			$question = @$d['question'];
			unset($d['question']);
			$r	= array();
			foreach($d AS $_d)
			{
				if(!empty($_d['text']) && !empty($_d['rank']))
				{
					$j = survey_demogafi_ranking_check($r, $_d['rank']);
					$r[$j] = $_d['text'];
				}
			}
			ksort($r);
			reset($r);
			$answer = array();
			foreach($r AS $j => $_d)
			{
				$answer[] = $j.'. '.$_d;
			}
			$arr[$i] = array(
				'question'=> $question
			,	'answer'	=> implode('<br />', $answer)
			);
		break;
	}
}
$sess['index_2'][$id]['ids'] = $arr;
survey_sess('index_2', $sess['index_2']);
$valid = false;