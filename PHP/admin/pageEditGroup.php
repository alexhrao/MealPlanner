<?php
	$currDir = dirname(__FILE__);
	require("{$currDir}/incCommon.php");

	// get groupID of anonymous group
	$anon_safe = makeSafe($adminConfig['anonymousGroup'], false);
	$anonGroupID = sqlValue("select groupID from membership_groups where name='{$anon_safe}'");

	// get list of tables
	$table_list = getTableList();
	$perm = array();

	// request to save changes?
	if($_POST['saveChanges'] != ''){
		// validate data
		$name = makeSafe($_POST['name']);
		$description = makeSafe($_POST['description']);
		switch($_POST['visitorSignup']){
			case 0:
				$allowSignup = 0;
				$needsApproval = 1;
				break;
			case 2:
				$allowSignup = 1;
				$needsApproval = 0;
				break;
			default:
				$allowSignup = 1;
				$needsApproval = 1;
		}

		foreach($table_list as $tn => $tc){
			$perm["{$tn}_insert"] = checkPermissionVal("{$tn}_insert");
			$perm["{$tn}_view"] = checkPermissionVal("{$tn}_view");
			$perm["{$tn}_edit"] = checkPermissionVal("{$tn}_edit");
			$perm["{$tn}_delete"] = checkPermissionVal("{$tn}_delete");
		}

		// new group or old?
		if($_POST['groupID'] == ''){ // new group
			// make sure group name is unique
			if(sqlValue("select count(1) from membership_groups where name='{$name}'")){
				echo "<div class=\"alert alert-danger\">{$Translation["group exists error"]}</div>";
				include("{$currDir}/incFooter.php");
			}

			// add group
			sql("insert into membership_groups set name='{$name}', description='{$description}', allowSignup='{$allowSignup}', needsApproval='{$needsApproval}'", $eo);

			// get new groupID
			$groupID = db_insert_id(db_link());

		}else{ // old group
			// validate groupID
			$groupID = intval($_POST['groupID']);

			/* force configured name and no signup for anonymous group */
			if($groupID == $anonGroupID){
				$name = $adminConfig['anonymousGroup'];
				$allowSignup = 0;
				$needsApproval = 0;
			}

			// make sure group name is unique
			if(sqlValue("select count(1) from membership_groups where name='{$name}' and groupID!='{$groupID}'")){
				echo "<div class=\"alert alert-danger\">{$Translation["group exists error"]}</div>";
				include("{$currDir}/incFooter.php");
			}

			// update group
			sql("update membership_groups set name='{$name}', description='{$description}', allowSignup='{$allowSignup}', needsApproval='{$needsApproval}' where groupID='{$groupID}'", $eo);

			// reset then add group permissions
			foreach($table_list as $tn => $tc){
				sql("delete from membership_grouppermissions where groupID='{$groupID}' and tableName='{$tn}'", $eo);
			}
		}

		// add group permissions
		if($groupID){
			foreach($table_list as $tn => $tc){
				$allowInsert = $perm["{$tn}_insert"];
				$allowView = $perm["{$tn}_view"];
				$allowEdit = $perm["{$tn}_edit"];
				$allowDelete = $perm["{$tn}_delete"];
				sql("insert into membership_grouppermissions set groupID='{$groupID}', tableName='{$tn}', allowInsert='{$allowInsert}', allowView='{$allowView}', allowEdit='{$allowEdit}', allowDelete='{$allowDelete}'", $eo);
			}
		}

		// redirect to group editing page
		redirect("admin/pageEditGroup.php?groupID={$groupID}");

	}elseif($_GET['groupID'] != ''){
		// we have an edit request for a group
		$groupID = intval($_GET['groupID']);
	}

	include("{$currDir}/incHeader.php");

	if($groupID != ''){
		// fetch group data to fill in the form below
		$res = sql("select * from membership_groups where groupID='{$groupID}'", $eo);
		if($row = db_fetch_assoc($res)){
			// get group data
			$name = $row['name'];
			$description = $row['description'];
			$visitorSignup = ($row['allowSignup'] == 1 && $row['needsApproval'] == 1 ? 1 : ($row['allowSignup'] == 1 ? 2 : 0));

			// get group permissions for each table
			$res = sql("select * from membership_grouppermissions where groupID='{$groupID}'", $eo);
			while($row = db_fetch_assoc($res)){
				$tn = $row['tableName'];
				$perm["{$tn}_insert"] = $row['allowInsert'];
				$perm["{$tn}_view"] = $row['allowView'];
				$perm["{$tn}_edit"] = $row['allowEdit'];
				$perm["{$tn}_delete"] = $row['allowDelete'];
			}
		}else{
			// no such group exists
			echo "<div class=\"alert alert-danger\">{$Translation["group not found error"]}</div>";
			$groupID=0;
		}
	}
