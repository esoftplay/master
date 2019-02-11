<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if($prefix == 'edit_')
{
	$category_form_title = 'Edit Category';
}else{
	$category_form_title = 'New Category';
}
ob_start();
if(!empty($_POST)) include 'category-form-action.php';
$r_key_lang = array_keys($r_lang);
if($prefix == 'edit_')
{
	$q = "SELECT * FROM bbc_content_cat WHERE id={$category_id}";
	$data = $db->getRow($q);
	if($db->Affected_rows())
	{
		$q = "SELECT lang_id, title, description, keyword FROM bbc_content_cat_text WHERE cat_id=$category_id";
		$data['text'] = $db->getAssoc($q);
	}
	if(isset($data['is_config']) && $data['is_config']=='1')
	{
		$data['config'] = @config_decode($data['config']);
	}else{
		$data['config'] = $def_config;
	}
	$sys->nav_add('Edit Category');
	// CATEGORY PARENT
	$q = "SELECT id, par_id, title FROM bbc_content_cat AS c
	LEFT JOIN bbc_content_cat_text AS t ON (t.cat_id=c.id AND t.lang_id=".lang_id().")
	WHERE id!=".$data['id']." AND type_id=".$data['type_id']." ORDER BY par_id, title";
	$r_parent = array_path($db->getAll($q), 0, '>', '', '--');
}else{
	if(isset($data['id'])) {
		$q = "SELECT title FROM bbc_content_cat_text WHERE cat_id=".$data['id']." AND lang_id=".lang_id();
		$par_name = $db->getOne($q);
	}else{
		$par_name = "NO PARENT";
	}
	$data = array(
		'id'				=> 0
	,	'type_id'		=> $sub_content ? $type_id : (isset($data['type_id']) ? $data['type_id'] : 0)
	,	'par_id'		=> @intval($data['id'])
	,	'par_name'	=> $par_name
	,	'config'		=> $def_config
	,	'is_config' => '0'
	,	'publish'		=> '1'
	);
	$data['config'] = content_type($data['type_id'], 'list');
}
?>
<form method="POST" action="" name="<?php echo $prefix;?>" enctype="multipart/form-data">
	<div class="col-md-8">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo $category_form_title; ?></h3>
			</div>
			<div class="panel-body">
				<?php
				$q = "SELECT id, title FROM bbc_content_type WHERE active=1";
				$r_types = $db->getAll($q);
				if (count($r_types) > 1)
				{
					if($prefix == 'add_' && !$sub_content && $data['par_id']==0)
					{
						$q = "SELECT id, title FROM bbc_content_type WHERE active=1";
						?>
						<div class="form-group">
							<label>Type</label>
							<select name="<?php echo $prefix;?>type_id" class="form-control">
								<?php echo createOption($db->getAssoc($q), $data['type_id']); ?>
							</select>
						</div>
						<?php
					}else{
						if($prefix == 'edit_' && !$sub_content)
						{
							$type = $db->getOne("SELECT title FROM bbc_content_type WHERE id=".$data['type_id']);
							?>
							<div class="form-group">
								<label>Type</label>
								<div class="form-control-static">
									<?php echo $type; ?>
								</div>
							</div>
							<?php
						}
						?>
						<input name="<?php echo $prefix;?>type_id" type="hidden" value="<?php echo $data['type_id'];?>" />
						<?php
					}
				}else{
					?>
					<input name="<?php echo $prefix;?>type_id" type="hidden" value="<?php echo $r_types[0]['id'];?>" />
					<?php
				}
				?>
				<div class="form-group">
					<label>Parent Category</label>
					<div class="form-control-static">
						<?php
						if($prefix == 'edit_')
						{
							?>
							<select name="<?php echo $prefix;?>par_id" class="form-control">
								<option value="0">TOP PARENT</option>
								<?php echo createOption($r_parent, $data['par_id']);?>
							</select>
							<?php
						}else{
							echo $data['par_name'];
							?>
							<input name="<?php echo $prefix;?>par_id" type="hidden" value="<?php echo $data['par_id'];?>">
							<?php
						}
						?>
					</div>
				</div>
				<div class="form-group">
					<label>Title</label>
					<?php
					if(count($r_lang) > 1)
					{
						foreach($r_lang AS $lang_id => $d)
						{
							?>
							<input name="<?php echo $prefix;?>text[<?php echo $lang_id;?>][title]" type="text" class="form-control" value="<?php echo @$data['text'][$lang_id]['title'];?>" placeholder="<?php echo $d['title'];?>" />
							<?php
						}
					}else{
						?>
						<input name="<?php echo $prefix;?>text[<?php echo lang_id();?>][title]" type="text" class="form-control" value="<?php echo @$data['text'][lang_id()]['title'];?>" />
						<?php
					}
					if($prefix == 'edit_')
					{
						?>
						<div class="help-block">to visit this category page, please <a href="<?php echo _URL.'id.htm/cat_id,'.$data['id']; ?>" target="external">click here! <?php echo icon('fa-external-link', 'new window'); ?></a></div>
						<?php
					}
					?>
				</div>
				<?php
				if (config('manage', 'cat_img') == '1')
				{
					?>
					<div class="form-group">
						<label>Image Logo</label>
						<?php
						if($prefix == 'edit_')
						{
							if (!empty($data['image']) && file_exists($Bbc->mod['dir'].$data['image']))
							{
								$file = $Bbc->mod['dir'].$data['image'];
								$name = $data['image'];
								@list($w,$h) = @getimagesize($file);
								if (!empty($w) && !empty($h)) {
									$name .= ' ('.money($w).' x '.money($h).' px)';
								}
								$out = '<div class="checkbox"><img src="'.$Bbc->mod['image'].$data['image'].'" class="img-thumbnail formFile-clickable" />';
								$out.= '<br /><label><input type="checkbox" name="edit_image_del" val="1"> Delete Image</label>';
								$out.= ' &raquo; <em>'.$name.' &raquo; '.money(round(filesize($file)/1000)).' Kb</em>';
								$out.= '</div>';
								echo $out;
							}
						}
						?>
						<input name="<?php echo $prefix;?>image" type="file" class="form-control" value="" />
					</div>
					<?php
				}
				?>
				<div class="form-group">
					<label>Meta Description</label>
					<?php
					if(count($r_lang) > 1)
					{
						foreach($r_lang AS $lang_id => $d)
						{
							echo '<textarea name="'.$prefix.'text['.$lang_id.'][description]" class="form-control" placeholder="'.$d['title'].'">'.@$data['text'][$lang_id]['description'].'</textarea>';
						}
					}else{
						?>
						<textarea name="<?php echo $prefix;?>text[<?php echo lang_id();?>][description]" class="form-control"><?php echo @$data['text'][lang_id()]['description'];?></textarea>
						<?php
					}
					?>
				</div>
				<div class="form-group">
					<label>Meta Keyword</label>
					<?php
					if(count($r_lang) > 1)
					{
						foreach($r_lang AS $lang_id => $d)
						{
							echo '<textarea name="'.$prefix.'text['.$lang_id.'][keyword]" class="form-control" placeholder="'.$d['title'].'">'.@$data['text'][$lang_id]['keyword'].'</textarea>';
						}
					}else{
						?>
						<textarea name="<?php echo $prefix;?>text[<?php echo lang_id();?>][keyword]" class="form-control"><?php echo @$data['text'][lang_id()]['keyword'];?></textarea>
						<?php
					}
					?>
				</div>
				<div class="panel-group" id="accordionlist">
					<div class="panel panel-default">
					  <div class="panel-heading">
					    <h4 class="panel-title" data-toggle="collapse" data-parent="#accordionlist" href="#pea_isHideToolOn<?php echo $prefix;?>" style="cursor: pointer;">
								Content List Parameters
					    </h4>
							<a href="index.php?mod=content.type_edit&id=<?php echo $data['type_id']; ?>" title="Edit Default Value" class="pull-right admin_link" style="margin-top: -18px;">
								<?php echo icon('wrench', 'Edit Default Value'); ?>
							</a>
					  </div>
					  <div id="pea_isHideToolOn<?php echo $prefix;?>" class="panel-collapse collapse">
							<div class="panel-body">
									<div class="text-right">
										<label>
											<input type="checkbox" name="<?php echo $prefix;?>is_config" value="1" id="<?php echo $prefix;?>is_config"<?php echo is_checked($data['is_config']);?> />
											Set Special Parameter
										</label>
									</div>
								<div id="<?php echo $prefix;?>param">
									<?php
									include_once '../_config.php';
									$conf = content_config_list();
									$form_config = _class('bbcconfig');
									echo $form_config->show_param($conf['config'], $data['config'], 'null', 'config');
									?>
								</div>
							</div>
					  </div>
					</div>
				</div>
				<div class="form-group">
					<label>Publish</label>
					<div class="input-group checkbox">
						<label>
							<input type="checkbox" name="<?php echo $prefix;?>publish" value="1" id="<?php echo $prefix;?>publish"<?php echo is_checked($data['publish']);?> /> Published
						</label>
					</div>
				</div>
			</div>
			<div class="panel-footer">
				<?php
				if($category_id > 0)
				{
					echo '<span type="button" class="btn btn-default btn-sm" onclick="document.location.href=\''.$base_link.'\';"><span class="glyphicon glyphicon-chevron-left"></span></span> ';
				}
				?>
				<button type="submit" name="submit_<?php echo $prefix;?>update" default="true" value="&nbsp;SAVE&nbsp;" class="btn btn-primary btn-sm">
					<?php echo icon('floppy-disk'); ?>
					SAVE
				</button>
				<button type="reset" class="btn btn-warning btn-sm">
					<?php echo icon('repeat'); ?>
					RESET
				</button>
			</div>
		</div>
 	</div>
 	<div class="col-md-4">
 		<?php
 		include 'category-form-menu.php';
 		?>
 	</div>
</form>
<?php
$category_form = ob_get_contents();
ob_end_clean();

