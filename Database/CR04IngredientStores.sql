USE Meal_Planner_DB;

CREATE TABLE IngredientStores(
	IngredientStoreID int auto_increment NOT NULL,
	IngredientID int NOT NULL,
	StoreID int NOT NULL,
	Cost decimal(13, 4) NULL,
 CONSTRAINT PK_IngredientStores PRIMARY KEY CLUSTERED 
(
	IngredientStoreID ASC
));



ALTER TABLE IngredientStores ADD CONSTRAINT FK_IngredientStores_Ingredients FOREIGN KEY(IngredientID)
REFERENCES Ingredients (IngredientID);


ALTER TABLE IngredientStores ADD CONSTRAINT FK_IngredientStores_Stores FOREIGN KEY(StoreID)
REFERENCES Stores (StoreID);