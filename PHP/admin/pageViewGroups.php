<?php
	$currDir = dirname(__FILE__);
	require("{$currDir}/incCommon.php");
	include("{$currDir}/incHeader.php");

	if($_GET['searchGroups'] != ""){
		$searchSQL = makeSafe($_GET['searchGroups']);
		$searchHTML = htmlspecialchars($_GET['searchGroups']);
		$where = "where name like '%$searchSQL%' or description like '%$searchSQL%'";
	}else{
		$searchSQL = '';
		$searchHTML = '';
		$where = "";
	}

	$numGroups = sqlValue("select count(1) from membership_groups $where");
	if(!$numGroups && $searchSQL != ''){
		echo "<div class=\"status\">{$Translation['no matching results found']}</div>";
		$noResults = true;
		$page = 1;
	}else{
		$noResults = false;
	}

	$page = intval($_GET['page']);
	if($page < 1){
		$page = 1;
	}elseif($page > ceil($numGroups / $adminConfig['groupsPerPage']) && !$noResults){
		redirect("admin/pageViewGroups.php?page=" . ceil($numGroups / $adminConfig['groupsPerPage']));
	}

	$start = ($page - 1) * $adminConfig['groupsPerPage'];

?>
<div class="page-header"><h1><?php echo $Translation['groups'] ; ?></h1></div>

<table class="table table-striped">
	<tr>
		<td colspan="5" align="center">
			<form method="get" action="pageViewGroups.php">
				<input type="hidden" name="page" value="1">
				<?php echo $Translation['search groups'] ; ?>
				<input class="formTextBox" type="text" name="searchGroups" value="<?php echo $searchHTML; ?>" size="20">
				<input type="submit" value="<?php echo $Translation['find'] ; ?>">
				<input type="button" value="<?php echo $Translation['reset'] ; ?>" onClick="window.location='pageViewGroups.php';">
				</form>
			</td>
		</tr>
	<tr>
		<td class="tdHeader">&nbsp;</td>
		<td class="tdHeader"><div class="ColCaption"><?php echo $Translation["group"]  ; ?></div></td>
		<td class="tdHeader"><div class="ColCaption"><?php echo $Translation["description"] ; ?></div></td>
		<td class="tdHeader"><div class="ColCaption"><?php echo $Translation['members count'] ; ?></div></td>
		<td class="tdHeader">&nbsp;</td>
		</tr>
<?php

	$res = sql("select groupID, name, description from membership_groups $where limit $start, ".$adminConfig['groupsPerPage'], $eo);
	while( $row = db_fetch_row($res)){
		$groupMembersCount = sqlValue("select count(1) from membership_users where groupID='$row[0]'");
		?>
		<tr>
			<td class="tdCaptionCell" align="left">
				<a href="pageEditGroup.php?groupID=<?php echo $row[0]; ?>"><img border="0" src="images/edit_icon.gif" alt="<?php echo $Translation['Edit group'] ; ?>" title="<?php echo $Translation['Edit group'] ; ?>"></a>
				<?php
					if(!$groupMembersCount){
						?>
						<a href="pageDeleteGroup.php?groupID=<?php echo $row[0]; ?>" onClick="return confirm('<?php echo $Translation['confirm delete group'] ; ?>');"><img border="0" src="images/delete_icon.gif" alt="<?php echo $Translation['delete group'] ; ?>" title="<?php echo $Translation['delete group'] ; ?>"></a>
						<?php
					}else{
						echo "&nbsp; &nbsp;";
					}
				?>
				</td>
			<td class="tdCell" align="left"><a href="pageEditGroup.php?groupID=<?php echo $row[0]; ?>"><?php echo $row[1]; ?></a></td>
			<td class="tdCell" align="left"><?php echo thisOr($row[2]); ?></td>
			<td align="right" class="tdCell">
				<?php echo $groupMembersCount; ?>
				</td>
			<td class="tdCaptionCell" align="left">
				<a href="pageEditMember.php?groupID=<?php echo $row[0]; ?>"><img border="0" src="images/add_icon.gif" alt="<?php echo $Translation["add new member"] ; ?>" title="<?php echo $Translation["add new member"] ; ?>"></a>
				<a href="pageViewRecords.php?groupID=<?php echo $row[0]; ?>"><img border="0" src="images/data_icon.gif" alt="<?php echo $Translation['view group records'] ; ?>" title="<?php echo $Translation['view group records'] ; ?>"></a>
				<?php if($groupMembersCount){ ?>
				<a href="pageViewMembers.php?groupID=<?php echo $row[0]; ?>"><img border="0" src="images/members_icon.gif" alt="<?php echo $Translation['view group members'] ; ?>" title="<?php echo $Translation['view group members'] ; ?>"></a>
				<a href="pageMail.php?groupID=<?php echo $row[0]; ?>"><img border="0" src="images/mail_icon.gif" alt="<?php echo $Translation['send message to group'] ; ?>" title="<?php echo $Translation['send message to group'] ; ?>"></a>
				<?php } ?>
				</td>
			</tr>
		<?php
	}
	?>
	<tr>
		<td colspan="5">
			<table width="100%" cellspacing="0">
				<tr>
				<td align="left" class="tdFooter">
					<input type="button" onClick="window.location='pageViewGroups.php?searchGroups=<?php echo $searchHTML; ?>&page=<?php echo ($page>1 ? $page-1 : 1); ?>';" value="<?php echo $Translation['previous'] ; ?>">
					</td>
				<td align="center" class="tdFooter">
					<?php 
						$originalValues =  array ('<GROUPNUM1>','<GROUPNUM2>','<GROUPS>' );
						$replaceValues = array ( $start+1 , $start+db_num_rows($res) , $numGroups );
						echo str_replace ( $originalValues , $replaceValues , $Translation['displaying groups'] );
					?>
				</td>
				<td align="right" class="tdFooter">
					<input type="button" onClick="window.location='pageViewGroups.php?searchGroups=<?php echo $searchHTML; ?>&page=<?php echo ($page<ceil($numGroups/$adminConfig['groupsPerPage']) ? $page+1 : ceil($numGroups/$adminConfig['groupsPerPage'])); ?>';" value="<?php echo $Translation['next'] ; ?>">
				</td>
			</tr></table></td>
		</tr>
	<tr>
		<td colspan="5">
			<table class="table">
				<tr>
					<td colspan="2"><br><b><?php echo $Translation['key'] ; ?></b></td>
					</tr>
				<tr>
					<td><img src="images/edit_icon.gif"> <?php echo $Translation['edit group details'] ; ?></td>
					<td><img src="images/delete_icon.gif"> <?php echo $Translation['delete group'] ; ?>.</td>
					</tr>
				<tr>
					<td><img src="images/add_icon.gif"> <?php echo $Translation['add member to group'] ; ?></td>
					<td><img src="images/data_icon.gif"> <?php echo $Translation['view data records'] ; ?></td>
					</tr>
				<tr>
					<td><img src="images/members_icon.gif"> <?php echo $Translation['list group members'] ; ?></td>
					<td><img src="images/mail_icon.gif"> <?php echo $Translation['send email to all members'] ; ?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

<?php
	include("{$currDir}/incFooter.php");
?>