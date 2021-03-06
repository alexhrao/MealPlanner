<?php
	/*
	 * You can add custom links in the home page by appending them here ...
	 * The format for each link is:
		$homeLinks[] = array(
			'url' => 'path/to/link', 
			'title' => 'Link title', 
			'description' => 'Link text',
			'groups' => array('group1', 'group2'), // groups allowed to see this link, use '*' if you want to show the link to all groups
			'grid_column_classes' => '', // optional CSS classes to apply to link block. See: http://getbootstrap.com/css/#grid
			'panel_classes' => '', // optional CSS classes to apply to panel. See: http://getbootstrap.com/components/#panels
			'link_classes' => '', // optional CSS classes to apply to link. See: http://getbootstrap.com/css/#buttons
			'icon' => 'path/to/icon' // optional icon to use with the link
		);
	 */
	$homeLinks[] = array(
		'url' => "calendar.php",
		'title' => "Calendar",
		'description' => 'View when your meals are in a calendar view',
		'groups' => array('Admins', 'Editors'),
		'grid_column_classes' => 'col-sm-3',
		'panel_classes' => 'panel-warning',
		'link_classes' => 'btn-primary',
		'icon' => 'resources/table_icons/calendar.png',
		'table_group' => 'Meal Planner',
	);

	$homeLinks[] = array(
		'url' => "shoppingList.php",
		'title' => "Shopping List",
		'description' => 'Generate a shopping list',
		'groups' => array('Admins', 'Editors'),
		'grid_column_classes' => 'col-sm-3',
		'panel_classes' => 'panel-warning',
		'link_classes' => 'btn-primary',
		'icon' => 'resources/table_icons/list.png',
		'table_group' => 'Meal Planner',
	);
?>