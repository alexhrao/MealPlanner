USE Meal_Planner_DB;

CREATE TABLE MealRecipes(
	MealRecipeID int auto_increment NOT NULL,
	MealID int NOT NULL,
	RecipeID int NOT NULL,
 CONSTRAINT PK_MealRecipes PRIMARY KEY CLUSTERED 
(
	MealRecipeID ASC
));

ALTER TABLE MealRecipes ADD CONSTRAINT FK_MealRecipes_Meals FOREIGN KEY(MealID)
REFERENCES Meals (MealID);

ALTER TABLE MealRecipes ADD CONSTRAINT FK_MealRecipes_Recipes FOREIGN KEY(RecipeID)
REFERENCES Recipes (RecipeID);


