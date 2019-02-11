<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

/*
// EXAMPLE HOW TO USE :
$cfg = array(
	'table'    => 'bbc_content_comment',
	'field'    => 'content',
	'id'       => $data['id'],	// id dari detail data misal content_id
	'par_id'   => 0,						// par_id jika ini reply message maka pastinya par_id > 0
	'type'     => 1,						// [1=Normal Form, 0=No Comment, 2=Facebook Comment]
	'list'     => 9,						// number of comment to show per page
	'link'     => 'url_to',			// current link
	'form'     => 1,						// show/hide comment form
	'emoticon' => 1,						// show/hide emoticon if form enable
	'captcha'  => 1,						// show/hide captcha in form comment if form enable
	'approve'  => 0,						// disable/enable auto publish if approve=0 admin must approve every comment manually
	'alert'    => 1,						// disable/enable alert to author of data
	'module'   => ''						// module name
#	'db'       => 'db'||'db1'		// berisi string untuk database lain berdasarkan urutan di config.php
	);
$comment = _class('comment');
$comment->init($cfg);
echo $comment->show();

# OR DISPLAY IN SINGLE LINE
echo _class('comment', $cfg)->show();

TO CREATE TABLE COMMENT USE THE EXAMPLE BELOW THIS FILE
*/
class comment
{
	private $config;
	private $default;
	public $sesname = 'commentUser';
	function __construct($params = array())
	{
		$this->default = array(
			'table'      => '',
			'field'      => '',
			'link'       => '',	// current link
			'id'         => 0,	// Eg. content_id or download_id
			'par_id'     => 0,	// par_id jika ini reply message
			// 'comment_id' => 0,	// Jika ingin menampilkan salah satu comment saja (dipakai untuk detail comment ato edit comment)
			'type'       => 1,	// [1=Normal Form, 0=No Comment, 2=Facebook Comment]
			'list'       => 5,	// number of comment per page
			'form'       => 1,	// show/hide comment form
			'emoticon'   => 1,	// show/hide emoticon if form enable
			'captcha'    => 1,	// show/hide captcha in form comment
			'approve'    => 0,	// disable/enable auto publish if approve=0 admin must approve every comment manually
			'alert'      => 1,	// disable/enable alert to author of data
			'admin'      => 0,	// if admin==1 then display all comments include unpublish
			'module'     => $GLOBALS['Bbc']->mod['name']
			);
		if (!empty($params))
		{
			$params = array_merge($this->default, $params);
			$this->init($params);
		}else{
			$this->init($this->default);
		}
	}
	function init($params)
	{
		foreach ($params as $key => $value)
		{
			$this->set($key, $value);
		}
	}
	function set($key, $value)
	{
		$this->config[$key] = $value;
	}
	function get($key)
	{
		if (!empty($this->config[$key]))
		{
			return $this->config[$key];
		}
		return null;
	}
	function session($data = array())
	{
		$sesname = $this->sesname;
		if (!empty($data))
		{
			$_SESSION[$sesname] = $data;
		}else
		if (!empty($_SESSION[$sesname]))
		{
			global $user;
			$u = $_SESSION[$sesname];
			$r = array('name', 'email', 'website', 'image');
			foreach ($r as $key)
			{
				if (!empty($u[$key]))
				{
					$user->$key = $u[$key];
				}
			}
		}
	}

	function show()
	{
		$config = $this->config;
		if (empty($config['table']) || empty($config['id']))
		{
			return msg('table is empty or field_id is 0', 'danger');
		}
		if (empty($config['field']))
		{
			$config['field'] = preg_replace(array('~^bbc_~is', '~_comment$~is'), array('',''), $config['table']);
		}
		if (empty($config['link']))
		{
			$config['link'] = seo_uri();
		}
		global $user, $db, $Bbc, $sys;
		ob_start();
		if (!empty($config['db']))
		{
			$db = $GLOBALS[$config['db']];
		}
		include _ROOT.'modules/user/comment.php';
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	function decode($token)
	{
		$token = str_replace(' ', '+', urldecode($token));
		$config = json_decode(decode($token), 1);
		return array($token, $config);
	}
}
/*
$modulename = 'modulename';
$q = "CREATE TABLE IF NOT EXISTS `{$modulename}_comment` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `par_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT '0',
  `reply_all` int(11) DEFAULT '0',
  `reply_on` int(11) DEFAULT '0',
  `{$modulename}_id` int(255) unsigned DEFAULT NULL,
  `{$modulename}_title` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `content` text,
  `date` datetime DEFAULT NULL,
  `publish` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `{$modulename}_id` (`{$modulename}_id`),
  KEY `publish` (`publish`),
  KEY `par_id` (`par_id`),
  KEY `user_id` (`user_id`),
  KEY `reply_all` (`reply_all`),
  KEY `reply_on` (`reply_on`),
  FULLTEXT KEY `{$modulename}_title` (`{$modulename}_title`,`name`,`email`,`website`,`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$db->Execute($q);
*/