<div class="block_gui_position">
	<h2><?php echo $position_name;?></h2>
	<span class="hide_block" onclick="return block_hide(this, <?php echo $position_id;?>);" title="show/hide this <?php echo $position_name;?>"></span>
	<span class="add_block" onclick="return block_new(this, <?php echo $position_id;?>);" title="add block [<?php echo $position_name;?>]"></span>
	<div id="position_<?php echo $position_id;?>" class="groupWrapper">
		<?php
		foreach($blocks AS $id => $data)
		{
			$data['title'] = empty($data['title']) ? 'Untitled Block' : $data['title'];
			?>
			<div id="block_<?php echo $id;?>" class="groupItem">
				<div style="-moz-user-select: none;" class="itemHeader">
					<div id="title_<?php echo $id;?>"><?php echo $data['title'];?></div>
					<span class="delete" onclick="block_delete(this, <?php echo $id;?>);" title="delete [<?php echo $data['title'];?>]"></span>
					<span class="edit" onclick="block_edit(this, <?php echo $id;?>);" title="edit '<?php echo $data['name']; ?>' [<?php echo $data['title'];?>]"></span>
					<span class="info" onclick="block_info(this, <?php echo $id;?>);" title="info [<?php echo $data['title'];?>]"></span>
				</div>
				<div class="itemContent"></div>
			</div>
			<?php
		}
		?>
		<p>&nbsp;</p>
	</div>
</div>
