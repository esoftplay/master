<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$act = '';
if(isset($id)) {
	$id = intval($id);
	$act= ($id > 0) ? 'edit' : '';
	$formtitle = 'Agenda Update';
}else {
	$cat_id = intval($cat_id);
	$act= 'add';
	$formtitle = 'Add Agenda';
}
if(empty($act)) redirect($Bbc->mod['circuit']);

_func('editor');
_func('date');
link_js('includes/lib/pea/includes/FormDate.js');
$r_lang   = lang_assoc();
$r_cat    = agenda_cat();
$r_month  = month_r();
$r_day    = day_r();
$r_date   = date_r();
$r_hour   = hour_r();
$r_minute = minute_r();

if(isset($_POST['submit_update'])) {
	include 'agenda_update-action.php';
}
if($act == 'edit')
{
	$q = "SELECT * FROM agenda WHERE id=$id";
	$data = $db->getRow($q);
	if($db->Affected_rows()) {
		$data = array_merge(content_fetch_admin($data['content_id']), $data);
		$cat_id = $data['cat_id'];
	}else{
		redirect($Bbc->mod['circuit'].'.agenda');
	}
}else{
	if(!isset($r_cat[$cat_id])) redirect($Bbc->mod['circuit'].'.agenda');
	$data = array();
	if($cat_id == 1 || $cat_id == 2) {
		$format = 'DATE_RFC822';
		$time = time();
		$data = array(
			'start_date'=> date('Y-m-d')
		,	'end_date'	=> '0000-00-00'
		);
	}
	$data['publish'] = '1';
}
if(isset($r_cat[$cat_id])) $r_cat_option = '';
else $r_cat_option = '<option value="" selected="selected">Select Type</option>';
$sys->nav_add($formtitle);
$return_url = $Bbc->mod['circuit'];
if (!empty($_GET['return']))
{
	$return_url = $_GET['return'];
}
?>
<form method="post" action="" enctype="multipart/form-data">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $formtitle;?></h3>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label>Agenda Type : <?php echo $r_cat[$cat_id];?></label>
			</div>
			<div class="form-group">
				<?php
				switch($cat_id)
				{
					case '1': // Event
						?>
						<div class="form-inline">
							<label>Start :</label>
							<input type="date" name="start_date" value="<?php echo @$data['start_date'];?>" class="form-control" />
							<select name="start_hour" class="form-control"><?php echo createOption($r_hour, @$data['start_hour']);?></select> :
							<select name="start_minute" class="form-control"><?php echo createOption($r_minute, @$data['start_minute']);?></select>
						</div>
						<div class="form-inline">
							<label>Until :</label>
							<input type="date" name="end_date" value="<?php echo @$data['end_date'];?>" class="form-control" />
							<select name="end_hour" class="form-control"><?php echo createOption($r_hour, @$data['end_hour']);?></select> :
							<select name="end_minute" class="form-control"><?php echo createOption($r_minute, @$data['end_minute']);?></select>
						</div>
						<?php
						break;
					case '2': // Daily
						?>
						<div class="form-inline">
							<label>Start Hour :</label>
							<select name="start_hour" class="form-control"><?php echo createOption($r_hour, @$data['start_hour']);?></select> :
							<select name="start_minute" class="form-control"><?php echo createOption($r_minute, @$data['start_minute']);?></select>
						</div>
						<div class="form-inline">
							<label>Until Hour :</label>
							<select name="end_hour" class="form-control"><?php echo createOption($r_hour, @$data['end_hour']);?></select> :
							<select name="end_minute" class="form-control"><?php echo createOption($r_minute, @$data['end_minute']);?></select>
						</div>
						<?php
						break;
					case '3': // weekly
						?>
						<div class="form-inline">
							<label>Start Day :</label>
							<select name="start_date" class="form-control"><?php echo createOption($r_day, @$data['start_date']);?></select>
							<select name="start_hour" class="form-control"><?php echo createOption($r_hour, @$data['start_hour']);?></select> :
							<select name="start_minute" class="form-control"><?php echo createOption($r_minute, @$data['start_minute']);?></select>
						</div>
						<div class="form-inline">
							<label>Until Day :</label>
							<select name="end_date" class="form-control"><?php echo createOption($r_day, @$data['end_date']);?></select>
							<select name="end_hour" class="form-control"><?php echo createOption($r_hour, @$data['end_hour']);?></select> :
							<select name="end_minute" class="form-control"><?php echo createOption($r_minute, @$data['end_minute']);?></select>
						</div>
						<?php
						break;
					case '4': // monthly
						?>
						<div class="form-inline">
							<label>Start Date :</label>
							<select name="start_date" class="form-control"><?php echo createOption($r_date, @$data['start_date']);?></select>
							<select name="start_hour" class="form-control"><?php echo createOption($r_hour, @$data['start_hour']);?></select> :
							<select name="start_minute" class="form-control"><?php echo createOption($r_minute, @$data['start_minute']);?></select>
						</div>
						<div class="form-inline">
							<label>Until Date :</label>
							<select name="end_date" class="form-control"><?php echo createOption($r_date, @$data['end_date']);?></select>
							<select name="end_hour" class="form-control"><?php echo createOption($r_hour, @$data['end_hour']);?></select> :
							<select name="end_minute" class="form-control"><?php echo createOption($r_minute, @$data['end_minute']);?></select>
						</div>
						<?php
						break;
					case '5': // yearly
						?>
						<div class="form-inline">
							<label>Start :</label>
							<input type="date" name="start_date" value="<?php echo @$data['start_date'];?>" class="form-control" data-date-format="0000-mm-dd" data-startView="1" />
							<select name="start_hour" class="form-control"><?php echo createOption($r_hour, @$data['start_hour']);?></select> :
							<select name="start_minute" class="form-control"><?php echo createOption($r_minute, @$data['start_minute']);?></select>
						</div>
						<div class="form-inline">
							<label>Until :</label>
							<input type="date" name="end_date" value="<?php echo @$data['end_date'];?>" class="form-control" data-date-format="0000-mm-dd" data-startView="1" />
							<select name="end_hour" class="form-control"><?php echo createOption($r_hour, @$data['end_hour']);?></select> :
							<select name="end_minute" class="form-control"><?php echo createOption($r_minute, @$data['end_minute']);?></select>
						</div>
						<?php
						break;
				}
				?>
			</div>
			<div class="form-group">
				<label>Agenda Title</label>
				<?php
				foreach($r_lang AS $lang_id => $dt)
				{
					?>
					<input type="text" name="title[<?php echo $lang_id;?>]" class="form-control" title="<?php echo $dt['title'];?>" value="<?php echo @$data['text'][$lang_id]['title'];?>" />
					<?php
				}
				?>
			</div>
			<div class="form-group">
				<label>Agenda Detail</label>
				<?php
				if(count($r_lang) > 1)
				{
					$tabs_text = array();
					foreach($r_lang AS $lang_id => $dt)
					{
						$tabs_text[$dt['title']] = editor_html('content['.$lang_id.']', @$data['text'][$lang_id]['content']);
					}
					echo tabs($tabs_text, 0);
				}else{
					$lang_id = lang_id();
					echo editor_html('content['.$lang_id.']', @$data['text'][$lang_id]['content']);
				}
				?>
			</div>
			<div class="form-group">
				<label>Publish</label>
				<div class="input-group checkbox">
					<label>
						<input type="checkbox" name="publish" value="1"<?php echo is_checked($data['publish']);?>> published
					</label>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<span type="button" class="btn btn-default btn-sm" onclick="document.location.href='<?php echo $return_url;?>';"><span class="glyphicon glyphicon-chevron-left"></span></span>
			<button type="submit" name="submit_update" value="SAVE" class="btn btn-primary btn-sm">
				<span class="glyphicon glyphicon-floppy-disk"></span>
				SAVE
			</button>
			<button type="reset" class="btn btn-warning btn-sm">
				<span class="glyphicon glyphicon-repeat"></span>
				RESET
			</button>
		</div>
	</div>
</form>