<?php
	$currDir = dirname(__FILE__);
	require("{$currDir}/incCommon.php");
	include("{$currDir}/incHeader.php");

	// process search
	if($_GET['searchMembers'] != ""){
		$searchSQL = makeSafe($_GET['searchMembers']);
		$searchHTML = htmlspecialchars($_GET['searchMembers']);
		$searchField = intval($_GET['searchField']);
		$searchFieldName = array_search($searchField, array(
			'm.memberID' => 1,
			'g.name' => 2,
			'm.email' => 3,
			'm.custom1' => 4,
			'm.custom2' => 5,
			'm.custom3' => 6,
			'm.custom4' => 7,
			'm.comments' => 8
		));
		if(!$searchFieldName){ // = search all fields
			$where = "where (m.memberID like '%{$searchSQL}%' or g.name like '%{$searchSQL}%' or m.email like '%{$searchSQL}%' or m.custom1 like '%{$searchSQL}%' or m.custom2 like '%{$searchSQL}%' or m.custom3 like '%{$searchSQL}%' or m.custom4 like '%{$searchSQL}%' or m.comments like '%{$searchSQL}%')";
		}else{ // = search a specific field
			$where = "where ({$searchFieldName} like '%{$searchSQL}%')";
		}
	}else{
		$searchSQL = '';
		$searchHTML = '';
		$searchField = 0;
		$searchFieldName = '';
		$where = '';
	}

	// process groupID filter
	$groupID = intval($_GET['groupID']);
	if($groupID){
		if($where != ''){
			$where .= " and (g.groupID='{$groupID}')";
		}else{
			$where = "where (g.groupID='{$groupID}')";
		}
	}

	// process status filter
	$status = intval($_GET['status']); // 1=waiting approval, 2=active, 3=banned, 0=any
	if($status){
		switch($status){
			case 1:
				$statusCond = "(m.isApproved=0)";
				break;
			case 2:
				$statusCond = "(m.isApproved=1 and m.isBanned=0)";
				break;
			case 3:
				$statusCond = "(m.isApproved=1 and m.isBanned=1)";
				break;
			default:
				$statusCond = "";
		}
		if($where != '' && $statusCond != ''){
			$where .= " and {$statusCond}";
		}else{
			$where = "where {$statusCond}";
		}
	}

# NEXT: Add a dateAfter and dateBefore filter [??]

	$numMembers=sqlValue("select count(1) from membership_users m left join membership_groups g on m.groupID=g.groupID $where");
	if(!$numMembers){
		echo "<div class=\"status\">{$Translation['no matching results found']}</div>";
		$noResults=TRUE;
		$page=1;
	}else{
		$noResults=FALSE;
	}

	$page=intval($_GET['page']);
	if($page<1){
		$page=1;
	}elseif($page>ceil($numMembers/$adminConfig['membersPerPage']) && !$noResults){
		redirect("admin/pageViewMembers.php?page=".ceil($numMembers/$adminConfig['membersPerPage']));
	}

	$start=($page-1)*$adminConfig['membersPerPage'];

?>
<div class="page-header"><h1><?php echo $Translation['members'] ; ?></h1></div>

