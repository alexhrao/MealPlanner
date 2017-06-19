<script>
	$j(function(){
		var tn = 'mealrecipes';

		/* data for selected record, or defaults if none is selected */
		var data = {
			MealID: { id: '<?php echo $rdata['MealID']; ?>', value: '<?php echo $rdata['MealID']; ?>', text: '<?php echo $jdata['MealID']; ?>' },
			RecipeID: { id: '<?php echo $rdata['RecipeID']; ?>', value: '<?php echo $rdata['RecipeID']; ?>', text: '<?php echo $jdata['RecipeID']; ?>' }
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

		/* saved value for RecipeID */
		cache.addCheck(function(u, d){
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'RecipeID' && d.id == data.RecipeID.id)
				return { results: [ data.RecipeID ], more: false, elapsed: 0.01 };
			return false;
		});

		cache.start();
	});
</script>

