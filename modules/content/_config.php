<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if(isset($_seo['URI']))
{
	$_seo['cfg'] = get_config('content','manage');
	if (@$_seo['cfg']['webtype'] == '1')
	{
		$r = $db->getAll("SELECT * FROM bbc_content_schedule WHERE action_time < NOW() ORDER BY action_time ASC LIMIT 100");
		if (!empty($r))
		{
			foreach ($r as $schedule)
			{
				switch (@$schedule['action'])
				{
					case '1': // publish
						$q = "UPDATE `bbc_content` SET `publish`=1, `created`=NOW(), `modified`=NOW() WHERE `id`=".$schedule['content_id'];
						$db->Execute($q);
						break;
					case '2': // unpublish
						$q = "UPDATE `bbc_content` SET `publish`=0, `modified`=NOW() WHERE `id`=".$schedule['content_id'];
						$db->Execute($q);
						break;
					case '3': // delete
						_func('content', 'delete', $schedule['id']);
						break;
				}
				$db->Execute("DELETE FROM bbc_content_schedule WHERE id=".$schedule['id']);
			}
		}
	}
}

function content_config_frontpage()
{
	global $sys;
	$module_id = $sys->get_module_id('content');
	$opt = array(
		'yes' => array('1' => 'yes', '0' => 'no')
	,	'show'=> array('1' => 'show', '0' => 'hide')
	);
	$_cfg = array(
		'auto'       => array(
			'text'	=> 'Display latest content in front page',
			'type'	=> 'radio',
			'option'=> $opt['yes']),
		'title'      => array(
			'text'	=> 'Show Title',
			'type'	=> 'radio',
			'option'=> $opt['show']),
		'title_link' => array(
			'text'	=> 'Link title to content detail',
			'type'	=> 'radio',
			'option'=> $opt['yes']),
		'intro'      => array(
			'text'	=> 'Use intro instead of detail content for description',
			'type'	=> 'radio',
			'option'=> $opt['yes'],
			'tips'	=> 'use content\'s intro as description, otherwise content detail is taken'),
		'created'    => array(
			'text'	=> 'Show Date Created',
			'type'	=> 'radio',
			'option'=> $opt['show']),
		'modified'   => array(
			'text'	=> 'Show Modified On',
			'type'	=> 'radio',
			'option'=> $opt['show']),
		'author'     => array(
			'text'	=> 'Show author name',
			'type'	=> 'radio',
			'option'=> $opt['show']),
		'tag'        => array(
			'text'	=> 'Show content tags',
			'type'	=> 'radio',
			'option'=> $opt['show']),
		'tag_link'   => array(
			'text'	=> 'Link tag to its page',
			'type'	=> 'radio',
			'option'=> $opt['yes']),
		'rating'     => array(
			'text'	=> 'Show Rating (vote)',
			'type'	=> 'radio',
			'option'=> $opt['show']),
		'read_more'  => array(
			'text'	=> 'Show readmore link',
			'type'	=> 'radio',
			'option'=> $opt['show']),
		'tot_list'   => array(
			'text'	=> 'Total content to show in frontpage',
			'type'	=> 'text',
			'add'		=> 'Item(s)'),
		'thumbnail'  => array(
			'text'	=> 'Show content thumbnail',
			'type'	=> 'radio',
			'option'=> $opt['show'])
		);
	$output = array(
		'config'=> $_cfg,
		'name'	=> 'frontpage',
		'title'	=> 'Frontpage Parameters',
		'id'		=> $module_id
		);
	return $output;
}

