<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$msg = @$_SESSION[_ROOT.'system_tools_msg'];
if(!empty($_POST['submit_ftp']))
{
	$con_ftp = (array)@$_POST;
	$con_ftp['root'] .= (substr($con_ftp['root'], -1) == '/') ? '' : '/';
	$con = $ftp->connect($con_ftp);
	if($con)
	{
		if($ftp->changedir($con_ftp['root'].'includes/system/'))
		{
			$_SESSION[_ROOT.'system_tools_ftp'] = $con_ftp;
			redirect($Bbc->mod['circuit'].'.tools');
		}else{
			$msg = 'Please provide the correct FTP root directory !';
		}
	}else{
		$msg = 'FTP username/password is incorrect !';
	}
}
if(!empty($msg)) echo msg($msg);
unset($_SESSION[_ROOT.'system_tools_msg'], $_SESSION[_ROOT.'system_tools_ftp']);
if(empty($con_ftp['hostname']))	$con_ftp['hostname'] = $_SERVER['HTTP_HOST'];
if(empty($con_ftp['root']))	$con_ftp['root'] = '/';
?>
<form method="POST" action="" name="ftp" enctype="multipart/form-data" role="form">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Login to FTP</h3>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label>Hostname</label>
				<input name="hostname" type="text" value="<?php echo $con_ftp['hostname'];?>" class="form-control" title="Hostname" placeholder="Hostname">
			</div>
			<div class="form-group">
				<label>FTP Username</label>
				<input name="username" type="text" value="<?php echo @$con_ftp['username'];?>" class="form-control" title="FTP Username" placeholder="FTP Username">
			</div>
			<div class="form-group">
				<label>FTP Password</label>
				<input name="password" type="text" value="<?php echo @$con_ftp['password'];?>" class="form-control" title="FTP Password" placeholder="FTP Password">
			</div>
			<div class="form-group">
				<label>FTP Root Directory</label>
				<input name="root" type="text" value="<?php echo @$con_ftp['root'];?>" class="form-control" title="FTP Root Directory" placeholder="FTP Root Directory">
				<div class="help-block">Eg. /public_html/ Or /htdocs/</div>
			</div>
		</div>
		<div class="panel-footer">
			<button name="submit_ftp" type="submit" value="LOGIN" class="btn btn-primary btn-sm">
				<span class="glyphicon glyphicon-log-in"></span>
				LOGIN
			</button>
			<button type="reset" class="btn btn-warning btn-sm">
				<span class="glyphicon glyphicon-repeat"></span>
				RESET
			</button>
		</div>
	</div>
</form>
<?php
echo explain('PS : <i>To perform the requested action, Fisip.Net needs to access to your web server. Please enter your FTP credentials to proceed. If you do not remember your credentials, you should contact your web host.</i>');