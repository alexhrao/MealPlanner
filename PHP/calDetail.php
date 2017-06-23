<!DOCTYPE html>
<html>
<head>
	
	<style type="text/css">
		.description {
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
			text-align: left !important;
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
	$dbConnection = $GLOBALS['dbConnection'];
	$meals = $dbConnection->prepare("SELECT meals.MealID, meals.Name, meals.Description FROM meals WHERE meals.MealID = :mealID;");
	$meals->bindValue(':mealID', $MealID, PDO::PARAM_INT);
	$meals->execute();

	$mealTimes = $dbConnection->prepare("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(recipes.PrepTime))) AS TotalTime FROM meals INNER JOIN mealrecipes ON mealrecipes.MealID = meals.MealID INNER JOIN recipes ON recipes.RecipeID = mealrecipes.RecipeID WHERE mealrecipes.MealID = :mealID;");
	$mealTimes->bindValue(':mealID', $MealID, PDO::PARAM_INT);
	$mealTimes->execute();

	$recipes = $dbConnection->prepare("SELECT recipes.RecipeID, recipes.Name, recipes.Instructions, recipes.PrepTime, recipes.Servings FROM meals INNER JOIN mealrecipes ON mealrecipes.MealID = meals.MealID INNER JOIN recipes ON recipes.RecipeID = mealrecipes.RecipeID WHERE mealrecipes.MealID = :mealID;");
	$recipes->bindValue(':mealID', $MealID, PDO::PARAM_INT);
	$recipes->execute();

	while ($row1 = $meals->fetch(PDO::FETCH_ASSOC)) {
		$time = $mealTimes->fetch(PDO::FETCH_ASSOC);
		$arrMeal[$row1['MealID']] = array($row1['Name'], $row1['Description'], $time['TotalTime'], array());
		$arrRecs = array();
		while ($row2 = $recipes->fetch(PDO::FETCH_ASSOC)) {
			$arrRecs[$row2['RecipeID']] = array($row2['Name'], $row2['Instructions'], $row2['PrepTime'], $row2['Servings'], array());
		}
		foreach ($arrRecs as $RecipeID => $info) {
			$ingreds = $dbConnection->prepare("SELECT ingredients.Name AS Name, CONCAT(recipeingredients.amount, \" \", ingredients.PricingUnit) AS Amount FROM meals INNER JOIN mealrecipes ON mealrecipes.MealID = meals.MealID INNER JOIN recipes ON recipes.RecipeID = mealrecipes.RecipeID INNER JOIN recipeIngredients ON recipeingredients.RecipeID = recipes.RecipeID INNER JOIN ingredients ON ingredients.IngredientID = recipeingredients.IngredientID WHERE mealrecipes.MealID = :mealID AND recipeingredients.RecipeID = :recipeID;");
			$ingreds->bindValue(':mealID', $MealID, PDO::PARAM_INT);
			$ingreds->bindValue(':recipeID', $RecipeID, PDO::PARAM_INT);
			$ingreds->execute();
			while ($row3 = $ingreds->fetch(PDO::FETCH_ASSOC)) {
				$arrRecs[$RecipeID][4][] = array($row3['Name'], $row3['Amount']);
			}
		}
		$arrMeal[$row1['MealID']][3] = $arrRecs;
	}
	return $arrMeal;
	mysql_close();
}

$dir = dirname(__FILE__);
include("$dir/defaultLang.php");
include("$dir/language.php");
include("$dir/lib.php");

$arrMeal = getMealInformation($_POST['MealID']);
echo "<html><body>";
echo "<div align=\"center\">";
foreach ($arrMeal as $MID => $Minfo) {
	echo "<h2 align=\"center\">$Minfo[0]</h2>";
	echo "<h4 align=\"center\">Time: $Minfo[2]</h4>";
	echo "<div class=\"description\" align=\"center\">$Minfo[1]</div>";
	echo "<p>" . $GLOBALS['z'] . "</p>";
	foreach ($Minfo[3] as $RID => $Rinfo) {
		echo "<h3 align=\"center\">$Rinfo[0]</h3>";
		echo "<h4 align=\"center\">Servings: $Rinfo[3] - Time: $Rinfo[2]</h3>";
		echo "<div class=\"instructions\"><p class=\"instructions\">$Rinfo[1]</p></div><div class=\"ingredient\"><ul>";
		foreach ($Rinfo[4] as $IID => $Iinfo) {
			echo "<li>$Iinfo[0], $Iinfo[1]</li>";
		}
		echo "</ul></div>";
	}
}
echo "</div>";
echo "</body></html>";
echo "<html><head><title>$Minfo[0]</title></head></html>";
?>