function content_config_detail()
{
	global $sys;
	$module_id = $sys->get_module_id('content');
	$opt = array(
		'yes' => array('1' => 'yes', '0' => 'no')
	,	'show'=> array('1' => 'show', '0' => 'hide')
	);
	$tpl = array();
	$r   = tpl_scan(_ROOT.'modules/content/');
	foreach ($r as $t)
	{
		if (preg_match('~detail(?:\-(.*?))?$~', $t, $m))
		{
			$tpl[$t.'.html.php'] = empty($m[1]) ? 'default' : $m[1];
		}
	}
	$_cfg = array(
		'template' => array(
			'text'	=> 'Content Detail Template',
			'type'	=> 'select',
			'option'=> $tpl),
		'title' => array(
			'text'	=> 'Show Title',
			'type'	=> 'radio',
			'option'=> $opt['show']),
		'created' => array(
			'text'	=> 'Show Date Created',
			'type'	=> 'radio',
			'option'=> $opt['show']),
		'modified' => array(
			'text'	=> 'Show Modified On',
			'type'	=> 'radio',
			'option'=> $opt['show']),
		'author' => array(
			'text'	=> 'Show author name',
			'type'	=> 'radio',
			'option'=> $opt['show']),
		'tag' => array(
			'text'	=> 'Show content tags',
			'type'	=> 'radio',
			'option'=> $opt['show']),
		'tag_link' => array(
			'text'	=> 'Link tag to its page',
			'type'	=> 'radio',
			'option'=> $opt['yes']),
		'rating' => array(
			'text'	=> 'Show rating (vote)',
			'type'	=> 'radio',
			'option'=> $opt['show']),
		'rating_vote' => array(
			'text'	=> 'Allow user to vote in content rating',
			'type'	=> 'radio',
			'option'=> $opt['show']),
		'thumbsize' => array(
			'text'	=> 'Thumbnail Size',
			'type'	=> 'text',
			'add'		=> 'Pixel'),
		'comment' => array(
			'text'	=> 'Which type of Comment Form in content detail',
			'type'	=> 'select',
			'option'=> array('1' => 'Normal Form', '0' => 'Hide Comment Form', '2' => 'Use Facebook', '3' => 'Use disqus.com'),
			'tips'	=> 'all configuration below will not be considered by system if you select "Use Facebook" or "<a href="index.php?mod=content.config" rel="admin_link">Use disqus.com</a>"'),
		'comment_auto'  => array(
			'text'	=> 'Auto publish new incoming comment',
			'type'	=> 'radio',
			'option'=> $opt['yes']),
		'comment_list'  => array(
			'text'	=> 'how many comment to diplay per page',
			'type'	=> 'text',
			'add'		=> 'Item(s)'),
		'comment_form'  => array(
			'text'	=> 'Display comment form',
			'type'	=> 'radio',
			'option'=> $opt['show']),
		'comment_emoticons' => array(
			'text'	=> 'Display emoticon in comment form',
			'type'	=> 'radio',
			'option'=> $opt['show']),
		'comment_spam' => array(
			'text'	=> 'Protect comment form with captcha (avoid spam)',
			'type'	=> 'radio',
			'option'=> $opt['yes']),
		'comment_email' => array(
			'text'	=> 'Notify author for new comment entry',
			'type'	=> 'radio',
			'option'=> $opt['yes']),
		'pdf' => array(
			'text'	=> 'Display icon to export PDF',
			'type'	=> 'radio',
			'option'=> $opt['yes']),
		'print' => array(
			'text'	=> 'Display icon to print',
			'type'	=> 'radio',
			'option'=> $opt['yes']),
		'email' => array(
			'text'	=> 'Display icon to tell their friend by email',
			'type'	=> 'radio',
			'option'=> $opt['yes']),
		'share' => array(
			'text'	=> 'Display share icon',
			'type'	=> 'radio',
			'option'=> $opt['yes'])
		);
	$output = array(
		'config'=> $_cfg ,
		'name'	=> 'detail',
		'title'	=> 'Detail Parameters',
		'id'		=> $module_id
		);
	return $output;
}

function content_config_list()
{
	global $sys;
	$module_id = $sys->get_module_id('content');
	$opt = array(
		'yes' => array('1' => 'yes', '0' => 'no')
	,	'show'=> array('1' => 'show', '0' => 'hide')
	);
	$tpl = array();
	$r   = tpl_scan(_ROOT.'modules/content/');
	foreach ($r as $t)
	{
		if (preg_match('~list(?:\-(.*?))?$~', $t, $m))
		{
			$tpl[$t.'.html.php'] = empty($m[1]) ? 'default' : $m[1];
		}
	}
	$_cfg = array(
		'template' => array(
			'text'	=> 'Content Listing Template',
			'type'	=> 'select',
			'option'=> $tpl),
		'title' => array(
			'text'	=> 'Show Title',
			'type'	=> 'radio',
			'option'=> $opt['show']),
		'title_link' => array(
			'text'	=> 'Link title to its page',
			'type'	=> 'radio',
			'option'=> $opt['yes']),
		'intro' => array(
			'text'	=> 'Use intro instead of detail content for description',
			'type'	=> 'radio',
			'option'=> $opt['yes']),
		'created' => array(
			'text'	=> 'Show Date Created',
			'type'	=> 'radio',
			'option'=> $opt['show']),
		'modified' => array(
			'text'	=> 'Show Modified On',
			'type'	=> 'radio',
			'option'=> $opt['show']),
		'author' => array(
			'text'	=> 'Show author name',
			'type'	=> 'radio',
			'option'=> $opt['show']),
		'tag' => array(
			'text'	=> 'Show content tags',
			'type'	=> 'radio',
			'option'=> $opt['show']),
		'tag_link' => array(
			'text'	=> 'Link tag to its page',
			'type'	=> 'radio',
			'option'=> $opt['yes']),
		'rating' => array(
			'text'	=> 'Show rating (vote)',
			'type'	=> 'radio',
			'option'=> $opt['show']),
		'read_more' => array(
			'text'	=> 'Show readmore link',
			'type'	=> 'radio',
			'option'=> $opt['show']),
		'tot_list' => array(
			'text'	=> 'Total content to show per page',
			'type'	=> 'text',
			'add'		=> 'Item(s)'),
		'thumbnail' => array(
			'text'	=> 'Show content thumbnail',
			'type'	=> 'radio',
			'option'=> $opt['show'])
		);
	$output = array(
		'config'=> $_cfg ,
		'name'	=> 'list',
		'title'	=> 'List Parameters',
		'id'		=> $module_id
		);
	return $output;
}

