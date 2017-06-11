USE Meal_Planner_DB;

CREATE TABLE MealDates(
	MealDateID int auto_increment NOT NULL,
	MealID int NOT NULL,
	MealDate date NOT NULL,
	MealTime nvarchar (50) NOT NULL,
 CONSTRAINT PK_MealDates PRIMARY KEY CLUSTERED 
(
	MealDateID ASC
));
ALTER TABLE MealDates ADD CONSTRAINT FK_MealDates_Meals FOREIGN KEY(MealID)
REFERENCES Meals (MealID);
