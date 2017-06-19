<?php

// Data functions (insert, update, delete, form) for table ingredientstores

// This script and data application were generated by AppGini 5.62
// Download AppGini for free from https://bigprof.com/appgini/download/

function ingredientstores_insert(){
	global $Translation;

	// mm: can member insert record?
	$arrPerm=getTablePermissions('ingredientstores');
	if(!$arrPerm[1]){
		return false;
	}

	$data['IngredientID'] = makeSafe($_REQUEST['IngredientID']);
		if($data['IngredientID'] == empty_lookup_value){ $data['IngredientID'] = ''; }
	$data['StoreID'] = makeSafe($_REQUEST['StoreID']);
		if($data['StoreID'] == empty_lookup_value){ $data['StoreID'] = ''; }
	$data['Cost'] = makeSafe($_REQUEST['Cost']);
		if($data['Cost'] == empty_lookup_value){ $data['Cost'] = ''; }
	if($data['IngredientID']== ''){
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">" . $Translation['error:'] . " 'Ingredient': " . $Translation['field not null'] . '<br><br>';
		echo '<a href="" onclick="history.go(-1); return false;">'.$Translation['< back'].'</a></div>';
		exit;
	}
	if($data['StoreID']== ''){
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">" . $Translation['error:'] . " 'Store': " . $Translation['field not null'] . '<br><br>';
		echo '<a href="" onclick="history.go(-1); return false;">'.$Translation['< back'].'</a></div>';
		exit;
	}

	// hook: ingredientstores_before_insert
	if(function_exists('ingredientstores_before_insert')){
		$args=array();
		if(!ingredientstores_before_insert($data, getMemberInfo(), $args)){ return false; }
	}

	$o = array('silentErrors' => true);
	sql('insert into `ingredientstores` set       `IngredientID`=' . (($data['IngredientID'] !== '' && $data['IngredientID'] !== NULL) ? "'{$data['IngredientID']}'" : 'NULL') . ', `StoreID`=' . (($data['StoreID'] !== '' && $data['StoreID'] !== NULL) ? "'{$data['StoreID']}'" : 'NULL') . ', `Cost`=' . (($data['Cost'] !== '' && $data['Cost'] !== NULL) ? "'{$data['Cost']}'" : 'NULL'), $o);
	if($o['error']!=''){
		echo $o['error'];
		echo "<a href=\"ingredientstores_view.php?addNew_x=1\">{$Translation['< back']}</a>";
		exit;
	}

	$recID = db_insert_id(db_link());

	// hook: ingredientstores_after_insert
	if(function_exists('ingredientstores_after_insert')){
		$res = sql("select * from `ingredientstores` where `IngredientStoreID`='" . makeSafe($recID, false) . "' limit 1", $eo);
		if($row = db_fetch_assoc($res)){
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = makeSafe($recID, false);
		$args=array();
		if(!ingredientstores_after_insert($data, getMemberInfo(), $args)){ return $recID; }
	}

	// mm: save ownership data
	sql("insert ignore into membership_userrecords set tableName='ingredientstores', pkValue='" . makeSafe($recID, false) . "', memberID='" . makeSafe(getLoggedMemberID(), false) . "', dateAdded='" . time() . "', dateUpdated='" . time() . "', groupID='" . getLoggedGroupID() . "'", $eo);

	return $recID;
}

function ingredientstores_delete($selected_id, $AllowDeleteOfParents=false, $skipChecks=false){
	// insure referential integrity ...
	global $Translation;
	$selected_id=makeSafe($selected_id);

	// mm: can member delete record?
	$arrPerm=getTablePermissions('ingredientstores');
	$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='ingredientstores' and pkValue='$selected_id'");
	$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='ingredientstores' and pkValue='$selected_id'");
	if(($arrPerm[4]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[4]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[4]==3){ // allow delete?
		// delete allowed, so continue ...
	}else{
		return $Translation['You don\'t have enough permissions to delete this record'];
	}

	// hook: ingredientstores_before_delete
	if(function_exists('ingredientstores_before_delete')){
		$args=array();
		if(!ingredientstores_before_delete($selected_id, $skipChecks, getMemberInfo(), $args))
			return $Translation['Couldn\'t delete this record'];
	}

	sql("delete from `ingredientstores` where `IngredientStoreID`='$selected_id'", $eo);

	// hook: ingredientstores_after_delete
	if(function_exists('ingredientstores_after_delete')){
		$args=array();
		ingredientstores_after_delete($selected_id, getMemberInfo(), $args);
	}

	// mm: delete ownership data
	sql("delete from membership_userrecords where tableName='ingredientstores' and pkValue='$selected_id'", $eo);
}

function ingredientstores_update($selected_id){
	global $Translation;

	// mm: can member edit record?
	$arrPerm=getTablePermissions('ingredientstores');
	$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='ingredientstores' and pkValue='".makeSafe($selected_id)."'");
	$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='ingredientstores' and pkValue='".makeSafe($selected_id)."'");
	if(($arrPerm[3]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[3]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[3]==3){ // allow update?
		// update allowed, so continue ...
	}else{
		return false;
	}

	$data['IngredientID'] = makeSafe($_REQUEST['IngredientID']);
		if($data['IngredientID'] == empty_lookup_value){ $data['IngredientID'] = ''; }
	if($data['IngredientID']==''){
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Ingredient': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">'.$Translation['< back'].'</a></div>';
		exit;
	}
	$data['StoreID'] = makeSafe($_REQUEST['StoreID']);
		if($data['StoreID'] == empty_lookup_value){ $data['StoreID'] = ''; }
	if($data['StoreID']==''){
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Store': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">'.$Translation['< back'].'</a></div>';
		exit;
	}
	$data['Cost'] = makeSafe($_REQUEST['Cost']);
		if($data['Cost'] == empty_lookup_value){ $data['Cost'] = ''; }
	$data['selectedID']=makeSafe($selected_id);

	// hook: ingredientstores_before_update
	if(function_exists('ingredientstores_before_update')){
		$args=array();
		if(!ingredientstores_before_update($data, getMemberInfo(), $args)){ return false; }
	}

	$o=array('silentErrors' => true);
	sql('update `ingredientstores` set       `IngredientID`=' . (($data['IngredientID'] !== '' && $data['IngredientID'] !== NULL) ? "'{$data['IngredientID']}'" : 'NULL') . ', `StoreID`=' . (($data['StoreID'] !== '' && $data['StoreID'] !== NULL) ? "'{$data['StoreID']}'" : 'NULL') . ', `Cost`=' . (($data['Cost'] !== '' && $data['Cost'] !== NULL) ? "'{$data['Cost']}'" : 'NULL') . " where `IngredientStoreID`='".makeSafe($selected_id)."'", $o);
	if($o['error']!=''){
		echo $o['error'];
		echo '<a href="ingredientstores_view.php?SelectedID='.urlencode($selected_id)."\">{$Translation['< back']}</a>";
		exit;
	}


	// hook: ingredientstores_after_update
	if(function_exists('ingredientstores_after_update')){
		$res = sql("SELECT * FROM `ingredientstores` WHERE `IngredientStoreID`='{$data['selectedID']}' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)){
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = $data['IngredientStoreID'];
		$args = array();
		if(!ingredientstores_after_update($data, getMemberInfo(), $args)){ return; }
	}

	// mm: update ownership data
	sql("update membership_userrecords set dateUpdated='".time()."' where tableName='ingredientstores' and pkValue='".makeSafe($selected_id)."'", $eo);

}

function ingredientstores_form($selected_id = '', $AllowUpdate = 1, $AllowInsert = 1, $AllowDelete = 1, $ShowCancel = 0, $TemplateDV = '', $TemplateDVP = ''){
	// function to return an editable form for a table records
	// and fill it with data of record whose ID is $selected_id. If $selected_id
	// is empty, an empty form is shown, with only an 'Add New'
	// button displayed.

	global $Translation;

	// mm: get table permissions
	$arrPerm=getTablePermissions('ingredientstores');
	if(!$arrPerm[1] && $selected_id==''){ return ''; }
	$AllowInsert = ($arrPerm[1] ? true : false);
	// print preview?
	$dvprint = false;
	if($selected_id && $_REQUEST['dvprint_x'] != ''){
		$dvprint = true;
	}

	$filterer_IngredientID = thisOr(undo_magic_quotes($_REQUEST['filterer_IngredientID']), '');
	$filterer_StoreID = thisOr(undo_magic_quotes($_REQUEST['filterer_StoreID']), '');

	// populate filterers, starting from children to grand-parents

	// unique random identifier
	$rnd1 = ($dvprint ? rand(1000000, 9999999) : '');
	// combobox: IngredientID
	$combo_IngredientID = new DataCombo;
	// combobox: StoreID
	$combo_StoreID = new DataCombo;

	if($selected_id){
		// mm: check member permissions
		if(!$arrPerm[2]){
			return "";
		}
		// mm: who is the owner?
		$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='ingredientstores' and pkValue='".makeSafe($selected_id)."'");
		$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='ingredientstores' and pkValue='".makeSafe($selected_id)."'");
		if($arrPerm[2]==1 && getLoggedMemberID()!=$ownerMemberID){
			return "";
		}
		if($arrPerm[2]==2 && getLoggedGroupID()!=$ownerGroupID){
			return "";
		}

		// can edit?
		if(($arrPerm[3]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[3]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[3]==3){
			$AllowUpdate=1;
		}else{
			$AllowUpdate=0;
		}

		$res = sql("select * from `ingredientstores` where `IngredientStoreID`='".makeSafe($selected_id)."'", $eo);
		if(!($row = db_fetch_array($res))){
			return error_message($Translation['No records found'], 'ingredientstores_view.php', false);
		}
		$urow = $row; /* unsanitized data */
		$hc = new CI_Input();
		$row = $hc->xss_clean($row); /* sanitize data */
		$combo_IngredientID->SelectedData = $row['IngredientID'];
		$combo_StoreID->SelectedData = $row['StoreID'];
	}else{
		$combo_IngredientID->SelectedData = $filterer_IngredientID;
		$combo_StoreID->SelectedData = $filterer_StoreID;
	}
	$combo_IngredientID->HTML = '<span id="IngredientID-container' . $rnd1 . '"></span><input type="hidden" name="IngredientID" id="IngredientID' . $rnd1 . '" value="' . html_attr($combo_IngredientID->SelectedData) . '">';
	$combo_IngredientID->MatchText = '<span id="IngredientID-container-readonly' . $rnd1 . '"></span><input type="hidden" name="IngredientID" id="IngredientID' . $rnd1 . '" value="' . html_attr($combo_IngredientID->SelectedData) . '">';
	$combo_StoreID->HTML = '<span id="StoreID-container' . $rnd1 . '"></span><input type="hidden" name="StoreID" id="StoreID' . $rnd1 . '" value="' . html_attr($combo_StoreID->SelectedData) . '">';
	$combo_StoreID->MatchText = '<span id="StoreID-container-readonly' . $rnd1 . '"></span><input type="hidden" name="StoreID" id="StoreID' . $rnd1 . '" value="' . html_attr($combo_StoreID->SelectedData) . '">';

	ob_start();
	?>

	<script>
		// initial lookup values
		AppGini.current_IngredientID__RAND__ = { text: "", value: "<?php echo addslashes($selected_id ? $urow['IngredientID'] : $filterer_IngredientID); ?>"};
		AppGini.current_StoreID__RAND__ = { text: "", value: "<?php echo addslashes($selected_id ? $urow['StoreID'] : $filterer_StoreID); ?>"};

		jQuery(function() {
			setTimeout(function(){
				if(typeof(IngredientID_reload__RAND__) == 'function') IngredientID_reload__RAND__();
				if(typeof(StoreID_reload__RAND__) == 'function') StoreID_reload__RAND__();
			}, 10); /* we need to slightly delay client-side execution of the above code to allow AppGini.ajaxCache to work */
		});
		function IngredientID_reload__RAND__(){
		<?php if(($AllowUpdate || $AllowInsert) && !$dvprint){ ?>

			$j("#IngredientID-container__RAND__").select2({
				/* initial default value */
				initSelection: function(e, c){
					$j.ajax({
						url: 'ajax_combo.php',
						dataType: 'json',
						data: { id: AppGini.current_IngredientID__RAND__.value, t: 'ingredientstores', f: 'IngredientID' },
						success: function(resp){
							c({
								id: resp.results[0].id,
								text: resp.results[0].text
							});
							$j('[name="IngredientID"]').val(resp.results[0].id);
							$j('[id=IngredientID-container-readonly__RAND__]').html('<span id="IngredientID-match-text">' + resp.results[0].text + '</span>');
							if(resp.results[0].id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=ingredients_view_parent]').hide(); }else{ $j('.btn[id=ingredients_view_parent]').show(); }


							if(typeof(IngredientID_update_autofills__RAND__) == 'function') IngredientID_update_autofills__RAND__();
						}
					});
				},
				width: ($j('fieldset .col-xs-11').width() - select2_max_width_decrement()) + 'px',
				formatNoMatches: function(term){ return '<?php echo addslashes($Translation['No matches found!']); ?>'; },
				minimumResultsForSearch: 10,
				loadMorePadding: 200,
				ajax: {
					url: 'ajax_combo.php',
					dataType: 'json',
					cache: true,
					data: function(term, page){ return { s: term, p: page, t: 'ingredientstores', f: 'IngredientID' }; },
					results: function(resp, page){ return resp; }
				},
				escapeMarkup: function(str){ return str; }
			}).on('change', function(e){
				AppGini.current_IngredientID__RAND__.value = e.added.id;
				AppGini.current_IngredientID__RAND__.text = e.added.text;
				$j('[name="IngredientID"]').val(e.added.id);
				if(e.added.id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=ingredients_view_parent]').hide(); }else{ $j('.btn[id=ingredients_view_parent]').show(); }


				if(typeof(IngredientID_update_autofills__RAND__) == 'function') IngredientID_update_autofills__RAND__();
			});

			if(!$j("#IngredientID-container__RAND__").length){
				$j.ajax({
					url: 'ajax_combo.php',
					dataType: 'json',
					data: { id: AppGini.current_IngredientID__RAND__.value, t: 'ingredientstores', f: 'IngredientID' },
					success: function(resp){
						$j('[name="IngredientID"]').val(resp.results[0].id);
						$j('[id=IngredientID-container-readonly__RAND__]').html('<span id="IngredientID-match-text">' + resp.results[0].text + '</span>');
						if(resp.results[0].id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=ingredients_view_parent]').hide(); }else{ $j('.btn[id=ingredients_view_parent]').show(); }

						if(typeof(IngredientID_update_autofills__RAND__) == 'function') IngredientID_update_autofills__RAND__();
					}
				});
			}

		<?php }else{ ?>

			$j.ajax({
				url: 'ajax_combo.php',
				dataType: 'json',
				data: { id: AppGini.current_IngredientID__RAND__.value, t: 'ingredientstores', f: 'IngredientID' },
				success: function(resp){
					$j('[id=IngredientID-container__RAND__], [id=IngredientID-container-readonly__RAND__]').html('<span id="IngredientID-match-text">' + resp.results[0].text + '</span>');
					if(resp.results[0].id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=ingredients_view_parent]').hide(); }else{ $j('.btn[id=ingredients_view_parent]').show(); }

					if(typeof(IngredientID_update_autofills__RAND__) == 'function') IngredientID_update_autofills__RAND__();
				}
			});
		<?php } ?>

		}
		function StoreID_reload__RAND__(){
		<?php if(($AllowUpdate || $AllowInsert) && !$dvprint){ ?>

			$j("#StoreID-container__RAND__").select2({
				/* initial default value */
				initSelection: function(e, c){
					$j.ajax({
						url: 'ajax_combo.php',
						dataType: 'json',
						data: { id: AppGini.current_StoreID__RAND__.value, t: 'ingredientstores', f: 'StoreID' },
						success: function(resp){
							c({
								id: resp.results[0].id,
								text: resp.results[0].text
							});
							$j('[name="StoreID"]').val(resp.results[0].id);
							$j('[id=StoreID-container-readonly__RAND__]').html('<span id="StoreID-match-text">' + resp.results[0].text + '</span>');
							if(resp.results[0].id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=stores_view_parent]').hide(); }else{ $j('.btn[id=stores_view_parent]').show(); }


							if(typeof(StoreID_update_autofills__RAND__) == 'function') StoreID_update_autofills__RAND__();
						}
					});
				},
				width: ($j('fieldset .col-xs-11').width() - select2_max_width_decrement()) + 'px',
				formatNoMatches: function(term){ return '<?php echo addslashes($Translation['No matches found!']); ?>'; },
				minimumResultsForSearch: 10,
				loadMorePadding: 200,
				ajax: {
					url: 'ajax_combo.php',
					dataType: 'json',
					cache: true,
					data: function(term, page){ return { s: term, p: page, t: 'ingredientstores', f: 'StoreID' }; },
					results: function(resp, page){ return resp; }
				},
				escapeMarkup: function(str){ return str; }
			}).on('change', function(e){
				AppGini.current_StoreID__RAND__.value = e.added.id;
				AppGini.current_StoreID__RAND__.text = e.added.text;
				$j('[name="StoreID"]').val(e.added.id);
				if(e.added.id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=stores_view_parent]').hide(); }else{ $j('.btn[id=stores_view_parent]').show(); }


				if(typeof(StoreID_update_autofills__RAND__) == 'function') StoreID_update_autofills__RAND__();
			});

			if(!$j("#StoreID-container__RAND__").length){
				$j.ajax({
					url: 'ajax_combo.php',
					dataType: 'json',
					data: { id: AppGini.current_StoreID__RAND__.value, t: 'ingredientstores', f: 'StoreID' },
					success: function(resp){
						$j('[name="StoreID"]').val(resp.results[0].id);
						$j('[id=StoreID-container-readonly__RAND__]').html('<span id="StoreID-match-text">' + resp.results[0].text + '</span>');
						if(resp.results[0].id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=stores_view_parent]').hide(); }else{ $j('.btn[id=stores_view_parent]').show(); }

						if(typeof(StoreID_update_autofills__RAND__) == 'function') StoreID_update_autofills__RAND__();
					}
				});
			}

		<?php }else{ ?>

			$j.ajax({
				url: 'ajax_combo.php',
				dataType: 'json',
				data: { id: AppGini.current_StoreID__RAND__.value, t: 'ingredientstores', f: 'StoreID' },
				success: function(resp){
					$j('[id=StoreID-container__RAND__], [id=StoreID-container-readonly__RAND__]').html('<span id="StoreID-match-text">' + resp.results[0].text + '</span>');
					if(resp.results[0].id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=stores_view_parent]').hide(); }else{ $j('.btn[id=stores_view_parent]').show(); }

					if(typeof(StoreID_update_autofills__RAND__) == 'function') StoreID_update_autofills__RAND__();
				}
			});
		<?php } ?>

		}
	</script>
	<?php

	$lookups = str_replace('__RAND__', $rnd1, ob_get_contents());
	ob_end_clean();


	// code for template based detail view forms

	// open the detail view template
	if($dvprint){
		$template_file = is_file("./{$TemplateDVP}") ? "./{$TemplateDVP}" : './templates/ingredientstores_templateDVP.html';
		$templateCode = @file_get_contents($template_file);
	}else{
		$template_file = is_file("./{$TemplateDV}") ? "./{$TemplateDV}" : './templates/ingredientstores_templateDV.html';
		$templateCode = @file_get_contents($template_file);
	}

	// process form title
	$templateCode = str_replace('<%%DETAIL_VIEW_TITLE%%>', 'Detail View', $templateCode);
	$templateCode = str_replace('<%%RND1%%>', $rnd1, $templateCode);
	$templateCode = str_replace('<%%EMBEDDED%%>', ($_REQUEST['Embedded'] ? 'Embedded=1' : ''), $templateCode);
	// process buttons
	if($AllowInsert){
		if(!$selected_id) $templateCode=str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-success" id="insert" name="insert_x" value="1" onclick="return ingredientstores_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save New'] . '</button>', $templateCode);
		$templateCode=str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="insert" name="insert_x" value="1" onclick="return ingredientstores_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save As Copy'] . '</button>', $templateCode);
	}else{
		$templateCode=str_replace('<%%INSERT_BUTTON%%>', '', $templateCode);
	}

	// 'Back' button action
	if($_REQUEST['Embedded']){
		$backAction = 'window.parent.jQuery(\'.modal\').modal(\'hide\'); return false;';
	}else{
		$backAction = '$$(\'form\')[0].writeAttribute(\'novalidate\', \'novalidate\'); document.myform.reset(); return true;';
	}

	if($selected_id){
		if(!$_REQUEST['Embedded']) $templateCode=str_replace('<%%DVPRINT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="dvprint" name="dvprint_x" value="1" onclick="$$(\'form\')[0].writeAttribute(\'novalidate\', \'novalidate\'); document.myform.reset(); return true;" title="' . html_attr($Translation['Print Preview']) . '"><i class="glyphicon glyphicon-print"></i> ' . $Translation['Print Preview'] . '</button>', $templateCode);
		if($AllowUpdate){
			$templateCode=str_replace('<%%UPDATE_BUTTON%%>', '<button type="submit" class="btn btn-success btn-lg" id="update" name="update_x" value="1" onclick="return ingredientstores_validateData();" title="' . html_attr($Translation['Save Changes']) . '"><i class="glyphicon glyphicon-ok"></i> ' . $Translation['Save Changes'] . '</button>', $templateCode);
		}else{
			$templateCode=str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);
		}
		if(($arrPerm[4]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[4]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[4]==3){ // allow delete?
			$templateCode=str_replace('<%%DELETE_BUTTON%%>', '<button type="submit" class="btn btn-danger" id="delete" name="delete_x" value="1" onclick="return confirm(\'' . $Translation['are you sure?'] . '\');" title="' . html_attr($Translation['Delete']) . '"><i class="glyphicon glyphicon-trash"></i> ' . $Translation['Delete'] . '</button>', $templateCode);
		}else{
			$templateCode=str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);
		}
		$templateCode=str_replace('<%%DESELECT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="deselect" name="deselect_x" value="1" onclick="' . $backAction . '" title="' . html_attr($Translation['Back']) . '"><i class="glyphicon glyphicon-chevron-left"></i> ' . $Translation['Back'] . '</button>', $templateCode);
	}else{
		$templateCode=str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);
		$templateCode=str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);
		$templateCode=str_replace('<%%DESELECT_BUTTON%%>', ($ShowCancel ? '<button type="submit" class="btn btn-default" id="deselect" name="deselect_x" value="1" onclick="' . $backAction . '" title="' . html_attr($Translation['Back']) . '"><i class="glyphicon glyphicon-chevron-left"></i> ' . $Translation['Back'] . '</button>' : ''), $templateCode);
	}

	// set records to read only if user can't insert new records and can't edit current record
	if(($selected_id && !$AllowUpdate && !$AllowInsert) || (!$selected_id && !$AllowInsert)){
		$jsReadOnly .= "\tjQuery('#IngredientID').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('#IngredientID_caption').prop('disabled', true).css({ color: '#555', backgroundColor: 'white' });\n";
		$jsReadOnly .= "\tjQuery('#StoreID').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('#StoreID_caption').prop('disabled', true).css({ color: '#555', backgroundColor: 'white' });\n";
		$jsReadOnly .= "\tjQuery('#Cost').replaceWith('<div class=\"form-control-static\" id=\"Cost\">' + (jQuery('#Cost').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('.select2-container').hide();\n";

		$noUploads = true;
	}elseif($AllowInsert){
		$jsEditable .= "\tjQuery('form').eq(0).data('already_changed', true);"; // temporarily disable form change handler
			$jsEditable .= "\tjQuery('form').eq(0).data('already_changed', false);"; // re-enable form change handler
	}

	// process combos
	$templateCode=str_replace('<%%COMBO(IngredientID)%%>', $combo_IngredientID->HTML, $templateCode);
	$templateCode=str_replace('<%%COMBOTEXT(IngredientID)%%>', $combo_IngredientID->MatchText, $templateCode);
	$templateCode=str_replace('<%%URLCOMBOTEXT(IngredientID)%%>', urlencode($combo_IngredientID->MatchText), $templateCode);
	$templateCode=str_replace('<%%COMBO(StoreID)%%>', $combo_StoreID->HTML, $templateCode);
	$templateCode=str_replace('<%%COMBOTEXT(StoreID)%%>', $combo_StoreID->MatchText, $templateCode);
	$templateCode=str_replace('<%%URLCOMBOTEXT(StoreID)%%>', urlencode($combo_StoreID->MatchText), $templateCode);

	/* lookup fields array: 'lookup field name' => array('parent table name', 'lookup field caption') */
	$lookup_fields = array(  'IngredientID' => array('ingredients', 'Ingredient'), 'StoreID' => array('stores', 'Store'));
	foreach($lookup_fields as $luf => $ptfc){
		$pt_perm = getTablePermissions($ptfc[0]);

		// process foreign key links
		if($pt_perm['view'] || $pt_perm['edit']){
			$templateCode = str_replace("<%%PLINK({$luf})%%>", '<button type="button" class="btn btn-default view_parent hspacer-md" id="' . $ptfc[0] . '_view_parent" title="' . html_attr($Translation['View'] . ' ' . $ptfc[1]) . '"><i class="glyphicon glyphicon-eye-open"></i></button>', $templateCode);
		}

		// if user has insert permission to parent table of a lookup field, put an add new button
		if($pt_perm['insert'] && !$_REQUEST['Embedded']){
			$templateCode = str_replace("<%%ADDNEW({$ptfc[0]})%%>", '<button type="button" class="btn btn-success add_new_parent hspacer-md" id="' . $ptfc[0] . '_add_new" title="' . html_attr($Translation['Add New'] . ' ' . $ptfc[1]) . '"><i class="glyphicon glyphicon-plus-sign"></i></button>', $templateCode);
		}
	}

	// process images
	$templateCode=str_replace('<%%UPLOADFILE(IngredientStoreID)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(IngredientID)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(StoreID)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(Cost)%%>', '', $templateCode);

	// process values
	if($selected_id){
		$templateCode=str_replace('<%%VALUE(IngredientStoreID)%%>', html_attr($row['IngredientStoreID']), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(IngredientStoreID)%%>', urlencode($urow['IngredientStoreID']), $templateCode);
		$templateCode=str_replace('<%%VALUE(IngredientID)%%>', html_attr($row['IngredientID']), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(IngredientID)%%>', urlencode($urow['IngredientID']), $templateCode);
		$templateCode=str_replace('<%%VALUE(StoreID)%%>', html_attr($row['StoreID']), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(StoreID)%%>', urlencode($urow['StoreID']), $templateCode);
		$templateCode=str_replace('<%%VALUE(Cost)%%>', html_attr($row['Cost']), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(Cost)%%>', urlencode($urow['Cost']), $templateCode);
	}else{
		$templateCode=str_replace('<%%VALUE(IngredientStoreID)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(IngredientStoreID)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(IngredientID)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(IngredientID)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(StoreID)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(StoreID)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(Cost)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(Cost)%%>', urlencode(''), $templateCode);
	}

	// process translations
	foreach($Translation as $symbol=>$trans){
		$templateCode=str_replace("<%%TRANSLATION($symbol)%%>", $trans, $templateCode);
	}

	// clear scrap
	$templateCode=str_replace('<%%', '<!-- ', $templateCode);
	$templateCode=str_replace('%%>', ' -->', $templateCode);

	// hide links to inaccessible tables
	if($_REQUEST['dvprint_x'] == ''){
		$templateCode .= "\n\n<script>\$j(function(){\n";
		$arrTables = getTableList();
		foreach($arrTables as $name => $caption){
			$templateCode .= "\t\$j('#{$name}_link').removeClass('hidden');\n";
			$templateCode .= "\t\$j('#xs_{$name}_link').removeClass('hidden');\n";
		}

		$templateCode .= $jsReadOnly;
		$templateCode .= $jsEditable;

		if(!$selected_id){
		}

		$templateCode.="\n});</script>\n";
	}

	// ajaxed auto-fill fields
	$templateCode .= '<script>';
	$templateCode .= '$j(function() {';


	$templateCode.="});";
	$templateCode.="</script>";
	$templateCode .= $lookups;

	// handle enforced parent values for read-only lookup fields

	// don't include blank images in lightbox gallery
	$templateCode = preg_replace('/blank.gif" data-lightbox=".*?"/', 'blank.gif"', $templateCode);

	// don't display empty email links
	$templateCode=preg_replace('/<a .*?href="mailto:".*?<\/a>/', '', $templateCode);

	/* default field values */
	$rdata = $jdata = get_defaults('ingredientstores');
	if($selected_id){
		$jdata = get_joined_record('ingredientstores', $selected_id);
		$rdata = $row;
	}
	$cache_data = array(
		'rdata' => array_map('nl2br', array_map('addslashes', $rdata)),
		'jdata' => array_map('nl2br', array_map('addslashes', $jdata)),
	);
	$templateCode .= loadView('ingredientstores-ajax-cache', $cache_data);

	// hook: ingredientstores_dv
	if(function_exists('ingredientstores_dv')){
		$args=array();
		ingredientstores_dv(($selected_id ? $selected_id : FALSE), getMemberInfo(), $templateCode, $args);
	}

	return $templateCode;
}
?>