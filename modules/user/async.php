<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$sys->set_layout('blank');
$id = @intval($_GET['id']);
if (!empty($id))
{
	switch ($_GET['act'])
	{
		case 'del':
			$db->Execute("DELETE FROM `bbc_async` WHERE `id`={$id}");
			break;

		case 'last':
			$first = $db->getRow("SELECT * FROM `bbc_async` WHERE 1 ORDER BY `id` ASC");
			$fID   = 0;
			if (!empty($first))
			{
				$overdue = strtotime('-2 minutes');
				$created = strtotime($first['created']);
				if ($created < $overdue)
				{
					$fID = $first['id'];
				}
			}
			$out    = array(
				'ok'     => 1,
				'lastID' => $fID
				);
			output_json($out);
			break;

		default:
			_class('async')->fix($id);
			break;
	}
	redirect();
}
$exist = $db->getOne("SHOW TABLES LIKE 'bbc_async'");
if (!empty($exist))
{
	$form = _lib('pea',  'bbc_async');

	$form->initSearch();
	$form->search->setFormName('search_async');

	$form->search->addInput('keyword','keyword');
	$form->search->input->keyword->addSearchField('function,arguments', false);

	$add_sql = $form->search->action();
	$keyword = $form->search->keyword();

	$searchForm =  $form->search->getForm();

	$form->initRoll("{$add_sql} ORDER BY id ASC");

	// $form->roll->setNumRows(2);
	$form->roll->setSaveTool(false);
	$form->roll->setDeleteTool(true);

	$form->roll->addInput('col1', 'multiinput');
	$form->roll->input->col1->setTitle('ID');
	$form->roll->input->col1->addInput('idx', 'sqlplaintext');
	$form->roll->input->col1->addInput('idx2', 'editlinks');

	$form->roll->input->idx->setFieldName('id AS idx');
	$form->roll->input->idx2->setFieldName('id AS idx2');
	$form->roll->input->idx2->setLinks($Bbc->mod['circuit'].'.'.$Bbc->mod['task'], icon('console').' Execute');
	$form->roll->input->idx2->setExtra(' class="executeOne"');

	$form->roll->addInput('function', 'sqlplaintext');
	$form->roll->input->function->setDisplayFunction('json_decode');

	$form->roll->addInput('arguments', 'sqlplaintext');
	$form->roll->input->arguments->setDisplayColumn();
	$form->roll->input->arguments->setDisplayFunction(function($a){
		$data = json_decode($a, 1);
		return '<code>'.json_encode($data, JSON_PRETTY_PRINT).'</code>';
	});

	_func('date');
	$form->roll->addInput('created', 'sqlplaintext');
	$form->roll->input->created->setTitle('Age');
	$form->roll->input->created->setDisplayFunction('timespan');

	$form->roll->action();
	?>
	<div class="container-fluid">
		<?php
		if (empty($_GET['page']) || @$_GET['page']==1)
		{
			?>
			<button type="button" class="btn btn-default pull-left" id="executeAll">Execute All</button>
			<script type="text/javascript">
				function executesync(x) {
					$.ajax({
						url: _URL+"user/async?act=last&id="+x,
						dataType:"json",
						success: function(a){
							if (a.ok) {
								if (a.lastID) {
									var lastID = sessionStorage.getItem("executeAll");
									if (lastID == a.lastID) {
										$("#executeAll").text('Checking..');
										setTimeout(function() {
											executesync(x);
										}, 1000);
									}else{
										var url = document.location.href;
										if (url.substr(-1) == "/") {
											url += a.lastID+"?return=";
										}else{
											url += url.indexOf("?") >= 0 ? "&" : "?";
											url += "id="+a.lastID+"&return=";
										}
										url += encodeURI(document.location.href);
										$("#executeAll").text('Loading...');
										sessionStorage.setItem("executeAll", a.lastID);
										document.location.href = url;
									}
								}else{
									sessionStorage.setItem("executeAll", 0);
									document.location.reload();
								}
							}
						}
					});
				};
				_Bbc(function($){
					if (typeof(Storage) !== "undefined") {
						$("#executeAll").on("click", function(e){
							e.preventDefault();
							var a = $(".executeOne");
							if (a.length > 0) {
								var b = a.attr("href").match(/\/([0-9]+)[\/\?]/);
								sessionStorage.setItem("executeAll", b[1]);
								$(this).text('Loading...');
								document.location.href = a.attr("href");
							}else{
								sessionStorage.setItem("executeAll", 0);
								$(this).hide();
							}
						});
					} else {
						$(this).hide();
					}
					// jika ada data di session
					var c = sessionStorage.getItem("executeAll");
					if (c > 0) {
						executesync(c);
					}
					if ($(".executeOne").length == 0) {
						$("#executeAll").hide();
					}
				});
			</script>
			<?php
		}
		echo $searchForm;
		echo $form->roll->getForm();
		?>
	</div>
	<?php
}