<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$code		= $_GET['id'];
$r_lang = get_lang();
$lang_id = array_search($code, $r_lang);
if($lang_id > 0)
{
	$_SESSION['lang_id'] = $lang_id;
	if (!empty($_GET['return']))
	{
		$url_length = strlen(_URL);
		$old_code  = substr($_GET['return'], $url_length, 2);
		if (preg_match('~[a-z]+~is', $old_code))
		{
			$new_lang_id   = array_search($old_code, $r_lang);
			if ($new_lang_id != $lang_id)
			{
				$_GET['return'] = preg_replace('~^'._URL.$old_code.'/~is', _URL.$code.'/', $_GET['return']);
			}
		}
	}

	redirect();
}
