USE meal_planner_db;

CREATE TABLE recipeingredients(
	RecipeIngredientID int auto_increment NOT NULL,
	RecipeID int NOT NULL,
	IngredientID int NOT NULL,
	Amount float NULL,
 CONSTRAINT PK_RecipeIngredients PRIMARY KEY CLUSTERED 
(
	RecipeIngredientID ASC
));
ALTER TABLE recipeingredients ADD CONSTRAINT FK_RecipeIngredients_Ingredients FOREIGN KEY(IngredientID)
	REFERENCES ingredients (IngredientID);
ALTER TABLE recipeingredients ADD CONSTRAINT FK_RecipeIngredients_Recipes FOREIGN KEY(RecipeID)
	REFERENCES recipes (RecipeID);