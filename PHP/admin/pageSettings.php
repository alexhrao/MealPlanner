<?php
	$currDir = dirname(__FILE__);
	require("{$currDir}/incCommon.php");
	include("{$currDir}/incHeader.php");

	if(isset($_POST['saveChanges'])){
		// csrf check
		if(!csrf_token(true)){
			?>
			<div class="alert alert-danger">
				<?php echo $Translation['invalid security token'] ; ?>
			</div>
			<?php
			include("{$currDir}/incFooter.php");
		}

		// validate inputs
		$errors = array();

		// if admin username changed, check if the new username already exists
		$adminUsername = makeSafe(strtolower($_POST['adminUsername']));
		if($adminConfig['adminUsername'] != strtolower(undo_magic_quotes($_POST['adminUsername'])) && sqlValue("select count(1) from membership_users where lcase(memberID)='$adminUsername'")){
			$errors[] = $Translation['unique admin username error'] ;
		}

		// if anonymous username changed, check if the new username already exists
		$anonymousMember = makeSafe(strtolower($_POST['anonymousMember']));
		if($adminConfig['anonymousMember'] != strtolower(undo_magic_quotes($_POST['anonymousMember'])) && sqlValue("select count(1) from membership_users where lcase(memberID)='$anonymousMember'")){
			$errors[] = $Translation['unique anonymous username error'];
		}

		// if anonymous group name changed, check if the new group name already exists
		$anonymousGroup = makeSafe($_POST['anonymousGroup']);
		if($adminConfig['anonymousGroup'] != undo_magic_quotes($_POST['anonymousGroup']) && sqlValue("select count(1) from membership_groups where name='$anonymousGroup'")){
			$errors[] = $Translation['unique anonymous group name error'];
		}

		$adminPassword = $_POST['adminPassword'];
		if($adminPassword != '' && $adminPassword == $_POST['confirmPassword']){
			$adminPassword = md5($adminPassword);
		}elseif($adminPassword != '' && $adminPassword != $_POST['confirmPassword']){
			$errors[] = $Translation['admin password mismatch'];
		}else{
			$adminPassword = $adminConfig['adminPassword'];
		}

		if(!isEmail($_POST['senderEmail'])){
			$errors[] = $Translation['invalid sender email'];
		}

		if(count($errors)){
			?>
			<div class="alert alert-danger">
				<?php echo $Translation['errors occurred'] ;  ?>
				<ul><li><?php echo implode('</li><li>', $errors); ?></li></ul>
				<?php echo $Translation['go back'] ;  ?>
			</div>
			<?php
			include("{$currDir}/incFooter.php");
		}

		$new_config = array(
			'dbServer' => config('dbServer'),
			'dbUsername' => config('dbUsername'),
			'dbPassword' => config('dbPassword'),
			'dbDatabase' => config('dbDatabase'),

			'adminConfig' => array(
				'adminUsername' => strtolower(undo_magic_quotes($_POST['adminUsername'])),
				'adminPassword' => $adminPassword,
				'notifyAdminNewMembers' => intval($_POST['notifyAdminNewMembers']),
				'defaultSignUp' => intval($_POST['visitorSignup']),
				'anonymousGroup' => undo_magic_quotes($_POST['anonymousGroup']),
				'anonymousMember' => strtolower(undo_magic_quotes($_POST['anonymousMember'])),
				'groupsPerPage' => (intval($_POST['groupsPerPage']) > 0 ? intval($_POST['groupsPerPage']) : $adminConfig['groupsPerPage']),
				'membersPerPage' => (intval($_POST['membersPerPage']) > 0 ? intval($_POST['membersPerPage']) : $adminConfig['membersPerPage']),
				'recordsPerPage' => (intval($_POST['recordsPerPage']) > 0 ? intval($_POST['recordsPerPage']) : $adminConfig['recordsPerPage']),
				'custom1' => undo_magic_quotes($_POST['custom1']),
				'custom2' => undo_magic_quotes($_POST['custom2']),
				'custom3' => undo_magic_quotes($_POST['custom3']),
				'custom4' => undo_magic_quotes($_POST['custom4']),
				'MySQLDateFormat' => undo_magic_quotes($_POST['MySQLDateFormat']),
				'PHPDateFormat' => undo_magic_quotes($_POST['PHPDateFormat']),
				'PHPDateTimeFormat' => undo_magic_quotes($_POST['PHPDateTimeFormat']),
				'senderName' => undo_magic_quotes($_POST['senderName']),
				'senderEmail' => $_POST['senderEmail'],
				'approvalSubject' => undo_magic_quotes($_POST['approvalSubject']),
				'approvalMessage' => undo_magic_quotes($_POST['approvalMessage']),
				'hide_twitter_feed' => ($_POST['hide_twitter_feed'] ? true : false)
			)
		);

		// save changes
		$save_result = save_config($new_config);
		if($save_result === true){
			// update admin member
			sql( "update membership_users set memberID='$adminUsername', passMD5='$adminPassword', email='{$_POST['senderEmail']}', comments=concat_ws('', comments, '\\n', '".str_replace ( "<DATE>" , @date('Y-m-d') , $Translation['record updated automatically'] ) ."') where lcase(memberID)='" . makeSafe(strtolower($adminConfig['adminUsername'])) . "'" , $eo);
			$_SESSION['memberID'] = $_SESSION['adminUsername'] = strtolower(undo_magic_quotes($_POST['adminUsername']));

			// update anonymous group name if changed
			if($adminConfig['anonymousGroup'] != undo_magic_quotes($_POST['anonymousGroup'])){
				sql("update membership_groups set name='$anonymousGroup' where name='" . addslashes($adminConfig['anonymousGroup']) . "'", $eo);
			}

			// update anonymous username if changed
			if($adminConfig['anonymousMember'] != undo_magic_quotes($_POST['anonymousMember'])){
				sql("update membership_users set memberID='$anonymousMember' where memberID='" . addslashes($adminConfig['anonymousMember']) . "'", $eo);
			}

			// display status
			echo "<div class=\"status\">{$Translation['admin settings saved']}</div>";
		}else{
			// display status
			echo "<div class=\"alert alert-danger\">".str_replace ( '<ERROR>' , $save_result['error'] , $Translation['admin settings not saved'] )."</div>";
		}

		// exit
		include("{$currDir}/incFooter.php");
	}    

