<html>
	<header>
		<style>
		/* Tooltip text */
		#tooltip #tooltiptext {
		    visibility: hidden !important;
		    width: 120px !important;
		    background-color: black !important;
		    color: #fff !important;
		    text-align: center !important;
		    border-radius: 6px !important;
		 
		    /* Position the tooltip text - see examples below! */
		    position: absolute !important;
		    z-index: 1 !important;
		}

		/* Show the tooltip text when you mouse over the tooltip container */
		#tooltip:hover #tooltiptext {
		    visibility: visible !important;
		}
		</style>
		<title>Calendar View</title>
	</header>
</html>


<?php
function getMealInformation() {
	$dbServer = config('dbServer');
	$dbUsername = config('dbUsername');
	$dbPassword = config('dbPassword');
	$dbDatabase = config('dbDatabase');
	mysql_connect($dbServer, $dbUsername, $dbPassword);
	mysql_select_db($dbDatabase) or die("Unable to select DB!");
	$queryDates = "SELECT meals.Name, meals.MealID, mealdates.MealDate, mealdates.MealTime, meals.Description FROM meals INNER JOIN mealdates ON mealdates.MealID = meals.MealID ORDER BY mealdates.MealDate";
	$resultDates = mysql_query($queryDates);
	$arrDates = array();
	$prevDate = "";
	$arrInfo = array();
	while ($row = mysql_fetch_assoc($resultDates)) {
		if ($prevDate != $row['MealDate'] and $prevDate != "") {
			$arrDates[$prevDate] = $arrInfo;
			$arrInfo = array();
		}
		$arrMeal = array($row['Name'], $row['MealTime'], $row['Description'], $row['MealID']);
		$arrInfo[] = $arrMeal;
		$prevDate = $row['MealDate'];
	}
		if ($prevDate != $row['MealDate'] and $prevDate != "") {
			$arrDates[$prevDate] = $arrInfo;
			$arrInfo = array();
		}
	return $arrDates;
	mysql_close();
}

