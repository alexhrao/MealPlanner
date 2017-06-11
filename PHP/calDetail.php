<!DOCTYPE html>
<html>
<head>
	<title>Hello World!</title>
	<style type="text/css">
		.description1 {
			color: #C0C0C0 !important;
			font-style: italic !important;
			padding-left: 15px !important;
			line-height: 85% !important;
			text-align: center !important;
		}

		.instructions {
			color: #808080 !important;
			font-style: italic !important;
			padding-left: 15px !important;
			line-height: 85% !important;
			text-align: center !important;
		}

		.ingredient {
			font-style: italic !important;
			line-height: 85% !important;
			text-align: left !important;
			width: 200px !important;
			margin: 0 auto !important;
			padding-left: 150px;
		}

		h2, h3 {
			line-height: 1% !important;
		}
	</style>
</head>
<body>

</body>
</html>

<?php


function getMealInformation($MealID)
{
	$usrn = "alexhrao";
	$pass = "Arodponyboy678";
	$db = "meal_planner_db";
	mysql_connect("mysql20.ezhostingserver.com:3306", $usrn, $pass);
	mysql_select_db($db) or die("Unable to select DB!");
	$queryMeal = "SELECT meals.MealID, meals.Name, meals.Description FROM meals WHERE meals.MealID = $MealID;";
	$queryRecipes = "SELECT recipes.RecipeID, recipes.Name, recipes.Instructions FROM meals INNER JOIN mealrecipes ON mealrecipes.MealID = $MealID INNER JOIN recipes ON recipes.RecipeID = mealrecipes.RecipeID;";
	$resultMeal = mysql_query($queryMeal);
	$resultRecipe = mysql_query($queryRecipes);
	while ($row1 = mysql_fetch_assoc($resultMeal)) {
		$arrMeal[$row1['MealID']] = array($row1['Name'], $row1['Description'], array());
		$arrRecs = array();
		while ($row2 = mysql_fetch_assoc($resultRecipe)) {
			$arrRecs[$row2['RecipeID']] = array($row2['Name'], $row2['Instructions'], array());
		}
		foreach ($arrRecs as $ID => $info) {
			$queryIngred = "SELECT ingredients.Name as Name, concat(recipeingredients.amount, \" \", ingredients.PricingUnit) AS Amount FROM meals INNER JOIN mealrecipes ON mealrecipes.MealID = $MealID INNER JOIN recipes ON recipes.RecipeID=$ID INNER JOIN recipeIngredients ON recipeingredients.RecipeID = recipes.RecipeID INNER JOIN ingredients ON ingredients.IngredientID = recipeingredients.IngredientID;";
			$resultIngred = mysql_query($queryIngred);
			$counter = 0;
			while ($row3 = mysql_fetch_assoc($resultIngred)) {
				if ($counter == 0) {
					$counter = 1;
				} else {
					$counter = 0;
					continue;
				}
				$arrRecs[$ID][2][] = array($row3['Name'], $row3['Amount']);
				#var_dump($row3['Name']);
			}
		}
		$arrMeal[$row1['MealID']][2] = $arrRecs;
	}
	return $arrMeal;
	mysql_close();
}

echo $_POST['MealID'];
$arrMeal = getMealInformation($_POST['MealID']);
echo "<div align=\"center\">";
foreach ($arrMeal as $MID => $Minfo) {
	echo "<h2 align=\"center\">$Minfo[0]</h2>";
	echo "<div class=\"description1\" align=\"center\">$Minfo[1]</div>";
	foreach ($Minfo[2] as $RID => $Rinfo) {
		echo "<h3 align=\"center\">$Rinfo[0]</h3>";
		#var_dump($Rinfo);
		echo "<div class=\"instructions\" align=\"center\">$Rinfo[1]<div class=\"ingredient\"><ul>";
		foreach ($Rinfo[2] as $IID => $Iinfo) {
			echo "<li>$Iinfo[0], $Iinfo[1]</li>";
		}
		echo "</ul></div>";
	}
	echo "</div></div>";
}
echo "</div>";
?>