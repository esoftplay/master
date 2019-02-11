<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$tmp = array(
	'title'       => '',
	'description' => '',
	'keyword'     => '',
	'intro'       => '',
	'content'     => '',
	'tags'        => ''
);
$text = @array_merge($tmp, $data['text'][$lang_id]);
$defConfig = array('language' => (count($r_lang) > 1) ? $language['code'] : 'en', 'path' => 'images/modules/content/');
?>
<div class="form-group">
	<label>Content Title</label>
	<input type="text" name="text[title][<?php echo $lang_id;?>]" value="<?php echo htmlentities($text['title']);?>" id="title_<?php echo $lang_id;?>" class="form-control" />
</div>
<div class="panel-group" id="accordion<?php echo $lang_id; ?>">
	<div class="panel panel-default">
	  <div class="panel-heading">
	    <span class="panel-title" data-toggle="collapse" data-parent="#accordion<?php echo $lang_id; ?>" href="#pea_isHideToolOn<?php echo $lang_id; ?>" style="cursor: pointer;display: block;">
	    	Specify Meta Data
	    </span>
	  </div>
	  <div id="pea_isHideToolOn<?php echo $lang_id; ?>" class="panel-collapse collapse on">
	    <div class="panel-body">
				<div class="form-group">
					<label>Meta Keyword</label>
					<textarea name="text[keyword][<?php echo $lang_id;?>]" id="keyword_<?php echo $lang_id;?>" class="form-control"><?php echo $text['keyword'];?></textarea>
				</div>
				<div class="form-group">
					<label>Meta Description</label>
					<textarea name="text[description][<?php echo $lang_id;?>]" id="description_<?php echo $lang_id;?>" class="form-control"><?php echo $text['description'];?></textarea>
				</div>
			</div>
	  </div>
	</div>
</div>
<div class="form-group">
	<label>Intro Text</label>
	<textarea name="text_intro_<?php echo $lang_id;?>" id="text_intro_<?php echo $lang_id;?>" class="form-control"><?php echo strip_tags($text['intro']); ?></textarea>
</div>
<div class="form-group">
	<label>Detail Content</label>
	<?php echo editor_html('text_content_'.$lang_id, $text['content'], array_merge(array('height'=>'600px'), $defConfig));?>
</div>