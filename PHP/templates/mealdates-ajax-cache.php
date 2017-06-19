<script>
	$j(function(){
		var tn = 'mealdates';

		/* data for selected record, or defaults if none is selected */
		var data = {
			MealID: { id: '<?php echo $rdata['MealID']; ?>', value: '<?php echo $rdata['MealID']; ?>', text: '<?php echo $jdata['MealID']; ?>' }
		};

		/* initialize or continue using AppGini.cache for the current table */
		AppGini.cache = AppGini.cache || {};
		AppGini.cache[tn] = AppGini.cache[tn] || AppGini.ajaxCache();
		var cache = AppGini.cache[tn];

		/* saved value for MealID */
		cache.addCheck(function(u, d){
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'MealID' && d.id == data.MealID.id)
				return { results: [ data.MealID ], more: false, elapsed: 0.01 };
			return false;
		});

		cache.start();
	});
</script>

