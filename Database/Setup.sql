CREATE DATABASE `meal_planner_db` /*!40100 DEFAULT CHARACTER SET latin1 */;

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

CREATE TABLE sources(
	SourceID int auto_increment NOT NULL,
	FullName nvarchar(50) NOT NULL,
	PhoneNumber nvarchar(50) NULL,
	Description nvarchar(100) NULL,
 CONSTRAINT PK_Sources PRIMARY KEY CLUSTERED 
(
	SourceID ASC
));
CREATE UNIQUE INDEX idxFullName ON sources (FullName);

CREATE TABLE stores(
	StoreID int auto_increment NOT NULL,
	Name nvarchar(50) NOT NULL,
	PhoneNumber nvarchar(20) NULL,
	Location nvarchar(2000) NULL,
	Notes nvarchar(2000) NULL,
 CONSTRAINT PK_Stores PRIMARY KEY CLUSTERED 
(
	StoreID ASC
));
CREATE UNIQUE INDEX idxName ON stores (Name);

CREATE TABLE meals(
	MealID int auto_increment NOT NULL,
	Name nvarchar(50) NOT NULL,
	Description nvarchar(2000) NULL,
	MealTime nvarchar(50) NOT NULL,
 CONSTRAINT PK_Meals PRIMARY KEY CLUSTERED 
(
	MealID ASC
));
CREATE UNIQUE INDEX idxName ON meals (Name);

CREATE TABLE recipes(
	RecipeID int auto_increment NOT NULL,
	Name nvarchar(50) NOT NULL,
	DateCreated datetime NULL,
	Instructions nvarchar(2000) NULL,
	Description nvarchar(2000) NULL,
	ServeTime time(0) NULL,
	Servings tinyint(2) unsigned zerofill NULL,
	SourceID int NULL,
 CONSTRAINT PK_Recipes PRIMARY KEY CLUSTERED 
(
	RecipeID ASC
));
ALTER TABLE recipes ADD  CONSTRAINT FK_Recipes_Sources FOREIGN KEY(SourceID) REFERENCES sources (SourceID);
CREATE UNIQUE INDEX idxName ON recipes (Name);

CREATE TABLE ingredientstores(
	IngredientStoreID int auto_increment NOT NULL,
	IngredientID int NOT NULL,
	StoreID int NOT NULL,
	Cost decimal(13, 4) NULL,
 CONSTRAINT PK_IngredientStores PRIMARY KEY CLUSTERED 
(
	IngredientStoreID ASC
));
ALTER TABLE ingredientstores ADD CONSTRAINT FK_IngredientStores_Ingredients FOREIGN KEY(IngredientID)
	REFERENCES ingredients (IngredientID);
ALTER TABLE ingredientstores ADD CONSTRAINT FK_IngredientStores_Stores FOREIGN KEY(StoreID)
	REFERENCES stores (StoreID);

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

CREATE TABLE mealdates(
	MealDateID int auto_increment NOT NULL,
	MealID int NOT NULL,
	MealDate date NOT NULL,
	MealTime nvarchar (50) NOT NULL,
 CONSTRAINT PK_MealDates PRIMARY KEY CLUSTERED 
(
	MealDateID ASC
));
ALTER TABLE mealdates ADD CONSTRAINT FK_MealDates_Meals FOREIGN KEY(MealID)
	REFERENCES meals (MealID);