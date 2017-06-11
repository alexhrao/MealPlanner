<?php
	$dir = dirname(__FILE__);
	include("$dir/defaultLang.php");
	include("$dir/language.php");
	include("$dir/lib.php");
	include_once("$dir/header.php");

# Testing for links as forms!

if (isset($_POST['testName'])) {
	echo "<p>Hello World!</p>";
}
else {
	echo "<p>SHIT</p>";
}
?>
<form target="_blank" action="test.php" method="post">
<input type="hidden" value="Hello!" name="testName">
<input id="button" type="submit" value="submit" name="hello">
</form>