<div class="well">
	<form method="get" id="content_search" class="form-horizontal" role="form">
		<div class="form-group" style="margin-bottom: 0;">
				<div class="col-sm-9 no-right">
					<div class="input-group">
						<div class="input-group-addon"><i class="glyphicon glyphicon-search"></i></div>
							<input class="form-control" type="text" placeholder="<?php echo lang('Enter Keywords'); ?>" name="id" id="content_search_keyword" value="<?php echo htmlentities($keyword);?>"/>
					</div>
				</div>
				<div class="col-sm-1">
					<input type="submit" class="btn btn-default" value="Search" />
				</div>
			</div>
	</form>
</div>
<script type="text/javascript">
	_Bbc(function($){
		$("#content_search").submit(function(e){
			e.preventDefault();
			var a = $("#content_search_keyword");
			if (a.val()=="") {
				alert("<?php echo lang('Please Insert Keyword!'); ?>");
				a.focus();
			}else{
				var b = _URL+'search.htm';
				var c = encodeURIComponent(a.val());
				if (c.length>12) {
					b += '?id=';
				}else{
					b += '/';
				}
				b += c;
				document.location.href = b;
			}
		})
	});
</script>