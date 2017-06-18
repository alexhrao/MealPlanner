<?php
	function get_ShoppingListInfo($startDate, $endDate, $prefStore, $arrNoStore) {
		$dbServer = config('dbServer');
		$dbUsername = config('dbUsername');
		$dbPassword = config('dbPassword');
		$dbDatabase = config('dbDatabase');
		mysql_connect($dbServer, $dbUsername, $dbPassword);
		mysql_select_db($dbDatabase) or die("Unable to select DB!");
		array_unshift($arrNoStore, 0);
		$queryIngred = "SELECT ingredients.IngredientID, recipeingredients.amount, concat(format(recipeingredients.Amount, 2), ' ', ingredients.PricingUnit) as Ingredient, ingredients.PricingUnit from meals inner join mealdates on mealdates.MealID = meals.MealID inner join mealrecipes on mealrecipes.MealID = meals.MealID inner join recipes on recipes.RecipeID = mealrecipes.RecipeID inner join recipeingredients on recipeingredients.RecipeID = recipes.RecipeID inner join ingredients on ingredients.IngredientID = recipeIngredients.IngredientID where mealdates.MealDate >= \"$startDate\" and mealdates.MealDate <= \"$endDate\";"; # Get ALL ingredients needed!
		$ingredID = mysql_query($queryIngred);
		$queryPref = "SELECT ingredients.IngredientID, ingredients.Name, concat(format(recipeingredients.Amount, 2), ' ', ingredients.PricingUnit) as Ingredient, recipeingredients.amount * ingredientstores.Cost as Cost from meals inner join mealdates on mealdates.MealID = meals.MealID inner join mealrecipes on mealrecipes.MealID = meals.MealID inner join recipes on recipes.RecipeID = mealrecipes.RecipeID inner join recipeingredients on recipeingredients.RecipeID = recipes.RecipeID inner join ingredients on ingredients.IngredientID = recipeIngredients.IngredientID inner join ingredientstores on ingredientstores.IngredientID = ingredients.IngredientID where mealdates.MealDate >= \"$startDate\" and mealdates.MealDate <= \"$endDate\";";

		$result = mysql_query($queryPref);
		$arrIngred = array();
		$arrAmount = array();
		$arrPref = array(0);
		$arrPrefCost = array();
		while ($row = mysql_fetch_assoc($result)) {
			$arrPref[] = $row['IngredientID'];
			$arrPrefCost[(int) $row['IngredientID']] = array((float) $row['Cost'], $row['Ingredient']);
		}
		
		while ($row = mysql_fetch_assoc($ingredID)) {
			$intKey = array_search($row['IngredientID'], $arrPref);
			if (!$intKey) {
				$arrIngred[(int) $row['IngredientID']] = array((float) $row['amount'], $row['Ingredient']);
			}
		}
		unset($arrPref[0]);
		$arrAltCost = array();
		$arrAltStore = array();
		foreach ($arrIngred as $k => $v) {
			$queryStore = "select ingredientstores.StoreID, ingredientstores.Cost from ingredientstores where ingredientstores.IngredientID = $k";
			$resultTemp = mysql_query($queryStore);
			$arrCosts = array();
			while ($row = mysql_fetch_assoc($resultTemp)) {
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
				$queryIName = "select ingredients.Name from ingredients where ingredients.IngredientID = $ingredTemp";
				$resultTemp = mysql_query($queryIName);
				$row = mysql_fetch_assoc($resultTemp);
				$v[$row['Name']] = $arrCostTemp;
				unset($v[$ingredTemp]);
			}
			$querySName = "select stores.Name from stores where stores.StoreID = $k";
			if ($k==0) {
				$arrList['<em>Unknown</em>'] = $v;
				unset($arrList[$k]);
			}
			else {
				$resultTemp = mysql_query($querySName);
				$row = mysql_fetch_assoc($resultTemp);
				$arrList[$row['Name']] = $v;
				unset($arrList[$k]);
			}
		}
		return $arrList;
		mysql_close();
	}
	function get_StoreInfo()
	{
		$dbServer = config('dbServer');
		$dbUsername = config('dbUsername');
		$dbPassword = config('dbPassword');
		$dbDatabase = config('dbDatabase');
		mysql_connect($dbServer, $dbUsername, $dbPassword);
		mysql_select_db($dbDatabase) or die("Unable to select DB!");
		$query = "Select stores.StoreID, stores.Name from stores;";
		$result = mysql_query($query);
		$arrStores = array();
		while ($row = mysql_fetch_assoc($result)) {
			$arrStores[(int) $row['StoreID']] = $row['Name'];
		}
		return $arrStores;
		mysql_close();
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
		$arrStores = get_StoreInfo();
		foreach ($arrStores as $ID => $name) {
			if (!is_null($_POST[$ID])) {
				$arrExempt[] = (int) $ID;
			}
		}
		# need yyyymmdd
		#$startDate = substr($startDate, 0, 4) . substr($startDate, 5, 2) . substr($startDate, 8, 2);
		#$endDate = substr($endDate, 0, 4) . substr($endDate, 5, 2) . substr($endDate, 8, 2);
		$prefStore = (int) $prefStore;
	}
	else {
		$arrStores = get_StoreInfo();
		echo "<form action=\"shoppingList.php\" name=\"testform\" method=\"post\">
				<div class=\"form\">
					<div id=\"startDate\">
						Start Date: <br><input type=\"date\" name=\"startDate\">
					</div>
					<div id=\"endDate\">
						End Date: <br><input type=\"date\" name=\"endDate\">
					</div>
					<div id=\"prefStore\">
						Preferred Store: <br><select id=\"prefStore\"  name=\"prefStore\">";
		foreach ($arrStores as $ID => $name) {
			echo 			"<option value=$ID>$name</option>";
		}
		echo 			"</select>
					</div>
					<div id=\"exempt\">
						Exempt Stores:<br>";
		foreach ($arrStores as $ID => $name) {
			echo 		"<input type=\"checkbox\" name=\"$ID\" value=$ID>&nbsp;&nbsp;$name<br>";
		}
		echo		"</div>
				</div>
			<div id=\"submit\">
				<input type=\"submit\" name=\"submit\" value=\"Generate Shopping List\">
			</div>
			</form>";
			exit;
		}
	$arrList = get_ShoppingListInfo($startDate, $endDate, $prefStore, $arrExempt);
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