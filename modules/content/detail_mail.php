<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
if(!$id) $sys->denied();

$data = content_fetch($id, true);
if(empty($data)) $sys->denied();

$config = $data['config'];
if($data['publish'] && $config['email'])
{
	meta_title($data['title'], 2);
	meta_desc($data['description'], 2);
	meta_keyword($data['keyword'], 2);
	$link = content_link($id, $data['title']);
	$Msg = '';
	if(isset($_POST['to']))
	{
		if(empty($_POST['from'])) {
			$Msg = lang('email tool from empty');
		}elseif(!is_email($_POST['from'])) {
			$Msg = lang('email tool from invalid');
		}elseif(empty($_POST['to'])) {
			$Msg = lang('email tool to empty');
		}elseif(empty($_POST['subject'])) {
			$Msg = lang('email tool subject empty');
		}else{
			$emails = array();
			$to = preg_replace("/[\,\s]/s", ";", $_POST['to']);
			$arr = explode(';', $to);
			foreach($arr AS $to)
			{
				$to = trim($to);
				if(is_email($to))
					$emails[] = $to;
			}
			if(count($emails) > 0)
			{
				_func('sendmail');
				sendmail(
					$emails
				, config('email', 'subject').$_POST['subject']
				, $_POST['message']."\n\n".$link."\n\n".config('email', 'footer')
				, array($_POST['from'], $_POST['from']));

				$_SESSION['detail_mail'] = '<b>'.$data['title'].'</b> '.lang('email tool success');
				redirect($Bbc->mod['circuit'].'.detail_mail_post');
			}else{
				$Msg = lang('email tool to invalid');
			}
		}
	}
		$_POST['from']		= isset($_POST['from']) ? $_POST['from'] : user_email();
		$_POST['subject'] = isset($_POST['subject']) ? $_POST['subject'] : $data['title'];
		$_POST['message'] = isset($_POST['message']) ? stripslashes($_POST['message']) : lang('email tool message default');
		$sys->stop();
		?>
	<html lang="en">
		<head><?php echo $sys->meta();?></head>
		<body style="background: #ffffff;text-align: left;">
			<script type="text/JavaScript">
				function tell_friend(obj) {
					var passed = false;
					var emailExp= /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
					with (obj)
					{
						if (from.value == "") {
							alert("<?php echo lang('email tool from empty');?>");
							from.focus();
						} else if (from.value != "" && !emailExp.test(from.value) ) {
							alert("<?php echo lang('email tool from invalid');?>");
							from.focus();
						} else if (to.value == "") {
							alert("<?php echo lang('email tool to empty');?>");
							to.focus();
						} else if (subject.value == "") {
							alert("<?php echo lang('email tool subject empty');?>");
							subject.focus();
						}else{
							passed = true; 
						}
					}
					return passed;
				}
			</script>
			<form action="" method="post" onsubmit="return tell_friend(this);">
				<?php	if(!empty($Msg)) echo msg($Msg);?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo $data['title'];?></h3>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label><?php echo lang('email tool from');?></label>
							<input name="from" value="<?php echo @$_POST['from'];?>" class="form-control" type="text" />
						</div>
						<div class="form-group">
							<label><?php echo lang('email tool to');?></label>
							<input name="to" value="<?php echo @$_POST['to'];?>" class="form-control" type="text" />
						</div>
						<div class="form-group">
							<label><?php echo lang('email tool subject');?></label>
							<input name="subject" value="<?php echo @$_POST['subject'];?>" placeholder="http://" class="form-control" type="text" />
						</div>
						<div class="form-group">
							<label><?php echo lang('email tool message');?></label>
							<textarea name="message" class="form-control"><?php echo @$_POST['message'];?></textarea>
							<p class="help-block"><?php echo lang('email tool');?></p>
						</div>
					</div>
					<div class="panel-footer">
						<button type="submit" name="submit_comment" class="btn btn-primary btn-sm">
							<?php echo icon('send').' '.lang('Send');?></button>
						<button type="reset" class="btn btn-danger btn-sm" onClick="window.close();return false;">
							<?php echo icon('remove').' '.lang('Close');?></button>
					</div>
				</div>
			</form>
		</body>
	</html>
<?php
} ?>
