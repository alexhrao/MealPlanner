<html>
	<head>
		<title>Shopping List</title>
		<style type="text/css">
			#shoppingList > td, th, tr, h1, h2 {
	  			padding-left: 10px;
			  	padding-right: 10px;
			  	text-align: center;
			}

			.formShoppingList {
			  	display: flex;
			  	width: 650px;
			}
			#submitShoppingList {
			  	margin-left: 665px;
			  	margin-top: -40px;
			}
			#prefStore {
			  	width: 200px;
			  	height: 40px;
			}
			#startDate, #endDate, #prefStore {
			  	flex-grow: 1;
			}
		</style>
	</head>
</html>

<?php
	function getShoppingListInfo($startDate, $endDate, $prefStore, $arrNoStore) {
		$dbConnection = $GLOBALS['dbConnection'];
		array_unshift($arrNoStore, 0);
		$ingreds = $dbConnection->prepare("SELECT ingredients.IngredientID, recipeingredients.amount, concat(format(recipeingredients.Amount, 2), ' ', ingredients.PricingUnit) AS Ingredient, ingredients.PricingUnit FROM meals INNER JOIN mealdates ON mealdates.MealID = meals.MealID INNER JOIN mealrecipes ON mealrecipes.MealID = meals.MealID INNER JOIN recipes ON recipes.RecipeID = mealrecipes.RecipeID INNER JOIN recipeingredients ON recipeingredients.RecipeID = recipes.RecipeID INNER JOIN ingredients ON ingredients.IngredientID = recipeIngredients.IngredientID WHERE mealdates.MealDate >= :startDate AND mealdates.MealDate <= :endDate;"); # Get ALL ingredients needed!
		$ingreds->bindValue(':startDate', $startDate, PDO::PARAM_STR);
		$ingreds->bindValue(':endDate', $endDate, PDO::PARAM_STR);
		$ingreds->execute();

		$prefs = $dbConnection->prepare("SELECT ingredients.IngredientID, ingredients.Name, concat(format(recipeingredients.Amount, 2), ' ', ingredients.PricingUnit) AS Ingredient, recipeingredients.amount * ingredientstores.Cost AS Cost FROM meals INNER JOIN mealdates ON mealdates.MealID = meals.MealID INNER JOIN mealrecipes ON mealrecipes.MealID = meals.MealID INNER JOIN recipes ON recipes.RecipeID = mealrecipes.RecipeID INNER JOIN recipeingredients ON recipeingredients.RecipeID = recipes.RecipeID INNER JOIN ingredients ON ingredients.IngredientID = recipeIngredients.IngredientID INNER JOIN ingredientstores ON ingredientstores.IngredientID = ingredients.IngredientID WHERE mealdates.MealDate >= :startDate AND mealdates.MealDate <= :endDate;");
		$prefs->bindValue(':startDate', $startDate, PDO::PARAM_STR);
		$prefs->bindValue(':endDate', $endDate, PDO::PARAM_STR);
		$prefs->execute();

		$arrIngred = array();
		$arrAmount = array();
		$arrPref = array(0);
		$arrPrefCost = array();
		while ($row = $prefs->fetch(PDO::FETCH_ASSOC)) {
			$arrPref[] = $row['IngredientID'];
			$arrPrefCost[(int) $row['IngredientID']] = array((float) $row['Cost'], $row['Ingredient']);
		}
		
		while ($row = $ingreds->fetch(PDO::FETCH_ASSOC)) {
			$intKey = array_search($row['IngredientID'], $arrPref);
			if (!$intKey) {
				$arrIngred[(int) $row['IngredientID']] = array((float) $row['amount'], $row['Ingredient']);
			}
		}
		unset($arrPref[0]);
		$arrAltCost = array();
		$arrAltStore = array();
		foreach ($arrIngred as $k => $v) {
			$stores = $dbConnection->prepare("SELECT ingredientstores.StoreID, ingredientstores.Cost FROM ingredientstores WHERE ingredientstores.IngredientID = :id");
			$stores->bindValue(':id', $k, PDO::PARAM_INT);
			$stores->execute();
			$arrCosts = array();
			while ($row = $stores->fetch(PDO::FETCH_ASSOC)) {
				if (!array_search($row['StoreID'], $arrNoStore)) {
					$arrCosts[(int) $row['StoreID']] = (float) $row['Cost'] * $v[0];
				}
			}
			if (!empty($arrCosts)) {
				$minCost = min($arrCosts);
				$minStore = array_search($minCost, $arrCosts);
				$arrAltCost[(int) $k] = array((float) $minCost, $v[1]);
				$arrAltStore[(int) $k] = (int) $minStore;
			}
			else {
				$arrAltCost[$k] = array((float) 0, $v[1]);
				$arrAltStore[$k] = 0;
			}
		}

		$arrStores = array($prefStore);
		$arrTemp = array_unique($arrAltStore);
		foreach ($arrTemp as $k => $v) {
			$arrStores[] = $v;
		}

		$arrList = array();
		foreach ($arrStores as $k => $v) {
			$arrList[$v] = array();
		}
		foreach ($arrPref as $k => $v) {
			$arrList[$prefStore][$v] = $arrPrefCost[$v];
		}
		foreach ($arrAltStore as $k => $v) {
			$arrList[$v][$k] = $arrAltCost[$k];
		}

		foreach ($arrList as $k => $v) {
			foreach ($v as $ingredTemp => $arrCostTemp) {

				$ingredNames = $dbConnection->prepare("SELECT ingredients.Name FROM ingredients WHERE ingredients.IngredientID = :id");
				$ingredNames->bindValue(':id', $ingredTemp, PDO::PARAM_INT);
				$ingredNames->execute();
				$row = $ingredNames->fetch(PDO::FETCH_ASSOC);
				$v[$row['Name']] = $arrCostTemp;
				unset($v[$ingredTemp]);
			}
			$storeNames = $dbConnection->prepare("SELECT stores.Name FROM stores WHERE stores.StoreID = :storeID");
			if ($k==0) {
				$arrList['<em>Unknown</em>'] = $v;
				unset($arrList[$k]);
			}
			else {
				$storeNames->bindValue(':storeID', $k, PDO::PARAM_INT);
				$storeNames->execute();
				$row = $storeNames->fetch(PDO::FETCH_ASSOC);
				$arrList[$row['Name']] = $v;
				unset($arrList[$k]);
			}
		}
		return $arrList;
	}

	function getStoreInfo()
	{
		$dbConnection = $GLOBALS['dbConnection'];
		$stores = $dbConnection->prepare("SELECT stores.StoreID, stores.Name FROM stores;");
		$stores->execute();
		$arrStores = array();
		while ($row = $stores->fetch(PDO::FETCH_ASSOC)) {
			$arrStores[(int) $row['StoreID']] = $row['Name'];
		}
		return $arrStores;
	}

	$dir = dirname(__FILE__);
	include("$dir/defaultLang.php");
	include("$dir/language.php");
	include("$dir/lib.php");
	include_once("$dir/header.php");

	if (isset($_POST['submit'])) {
		$startDate = $_POST['startDate'];
		$endDate = $_POST['endDate'];
		$prefStore = $_POST['prefStore'];
		$arrExempt = array();
		$arrStores = getStoreInfo();
		foreach ($arrStores as $ID => $name) {
			if (!is_null($_POST[$ID])) {
				$arrExempt[] = (int) $ID;
			}
		}
		$prefStore = (int) $prefStore;
	}
	else {
		$arrStores = getStoreInfo();
		echo   "<div class=\"container-fluid\">
					<form action=\"shoppingList.php\" id=\"shoppingListForm\" method=\"post\">
						<div class=\"row\" style=\"padding:10px;\">
							<div class=\"col-sm-3 form-group\">
								<label for=\"startDate\">Start Date:</label>
								<input type=\"date\" class=\"form-control\" id=\"startDate\">
							</div>
							<div class=\"col-sm-3 form-group\">
								<label for=\"endDate\">End Date:</label>
								<input type=\"date\" class=\"form-control\" id=\"endDate\">
							</div>
							<div class=\"col-sm-3 form-group\">
								<label for=\"prefStore\">Preferred Store:</label>
								<select class=\"form-control\" id=\"prefStore\">";
		foreach ($arrStores as $ID => $name) {
			echo 				   "<option value=$ID>$name</option>";
		}
		echo				   "</select>
							</div>
							<div class=\"col-sm-3 form-group\">
							<label for=\"exemptStores\">Exempt Stores:</label>
							<select multiple class=\"form-control\" id=\"exemptStores\">";
		foreach ($arrStores as $ID => $name) {
			echo 			   "<option name=\"$ID\" value=$ID>$name</option>";
		}
		echo			   "</select>
						</div>
						<div class=\"col-sm-3 form-group\">
							<button type=\"submit\" name=\"submit\" form=\"shoppingListForm\" value=\"Generate Shopping List\" class=\"btn btn-primary btn-md\">Generate Shopping List</button>
						</div>
					</div>
				</form>
			</div>";
		exit;
	}
	$arrList = getShoppingListInfo($startDate, $endDate, $prefStore, $arrExempt);
	$intTotal = 0;
	echo "<h1 id=\"shoppingList\"><strong>Shopping List</strong></h1><br>";
	foreach ($arrList as $store => $info) {
		$intCost = 0;
		echo "<h2 id=\"shoppingList\"><strong>$store</strong></h2>";
		echo "<table class=\"shoppingList\" border=\"5px\" width=\"200px\" align=\"center\"><tr><th><em>Ingredient</em></th><th><em>Amount</em></th><th><em>Cost</em></th></tr>";
		foreach ($info as $ingred => $arrCost) {
			echo "<tr id=\"shoppingList\"><td>$ingred</td><td>" . $arrCost[1] . "</td><td>$" . number_format($arrCost[0], 2, ".", ",") . "</td></tr>";
			$intCost = $intCost + $arrCost[0];
			$intTotal = $intTotal + $arrCost[0];
		}
		echo "<tr id=\"shoppingList\"><td colspan=\"2\"><strong>Estimated Cost:</strong></td><td><strong>$" . number_format($intCost, 2, ".", ",") . "</strong></td></tr></table>";
	}
	echo "<br>";
	echo "<h2 id = \"shoppingList\"><em>Total Estimated Cost: $" . number_format($intTotal, 2, ".", ",") . "</h2>";
	include_once("$dir/footer.php");
?>