<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->nav_add('Activate Content Ads');
if (!empty($_POST['action']))
{
	$q = "CREATE TABLE IF NOT EXISTS `bbc_content_ad` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cat_id` int(11) DEFAULT '0' COMMENT 'based on bbc_content_cat',
  `type_id` tinyint(1) DEFAULT '0' COMMENT '0=logo n text, 1=banner, 2=text Only',
  `image` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `hit` int(11) DEFAULT '0' COMMENT 'how many time ad had been clicked',
  `hit_last` datetime DEFAULT '0000-00-00 00:00:00',
  `created` datetime DEFAULT '0000-00-00 00:00:00',
  `updated` datetime DEFAULT '0000-00-00 00:00:00',
  `displayed` datetime DEFAULT '0000-00-00 00:00:00',
  `expire` tinyint(1) DEFAULT '0' COMMENT '1=use expire time, 0=unlimited time of use',
  `expire_date` date DEFAULT '0000-00-00',
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `cat_id` (`cat_id`),
  KEY `hit` (`hit`),
  KEY `expire` (`expire`),
  KEY `expire_time` (`expire_date`),
  KEY `active` (`active`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	if ($db->Execute($q))
	{
		/* CREATING MENU CONTENT_TAG IN ADMIN */
		$q = "SELECT 1 FROM bbc_menu WHERE `link`='index.php?mod=content.ads' AND is_admin=1";
		if (!$db->getOne($q))
		{
			$dt = $db->getRow("SELECT * FROM bbc_menu WHERE `link`='index.php?mod=content.type' AND is_admin=1");
			if (!empty($dt))
			{
				// move down all other menus below content type
				$db->Execute("UPDATE bbc_menu SET orderby=(orderby+1) WHERE `par_id`=".$dt['par_id']." AND orderby > ".$dt['orderby']);
				// create content ads menu
				unset($dt['id']);
				$dt['orderby']++;
				$dt['link'] = 'index.php?mod=content.ads';
				$fields = array();
				foreach ($dt as $key => $value)
				{
					$fields[] = "`{$key}`='{$value}'";
				}
				$db->Execute("INSERT INTO bbc_menu SET ".implode(', ', $fields));
				$new_id = $db->Insert_ID();
				if ($new_id)
				{
					$db->Execute("INSERT INTO bbc_menu_text SET `menu_id`={$new_id}, `title`='3d Party Ads', `lang_id`=".lang_id());
					$sys->clean_cache();
					echo msg('This admin page will reload in <span id="timer_reload">10</span> seconds to get new menu', 'danger');
					?>
					<script type="text/javascript">
						_Bbc(function($){
							if (window.top != window.self) {
								window.toreload = 10;
								setInterval(function(){
									window.toreload--;
									if (window.toreload==0) {
										window.top.document.location.reload();
									}else{
										$("#timer_reload").html(window.toreload);
									}
								}, 1000);
							}else{
								document.location.href="index.php?mod=_cpanel.application";
							}
						});
					</script>
					<?php
				}
			}
		}
	}
}else{
	$f = $db->getCol("SHOW TABLES LIKE 'bbc_content_ad'");
	if (!empty($f))
	{
		redirect('index.php?mod=_cpanel.application');
	}
	?>
	<div class="jumbotron">
	  <h1>Do you want to activate Content Ads!</h1>
	  <p>Activating Content Ads, will create new submenu "Content Ads" under menu "Content" which is you can move to any location in admin menu</p>
	  <p>
		  <form action="" method="POST" class="form-inline" role="form">
		  	<input type="hidden" name="action" value="ok" />
		  	<button type="submit" class="btn btn-primary">Yes, activate it!</button>
		  </form>
	  </p>
	</div>
	<?php
}