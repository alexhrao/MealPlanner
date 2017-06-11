USE Meal_Planner_DB;


CREATE TABLE Sources(
	SourceID int auto_increment NOT NULL,
	FullName nvarchar(50) NOT NULL,
	PhoneNumber nvarchar(50) NULL,
	Description nvarchar(100) NOT NULL,
 CONSTRAINT PK_Sources PRIMARY KEY CLUSTERED 
(
	SourceID ASC
));
