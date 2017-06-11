<?php
	$currDir = dirname(__FILE__);
	require("{$currDir}/incCommon.php");

	// get memberID of anonymous member
	$anonMemberID = strtolower($adminConfig['anonymousMember']);

	$memberID = '';
	// request to save changes?
	if($_POST['saveChanges'] != ''){
		// validate data
		$oldMemberID = makeSafe(strtolower($_POST['oldMemberID']));
		$password = makeSafe($_POST['password']);
		$email = isEmail($_POST['email']);
		$groupID = intval($_POST['groupID']);
		$isApproved = ($_POST['isApproved'] == 1 ? 1 : 0);
		$isBanned = ($_POST['isBanned'] == 1 ? 1 : 0);
		$custom1 = makeSafe($_POST['custom1']);
		$custom2 = makeSafe($_POST['custom2']);
		$custom3 = makeSafe($_POST['custom3']);
		$custom4 = makeSafe($_POST['custom4']);
		$comments = makeSafe($_POST['comments']);
		###############################

		// new member or old?
		if(!$oldMemberID){ // new member
			// make sure member name is unique
			$memberID = is_allowed_username($_POST['memberID']);
			if(!$memberID){
				echo "<div class=\"alert alert-danger\">{$Translation['username error']}</div>";
				include("{$currDir}/incFooter.php");
			}

			// add member
			sql("INSERT INTO `membership_users` set memberID='$memberID', passMD5='".md5($password)."', email='$email', signupDate='".@date('Y-m-d')."', groupID='$groupID', isBanned='$isBanned', isApproved='$isApproved', custom1='$custom1', custom2='$custom2', custom3='$custom3', custom4='$custom4', comments='$comments'", $eo);

			if($isApproved){
				notifyMemberApproval($memberID);
			}

			// redirect to member editing page
			redirect("admin/pageEditMember.php?memberID=$memberID&new_member=1");

		}else{ // old member

			// make sure new member username, if applicable, is valid
			$memberID = makeSafe(strtolower($_POST['memberID']));
			if($oldMemberID != $memberID) $memberID = is_allowed_username($_POST['memberID']);

			if(!$memberID){
				echo "<div class=\"alert alert-danger\">{$Translation['username error']}</div>";
				include("{$currDir}/incFooter.php");
			}

			// anonymousMember?
			if($anonMemberID == $memberID){
				$password = '';
				$email = '';
				$groupID = sqlValue("select groupID from membership_groups where name='".$adminConfig['anonymousGroup']."'");
				$isApproved = 1;
			}

			// get current approval state
			$oldIsApproved = sqlValue("select isApproved from membership_users where lcase(memberID)='$memberID'");

			// update member
			$upQry = "UPDATE `membership_users` set memberID='$memberID', passMD5=".($password!='' ? "'".md5($password)."'" : "passMD5").", email='$email', groupID='$groupID', isBanned='$isBanned', isApproved='$isApproved', custom1='$custom1', custom2='$custom2', custom3='$custom3', custom4='$custom4', comments='$comments' WHERE lcase(memberID)='$oldMemberID'";
			sql($upQry, $eo);

			// if memberID was changed, update membership_userrecords
			if($oldMemberID != $memberID){
				sql("update membership_userrecords set memberID='$memberID' where lcase(memberID)='$oldMemberID'", $eo);
			}

			// is member was approved, notify him
			if($isApproved && !$oldIsApproved){
				notifyMemberApproval($memberID);
			}

			// redirect to member editing page
			redirect("admin/pageEditMember.php?memberID=$memberID");
		}


	}elseif($_GET['memberID']!=''){
		// we have an edit request for a member
		$memberID=makeSafe(strtolower($_GET['memberID']));

		// display dismissible alert
		if (isset ($_GET['new_member']) && $_GET['new_member'] == 1 ){
			$displayCreatedAlert = true;
		}
	}elseif($_GET['groupID']!=''){
		// show the form for adding a new member, and pre-select the provided group
		$groupID=intval($_GET['groupID']);
		$group_name = sqlValue("select name from membership_groups where groupID='$groupID'");
		if($group_name) $addend = " to '{$group_name}'";
	}

	include("{$currDir}/incHeader.php");

	if($memberID!=''){
		// fetch group data to fill in the form below
		$res=sql("select * from membership_users where lcase(memberID)='$memberID'", $eo);
		if($row=db_fetch_assoc($res)){
			// get member data
			$email=$row['email'];
			$groupID=$row['groupID'];
			$isApproved=$row['isApproved'];
			$isBanned=$row['isBanned'];
			$custom1=htmlspecialchars($row['custom1']);
			$custom2=htmlspecialchars($row['custom2']);
			$custom3=htmlspecialchars($row['custom3']);
			$custom4=htmlspecialchars($row['custom4']);
			$comments=htmlspecialchars($row['comments']);


			//display dismissible alert if it is a new member
			if ( $displayCreatedAlert ){ 
				$id = 'notification-' . rand(); ?>

				<div id="<?php echo $id ; ?>" class="alert alert-success" style="display: none; padding-top: 6px; padding-bottom: 6px;">
					<?php echo str_replace ( '<USERNAME>' , $memberID , $Translation['member added']); ?>
				</div>
				<script>
					jQuery(function(){
							jQuery("#<?php echo $id; ?>").show("slow", function(){
								setTimeout(function(){ jQuery("#<?php echo $id; ?>").hide("slow"); }, 4000);
							});
					});
				</script>
	<?php   } 

		}else{
			// no such member exists
			echo "<div class=\"alert alert-danger\">{$Translation['member not found']}</div>";
			$memberID='';
		}
	}

	if($memberID!='' && $memberID!=$anonMemberID && $groupID!=sqlValue("select groupID from membership_groups where name='Admins'")){
		if(sqlValue("select count(1) from membership_userpermissions where memberID='$memberID'")>0){
			$userPermissionsNote="<br><i>".$Translation["user permissions note"]."</i><br>";
		}else{
			$userPermissionsNote='<br><i>'.str_replace ('<GROUPID>' , $groupID , $Translation["user has group permissions"] ).'</i><br>';
		}
		$userPermissionsNote.='<input type="button" class="" value="'.$Translation["set user special permissions"].'" onClick="if(confirm(\''.$Translation["sure continue"].'\')){ window.location=\'pageEditMemberPermissions.php?memberID='.urlencode($memberID).'\'; }">';
	}else{
		$userPermissionsNote='';
	}
