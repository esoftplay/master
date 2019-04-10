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
	$form->initRoll("WHERE 1 ORDER BY id ASC");

	$form->roll->setSaveTool(false);
	$form->roll->setDeleteTool(false);

	$form->roll->addInput('col1', 'multiinput');
	$form->roll->input->col1->setTitle('ID');
	$form->roll->input->col1->addInput('idx', 'sqlplaintext');
	$form->roll->input->col1->addInput('idx2', 'editlinks');

	$form->roll->input->idx->setFieldName('id AS idx');
	$form->roll->input->idx2->setFieldName('id AS idx2');
	$form->roll->input->idx2->setLinks(
		array(
		$Bbc->mod['circuit'].'.'.$Bbc->mod['task']            => icon('console').' Execute',
		$Bbc->mod['circuit'].'.'.$Bbc->mod['task'].'&act=del' => icon('trash').' Delete',
		)
	);

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
		<?php echo $form->roll->getForm(); ?>
	</div>
	<script type="text/javascript">
		_Bbc(function($){
			$(".glyphicon-trash").parent().on("click", function(e){
				e.preventDefault();
				if (confirm("Apakah anda ingin menghapus proses ini?")) {
					document.location.href = $(this).attr("href")
				}
			});
		});
	</script>
	<?php
}