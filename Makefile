run: 
	rm -f -R /Applications/XAMPP/xamppfiles/htdocs/*
	cp -R ~/Documents/MealPlanner/PHP/ /Applications/XAMPP/xamppfiles/htdocs/
	cp ~/Documents/MealPlanner/Configurations/Development.php /Applications/XAMPP/xamppfiles/htdocs/config.php
	/usr/bin/open -a "/Applications/Google Chrome.app"

checkpoint:
	git add *
	git commit
	git push

deploy:
	git clone "https://github.com/alexhrao/MealPlanner.git"
	cd MealPlanner
	git checkout Production