<table class="table table-striped">
	<tr>
		<td colspan="10" align="center">
			<form method="get" action="pageViewMembers.php">
				<table class="table table-striped">
					<tr>
						<td valign="top" align="center">
							<input type="hidden" name="page" value="1">
							<?php 
								$originalValues =  array ('<SEARCH>','<HTMLSELECT>');
								$searchValue = "<input class='formTextBox' type='text' name='searchMembers' value='$searchHTML' size='20'>";
								$arrFields=array(0, 1, 2, 3, 4, 5, 6, 7, 8);
								$arrFieldCaptions=array( $Translation['all fields'] , $Translation['username'] , $Translation["group"] , $Translation["email"] , $adminConfig['custom1'], $adminConfig['custom2'], $adminConfig['custom3'], $adminConfig['custom4'], $Translation["comments"] );
								$htmlSelect = htmlSelect('searchField', $arrFields, $arrFieldCaptions, $searchField);
								$replaceValues = array ( $searchValue , $htmlSelect );
								echo str_replace ( $originalValues , $replaceValues , $Translation['search members'] );
							?>
							</td>
						<td valign="bottom" rowspan="2">
							<input type="submit" value="<?php echo $Translation['find'] ; ?>">
							<input type="button" value="<?php echo $Translation['reset'] ; ?>" onClick="window.location='pageViewMembers.php';">
						</td>
						</tr>
					<tr>
						<td align="center">
							<?php 
								echo $Translation["group"] ; 
								echo htmlSQLSelect("groupID", "select groupID, name from membership_groups order by name", $groupID);
							?>
							&nbsp; &nbsp; &nbsp; 
							Status
							<?php
								$arrFields=array(0, 1, 2, 3);
								$arrFieldCaptions=array(  $Translation['any'] , $Translation['waiting approval'], $Translation['active'] , $Translation['Banned'] );
								echo htmlSelect("status", $arrFields, $arrFieldCaptions, $status);
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
		<td class="tdHeader"><div class="ColCaption"><?php echo $Translation['sign up date'] ; ?></div></td>
		<td class="tdHeader"><div class="ColCaption"><?php echo $adminConfig['custom1']; ?></div></td>
		<td class="tdHeader"><div class="ColCaption"><?php echo $adminConfig['custom2']; ?></div></td>
		<td class="tdHeader"><div class="ColCaption"><?php echo $adminConfig['custom3']; ?></div></td>
		<td class="tdHeader"><div class="ColCaption"><?php echo $adminConfig['custom4']; ?></div></td>
		<td class="tdHeader"><div class="ColCaption"><?php echo $Translation['Status'] ; ?></div></td>
		<td class="tdHeader">&nbsp;</td>
		</tr>
