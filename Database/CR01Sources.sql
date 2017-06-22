USE meal_planner_db;

CREATE TABLE sources(
	SourceID int auto_increment NOT NULL,
	FullName nvarchar(50) NOT NULL,
	PhoneNumber nvarchar(50) NULL,
	Description nvarchar(100) NULL,
 CONSTRAINT PK_Sources PRIMARY KEY CLUSTERED 
(
	SourceID ASC
));
CREATE UNIQUE INDEX idxFullName	ON sources (FullName);