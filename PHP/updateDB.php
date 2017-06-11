<?php
	// check this file's MD5 to make sure it wasn't called before
	$prevMD5=@implode('', @file(dirname(__FILE__).'/setup.md5'));
	$thisMD5=md5(@implode('', @file("./updateDB.php")));
	if($thisMD5==$prevMD5){
		$setupAlreadyRun=true;
	}else{
		// set up tables
		if(!isset($silent)){
			$silent=true;
		}

		// set up tables
		setupTable('mealdates', "create table if not exists `mealdates` (   `MealDateID` INT(11) not null auto_increment , primary key (`MealDateID`), `MealID` INT(11) not null , `MealDate` DATE not null , `MealTime` VARCHAR(50) ) CHARSET latin1", $silent);
		setupIndexes('mealdates', array('MealID'));
		setupTable('meals', "create table if not exists `meals` (   `MealID` INT(11) not null auto_increment , primary key (`MealID`), `Name` VARCHAR(50) not null , unique(`Name`), `Description` TEXT , `MealTime` VARCHAR(50) ) CHARSET latin1", $silent);
		setupTable('recipes', "create table if not exists `recipes` (   `RecipeID` INT(11) not null auto_increment , primary key (`RecipeID`), `Name` VARCHAR(50) not null , unique(`Name`), `DateCreated` DATE , `Instructions` TEXT , `Description` TEXT , `SourceID` INT(11) ) CHARSET latin1", $silent);
		setupIndexes('recipes', array('SourceID'));
		setupTable('ingredients', "create table if not exists `ingredients` (   `IngredientID` INT(11) not null auto_increment , primary key (`IngredientID`), `Name` VARCHAR(50) not null , unique(`Name`), `PricingUnit` VARCHAR(50) , `RecipeUnit` VARCHAR(50) , `PluralForm` VARCHAR(50) , `Description` TEXT ) CHARSET latin1", $silent);
		setupTable('mealrecipes', "create table if not exists `mealrecipes` (   `MealRecipeID` INT(11) not null auto_increment , primary key (`MealRecipeID`), `MealID` INT(11) not null , `RecipeID` INT(11) not null ) CHARSET latin1", $silent);
		setupIndexes('mealrecipes', array('MealID','RecipeID'));
		setupTable('recipeingredients', "create table if not exists `recipeingredients` (   `RecipeIngredientID` INT(11) not null auto_increment , primary key (`RecipeIngredientID`), `RecipeID` INT(11) not null , `IngredientID` INT(11) not null , `Amount` FLOAT(5,5) unsigned default '0.00' ) CHARSET latin1", $silent);
		setupIndexes('recipeingredients', array('RecipeID','IngredientID'));
		setupTable('ingredientstores', "create table if not exists `ingredientstores` (   `IngredientStoreID` INT(11) not null auto_increment , primary key (`IngredientStoreID`), `IngredientID` INT(11) not null , `StoreID` INT(11) not null , `Cost` DECIMAL(13,4) ) CHARSET latin1", $silent);
		setupIndexes('ingredientstores', array('IngredientID','StoreID'));
		setupTable('sources', "create table if not exists `sources` (   `SourceID` INT(11) not null auto_increment , primary key (`SourceID`), `FullName` VARCHAR(50) not null , unique(`FullName`), `PhoneNumber` VARCHAR(50) , `Description` VARCHAR(100) ) CHARSET latin1", $silent);
		setupTable('stores', "create table if not exists `stores` (   `StoreID` INT(11) not null auto_increment , primary key (`StoreID`), `Name` VARCHAR(50) not null , `PhoneNumber` VARCHAR(20) , `Location` TEXT , `Notes` TEXT ) CHARSET latin1", $silent);


		// save MD5
		if($fp=@fopen(dirname(__FILE__).'/setup.md5', 'w')){
			fwrite($fp, $thisMD5);
			fclose($fp);
		}
	}


	function setupIndexes($tableName, $arrFields){
		if(!is_array($arrFields)){
			return false;
		}

		foreach($arrFields as $fieldName){
			if(!$res=@db_query("SHOW COLUMNS FROM `$tableName` like '$fieldName'")){
				continue;
			}
			if(!$row=@db_fetch_assoc($res)){
				continue;
			}
			if($row['Key']==''){
				@db_query("ALTER TABLE `$tableName` ADD INDEX `$fieldName` (`$fieldName`)");
			}
		}
	}


	function setupTable($tableName, $createSQL='', $silent=true, $arrAlter=''){
		global $Translation;
		ob_start();

		echo '<div style="padding: 5px; border-bottom:solid 1px silver; font-family: verdana, arial; font-size: 10px;">';

		// is there a table rename query?
		if(is_array($arrAlter)){
			$matches=array();
			if(preg_match("/ALTER TABLE `(.*)` RENAME `$tableName`/", $arrAlter[0], $matches)){
				$oldTableName=$matches[1];
			}
		}

		if($res=@db_query("select count(1) from `$tableName`")){ // table already exists
			if($row = @db_fetch_array($res)){
				echo str_replace("<TableName>", $tableName, str_replace("<NumRecords>", $row[0],$Translation["table exists"]));
				if(is_array($arrAlter)){
					echo '<br>';
					foreach($arrAlter as $alter){
						if($alter!=''){
							echo "$alter ... ";
							if(!@db_query($alter)){
								echo '<span class="label label-danger">' . $Translation['failed'] . '</span>';
								echo '<div class="text-danger">' . $Translation['mysql said'] . ' ' . db_error(db_link()) . '</div>';
							}else{
								echo '<span class="label label-success">' . $Translation['ok'] . '</span>';
							}
						}
					}
				}else{
					echo $Translation["table uptodate"];
				}
			}else{
				echo str_replace("<TableName>", $tableName, $Translation["couldnt count"]);
			}
		}else{ // given tableName doesn't exist

			if($oldTableName!=''){ // if we have a table rename query
				if($ro=@db_query("select count(1) from `$oldTableName`")){ // if old table exists, rename it.
					$renameQuery=array_shift($arrAlter); // get and remove rename query

					echo "$renameQuery ... ";
					if(!@db_query($renameQuery)){
						echo '<span class="label label-danger">' . $Translation['failed'] . '</span>';
						echo '<div class="text-danger">' . $Translation['mysql said'] . ' ' . db_error(db_link()) . '</div>';
					}else{
						echo '<span class="label label-success">' . $Translation['ok'] . '</span>';
					}

					if(is_array($arrAlter)) setupTable($tableName, $createSQL, false, $arrAlter); // execute Alter queries on renamed table ...
				}else{ // if old tableName doesn't exist (nor the new one since we're here), then just create the table.
					setupTable($tableName, $createSQL, false); // no Alter queries passed ...
				}
			}else{ // tableName doesn't exist and no rename, so just create the table
				echo str_replace("<TableName>", $tableName, $Translation["creating table"]);
				if(!@db_query($createSQL)){
					echo '<span class="label label-danger">' . $Translation['failed'] . '</span>';
					echo '<div class="text-danger">' . $Translation['mysql said'] . db_error(db_link()) . '</div>';
				}else{
					echo '<span class="label label-success">' . $Translation['ok'] . '</span>';
				}
			}
		}

		echo "</div>";

		$out=ob_get_contents();
		ob_end_clean();
		if(!$silent){
			echo $out;
		}
	}
?>