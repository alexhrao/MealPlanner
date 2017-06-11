var FiltersEnabled = 0; // if your not going to use transitions or filters in any of the tips set this to 0
var spacer="&nbsp; &nbsp; &nbsp; ";

// email notifications to admin
notifyAdminNewMembers0Tip=["", spacer+"No email notifications to admin."];
notifyAdminNewMembers1Tip=["", spacer+"Notify admin only when a new member is waiting for approval."];
notifyAdminNewMembers2Tip=["", spacer+"Notify admin for all new sign-ups."];

// visitorSignup
visitorSignup0Tip=["", spacer+"If this option is selected, visitors will not be able to join this group unless the admin manually moves them to this group from the admin area."];
visitorSignup1Tip=["", spacer+"If this option is selected, visitors can join this group but will not be able to sign in unless the admin approves them from the admin area."];
visitorSignup2Tip=["", spacer+"If this option is selected, visitors can join this group and will be able to sign in instantly with no need for admin approval."];

// mealdates table
mealdates_addTip=["",spacer+"This option allows all members of the group to add records to the 'Date Planner' table. A member who adds a record to the table becomes the 'owner' of that record."];

mealdates_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Date Planner' table."];
mealdates_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Date Planner' table."];
mealdates_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Date Planner' table."];
mealdates_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Date Planner' table."];

mealdates_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Date Planner' table."];
mealdates_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Date Planner' table."];
mealdates_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Date Planner' table."];
mealdates_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Date Planner' table, regardless of their owner."];

mealdates_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Date Planner' table."];
mealdates_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Date Planner' table."];
mealdates_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Date Planner' table."];
mealdates_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Date Planner' table."];

// meals table
meals_addTip=["",spacer+"This option allows all members of the group to add records to the 'Meals' table. A member who adds a record to the table becomes the 'owner' of that record."];

meals_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Meals' table."];
meals_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Meals' table."];
meals_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Meals' table."];
meals_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Meals' table."];

meals_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Meals' table."];
meals_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Meals' table."];
meals_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Meals' table."];
meals_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Meals' table, regardless of their owner."];

meals_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Meals' table."];
meals_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Meals' table."];
meals_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Meals' table."];
meals_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Meals' table."];

// recipes table
recipes_addTip=["",spacer+"This option allows all members of the group to add records to the 'Recipes' table. A member who adds a record to the table becomes the 'owner' of that record."];

recipes_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Recipes' table."];
recipes_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Recipes' table."];
recipes_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Recipes' table."];
recipes_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Recipes' table."];

recipes_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Recipes' table."];
recipes_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Recipes' table."];
recipes_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Recipes' table."];
recipes_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Recipes' table, regardless of their owner."];

recipes_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Recipes' table."];
recipes_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Recipes' table."];
recipes_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Recipes' table."];
recipes_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Recipes' table."];

// ingredients table
ingredients_addTip=["",spacer+"This option allows all members of the group to add records to the 'Ingredients' table. A member who adds a record to the table becomes the 'owner' of that record."];

ingredients_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Ingredients' table."];
ingredients_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Ingredients' table."];
ingredients_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Ingredients' table."];
ingredients_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Ingredients' table."];

ingredients_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Ingredients' table."];
ingredients_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Ingredients' table."];
ingredients_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Ingredients' table."];
ingredients_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Ingredients' table, regardless of their owner."];

ingredients_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Ingredients' table."];
ingredients_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Ingredients' table."];
ingredients_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Ingredients' table."];
ingredients_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Ingredients' table."];

// mealrecipes table
mealrecipes_addTip=["",spacer+"This option allows all members of the group to add records to the 'Meals & Recipes' table. A member who adds a record to the table becomes the 'owner' of that record."];

mealrecipes_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Meals & Recipes' table."];
mealrecipes_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Meals & Recipes' table."];
mealrecipes_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Meals & Recipes' table."];
mealrecipes_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Meals & Recipes' table."];

