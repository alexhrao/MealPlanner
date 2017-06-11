<?php
	$currDir = dirname(__FILE__);
	require("{$currDir}/incCommon.php");
	include("{$currDir}/incHeader.php");

	// process search
	$memberID = makeSafe(strtolower($_GET['memberID']));
	$groupID = intval($_GET['groupID']);
	$tableName = makeSafe($_GET['tableName']);

	// process sort
	$sortDir = ($_GET['sortDir'] ? 'desc' : '');
	$sort = makeSafe($_GET['sort']);
	if($sort != 'dateAdded' && $sort != 'dateUpdated'){ // default sort is newly created first
		$sort = 'dateAdded';
		$sortDir = 'desc';
	}

	if($sort){
		$sortClause = "order by {$sort} {$sortDir}";
	}

	if($memberID != ''){
		$where .= ($where ? " and " : "") . "r.memberID like '{$memberID}%'";
	}

	if($groupID != ''){
		$where .= ($where ? " and " : "") . "g.groupID='{$groupID}'";
	}

	if($tableName != ''){
		$where .= ($where ? " and " : "") . "r.tableName='{$tableName}'";
	}

	if($where){
		$where = "where {$where}";
	}

	$numRecords=sqlValue("select count(1) from membership_userrecords r left join membership_groups g on r.groupID=g.groupID $where");
	if(!$numRecords){
		echo "<div class=\"status\">{$Translation['no matching results found']}</div>";
		$noResults=TRUE;
		$page=1;
	}else{
		$noResults=FALSE;
	}

	$page=intval($_GET['page']);
	if($page<1){
		$page=1;
	}elseif($page>ceil($numRecords/$adminConfig['recordsPerPage']) && !$noResults){
		redirect("admin/pageViewRecords.php?page=".ceil($numRecords/$adminConfig['recordsPerPage']));
	}

	$start=($page-1)*$adminConfig['recordsPerPage'];

?>
<div class="page-header"><h1><?php echo $Translation['data records'] ; ?></h1></div>

<table class="table table-striped">
	<tr>
		<td colspan="7" align="center">
			<form method="get" action="pageViewRecords.php">
				<table class="table">
					<tr>
						<td align="center">
							<?php 
								echo $Translation["group"] ; 
								echo htmlSQLSelect("groupID", "select groupID, name from membership_groups order by name", $groupID);
							?>
							&nbsp; &nbsp; &nbsp; 
							<?php echo $Translation["member username"] ; ?>
							<input class="formTextBox" type="text" name="memberID" value="<?php echo $memberID; ?>" size="20">
							<input type="hidden" name="page" value="1">
							</td>
						<td valign="bottom" rowspan="3">
							<input type="submit" value="<?php echo $Translation['find'] ; ?>">
							<input type="button" value="<?php echo $Translation['reset'] ; ?>" onClick="window.location='pageViewRecords.php';">
							</td>
						</tr>
					<tr>
						<td align="center">
							<?php echo $Translation['show records'] ; ?>
							<?php
								$arrFields = array('', 'mealdates', 'meals', 'recipes', 'ingredients', 'mealrecipes', 'recipeingredients', 'ingredientstores', 'sources', 'stores');
								$arrFieldCaptions = array('All tables', 'Date Planner', 'Meals', 'Recipes', 'Ingredients', 'Meals & Recipes', 'Recipes & Ingredients', 'Ingredients & Stores', 'Sources', 'Stores');
								echo htmlSelect('tableName', $arrFields, $arrFieldCaptions, $tableName);
							?>
							</td>
						</tr>
					<tr>
						<td align="center">
							<?php echo $Translation['sort records'] ; ?>
							<?php
								$arrFields=array('dateAdded', 'dateUpdated');
								$arrFieldCaptions=array( $Translation['date created'] , $Translation['date modified'] );
								echo htmlSelect('sort', $arrFields, $arrFieldCaptions, $sort);

								$arrFields=array('desc', '');
								$arrFieldCaptions=array( $Translation['newer first'] , $Translation['older first'] );
								echo htmlSelect('sortDir', $arrFields, $arrFieldCaptions, $sortDir);
							?>
							</td>
						</tr>
					</table>
				</form>
			</td>
		</tr>
	<tr>
		<td class="tdHeader">&nbsp;</td>
		<td class="tdHeader"><div class="ColCaption"><?php echo $Translation['username'] ; ?></div></td>
		<td class="tdHeader"><div class="ColCaption"><?php echo $Translation["group"] ; ?></div></td>
		<td class="tdHeader"><div class="ColCaption"><?php echo $Translation["table"] ; ?></div></td>
		<td class="tdHeader"><div class="ColCaption"><?php echo $Translation['created'] ; ?></div></td>
		<td class="tdHeader"><div class="ColCaption"><?php echo $Translation['modified'] ; ?></div></td>
		<td class="tdHeader"><div class="ColCaption"><?php echo $Translation['data'] ; ?></div></td>
		</tr>
