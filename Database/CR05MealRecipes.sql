USE meal_planner_db;

CREATE TABLE mealrecipes(
	MealRecipeID int auto_increment NOT NULL,
	MealID int NOT NULL,
	RecipeID int NOT NULL,
 CONSTRAINT PK_MealRecipes PRIMARY KEY CLUSTERED 
(
	MealRecipeID ASC
));
ALTER TABLE mealrecipes ADD CONSTRAINT FK_MealRecipes_Meals FOREIGN KEY(MealID)
	REFERENCES meals (MealID);
ALTER TABLE mealrecipes ADD CONSTRAINT FK_MealRecipes_Recipes FOREIGN KEY(RecipeID)
	REFERENCES recipes (RecipeID);