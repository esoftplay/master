<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$position_id	= @intval($_GET['pos_id']);
$block_ref_id = @intval($_GET['ref_id']);
if($position_id == 0) {
	redirect();
}else{
	$sys->stop();
	if($block_ref_id==0)
	{
		$r_info = array();
		$path   = _ROOT.'blocks/';
		$r      = _func('path', 'list', $path);
		foreach ($r as $p)
		{
			$r_info[$p] = '';
			if (file_exists($path.$p.'/_switch.php'))
			{
				$txt = file_read($path.$p.'/_switch.php');
				if (preg_match('~(?:\n|\r)//([^\r\n]+)~', $txt, $match))
				{
					$r_info[$p] = $match[1];
				}
			}
		}
		$getname = $position_id;
		$linkto  = $Bbc->mod['circuit'].'.block&act=block_position_new'.$add_link.'&pos_id='.$position_id.'&ref_id=';
		$q       = "SELECT id, name FROM bbc_block_ref WHERE 1 ORDER BY name ASC";
		$r       = $db->getAssoc($q);
		$f       = $db->getAssoc("SELECT block_ref_id, COUNT(*) AS total FROM bbc_block WHERE template_id={$template_id} GROUP BY block_ref_id");
		$r2      = array();
		foreach ($r as $i => $d)
		{
			if (!empty($f[$i]))
			{
				$r[$i] .= ' ('.items($f[$i], 'time').' in used)';
			}
			$r2[$i] = @$r_info[$d];
		}
		?>
		<div class="form-group">
			<div class="list-group">
				<?php
				foreach ($r as $i => $d)
				{
					?>
				  <a href="<?php echo $linkto.$i; ?>" onclick="return block_ref_select(this);" class="list-group-item">
				    <h4 class="list-group-item-heading"><?php echo $d; ?></h4>
				    <p class="list-group-item-text"><?php echo @$r2[$i]; ?></p>
				  </a>
					<?php
				}
				?>
			</div>
		</div>
		<?php
	}else{
		$id = 0;
		include 'block_position_edit.php';
	}
}
