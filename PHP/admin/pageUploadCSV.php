<?php
	@ini_set('auto_detect_line_endings', 1);
	define('MAXROWS', 500000); /* max records to import from the csv file per run */
	define('BATCHSIZE', 200); /* number of records to insert per query */

	ignore_user_abort(true);
	set_time_limit(0);
	@ini_set('auto_detect_line_endings', '1');
	$currDir = dirname(__FILE__);
	require("{$currDir}/incCommon.php");
	include("{$currDir}/incHeader.php");

	$arrTables=getTableList();

	if($_POST['csvPreview']!=''){
		$fn=(strpos($_POST['csvPreview'], 'Apply') === false ? getUploadedFile('csvFile') : $_SESSION['csvUploadFile']);

		$headCellStyle='border: solid 1px white; border-bottom: solid 1px #C0C0C0; border-right: solid 1px #C0C0C0; background-color: #ECECFB; font-weight: bold; font-size: 12px; padding: 0 2px;';
		$dataCellStyle='border: solid 1px white; border-bottom: solid 1px #C0C0C0; border-right: solid 1px #C0C0C0; font-size: 10px; padding: 0 2px;';

		if(!is_file($fn)){
			?>
			<div class="alert alert-danger">
				<?php echo str_replace ( '<FILENAME>' , $fn , $Translation['file not found error'] ) ; ?>
			</div>
			<?php
			include("{$currDir}/incFooter.php");
			exit;
		}

		$_SESSION['csvUploadFile'] = $fn;

		$arrPreviewData=getCSVArray(0, 10, false);
		if(!is_array($arrPreviewData)){
			die("<div class='alert alert-danger'>{$Translation['error']}: ". $arrPreviewData."</div>");
		}
		?>
		<div class="page-header"><h1><?php echo $Translation['preview and confirm CSV data'] ;  ?></h1></div>

		<form method="post" action="pageUploadCSV.php">
		<div class="table-responsive"><table class="table table-striped">
			<tr><td colspan="<?php echo (count($arrPreviewData[0])+1); ?>"><i><?php echo $Translation['display csv file rows'] ;  ?></i></td></tr>
			<tr><td width="60" style="<?php echo $headCellStyle; ?>">&nbsp;</td><?php
			foreach($arrPreviewData[0] as $fc){
				echo '<td style="'.$headCellStyle.'">'.$fc.'</td>';
			}
			?></tr><?php

		for($i=1; $i<count($arrPreviewData); $i++){
			?><tr><td style="<?php echo $headCellStyle; ?>" align="right"><?php echo $i; ?></td><?php
			foreach($arrPreviewData[$i] as $fv){
				?><td style="<?php echo $dataCellStyle; ?>"><?php echo nl2br($fv != '' ? (strlen($fv) > 20 ? substr($fv, 0, 18) . '...' : $fv) : '&nbsp;'); ?></td><?php
			}
			?></tr><?php
		}

		?>

		<tr><td align="left" colspan="<?php echo (count($arrPreviewData[0])+1); ?>" style="<?php echo $headCellStyle; ?>">
			<input type="button" value="<?php echo $Translation['change CSV settings'] ; ?>" style="font-weight: bold;" onclick=" 
				document.getElementById('advancedOptions').style.display='inline'; 
				document.getElementById('applyCSVSettings').style.display='inline';
				this.style.display='none';
				">
			<input type="submit" name="csvImport" value="<?php echo $Translation['import CSV data'] ; ?>" style="font-weight: bold;" onclick="this.visibility='hidden';">
			</td></tr>
		</table></div>

		<?php echo advancedCSVSettingsForm(); ?>
		<div id="applyCSVSettings" style="width: 850px; text-align: right; visibility: hidden;">
			<input type="submit" name="csvPreview" value="<?php echo $Translation['apply CSV settings'] ; ?>" style="font-weight: bold;">
			</div>
		<input type="hidden" name="tableName" value="<?php echo htmlspecialchars($_POST['tableName'])?>">
		</form>
		<?php
	}elseif($_POST['csvImport']!='' || $_GET['csvImport']!=''){
		if($_GET['csvImport']!=''){
			$_POST=$_GET;
			$csvStart=intval($_GET['csvStart']);
		}else{
			$csvStart=0;
		}

		// get settings
		getCSVSettings($csvIgnoreNRows, $csvCharsPerLine, $csvFieldSeparator, $csvFieldDelimiter, $csvFieldNamesOnTop, $csvUpdateIfPKExists, $csvBackupBeforeImport);

		// measure time
		$t1=array_sum(explode(' ', microtime()));

		// validate filename
		$fn=$_SESSION['csvUploadFile'];
		if($fn==''){
			die('<META HTTP-EQUIV="Refresh" CONTENT="0;url=pageUploadCSV.php?entropy='.rand().'">');
		}
		if(!is_file($fn)){
			?>
			<div class="alert alert-danger">
				<?php echo str_replace ( '<FILENAME>' , $fn , $Translation['file not found error'] ) ; ?>
			</div>
			<?php
			include("{$currDir}/incFooter.php");
			exit;
		}

		// estimate number of records
		if(!$_SESSION['csvEstimatedRecords']){
			if($handle=@fopen($fn, "r")){
				$i=0;
				while(!feof($handle)){
					$tempLine=fgets($handle, 4096);
					if(trim($tempLine)!='') $i++;
				}
				fclose($handle);
			}
			$_SESSION['csvEstimatedRecords']=($i-$csvIgnoreNRows-($csvFieldNamesOnTop ? 1 : 0));
		}

		// header
		?>
		<div class="page-header"><h1><?php echo $Translation['importing CSV data'] ; ?></h1></div>
		<div style="width: 700px; text-align: left;">
		<?php

		// get tablename and csv data
		$tn = $_POST['tableName'];
		$arrCSVData = getCSVArray($csvStart, 0, false);
		$originalValues =  array ('<RECORDNUMBER>','<RECORDS>' );
		$replaceValues = array ( number_format($csvStart) , number_format($_SESSION['csvEstimatedRecords']) );
		echo str_replace ( $originalValues , $replaceValues , $Translation['start at estimated record'] )."<br>";


		if(@count($arrCSVData)>1){
			// backup table
			if($_POST['csvBackupBeforeImport']){
				if(sqlValue("select count(1) from `$tn`")){
					$btn=$tn.'_backup_'.@date('YmdHis');
					sql("drop table if exists `$btn`", $eo);
					sql("create table if not exists `$btn` select * from `$tn`", $eo);

					$originalValues =  array ('<TABLE>','<TABLENAME>' );
					$replaceValues = array ( $tn , $btn );
					echo str_replace ( $originalValues , $replaceValues , $Translation['table backed up'])."<br><br>";
				}else{
					echo str_replace ( '<TABLE>' , $tn , $Translation['table backup not done'] )."<br><br>";
				}
			}

			// field list
			$fieldList='`'.implode('`,`', noSpaces($arrCSVData[0])).'`';

			// insert records
			$batch=BATCHSIZE; /* batch size (records per batch) */
			$numRows=count($arrCSVData)-1;
			$numBatches=ceil($numRows/$batch);

			echo '<textarea cols="70" rows="15" class="formTextBox">';
			for($i=1; $i<=$numRows; $i+=$batch){
				$insert='';
				for($j=$i; $j<($i+$batch) && $j<=$numRows; $j++){
					// add slashes to field values if necessary
					foreach($arrCSVData[$j] as $fi=>$fv){
						$arrCSVData[$j][$fi] = makeSafe($fv);
					}
					$valList=implode("','", $arrCSVData[$j]);
					if($valList!='' && strlen($valList)>count($arrCSVData[$j])*3)
						$insert.="('".$valList."'),";
				}

				// update record if pk matches
				if($_POST['csvUpdateIfPKExists']){
					$insert="replace `$tn` ($fieldList) values ".substr($insert, 0, -1);
				}else{
					$insert="insert ignore into `$tn` ($fieldList) values ".substr($insert, 0, -1);
				}

				// execute batch
				$originalValues =  array ('<BATCH>','<BATCHNUM>' );
				$replaceValues = array ( (($i-1)/$batch + 1) , $numBatches );
				echo str_replace ( $originalValues , $replaceValues , $Translation['importing batch']);

				if(!@db_query($insert)){
					echo "{$Translation['error']}: " . db_error(db_link()) . "\n";
				}else{
					echo $Translation['ok']."\n";
				}

				if(!($i%($batch*5)))   flush();
			}
			echo "</textarea>";
		}else{ /* no more records in csv file */
			$numRows=0;
		}

		if($numRows<MAXROWS){ /* reached end of data */
			// remove uploaded csv file
			@unlink($fn);
			$_SESSION['csvUploadFile']='';
			$_SESSION['csvEstimatedRecords']='';
			?>
			<br><b>
			<?php
				$secondsNum = round(array_sum(explode(' ', microtime())) - $t1, 3);
				$originalValues =  array ('<RECORDS>',  '<SECONDS>'  );
				$replaceValues = array ( $numRows , $secondsNum );
				echo str_replace ( $originalValues , $replaceValues , $Translation['records inserted or updated successfully'] );
			?> <i style="color: green;"><?php echo $Translation['mission accomplished'] ; ?></i>
			</b>
			<br><br><input type="button" name="assignOwner" value="<?php echo $Translation['assign a records owner'] ; ?>" style="font-weight: bold;" onclick="window.location='pageAssignOwners.php';">
			<?php
		}else{
			?>
			<META HTTP-EQUIV="Refresh" CONTENT="0;url=pageUploadCSV.php?csvImport=1&tableName=<?php echo urlencode($tn); ?>&csvBackupBeforeImport=0&csvUpdateIfPKExists=<?php echo $csvUpdateIfPKExists; ?>&csvIgnoreNRows=<?php echo $csvIgnoreNRows; ?>&csvCharsPerLine=<?php echo $csvCharsPerLine; ?>&csvFieldSeparator=<?echo urlencode($csvFieldSeparator); ?>&csvStart=<?php echo ($csvStart+$numRows); ?>&csvFieldDelimiter=<?php echo urlencode($csvFieldDelimiter); ?>">
			<br><b>
			<?php
				$secondsNum = round(array_sum(explode(' ', microtime())) - $t1, 3);
				$originalValues =  array ('<RECORDS>',  '<SECONDS>'  );
				$replaceValues = array ( $numRows , $secondsNum );
				echo str_replace ( $originalValues , $replaceValues , $Translation['records inserted or updated successfully'] );
			?> <i style="color: red; background-color: #FFFF9C;"><?php echo $Translation['please wait and do not close']; ?></i></b>
			<?php
		}
		echo '</div>';

	}else{ // first step
		?>
		<script>
			<!--
			function toggleAdvancedOptions(){
				var t=document.getElementById('advancedOptions');
				var b=document.getElementById('TAO');

				if(b.checked){
					t.style.display='inline';
					b.value='<?php echo $Translation['hide advanced options']; ?>';
				}else{
					t.style.display='none';
					b.value='<?php echo $Translation['show advanced options']; ?>';
				}
			}
			//-->
			</script>

		<div class="page-header"><h1><?php echo $Translation['import CSV to database']; ?></h1></div>

		<form enctype="multipart/form-data" method="post" action="pageUploadCSV.php">
			<table class="table table-striped">
				<tr>
					<td colspan="2" class="tdFormCaption">
						<div class="formFieldCaption">
							<?php echo $Translation['import CSV to database page']; ?>
						</div>
						</td>
					</tr>
				<tr>
					<td align="right" class="tdFormCaption" valign="top" width="250">
						<div class="formFieldCaption"><?php echo $Translation["table"] ; ?></div>
					</td>
					<td align="left" class="tdFormInput">
						<?php 
							echo htmlSelect('tableName', array_keys($arrTables), array_values($arrTables), '');
						?>
						<br><i><?php echo $Translation['populate table from CSV'] ; ?></i>
					</td>
					</tr>
				<tr>
					<td align="right" class="tdFormCaption" valign="top">
						<div class="formFieldCaption"><?php echo $Translation['CSV file'] ; ?></div>
					</td>
					<td align="left" class="tdFormInput">
						<input type="file" name="csvFile" class="formTextBox"><br>
					</td>
					</tr>
				<tr>
					<td align="left" class="tdFormCaption" valign="top" colspan="2">
						<div class="formFieldCaption"><input type="checkbox" id="TAO" onclick="toggleAdvancedOptions();"> <label for="TAO"><?php echo $Translation['show advanced options'] ; ?></label></div>
					</td>
					</tr>
				</table>

			<?php echo advancedCSVSettingsForm(); ?>

			<table class="table table-striped">
				<tr>
					<td align="right" class="tdFormCaption" valign="top" colspan="2">
						<input type="submit" name="csvPreview" value="<?php echo $Translation['preview CSV data'] ; ?>" style="font-weight: bold;">
					</td>
					</tr>
				</table>
			</form>
		<?php
	}

	include("{$currDir}/incFooter.php");

	##########################################################################
	function getCSVArray($start = 0, $numRows = 0, $makeSafe = true){
		global $Translation;

		if($numRows<1) $numRows=MAXROWS;

		getCSVSettings($csvIgnoreNRows, $csvCharsPerLine, $csvFieldSeparator, $csvFieldDelimiter, $csvFieldNamesOnTop, $csvUpdateIfPKExists, $csvBackupBeforeImport);

		$tn=$_POST['tableName'];
		if($tn=='')    return $Translation['no table name provided'];

		// get field names of table
		$res=sql('select * from `'.$tn.'` limit 1', $eo);
		for($i=0; $i<db_num_fields($res); $i++){
			$arrFieldName[]=db_field_name($res, $i);
		}

		$fn=$_SESSION['csvUploadFile'];
		if(!$fp=fopen($fn, 'r'))   return  str_replace ( '<FILENAME>' , $fn , $Translation['can not open CSV'] ) ;

		if($_POST['csvFieldNamesOnTop']==1){
			// read first line
			if(!$arr=fgetcsv($fp, $csvCharsPerLine, $csvFieldSeparator, $csvFieldDelimiter)){
				fclose($fp);
				return str_replace ( '<FILENAME>' , $fn , $Translation['empty CSV file'] ) ;
			}
			if(lineHasFieldNames($arr, $tn)){
				$arrCSVData[0]=arrayResize($arr, count($arrFieldName));
				// skip n+start rows
				for($i=0; $i<$csvIgnoreNRows+$start; $i++){
					if(!fgets($fp)){
						fclose($fp);
						return $arrCSVData;
					}
				}
				echo '<!-- getCSVArray: line '.__LINE__.' -->';
			}else{
				if($csvIgnoreNRows>0){
					// skip n-1 rows
					for($i=1; $i<$csvIgnoreNRows; $i++){
						if(!fgets($fp)){
							fclose($fp);
							return str_replace ( '<FILENAME>' , $fn , $Translation['no CSV file data'] ) ;
						}
					}
					echo '<!-- getCSVArray: line '.__LINE__.' -->';
					// read one line
					if(!$arr=fgetcsv($fp, $csvCharsPerLine, $csvFieldSeparator, $csvFieldDelimiter)){
						fclose($fp);
						return str_replace ( '<FILENAME>' , $fn , $Translation['no CSV file data'] ) ;
					}
					if(lineHasFieldNames($arr, $tn)){
						$arrCSVData[0]=arrayResize($arr, count($arrFieldName));
						// skip $start rows
						for($i=0; $i<$start; $i++){
							if(!fgets($fp)){
								fclose($fp);
								return $arrCSVData;
							}
						}
						echo '<!-- getCSVArray: line '.__LINE__.' -->';
					}else{
						// warning! no field names found
						// assume default field order
						$arrCSVData[0]=$arrFieldName;
						// add previously-read line, or ignore it
						if(!$start){
							$arrCSVData[]=arrayResize($arr, count($arrFieldName));
							$numRows--;
							echo '<!-- getCSVArray: line '.__LINE__.' -->';
						}else{
							// skip $start rows
							for($i=0; $i<$start-1; $i++){
								if(!fgets($fp)){
									fclose($fp);
									return $arrCSVData;
								}
							}
							echo '<!-- getCSVArray: line '.__LINE__.' -->';
						}
					}
				}else{
					// warning! no field names found
					// assume default field order
					$arrCSVData[0]=$arrFieldName;
					$arrCSVData[]=arrayResize($arr, count($arrFieldName));
					$numRows--;
					// skip $start rows
					for($i=0; $i<$start; $i++){
						if(!fgets($fp)){
							fclose($fp);
							return $arrCSVData;
						}
					}
					echo '<!-- getCSVArray: line '.__LINE__.' -->';
				}
			}
		}else{
			// skip n+start rows
			for($i=0; $i<$csvIgnoreNRows+$start; $i++){
				if(!fgets($fp)){
					fclose($fp);
					return $arrCSVData;
				}
			}
			echo '<!-- getCSVArray: line '.__LINE__.' -->';
			// assume default field order
			$arrCSVData[0]=$arrFieldName;
		}

		// fetch data
		$i=0;
		while(($arr=fgetcsv($fp, $csvCharsPerLine, $csvFieldSeparator, $csvFieldDelimiter)) && $i<$numRows){
			$arr=arrayResize($arr, count($arrCSVData[0]));
			$arrCSVData[0]=arrayResize($arrCSVData[0], count($arr));
			foreach($arr as $k => $v){
				$arr[$k] = ($makeSafe ? makeSafe($v) : $v);
			}
			$arrCSVData[]=$arr;
			$i++;
		}

		fclose($fp);
		return $arrCSVData;
	}
	##########################################################################
	function lineHasFieldNames($arr, $table){
		global $Translation;

		if(!is_array($arr)){
			#echo '<!-- lineHasFieldNames: line '.__LINE__.' -->';
			return false;
		}

		// get field names of table
		$res=sql('select * from `'.$table.'` limit 1', $eo);
		for($i=0; $i<db_num_fields($res); $i++){
			$arrTableFieldName[]=db_field_name($res, $i);
		}

		$arrCommon=array_intersect($arrTableFieldName, noSpaces($arr));
		//echo '<!-- lineHasFieldNames: arrTableFieldName: '.count($arrTableFieldName).' -->';
		//echo '<!-- lineHasFieldNames: arr: '.count($arr).' -->';
		//echo '<!-- lineHasFieldNames: arrCommon: '.count($arrCommon).' -->';
		return (count($arrCommon) < count($arr) ? false : true);
	}
	##########################################################################
	function noSpaces($arr){
		$cArr=count($arr);
		for($i=0; $i<$cArr; $i++){
			$arr[$i]=str_replace(' ', '', $arr[$i]);
		}
		return $arr;
	}
	##########################################################################
	function arrayResize($arr, $size){
		if(count($arr)<$size){
			return $arr;
		}elseif(count($arr)>$size){
			array_splice($arr, $size);
			return $arr;
		}else{
			return $arr;
		}
	}
	##########################################################################
	function getCSVSettings(&$csvIgnoreNRows, &$csvCharsPerLine, &$csvFieldSeparator, &$csvFieldDelimiter, &$csvFieldNamesOnTop, &$csvUpdateIfPKExists, &$csvBackupBeforeImport){
		if(count($_POST)){
			$csvIgnoreNRows=intval($_POST['csvIgnoreNRows']);
			if($csvIgnoreNRows<0)  $csvIgnoreNRows=0;

			$csvCharsPerLine=intval($_POST['csvCharsPerLine']);
			if($csvCharsPerLine<1000)  $csvCharsPerLine=1000;

			$csvFieldSeparator=(get_magic_quotes_gpc() ? stripslashes($_POST['csvFieldSeparator']) : $_POST['csvFieldSeparator']);
			if($csvFieldSeparator=='') $csvFieldSeparator=',';

			$csvFieldDelimiter=(get_magic_quotes_gpc() ? stripslashes($_POST['csvFieldDelimiter']) : $_POST['csvFieldDelimiter']);
			if($csvFieldDelimiter=='') $csvFieldDelimiter='"';

			$csvFieldNamesOnTop=($_POST['csvFieldNamesOnTop'] ? 1 : 0);
			$csvUpdateIfPKExists=($_POST['csvUpdateIfPKExists'] ? 1 : 0);
			$csvBackupBeforeImport=($_POST['csvBackupBeforeImport'] ? 1 : 0);
		}else{
			$csvIgnoreNRows=0;
			$csvCharsPerLine=10000;
			$csvFieldSeparator=',';
			$csvFieldDelimiter='"';
			$csvFieldNamesOnTop=1;
			$csvUpdateIfPKExists=0;
			$csvBackupBeforeImport=1;
		}
	}
	##########################################################################
	function advancedCSVSettingsForm(){
		global $Translation;

		getCSVSettings($csvIgnoreNRows, $csvCharsPerLine, $csvFieldSeparator, $csvFieldDelimiter, $csvFieldNamesOnTop, $csvUpdateIfPKExists, $csvBackupBeforeImport);
		ob_start();
		?>
		<div style="display: none;" id="advancedOptions">
		<table class="table table-striped">
			<tr>
				<td align="right" class="tdFormCaption" valign="top" width="250">
					<div class="formFieldCaption"><?php echo $Translation['field separator'] ; ?></div>
					</td>
				<td align="left" class="tdFormInput">
					<input type="text" name="csvFieldSeparator" class="formTextBox" value="<?php echo htmlspecialchars($csvFieldSeparator); ?>" size="2"> <i><?php echo $Translation['default comma'] ; ?></i>
					</td>
				</tr>
			<tr>
				<td align="right" class="tdFormCaption" valign="top">
					<div class="formFieldCaption"><?php echo $Translation['field delimiter'] ; ?></div>
					</td>
				<td align="left" class="tdFormInput">
					<input type="text" name="csvFieldDelimiter" class="formTextBox" value="<?php echo htmlspecialchars($csvFieldDelimiter); ?>" size="2"> <i><?php echo $Translation['default double-quote'] ; ?></i>
					</td>
				</tr>
			<tr>
				<td align="right" class="tdFormCaption" valign="top">
					<div class="formFieldCaption"><?php echo $Translation['maximum characters per line'] ; ?></div>
					</td>
				<td align="left" class="tdFormInput">
					<input type="text" name="csvCharsPerLine" class="formTextBox" value="<?php echo intval($csvCharsPerLine); ?>" size="6"> <i><?php echo $Translation['trouble importing CSV'] ; ?></i>
					</td>
				</tr>
			<tr>
				<td align="right" class="tdFormCaption" valign="top">
					<div class="formFieldCaption"><?php echo $Translation['ignore lines number'] ; ?></div>
					</td>
				<td align="left" class="tdFormInput">
					<input type="text" name="csvIgnoreNRows" class="formTextBox" value="<?php echo intval($csvIgnoreNRows); ?>" size="8"> <i><?php echo $Translation['skip lines number'] ; ?></i>
					</td>
				</tr>
			<tr>
				<td align="right" class="tdFormCaption" valign="top">
					<div class="formFieldCaption"><input type="checkbox" id="csvFieldNamesOnTop" name="csvFieldNamesOnTop" value="1" <?php echo ($csvFieldNamesOnTop ? 'checked' : ''); ?>></div>
					</td>
				<td align="left" class="tdFormInput">
					<label for="csvFieldNamesOnTop"><?php echo $Translation['first line field names'] ; ?></label>
					<br><i><?php echo $Translation['field names must match'] ; ?></i>
					</td>
				</tr>
			<tr>
				<td align="right" class="tdFormCaption" valign="top">
					<div class="formFieldCaption"><input type="checkbox" id="csvUpdateIfPKExists" name="csvUpdateIfPKExists" value="1" <?php echo ($csvUpdateIfPKExists ? 'checked' : ''); ?>></div>
					</td>
				<td align="left" class="tdFormInput">
					<label for="csvUpdateIfPKExists"><?php echo $Translation['update table records'] ; ?></label>
					<br><i><?php echo $Translation['ignore CSV table records'] ; ?></i>
					</td>
				</tr>
			<tr>
				<td align="right" class="tdFormCaption" valign="top">
					<div class="formFieldCaption"><input type="checkbox" id="csvBackupBeforeImport" name="csvBackupBeforeImport" value="1" <?php echo ($csvBackupBeforeImport ? 'checked' : ''); ?>></div>
					</td>
				<td align="left" class="tdFormInput">
					<label for="csvBackupBeforeImport"><?php echo $Translation['back up the table'] ; ?></label>
					</td>
				</tr>
			</table>
			</div>
		<?php
		$out=ob_get_contents();
		ob_end_clean();

		return $out;
	}
?>