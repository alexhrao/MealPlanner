USE meal_planner_db;

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
CREATE UNIQUE INDEX idxName	ON stores (Name);