<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

include_once '../_config.php';
/*
MEMASUKKAN TABLE CONTENT_TAGS AND COLOUMN `image` DI TABLE `bbc_content_cat`
*/
if (!empty($_POST['manage']))
{
	if (isset($_POST['manage']['webtype']))
	{
		$webtype = config('manage', 'webtype');
		if ($webtype != $_POST['manage']['webtype'])
		{
			if (!empty($_POST['manage']['webtype']))
			{
				/* CREATE NEW DB TABLE IF NOT AVAILABLE */
				$q = "CREATE TABLE IF NOT EXISTS `bbc_content_tag` (
					  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
					  `title` VARCHAR(255) DEFAULT '',
					  `total` int(11) DEFAULT 0,
					  `created` datetime DEFAULT '0000-00-00 00:00:00',
					  `updated` datetime DEFAULT '0000-00-00 00:00:00',
					  PRIMARY KEY (`id`),
					  KEY (`title`),
					  KEY (`total`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8";
				$db->Execute($q);
				$q = "CREATE TABLE IF NOT EXISTS `bbc_content_tag_list` (
						  `tag_id` int(255) DEFAULT '0',
						  `content_id` int(255) DEFAULT '0',
						  KEY `tag_id` (`tag_id`),
						  KEY `content_id` (`content_id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8";
				$db->Execute($q);
				$q = "CREATE TABLE IF NOT EXISTS `bbc_content_schedule` (
						  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						  `content_id` int(11) unsigned DEFAULT '0',
						  `action` tinyint(1) DEFAULT '0' COMMENT '1=publish, 2=unpublish, 3=delete',
						  `action_time` datetime DEFAULT '0000-00-00 00:00:00',
						  `created` datetime DEFAULT '0000-00-00 00:00:00',
						  PRIMARY KEY (`id`),
						  KEY `content_id` (`content_id`),
						  KEY `action_time` (`action_time`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8";
				$db->Execute($q);
				/* SET CONTENT IN BBC_MODULE USE CONFIG */
				$q = "UPDATE bbc_module SET is_config=1 WHERE name='content'";
				$db->Execute($q);

				/* CREATING MENU CONTENT_TAG IN ADMIN */
				$q = "SELECT 1 FROM bbc_menu WHERE `link`='index.php?mod=content.tag' AND is_admin=1";
				if (!$db->getOne($q))
				{
					$dt = $db->getRow("SELECT * FROM bbc_menu WHERE `link`='index.php?mod=content.type' AND is_admin=1");
					if (!empty($dt))
					{
						// move down all other menus below content type
						$db->Execute("UPDATE bbc_menu SET orderby=(orderby+1) WHERE `par_id`=".$dt['par_id']." AND orderby > ".$dt['orderby']);
						// create content tag menu
						unset($dt['id']);
						$dt['orderby']++;
						$dt['link'] = 'index.php?mod=content.tag';
						$fields = array();
						foreach ($dt as $key => $value)
						{
							$fields[] = "`{$key}`='{$value}'";
						}
						$db->Execute("INSERT INTO bbc_menu SET ".implode(', ', $fields));
						$new_id = $db->Insert_ID();
						if ($new_id)
						{
							$db->Execute("INSERT INTO bbc_menu_text SET `menu_id`={$new_id}, `title`='Content Tags', `lang_id`=".lang_id());
							echo msg("<a href=\""._URL."admin/\" target='_parent'>Click here</a> to refresh the page and get the latest menu for content tags", "danger");
						}
					}
				}
			}else{
				/* DISABLE CONTENT'S CONFIG IN BBC_MODULE */
				$q = "UPDATE bbc_module SET is_config=0 WHERE name='content'";
				$db->Execute($q);
			}
		}
	}
	$r = $db->getCol("SHOW COLUMNS FROM `bbc_content_cat`");
	if (!in_array('image', $r))
	{
		$db->Execute("ALTER TABLE `bbc_content_cat` ADD `image` VARCHAR(255)  NULL  DEFAULT ''  AFTER `type_id`");
	}
}
$conf = _class('bbcconfig');
$config_tabs = array();

$output = content_config_content();
$conf->set($output);
$config_tabs['Configuration'] = $conf->show();

$output = content_config_frontpage();
$conf->set($output);
$config_tabs['Front Page'] = $conf->show();

$output = content_config_entry();
$conf->set($output);
$config_tabs['User Entry'] = $conf->show();

include 'config_default.php';

echo tabs($config_tabs);