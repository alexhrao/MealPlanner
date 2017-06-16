runLocal: 
	rm -f -R /Applications/XAMPP/xamppfiles/htdocs/*
	cp ~/Documents/MealPlanner/Configurations/Development.php ~/Documents/MealPlanner/PHP/config.php
	cp -R ~/Documents/MealPlanner/PHP/ /Applications/XAMPP/xamppfiles/htdocs/
	/usr/bin/open -a "/Applications/Google Chrome.app" 'http://localhost:8080'

checkpoint:
	cp ~/Documents/MealPlanner/Configurations/Production.php ~/Documents/MealPlanner/PHP/config.php
	git add *
	git commit
	git push

deploy:
	git clone "https://github.com/alexhrao/MealPlanner.git"
	cd MealPlanner
	git checkout Production