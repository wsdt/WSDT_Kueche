<?php
require_once '/../db_connection.php';
require_once '/../functions.php';
require_once "/../_auth.php";

if (!empty($_POST)) {
	if (isset($_POST['refresh_ku_list'])) {
		$all_ku = _DPLKuerzel::__construct_empty();
		printNestedArray($all_ku->db_loadAllKuFromDB(),"Keine Kürzel");
		unset($all_ku);
	}
	//Speichere neuen MA in die Datenbank -----------------------
	if (isset($_POST['add_ku_to_db'])) {
		echo "var error=false;"; 
		$dpl_kuerzel = $_POST['dpl_kuerzel'];
		$dpl_kuerzelvalue = $_POST['dpl_kuerzelvalue'];
		if (!empty($dpl_kuerzelvalue) && !empty($dpl_kuerzel)) {
			$new_kuerzel = new _DPLKuerzel($dpl_kuerzel,$dpl_kuerzelvalue);
			$success = $new_kuerzel->db_insertNewKuerzel(); //If success no return value, if error then there is a warning
			unset($new_kuerzel);
			//SAVE TO DB und gib success notification aus
			if ($success === false) {
				echo '$.notify("Kürzel darf nicht bereits vergeben sein!","error");';
			} else {
				echo '$.notify("Kürzel in Datenbank gespeichert!","success");$(".slbCloseBtn").click();';
			}
		} else { //Nicht mit script zurückgeben, da AJAX diesen Code direkt im JS ausführt
			echo '$.notify("Kürzel nicht gespeichert!","error");error=true;';
		}
	}

	//Update onchange UpdateForm
	if (isset($_POST['upd_ku_formOnchange'])) {
		echo "var error=false;"; 
		$dpl_kuerzel = $_POST['dpl_kuerzel'];

		if(!(empty($dpl_kuerzel)) && ($dpl_kuerzel != "empty")) {
			$loaded_kuerzel = _DPLKuerzel::__construct_empty();
			$loaded_kuerzel->setDpl_Kuerzel($dpl_kuerzel);
			$loaded_kuerzel = $loaded_kuerzel->db_loadKuById();

			//Bei Success Nichts ausgeben außer JS
			echo "document.getElementById('dpl_kuerzelvalue').value=".intval($loaded_kuerzel->getdpl_KuerzelValue()).";";
			//Keine Success Message
		} else {
			echo '$.notify("Could not find Kürzel!","error");error=true;';
		}
	}

	//Update Kürzel
	if (isset($_POST['upd_ku_to_db'])) {
		echo "var error=false;"; 
		$dpl_kuerzel = $_POST['dpl_kuerzel'];
		$dpl_kuerzelvalue = $_POST['dpl_kuerzelvalue'];

		if(!(empty($dpl_kuerzel)) && !(empty($dpl_kuerzelvalue))) {
			$update_ku = new _DPLKuerzel($dpl_kuerzel,$dpl_kuerzelvalue);
			$update_ku->db_updateKuerzel(); 
			unset($update_ku);
			echo '$.notify("Kürzel aktualisiert!","success");$(".slbCloseBtn").click();';
		} else {
			echo '$.notify("Felder müssen gefüllt sein!","error");error=true;';
			//die(header("HTTP/1.0 404 Not Found"));
		}
	}

	//Lösche alle KU aus DB
	if (isset($_POST['del_all_ku_from_db'])) {
		echo "var error=false;"; 
		$delete_all_ku = _DPLKuerzel::__construct_empty();
		$delete_all_ku->db_deleteALLKuerzel(); //TODO: IMPORTANT: DELETE All assessments of Employee before deleting employee
		unset($delete_all_ku);
		echo '$.notify("ALLE Kürzel gelöscht!","success");$(".slbCloseBtn").click();';
	}

	//Lösche MA by ID aus Datenbank
	if (isset($_POST['del_ku_from_db'])) {
		echo "var error=false;"; 
		$dpl_kuerzel = $_POST['dpl_kuerzel'];
		if (!empty($dpl_kuerzel) && ($dpl_kuerzel != 'empty')) {
			$old_ku = _DPLKuerzel::__construct_empty();
			$old_ku->setDpl_Kuerzel($dpl_kuerzel);
			$old_ku->db_deleteKuerzelById(); 
			unset($old_ku);
			echo '$.notify("Kürzel aus DB gelöscht!","success");$(".slbCloseBtn").click();';
		} else if($dpl_kuerzel == 'empty') {
			echo '$.notify("Kein Kürzel in der Datenbank!","warn");error=true;';
		} else {
			echo '$.notify("Kürzel nicht gelöscht!","error");error=true;';
		}
	}

}



