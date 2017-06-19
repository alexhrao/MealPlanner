<?php

// Data functions (insert, update, delete, form) for table mealrecipes

// This script and data application were generated by AppGini 5.62
// Download AppGini for free from https://bigprof.com/appgini/download/

function mealrecipes_insert(){
	global $Translation;

	// mm: can member insert record?
	$arrPerm=getTablePermissions('mealrecipes');
	if(!$arrPerm[1]){
		return false;
	}

	$data['MealID'] = makeSafe($_REQUEST['MealID']);
		if($data['MealID'] == empty_lookup_value){ $data['MealID'] = ''; }
	$data['RecipeID'] = makeSafe($_REQUEST['RecipeID']);
		if($data['RecipeID'] == empty_lookup_value){ $data['RecipeID'] = ''; }
	if($data['MealID']== ''){
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">" . $Translation['error:'] . " 'Meal': " . $Translation['field not null'] . '<br><br>';
		echo '<a href="" onclick="history.go(-1); return false;">'.$Translation['< back'].'</a></div>';
		exit;
	}
	if($data['RecipeID']== ''){
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">" . $Translation['error:'] . " 'Recipe': " . $Translation['field not null'] . '<br><br>';
		echo '<a href="" onclick="history.go(-1); return false;">'.$Translation['< back'].'</a></div>';
		exit;
	}

	// hook: mealrecipes_before_insert
	if(function_exists('mealrecipes_before_insert')){
		$args=array();
		if(!mealrecipes_before_insert($data, getMemberInfo(), $args)){ return false; }
	}

	$o = array('silentErrors' => true);
	sql('insert into `mealrecipes` set       `MealID`=' . (($data['MealID'] !== '' && $data['MealID'] !== NULL) ? "'{$data['MealID']}'" : 'NULL') . ', `RecipeID`=' . (($data['RecipeID'] !== '' && $data['RecipeID'] !== NULL) ? "'{$data['RecipeID']}'" : 'NULL'), $o);
	if($o['error']!=''){
		echo $o['error'];
		echo "<a href=\"mealrecipes_view.php?addNew_x=1\">{$Translation['< back']}</a>";
		exit;
	}

	$recID = db_insert_id(db_link());

	// hook: mealrecipes_after_insert
	if(function_exists('mealrecipes_after_insert')){
		$res = sql("select * from `mealrecipes` where `MealRecipeID`='" . makeSafe($recID, false) . "' limit 1", $eo);
		if($row = db_fetch_assoc($res)){
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = makeSafe($recID, false);
		$args=array();
		if(!mealrecipes_after_insert($data, getMemberInfo(), $args)){ return $recID; }
	}

	// mm: save ownership data
	sql("insert ignore into membership_userrecords set tableName='mealrecipes', pkValue='" . makeSafe($recID, false) . "', memberID='" . makeSafe(getLoggedMemberID(), false) . "', dateAdded='" . time() . "', dateUpdated='" . time() . "', groupID='" . getLoggedGroupID() . "'", $eo);

	return $recID;
}

function mealrecipes_delete($selected_id, $AllowDeleteOfParents=false, $skipChecks=false){
	// insure referential integrity ...
	global $Translation;
	$selected_id=makeSafe($selected_id);

	// mm: can member delete record?
	$arrPerm=getTablePermissions('mealrecipes');
	$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='mealrecipes' and pkValue='$selected_id'");
	$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='mealrecipes' and pkValue='$selected_id'");
	if(($arrPerm[4]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[4]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[4]==3){ // allow delete?
		// delete allowed, so continue ...
	}else{
		return $Translation['You don\'t have enough permissions to delete this record'];
	}

	// hook: mealrecipes_before_delete
	if(function_exists('mealrecipes_before_delete')){
		$args=array();
		if(!mealrecipes_before_delete($selected_id, $skipChecks, getMemberInfo(), $args))
			return $Translation['Couldn\'t delete this record'];
	}

	sql("delete from `mealrecipes` where `MealRecipeID`='$selected_id'", $eo);

	// hook: mealrecipes_after_delete
	if(function_exists('mealrecipes_after_delete')){
		$args=array();
		mealrecipes_after_delete($selected_id, getMemberInfo(), $args);
	}

	// mm: delete ownership data
	sql("delete from membership_userrecords where tableName='mealrecipes' and pkValue='$selected_id'", $eo);
}

function mealrecipes_update($selected_id){
	global $Translation;

	// mm: can member edit record?
	$arrPerm=getTablePermissions('mealrecipes');
	$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='mealrecipes' and pkValue='".makeSafe($selected_id)."'");
	$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='mealrecipes' and pkValue='".makeSafe($selected_id)."'");
	if(($arrPerm[3]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[3]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[3]==3){ // allow update?
		// update allowed, so continue ...
	}else{
		return false;
	}

	$data['MealID'] = makeSafe($_REQUEST['MealID']);
		if($data['MealID'] == empty_lookup_value){ $data['MealID'] = ''; }
	if($data['MealID']==''){
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Meal': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">'.$Translation['< back'].'</a></div>';
		exit;
	}
	$data['RecipeID'] = makeSafe($_REQUEST['RecipeID']);
		if($data['RecipeID'] == empty_lookup_value){ $data['RecipeID'] = ''; }
	if($data['RecipeID']==''){
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Recipe': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">'.$Translation['< back'].'</a></div>';
		exit;
	}
	$data['selectedID']=makeSafe($selected_id);

	// hook: mealrecipes_before_update
	if(function_exists('mealrecipes_before_update')){
		$args=array();
		if(!mealrecipes_before_update($data, getMemberInfo(), $args)){ return false; }
	}

	$o=array('silentErrors' => true);
	sql('update `mealrecipes` set       `MealID`=' . (($data['MealID'] !== '' && $data['MealID'] !== NULL) ? "'{$data['MealID']}'" : 'NULL') . ', `RecipeID`=' . (($data['RecipeID'] !== '' && $data['RecipeID'] !== NULL) ? "'{$data['RecipeID']}'" : 'NULL') . " where `MealRecipeID`='".makeSafe($selected_id)."'", $o);
	if($o['error']!=''){
		echo $o['error'];
		echo '<a href="mealrecipes_view.php?SelectedID='.urlencode($selected_id)."\">{$Translation['< back']}</a>";
		exit;
	}


	// hook: mealrecipes_after_update
	if(function_exists('mealrecipes_after_update')){
		$res = sql("SELECT * FROM `mealrecipes` WHERE `MealRecipeID`='{$data['selectedID']}' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)){
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = $data['MealRecipeID'];
		$args = array();
		if(!mealrecipes_after_update($data, getMemberInfo(), $args)){ return; }
	}

	// mm: update ownership data
	sql("update membership_userrecords set dateUpdated='".time()."' where tableName='mealrecipes' and pkValue='".makeSafe($selected_id)."'", $eo);

}

function mealrecipes_form($selected_id = '', $AllowUpdate = 1, $AllowInsert = 1, $AllowDelete = 1, $ShowCancel = 0, $TemplateDV = '', $TemplateDVP = ''){
	// function to return an editable form for a table records
	// and fill it with data of record whose ID is $selected_id. If $selected_id
	// is empty, an empty form is shown, with only an 'Add New'
	// button displayed.

	global $Translation;

	// mm: get table permissions
	$arrPerm=getTablePermissions('mealrecipes');
	if(!$arrPerm[1] && $selected_id==''){ return ''; }
	$AllowInsert = ($arrPerm[1] ? true : false);
	// print preview?
	$dvprint = false;
	if($selected_id && $_REQUEST['dvprint_x'] != ''){
		$dvprint = true;
	}

	$filterer_MealID = thisOr(undo_magic_quotes($_REQUEST['filterer_MealID']), '');
	$filterer_RecipeID = thisOr(undo_magic_quotes($_REQUEST['filterer_RecipeID']), '');

	// populate filterers, starting from children to grand-parents

	// unique random identifier
	$rnd1 = ($dvprint ? rand(1000000, 9999999) : '');
	// combobox: MealID
	$combo_MealID = new DataCombo;
	// combobox: RecipeID
	$combo_RecipeID = new DataCombo;

	if($selected_id){
		// mm: check member permissions
		if(!$arrPerm[2]){
			return "";
		}
		// mm: who is the owner?
		$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='mealrecipes' and pkValue='".makeSafe($selected_id)."'");
		$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='mealrecipes' and pkValue='".makeSafe($selected_id)."'");
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

		$res = sql("select * from `mealrecipes` where `MealRecipeID`='".makeSafe($selected_id)."'", $eo);
		if(!($row = db_fetch_array($res))){
			return error_message($Translation['No records found'], 'mealrecipes_view.php', false);
		}
		$urow = $row; /* unsanitized data */
		$hc = new CI_Input();
		$row = $hc->xss_clean($row); /* sanitize data */
		$combo_MealID->SelectedData = $row['MealID'];
		$combo_RecipeID->SelectedData = $row['RecipeID'];
	}else{
		$combo_MealID->SelectedData = $filterer_MealID;
		$combo_RecipeID->SelectedData = $filterer_RecipeID;
	}
	$combo_MealID->HTML = '<span id="MealID-container' . $rnd1 . '"></span><input type="hidden" name="MealID" id="MealID' . $rnd1 . '" value="' . html_attr($combo_MealID->SelectedData) . '">';
	$combo_MealID->MatchText = '<span id="MealID-container-readonly' . $rnd1 . '"></span><input type="hidden" name="MealID" id="MealID' . $rnd1 . '" value="' . html_attr($combo_MealID->SelectedData) . '">';
	$combo_RecipeID->HTML = '<span id="RecipeID-container' . $rnd1 . '"></span><input type="hidden" name="RecipeID" id="RecipeID' . $rnd1 . '" value="' . html_attr($combo_RecipeID->SelectedData) . '">';
	$combo_RecipeID->MatchText = '<span id="RecipeID-container-readonly' . $rnd1 . '"></span><input type="hidden" name="RecipeID" id="RecipeID' . $rnd1 . '" value="' . html_attr($combo_RecipeID->SelectedData) . '">';

	ob_start();
	?>

	<script>
		// initial lookup values
		AppGini.current_MealID__RAND__ = { text: "", value: "<?php echo addslashes($selected_id ? $urow['MealID'] : $filterer_MealID); ?>"};
		AppGini.current_RecipeID__RAND__ = { text: "", value: "<?php echo addslashes($selected_id ? $urow['RecipeID'] : $filterer_RecipeID); ?>"};

		jQuery(function() {
			setTimeout(function(){
				if(typeof(MealID_reload__RAND__) == 'function') MealID_reload__RAND__();
				if(typeof(RecipeID_reload__RAND__) == 'function') RecipeID_reload__RAND__();
			}, 10); /* we need to slightly delay client-side execution of the above code to allow AppGini.ajaxCache to work */
		});
		function MealID_reload__RAND__(){
		<?php if(($AllowUpdate || $AllowInsert) && !$dvprint){ ?>

			$j("#MealID-container__RAND__").select2({
				/* initial default value */
				initSelection: function(e, c){
					$j.ajax({
						url: 'ajax_combo.php',
						dataType: 'json',
						data: { id: AppGini.current_MealID__RAND__.value, t: 'mealrecipes', f: 'MealID' },
						success: function(resp){
							c({
								id: resp.results[0].id,
								text: resp.results[0].text
							});
							$j('[name="MealID"]').val(resp.results[0].id);
							$j('[id=MealID-container-readonly__RAND__]').html('<span id="MealID-match-text">' + resp.results[0].text + '</span>');
							if(resp.results[0].id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=meals_view_parent]').hide(); }else{ $j('.btn[id=meals_view_parent]').show(); }


							if(typeof(MealID_update_autofills__RAND__) == 'function') MealID_update_autofills__RAND__();
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
					data: function(term, page){ return { s: term, p: page, t: 'mealrecipes', f: 'MealID' }; },
					results: function(resp, page){ return resp; }
				},
				escapeMarkup: function(str){ return str; }
			}).on('change', function(e){
				AppGini.current_MealID__RAND__.value = e.added.id;
				AppGini.current_MealID__RAND__.text = e.added.text;
				$j('[name="MealID"]').val(e.added.id);
				if(e.added.id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=meals_view_parent]').hide(); }else{ $j('.btn[id=meals_view_parent]').show(); }


				if(typeof(MealID_update_autofills__RAND__) == 'function') MealID_update_autofills__RAND__();
			});

			if(!$j("#MealID-container__RAND__").length){
				$j.ajax({
					url: 'ajax_combo.php',
					dataType: 'json',
					data: { id: AppGini.current_MealID__RAND__.value, t: 'mealrecipes', f: 'MealID' },
					success: function(resp){
						$j('[name="MealID"]').val(resp.results[0].id);
						$j('[id=MealID-container-readonly__RAND__]').html('<span id="MealID-match-text">' + resp.results[0].text + '</span>');
						if(resp.results[0].id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=meals_view_parent]').hide(); }else{ $j('.btn[id=meals_view_parent]').show(); }

						if(typeof(MealID_update_autofills__RAND__) == 'function') MealID_update_autofills__RAND__();
					}
				});
			}

		<?php }else{ ?>

			$j.ajax({
				url: 'ajax_combo.php',
				dataType: 'json',
				data: { id: AppGini.current_MealID__RAND__.value, t: 'mealrecipes', f: 'MealID' },
				success: function(resp){
					$j('[id=MealID-container__RAND__], [id=MealID-container-readonly__RAND__]').html('<span id="MealID-match-text">' + resp.results[0].text + '</span>');
					if(resp.results[0].id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=meals_view_parent]').hide(); }else{ $j('.btn[id=meals_view_parent]').show(); }

					if(typeof(MealID_update_autofills__RAND__) == 'function') MealID_update_autofills__RAND__();
				}
			});
		<?php } ?>

		}
		function RecipeID_reload__RAND__(){
		<?php if(($AllowUpdate || $AllowInsert) && !$dvprint){ ?>

			$j("#RecipeID-container__RAND__").select2({
				/* initial default value */
				initSelection: function(e, c){
					$j.ajax({
						url: 'ajax_combo.php',
						dataType: 'json',
						data: { id: AppGini.current_RecipeID__RAND__.value, t: 'mealrecipes', f: 'RecipeID' },
						success: function(resp){
							c({
								id: resp.results[0].id,
								text: resp.results[0].text
							});
							$j('[name="RecipeID"]').val(resp.results[0].id);
							$j('[id=RecipeID-container-readonly__RAND__]').html('<span id="RecipeID-match-text">' + resp.results[0].text + '</span>');
							if(resp.results[0].id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=recipes_view_parent]').hide(); }else{ $j('.btn[id=recipes_view_parent]').show(); }


							if(typeof(RecipeID_update_autofills__RAND__) == 'function') RecipeID_update_autofills__RAND__();
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
					data: function(term, page){ return { s: term, p: page, t: 'mealrecipes', f: 'RecipeID' }; },
					results: function(resp, page){ return resp; }
				},
				escapeMarkup: function(str){ return str; }
			}).on('change', function(e){
				AppGini.current_RecipeID__RAND__.value = e.added.id;
				AppGini.current_RecipeID__RAND__.text = e.added.text;
				$j('[name="RecipeID"]').val(e.added.id);
				if(e.added.id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=recipes_view_parent]').hide(); }else{ $j('.btn[id=recipes_view_parent]').show(); }


				if(typeof(RecipeID_update_autofills__RAND__) == 'function') RecipeID_update_autofills__RAND__();
			});

			if(!$j("#RecipeID-container__RAND__").length){
				$j.ajax({
					url: 'ajax_combo.php',
					dataType: 'json',
					data: { id: AppGini.current_RecipeID__RAND__.value, t: 'mealrecipes', f: 'RecipeID' },
					success: function(resp){
						$j('[name="RecipeID"]').val(resp.results[0].id);
						$j('[id=RecipeID-container-readonly__RAND__]').html('<span id="RecipeID-match-text">' + resp.results[0].text + '</span>');
						if(resp.results[0].id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=recipes_view_parent]').hide(); }else{ $j('.btn[id=recipes_view_parent]').show(); }

						if(typeof(RecipeID_update_autofills__RAND__) == 'function') RecipeID_update_autofills__RAND__();
					}
				});
			}

		<?php }else{ ?>

			$j.ajax({
				url: 'ajax_combo.php',
				dataType: 'json',
				data: { id: AppGini.current_RecipeID__RAND__.value, t: 'mealrecipes', f: 'RecipeID' },
				success: function(resp){
					$j('[id=RecipeID-container__RAND__], [id=RecipeID-container-readonly__RAND__]').html('<span id="RecipeID-match-text">' + resp.results[0].text + '</span>');
					if(resp.results[0].id == '<?php echo empty_lookup_value; ?>'){ $j('.btn[id=recipes_view_parent]').hide(); }else{ $j('.btn[id=recipes_view_parent]').show(); }

					if(typeof(RecipeID_update_autofills__RAND__) == 'function') RecipeID_update_autofills__RAND__();
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
		$template_file = is_file("./{$TemplateDVP}") ? "./{$TemplateDVP}" : './templates/mealrecipes_templateDVP.html';
		$templateCode = @file_get_contents($template_file);
	}else{
		$template_file = is_file("./{$TemplateDV}") ? "./{$TemplateDV}" : './templates/mealrecipes_templateDV.html';
		$templateCode = @file_get_contents($template_file);
	}

	// process form title
	$templateCode = str_replace('<%%DETAIL_VIEW_TITLE%%>', 'Detail View', $templateCode);
	$templateCode = str_replace('<%%RND1%%>', $rnd1, $templateCode);
	$templateCode = str_replace('<%%EMBEDDED%%>', ($_REQUEST['Embedded'] ? 'Embedded=1' : ''), $templateCode);
	// process buttons
	if($AllowInsert){
		if(!$selected_id) $templateCode=str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-success" id="insert" name="insert_x" value="1" onclick="return mealrecipes_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save New'] . '</button>', $templateCode);
		$templateCode=str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="insert" name="insert_x" value="1" onclick="return mealrecipes_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save As Copy'] . '</button>', $templateCode);
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
			$templateCode=str_replace('<%%UPDATE_BUTTON%%>', '<button type="submit" class="btn btn-success btn-lg" id="update" name="update_x" value="1" onclick="return mealrecipes_validateData();" title="' . html_attr($Translation['Save Changes']) . '"><i class="glyphicon glyphicon-ok"></i> ' . $Translation['Save Changes'] . '</button>', $templateCode);
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
		$jsReadOnly .= "\tjQuery('#MealID').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('#MealID_caption').prop('disabled', true).css({ color: '#555', backgroundColor: 'white' });\n";
		$jsReadOnly .= "\tjQuery('#RecipeID').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('#RecipeID_caption').prop('disabled', true).css({ color: '#555', backgroundColor: 'white' });\n";
		$jsReadOnly .= "\tjQuery('.select2-container').hide();\n";

		$noUploads = true;
	}elseif($AllowInsert){
		$jsEditable .= "\tjQuery('form').eq(0).data('already_changed', true);"; // temporarily disable form change handler
			$jsEditable .= "\tjQuery('form').eq(0).data('already_changed', false);"; // re-enable form change handler
	}

	// process combos
	$templateCode=str_replace('<%%COMBO(MealID)%%>', $combo_MealID->HTML, $templateCode);
	$templateCode=str_replace('<%%COMBOTEXT(MealID)%%>', $combo_MealID->MatchText, $templateCode);
	$templateCode=str_replace('<%%URLCOMBOTEXT(MealID)%%>', urlencode($combo_MealID->MatchText), $templateCode);
	$templateCode=str_replace('<%%COMBO(RecipeID)%%>', $combo_RecipeID->HTML, $templateCode);
	$templateCode=str_replace('<%%COMBOTEXT(RecipeID)%%>', $combo_RecipeID->MatchText, $templateCode);
	$templateCode=str_replace('<%%URLCOMBOTEXT(RecipeID)%%>', urlencode($combo_RecipeID->MatchText), $templateCode);

	/* lookup fields array: 'lookup field name' => array('parent table name', 'lookup field caption') */
	$lookup_fields = array(  'MealID' => array('meals', 'Meal'), 'RecipeID' => array('recipes', 'Recipe'));
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
	$templateCode=str_replace('<%%UPLOADFILE(MealRecipeID)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(MealID)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(RecipeID)%%>', '', $templateCode);

	// process values
	if($selected_id){
		$templateCode=str_replace('<%%VALUE(MealRecipeID)%%>', html_attr($row['MealRecipeID']), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(MealRecipeID)%%>', urlencode($urow['MealRecipeID']), $templateCode);
		$templateCode=str_replace('<%%VALUE(MealID)%%>', html_attr($row['MealID']), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(MealID)%%>', urlencode($urow['MealID']), $templateCode);
		$templateCode=str_replace('<%%VALUE(RecipeID)%%>', html_attr($row['RecipeID']), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(RecipeID)%%>', urlencode($urow['RecipeID']), $templateCode);
	}else{
		$templateCode=str_replace('<%%VALUE(MealRecipeID)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(MealRecipeID)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(MealID)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(MealID)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(RecipeID)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(RecipeID)%%>', urlencode(''), $templateCode);
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
	$rdata = $jdata = get_defaults('mealrecipes');
	if($selected_id){
		$jdata = get_joined_record('mealrecipes', $selected_id);
		$rdata = $row;
	}
	$cache_data = array(
		'rdata' => array_map('nl2br', array_map('addslashes', $rdata)),
		'jdata' => array_map('nl2br', array_map('addslashes', $jdata)),
	);
	$templateCode .= loadView('mealrecipes-ajax-cache', $cache_data);

	// hook: mealrecipes_dv
	if(function_exists('mealrecipes_dv')){
		$args=array();
		mealrecipes_dv(($selected_id ? $selected_id : FALSE), getMemberInfo(), $templateCode, $args);
	}

	return $templateCode;
}
?>