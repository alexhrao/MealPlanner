USE Meal_Planner_DB;


CREATE TABLE Stores(
	StoreID int auto_increment NOT NULL,
	Name nvarchar(50) NOT NULL,
	PhoneNumber nvarchar(20) NULL,
	Location nvarchar(2000) NULL,
	Notes nvarchar(2000) NULL,
 CONSTRAINT PK_Stores PRIMARY KEY CLUSTERED 
(
	StoreID ASC
));

