<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->set_layout('login.php');
$sys->link_set($sys->template_url.'css/login.css', 'css');
$sys->link_set('', 'js');
$msg = '';
if(preg_match('~^demo\.~is', @$_SERVER['HTTP_HOST']))
	$v = array('usr'=>' value="admin"','pwd'=>' value="123456"');
else	$v = array('usr'=>'','pwd'=>'');

$fail_txt = !empty($_SERVER['REMOTE_ADDR']) ? _CACHE.'failed_login/'.$_SERVER['REMOTE_ADDR'].'.txt' : '';
$fails_no = 0;
if (!empty($fail_txt))
{
	$fails_no = intval(file_read($fail_txt));
}
if ($fails_no >= 3)
{
	$msg = 'You\'ve been failed to login 3 times or more!';
}else
if(!empty($_POST['usr']))
{
	$output = user_login($_POST['usr'], $_POST['pwd'], '1');
	switch($output)
	{
		case 'allowed':
			redirect(_URL._ADMIN);
		break;
		case 'inactive':
			$msg = "Your account has been disabled.<br />For further information, please contact administrator";
		break;
		case 'notallowed':
			$msg = "Your account is not allowed to access this section.";
		break;
		case 'none':
			$msg = "Invalid Username or Password";
		break;
	}
}
if(!@empty($_SESSION[bbcAuth]['username']))
{
	$msg = 'Your Session Has Expired';
}
?>
<center>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<table width="350" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td style="width: 13px; height: 19px;"><img src="<?php echo $sys->template_url;?>images/logintopcleft.gif" /></td>
		<td style="width: 320px; height: 19px; background: url(<?php echo $sys->template_url;?>images/logintophline.gif) repeat-x left top;"><img src="<?php echo $sys->template_url;?>images/logintophline.gif" /></td>
		<td style="width: 17px; height: 19px;"><img src="<?php echo $sys->template_url;?>images/logintopcright.gif" /></td>
	</tr>
	<tr>
		<td style="width: 13px; height: 196px;"><img src="<?php echo $sys->template_url;?>images/loginvlineleft.gif" /></td>
		<td style="width: 320px; height: 196px; background: url(<?php echo $sys->template_url;?>images/loginmiddlecenter.gif) repeat-x left top;" valign="top">
		<div id="login-title"><img src="<?php echo $sys->template_url;?>images/loginform.gif" /></div>
		<div id="login-error"><?php echo $msg;?></div>
		<div id="login-form">
		<form name="login_form" action="" method="POST" target='_parent'>
			<input type="hidden" name="login" value="true" />
			<div id="login-input">Username:</div>
			<div id="login-input"><input type="text" name="usr" size="30"<?php echo $v['usr'];?> /></div>
			<div id="login-input">Password:</div>
			<div id="login-input"><input type="password" name="pwd" size="30"<?php echo $v['pwd'];?> /></div>
			<div id="login-button"><input name="submit" id="submitButton" type="submit" value="Login" class="button" onmouseover="this.className='button-hover';" onmouseout="this.className='button';" /></div>
		</form>
		</div>
		<div id="login-loading">Loading...</div>
		<div id="login-devby">Login with your <strong>password</strong>!</div>
		</td>
		<td style="width: 17px; height: 196px;"><img src="<?php echo $sys->template_url;?>images/loginvlineright.gif" /></td>
	</tr>
	<tr>
		<td style="width: 13px; height: 15px;"><img src="<?php echo $sys->template_url;?>images/loginbottomcleft.gif" /></td>
		<td style="width: 320px; height: 15px; background: url(<?php echo $sys->template_url;?>images/loginbottomhline.gif) repeat-x left top;"><img src="<?php echo $sys->template_url;?>images/loginbottomhline.gif" /></td>
		<td style="width: 17px; height: 15px;"><img src="<?php echo $sys->template_url;?>images/loginbottomcright.gif" /></td>
	</tr>
</table>
</center>
<div style="display:none;"><img src="<?php echo _URL;?>templates/admin/images/wallpapers.jpg"></div>
<?php
$sys->link_css($sys->template_url.'css/style.css', false);
$sys->link_css($sys->template_url.'css/home.css', false);
$sys->link_js($sys->template_url.'js/home.js', false);
$sys->link_js($sys->template_url.'bootstrap/js/bootstrap.min.js', false);
