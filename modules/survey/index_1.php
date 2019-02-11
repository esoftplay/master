<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if(isset($_POST['Submit']))
{
	if(isset($_POST['ids']) && count($_POST['ids']) > 0)
	{
		survey_sess('index', $_POST['ids']);
		redirect($Bbc->mod['circuit'].'.index_2');
	}else{
		$errorMsg = lang('No Selection');
	}
}
if( !empty($errorMsg) ){
	echo msg( $errorMsg);
}
?>
<p><?php echo lang('please select');?></p>
<form action="" method="post">
	<ul class="list">
		<?php
		$q = "SELECT * FROM survey_question AS q LEFT JOIN survey_question_text AS t
				ON (q.id=t.question_id AND t.lang_id=".lang_id().") WHERE publish=1 ORDER BY orderby";
		$r = $db->getAll($q);
		foreach($r AS $data)
		{
			$checked = ($data['checked']) ? ' checked' : '';
			?>
			<li>
				<input type="checkbox" name="ids[]" value="<?php echo $data['id'];?>" id="select<?php echo $data['id'];?>"<?php echo $checked;?>>
				<label for="select<?php echo $data['id'];?>"><?php echo $data['title'];?></label>
				<p><?php echo $data['description'];?></p>
			</li>
			<?php
		}
		?>
	</ul>
	<p class="button">
		<input type="Button" name="Button" value="&#171; Back" class="btn" onClick="window.history.go(-1);" />
		<input type="Submit" name="Submit" value="Next &#187;" class="btn" />
	</p>
</form>
