jQuery(function() {
	jQuery('#NoFilter').after('<button class="btn btn-default" type="button" id="shoppingList"><i class="glyphicon glyphicon-th-list"></i>Generate Shopping List</button>');
	jQuery('#shoppingList').after('<button class="btn btn-default" type="button" id="calendar"><i class="glyphicon glyphicon-calendar"></i>View Calendar</button>');
	jQuery('#shoppingList').click(function() {
		window.open("shoppingList.php");
	});
	jQuery('#calendar').click(function() {
		window.open("calendar.php");
	});
});