<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

include_once _ROOT.'modules/content/_function.php';
include_once '_class.php';
$Agenda = new agenda_class();
$id		= @$_GET['id'];
$r		= explode('-', $id);
$year	= isset($r[0]) ? $r[0] : 'none';
$month= isset($r[1]) ? $r[1] : 'none';
$date	= isset($r[2]) ? $r[2] : 'none';
$page	= @intval($_GET['page']);
$page = ($page >= 0) ? $page : 0;
$id		= intval($id);
// Untuk menampilkan data agenda beserta detail informasi per agenda yang telah dibuat
switch($Bbc->mod['task'])
{
	case 'main': // menampilkan semua agenda baik jadwal yang akan datang maupun yang telah lalu
	case 'agenda': // alias dari task "main"
		$Agenda->main();
		$page = @intval($_GET['page_e']);
		$start= $page*$Agenda->limit;
		$q = "SELECT SQL_CALC_FOUND_ROWS a.content_id, t.title FROM agenda AS a LEFT JOIN bbc_content_text AS t ON (t.content_id=a.content_id
		AND t.lang_id=".lang_id().")	WHERE publish=1 AND cat_id=1 AND DATE(end_date) < CURDATE() ORDER BY `start_date` DESC LIMIT $start, ".$Agenda->limit;
		$r = $db->getAll($q);
		$t = $db->GetOne("SELECT FOUND_ROWS()");
		if(!empty($t))
		{
			include tpl('agenda.html.php');
		}
		break;
	case 'calendar': // menampilkan kalender beserta informasi event jika ditanggal tertentu terdapat acara
		if(!$sys->menu_real)	$sys->nav_add(lang('Agenda Calendar'));
		$Agenda->calendar($year, $month, $page);
		break;
	case 'routine': // daftar acara rutin (berkala)
		$Agenda->routine(@$_GET['id']);
		break;
	case 'events': // daftar acara yang jadwalnya hanya sekali saja
		$Agenda->events($year, $month, $date, $page);
		$page = @intval($_GET['page_e']);
		$start= $page*$Agenda->limit;
		$q = "SELECT SQL_CALC_FOUND_ROWS a.content_id, t.title FROM agenda AS a LEFT JOIN bbc_content_text AS t ON (t.content_id=a.content_id
		AND t.lang_id=".lang_id().")	WHERE publish=1 AND cat_id=1 AND DATE(end_date) < CURDATE() ORDER BY `start_date` DESC LIMIT $start, ".$Agenda->limit;
		$r = $db->getAll($q);
		$t = $db->GetOne("SELECT FOUND_ROWS()");
		if(!empty($t))
		{
			include tpl('events.html.php');
		}
		break;
	case 'daily': // daftar acara harian (jika ada atau telah dibuat oleh admin)
		$Agenda->daily($id);
		break;
	case 'weekly': // daftar acara mingguan (jika ada atau telah dibuat oleh admin)
		$Agenda->weekly($id);
		break;
	case 'monthly': // daftar acara bulanan (jika ada atau telah dibuat oleh admin)
		$Agenda->monthly($id);
		break;
	case 'yearly': // daftar acara tahunan (jika ada atau telah dibuat oleh admin)
		$Agenda->yearly($id);
		break;

	default:
		echo "Invalid action <b>".$Bbc->mod['task']."</b> has been received...";
		break;
}
