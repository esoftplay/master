<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id   = @intval($_GET['id']);
$q    = "SELECT * FROM contact WHERE id=$id";
$data = $db->getRow($q);
if (empty($_GET['return']))
{
	$_GET['return'] = $Bbc->mod['circuit'].'.posted';
}
if($data['followed']) redirect($Bbc->mod['circuit'].'.posted_detail&id='.$id.'&return='.urlencode($_GET['return']));
$r_param = urldecode_r(config_decode($data['params']));
if(isset($_POST['submit']))
{
	$message = $_POST['answer'];
	if(isset($_POST['include_message']))
	{
		$c = get_config('contact', 'form');
		$email = is_email($c['email']) ? $c['email'] : config('email','address');
		$message .= '

----- Original Message -----
From: "'.$data['name'].'" <'.$data['email'].'>
To: <'.$email.'>
Sent: '.date('l, F jS, Y H:i:s', strtotime($data['post_date'])).'

'.$data['message'];
	}
	_func('sendmail');
	$tpl = $sys->mail_fetch('contact');
	sendmail(
	  array($data['email'], $email)
	, 'Re: '.$tpl['subject']
	, $message
	, array($email, config('email', 'name'))
	);
	$answer = $_POST['answer']."\n\n by ".$user->name;
	$q = "UPDATE contact SET answer='{$answer}', answer_date=NOW(), followed=1 WHERE id=$id";
	$db->Execute($q);
	redirect($Bbc->mod['circuit'].'.posted_detail&id='.$id);
}
_lib('pea', 'contact');
link_js(_PEA_URL.'includes/formIsRequire.js', false);
?>
<form action="" method="post" name="contact" enctype="multipart/form-data" class="formIsRequire">
	<table class="table table-hover">
		<thead>
			<tr>
				<td colspan=2><strong>Contact Answer</strong></td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td width="150px">Name</td>
				<td><?php echo $data['name'];?></td>
			</tr>
			<?php
			foreach((array)$r_param AS $title => $value)
			{
				?>
				<tr>
					<td><?php echo $title;?></td>
					<td><?php echo $value;?></td>
				</tr>
				<?php
			}
			?>
			<tr>
				<td>Email</td>
				<td><?php echo $data['email'];?></td>
			</tr>
			<tr>
				<td>Posted</td>
				<td><i><?php echo date( 'D - M jS, Y H:i:s', strtotime($data['post_date']));?></i></td>
			</tr>
			<tr>
				<th>Message</th>
				<td><?php echo nl2br($data['message']);?></td>
			</tr>
			<tr>
				<th>Answer</th>
				<td>
					<textarea name="answer" class="form-control" req="any true"></textarea>
				</td>
			</tr>
			<tr>
				<td>User message</td>
				<td>
					<label for="include_message">
						<input type="checkbox" name="include_message" value="1" id="include_message" checked="checked">
						Include user message
					</label>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td></td>
				<td>
					<?php echo $sys->button($_GET['return']); ?>
					<button type="submit" name="submit" value="Submit" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-floppy-disk"></span> Submit</button>
				</td>
			</tr>
		</tfoot>
	</table>
</form>
