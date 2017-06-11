USE Meal_Planner_DB;

CREATE TABLE RecipeIngredients(
	RecipeIngredientID int auto_increment NOT NULL,
	RecipeID int NOT NULL,
	IngredientID int NOT NULL,
	Amount float NULL,
 CONSTRAINT PK_RecipeIngredients PRIMARY KEY CLUSTERED 
(
	RecipeIngredientID ASC
));

ALTER TABLE RecipeIngredients ADD CONSTRAINT FK_RecipeIngredients_Ingredients FOREIGN KEY(IngredientID)
REFERENCES Ingredients (IngredientID);

ALTER TABLE RecipeIngredients ADD CONSTRAINT FK_RecipeIngredients_Recipes FOREIGN KEY(RecipeID)
REFERENCES Recipes (RecipeID);

