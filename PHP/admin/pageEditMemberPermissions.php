<?php
	$currDir = dirname(__FILE__);
	require("{$currDir}/incCommon.php");

	// tables list
		$tables=getTableList();

	// ensure that a memberID is provided
		if($_GET['memberID']!=''){
			$memberID=makeSafe(strtolower($_GET['memberID']));
		}elseif($_POST['memberID']!=''){
			$memberID=makeSafe(strtolower($_POST['memberID']));
		}else{
			// error in request. redirect to members page.
			redirect('admin/pageViewMembers.php');
		}

	// validate memberID exists and is not guest and is not admin
		$anonymousMember=strtolower($adminConfig['anonymousMember']);
		$anonymousGroup=$adminConfig['anonymousGroup'];
		$anonGroupID=sqlValue("select groupID from membership_groups where lcase(name)='".strtolower(makeSafe($anonymousGroup))."'");
		$adminGroupID=sqlValue("select groupID from membership_groups where name='Admins'");
		$groupID=sqlValue("select groupID from membership_users where lcase(memberID)='$memberID'");
		$group=sqlValue("select name from membership_groups where groupID='$groupID'");
		if($groupID==$anonGroupID || $memberID==$anonymousMember || !$groupID || $groupID==$adminGroupID || $memberID==$adminConfig['adminUsername']){
			// error in request. redirect to members page.
			redirect('admin/pageViewMembers.php');
		}

	// request to save changes?
	if($_POST['saveChanges']!=''){
		// validate data
		foreach($tables as $t=>$tc){
			eval(" 
				\${$t}_insert=checkPermissionVal('{$t}_insert');
				\${$t}_view=checkPermissionVal('{$t}_view');
				\${$t}_edit=checkPermissionVal('{$t}_edit');
				\${$t}_delete=checkPermissionVal('{$t}_delete');
			");
		}

		// reset then add member permissions
		sql("delete from membership_userpermissions where lcase(memberID)='$memberID'", $eo);

		// add new member permissions
		$query="insert into membership_userpermissions (memberID, tableName, allowInsert, allowView, allowEdit, allowDelete) values ";
		foreach($tables as $t=>$tc){
			$insert="{$t}_insert";
			$view="{$t}_view";
			$edit="{$t}_edit";
			$delete="{$t}_delete";
			$query.="('$memberID', '$t', '${$insert}', '${$view}', '${$edit}', '${$delete}'),";
		}
		$query=substr($query, 0, -1);
		sql($query, $eo);

		// redirect to member permissions page
		redirect("admin/pageEditMemberPermissions.php?saved=1&memberID=".urlencode($memberID));
	}elseif($_POST['resetPermissions']!=''){
		sql("delete from membership_userpermissions where lcase(memberID)='$memberID'", $eo);
		// redirect to member permissions page
		redirect("admin/pageEditMemberPermissions.php?reset=1&memberID=".urlencode($memberID));
	}elseif($_GET['memberID']!=''){
		// we have an edit request for a group
	}

	include("{$currDir}/incHeader.php");

	// fetch group permissions to fill in the form below in case user has no special permissions
		$res1=sql("select * from membership_grouppermissions where groupID='$groupID'", $eo);
		while($row=db_fetch_assoc($res1)){
			$tableName=$row['tableName'];
			$vIns=$tableName."_insert";
			$vUpd=$tableName."_edit";
			$vDel=$tableName."_delete";
			$vVue=$tableName."_view";
			$$vIns=$row['allowInsert'];
			$$vUpd=$row['allowEdit'];
			$$vDel=$row['allowDelete'];
			$$vVue=$row['allowView'];
		}

	// fetch user permissions to fill in the form below, overwriting his group permissions
		$res2=sql("select * from membership_userpermissions where lcase(memberID)='$memberID'", $eo);
		while($row=db_fetch_assoc($res2)){
			$tableName=$row['tableName'];
			$vIns=$tableName."_insert";
			$vUpd=$tableName."_edit";
			$vDel=$tableName."_delete";
			$vVue=$tableName."_view";
			$$vIns=$row['allowInsert'];
			$$vUpd=$row['allowEdit'];
			$$vDel=$row['allowDelete'];
			$$vVue=$row['allowView'];
		}
?>
<!-- show notifications -->
<?php
	if($_GET['saved']){
		?>
		<div id="savedNotification" class="status"><?php echo  $Translation["member permissions saved"]  ; ?></div>
		<script> setTimeout("document.getElementById('savedNotification').style.display='none';", 5000); </script>
		<?php
	}elseif($_GET['reset']){
		?>
		<div id="resetNotification" class="status"><?php echo  $Translation["member permissions reset"] ; ?></div>
		<script> setTimeout("document.getElementById('resetNotification').style.display='none';", 5000); </script>
		<?php
	}
?>
<!-- done showing notifications -->
<input style="display: none;" type="checkbox" id="showToolTips" value="1">
<form method="post" action="pageEditMemberPermissions.php">
	<input type="hidden" name="memberID" value="<?php echo $memberID; ?>">
	<div class="table-responsive"><table class="table table-striped">
		<tr>
			<td class="tdFormHeader" colspan="5"><div class="page-header"><h1><?php echo str_replace (array( '<MEMBER>' , '<MEMBERID>'  , '<GROUPID>' , '<GROUP>' ) , array(urlencode($memberID) , $memberID , $groupID , $group) , $Translation["user table permissions"] ); ?></h1></div></td>
			</tr>
<?php
	if(!db_num_rows($res2)){
		?>
		<tr>
			<td class="tdFormHeader" colspan="5" align="center"><div class="alert alert-info"><?php echo  $Translation["no member permissions"] ; ?></div></td>
			</tr>
		<?php
	}else{
		?>
		<tr>
			<td colspan="5" align="center" class="tdFormFooter">
				<input type="submit" name="resetPermissions" value="<?php echo  $Translation['reset member permissions'] ; ?>" onclick="return confirm('<?php echo  $Translation["remove special permissions"] ; ?>')" />
				</td>
			</tr>
		<?php
	}

			// permissions arrays common to the radio groups below
			$arrPermVal=array(0, 1, 2, 3);
			$arrPermText=array( $Translation["no"] , $Translation["owner"] , $Translation["group"] ,  $Translation["all"] );
		?>
		<tr>
			<td class="tdHeader"><div class="ColCaption"><?php echo $Translation["table"] ; ?></div></td>
			<td class="tdHeader"><div class="ColCaption"><?php echo $Translation["insert"] ; ?></div></td>
			<td class="tdHeader"><div class="ColCaption"><?php echo $Translation["view"] ; ?></div></td>
			<td class="tdHeader"><div class="ColCaption"><?php echo $Translation["edit"] ; ?></div></td>
			<td class="tdHeader"><div class="ColCaption"><?php echo $Translation["delete"] ; ?></div></td>
			</tr>
<?php
	foreach($tables as $t=>$tc){
		$insert="{$t}_insert";
		$view="{$t}_view";
		$edit="{$t}_edit";
		$delete="{$t}_delete";
		?>
		<!-- <?php echo $tc; ?> table -->
		<tr>
			<td class="tdCaptionCell" valign="top"><?php echo $tc; ?></td>
			<td class="tdCell" valign="top">
				<input onMouseOver="stm(<?php echo $t; ?>_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="<?php echo $t; ?>_insert" value="1" <?php echo ($$insert ? "checked class=\"highlight\"" : ""); ?>>
				</td>
			<td class="tdCell">
				<?php echo htmlRadioGroup("{$t}_view", $arrPermVal, $arrPermText, $$view, "highlight");    ?>
				</td>
			<td class="tdCell">
				<?php echo htmlRadioGroup("{$t}_edit", $arrPermVal, $arrPermText, $$edit, "highlight"); ?>
				</td>
			<td class="tdCell">
				<?php echo htmlRadioGroup("{$t}_delete", $arrPermVal, $arrPermText, $$delete, "highlight"); ?>
				</td>
			</tr>
		<?php
	}
?>
		<tr>
			<td colspan="5" align="right" class="tdFormFooter">
				<input type="submit" name="saveChanges" value="<?php echo  $Translation["save changes"] ; ?>">
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