mealrecipes_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Meals & Recipes' table."];
mealrecipes_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Meals & Recipes' table."];
mealrecipes_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Meals & Recipes' table."];
mealrecipes_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Meals & Recipes' table, regardless of their owner."];

mealrecipes_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Meals & Recipes' table."];
mealrecipes_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Meals & Recipes' table."];
mealrecipes_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Meals & Recipes' table."];
mealrecipes_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Meals & Recipes' table."];

// recipeingredients table
recipeingredients_addTip=["",spacer+"This option allows all members of the group to add records to the 'Recipes & Ingredients' table. A member who adds a record to the table becomes the 'owner' of that record."];

recipeingredients_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Recipes & Ingredients' table."];
recipeingredients_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Recipes & Ingredients' table."];
recipeingredients_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Recipes & Ingredients' table."];
recipeingredients_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Recipes & Ingredients' table."];

recipeingredients_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Recipes & Ingredients' table."];
recipeingredients_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Recipes & Ingredients' table."];
recipeingredients_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Recipes & Ingredients' table."];
recipeingredients_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Recipes & Ingredients' table, regardless of their owner."];

recipeingredients_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Recipes & Ingredients' table."];
recipeingredients_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Recipes & Ingredients' table."];
recipeingredients_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Recipes & Ingredients' table."];
recipeingredients_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Recipes & Ingredients' table."];

// ingredientstores table
ingredientstores_addTip=["",spacer+"This option allows all members of the group to add records to the 'Ingredients & Stores' table. A member who adds a record to the table becomes the 'owner' of that record."];

ingredientstores_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Ingredients & Stores' table."];
ingredientstores_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Ingredients & Stores' table."];
ingredientstores_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Ingredients & Stores' table."];
ingredientstores_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Ingredients & Stores' table."];

ingredientstores_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Ingredients & Stores' table."];
ingredientstores_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Ingredients & Stores' table."];
ingredientstores_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Ingredients & Stores' table."];
ingredientstores_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Ingredients & Stores' table, regardless of their owner."];

ingredientstores_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Ingredients & Stores' table."];
ingredientstores_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Ingredients & Stores' table."];
ingredientstores_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Ingredients & Stores' table."];
ingredientstores_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Ingredients & Stores' table."];

// sources table
sources_addTip=["",spacer+"This option allows all members of the group to add records to the 'Sources' table. A member who adds a record to the table becomes the 'owner' of that record."];

sources_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Sources' table."];
sources_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Sources' table."];
sources_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Sources' table."];
sources_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Sources' table."];

sources_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Sources' table."];
sources_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Sources' table."];
sources_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Sources' table."];
sources_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Sources' table, regardless of their owner."];

sources_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Sources' table."];
sources_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Sources' table."];
sources_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Sources' table."];
sources_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Sources' table."];

// stores table
stores_addTip=["",spacer+"This option allows all members of the group to add records to the 'Stores' table. A member who adds a record to the table becomes the 'owner' of that record."];

stores_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Stores' table."];
stores_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Stores' table."];
stores_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Stores' table."];
stores_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Stores' table."];

stores_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Stores' table."];
stores_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Stores' table."];
stores_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Stores' table."];
stores_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Stores' table, regardless of their owner."];

stores_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Stores' table."];
stores_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Stores' table."];
stores_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Stores' table."];
stores_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Stores' table."];

/*
	Style syntax:
	-------------
	[TitleColor,TextColor,TitleBgColor,TextBgColor,TitleBgImag,TextBgImag,TitleTextAlign,
	TextTextAlign,TitleFontFace,TextFontFace, TipPosition, StickyStyle, TitleFontSize,
	TextFontSize, Width, Height, BorderSize, PadTextArea, CoordinateX , CoordinateY,
	TransitionNumber, TransitionDuration, TransparencyLevel ,ShadowType, ShadowColor]

*/

toolTipStyle=["white","#00008B","#000099","#E6E6FA","","images/helpBg.gif","","","","\"Trebuchet MS\", sans-serif","","","","3",400,"",1,2,10,10,51,1,0,"",""];

applyCssFilter();
