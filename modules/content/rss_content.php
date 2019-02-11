<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
_func('smiley');
if($id > 0)
{
	$sys->stop();
	$data = content_fetch($id, true);
	$config = $data['config'];
	if(!empty($data['publish']))
	{
		$q = "SELECT * FROM bbc_content_comment WHERE content_id={$id} AND publish=1 ORDER BY id DESC LIMIT 0, ".$config['comment_list'];
		$comments = $r = $db->getAll($q);
		header ("content-type: application/xml");
		echo '<?xml version="1.0" encoding="iso-8859-1"?>',"\n";
		?>
		<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
			<channel>
				<atom:link href="<?php echo seo_uri();?>" rel="self" type="application/rss+xml" />
				<title><?php echo htmlentities($data['title']);?></title>
				<link><?php echo htmlentities(content_link($data['id'], $data['title']));?></link>
				<description><?php echo htmlentities($data['description']);?></description>
				<image>
					<title><?php echo htmlentities($data['title']);?></title>
					<link><?php echo htmlentities(content_link($data['id'], $data['title']));?></link>
					<url><?php echo content_src($data['image']);?></url>
				</image>
				<?php
				foreach((array)$comments AS $dt)
				{
					if(is_url($dt['website']))
					{
						$link = '<link>'.htmlentities($dt['website']).'</link>
						<guid>'.htmlentities($dt['website']).'</guid>';
					}else{
						$link = '';
					}
					$img = !empty($dt['image']) ? $dt['image'] : $sys->avatar($dt['email'], 60);
					preg_match('~src="(.*?)"~is', $img, $m);
					$dt['content'] = htmlentities($img.' '.nl2br(smiley_parse($dt['content'])));
					$enclosure = '<enclosure url="'.@$m[1].'" type="image/jpg" />';
					?>
					<item>
						<title><?php echo htmlentities($dt['name']);?></title>
						<?php echo $link;?>
						<pubDate><?php echo date('D, d M Y H:i:s', strtotime($dt['date']));?> +0700</pubDate>
						<description><?php echo $dt['content'];?></description>
						<?php echo $enclosure;?>
					</item>
					<?php
				}
				?>
			</channel>
		</rss>
		<?php
	}
}