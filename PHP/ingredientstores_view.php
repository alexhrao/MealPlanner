<?php
// This script and data application were generated by AppGini 5.62
// Download AppGini for free from https://bigprof.com/appgini/download/

	$currDir=dirname(__FILE__);
	include("$currDir/defaultLang.php");
	include("$currDir/language.php");
	include("$currDir/lib.php");
	@include("$currDir/hooks/ingredientstores.php");
	include("$currDir/ingredientstores_dml.php");

	// mm: can the current member access this page?
	$perm=getTablePermissions('ingredientstores');
	if(!$perm[0]){
		echo error_message($Translation['tableAccessDenied'], false);
		echo '<script>setTimeout("window.location=\'index.php?signOut=1\'", 2000);</script>';
		exit;
	}

	$x = new DataList;
	$x->TableName = "ingredientstores";

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV = array(   
		"`ingredientstores`.`IngredientStoreID`" => "IngredientStoreID",
		"IF(    CHAR_LENGTH(`ingredients1`.`Name`) || CHAR_LENGTH(`ingredients1`.`PricingUnit`), CONCAT_WS('',   `ingredients1`.`Name`, ' - ', `ingredients1`.`PricingUnit`), '') /* Ingredient */" => "IngredientID",
		"IF(    CHAR_LENGTH(`stores1`.`Name`), CONCAT_WS('',   `stores1`.`Name`), '') /* Store */" => "StoreID",
		"CONCAT('$', FORMAT(`ingredientstores`.`Cost`, 2))" => "Cost"
	);
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = array(   
		1 => '`ingredientstores`.`IngredientStoreID`',
		2 => 2,
		3 => '`stores1`.`Name`',
		4 => '`ingredientstores`.`Cost`'
	);

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV = array(   
		"`ingredientstores`.`IngredientStoreID`" => "IngredientStoreID",
		"IF(    CHAR_LENGTH(`ingredients1`.`Name`) || CHAR_LENGTH(`ingredients1`.`PricingUnit`), CONCAT_WS('',   `ingredients1`.`Name`, ' - ', `ingredients1`.`PricingUnit`), '') /* Ingredient */" => "IngredientID",
		"IF(    CHAR_LENGTH(`stores1`.`Name`), CONCAT_WS('',   `stores1`.`Name`), '') /* Store */" => "StoreID",
		"CONCAT('$', FORMAT(`ingredientstores`.`Cost`, 2))" => "Cost"
	);
	// Fields that can be filtered
	$x->QueryFieldsFilters = array(   
		"`ingredientstores`.`IngredientStoreID`" => "IngredientStoreID",
		"IF(    CHAR_LENGTH(`ingredients1`.`Name`) || CHAR_LENGTH(`ingredients1`.`PricingUnit`), CONCAT_WS('',   `ingredients1`.`Name`, ' - ', `ingredients1`.`PricingUnit`), '') /* Ingredient */" => "Ingredient",
		"IF(    CHAR_LENGTH(`stores1`.`Name`), CONCAT_WS('',   `stores1`.`Name`), '') /* Store */" => "Store",
		"`ingredientstores`.`Cost`" => "Cost"
	);

	// Fields that can be quick searched
	$x->QueryFieldsQS = array(   
		"`ingredientstores`.`IngredientStoreID`" => "IngredientStoreID",
		"IF(    CHAR_LENGTH(`ingredients1`.`Name`) || CHAR_LENGTH(`ingredients1`.`PricingUnit`), CONCAT_WS('',   `ingredients1`.`Name`, ' - ', `ingredients1`.`PricingUnit`), '') /* Ingredient */" => "IngredientID",
		"IF(    CHAR_LENGTH(`stores1`.`Name`), CONCAT_WS('',   `stores1`.`Name`), '') /* Store */" => "StoreID",
		"CONCAT('$', FORMAT(`ingredientstores`.`Cost`, 2))" => "Cost"
	);

	// Lookup fields that can be used as filterers
	$x->filterers = array(  'IngredientID' => 'Ingredient', 'StoreID' => 'Store');

	$x->QueryFrom = "`ingredientstores` LEFT JOIN `ingredients` as ingredients1 ON `ingredients1`.`IngredientID`=`ingredientstores`.`IngredientID` LEFT JOIN `stores` as stores1 ON `stores1`.`StoreID`=`ingredientstores`.`StoreID` ";
	$x->QueryWhere = '';
	$x->QueryOrder = '';

	$x->AllowSelection = 1;
	$x->HideTableView = ($perm[2]==0 ? 1 : 0);
	$x->AllowDelete = $perm[4];
	$x->AllowMassDelete = false;
	$x->AllowInsert = $perm[1];
	$x->AllowUpdate = $perm[3];
	$x->SeparateDV = 0;
	$x->AllowDeleteOfParents = 0;
	$x->AllowFilters = 1;
	$x->AllowSavingFilters = 0;
	$x->AllowSorting = 1;
	$x->AllowNavigation = 1;
	$x->AllowPrinting = 1;
	$x->AllowCSV = 1;
	$x->RecordsPerPage = 10;
	$x->QuickSearch = 1;
	$x->QuickSearchText = $Translation["quick search"];
	$x->ScriptFileName = "ingredientstores_view.php";
	$x->RedirectAfterInsert = "ingredientstores_view.php?SelectedID=#ID#";
	$x->TableTitle = "Ingredients & Stores";
	$x->TableIcon = "resources/table_icons/cart.png";
	$x->PrimaryKey = "`ingredientstores`.`IngredientStoreID`";

	$x->ColWidth   = array(  150, 150, 150);
	$x->ColCaption = array("Ingredient", "Store", "Cost");
	$x->ColFieldName = array('IngredientID', 'StoreID', 'Cost');
	$x->ColNumber  = array(2, 3, 4);

	// template paths below are based on the app main directory
	$x->Template = 'templates/ingredientstores_templateTV.html';
	$x->SelectedTemplate = 'templates/ingredientstores_templateTVS.html';
	$x->TemplateDV = 'templates/ingredientstores_templateDV.html';
	$x->TemplateDVP = 'templates/ingredientstores_templateDVP.html';

	$x->ShowTableHeader = 1;
	$x->ShowRecordSlots = 0;
	$x->TVClasses = "";
	$x->DVClasses = "";
	$x->HighlightColor = '#FFF0C2';

	// mm: build the query based on current member's permissions
	$DisplayRecords = $_REQUEST['DisplayRecords'];
	if(!in_array($DisplayRecords, array('user', 'group'))){ $DisplayRecords = 'all'; }
	if($perm[2]==1 || ($perm[2]>1 && $DisplayRecords=='user' && !$_REQUEST['NoFilter_x'])){ // view owner only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `ingredientstores`.`IngredientStoreID`=membership_userrecords.pkValue and membership_userrecords.tableName='ingredientstores' and lcase(membership_userrecords.memberID)='".getLoggedMemberID()."'";
	}elseif($perm[2]==2 || ($perm[2]>2 && $DisplayRecords=='group' && !$_REQUEST['NoFilter_x'])){ // view group only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `ingredientstores`.`IngredientStoreID`=membership_userrecords.pkValue and membership_userrecords.tableName='ingredientstores' and membership_userrecords.groupID='".getLoggedGroupID()."'";
	}elseif($perm[2]==3){ // view all
		// no further action
	}elseif($perm[2]==0){ // view none
		$x->QueryFields = array("Not enough permissions" => "NEP");
		$x->QueryFrom = '`ingredientstores`';
		$x->QueryWhere = '';
		$x->DefaultSortField = '';
	}
	// hook: ingredientstores_init
	$render=TRUE;
	if(function_exists('ingredientstores_init')){
		$args=array();
		$render=ingredientstores_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// hook: ingredientstores_header
	$headerCode='';
	if(function_exists('ingredientstores_header')){
		$args=array();
		$headerCode=ingredientstores_header($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$headerCode){
		include_once("$currDir/header.php"); 
	}else{
		ob_start(); include_once("$currDir/header.php"); $dHeader=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%HEADER%%>', $dHeader, $headerCode);
	}

	echo $x->HTML;
	// hook: ingredientstores_footer
	$footerCode='';
	if(function_exists('ingredientstores_footer')){
		$args=array();
		$footerCode=ingredientstores_footer($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$footerCode){
		include_once("$currDir/footer.php"); 
	}else{
		ob_start(); include_once("$currDir/footer.php"); $dFooter=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%FOOTER%%>', $dFooter, $footerCode);
	}
?>