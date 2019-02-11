_Bbc(function($) {
	$('.dependentdropdown').each(function(a){
		var b = $(this).attr('rel')+'_dependentdropdown';
		if (typeof window[b]!='undefined') {
			var c = window[b];
			var d = $(this).find('select');
			for (var e = 0; e < d.length; e++) {
				var f = d[e];
				$(f).html('');
				for (var g = 0; g < c.length; g++) {
					var h = c[g];
					var i = $(f).attr('rel')==h[0] ? ' selected' : '';
					$(f).append('<option value="'+h[0]+'"'+i+'>'+h[1]+'</option>');
				};
				var j = f.selectedIndex;
				c = (typeof c[j]!='undefined')?((typeof c[j][2]!='undefined')?c[j][2]:[]):[];
				if ($(d[(e+1)]).length) {
					$(f).on('change',{object:d,option:window[b],target:e},function(event){
						var a = event.data;
						var b = $(a.object).get(a.target+1);
						var c = a.option;
						for (var i = 0; i <= a.target; i++) {
							var j = $(a.object).get(i).selectedIndex;
							c = (typeof c[j]!='undefined')?((typeof c[j][2]!='undefined')?c[j][2]:[]):[];
						};
						b.selectedIndex=0;
						$(b).html('');
						for (var i = 0; i < c.length; i++)
						{
							var d = c[i];
							var j = $(b).attr('rel')==d[0] ? ' selected' : '';
							$(b).append('<option value="'+d[0]+'"'+j+'>'+d[1]+'</option>');
						};
						$(b).trigger('change');
					});
				};
			};
		};
	});
});
