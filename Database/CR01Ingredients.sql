USE meal_planner_db;

CREATE TABLE ingredients(
	IngredientID int auto_increment NOT NULL,
	Name nvarchar(50) NOT NULL,
	PricingUnit nvarchar(50) NULL,
	RecipeUnit nvarchar(50) NULL COMMENT "Deprecated - use PricingUnit instead",
	PluralForm nvarchar(50) NULL,
	Description nvarchar(2000) NULL,
 CONSTRAINT PK_Ingredients PRIMARY KEY CLUSTERED 
(
	IngredientID ASC
));
CREATE UNIQUE INDEX idxName ON ingredients (Name);