<?php
// This script and data application were generated by AppGini 5.50
// Download AppGini for free from http://bigprof.com/appgini/download/

	$currDir=dirname(__FILE__);
	include("$currDir/defaultLang.php");
	include("$currDir/language.php");
	include("$currDir/lib.php");
	@include("$currDir/hooks/mealdates.php");
	include("$currDir/mealdates_dml.php");

	// mm: can the current member access this page?
	$perm=getTablePermissions('mealdates');
	if(!$perm[0]){
		echo error_message($Translation['tableAccessDenied'], false);
		echo '<script>setTimeout("window.location=\'index.php?signOut=1\'", 2000);</script>';
		exit;
	}

	$x = new DataList;
	$x->TableName = "mealdates";

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV=array(   
		"`mealdates`.`MealDateID`" => "MealDateID",
		"IF(    CHAR_LENGTH(`meals1`.`Name`), CONCAT_WS('',   `meals1`.`Name`), '') /* Meal */" => "MealID",
		"if(`mealdates`.`MealDate`,date_format(`mealdates`.`MealDate`,'%m/%d/%Y'),'')" => "MealDate",
		"`mealdates`.`MealTime`" => "MealTime"
	);
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = array(   
		1 => '`mealdates`.`MealDateID`',
		2 => 2,
		3 => '`mealdates`.`MealDate`',
		4 => 4
	);

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV=array(   
		"`mealdates`.`MealDateID`" => "MealDateID",
		"IF(    CHAR_LENGTH(`meals1`.`Name`), CONCAT_WS('',   `meals1`.`Name`), '') /* Meal */" => "MealID",
		"if(`mealdates`.`MealDate`,date_format(`mealdates`.`MealDate`,'%m/%d/%Y'),'')" => "MealDate",
		"`mealdates`.`MealTime`" => "MealTime"
	);
	// Fields that can be filtered
	$x->QueryFieldsFilters=array(   
		"`mealdates`.`MealDateID`" => "MealDateID",
		"IF(    CHAR_LENGTH(`meals1`.`Name`), CONCAT_WS('',   `meals1`.`Name`), '') /* Meal */" => "Meal",
		"`mealdates`.`MealDate`" => "Date",
		"`mealdates`.`MealTime`" => "Time"
	);

	// Fields that can be quick searched
	$x->QueryFieldsQS=array(   
		"`mealdates`.`MealDateID`" => "MealDateID",
		"IF(    CHAR_LENGTH(`meals1`.`Name`), CONCAT_WS('',   `meals1`.`Name`), '') /* Meal */" => "MealID",
		"if(`mealdates`.`MealDate`,date_format(`mealdates`.`MealDate`,'%m/%d/%Y'),'')" => "MealDate",
		"`mealdates`.`MealTime`" => "MealTime"
	);

	// Lookup fields that can be used as filterers
	$x->filterers = array(  'MealID' => 'Meal');

	$x->QueryFrom="`mealdates` LEFT JOIN `meals` as meals1 ON `meals1`.`MealID`=`mealdates`.`MealID` ";
	$x->QueryWhere='';
	$x->QueryOrder='';

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
	$x->ScriptFileName = "mealdates_view.php";
	$x->RedirectAfterInsert = "mealdates_view.php?SelectedID=#ID#";
	$x->TableTitle = "Date Planner";
	$x->TableIcon = "resources/table_icons/application_form_magnify.png";
	$x->PrimaryKey = "`mealdates`.`MealDateID`";

	$x->ColWidth   = array(  150, 150, 150);
	$x->ColCaption = array("Meal", "Date", "Time");
	$x->ColFieldName = array('MealID', 'MealDate', 'MealTime');
	$x->ColNumber  = array(2, 3, 4);

	$x->Template = 'templates/mealdates_templateTV.html';
	$x->SelectedTemplate = 'templates/mealdates_templateTVS.html';
	$x->ShowTableHeader = 1;
	$x->ShowRecordSlots = 0;
	$x->HighlightColor = '#FFF0C2';

	// mm: build the query based on current member's permissions
	$DisplayRecords = $_REQUEST['DisplayRecords'];
	if(!in_array($DisplayRecords, array('user', 'group'))){ $DisplayRecords = 'all'; }
	if($perm[2]==1 || ($perm[2]>1 && $DisplayRecords=='user' && !$_REQUEST['NoFilter_x'])){ // view owner only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `mealdates`.`MealDateID`=membership_userrecords.pkValue and membership_userrecords.tableName='mealdates' and lcase(membership_userrecords.memberID)='".getLoggedMemberID()."'";
	}elseif($perm[2]==2 || ($perm[2]>2 && $DisplayRecords=='group' && !$_REQUEST['NoFilter_x'])){ // view group only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `mealdates`.`MealDateID`=membership_userrecords.pkValue and membership_userrecords.tableName='mealdates' and membership_userrecords.groupID='".getLoggedGroupID()."'";
	}elseif($perm[2]==3){ // view all
		// no further action
	}elseif($perm[2]==0){ // view none
		$x->QueryFields = array("Not enough permissions" => "NEP");
		$x->QueryFrom = '`mealdates`';
		$x->QueryWhere = '';
		$x->DefaultSortField = '';
	}
	// hook: mealdates_init
	$render=TRUE;
	if(function_exists('mealdates_init')){
		$args=array();
		$render=mealdates_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// hook: mealdates_header
	$headerCode='';
	if(function_exists('mealdates_header')){
		$args=array();
		$headerCode=mealdates_header($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$headerCode){
		include_once("$currDir/header.php"); 
	}else{
		ob_start(); include_once("$currDir/header.php"); $dHeader=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%HEADER%%>', $dHeader, $headerCode);
	}

	echo $x->HTML;
	// hook: mealdates_footer
	$footerCode='';
	if(function_exists('mealdates_footer')){
		$args=array();
		$footerCode=mealdates_footer($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$footerCode){
		include_once("$currDir/footer.php"); 
	}else{
		ob_start(); include_once("$currDir/footer.php"); $dFooter=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%FOOTER%%>', $dFooter, $footerCode);
	}
?>