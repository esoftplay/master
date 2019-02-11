<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

if (!empty($_GET['q']) && !empty($_GET['token']))
{
	header("Content-Type: text/plain");
	$default = array(
		'table'  => 'bbc_content_tag',
		'field'  => 'title',
		'id'     => 'id',
		// 'format' => 'CONCAT(title, "(", id, ")")',
		// 'sql'    => 'par_id=0 AND active=1' # optional
		// 'parent' => 'par_id',
		);
	$token = json_decode(decode(str_replace(' ', '+', urldecode($_GET['token']))), 1);
	if (!empty($token['expire']) && $token['expire'] > time())
	{
		$sql = '';
		$tkn = !empty($token['reference']) ? $token['reference'] : $token;
		$cfg = array_merge($default, $token);
		if (empty($cfg['format']))
		{
			$cfg['format'] = $cfg['field'];
		}
		// extract($cfg); // table, field, id, format, sql, parent, db
		$table  = @$cfg['table'];
		$field  = @$cfg['field'];
		$id     = @$cfg['id'];
		$format = @$cfg['format'];
		$sql    = @$cfg['sql'];
		$parent = @$cfg['parent'];
		if (!empty($cfg['db']))
		{
			$db = ${$cfg['db']};
		}
		$field_id = $id;
		$id       = @intval($_GET['id']);
		if (!empty($sql))
		{
			$sql = 'AND '.$sql;
		}
		if (!empty($token['parent']) && !empty($_GET['parent']))
		{
			$parent = str_replace(array('"',"'",' '), '', $_GET['parent']);
			if (!empty($parent))
			{
				$parent = is_numeric($parent) ? $parent : "'{$parent}'";
				$sql   .= ' AND `'.$token['parent'].'`='.$parent;
			}
		}
		$table = trim($table);
		if (preg_match('~ where ~is', $table))
		{
			$table .= ' AND';
		}else{
			$table .= ' WHERE';
		}
		if (isset($_GET['id']))
		{
			$q = "SELECT {$format} AS `format`, {$field} AS `title`, {$field_id} AS `id` FROM {$table} {$field_id}={$id} {$sql} ORDER BY {$field} ASC LIMIT 1";
			$r = $db->getRow($q);
			if (!empty($r))
			{
				echo implode('|', $r)."\n";
			}
		}else{
			$name = preg_replace('~[^a-z0-9\s\.]~is', '', $_GET['q']);
			$q    = "SELECT {$format} AS `format`, {$field} AS `title`, {$field_id} AS `id` FROM {$table} {$field} LIKE '%{$name}%' {$sql} ORDER BY {$field} ASC LIMIT 1000";
			$r    = $db->cacheGetAll($q);
			foreach ($r as $d)
			{
				echo implode('|', $d)."\n";
			}
		}
	}
}
die();
/*
// CODE EXAMPLE "Multi Text Input":
link_js(_URL.'includes/lib/pea/includes/FormTags.js');
$token = array(
	'table'  => 'bbc_content_tag',
	'field'  => 'title',
	'id'     => 'id',
	'format' => 'CONCAT(title, " (", id, ")")',
	'sql'    => 'total>0',
	'links'  => 'index.php?mod=content.tag_detail&id=',
	'expire' => strtotime('+2 HOURS'),
	);
?>
<div class="form-group">
	<label>Content Tags</label>
	<div class="form-control tags">
		<span>
			<?php
			if (!empty($content_id))
			{
				$q = "SELECT t.id, t.title FROM bbc_content_tag_list AS l LEFT JOIN bbc_content_tag AS t ON (l.tag_id=t.id) WHERE l.content_id=".$content_id;
				$r = $db->getAll($q);
				foreach ($r as $t)
				{
					?>
					<input type="hidden" name="tags_ids[]" value="<?php echo $t['id']; ?>" title="<?php echo $t['title'];?>" />
					<?php
				}
			}
			?>
		</span>
		<span data-token="<?php echo encode(json_encode($token)); ?>" data-isallowednew="1" data-href="<?php echo $token['links']; ?>" name="tags_ids" contenteditable></span>
	</div>
</div>
<script type="text/javascript">
	// JIKA INGIN CUSTOM, MAKA OVERWRITE FUNCTION DI BAWAH TANPA DIBUNGKUS _Bbc(function($){...});
  function FormTags(new_link_tags, new_delete_icons) {
    new_link_tags.on('click', function (e) {
      var a = $(this).attr("href");
      if (a) {
        e.preventDefault();
        alert('ini linknya "'+a+'"');
      }
    });
    new_delete_icons.on('click', function (e) {
      e.preventDefault();
      if (confirm("serius loe mo hapus nih?")) {
        $(this).parent().remove()
      }
    })
  };
</script>




// CODE EXAMPLE "Auto Complete":
link_js(_LIB.'pea/includes/FormTags.js');
$token = array(
	'table'  => 'bbc_content_cat_text',
	'field'  => 'title',
	'id'     => 'cat_id',
	'format' => 'CONCAT(title, " (", cat_id, ")")',
	'sql'    => 'lang_id=1',
	'expire' => strtotime('+2 HOURS'),
	);
$token2 = array(
	'table'  => 'bbc_content_category AS y LEFT JOIN bbc_content_text AS t ON (y.content_id=t.content_id)',
	'field'  => 'title',
	'id'     => 't.content_id',
	'format' => 'CONCAT(title, " (", t.content_id, ")")',
	'sql'    => 'lang_id=1',
	'parent' => 'cat_id', // input field kedua adalah child dari input pertama dengan fieldName 'cat_id'
	'expire' => strtotime('+2 HOURS'),
	);
if(!empty($_POST['tag_id']) || !empty($_POST['content_id']))
{
	pr($_POST);
}
?>
<form action="" method="POST" role="form">
	<div class="form-group">
		<label>Insert test</label>
		<input name="category_id_field" rel="ac" data-token="<?php echo encode(json_encode($token)); ?>" value="" type="text" placeholder="Masukkan Category" class="form-control" />
		<input name="content_id_field" rel="ac" data-parent="category_id_field" data-token="<?php echo encode(json_encode($token2)); ?>" value="" type="text" placeholder="Masukkan Content" class="form-control" />
	</div>
	<button type="submit" class="btn btn-primary">Submit</button>
</form>
<script type="text/javascript">
	_Bbc(function($){
		$('input[name="content_id_field"]').on("change", function(e){
			e.preventDefault();
			alert('Event "change" untuk input[name="content_id_field"] dengan value: '+$(this).val());
		});
	});
</script>


// HOW TO USE YOUR OWN FUNCTION :
<form action="" method="POST" role="form">
	<div class="form-group">
		<label>Insert test</label>
		<input class="form-control" name="tag_id" value="" id="test_onfind" type="text" placeholder="Masukkan Content Tags" data-token="<?php echo encode(json_encode($token)); ?>" />
	</div>
	<button type="submit" class="btn btn-primary">Submit</button>
</form>
<script type="text/javascript">
_Bbc(function($){
	$('#test_onfind').autocomplete({
		onfind: function(a) {
			  if (a == null) return alert("No match!");
			  if ( !! a.extra) {
			    $('#test_onfind_ac').val(a.extra[0]);
			    $('#test_onfind').val(a.extra[1]);
			  	alert("I execute special function!!\nUnbind content tags suggestion");
			  	$('#tag_id').autocomplete("clear");
			  } else {
			    var b = a.selectValue;
			  }
			}
	});
});
</script>

// HOW TO USE ALL OPTIONS AVAILABLE :
<input class="form-control" name="sample_id" rel="ac" type="text" placeholder="Sample with all options"
	data-url="<?php echo site_url($Bbc->mod['circuit'].'.'.$Bbc->mod['task']); ?>"
	data-token="<?php echo encode(json_encode($token)); ?>"
	data-value="Label/Title for sample_id"
	data-onfind="function(a){if(a==null)return alert('No match!');if(!!a.extra){$('#test_onfind_ac').val(a.extra[0]);$('#test_onfind').val(a.extra[1]);alert('I execute special function!! AND Unbind content tags suggestion');$('#tag_id').autocomplete('clear');}else{var b=a.selectValue;}}"
	data-onselect="null"
	data-formatItem="function(a){return a[2]}"
	data-url="user/selecttable?token=*&q="
	data-data="function(){return {}};"
	data-inputClass="ac_input"
	data-resultsClass="ac_results"
	data-loadingClass="ac_loading"
	data-lineSeparator="\n"
	data-cellSeparator="|"
	data-minChars="2"
	data-delay="10"
	data-matchCase="0"
	data-matchSubset="1"
	data-matchContains="1"
	data-cacheLength="10"
	data-mustMatch="0"
	data-extraParams="function(){return {}};"
	data-selectFirst="true"
	data-selectOnly="false"
	data-maxItemsToShow="-1"
	data-autoFill="false"
	data-width="0"
	/>

// SAMPLE UNTUK FormTags semisal content Tags bisa di lihat di 'modules/content/admin/content_edit-attributes.php'
*/