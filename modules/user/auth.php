<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!empty($_GET['id']))
{
	$data = $sys->login($_GET['id']);
	user_auth($data);
}
ob_start();
$url = seo_url();
$url.= preg_match('~\?~', $url) ? '&' : '?';
$url.= 'id=';
echo '<center><div class="btn-group" role="group">';
echo $sys->button($url.'facebook', 'Facebook', 'fa-facebook-official');
echo $sys->button($url.'twitter', 'Twitter', 'fa-twitter-square');
echo $sys->button($url.'google', 'Google', 'fa-google-plus-square');
echo $sys->button($url.'linkedin', 'Linkedin', 'fa-linkedin-square');
echo $sys->button($url.'yahoo', 'Yahoo', 'fa-yahoo');
echo '</div></center>';
$button = ob_get_contents();
ob_end_clean();
if (!empty($_GET['msg']))
{
	$text = $_GET['msg'].$button;
}else{
	$text = lang('We must validate your profile before continuing... Please select which account you would like to provide <br />%s', $button);
}
echo msg($text, 'warning');
