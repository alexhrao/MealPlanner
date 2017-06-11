<?php
	// For help on using hooks, please refer to http://bigprof.com/appgini/help/working-with-generated-web-database-application/hooks

	function ingredients_init(&$options, $memberInfo, &$args){

		return TRUE;
	}

	function ingredients_header($contentType, $memberInfo, &$args){
		$header='';

		switch($contentType){
			case 'tableview':
				$header='';
				break;

			case 'detailview':
				$header='';
				break;

			case 'tableview+detailview':
				$header='';
				break;

			case 'print-tableview':
				$header='';
				break;

			case 'print-detailview':
				$header='';
				break;

			case 'filters':
				$header='';
				break;
		}

		return $header;
	}

	function ingredients_footer($contentType, $memberInfo, &$args){
		$footer='';

		switch($contentType){
			case 'tableview':
				$footer='';
				break;

			case 'detailview':
				$footer='';
				break;

			case 'tableview+detailview':
				$footer='';
				break;

			case 'print-tableview':
				$footer='';
				break;

			case 'print-detailview':
				$footer='';
				break;

			case 'filters':
				$footer='';
				break;
		}

		return $footer;
	}

	function ingredients_before_insert(&$data, $memberInfo, &$args){
		$data['RecipeUnit'] = $data['PricingUnit'];
		return TRUE;
	}

	function ingredients_after_insert($data, $memberInfo, &$args){

		return TRUE;
	}

	function ingredients_before_update(&$data, $memberInfo, &$args){
		$data['RecipeUnit'] = $data['PricingUnit'];
		return TRUE;
	}

	function ingredients_after_update($data, $memberInfo, &$args){

		return TRUE;
	}

	function ingredients_before_delete($selectedID, &$skipChecks, $memberInfo, &$args){

		return TRUE;
	}

	function ingredients_after_delete($selectedID, $memberInfo, &$args){

	}

	function ingredients_dv($selectedID, $memberInfo, &$html, &$args){

	}

	function ingredients_csv($query, $memberInfo, &$args){

		return $query;
	}
	function ingredients_batch_actions(&$args){

		return array();
	}