?>
<div class="page-header"><h1><?php echo ($groupID ? str_replace ('<GROUPNAME>' , $name, $Translation["edit group"] ) : $Translation["add new group"]); ?></h1></div>
<?php if($anonGroupID==$groupID){ ?>
	<div class="alert alert-warning"><?php echo $Translation["anonymous group attention"]; ?></div>
<?php } ?>
<input type="checkbox" id="showToolTips" value="1" checked><label for="showToolTips"><?php echo $Translation["show tool tips"]; ?></label>
<form method="post" action="pageEditGroup.php">
	<input type="hidden" name="groupID" value="<?php echo $groupID; ?>">
	<div class="table-responsive"><table class="table table-striped">
		<tr>
			<td class="tdFormCaption text-right flip" valign="top">
				<div class="formFieldCaption"><?php echo $Translation["group name"]; ?></div>
				</td>
			<td class="tdFormInput text-left flip">
				<input type="text" name="name" <?php echo ($anonGroupID == $groupID ? "readonly" : ""); ?> value="<?php echo htmlspecialchars($name); ?>" size="20" class="formTextBox">
				<br>
				<?php if($anonGroupID == $groupID){ 
							echo $Translation["readonly group name"]; 
					  }else{ 
							echo str_replace ( '<ANONYMOUSGROUP>' ,  $adminConfig['anonymousGroup'] , $Translation["anonymous group name"] );
					  }
				?>
			</td>
		</tr>
		<tr>
			<td valign="top" class="tdFormCaption text-right flip">
				<div class="formFieldCaption"><?php echo $Translation["description"] ; ?></div>
			</td>
			<td class="tdFormInput text-left flip">
				<textarea name="description" cols="50" rows="5" class="formTextBox"><?php echo htmlspecialchars($description); ?></textarea>
			</td>
		</tr>
		<?php if($anonGroupID != $groupID){ ?>
		<tr>
			<td valign="top" class="tdFormCaption text-right flip">
				<div class="formFieldCaption"><?php echo $Translation["allow visitors sign up"] ; ?></div>
			</td>
			<td class="tdFormInput text-left flip">
				<?php
					echo htmlRadioGroup(
						"visitorSignup",
						array(0, 1, 2),
						array(
							$Translation["admin add users"],
							$Translation["admin approve users"],
							$Translation["automatically approve users"]
						),
						($groupID ? $visitorSignup : $adminConfig['defaultSignUp'])
					);
				?>
			</td>
		</tr>
		<?php } ?>
		<tr>
			<td colspan="2" class="tdFormFooter text-right flip">
				<input type="submit" name="saveChanges" value="<?php echo $Translation["save changes"] ; ?>" >
			</td>
		</tr>
		<tr>
			<td colspan="2" class="tdFormHeader">
				<table class="table table-striped">
					<tr>
						<td class="tdFormHeader" colspan="5"><h2><?php echo $Translation["group table permissions"] ; ?></h2></td>
					</tr>
					<?php
						// permissions arrays common to the radio groups below
						$arrPermVal=array(0, 1, 2, 3);
						$arrPermText=array($Translation["no"], $Translation["owner"], $Translation["group"] , $Translation["all"] );
					?>
					<tr>
						<td class="tdHeader"><div class="ColCaption"><?php echo $Translation["table"] ; ?></div></td>
						<td class="tdHeader"><div class="ColCaption"><?php echo $Translation["insert"] ; ?></div></td>
						<td class="tdHeader"><div class="ColCaption"><?php echo $Translation["view"] ; ?></div></td>
						<td class="tdHeader"><div class="ColCaption"><?php echo $Translation["edit"] ; ?></div></td>
						<td class="tdHeader"><div class="ColCaption"><?php echo $Translation["delete"] ; ?></div></td>
					</tr>
					<?php foreach($table_list as $tn => $tc){ ?>
					<!-- <?php echo $tn; ?> table -->
						<tr>
							<td class="tdCaptionCell" valign="top"><?php echo $tc; ?></td>
							<td class="tdCell" valign="top">
								<input onMouseOver="stm(<?php echo $tn; ?>_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="<?php echo $tn; ?>_insert" value="1" <?php echo ($perm["{$tn}_insert"] ? "checked class=\"highlight\"" : ""); ?>>
							</td>
							<td class="tdCell">
								<?php
									echo htmlRadioGroup("{$tn}_view", $arrPermVal, $arrPermText, $perm["{$tn}_view"], "highlight");
								?>
							</td>
							<td class="tdCell">
								<?php
									echo htmlRadioGroup("{$tn}_edit", $arrPermVal, $arrPermText, $perm["{$tn}_edit"], "highlight");
								?>
							</td>
							<td class="tdCell">
								<?php
									echo htmlRadioGroup("{$tn}_delete", $arrPermVal, $arrPermText, $perm["{$tn}_delete"], "highlight");
								?>
							</td>
						</tr>
					<?php } ?>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="tdFormFooter text-right flip">
				<input type="submit" name="saveChanges" value="<?php echo $Translation["save changes"] ; ?>">
			</td>
		</tr>
	</table></div>
</form>

<script>
	$j(function(){
		var highlight_selections = function(){
			$j('input[type=radio]:checked').next().addClass('text-primary');
			$j('input[type=radio]:not(:checked)').next().removeClass('text-primary');
		}

		$j('input[type=radio]').change(function(){ highlight_selections(); });
		highlight_selections();
	});
</script>


<?php
	include("{$currDir}/incFooter.php");
?>