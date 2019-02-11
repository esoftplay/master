<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$config['expire'] = strtotime('+4 HOUR');
switch (@$config['type'])
{
	case '2': // Facebook
	echo '<style type="text/css">.fb-comments, .fb-comments iframe[style], fb_iframe_widget > span[style] {width: 100% !important;}</style><div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#appId=140222152715389&amp;xfbml=1"></script><fb:comments href="'.$config['link'].'" num_posts="'.$config['list'].'"></fb:comments><div class="clearfix"></div>';
		break;
	case '3': // disqus.com
		$cfg = get_config('content', 'manage');
		if (!empty($cfg['disqus']))
		{
			?>
			<div id="disqus_thread"></div>
			<script type="text/javascript">
		    var disqus_shortname = '<?php echo $cfg['disqus']; ?>';
		    (function() {
	        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
	        dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
	        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
		    })();
			</script>
			<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript" rel="nofollow">comments powered by Disqus.</a></noscript>
			<?php
		}else{
			echo msg("Disqus shortname is not setup in Content / Configuration!", 'warning');
		}
		break;

	case '1': // Normal Form
		if (!empty($config['comment_id']))
		{
			$total_all  = 1;
			$total_list = 1;
		}else{
			$total_all  = $db->getOne("SELECT COUNT(*) FROM `".$config['table']."` WHERE `".$config['field']."_id`=".$config['id']);
			$total_list = $db->getOne("SELECT COUNT(*) FROM `".$config['table']."` WHERE `par_id`=".$config['par_id']." AND `".$config['field']."_id`=".$config['id']);
		}
		$token      = encode(json_encode($config));
		include tpl('comment.html.php');
		break;
}
