<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

/*========================================*
 * USER ONLINE
 *========================================*/
$tmp_time = (config('logged','period') == 'SECOND') ? 'SECOND' : config('logged','period').'_SECOND';
$add_sql = "WHERE exp_checked > DATE_ADD(exp_checked, INTERVAL '-".config('logged','duration')." 900' $tmp_time)";
$form = _lib('pea',  $str_table = "bbc_user" );

$form->initRoll( $add_sql, 'id' );
$form->roll->setFormName( "memberonline" );

$form->roll->setDeleteTool(false);
$form->roll->setSaveTool(false);

$form->roll->addInput( 'username', 'sqllinks' );
$form->roll->input->username->setTitle( 'user' );
$form->roll->input->username->setExtra( 'rel="admin_link"' );
$form->roll->input->username->setLinks( 'index.php?mod=_cpanel.user&act=edit' );

$form->roll->addInput( 'last_ip', 'text' );
$form->roll->input->last_ip->setTitle( 'IP' );
$form->roll->input->last_ip->setPlaintext( true );

$form->roll->addInput( 'login_time', 'sqlplaintext' );
$form->roll->input->login_time->setTitle( 'times' );

$form->roll->addInput( 'links1', 'editlinks' );
$form->roll->input->links1->setTitle( 'logout' );
$form->roll->input->links1->setIcon( 'delete', 'logout this user' );
$form->roll->input->links1->setFieldName( 'id' );
$form->roll->input->links1->setGetName( 'id' );
$form->roll->input->links1->setLinks( 'index.php?mod=_cpanel.user&act=force2Logout');

$userOnline = $form->roll->getForm();

$form1 = _lib('pea',  $str_table = "bbc_log" );

$form1->initRoll( "WHERE datetime > DATE_ADD(datetime, INTERVAL '-".config('logged','duration')." 900' $tmp_time)", 'id' );
$form1->roll->setFormName( "useronline" );

$form1->roll->setDeleteTool(false);
$form1->roll->setSaveTool(false);

$form1->roll->addInput( 'ip', 'text' );
$form1->roll->input->ip->setTitle( 'Visitor IP' );
$form1->roll->input->ip->setPlaintext( true );

$form1->roll->addInput( 'datetime', 'datetime' );
$form1->roll->input->datetime->setTitle( 'Exp' );
$form1->roll->input->datetime->setPlaintext( true );

$userOnline .= '<p>&nbsp;</p>'.$form1->roll->getForm();

/*========================================*
 * Popular Content...
 *========================================*/
$add_sql = 'WHERE publish=1 AND is_front!=1 ORDER BY hits DESC';
$form2 = _lib('pea',  'bbc_content AS c LEFT JOIN bbc_content_text AS t ON(c.id=t.content_id AND lang_id='.lang_id().')' );

$form2->initRoll( $add_sql, 'id' );
$form2->roll->setFormName( "content" );
$form2->roll->setNumRows( 10 );

$form2->roll->setDeleteTool(false);
$form2->roll->setSaveTool(false);

$form2->roll->addInput( 'title', 'sqllinks' );
$form2->roll->input->title->setExtra( 'rel="admin_link"' );
$form2->roll->input->title->setLinks( 'index.php?mod=content.content_edit' );

$form2->roll->addInput( 'created', 'datetime' );
$form2->roll->input->created->setTitle( 'Created' );
$form2->roll->input->created->setPlaintext( true );


$form2->roll->addInput( 'hits', 'sqlplaintext' );
$form2->roll->input->hits->setTitle( 'Hits' );

$popular = $form2->roll->getForm();

/*========================================*
 * Control Panel Menu...
 *========================================*/
_func('tree');
ob_start();
?>
<div style="padding: 10px;border: 1px solid #ccc;">
	<?php echo tree_list($Bbc->menu->cpanel, 'Control Panel', false, true, '', _URL.'modules/_cpanel/admin/images/icon_');?>
	<script type="text/javascript">
		<!--
		d.openAll();
		_Bbc(function($){
			$(".node, .thumbnail").on("click", function(e){
				e.preventDefault();
				adminLink($(this).attr("href"));
			});
		});
		-->
	</script>
</div>
<?php
$cpanel_menu = ob_get_contents();
ob_end_clean();

/*========================================*
 * Start Display Content
 *========================================*/
$r_cpanel = array();
foreach($Bbc->menu->cpanel AS $dt)
	if($dt['par_id']==0)
		$r_cpanel[] = $dt;
?>
<div class="row">
	<div class="col-md-6 col-sm-6">
		<center>
<?php	foreach($r_cpanel AS $dt){	?>
				<div class="col-md-3 col-sm-3 col-xs-3">
		    	<a href="<?php echo $dt['link'];?>" title="<?php echo $dt['title'];?>" class="thumbnail">
		    		<img src="<?php echo $Bbc->mod['url'];?>images/<?php echo $dt['image'];?>" alt="<?php echo $dt['title'];?>" align="middle" />
		    		<small class="help-block"><?php echo $dt['title'];?></small>
		    	</a>
				</div>
<?php	}	?>
		</center>
		<div class="clearfix"></div>
	</div>
	<div class="col-md-6 col-sm-6">
<?php
		$tabs = array(
			'User Online'	=> $userOnline
		,	'Popular'			=> $popular
		,	'Panels'			=> $cpanel_menu
		);
		echo tabs($tabs);
?>
	</div>
</div>