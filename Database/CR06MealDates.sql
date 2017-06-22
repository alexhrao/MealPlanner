USE meal_planner_db;

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