USE meal_planner_db;

CREATE TABLE ingredientstores(
	IngredientStoreID int auto_increment NOT NULL,
	IngredientID int NOT NULL,
	StoreID int NOT NULL,
	Cost decimal(13, 4) NULL,
 CONSTRAINT PK_IngredientStores PRIMARY KEY CLUSTERED 
(
	IngredientStoreID ASC
));
ALTER TABLE ingredientstores ADD CONSTRAINT FK_IngredientStores_Ingredients FOREIGN KEY(IngredientID)
	REFERENCES ingredients (IngredientID);
ALTER TABLE ingredientstores ADD CONSTRAINT FK_IngredientStores_Stores FOREIGN KEY(StoreID)
	REFERENCES stores (StoreID);