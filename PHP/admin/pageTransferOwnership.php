<?php
	$currDir = dirname(__FILE__);
	require("{$currDir}/incCommon.php");
	include("{$currDir}/incHeader.php");

	/* we need the following variables:
		$sourceGroupID
		$sourceMemberID (-1 means "all")
		$destinationGroupID
		$destinationMemberID

		if $sourceGroupID!=$destinationGroupID && $sourceMemberID==-1, an additional var:
		$moveMembers (=0 or 1)
	*/

	// validate input vars
	$sourceGroupID=intval($_GET['sourceGroupID']);
	$sourceMemberID=makeSafe(strtolower($_GET['sourceMemberID']));
	$destinationGroupID=intval($_GET['destinationGroupID']);
	$destinationMemberID=makeSafe(strtolower($_GET['destinationMemberID']));
	$moveMembers=intval($_GET['moveMembers']);

	// transfer operations
	if($sourceGroupID && $sourceMemberID && $destinationGroupID && ($destinationMemberID || $moveMembers) && $_GET['beginTransfer']!=''){
		/* validate everything:
			1. Make sure sourceMemberID belongs to sourceGroupID
			2. if moveMembers is false, make sure destinationMemberID belongs to destinationGroupID
		*/
		if(!sqlValue("select count(1) from membership_users where lcase(memberID)='$sourceMemberID' and groupID='$sourceGroupID'")){
			if($sourceMemberID!=-1){
				errorMsg( $Translation['invalid source member'] );
				include("{$currDir}/incFooter.php");
			}
		}
		if(!$moveMembers){
			if(!sqlValue("select count(1) from membership_users where lcase(memberID)='$destinationMemberID' and groupID='$destinationGroupID'")){
				errorMsg($Translation['invalid destination member']);
				include("{$currDir}/incFooter.php");
			}
		}

		// get group names
		$sourceGroup=sqlValue("select name from membership_groups where groupID='$sourceGroupID'");
		$destinationGroup=sqlValue("select name from membership_groups where groupID='$destinationGroupID'");

		// begin transfer
		echo "<br><br><br>";
		if($moveMembers && $sourceMemberID!=-1){
			$originalValues =  array ('<MEMBERID>','<SOURCEGROUP>','<DESTINATIONGROUP>' );
			$replaceValues = array ( $sourceMemberID , $sourceGroup , $destinationGroup );
			echo str_replace ( $originalValues , $replaceValues , $Translation['moving member'] );

			// change source member group
			sql("update membership_users set groupID='$destinationGroupID' where lcase(memberID)='$sourceMemberID' and groupID='$sourceGroupID'", $eo);
			$newGroup=sqlValue("select name from membership_users u, membership_groups g where u.groupID=g.groupID and lcase(u.memberID)='$sourceMemberID'");

			// change group of source member's data
			sql("update membership_userrecords set groupID='$destinationGroupID' where lcase(memberID)='$sourceMemberID' and groupID='$sourceGroupID'", $eo);
			$dataRecs=sqlValue("select count(1) from membership_userrecords where lcase(memberID)='$sourceMemberID' and groupID='$destinationGroupID'");

			// status
			$originalValues =  array ('<MEMBERID>','<NEWGROUP>','<DATARECORDS>' );
			$replaceValues = array ( $sourceMemberID , $newGroup , $dataRecs );
			$status = str_replace ( $originalValues , $replaceValues , $Translation['data records transferred'] );

		}elseif(!$moveMembers && $sourceMemberID!=-1){
			$originalValues =  array ('<SOURCEMEMBER>','<SOURCEGROUP>', '<DESTINATIONMEMBER>' , '<DESTINATIONGROUP>' );
			$replaceValues = array ( $sourceMemberID , $sourceGroup ,$destinationMemberID ,  $destinationGroup );
			echo str_replace ( $originalValues , $replaceValues , $Translation['moving data'] );

			// change group and owner of source member's data
			$srcDataRecsBef=sqlValue("select count(1) from membership_userrecords where lcase(memberID)='$sourceMemberID' and groupID='$sourceGroupID'");
			sql("update membership_userrecords set groupID='$destinationGroupID', memberID='$destinationMemberID' where lcase(memberID)='$sourceMemberID' and groupID='$sourceGroupID'", $eo);
			$srcDataRecsAft=sqlValue("select count(1) from membership_userrecords where lcase(memberID)='$sourceMemberID' and groupID='$sourceGroupID'");

			// status
			$originalValues =  array ('<SOURCEMEMBER>','<SOURCEGROUP>', '<DATABEFORE>' ,'<TRANSFERSTATUS>', '<DESTINATIONMEMBER>' , '<DESTINATIONGROUP>' );
			$transferStatus = ($srcDataRecsAft>0 ? "No records were transferred" : "These records now belong");
			$replaceValues = array ( $sourceMemberID , $sourceGroup , $srcDataRecsBef , $transferStatus ,$destinationMemberID , $destinationGroup );
			$status = str_replace ( $originalValues , $replaceValues , $Translation['member records status'] );

		}elseif($moveMembers){
			$originalValues =  array ('<SOURCEGROUP>', '<DESTINATIONGROUP>' );
			$replaceValues = array (  $sourceGroup ,$destinationGroup );
			echo str_replace ( $originalValues , $replaceValues , $Translation['moving all group members'] );

			// change source members group
			sql("update membership_users set groupID='$destinationGroupID' where groupID='$sourceGroupID'", $eo);
			$srcGroupMembers=sqlValue("select count(1) from membership_users where groupID='$sourceGroupID'");

			// change group of source member's data
			if(!$srcGroupMembers){
				$dataRecsBef=sqlValue("select count(1) from membership_userrecords where groupID='$sourceGroupID'");
				sql("update membership_userrecords set groupID='$destinationGroupID' where groupID='$sourceGroupID'", $eo);
				$dataRecsAft=sqlValue("select count(1) from membership_userrecords where groupID='$sourceGroupID'");
			}

			// status
			$originalValues =  array ('<SOURCEGROUP>', '<DESTINATIONGROUP>' );
			$replaceValues = array (  $sourceGroup ,$destinationGroup );
			if($srcGroupMembers){
				$status = str_replace ( $originalValues , $replaceValues , $Translation['failed transferring group members'] );
			}else{
				$status = str_replace ( $originalValues , $replaceValues , $Translation['group members transferred'] );

				if($dataRecsAft){
					$status.= $Translation['failed transfer data records'];
				}else{
					$status.= str_replace ( '<DATABEFORE>' , $dataRecsBef , $Translation['data records were transferred'] );
				}
			}

		}else{
			$originalValues =  array ('<SOURCEGROUP>', '<DESTINATIONMEMBER>' , '<DESTINATIONGROUP>' );
			$replaceValues = array (  $sourceGroup , $destinationMemberID , $destinationGroup );
			echo str_replace ( $originalValues , $replaceValues , $Translation['moving group data to member'] );

			// change group of source member's data
			$recsBef=sqlValue("select count(1) from membership_userrecords where lcase(memberID)='$destinationMemberID'");
			sql("update membership_userrecords set groupID='$destinationGroupID', memberID='$destinationMemberID' where groupID='$sourceGroupID'", $eo);
			$recsAft=sqlValue("select count(1) from membership_userrecords where lcase(memberID)='$destinationMemberID'");

			// status
			$originalValues =  array ( '<NUMBER>' , '<SOURCEGROUP>', '<DESTINATIONMEMBER>' , '<DESTINATIONGROUP>' );
			$recordsNumber = intval($recsAft-$recsBef);
			$replaceValues = array ( $recordsNumber ,  $sourceGroup , $destinationMemberID , $destinationGroup );
			$status= str_replace ( $originalValues , $replaceValues , $Translation['moving group data to member status'] );

		}

		// display status and a batch bookmark for later instant reuse of the wizard
		?>
		<div class="alert alert-info"><b><?php echo $Translation['status']; ?></b><br><?php echo $status; ?></div>
		<div>
			<?php 
				$originalValues =  array ( '<SOURCEGROUP>' , '<SOURCEMEMBER>' , '<DESTINATIONGROUP>' , '<DESTINATIONMEMBER>' , '<MOVEMEMBERS>' );
				$replaceValues = array ( $sourceGroupID , urlencode($sourceMemberID) , $destinationGroupID , urlencode($destinationMemberID) , $moveMembers );
				echo str_replace ( $originalValues , $replaceValues , $Translation['batch transfer link'] );
			?>
		</div>
		<?php

		// quit
		include("{$currDir}/incFooter.php");
	}


	// STEP 1
	?>

	<div class="page-header"><h1><?php echo $Translation['ownership batch transfer'] ; ?></h1></div>

	<form method="get" action="pageTransferOwnership.php">
		<table class="table table-striped">
			<tr>
				<td class="tdHeader" colspan="2">
					<h3><?php echo $Translation['step 1'] ; ?></h3>
						<?php echo $Translation['batch transfer wizard'] ; ?>
					</td>
				</tr>
			<tr>
				<td class="tdFormCaption">
					<?php echo $Translation['source group'] ; ?>
					</td>
				<td class="tdCell">
					<?php
						echo htmlSQLSelect("sourceGroupID", "select distinct g.groupID, g.name from membership_groups g, membership_users u where g.groupID=u.groupID order by g.name", $sourceGroupID);
					?>
					<input type="submit" value="<?php echo ($sourceGroupID ? $Translation['update'] : $Translation['next step']); ?>">
					</td>
				</tr>
	<?php

	// STEP 2
		if($sourceGroupID){
			?>
			<tr>
				<td class="tdCell" colspan="2">
					<?php 
						$originalValues =  array ( '<MEMBERS>' , '<RECORDS>' );
						$membersNum = sqlValue("select count(1) from membership_users where groupID='$sourceGroupID'"); 
						$recordsNum = sqlValue("select count(1) from membership_userrecords where groupID='$sourceGroupID'");
						$replaceValues = array ( $membersNum , $recordsNum );
						echo str_replace ( $originalValues , $replaceValues , $Translation['group statistics']  );
					?>
					</td>
				</tr>
			<tr>
				<td class="tdHeader" colspan="2">
					<h3><?php echo $Translation['step 2'] ; ?></h3>
						<?php echo $Translation['source member message'] ; ?>
					</td>
				</tr>
			<tr>
				<td class="tdFormCaption">
					<?php echo $Translation['source member'] ; ?>
					</td>
				<td class="tdCell">
					<?php
						$arrVal[]='';
						$arrCap[]='';
						$arrVal[]='-1';
						$arrCap[]= str_replace ('<GROUPNAME>' , htmlspecialchars(sqlValue("select name from membership_groups where groupID='$sourceGroupID'")) , $Translation['all group members'] );
						if($res=sql("select lcase(memberID), lcase(memberID) from membership_users where groupID='$sourceGroupID' order by memberID", $eo)){
							while($row=db_fetch_row($res)){
								$arrVal[]=$row[0];
								$arrCap[]=$row[1];
							}
							echo htmlSelect("sourceMemberID", $arrVal, $arrCap, $sourceMemberID);
						}
					?>
					<input type="submit" value="<?php echo ($sourceMemberID ? $Translation['update'] : $Translation['next step'] ); ?>">
					</td>
				</tr>
			<?php
		}

	// STEP 3
		if($sourceMemberID){
			?>
			<tr>
				<td class="tdCell" colspan="2">
					<?php
						$recordsNum = sqlValue("select count(1) from membership_userrecords where ".($sourceMemberID==-1 ? "groupID='$sourceGroupID'" : "memberID='$sourceMemberID'"));
						echo str_replace ('<RECORDS>' , $recordsNum , $Translation['member statistics'] );
					?>
					</td>
				</tr>
			<tr>
				<td class="tdHeader" colspan="2">
					<h3><?php echo $Translation['step 3'] ; ?></h3>
					<?php echo $Translation['destination group message'] ; ?>
				</td>
				</tr>
			<tr>
				<td class="tdFormCaption">
					<?php echo $Translation['destination group'] ; ?>
					</td>
				<td class="tdCell">
					<?php
						echo htmlSQLSelect("destinationGroupID", "select distinct membership_groups.groupID, name from membership_groups, membership_users where membership_groups.groupID=membership_users.groupID order by name", $destinationGroupID);
					?>
					<input type="submit" value="<?php echo ($destinationGroupID ? $Translation['update'] : $Translation['next step'] ); ?>">
					</td>
				</tr>
			<?php
		}

	// STEP 4, source group same as destination
		if($destinationGroupID && $destinationGroupID==$sourceGroupID){
			?>
			<tr>
				<td class="tdHeader" colspan="2">
					<h3><?php echo $Translation['step 4'] ; ?></h3>
					<?php echo $Translation['destination member message'] ; ?>
				</td>
				</tr>
			<tr>
				<td class="tdFormCaption">
					<?php echo $Translation['destination member'] ; ?>
				</td>
				<td class="tdCell">
					<?php
						echo htmlSQLSelect("destinationMemberID", "select lcase(memberID), lcase(memberID) from membership_users where groupID='$destinationGroupID' and lcase(memberID)!='$sourceMemberID' order by memberID", $destinationMemberID);
					?>
					</td>
				</tr>
			<tr>
				<td class="tdFormFooter" colspan="2" align="right">
					<input type="submit" name="beginTransfer" value="<?php echo $Translation['begin transfer'] ; ?>" onClick="return jsConfirmTransfer();">
					</td>
				</tr>
			<?php

	// STEP 4, source group not same as destination
		}elseif($destinationGroupID){
			?>
			<tr>
				<td class="tdHeader" colspan="2">
					<h3><?php echo $Translation['step 4'] ; ?></h3>
					<?php
						$noMove=($sourceGroupID==sqlValue("select groupID from membership_groups where name='".$adminConfig['anonymousGroup']."'"));
						if(!$noMove){
							echo $Translation['move records'] ; 
						}
					?>
					</td>
				</tr>
			<?php
				if(sqlValue("select count(1) from membership_users where groupID='$destinationGroupID'")>0){
					$destinationHasMembers=TRUE;
					?>
					<tr>
						<td class="tdCell" colspan="2">
							<input type="radio" name="moveMembers" id="dontMoveMembers" value="0" <?php echo ($moveMembers ? "" : "checked"); ?>>
							<?php 
								echo $Translation['move data records to member'] ; 
								echo htmlSQLSelect("destinationMemberID", "select lcase(memberID), lcase(memberID) from membership_users where groupID='$destinationGroupID' order by memberID", $destinationMemberID);
							?>
							</td>
						</tr>
					<?php
				}else{
					$destinationHasMembers=FALSE;
				}

				if(!$noMove){
					?>
					<tr>
						<td class="tdCell" colspan="2">
							<input type="radio" name="moveMembers" id="moveMembers" value="1" <?php echo ($moveMembers || !$destinationHasMembers ? "checked" : ""); ?>>
							<?php echo str_replace('<GROUPNAME>', sqlValue("select name from membership_groups where groupID='$destinationGroupID'") , $Translation['move source member to group'] ); ?>
							</td>
						</tr>
					<?php
				}
			?>
			<tr>
				<td class="tdFormFooter" colspan="2" align="right">
					<input type="submit" name="beginTransfer" value="<?php echo $Translation['begin transfer'] ; ?>" onClick="return jsConfirmTransfer();">
				</td>
				</tr>
			<?php
		}
	?>
			</table>
		</form>


	<?php


?>

<?php
	include("{$currDir}/incFooter.php");
?>