function content_config_entry()
{
	global $sys, $db;
	$module_id = $sys->get_module_id('content');
	$q = "SELECT id, name FROM bbc_user_group WHERE is_admin=0";
	$g = $db->getAll($q);
	$t = $db->getAssoc("SELECT id, title FROM bbc_content_type WHERE active=1");
	_func('array');
	$curr = config('entry', 'cat_option') ? config('entry', 'cat_option') : array();
	$cats = array();
	$rcat = $db->getAll("SELECT `id`, `par_id`, `type_id`, `title` FROM `bbc_content_cat` AS c LEFT JOIN `bbc_content_cat_text` AS t ON (c.`id`=t.`cat_id` AND t.`lang_id`=".lang_id().") ");
	foreach ($rcat as $cat)
	{
		$cats[$cat['type_id']][] = $cat;
	}
	foreach ($t as $i => $j)
	{
		if (empty($cats[$i]))
		{
			$cats[$i] = array();
		}else{
			$cats[$i] = array_path($cats[$i]);
		}
	}
	$cat = $cats[config('entry', 'type_id')];
	$opt = array(
		'yes' => array('1' => 'yes', '0' => 'no')
	,	'show'=> array('1' => 'show', '0' => 'hide')
	);
	ob_start();
	?>
	<script type="text/javascript">
		var catOption= <?php echo json_encode($cats); ?>;
		var curCat= <?php echo json_encode($curr); ?>;
		_Bbc(function($){
			$("#entry\\[type_id\\]").on("change", function(){
				var cat = catOption[$(this).val()];
				var opt = "";
				var sel = "";
				for(var k in cat) {
					sel = $.inArray(k , curCat)!=-1 ? " selected" : "";
					opt += "<option value=\""+k+"\""+sel+">"+cat[k]+"</option>";
				}
				$("#entry\\[cat_option\\]\\[\\]").html(opt);
			}).trigger("change");
			$("input[name=entry\\[show_cat\\]]").on("click", function(){
				if ($(this).val()==2) {
					$("#entry\\[cat_option\\]\\[\\]").closest(".form-group").show();
				}else{
					$("#entry\\[cat_option\\]\\[\\]").closest(".form-group").hide();
				}
			});
			if ($("input[name=entry\\[show_cat\\]]:checked").val()!=2) {
				$("#entry\\[cat_option\\]\\[\\]").closest(".form-group").hide();
			}
		});
	</script>
	<?php
	$js = ob_get_contents();
	ob_end_clean();
	$_cfg = array(
		'groups' => array(
			'text'    => 'Select which user group is allowed to post content',
			'type'    => 'select',
			'is_arr'  => true,
			'option'  => $g
			),
		'auto' => array(
			'text'    => 'Which method is used for new content entry by users',
			'type'    => 'radio',
			'option'  => array(1=>'Auto Publish', 0=>'Publish by admin')
			),
		'alert' => array(
			'text'    => 'Notify admin by email for new content entry',
			'type'    => 'radio',
			'option'  => $opt['yes']
			),
		'address' => array(
			'text'    => 'Email Address',
			'type'    => 'text',
			'tips'    => 'if <i>Notify Admin</i> is "yes", then insert email destination or leave it blank to use <a href="index.php?mod=_cpanel.config" rel="admin_link">global configuration</a> in tab "email"'
			),
		'delete' => array(
			'text'    => 'User is allowed to delete their own content',
			'type'    => 'radio',
			'option'  => $opt['yes']
			),
		'type_id' => array(
			'text'    => 'Content Type',
			'type'    => 'select',
			'option'  => $t,
			'tips'    => 'Select which content type is allowed to post by users'.$js
			),
		'show_cat' => array(
			'text'    => 'Show Category Option',
			'type'    => 'radio',
			'option'  => array('1' => 'yes', '0' => 'no', '2' => 'custom'),
			'tips'    => 'Allow user to select in which category(s) their content will be posted in'
			),
		'cat_option' => array(
			'text'    => 'Please select public category where user can post their content',
			'type'    => 'select',
			'is_arr'  => true,
			'option'  => $cat
			),
		'tot'      => array(
			'text'    => 'Total content to show per page',
			'type'    => 'text',
			'default' => '12',
			'add'     => 'Item(s)'
			),
		'orderby'  => array(
			'text'    => 'Content Order By',
			'type'    => 'select',
			'option'  => array('1'=>'Last Posted','2'=>'First Posted', '3'=>'Popular (DESC)', '4'=>'Popular (ASC)'),
			'default' => '1'
			)
		);
	$output = array(
		'config'=> $_cfg
	,	'name'	=> 'entry'
	,	'title'	=> 'User Entries'
	,	'id'		=> $module_id
	);
	return $output;
}