<?php

	$res=sql("select lcase(m.memberID), g.name, DATE_FORMAT(m.signupDate, '".$adminConfig['MySQLDateFormat']."'), m.custom1, m.custom2, m.custom3, m.custom4, m.isBanned, m.isApproved from membership_users m left join membership_groups g on m.groupID=g.groupID $where order by m.signupDate limit $start, ".$adminConfig['membersPerPage'], $eo);
	while($row=db_fetch_row($res)){
		?>
		<tr>
			<td class="tdCaptionCell" align="left">
				<a href="pageEditMember.php?memberID=<?php echo $row[0]; ?>"><img border="0" src="images/edit_icon.gif" alt="<?php echo $Translation['Edit member'] ; ?>" title="<?php echo $Translation['Edit member'] ; ?>"></a>
				<a href="pageDeleteMember.php?memberID=<?php echo $row[0]; ?>" onClick="return confirm('<?php echo str_replace ( '<USERNAME>' , $row[0] , $Translation['sure delete user'] ); ?>');"><img border="0" src="images/delete_icon.gif" alt="<?php echo $Translation['delete member'] ; ?>" title="<?php echo $Translation['delete member'] ; ?>"></a>
				</td>
			<td class="tdCell" align="left"><?php echo thisOr($row[0]); ?></td>
			<td class="tdCell" align="left"><?php echo thisOr($row[1]); ?></td>
			<td class="tdCell" align="left"><?php echo thisOr($row[2]); ?></td>
			<td class="tdCell" align="left"><?php echo thisOr($row[3]); ?></td>
			<td class="tdCell" align="left"><?php echo thisOr($row[4]); ?></td>
			<td class="tdCell" align="left"><?php echo thisOr($row[5]); ?></td>
			<td class="tdCell" align="left"><?php echo thisOr($row[6]); ?></td>
			<td class="tdCell" align="left">
				<?php echo (($row[7] && $row[8]) ? $Translation['Banned'] : ($row[8] ? $Translation['active'] : $Translation['waiting approval'] )); ?>
				</td>
			<td class="tdCaptionCell" align="left">
				<?php
					if(!$row[8]){ // if member is not approved, display approve link
						?><a href="pageChangeMemberStatus.php?memberID=<?php echo $row[0]; ?>&approve=1"><img border="0" src="images/approve_icon.gif" alt="<?php echo $Translation["approve this member"] ; ?>" title="<?php echo $Translation["approve this member"] ; ?>"></a><?php
					}else{
						if($row[7]){ // if member is banned, display unban link
							?><a href="pageChangeMemberStatus.php?memberID=<?php echo $row[0]; ?>&unban=1"><img border="0" src="images/approve_icon.gif" alt="<?php echo $Translation["unban this member"] ; ?>" title="<?php echo $Translation["unban this member"] ; ?>"></a><?php
						}else{ // if member is not banned, display ban link
							?><a href="pageChangeMemberStatus.php?memberID=<?php echo $row[0]; ?>&ban=1"><img border="0" src="images/stop_icon.gif" alt="<?php echo $Translation["ban this member"] ; ?>" title="<?php echo $Translation["ban this member"] ; ?>"></a><?php
						}
					}
				?>
				<a href="pageViewRecords.php?memberID=<?php echo $row[0]; ?>"><img border="0" src="images/data_icon.gif" alt="<?php echo $Translation["View member records"] ; ?>" title="<?php echo $Translation["View member records"] ; ?>"></a>
				<?php if($adminConfig['anonymousMember']!=$row[0]){ ?>
				<a href="pageMail.php?memberID=<?php echo $row[0]; ?>"><img border="0" src="images/mail_icon.gif" alt="<?php echo $Translation["send message to member"] ; ?>" title="<?php echo $Translation["send message to member"] ; ?>"></a>
				<?php } ?>
				</td>
			</tr>
		<?php
	}
	?>
	<tr>
		<td colspan="10">
			<table width="100%" cellspacing="0">
				<tr>
				<td align="left" class="tdFooter">
					<input type="button" onClick="window.location='pageViewMembers.php?searchMembers=<?php echo $searchHTML; ?>&groupID=<?php echo $groupID; ?>&status=<?php echo $status; ?>&searchField=<?php echo $searchField; ?>&page=<?php echo ($page>1 ? $page-1 : 1); ?>';" value="<?php echo $Translation['previous'] ; ?>">
					</td>
				<td align="center" class="tdFooter">
					<?php 
						$originalValues =  array ('<MEMBERNUM1>','<MEMBERNUM2>','<MEMBERS>' );
						$replaceValues = array ( $start+1 , $start+db_num_rows($res) , $numMembers );
						echo str_replace ( $originalValues , $replaceValues , $Translation['displaying members'] );
					?>
				</td>
				<td align="right" class="tdFooter">
					<input type="button" onClick="window.location='pageViewMembers.php?searchMembers=<?php echo $searchHTML; ?>&groupID=<?php echo $groupID; ?>&status=<?php echo $status; ?>&searchField=<?php echo $searchField; ?>&page=<?php echo ($page<ceil($numMembers/$adminConfig['membersPerPage']) ? $page+1 : ceil($numMembers/$adminConfig['membersPerPage'])); ?>';" value="<?php echo $Translation['next'] ; ?>">
					</td>
			</tr></table></td>
		</tr>
	<tr>
		<td colspan="10">
			</td>
		</tr>
	<tr>
		<td colspan="10">
			<table class="table">
				<tr>
					<td colspan="2"><br><b><?php echo $Translation['key'] ; ?></b></td>
					</tr>
				<tr>
					<td><img src="images/edit_icon.gif"> <?php echo $Translation['edit member details'] ; ?>.</td>
					<td><img src="images/delete_icon.gif"> <?php echo $Translation['delete member'] ; ?>.</td>
					</tr>
				<tr>
					<td><img src="images/approve_icon.gif"> <?php echo $Translation['activate member'] ; ?></td>
					<td><img src="images/stop_icon.gif"> <?php echo $Translation['ban member'] ; ?></td>
					</tr>
				<tr>
					<td><img src="images/data_icon.gif"> <?php echo $Translation['view entered member records'] ; ?></td>
					<td><img src="images/mail_icon.gif"> <?php echo $Translation['send email to member'] ; ?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

<?php
	include("{$currDir}/incFooter.php");
?>
