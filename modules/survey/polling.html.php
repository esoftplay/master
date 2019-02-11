<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');
?>
<h1><?php echo lang('Polling Result');?></h1>
<?php
foreach($pollings AS $polling_id => $data)
	{		
?>
		<div class="">
			<div class="panel panel-info" style="padding: 10px;display: inline-block;width: 96%;">
				<p><strong><?php echo $data['question'];?></strong></p>
				<hr>
				<ul class="list-unstyled">
				<?php				foreach((array)$data['option'] AS $d)
				{
					if($d['voted'] > 0) $voted = round($d['voted'] / $data['total'] * 100, 2);
					else $voted = 0;
					?>
					<li>
						<div class="col-md-3"><strong><?php echo $d['title'];?></strong></div>
							<div class="col-md-5 no-left">
								<div class="progress">
									<div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $voted;?>%"></div>
								</div>
							</div>
						<div class="col-md-1 no-both"><?php echo $voted;?>%</div>
					</li>
<?php
				}
?>
				</ul>
			</div>
		</div>
	
<?php
	}
	echo page_list($found, $limit_per_page, $page, 'page', $Bbc->mod['circuit'].'.polling&id='.$id);