class _DPLKuerzel {
	private $dpl_kuerzel;
	private $dpl_kuerzelvalue;
	private $cre_id;

	public function __construct($dpl_kuerzel,$dpl_kuerzelvalue) {
		if (!empty($dpl_kuerzel)) { //wenn erster Parameter nicht existiert, dann wurde rest auch nicht eingegeben
			$this->setdpl_Kuerzel($dpl_kuerzel);
			$this->setdpl_Kuerzelvalue($dpl_kuerzelvalue);
		}
		$this->setCre_Id($_SESSION['username']); //username immer zuweisen
	}
	public static function __construct_empty() {
		return new self("","","","","");
	}

	//DB Functions
	public function db_loadAllKuFromDB() {
		$con = new db_connection();
		$a = 0;
		$sqlite_obj = $con->query("SELECT dpl_kuerzel,dpl_kuerzelvalue FROM DPLKuerzel WHERE cre_id='".$this->getCre_Id()."';");

		while ($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
			$tmp_result[$a++] = $row; //Tmp_result is a nested array
		}
		$con->close();
		return empty($tmp_result) ? $tmp_result = false : $tmp_result; //Wenn Tmp_result = null, dann mach Boolean draus
	}

	public function db_insertNewKuerzel() {
		$con = new db_connection();
		$success = $con->query("INSERT INTO DPLKuerzel (dpl_kuerzel,dpl_kuerzelvalue,cre_id) VALUES ('".$this->getdpl_Kuerzel()."',".$this->getdpl_KuerzelValue().",'".$this->getCre_Id()."');");
		$con->close();
		return $success;
	}

	public function db_deleteKuerzelById() {
		execDBStatement("DELETE FROM DPLKuerzel WHERE cre_id='".$this->getCre_Id()."' AND dpl_kuerzel='".$this->getdpl_Kuerzel()."';");
	}

	public function db_deleteALLKuerzel() {
		execDBStatement("DELETE FROM DPLKuerzel WHERE cre_id='".$this->getCre_Id()."';");
	}

	public function db_updateKuerzel() {
		execDBStatement("UPDATE DPLKuerzel SET dpl_kuerzelvalue=".$this->getdpl_KuerzelValue()." WHERE cre_id='".$this->getCre_Id()."' AND dpl_kuerzel='".$this->getdpl_Kuerzel()."';");
	}

	public function db_loadKuById() {
		$loaded_kuerzel = _DPLKuerzel::__construct_empty();
		//LOAD 
		$con = new db_connection();
		$sqlite_obj = $con->query("SELECT * FROM DPLKuerzel WHERE cre_id='".$this->getCre_Id()."' AND dpl_kuerzel='".$this->getdpl_Kuerzel()."';");

		while ($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
			$loaded_kuerzel->setdpl_Kuerzel($row['dpl_kuerzel']);
			$loaded_kuerzel->setdpl_Kuerzelvalue($row['dpl_kuerzelvalue']);
			$loaded_kuerzel->setCre_Id($row['cre_id']);
			break;
		}
		$con->close();
		return $loaded_kuerzel;
	}


	//Getter/Setter
	public function getCre_Id() {
		return $this->cre_id;
	}
	public function setCre_Id($cre_id) {
		$this->cre_id = escapeString($cre_id);
	}
	public function getdpl_Kuerzel() {
		return $this->dpl_kuerzel;
	}
	public function setdpl_Kuerzel($dpl_kuerzel) {
		$this->dpl_kuerzel = escapeString($dpl_kuerzel);
	}
	public function getdpl_KuerzelValue() {
		return $this->dpl_kuerzelvalue;
	}
	public function setdpl_Kuerzelvalue($dpl_kuerzelvalue) {
		$this->dpl_kuerzelvalue = escapeString($dpl_kuerzelvalue);
	}

}

?>