USE Meal_Planner_DB;

CREATE TABLE Recipes(
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

ALTER TABLE Recipes  ADD  CONSTRAINT FK_Recipes_Sources FOREIGN KEY(SourceID) REFERENCES Sources (SourceID);