<?php

	$res = sql("select r.recID, r.memberID, g.name, r.tableName, r.dateAdded, r.dateUpdated, r.pkValue from membership_userrecords r left join membership_groups g on r.groupID=g.groupID $where $sortClause limit $start, ".$adminConfig['recordsPerPage'], $eo);
	while($row = db_fetch_row($res)){
		?>
		<tr>
			<td class="tdCaptionCell">
				<a href="pageEditOwnership.php?recID=<?php echo $row[0]; ?>"><img border="0" src="images/edit_icon.gif" alt="<?php echo $Translation['change record ownership'] ; ?>" title="<?php echo $Translation['change record ownership'] ; ?>"></a>
				<a href="pageDeleteRecord.php?recID=<?php echo $row[0]; ?>" onClick="return confirm('<?php echo $Translation['sure delete record'] ; ?>');"><img border="0" src="images/delete_icon.gif" alt="<?php echo $Translation['delete record'] ; ?>" title="<?php echo $Translation['delete record'] ; ?>"></a>
			</td>
			<td class="tdCell"><?php echo $row[1]; ?></td>
			<td class="tdCell"><?php echo $row[2]; ?></td>
			<td class="tdCell"><?php echo $row[3]; ?></td>
			<td class="tdCell <?php echo ($sort == 'dateAdded' ? 'warning' : '');?>"><?php echo @date($adminConfig['PHPDateTimeFormat'], $row[4]); ?></td>
			<td class="tdCell <?php echo ($sort == 'dateUpdated' ? 'warning' : '');?>"><?php echo @date($adminConfig['PHPDateTimeFormat'], $row[5]); ?></td>
			<td class="tdCell"><?php echo substr(getCSVData($row[3], $row[6]), 0, 80)." ... "; ?></td>
		</tr>
		<?php
	}
	?>
	<tr>
		<td colspan="7" style="padding: 0;">
			<table width="100%" cellspacing="0">
				<tr>
				<td class="tdFooter text-left flip">
					<input type="button" onClick="window.location='pageViewRecords.php?groupID=<?php echo $groupID; ?>&memberID=<?php echo $memberID; ?>&tableName=<?php echo $tableName; ?>&page=<?php echo ($page>1 ? $page-1 : 1); ?>&sort=<?php echo $sort; ?>&sortDir=<?php echo $sortDir; ?>';" value="<?php echo $Translation['previous'] ; ?>">
				</td>
				<td class="text-center tdFooter">
					<?php 
						$originalValues =  array ('<RECORDNUM1>','<RECORDNUM2>','<RECORDS>' );
						$replaceValues = array ( $start+1 , $start+db_num_rows($res) , $numRecords );
						echo str_replace ( $originalValues , $replaceValues , $Translation['displaying records'] );
					?>
				</td>
				<td class="tdFooter text-right flip">
					<input type="button" onClick="window.location='pageViewRecords.php?groupID=<?php echo $groupID; ?>&memberID=<?php echo $memberID; ?>&tableName=<?php echo $tableName; ?>&page=<?php echo ($page<ceil($numRecords/$adminConfig['recordsPerPage']) ? $page+1 : ceil($numRecords/$adminConfig['recordsPerPage'])); ?>&sort=<?php echo $sort; ?>&sortDir=<?php echo $sortDir; ?>';" value="<?php echo $Translation['next'] ; ?>">
				</td>
			</tr><table></td>
		</tr>
	</table>

<?php
	include("{$currDir}/incFooter.php");
?>