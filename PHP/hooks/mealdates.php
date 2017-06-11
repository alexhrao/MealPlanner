<?php
	// For help on using hooks, please refer to http://bigprof.com/appgini/help/working-with-generated-web-database-application/hooks

	function mealdates_init(&$options, $memberInfo, &$args){
		/* ob_start();
		$xc=get_object_vars($options);
		ksort($xc);
		print_r($xc);
		$c = ob_get_clean();
		echo "<pre>" . htmlspecialchars($c) . "</pre>"; */
		return TRUE;
	}

	function mealdates_header($contentType, $memberInfo, &$args){
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

	function mealdates_footer($contentType, $memberInfo, &$args){
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

	function mealdates_before_insert(&$data, $memberInfo, &$args){

		return TRUE;
	}

	function mealdates_after_insert($data, $memberInfo, &$args){

		return TRUE;
	}

	function mealdates_before_update(&$data, $memberInfo, &$args){

		return TRUE;
	}

	function mealdates_after_update($data, $memberInfo, &$args){

		return TRUE;
	}

	function mealdates_before_delete($selectedID, &$skipChecks, $memberInfo, &$args){

		return TRUE;
	}

	function mealdates_after_delete($selectedID, $memberInfo, &$args){

	}

	function mealdates_dv($selectedID, $memberInfo, &$html, &$args){

	}

	function mealdates_csv($query, $memberInfo, &$args){

		return $query;
	}
	function mealdates_batch_actions(&$args){

		return array();
	}
