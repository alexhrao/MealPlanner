<script>
	$j(function(){
		var tn = 'ingredientstores';

		/* data for selected record, or defaults if none is selected */
		var data = {
			IngredientID: { id: '<?php echo $rdata['IngredientID']; ?>', value: '<?php echo $rdata['IngredientID']; ?>', text: '<?php echo $jdata['IngredientID']; ?>' },
			StoreID: { id: '<?php echo $rdata['StoreID']; ?>', value: '<?php echo $rdata['StoreID']; ?>', text: '<?php echo $jdata['StoreID']; ?>' }
		};

		/* initialize or continue using AppGini.cache for the current table */
		AppGini.cache = AppGini.cache || {};
		AppGini.cache[tn] = AppGini.cache[tn] || AppGini.ajaxCache();
		var cache = AppGini.cache[tn];

		/* saved value for IngredientID */
		cache.addCheck(function(u, d){
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'IngredientID' && d.id == data.IngredientID.id)
				return { results: [ data.IngredientID ], more: false, elapsed: 0.01 };
			return false;
		});

		/* saved value for StoreID */
		cache.addCheck(function(u, d){
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'StoreID' && d.id == data.StoreID.id)
				return { results: [ data.StoreID ], more: false, elapsed: 0.01 };
			return false;
		});

		cache.start();
	});
</script>

