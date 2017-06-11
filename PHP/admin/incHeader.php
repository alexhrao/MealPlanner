<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
		<meta charset="iso-8859-1">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?php echo $Translation['membership management']; ?></title>

		<link id="browser_favicon" rel="shortcut icon" href="../resources/table_icons/administrator.png">

		<link rel="stylesheet" href="../resources/initializr/css/bootstrap.css">
		<!--[if gt IE 8]><!-->
			<link rel="stylesheet" href="../resources/initializr/css/bootstrap-theme.css">
		<!--<![endif]-->
		<link rel="stylesheet" href="../dynamic.css.php">

		<!--[if lt IE 9]>
			<script src="../resources/initializr/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
		<![endif]-->
		<script src="../resources/jquery/js/jquery-1.11.2.min.js"></script>
		<script>var $j = jQuery.noConflict();</script>
		<script src="toolTips.js"></script>
		<script src="../resources/initializr/js/vendor/bootstrap.min.js"></script>
		<script src="../resources/lightbox/js/prototype.js"></script>
		<script src="../resources/lightbox/js/scriptaculous.js?load=effects"></script>
		<script>

			// VALIDATION FUNCTIONS FOR VARIOUS PAGES

			function jsValidateMember(){
				var p1=document.getElementById('password').value;
				var p2=document.getElementById('confirmPassword').value;
				if(p1=='' || p1==p2){
					return true;
				}else{
					modal_window({message: '<div class="alert alert-danger"><?php echo $Translation['password mismatch'] ; ?></div>', title: "<?php echo $Translation['error'] ; ?>" });
					return false;
				}
			}

			function jsValidateEmail(address){
				var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
				if(reg.test(address) == false){
					modal_window({ message: '<div class="alert alert-danger">'+"<?php echo $Translation['invalid email'];?>"+'</div>', title: "<?php echo $Translation['error'] ; ?>"  });
					return false;
				}else{
					return true;
				}
			}

			function jsShowWait(){
				return window.confirm("<?php echo $Translation['sending mails']; ?>");
			}

			function jsValidateAdminSettings(){
				var p1=document.getElementById('adminPassword').value;
				var p2=document.getElementById('confirmPassword').value;
				if(p1=='' || p1==p2){
					return jsValidateEmail(document.getElementById('senderEmail').value);
				}else{
					modal_window({ message: '<div class="alert alert-error">'+"<?php echo $Translation['password mismatch']; ?>"+'</div>', title: "<?php echo $Translation['error'] ; ?>" });
					return false;
				}
			}

			function jsConfirmTransfer(){
				var confirmMessage;
				var sg=document.getElementById('sourceGroupID').options[document.getElementById('sourceGroupID').selectedIndex].text;
				var sm=document.getElementById('sourceMemberID').value;
				var dg=document.getElementById('destinationGroupID').options[document.getElementById('destinationGroupID').selectedIndex].text;
				if(document.getElementById('destinationMemberID')){
					var dm=document.getElementById('destinationMemberID').value;
				}
				if(document.getElementById('dontMoveMembers')){
					var dmm=document.getElementById('dontMoveMembers').checked;
				}
				if(document.getElementById('moveMembers')){
					var mm=document.getElementById('moveMembers').checked;
				}

				//confirm('sg='+sg+'\n'+'sm='+sm+'\n'+'dg='+dg+'\n'+'dm='+dm+'\n'+'mm='+mm+'\n'+'dmm='+dmm+'\n');

				if(dmm && !dm){
					modal_window({ message: '<div>'+"<?php echo $Translation['complete step 4']; ?>"+'</div>', title: "<?php echo $Translation['info']; ?>", close: function(){ jQuery('#destinationMemberID').focus(); } });
					return false;
				}

				if(mm && sm!='-1'){

					confirmMessage = "<?php echo $Translation['sure move member']; ?>";
					confirmMessage = confirmMessage.replace(/<MEMBER>/, sm).replace(/<OLDGROUP>/, sg).replace(/<NEWGROUP>/, dg);
					return window.confirm(confirmMessage);

				}
				if((dmm || dm) && sm!='-1'){

					confirmMessage = "<?php echo $Translation['sure move data of member']; ?>";
					confirmMessage = confirmMessage.replace(/<OLDMEMBER>/, sm).replace(/<OLDGROUP>/, sg).replace(/<NEWMEMBER>/, dm).replace(/<NEWGROUP>/, dg);                 
					return window.confirm(confirmMessage);
				}

				if(mm){

					confirmMessage = "<?php echo $Translation['sure move all members']; ?>";
					confirmMessage = confirmMessage.replace(/<OLDGROUP>/, sg).replace(/<NEWGROUP>/, dg);
					return window.confirm(confirmMessage);
				}

				if(dmm){


					confirmMessage = "<?php echo $Translation['sure move data of all members']; ?>";
					confirmMessage = confirmMessage.replace(/<OLDGROUP>/, sg).replace(/<MEMBER>/, dm).replace(/<NEWGROUP>/, dg);
					return window.confirm(confirmMessage);
				}
			}

			function showDialog(dialogId){
				$$('.dialog-box').invoke('addClassName', 'hidden-block');
				$(dialogId).removeClassName('hidden-block');
				return false
			};

			function hideDialogs(){
				$$('.dialog-box').invoke('addClassName', 'hidden-block');
				return false
			};


			$j(function(){
				$j('input[type=submit],input[type=button]').each(function(){
					var label = $j(this).val();
					var onclick = $j(this).attr('onclick') || '';
					var name = $j(this).attr('name') || '';
					var type = $j(this).attr('type');

					$j(this).replaceWith('<button class="btn btn-primary" type="' + type + '" onclick="' + onclick + '" name="' + name + '" value="' + label + '">' + label + '</button>');
				});
			});

		</script>

		<link rel="stylesheet" href="adminStyles.css">

		<style>
			.dialog-box{
				background-color: white;
				border: 1px solid silver;
				border-radius: 10px 10px 10px 10px;
				box-shadow: 0 3px 100px silver;
				left: 30%;
				padding: 10px;
				position: absolute;
				top: 20%;
				width: 40%;
			}
			.hidden-block{
				display: none;
			}
		</style>
	</head>
	<body>
	<div class="container">

		<!-- top navbar -->
		<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
					<span class="sr-only"><?php echo $Translation['toggle navigation'];?></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="pageHome.php"><span class="text-warning"><i class="glyphicon glyphicon-cog"></i> <?php echo $Translation['admin area']; ?></span></a>
			</div>

			<div class="collapse navbar-collapse navbar-ex1-collapse">
				<ul class="nav navbar-nav">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-globe"></i> <?php echo $Translation['groups']; ?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="pageViewGroups.php"><?php echo $Translation['view groups']; ?></a></li>
							<li><a href="pageEditGroup.php"><?php echo   $Translation['add group']  ; ?></a></li>
							<li class="divider"></li>
							<li><a href="pageEditGroup.php?groupID=<?php echo sqlValue("select groupID from membership_groups where name='" . makeSafe($adminConfig['anonymousGroup']) . "'"); ?>"><?php echo  $Translation['edit anonymous permissions'] ; ?></a></li>
						</ul>
					</li>

					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-user"></i> <?php echo $Translation['members']  ;?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="pageViewMembers.php"><?php echo $Translation['view members'] ; ?></a></li>
							<li><a href="pageEditMember.php"><?php echo $Translation['add member']  ; ?></a></li>
							<li class="divider"></li>
							<li><a href="pageViewRecords.php"><?php echo $Translation["view members' records"]; ?> </a></li>
						</ul>
					</li>

					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-cog"></i> <?php echo $Translation["utilities"] ; ?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="pageSettings.php"><?php echo $Translation["admin settings"]  ; ?></a></li>
							<li class="divider"></li>
							<li><a href="pageRebuildThumbnails.php"><?php echo  $Translation["rebuild thumbnails"]  ; ?></a></li>
							<li><a href="pageRebuildFields.php"><?php echo  $Translation['rebuild fields'] ; ?></a></li>
							<li><a href="pageUploadCSV.php"><?php echo $Translation['import CSV'] ; ?></a></li>
							<li><a href="pageTransferOwnership.php"><?php echo $Translation['batch transfer'] ; ?></a></li>
							<li><a href="pageMail.php?sendToAll=1"><?php echo $Translation['mail all users'] ; ?></a></li>
							<li class="divider"></li>
							<li><a href="http://forums.appgini.com" target="_blank"><i class="glyphicon glyphicon-new-window"></i> <?php echo $Translation['AppGini forum']; ?></a></li>
						</ul>
					</li>

					<?php $plugins = get_plugins(); ?>

					<?php if(count($plugins)){ ?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-plus"></i> <?php echo $Translation["plugins"] ; ?> <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<?php foreach($plugins as $plugin){ ?>
									<?php
										$plugin_icon = '';
										if($plugin['glyphicon']) $plugin_icon = "<i class=\"glyphicon glyphicon-{$plugin['glyphicon']}\"></i> ";
										if($plugin['icon']) $plugin_icon = "<img src=\"{$plugin['admin_path']}/{$plugin['icon']}\"> ";
									?>
									<li><a target="_blank" href="<?php echo $plugin['admin_path']; ?>"><?php echo $plugin_icon . $plugin['title']; ?></a></li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
				</ul>

				<div class="navbar-right">
					<a href="../index.php" class="btn btn-success navbar-btn"><?php echo $Translation["user's area"] ; ?></a>
					<a href="pageHome.php?signOut=1" class="btn btn-warning navbar-btn"><i class="glyphicon glyphicon-log-out"></i> <?php echo $Translation["sign out"] ; ?></a>
				</div>
			</div>
		</nav>

		<div style="height: 80px;"></div>

		<!-- tool tips support -->
		<div id="TipLayer" style="visibility:hidden;position:absolute;z-index:1000;top:-100"></div>
		<script src="toolTipData.js"></script>
		<!-- /tool tips support -->

<?php
	if(!strstr($_SERVER['PHP_SELF'], 'pageSettings.php') && $adminConfig['adminPassword'] == md5('admin')){
		$noSignup=TRUE;
		?>
		<div class="alert alert-danger">
			<p><strong><?php echo $Translation["attention"] ; ?></strong></p>
			<p><?php if($adminConfig['adminUsername'] == 'admin'){
					echo $Translation['security risk admin'];
			}else{
					echo $Translation['security risk'];
			} ?></p>
		</div>
	<?php  } ?>