?>
<div class="page-header row">
	<h1><?php echo ($memberID ? str_replace ('<MEMBERID>' , $memberID , $Translation["edit member"] ) : $Translation["add new member"].$addend); ?>
		<a id="orders_link" class="btn btn-default btn-lg pull-right hspacer-sm col-xs-12 col-sm-3 col-lg-2" href="pageViewMembers.php">
				<?php echo $Translation["back to members"] ; ?>
		</a>
	</h1>
</div>




<?php if($anonMemberID==$memberID){ ?>
	<div class="alert alert-warning"><?php echo $Translation["anonymous guest member"] ; ?></div>
<?php }elseif($memberID==strtolower($adminConfig['adminUsername'])){ ?>
	<div class="alert alert-warning"><?php echo $Translation["admin member"] ; ?></div>
<?php } ?>
<form method="post" action="pageEditMember.php" onSubmit="return jsValidateMember();" autocomplete="off">
	<input type="hidden" name="oldMemberID" value="<?php echo ($memberID ? $memberID : ""); ?>">
	<div class="table-responsive"><table class="table table-striped">
	<?php if($memberID!=strtolower($adminConfig['adminUsername'])){ ?>
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation["member username"] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="memberID" <?php echo ($anonMemberID==$memberID ? "readonly" : "");?> id="memberID" value="<?php echo $memberID; ?>" size="20" class="formTextBox">
				<?php echo ($memberID ? "" : "<input type=\"button\" value=\"{$Translation["check availability"]}\" onClick=\"window.open('../checkMemberID.php?memberID='+document.getElementById('memberID').value, 'checkMember', 'innerHeight=100,innerWidth=400,dependent=yes,screenX=200,screenY=200,status=no');\">"); ?>
				<?php if($anonMemberID==$memberID){ ?>
				<br><?php echo $Translation["read only username"] ; ?>
				<?php } ?>
				</td>
			</tr>
		<?php if($anonMemberID!=$memberID){ ?>
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation["password"] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="password" name="password" id="password" value="" size="20" class="formTextBox" autocomplete="off">
				<?php echo ($memberID ? "<br>".$Translation["change password"] : ""); ?>
				</td>
			</tr>
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation["confirm password"]; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="password" name="confirmPassword" id="confirmPassword" value="" size="20" class="formTextBox" autocomplete="off">
				</td>
			</tr>
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation["email"] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="email" value="<?php echo $email; ?>" size="40" class="formTextBox">
				</td>
			</tr>
		<?php } ?>
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation["group"] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<?php 
					if($anonMemberID!=$memberID){
						echo htmlSQLSelect('groupID', "select groupID, name from membership_groups order by name", $groupID);
					}else{
						echo $adminConfig['anonymousGroup'];
					}

					echo $userPermissionsNote;
				?>
				</td>
			</tr>
		<?php if($anonMemberID!=$memberID){ ?>
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation["approved"] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="checkbox" name="isApproved" value="1" <?php echo ($isApproved ? "checked" : ($memberID ? "" : "checked")); ?>>
				</td>
			</tr>
		<?php } ?>
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation["banned"] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="checkbox" name="isBanned" value="1" <?php echo ($isBanned ? "checked" : ""); ?>>
				</td>
			</tr>
	<?php } ?>
		<?php if($adminConfig['custom1']!=''){ ?>
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $adminConfig['custom1']; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="custom1" value="<?php echo $custom1; ?>" size="40" class="formTextBox">
				</td>
			</tr>
		<?php } ?>
		<?php if($adminConfig['custom2']!=''){ ?>
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $adminConfig['custom2']; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="custom2" value="<?php echo $custom2; ?>" size="40" class="formTextBox">
				</td>
			</tr>
		<?php } ?>
		<?php if($adminConfig['custom3']!=''){ ?>
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $adminConfig['custom3']; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="custom3" value="<?php echo $custom3; ?>" size="40" class="formTextBox">
				</td>
			</tr>
		<?php } ?>
		<?php if($adminConfig['custom4']!=''){ ?>
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $adminConfig['custom4']; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="custom4" value="<?php echo $custom4; ?>" size="40" class="formTextBox">
				</td>
			</tr>
		<?php } ?>
		<tr>
			<td align="right" valign="top" class="tdFormCaption">
				<div class="formFieldCaption"><?php echo $Translation["comments"] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<textarea name="comments" cols="50" rows="3" class="formTextBox"><?php echo $comments; ?></textarea>
				</td>
			</tr>
		<tr>
			<td colspan="2" align="right" class="tdFormFooter">
				<input type="submit" name="saveChanges" value="<?php echo $Translation["save changes"] ; ?>">
				</td>
			</tr>
		</table></div>
	</form>


<?php
	include("{$currDir}/incFooter.php");
?>
