<div class="well">
	<form method="get" action=""<?php echo $add_js;?> class="form-horizontal" role="form">
		<div class="form-group" style="margin-bottom: 0;">
				<div class="col-sm-9 no-right">
					<div class="input-group">
						<div class="input-group-addon"><i class="glyphicon glyphicon-search"></i></div>
							<input type="hidden" name="mod" size="30" value="<?php echo $Bbc->mod['name'].'.result';?>" />
							<input class="form-control" type="text" placeholder="Enter Keywords" name="id" size="30" value="<?php echo htmlentities($keyword);?>"/>
					</div>
				</div>
				<div class="col-sm-1">
					<input type="submit" class="btn btn-default" value="Search" />
				</div>
			</div>
		<?php echo $add_input;?>
	</form>
</div>
