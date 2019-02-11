<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

// Menampilkan data statistic website, dari jumlah penngunjung, jumlah user aktif saat itu, serta berapa lama website ini telah di publikasikan. Anda bisa menentukan apa aja yang ingin anda tampilkan dari opsi2 yang telah tersedia
$output = array();
if($config['total_visit'])
{
	$total 					 = $db->getOne("SELECT auto_increment FROM information_schema.tables WHERE table_name='bbc_log'")-1;
	if(!$total)
	$total           = $db->getOne("SELECT LAST_INSERT_ID(id) FROM bbc_log");
	$output['total_visit'] = $total;
}
if($config['total_member'])
{
	$total = $db->getOne("SELECT COUNT(*) FROM bbc_user");
	$output['total_member'] = $total;
}
if($config['member_online'])
{
	$tmp_time = (config('logged','period') == 'SECOND') ? 'SECOND' : config('logged','period').'_SECOND';
	$add_sql = "WHERE exp_checked > DATE_ADD(exp_checked, INTERVAL '-".config('logged','duration')." 900' $tmp_time)";
	$q = "SELECT COUNT(*) FROM bbc_user $add_sql";
	$total = $db->getOne($q);
	$output['member_online'] = $total;
}
if($config['user_online'])
{
	$q = "SELECT COUNT(*) FROM bbc_log WHERE `datetime` > DATE_ADD(NOW(), INTERVAL -".$config['interval_time']." SECOND)";
	$total = $db->getOne($q);
	$output['user_online'] = $total;
}
if($config['active_days'])
{
	_func('date');
	$timespan = timespan(strtotime($config['start_days']), '', array('year', 'month', 'week', 'day'));
	$output['timespan'] = $timespan;
}
include tpl(@$config['template'].'.html.php', 'statistic.html.php');
