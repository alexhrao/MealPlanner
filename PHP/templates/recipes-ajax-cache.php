<script>
	$j(function(){
		var tn = 'recipes';

		/* data for selected record, or defaults if none is selected */
		var data = {
			SourceID: { id: '<?php echo $rdata['SourceID']; ?>', value: '<?php echo $rdata['SourceID']; ?>', text: '<?php echo $jdata['SourceID']; ?>' }
		};

		/* initialize or continue using AppGini.cache for the current table */
		AppGini.cache = AppGini.cache || {};
		AppGini.cache[tn] = AppGini.cache[tn] || AppGini.ajaxCache();
		var cache = AppGini.cache[tn];

		/* saved value for SourceID */
		cache.addCheck(function(u, d){
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'SourceID' && d.id == data.SourceID.id)
				return { results: [ data.SourceID ], more: false, elapsed: 0.01 };
			return false;
		});

		cache.start();
	});
</script>

