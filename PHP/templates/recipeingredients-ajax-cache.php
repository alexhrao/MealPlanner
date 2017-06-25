<script>
	$j(function(){
		var tn = 'recipeingredients';

		/* data for selected record, or defaults if none is selected */
		var data = {
			RecipeID: { id: '<?php echo $rdata['RecipeID']; ?>', value: '<?php echo $rdata['RecipeID']; ?>', text: '<?php echo $jdata['RecipeID']; ?>' },
			IngredientID: { id: '<?php echo $rdata['IngredientID']; ?>', value: '<?php echo $rdata['IngredientID']; ?>', text: '<?php echo $jdata['IngredientID']; ?>' }
		};

		/* initialize or continue using AppGini.cache for the current table */
		AppGini.cache = AppGini.cache || {};
		AppGini.cache[tn] = AppGini.cache[tn] || AppGini.ajaxCache();
		var cache = AppGini.cache[tn];

		/* saved value for RecipeID */
		cache.addCheck(function(u, d){
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'RecipeID' && d.id == data.RecipeID.id)
				return { results: [ data.RecipeID ], more: false, elapsed: 0.01 };
			return false;
		});

		/* saved value for IngredientID */
		cache.addCheck(function(u, d){
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'IngredientID' && d.id == data.IngredientID.id)
				return { results: [ data.IngredientID ], more: false, elapsed: 0.01 };
			return false;
		});

		cache.start();
	});
</script>