?>

<div class="page-header"><h1><?php echo $Translation["admin settings"] ; ?></h1></div>

<form method="post" action="pageSettings.php">
	<?php echo csrf_token(); ?>
	<table class="table table-striped">
		<tr><td align="center" colspan="2" class="tdFormCaption"><input type="checkbox" id="showToolTips" value="1" checked><label for="showToolTips"><?php echo $Translation['show tool tips'] ; ?></label></td></tr>
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation['admin username'] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="adminUsername" id="adminUsername" value="<?php echo htmlspecialchars($adminConfig['adminUsername']); ?>" size="20" class="formTextBox">
				</td>
			</tr>
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation['admin password'] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="password" autocomplete="off" name="adminPassword" id="adminPassword" value="" size="20" class="formTextBox">
				<br><?php echo $Translation['change admin password'] ; ?>
				</td>
			</tr>
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation["confirm password"] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="password" autocomplete="off" name="confirmPassword" id="confirmPassword" value="" size="20" class="formTextBox">
				</td>
			</tr>

		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation['sender email'] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="senderEmail" id="senderEmail" value="<?php echo htmlspecialchars($adminConfig['senderEmail']); ?>" size="40" class="formTextBox">
				<br><?php echo $Translation['sender name and email'] ; ?> 
				<br><?php echo $Translation['email messages'] ; ?>
				</td>
			</tr>
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation['admin notifications'] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<?php
					echo htmlRadioGroup(
						"notifyAdminNewMembers",
						array(0, 1, 2),
						array(
							$Translation['no email notifications'] ,
							$Translation['member waiting approval'] ,
							$Translation['new sign-ups']  
						),
						intval($adminConfig['notifyAdminNewMembers'])
					);
				?>
				</td>
			</tr>

		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation['sender name'] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="senderName" id="senderName" value="<?php echo htmlspecialchars($adminConfig['senderName']); ?>" size="40" class="formTextBox">
				</td>
			</tr>

		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation['members custom field 1'] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="custom1" id="custom1" value="<?php echo htmlspecialchars($adminConfig['custom1']); ?>" size="20" class="formTextBox">
				</td>
			</tr>
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation['members custom field 2'] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="custom2" id="custom2" value="<?php echo htmlspecialchars($adminConfig['custom2']); ?>" size="20" class="formTextBox">
				</td>
			</tr>
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation['members custom field 3'] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="custom3" id="custom3" value="<?php echo htmlspecialchars($adminConfig['custom3']); ?>" size="20" class="formTextBox">
				</td>
			</tr>
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation['members custom field 4'] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="custom4" id="custom4" value="<?php echo htmlspecialchars($adminConfig['custom4']); ?>" size="20" class="formTextBox">
				</td>
			</tr>

		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation['member approval email subject'] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="approvalSubject" id="approvalSubject" value="<?php echo htmlspecialchars($adminConfig['approvalSubject']); ?>" size="40" class="formTextBox">
				<br><?php echo $Translation['member approval email subject control'] ; ?>
				</td>
			</tr>

		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation['member approval email message'] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<textarea wrap="virtual" name="approvalMessage" cols="60" rows="6" class="formTextBox"><?php echo htmlspecialchars(str_replace(array('\r', '\n'), array("", "\n"), $adminConfig['approvalMessage'])); ?></textarea>
				</td>
			</tr>

		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation['MySQL date'] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="MySQLDateFormat" id="MySQLDateFormat" value="<?php echo htmlspecialchars($adminConfig['MySQLDateFormat']); ?>" size="30" class="formTextBox">
				<br><?php echo $Translation['MySQL reference'] ; ?>
				</td>
			</tr>
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation['PHP short date'] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="PHPDateFormat" id="PHPDateFormat" value="<?php echo htmlspecialchars($adminConfig['PHPDateFormat']); ?>" size="30" class="formTextBox">
				<br><?php echo $Translation['PHP manual'] ; ?> 
				</td>
			</tr>
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation['PHP long date'] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="PHPDateTimeFormat" id="PHPDateTimeFormat" value="<?php echo htmlspecialchars($adminConfig['PHPDateTimeFormat']); ?>" size="30" class="formTextBox">
				<br><?php echo $Translation['PHP manual'] ; ?>
				</td>
			</tr>

		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation['groups per page'] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="groupsPerPage" id="groupsPerPage" value="<?php echo htmlspecialchars($adminConfig['groupsPerPage']); ?>" size="5" class="formTextBox">
				</td>
			</tr>
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation['members per page'] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="membersPerPage" id="membersPerPage" value="<?php echo intval($adminConfig['membersPerPage']); ?>" size="5" class="formTextBox">
				</td>
			</tr>
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation['records per page'] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="recordsPerPage" id="recordsPerPage" value="<?php echo intval($adminConfig['recordsPerPage']); ?>" size="5" class="formTextBox">
				</td>
			</tr>



		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation['default sign-up mode'] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<?php
					echo htmlRadioGroup(
						"visitorSignup",
						array(0, 1, 2),
						array(
							$Translation['no sign-up allowed'] ,
							$Translation['admin approve members'],
							$Translation['automatically approve members'] 
						),
						intval($adminConfig['defaultSignUp'])
					);
				?>
				</td>
			</tr>
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation['anonymous group'] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="anonymousGroup" id="anonymousGroup" value="<?php echo htmlspecialchars($adminConfig['anonymousGroup']); ?>" size="30" class="formTextBox">
				</td>
			</tr>

		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation['anonymous user name'] ; ?></div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="anonymousMember" id="anonymousMember" value="<?php echo htmlspecialchars($adminConfig['anonymousMember']); ?>" size="30" class="formTextBox">
				</td>
			</tr>

		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption"><?php echo $Translation['hide twitter feed'] ; ?></div>
			</td>
			<td align="left" class="tdFormInput">
				<input type="checkbox" name="hide_twitter_feed" id="hide_twitter_feed" value="1" <?php echo ($adminConfig['hide_twitter_feed'] ? 'checked' : ''); ?>>
				<div class="text-info"><?php echo $Translation['twitter feed'] ; ?></div>
			</td>
		</tr>

		<tr>
			<td colspan="2" align="right" class="tdFormFooter">
				<input type="submit" name="saveChanges" value="<?php echo $Translation["save changes"] ; ?>" onClick="return jsValidateAdminSettings();">
				</td>
			</tr>
		</table>
</form>

<div style="height: 600px;"></div>

<?php
	include("{$currDir}/incFooter.php");
?>
