<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$id = @intval($_GET['id']);
$data = content_rss($id, 0);
$sys->stop();
$config = $data['config'];
if(@$data['publish'])
{
	header ("content-type: application/xml");
	_func('image');
	$link = ($id > 0) ? content_cat_link($data['id'], $data['title']) : _URL;
	$link = htmlentities($link);
	echo '<?xml version="1.0" encoding="iso-8859-1"?>',"\n";
	?>
	<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
		<channel>
			<atom:link href="<?php echo seo_uri();?>" rel="self" type="application/rss+xml" />
			<title><?php echo htmlentities($data['title']);?></title>
			<link><?php echo $link;?></link>
			<description><?php echo htmlentities($data['description']);?></description>
			<image>
				<title><?php echo htmlentities($data['title']);?></title>
				<link><?php echo $link;?></link>
				<url><?php echo _URL.'images/'.config('site','logo');?></url>
			</image>
			<?php
			foreach((array)$data['list'] AS $dt)
			{
				$image = !empty($dt['image']) ? content_src($dt['image'], ' align="left" hspace="5"') : '';
				if(empty($image))
				{
					$enclosure = '';
				}else{
					$img_src = content_src($dt['image'], false, true);
					$img_dir = str_replace(_URL, _ROOT, $img_src);
					if (is_file($img_dir))
					{
						$img_size = filesize($img_dir);
					}else{
						$img_size = filesize(_ROOT.'images/loading.gif');
					}
					$enclosure = "\n".'<enclosure url="'.$img_src.'" length="'.$img_size.'" type="image/jpg" />';
				}
				?>
				<item>
					<title><?php echo htmlentities($dt['title']);?></title>
					<link><?php echo htmlentities(content_link($dt['id'], $dt['title']));?></link>
					<guid><?php echo htmlentities(content_link($dt['id'], $dt['title']));?></guid>
					<pubDate><?php echo date('D, d M Y H:i:s', strtotime($dt['created']));?> +0700</pubDate>
					<description><?php echo htmlentities($image.' '.strip_tags($dt['intro']));?></description><?php echo $enclosure;?>
				</item>
				<?php
			}
			?>
		</channel>
	</rss>
	<?php
}
