<form action="" method="post">
	<legend><?php echo lang('please select');?></legend>
	<ul class="list-group">
		<?php
		$q = "SELECT * FROM survey_question AS q LEFT JOIN survey_question_text AS t
				ON (q.id=t.question_id AND t.lang_id=".lang_id().") WHERE publish=1 ORDER BY orderby";
		$r = $db->getAll($q);
		foreach($r AS $data)
		{
			$checked = ($data['checked']) ? ' checked' : '';
			?>
			<li class="list-group-item">
				<p class="checkbox">
					<label for="select<?php echo $data['id'];?>">
						<input type="checkbox" name="ids[]" value="<?php echo $data['id'];?>" id="select<?php echo $data['id'];?>"<?php echo $checked;?>>
						<?php echo $data['title'];?>
					</label>
				</p>
				<p><?php echo $data['description'];?></p>
			</li>
			<?php
		}
		?>
	</ul>
	<p class="button">
		<input type="Submit" name="Submit" value="Next &#187;" class="btn" />
	</p>
</form>