<div class="panel panel-default">
  <div class="panel-heading" id="searchControl"></div>
  <div class="panel-body" id="searchResult"></div>
</div>

<script language="Javascript" type="text/javascript">
  google.load("search", "1");
  function OnLoad() {
		var siteSearch = new google.search.WebSearch();
    siteSearch.setSiteRestriction('<?php echo $m[1];?>');

		var options = new google.search.SearcherOptions();
		options.setExpandMode(google.search.SearchControl.EXPAND_MODE_OPEN);
		options.setRoot(document.getElementById("searchResult"));
		options.setNoResultsString('<?php echo msg(lang('not found'),'danger');?>');

		var searchControl = new google.search.SearchControl();
		searchControl.addSearcher(siteSearch, options);
		searchControl.setLinkTarget(google.search.Search.LINK_TARGET_SELF);
    searchControl.draw(document.getElementById("searchControl"));
		<?php
		if(!empty($keyword))
		{
			?>
			searchControl.execute("<?php echo $keyword;?>");
			<?php
		}
		?>
  }
  google.setOnLoadCallback(OnLoad);
</script>