function content_config_content()
{
	global $sys, $Bbc;
	$module_id = $sys->get_module_id('content');
	$_cfg = array(
		'webtype' => array(
			'text'    => 'Website Type',
			'type'    => 'select',
			'option'  => array('1' => 'News Article', '0' => 'Corporate' ),
			'default' => 0,
			'tips'    => 'Select which type of your website to manage your content/article. "News Article" is using content schedule, tags and related, and "Corporate" is only related content'),
		'image_size' => array(
			'text' => 'Large Image Size',
			'type' => 'text',
			'add'  => ' pixel',
			'tips' => 'This is where you can define maximum size of image to display in content detail Eg. 400 (will consider as 400x400)'),
		'images' => array(
			'text' => 'Default Image',
			'type' => 'file',
			'path' => $Bbc->mod['dir'],
			'tips' => 'This image will be used in content detail if content image doesn\'t exists'),
		'image_watermark' => array(
			'text'   => 'Watermark in content image',
			'type'   => 'checkbox',
			'option' => 'Use image below to place in every new images in content (works only for article and gallery content)'),
		'image_watermark_file' => array(
			'text' => 'Watermark Image',
			'type' => 'file',
			'path' => $Bbc->mod['dir'],
			'tips' => 'this image only work in PNG format, Normally watermark is smaller than the image size, then you should place your logo in any position you prefer in option below'),
		'image_watermark_position' => array(
			'text'   => 'Watermark Position',
			'type'   => 'select',
			'option' => array('center', 'top-left', 'top-right', 'bottom-left', 'bottom-right')),
		'is_nested' => array(
			'text'   => 'Content Relation',
			'type'   => 'radio',
			'option' => array(1=>'Nested Content', 0=>'Related Content'),
			'tips'   => 'This configuration is only affected when you create new content. Whether you insert the parent ID (Nested Content) or you insert multiple IDs to define content related (Related Content) '),
		'cat_img' => array(
			'text'   => 'Use Image for Content Category',
			'type'   => 'checkbox',
			'option' => 'Category Image',
			'tips'   => 'by activating the option, image field will display when you add/edit <a href="index.php?mod=content.category" rel="admin_link">content category</a>'),
		'disqus' => array(
			'text' => 'Default Disqus\'s shortname',
			'type' => 'text',
			'add'  => '<a href="https://disqus.com/admin/create/" target="_blank">Create Disqus Shortname</a>',
			'tips' => 'Create disqus account or insert your `disqus_shortname` if you already have one, this will be the default comment form if you select "Use disqus.com" for comment form. <a href="#" onclick="var a=$(\'input[name=manage\\\\[disqus\\\\]]\');if(a.val()!=\'\'){window.open(\'https://\'+a.val()+\'.disqus.com/admin/\', \'_blank\')}else{alert(\'Please insert your disqus_shortname!\');a.focus()}return false;">Click here</a> to manage your public comment')
		);
	$output = array(
		'config'=> $_cfg ,
		'name'	=> 'manage',
		'title'	=> 'Content Management',
		'id'		=> $module_id
		);
	return $output;
}
