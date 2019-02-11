<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if(_SEO)
{
	$add_js = ' onSubmit="return submit_search(this);"';
	$add_input = '<input type="hidden" id="search_action_url" value="'.site_url($Bbc->mod['circuit'].'.result').'/" />';
	$sys->link_js($Bbc->mod['url'].'search.php');
}else{
	$add_js = $add_input = '';
}
include tpl('search-database.html.php');
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
		$v =& $result[$k];
		$function = $v['search_func'];
		$d = $function($v);
		$v['title'] = $d['title'];
		$v['link'] = $d['link'];
		$v['description'] = $d['description'];
		unset($v);
	}
//	adodb_pr($result);
	$to = $limitstart + count($result);
	$limitstart++;
	if(!empty($result))
	{
		echo '<h4>'.lang('Results').' '.$limitstart.' - '.$to.' '.lang('of').' '.$total.' '.lang('for').' <strong>'.$keyword.'</strong>'.'</h4>';
	}
include tpl('result-database.html.php');
}
