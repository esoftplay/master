<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!empty($_SESSION[_class('comment')->sesname]))
{
	_class('comment')->session();
	?>
	<script type="text/javascript">
		if (window.opener) {
			if (window.opener.BS3) {
				var $ = window.opener.BS3;
				if ($('.form-comment').length) {
					var a = $('.form-comment');
					$('input[name="name"]', a).val('<?php echo @$user->name; ?>');
			  	$('input[name="email"]', a).val('<?php echo @$user->email; ?>');
			  	$('input[name="website"]', a).val('<?php echo @$user->website; ?>');
			  	$('input[name="image"]', a).val('<?php echo @$user->image; ?>');
			  	a.trigger("init");
				}
			}
		}
		window.close();
	</script>
	<?php
	die();
}
$id = @$_GET['id'];
if (empty($id))
{
	$id = 'facebook';
}
$data = $sys->login($id);
$url = $Bbc->mod['circuit'].'.'.$Bbc->mod['task'];
if (!empty($data['email']))
{
	if (!isset($data['website']))
	{
		$r = array('url', 'link', 'profileUrl');
		foreach ($r as $j)
		{
			if (isset($data[$j]))
			{
				$data['website'] = $data[$j];
			}
		}
	}
	if (empty($data['website']))
	{
		if (isset($_POST['submit']))
		{
			if ($_POST['submit']=='submit')
			{
				$website = $_POST['website'];
				if (is_url($website) || preg_match('~^[a-z][a-z0-9\-]+\.[a-z0-9\-\./]+$~is', $website))
				{
					$data['website'] = $website;
					_class('comment')->session($data);
					redirect($url);
				}else{
					echo msg(lang('Website is invalid URL'), 'danger');
				}
			}else{
				_class('comment')->session($data);
				redirect($url);
			}
		}
		$tpl = $sys->template_dir.'blank.php';
		if (!file_exists($tpl))
		{
			$tpl = _ROOT.'templates/admin/blank.php';
		}
		$sys->set_layout($tpl);
		?>
		<form action="" method="POST" role="form">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><?php echo lang('Insert your website if any');?></h3>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<input type="text" class="form-control" name="website" placeholder="http://" value="<?php echo @$_POST['website']; ?>" />
						<div class="help-block"><?php echo lang('Your name link to your website if any'); ?></div>
					</div>
				</div>
				<div class="panel-footer">
					<button type="submit" name="submit" value="submit" class="btn btn-default"><?php echo icon('fa-floppy-o').' '.lang('Submit'); ?></button>
					<button type="submit" name="submit" value="skip" class="btn btn-default pull-right"><?php echo lang('Skip').' '.icon('fa-angle-double-right'); ?></button>
				</div>
			</div>
		</form>
		<?php
	}else{
		_class('comment')->session($data);
		redirect($url);
	}
}