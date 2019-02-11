<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$keyword    = @$_GET['keyword'];
$limitstart = @intval($_GET['page']);
_func('search');

if(!empty($keyword))
{
	$lang_id		= lang_id();
	$conf 			= get_config('search', 'search');
	$limit 			= $conf['per_page'];
	$limitstart = $limitstart * $limit;
	$q_keyword 	= addslashes($keyword);
	//jika di admin milih search from all
	if ($conf['from'] == 1)
	{
		$r = $db->cacheGetAll("SELECT id, name, protected, allow_group FROM bbc_module WHERE search_func <> '' AND active=1 ORDER BY name", 3600);
		$conf['module'] = search_permission($r, $user);
	}else
	if (!$conf['module']) {
		$conf['module'] = $sys->get_module_id('content');
	}else{
		$r = $db->cacheGetAll("SELECT id, name, protected, allow_group FROM bbc_module WHERE id IN(".implode(',', $conf['module']).") ORDER BY name", 3600);
		$conf['module'] = search_permission($r, $user);
	}

	//search function
	$modules = implode(',', $conf['module']);
	$search_func = $db->cacheGetAssoc("SELECT id, name, search_func FROM bbc_module WHERE id IN ($modules)", 3600);

	$calc = 'SQL_CALC_FOUND_ROWS';
	$sql = array();
	foreach ((array)$search_func as $i => $d)
	{
		@list($func, $table, $title, $description) = explode('#', $d['search_func']);
		$table			.= empty($table) ? $d['name'].'_text' : '_text';
		if(empty($title)) 		$title			= '`title`';
		if(empty($description))$description = '`description`';
		$pkey		= $db->cacheGetOne("SHOW COLUMNS FROM $table WHERE `Field` LIKE '%_id' AND `Field` <> 'lang_id'", 3600);
		$indexes= $db->cacheGetAll("SHOW INDEX FROM $table WHERE index_type = 'FULLTEXT'", 3600);
		$module	= $d['name'];
		include_once(_ROOT.'modules/'.$d['name'].'/_function.php');
		$fulltext = array();
		foreach ((array)$indexes as $index)
		{
			$fulltext[] = $index['Column_name'];
		}
		$fulltext = implode(', ', $fulltext);

		$sql[$table] = "
			(SELECT
				$calc
				$pkey AS id
				, $title AS title
				, $description AS description
				, MATCH ($fulltext) AGAINST ('$q_keyword' IN BOOLEAN MODE) relevance
				, '".$func."' AS search_func
			FROM
				$table
			WHERE
				lang_id = $lang_id
				AND MATCH ($fulltext) AGAINST ('$q_keyword' IN BOOLEAN MODE))
		";
		$calc = '';
	}

	if (!empty($sql))
	{
		$sql = implode('UNION', $sql) . "ORDER BY relevance DESC LIMIT $limitstart, $limit";
		$result = $db->cacheGetAll($sql, 3600);
		$total = $db->cacheGetOne("SELECT FOUND_ROWS(), '$q_keyword' AS keyword", 3600);
	}else{
		$result = array();
		$total = 0;
	}

	foreach ((array)$result as $k => $v)
	{
		$v          =& $result[$k];
		$function   = $v['search_func'];
		$d          = $function($v);
		$v['title'] = $d['title'];
		$v['url']   = $d['link'];
		$v['intro'] = $d['description'];
		unset($v['description'], $v);
	}
//	adodb_pr($result);
	$output['total']      = @intval($total);
	$output['total_page'] = ceil($total/$conf['per_page']);
	$output['list']       = $result;

	$data_output = _cpanel_result($output);
}