function leapYear($year) {
	if ($year % 400 == 0) {
		return TRUE;
	}
	elseif ($year % 100 == 0) {
		return FALSE;
	}
	elseif ($year % 4 == 0) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

function getOffset($month, $year) {
	switch ($month) {
		case 'January':
			$month = 1;
			break;
		case 'February':
			$month = 2;
			break;
		case 'March':
			$month = 3;
			break;
		case 'April':
			$month = 4;
			break;
		case 'May':
			$month = 5;
			break;
		case 'June':
			$month = 6;
			break;
		case 'July':
			$month = 7;
			break;
		case 'August':
			$month = 8;
			break;
		case 'September':
			$month = 9;
			break;
		case 'October':
			$month = 10;
			break;
		case 'November':
			$month = 11;
			break;
		case 'December':
			$month = 12;
			break;
		default:
			return 0;
			break;
	}
	$year = (int)$year;
	$firstOfMonth = mktime(1, 1, 1, $month, 1, $year);
	$info = getdate($firstOfMonth);
	echo "<p>" . $info['wday'] . "</p>";
	return $info['wday'];
}

$dir = dirname(__FILE__);
include("$dir/defaultLang.php");
include("$dir/language.php");
include("$dir/lib.php");
include_once("$dir/header.php");

if (is_null($_POST['year'])) {
	$year = getdate();
	$year = $year['year'];
}
else {
	$year = $_POST['year'];
}
if (is_null($_POST['month'])) {
	$month = getdate();
	$month = $month['month'];
}
else {
	$month = $_POST['month'];
}

$arrMonths = array("January" => array(1, 31), "February" => array(2, 28), "March" => array(3, 31), "April" => array(4, 30), "May" => array(5, 31), "June" => array(6, 30), "July" => array(7, 31), "August" => array(8, 31), "September" => array(9, 30), "October" => array(10, 31), "November" => array(11, 30), "December" => array(12, 31));

if (leapYear($year)) {
	$arrMonths['February'][1] = 29;
}

$arrAbr = array("Breakfast" => "B", "Lunch" => "L", "Dinner" => "D", "Brunch" => "Br", "Snack" => "S", "Dessert" => "De", "Appetizer" => "Ap", "Anytime" => "A");

echo '<div style="width: 500px; margin:0 auto;">' . "\r\n\t";
echo '<form action="calendar.php" method="post">' . "\r\n\t\t";
echo 'Year: ';
echo "<input type=\"number\" maxlength=\"4\" name=\"year\" value=\"$year\">&nbsp;&nbsp;";
echo	'Month: ';
echo	"<select name=\"month\">";
foreach ($arrMonths as $m => $info) {
	if ($month == $m) {
		echo "<option value=\"$m\" selected>$m</option>";
	}
	else {
		echo "<option value=\"$m\">$m</option>";
	}
}
echo	'</select>&nbsp;&nbsp;';
echo	'<input type="submit" name="submit" value="Go">';
echo '</form>';
echo '</div>';

$arrInfo = getMealInformation();

echo "<table align=\"center\" border=\"5\" id=\"calendarTB\"><tr><td colspan=\"7\" text-align=\"center\"><em><strong>$month</strong></em></th></tr>";



$offset = getOffset($month, $year);

for ($counter = 0; $counter < $offset; $counter++) {
	echo "<td id=\"calendarTD\"><div class=\"content\"><br></div></td>";
}
for ($day=1; $day <= $arrMonths[$month][1]; $day++) { 
	if (($offset + $day) % 7 == 1) {
		echo "<tr>";
	}
	echo "<td id=\"calendarTD\"><div class=\"content\">$day<br>";
	$key = "$year-" . sprintf("%'.02u", $arrMonths[$month][0]) . "-" . sprintf("%'.02u", $day);
	if (array_key_exists($key, $arrInfo)) {
		foreach ($arrInfo[$key] as $k => $meal) {
			echo '<div id="tooltip">';
			echo '<form name="calDetail" action="calDetail.php" method="post" target="_blank">';
			echo "<input type=\"hidden\" name=\"MealID\" value=\"$meal[3]\">";
			echo "<input id=\"calDetail\" type=\"submit\" name=\"calDetail\" value=\"";
			echo $arrAbr[$meal[1]] . ": $meal[0]\"></form>";
			echo '<div id="tooltiptext">' . $meal[2] . '</div></div>';
		}
	}
	echo "</div></td>";

	if (($offset + $day) % 7 == 0) {
		echo "</tr>";
	}
}

$i = 1;
foreach ($arrMonths as $m => $n) {
	if ($n[0] == $arrMonths[$month][0] + 1) {
		$month = $m;
		break;
	}
}
if ($month == "December") {
	$month = "January";
	$year = $year + 1;
}
if (($offset + $day - 1) % 7 != 0) {
	echo "<td id=\"calendarTD\"><div class=\"content\"><strong>$month</strong> 1";
	$key = "$year-" . sprintf("%'.02u", $arrMonths[$month][0]) . "-" . sprintf("%'.02u", $i);
	if (array_key_exists($key, $arrInfo)) {
		foreach ($arrInfo[$key] as $k => $meal) {
			echo '<div id="tooltip">';
			echo $arrAbr[$meal[1]] . ": $meal[0]<br>";
			echo '<div id="tooltiptext">' . $meal[2] . '</div></div>';
		}
	}
	echo "</div></td>";
	$day ++;
	$i ++;
}
while (($offset + $day - 1) % 7 != 0) {
	echo "<td id=\"calendarTD\"><div class=\"content\">$i";
	$key = "$year-" . sprintf("%'.02u", $arrMonths[$month][0]) . "-" . sprintf("%'.02u", $i);
	if (array_key_exists($key, $arrInfo)) {
		foreach ($arrInfo[$key] as $k => $meal) {
			echo '<div id="tooltip">';
			echo $arrAbr[$meal[1]] . ": $meal[0]<br>";
			echo '<div id="tooltiptext">' . $meal[2] . '</div></div>';
		}
	}
	echo "</div></td>";
	if ($day % 7 == 0) {
		echo "</tr>";
		break;
	}
	$day ++;
	$i ++;
}
echo "</table>";


include_once("$dir/footer.php");
?>