<?php
require_once "/../_auth.php";
require_once "/../db_connection.php";
require_once "/../functions.php";


if (!empty($_POST)) {
	if (isset($_POST['show_dienstplan'])) {
		show_dienstplan($_POST['year'],$_POST['month']);
	}
}


// METHODS #############################################################
function show_dienstplan($year,$month) {
	$data_array = loadDPFromDb($year,$month);

	//Auch Button für DP-Wechsel anzeigen
	echo "<div class='table'>";
		echo "<div class='table-row'>";
			echo "<div class='table-caption'>NAME</div>";
			//Generate Heading
			for ($i = 1;$i <= cal_days_in_month(CAL_GREGORIAN, $month, $year);$i++) {
				echo "<div class='table-caption'>$i (".date("D",strtotime($i.".".$month.".".$year)).")</div>";
			}
		echo "</div>";

		
		
		/*for ($j = 0;$j < count($data_array);$j++) {
			echo "<div class='table-row'>";
			for ($k = 1;$k <= cal_days_in_month(CAL_GREGORIAN, $month, $year);$k++) {

			}
			echo "</div>";
		}*/



	echo "</div>";

}

//DB FUNCTIONS #################################################
function loadDPFromDb($year,$month) {
	$con = new db_connection();
	$sqlite_obj = $con->query("SELECT * FROM Plantag WHERE cre_id='".$_SESSION['username']."' and pl_jahr=".$year." and pl_monat=".$month." ORDER BY pl_emp_id ASC,pl_jahr ASC,pl_monat ASC,pl_tag ASC;");

	//emp_id = zeile
	//tage sind spalten
	//eine db_row = eine zelle !
	$i=0;
	while ($cell = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
		$dp[$i++] = $cell;
	}
	$con->close();
	return $dp;
}


function loadDPListAsArray() {
	$con = new db_connection();
	$sqlite_obj = $con->query("SELECT DISTINCT pl_monat,pl_jahr FROM Plantag WHERE cre_id='".$_SESSION['username']."';");
	$a = 0;
	while ($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
		$tmp_result[$a++] = $row; //nested array
	}
	$con->close();
	return empty($tmp_result) ? false : $tmp_result;
}




?>