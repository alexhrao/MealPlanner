<?php
	$currDir = dirname(__FILE__);
	require("{$currDir}/incCommon.php");
?>
<!doctype html public "-//W3C//DTD html 4.0 //en">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="adminStyles.css">
		<title><?php echo $Translation["record details"]; ?></title>
		</head>
	<body>
		<div align="center">

<?php
	$recID=makeSafe($_GET['recID']);
	if($recID!=''){
		// fetch record data to fill in the form below
		$res=sql("select * from membership_userrecords where recID='$recID'", $eo);
		if($row=db_fetch_assoc($res)){
			// get record data
			$tableName=$row['tableName'];
			$pkValue=$row['pkValue'];
			$memberID=strtolower($row['memberID']);
			$dateAdded=@date($adminConfig['PHPDateTimeFormat'], $row['dateAdded']);
			$dateUpdated=@date($adminConfig['PHPDateTimeFormat'], $row['dateUpdated']);
			$groupID=$row['groupID'];
		}else{
			// no such record exists
			die("<div class=\"status\">{$Translation["record not found error"]}</div>");
		}
	}


	// get pk field name
	$pkField=getPKFieldName($tableName);

	// get field list
	if(!$res=sql("show fields from `$tableName`", $eo)){
		errorMsg(str_replace ( "<TABLENAME>" , $tableName , $Translation["could not retrieve field list"] ));
	}
	while($row=db_fetch_assoc($res)){
		$field[]=$row['Field'];
	}

	$res=sql("select * from `$tableName` where `$pkField`='" . makeSafe($pkValue, false) . "'", $eo);
	if($row=db_fetch_assoc($res)){
		?>
		<h2><?php echo str_replace ( "<TABLENAME>" , $tableName , $Translation["table name"] ); ?></h2>
		<table class="table table-striped">
			<tr>
				<td class="tdHeader"><div class="ColCaption"><?php echo $Translation["field name"]; ?></div></td>
				<td class="tdHeader"><div class="ColCaption"><?php echo $Translation["value"]; ?></div></td>
				</tr>
		<?php
		include("{$currDir}/../language.php");
		foreach($field as $fn){
			if(@is_file("{$currDir}/../".$Translation['ImageFolder'].$row[$fn])){
				$op="<a href=\""."../".$Translation['ImageFolder'].$row[$fn]."\" target=\"_blank\">".htmlspecialchars($row[$fn])."</a>";
			}else{
				$op=htmlspecialchars($row[$fn]);
			}
			?>
			<tr>
				<td class="tdCaptionCell" valign="top"><?php echo $fn; ?></td>
				<td class="tdCell" valign="top">
					<?php echo $op; ?>
					</td>
				</tr>
			<?php
		}
		?>
			</table>
		<?php
	}


	include("{$currDir}/incFooter.php");
?>
