USE meal_planner_db;

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