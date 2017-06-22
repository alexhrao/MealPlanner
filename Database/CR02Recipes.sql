USE meal_planner_db;

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
ALTER TABLE recipes ADD  CONSTRAINT FK_Recipes_Sources FOREIGN KEY(SourceID)
	REFERENCES sources (SourceID);
CREATE UNIQUE INDEX idxName ON recipes (Name);