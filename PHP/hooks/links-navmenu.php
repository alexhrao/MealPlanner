<?php
	/*
	* You can add custom links to the navigation menu by appending them here ...
	* The format for each link is:
		$navLinks[] = array(
			'url' => 'path/to/link', 
			'title' => 'Link title', 
			'groups' => array('group1', 'group2'), // groups allowed to see this link, use '*' if you want to show the link to all groups
			'icon' => 'path/to/icon',
			'table_group' => 0, // optional index of table group, default is 0
		);
	*/
	$navLinks[] = array(
		'url' => '/calendar.php',
		'title' => 'Calendar',
		'table_group' => 0,
		'groups' => '*',
		'icon' => 'resources/table_icons/calendar.png'
	);
	$navLinks[] = array(
		'url' => '/shoppingList.php',
		'title' => 'Shopping List',
		'table_group' => 0,
		'groups' => '*',
		'icon' => 'resources/table_icons/list.png'
	);
		?>