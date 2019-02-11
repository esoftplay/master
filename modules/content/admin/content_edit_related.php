<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$ids    = @$_POST['ids'];
$output = array(
	'success' => 0,
	'value'   => '',
	'html'    => explain('No content Found !', 'Sorry'));
if(!empty($ids))
{
	$ids = preg_replace(array('~^[^0-9]+~','~[^0-9]+$~','~[^0-9]+~', '~,{2,}~'), array('','',',',','), $ids);
	if(!empty($ids))
	{
		$ids = array_unique(explode(',',$ids));
		$q   = "SELECT `content_id`, `title` FROM `bbc_content_text` WHERE `lang_id`=".lang_id()." AND `content_id` IN (".implode(',', $ids).")";
		$r   = $db->getAssoc($q);
		$ar  = array();
		$is  = array();
		$url = _ADMIN ? 'index.php?mod=content.content_edit&id=' : _URL.'posted_form.htm/';
		foreach($ids AS $i)
		{
			$title = @$r[$i];
			if (empty($title))
			{
				$t = content_fetch($i);
				$title = @$t['title'];
			}
			if(!empty($title))
			{
				$_url   = $url.$i;
				$_url .= preg_match('~\?~s', $_url) ? '&' : '?';
				$j      = '<a href="'.$_url.'" title="Edit this page" onclick="document.location.href=this.href+\'return=\'+escape(document.location.href);return false;">'.$i.'</a>';
				$ar[$j] = '<a href="'._URL.'id.htm/'.$i.'" target="external" title="Go to this page">'.$title.'</a>';
				$is[]   = $i;
			}
		}
		if(!empty($ar))
		{
			$output = array(
				'success' => 1,
				'value'   => implode(',', $is),
				'html'    => table($ar)
				);
		}
	}
}
output_json($output); die();
