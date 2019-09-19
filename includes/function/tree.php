<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

/*=====================================
	$arr[] = array(
		'id'          => '',
		'par_id'      => '',
		'title'       => '',
		'link'        => '',
		'description' => '' // for alt, if doesn't any then use 'title'
		);
 	 $cookie can be config if (array)
 *====================================*/
function tree_list($arr, $title = '', $cookie = true, $all_active_link = true, $target = '', $path_icon = '')
{
	global $sys;
	$menu = '';
	$sys->link_js( _URL.'includes/function/js/tree/tree.js', false);
#	$sys->link_css( _URL.'includes/function/js/tree/tree.css'); sudah di load di script js
	if(is_array($title))
	{
		$r = array_values($title);
		$title = array(
		  'text' => @$r[0]
		, 'link' => @$r[1]
		);
	}else{
		$r = array(
		  'text' => $title
		, 'link' => ''
		);
		$title = $r;
	}
	$config = '';
	if(is_array($cookie))
	{
		$r			= $cookie;
		$cookie = true;
		foreach($r AS $var => $val)
		{
			$config .= "\n".'d.config.'.$var.' = '._tree_list_value($val).';';
		}
	}else{
		$cookie = $cookie ? true : false;
	}
	$j = 0;
	foreach((array)$arr as $i => $row)
	{
		$j++;
		$link    = (!$all_active_link) ? _tree_list_link($arr, $row['id'], $i, $j) : $row['link'];
		$alt     = !empty($row['description']) ? $row['description'] : $row['title'];
		$alt     = addslashes(htmlentities(trim(strip_tags(preg_replace('~\s{2,}~s', ' ', $alt)))));
		$targeti = $row['link'] != $link ? '' : $target;
		if(!empty($row['image']))
		{
			$icon = $path_icon.$row['image'];
		}else{
			$icon = '';
		}
		$menu .= "d.add({$row['id']}, {$row['par_id']},'{$row['title']}', '{$link}','{$alt}','{$targeti}','{$icon}', '{$icon}', false);\n";
	}
	$url = is_file($sys->template_dir.'images/folder.gif') ? $sys->template_url.'images/' : '';
	ob_start();
	?>
	<div class="expand-collapse">
		<div class="btn-group" data-toggle="buttons">
		  <label class="btn btn-xs btn-default btn-secondary" onclick="d.openAll();">
		  	<input type="radio" autocomplete="off">
		    <?php echo icon('chevron-down'); ?>
		  </label>
		  <label class="btn btn-xs btn-default btn-secondary" onclick="d.closeAll();">
		  	<input type="radio" autocomplete="off">
		    <?php echo icon('chevron-up'); ?>
		  </label>
		</div>
	</div>
	<div class="dtree">
		<script type="text/javascript">
			d = new dTree('d','<?php echo $url;?>');
			d.config.useCookies = <?php echo _tree_list_value($cookie);?>;<?php echo $config;?>
			d.add(0,-1,'<?php echo $title['text'];?>', '<?php echo $title['link'];?>','<?php echo $title['text'];?>','','', false);
			<?php echo $menu;?>
			document.write(d);
			<?php
			if (isset($row['description']))
			{
				?>
				_Bbc(function($){
					$("a", $(".dtree")).each(function(){
						var a = $(this);
						var b = $(this).attr("title");
						var c = document.location.href;
						if (a.hasClass("nodeSel") && a.attr("href")!=c) {
							a.removeClass("nodeSel").addClass("node");
						}
						if (a.attr("href")==c && !a.hasClass("nodeSel")) {
							a.removeClass("node").addClass("nodeSel");
						}
						if(b!=a.text()) {
							$(this).attr({
								"data-toggle":"popover",
								"data-placement": "auto",
								"title":"",
								"data-content":b
							});
						}
					});
					BS3load.popover();
				});
				<?php
			}
			?>
		</script>
	</div>
	<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}

function _tree_list_link($arr, $id, $i, $j)
{
	$output = $arr[$i]['link'];
	foreach($arr AS $i => $dt)
	{
		if($dt['par_id'] == $id)
		{
			$output = 'javascript: d.o('.$j.');';
			break;
		}
	}
	return $output;
}

function _tree_list_value($var)
{
	if(is_bool($var))
		$output = $var ? 'true' : 'false';
	else
	if(is_string($var))
		$output = "'$var'";
	else
	if(is_array($var))
	{
		$output = '';
		foreach($var AS $i => $j)
			$output .= "\n".$i.'='._tree_list_value($j).';';
	}else $output = $var;
	return $output;
}