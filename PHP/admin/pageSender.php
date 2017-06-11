<?php
	$currDir = dirname(__FILE__);
	require("{$currDir}/incCommon.php");
	include("{$currDir}/incHeader.php");
	$mailsPerBatch = 5;

	$queue = $_GET['queue'];
	if(!preg_match('/^[a-f0-9]{32}$/i', $queue)){
		echo "<div class=\"status\">{$Translation['invalid mail queue']}</div>";
		include("{$currDir}/incFooter.php");
	}

	$queueFile="{$currDir}/{$queue}.php";
	if(!is_file($queueFile)){
		echo "<div class=\"status\">{$Translation['invalid mail queue']}</div>";
		include("{$currDir}/incFooter.php");
	}

	include($queueFile);
	$fLog=@fopen("{$currDir}/mailLog.log", "a");
	// send a batch of up to $mailsPerBatch messages
	$i=0;
	foreach($to as $email){
		$i++;
		if(!@mail($email, $mailSubject, $mailMessage, "From: ".$adminConfig['senderName']." <".$adminConfig['senderEmail'].">")){
			@fwrite($fLog, @date("d.m.Y H:i:s").str_replace ( "<EMAIL>" , $email , $Translation['sending message failed'] )."\n");
		}else{
			@fwrite($fLog, @date("d.m.Y H:i:s").str_replace ( "<EMAIL>" , $email , $Translation['sending message ok'] )."\n");
		}
		if($i>=$mailsPerBatch){  break; }
	}
	@fclose($fLog);

	if($i<$mailsPerBatch){
		// no more emails in queue
		@unlink($queueFile);
		?>
		<div class="page-header"><h1><?php echo  $Translation['done!'] ; ?></h1></div><?php echo  $Translation['close page'] ; ?>
		<br><br><pre style="text-align: left;"><?php echo "{$Translation['mail log']}\n" . @file_get_contents("{$currDir}/mailLog.log"); ?></pre>
		<?php
		@unlink("{$currDir}/mailLog.log");
		include("{$currDir}/incFooter.php");
	}else{
		while($i--){ array_shift($to); }

		if(!$fp=fopen($queueFile, "w")){
			?>
			<div class="alert alert-danger">
				<?php echo str_replace ( "<CURRDIR>" , $currDir , $Translation["mail queue not saved"] ); ?>
			</div>
			<?php
			include("{$currDir}/incFooter.php");
		}else{
			fwrite($fp, "<?php\n");
			foreach($to as $recip){
				fwrite($fp, "\t\$to[]='{$recip}';\n");
			}
			$mailSubject = addslashes(stripslashes($mailSubject));
			$mailMessage = addslashes(stripslashes($mailMessage));
			$mailMessage = str_replace("\n", "\\n", $mailMessage);
			$mailMessage = str_replace("\r", "\\r", $mailMessage);
			fwrite($fp, "\t\$mailSubject=\"{$mailSubject}\";\n");
			fwrite($fp, "\t\$mailMessage=\"{$mailMessage}\";\n");
			fwrite($fp, "?>");
			fclose($fp);
		}

		// redirect to mail queue processor
		redirect("admin/pageSender.php?queue={$queue}");
	}

	include("{$currDir}/incFooter.php");
?>
