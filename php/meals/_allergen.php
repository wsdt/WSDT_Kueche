<?php
require_once '/../db_connection.php';
require_once '/../functions.php';
require_once "/../_auth.php";

/*
###################################################################################################################
###################################################################################################################
###################################################################################################################
######################## DEPRECATED ## DEPRECATED ## DEPRECATED ## DEPRECATED ## DEPRECATED########################
###################################################################################################################
###################################################################################################################
###################################################################################################################
*/
if (!empty($_POST)) {
}

class _allergen {
	private $all_kuerzel;
	private $all_kurzbeschreibung;
	private $all_langbeschreibung;
	private $cre_id; // = LAST EDITOR
	//Cre_id nicht in SELECTs usw dazu, da für jeden einsehbar, aber abspeichern wer was gespeichert hat

	public function __construct($all_kuerzel,$all_kurzbeschreibung,$all_langbeschreibung) {
		if (!empty($all_kuerzel)) { //wenn erster Parameter nicht existiert, dann wurde rest auch nicht eingegeben
			$this->setAll_Kuerzel($all_kuerzel);
			$this->setAll_Kurzbeschreibung($all_kurzbeschreibung);
			$this->setAll_Langbeschreibung($all_langbeschreibung);
		}
		$this->setCre_Id($_SESSION['username']); //username immer zuweisen
	}
	public static function __construct_empty() {
		return new self("","","");
	}

	//DB Functions
	
	public function db_loadAllAllFromDB() {
		$con = new db_connection();
		$a = 0;
		$sqlite_obj = $con->query("SELECT * FROM Allergen;"); //Nicht nach Cre_Id filtern, da Cre_Id hier nur letzter Bearbeiter, aber nicht alleiniger Besitzer

		while ($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
			$tmp_result[$a++] = $row; //Tmp_result is a nested array
		}
		$con->close();
		return empty($tmp_result) ? $tmp_result = false : $tmp_result; //Wenn Tmp_result = null, dann mach Boolean draus
	}

	public function db_insertNewAllergen() {
		$con = new db_connection();
		$success = $con->query("INSERT INTO Allergen (all_kuerzel,all_kurzbeschreibung,all_langbeschreibung,cre_id) VALUES ('".$this->getAll_Kuerzel()."','".$this->getAll_Kurzbeschreibung()."','".$this->getAll_Langbeschreibung()."','".$this->getCre_Id()."');");
		$con->close();
		return $success;
	}

	public function db_deleteAllergenById() {
		execDBStatement("DELETE FROM Allergen WHERE all_kuerzel='".$this->getAll_Kuerzel()."';");
	}

	public function db_deleteAllMyAllergen() {
		execDBStatement("DELETE FROM Allergen WHERE cre_id='".$this->getCre_Id()."';");
	}

	public function db_deleteALLAllergen() {
		execDBStatement("DELETE FROM Allergen;");
	}

	public function db_updateAllergen() {
		execDBStatement("UPDATE Allergen SET all_kurzbeschreibung='".$this->getAll_Kurzbeschreibung()."',all_langbeschreibung='".$this->getAll_Langbeschreibung."',cre_id='".$this->getCre_Id()."' WHERE all_kuerzel='".$this->getAll_Kuerzel()."';");
	}

	public function db_loadAllById() {
		$loaded_allergen = _allergen::__construct_empty();
		//LOAD 
		$con = new db_connection();
		$sqlite_obj = $con->query("SELECT * FROM Allergen WHERE all_kuerzel='".$this->getAll_Kuerzel()."';");

		while ($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
			$loaded_allergen->setAll_Kuerzel($row['all_kuerzel']);
			$loaded_allergen->setAll_Kurzbeschreibung($row['all_kurzbeschreibung']);
			$loaded_allergen->setAll_Langbeschreibung($row['all_langbeschreibung']);
			$loaded_allergen->setCre_Id($row['cre_id']);
			break;
		}
		$con->close();
		return $loaded_allergen;
	}


	//Getter/Setter
	public function getCre_Id() {
		return $this->cre_id;
	}
	public function setCre_Id($cre_id) {
		$this->cre_id = escapeString($cre_id);
	}
	public function getAll_Kuerzel() {
		return $this->all_kuerzel;
	}
	public function setAll_Kuerzel($all_kuerzel) {
		$this->all_kuerzel = escapeString($all_kuerzel);
	}
	public function getAll_Kurzbeschreibung() {
		return $this->all_kurzbeschreibung;
	}
	public function setAll_Kurzbeschreibung($all_kurzbeschreibung) {
		$this->all_kurzbeschreibung = escapeString($all_kurzbeschreibung);
	}	
	public function getAll_Langbeschreibung() {
		return $this->all_langbeschreibung;
	}
	public function setAll_Langbeschreibung($all_langbeschreibung) {
		$this->all_langbeschreibung = escapeString($all_langbeschreibung);
	}

}
	

?>