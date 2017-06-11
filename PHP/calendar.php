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
	</header>
</html>


<?php
function getMealInformation() {
	$usrn = "alexhrao";
	$pass = "Arodponyboy678";
	$db = "meal_planner_db";
	mysql_connect("mysql20.ezhostingserver.com:3306", $usrn, $pass);
	mysql_select_db($db) or die("Unable to select DB!");
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

function offsetDay($month) {

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
$offset = 4;
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