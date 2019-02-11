<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

function minute_r($id = 'none')
{
	$output = array();
	for($i=0;$i < 60;$i++) {
		$t = strlen($i) == 1 ? '0'.$i : $i;
		$output[] = $t;
	}
	if($id == 'none') return $output;
	elseif(isset($output[$id])) return $output[$id];
	else return false;
}
function hour_r($id = 'none')
{
	$output = array();
	for($i=0;$i <= 24;$i++) {
		$t = strlen($i) == 1 ? '0'.$i : $i;
		$output[] = $t;
	}
	if($id == 'none') return $output;
	elseif(isset($output[$id])) return $output[$id];
	else return false;
}
function date_r($id = 'none')
{
	$output = array();
	for($i=0;$i < 31;$i++) {
		$t = strlen($i) == 1 ? '0'.$i : $i;
		$output[] = $t;
	}
	if($id == 'none') return $output;
	elseif(isset($output[$id])) return $output[$id];
	else return false;
}
function day_r($id = 'none')
{
	$output = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
	if($id == 'none') return $output;
	elseif(isset($output[$id])) return $output[$id];
	else return false;
}
function month_r($id = 'none')
{
	$output = array('january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december');
	if(isset($output[$id])) return $output[$id];
	else return $output;
}
function timespan($seconds = 1, $time = '', $show = array('year', 'month', 'week', 'day', 'hour', 'minute', 'second'), $max_show = 2)
{
	if ( ! is_numeric($seconds))
	{
		if (preg_match('~[0-9]+~s', $seconds))
		{
			if (preg_match('~0000\-00\-00~', $seconds))
			{
				return '';
			}
			$seconds = strtotime($seconds);
		}else{
			$seconds = 1;
		}
	}
	if ( ! is_numeric($time))
	{
		$time = time();
	}
	if ($time <= $seconds)
	{
		$seconds = 1;
	}else{
		$seconds = $time - $seconds;
	}
	$str = '';
	$years = floor($seconds / 31536000);
	if ($years > 0 && $max_show)
	{
		if(in_array('year', $show))
		{
			$str .= $years.' '.lang((($years	> 1) ? 'Years' : 'Year')).', ';
			$max_show--;
		}
	}
	$seconds -= $years * 31536000;
	$months = floor($seconds / 2628000);
	if (($years > 0 OR $months > 0) && $max_show)
	{
		if(in_array('month', $show))
		{
			if ($months > 0)
			{
				$str .= $months.' '.lang((($months	> 1) ? 'Months' : 'Month')).', ';
				$max_show--;
			}
		}
		$seconds -= $months * 2628000;
	}
	$weeks = floor($seconds / 604800);
	if (($years > 0 OR $months > 0 OR $weeks > 0) && $max_show)
	{
		if(in_array('week', $show))
		{
			if ($weeks > 0)
			{
				$str .= $weeks.' '.lang((($weeks	> 1) ? 'Weeks' : 'Week')).', ';
				$max_show--;
			}
		}
		$seconds -= $weeks * 604800;
	}
	$days = floor($seconds / 86400);
	if (($months > 0 OR $weeks > 0 OR $days > 0) && $max_show)
	{
		if(in_array('day', $show))
		{
			if ($days > 0)
			{
				$str .= $days.' '.lang((($days	> 1) ? 'Days' : 'Day')).', ';
				$max_show--;
			}
		}
		$seconds -= $days * 86400;
	}
	$hours = floor($seconds / 3600);
	if (($days > 0 OR $hours > 0) && $max_show)
	{
		if(in_array('hour', $show))
		{
			if ($hours > 0)
			{
				$str .= $hours.' '.lang((($hours	> 1) ? 'Hours' : 'Hour')).', ';
				$max_show--;
			}
		}
		$seconds -= $hours * 3600;
	}
	$minutes = floor($seconds / 60);
	if (($days > 0 OR $hours > 0 OR $minutes > 0) && $max_show)
	{
		if(in_array('minute', $show))
		{
			if ($minutes > 0)
			{
				$str .= $minutes.' '.lang((($minutes	> 1) ? 'Minutes' : 'Minute')).', ';
				$max_show--;
			}
		}
		$seconds -= $minutes * 60;
	}
	if(in_array('second', $show) && $max_show)
	{
		if ($str == '')
		{
			$str .= $seconds.' '.lang((($seconds	> 1) ? 'Seconds' : 'Second')).', ';
			$max_show--;
		}
	}
	return substr(trim($str), 0, -1);
}
function date_month($index, $char = 0)
{
	$output = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	$index = intval($index) - 1;
	$output = @$output[$index];
	if ($char > 0)
	{
		$output = substr($output, 0, $char);
	}
	return $output;
}
function date_interval( $date_start, $date_end='' , $month_char = 3)
{
	$output = '';
	if (!empty($date_start) && $date_start!='0000-00-00' && ($date_start==$date_end || $date_end=='0000-00-00'))
	{
		$output = date('M d Y', strtotime($date_start));
	}else{
		if (!preg_match('~[0-9]+\-[0-9]+\-[0-9]+~s', $date_start))
		{
			$date_start = date('Y-m-d', strtotime($date_start));
		}
		if (!preg_match('~[0-9]+\-[0-9]+\-[0-9]+~s', $date_end))
		{
			$date_end = date('Y-m-d', strtotime($date_end));
		}
		$date_start  = explode('-', $date_start);
		$date_end    = explode('-', $date_end);
		$c_start     = count($date_start);
		$c_end       = count($date_end);
		if ($c_start == $c_end)
		{
			if ($c_start>=3)
			{
				if ($date_start[0]==$date_end[0])
				{
		      if ($date_start[1] == $date_end[1])
		      {
		      	$date_start[0] = empty($date_start[0])||$date_start[0]=='0000' ? '' : $date_start[0].' ';
		        $output 	= $date_start[0].date_month($date_start[1], $month_char).' '.$date_start[2].' -&gt; '.$date_end[2];
		      }else{
		      	$date_start[0] = empty($date_start[0])||$date_start[0]=='0000' ? '' : $date_start[0].', ';
		        $output 	= $date_start[0].date_month($date_start[1], $month_char).' '.$date_start[2].' -&gt; '.date_month($date_end[1], $month_char).' '.$date_end[2];
		      }
		    }else{
		      $output 	= date_month($date_start[1], $month_char).' '.$date_start[2].' '.$date_start[0]
		      .' -&gt; '.	date_month($date_end[1], $month_char).' '.$date_end[2].' '.$date_end[0];
		    }
			}else
			if ($c_start==2) // bulan-tanggal saja -> 06-04
			{
	      if ($date_start[0] == $date_end[0])
	      {
	        $output 	= date_month($date_start[0], $month_char).' '.$date_start[1].' -&gt; '.$date_end[1];
	      }else{
	        $output 	= date_month($date_start[0], $month_char).' '.$date_start[1].' -&gt; '.date_month($date_end[0], $month_char).' '.$date_end[1];
	      }
			}else
			if ($c_start==1) // tanggal saja -> 04
			{
				if ($date_start[0] == $date_end[0])
				{
					$output = $date_start[0];
				}else{
					$output = $date_start[0].' -&gt; '.$date_end[0];
				}
			}
		}
	}
	return $output;
}
/*
menghitung brp hari lagi:
date_count($yesterday); = 1
date_count($today);     = 0
date_count($tomorrow);  = -1
*/
function date_count($date_start, $date_end='now')
{
	$datediff  = strtotime(date('Y-m-d', strtotime($date_end))) - strtotime(date('Y-m-d', strtotime($date_start)));
	return floor($datediff/(60*60*24));
}