USE meal_planner_db;

/****** Object:  Table dbo.Ingredients    Script Date: 5/30/2016 2:42:36 PM ******/

create TABLE Ingredients(
	IngredientID int auto_increment NOT NULL,
	Name nvarchar(50) NOT NULL,
	PricingUnit nvarchar(50) NULL,
	RecipeUnit nvarchar(50) NULL,
	PluralForm nvarchar(50) NULL,
	Description nvarchar(2000) NULL,
 CONSTRAINT PK_Ingredients PRIMARY KEY CLUSTERED 
(
	IngredientID ASC
));




