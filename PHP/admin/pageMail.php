<?php
	$currDir = dirname(__FILE__);
	require("{$currDir}/incCommon.php");
	include("{$currDir}/incHeader.php");

	// check configured sender
	if(!isEmail($adminConfig['senderEmail'])){
		?>
		<div class="alert alert-danger">
				<?php echo $Translation["can not send mail"]; ?>
		</div>
		<?php
		include("{$currDir}/incFooter.php");
	}

	// determine and validate recipients
	if($_POST['saveChanges']==''){
		$memberID=makeSafe(strtolower($_GET['memberID']));
		$groupID=intval($_GET['groupID']);
		$sendToAll=intval($_GET['sendToAll']);

		$isGroup=($memberID!='' ? FALSE : TRUE);
		$recipient=($sendToAll ? $Translation["all groups"] : ($isGroup ? sqlValue("select name from membership_groups where groupID='$groupID'") : sqlValue("select memberID from membership_users where lcase(memberID)='$memberID'")));
		if(!$recipient){
			?>
			<div class="alert alert-danger">
				<?php echo $Translation["no recipient"];  ?>
			</div>
			<?php
			include("{$currDir}/incFooter.php");
		}
	}else{
	// begin sending emails
		$memberID=makeSafe(strtolower($_POST['memberID']));
		$groupID=intval($_POST['groupID']);
		$sendToAll=intval($_POST['sendToAll']);

		$mailSubject=(get_magic_quotes_gpc() ? $_POST['mailSubject'] : addslashes($_POST['mailSubject']));
		$mailMessage=(get_magic_quotes_gpc() ? $_POST['mailMessage'] : addslashes($_POST['mailMessage']));
		$mailMessage=str_replace("\n", "\\n", $mailMessage);
		$mailMessage=str_replace("\r", "\\r", $mailMessage);

		// validate that subject is a single line
		if(preg_match("/(%0A|%0D|\n+|\r+)/i", $mailSubject)){
			echo "<div class=\"status\">{$Translation["invalid subject line"]}</div>";
			exit;
		}

		$isGroup=($memberID!='' ? FALSE : TRUE);
		$recipient=($sendToAll ? $Translation["all groups"] : ($isGroup ? sqlValue("select name from membership_groups where groupID='$groupID'") : sqlValue("select lcase(memberID) from membership_users where lcase(memberID)='$memberID'")));
		if(!$recipient){
			?>
			<div class="alert alert-danger">
				<?php echo $Translation["no recipient"];  ?>
			</div>
			<?php
			include("{$currDir}/incFooter.php");
		}

		// create a recipients array
		if($sendToAll){
			$res=sql("select email from membership_users", $eo);
		}elseif($isGroup){
			$res=sql("select email from membership_users where groupID='$groupID'", $eo);
		}else{
			$res=sql("select email from membership_users where lcase(memberID)='$memberID'", $eo);
		}
		while($row=db_fetch_row($res)){
			$to[]=$row[0];
		}

		// check that there is at least 1 recipient
		if(count($to)<1){
			?>
			<div class="alert alert-danger">
				<?php echo $Translation["no recipient found"] ;  ?>
			</div>
			<?php
			include("{$currDir}/incFooter.php");
		}

		// save mail queue
		$queueFile=md5(microtime());
		$currDir = dirname(__FILE__);
		if(!$fp=fopen("{$currDir}/$queueFile.php", "w")){
			?>
			<div class="alert alert-danger">
				<?php echo str_replace ( "<CURRDIR>" , $currDir , $Translation["mail queue not saved"] ); ?>
			</div>
			<?php
			include("{$currDir}/incFooter.php");
		}else{
			fwrite($fp, "<?php\n");
			foreach($to as $recip){
				fwrite($fp, "\t\$to[]='$recip';\n");
			}
			fwrite($fp, "\t\$mailSubject=\"$mailSubject\";\n");
			fwrite($fp, "\t\$mailMessage=\"$mailMessage\";\n");
			fwrite($fp, "?>");
			fclose($fp);
		}

		// redirect to mail queue processor
		redirect("admin/pageSender.php?queue=$queueFile");
		include("{$currDir}/incFooter.php");
	}


?>

<div class="page-header"><h1><?php echo $Translation["send mail"] ;  ?></h1></div>

<?php if($sendToAll){ ?>
	<div class="alert alert-warning"><u><?php echo $Translation["attention"] ;  ?></u><br><?php echo $Translation["send mail to all members"] ; ?></div>
<?php } ?>

<form method="post" action="pageMail.php">
	<input type="hidden" name="memberID" value="<?php echo $memberID; ?>">
	<input type="hidden" name="groupID" value="<?php echo $groupID; ?>">
	<input type="hidden" name="sendToAll" value="<?php echo $sendToAll; ?>">
	<table class="table table-striped">
		<tr>
			<td class="tdFormCaption text-right flip" valign="top">
				<div class="formFieldCaption"><?php echo $Translation["from"] ; ?></div>
				</td>
			<td class="tdFormInput text-left flip">
				<?php echo $adminConfig['senderName']." &lt;".$adminConfig['senderEmail']."&gt;"; ?>
				<br><a href="pageSettings.php"><?php echo $Translation["change setting"] ; ?></a>
				</td>
			</tr>

		<tr>
			<td class="tdFormCaption text-right flip" valign="top">
				<div class="formFieldCaption"><?php echo $Translation["to"] ; ?></div>
				</td>
			<td class="tdFormInput text-left flip">
				<a href="<?php echo ($sendToAll ? "pageViewMembers.php" : ($isGroup ? "pageViewMembers.php?groupID=$groupID" : "pageEditMember.php?memberID=$memberID")); ?>"><img src="images/<?php echo (($isGroup||$sendToAll) ? "members_icon.gif" : "member_icon.gif"); ?>" border="0"></a> <?php echo $recipient; ?>
				</td>
			</tr>

		<tr>
			<td class="tdFormCaption text-right flip" valign="top">
				<div class="formFieldCaption"><?php echo $Translation["subject"] ; ?></div>
				</td>
			<td class="tdFormInput text-left flip">
				<input type="text" name="mailSubject" value="" size="60" class="formTextBox">
				</td>
			</tr>

		<tr>
			<td class="tdFormCaption text-right flip" valign="top">
				<div class="formFieldCaption"><?php echo $Translation["message"] ; ?></div>
				</td>
			<td class="tdFormInput text-left flip">
				<textarea name="mailMessage" cols="60" rows="10" class="formTextBox"></textarea>
				</td>
			</tr>

		<tr>
			<td colspan="2" class="tdFormFooter text-right flip">
				<input type="submit" name="saveChanges" value="<?php echo $Translation["send message"] ; ?>" onClick="return jsShowWait();">
				</td>
			</tr>
		</table>
</form>
<?php
	include("{$currDir}/incFooter.php");
?>
