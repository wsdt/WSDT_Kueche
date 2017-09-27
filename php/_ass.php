<?php
if(!isset($_SESSION)) {session_start();}
//require_once "_auth.php";

require_once 'db_connection.php';
require_once 'functions.php';

//AJAX HANDLING
//ASS Liste aktualisieren und aufrufen --------------------------
if (!empty($_POST)) {
	if (isset($_POST['refresh_ass_list'])) { 
		$all_ass = _ass::__construct_empty();
		printNestedArray($all_ass->db_loadAllAssFromDB(),"Keine Assessments");
		unset($all_emp);
	} //Erlaube multiple Anfragen, also statt else if nur if 

	if (isset($_POST['ass_add_to_db'])) {
		echo "var error=false;";
		$ass_add_amount_stars = $_POST['ass_add_amount_stars'];
		$ass_add_description = $_POST['ass_add_description'];
		$ass_add_emp_id = $_POST['ass_add_emp_id'];
		if (!empty($ass_add_amount_stars) && !empty($ass_add_description) && !empty($ass_add_emp_id) && $ass_add_emp_id!="empty") {
			$new_assessment = new _ass(-1,$ass_add_amount_stars,$ass_add_description,"",$ass_add_emp_id); //-1 means that i want auto_increment
			$success = $new_assessment->db_insertNewAssessment(); //If success no return value, if error then there is a warning
			unset($new_assessment);
			//SAVE TO DB und gib success notification aus
			echo '$.notify("Assessment in Datenbank gespeichert!","success");$(".slbCloseBtn").click();';
		} else { //Nicht mit script zurückgeben, da AJAX diesen Code direkt im JS ausführt
			echo '$.notify("Assessment nicht gespeichert!","error");error=true;';
		}
	} 

	//Lösche alle Ass aus DB
	if (isset($_POST['del_all_ass_from_db'])) {
		echo "var error=false;";
		$delete_all_ass = _ass::__construct_empty();
		$delete_all_ass->db_deleteALLAssessments(); //TODO: IMPORTANT: DELETE All assessments of Employee before deleting employee
		unset($delete_all_ass);
		echo '$.notify("ALLE Assessments gelöscht!","success");$(".slbCloseBtn").click();';
	}

	//Lösche ASS by ID aus Datenbank
	if (isset($_POST['del_ass_from_db'])) {
		echo "var error=false;"; 
		$ass_id = $_POST['ass_id'];
		if (!empty($ass_id) && ($ass_id != 'empty')) {
			$old_assessment = _ass::__construct_empty();
			$old_assessment->setAss_Id($ass_id);
			$old_assessment->db_deleteAssessmentById(); //TODO: IMPORTANT: DELETE All assessments of Employee before deleting employee
			unset($old_assessment);
			echo '$.notify("Assessment aus DB gelöscht!","success");$(".slbCloseBtn").click();';
		} else if($emp_id == 'empty') {
			echo '$.notify("Keine Bewertung in der Datenbank!","warn");error=true;';
		} else {
			echo '$.notify("Assessment nicht gelöscht!","error");error=true;';
		}
	}




}


class _ass {
	private $ass_id;
	private $ass_amount_stars;
	private $ass_description;
	private $ass_zeitpunkt;
	private $ass_emp_id;
	private $cre_id;

	public function __construct($ass_id,$ass_amount_stars,$ass_description,$ass_zeitpunkt,$ass_emp_id) {
		if (!empty($ass_id)) { //wenn erster Parameter nicht existiert, dann wurde rest auch nicht eingegeben
			$this->setAss_Id($ass_id);
			$this->setAss_Amount_Stars($ass_amount_stars);
			$this->setAss_Description($ass_description);
			$this->setAss_Zeitpunkt($ass_zeitpunkt);
			$this->setAss_Emp_Id($ass_emp_id);
		}
		$this->setCre_Id($_SESSION['username']); //username immer zuweisen
	}

	public static function __construct_empty() {
		return new self("","","","","");
	}


	//DB Functions
	public function db_loadAllAssFromDB() {
		$con = new db_connection();
		$a = 0;
		$sqlite_obj = $con->query("SELECT ass_emp_id,emp_first_name,emp_last_name,ass_id,ass_amount_stars,ass_description,ass_zeitpunkt FROM Assessment as A INNER JOIN Employee as E on A.ass_emp_id = E.emp_id WHERE E.cre_id='".$this->getCre_Id()."' ORDER BY ass_id DESC;");
		while ($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
			$tmp_result[$a++] = $row; //Tmp_result is a nested array
		}
		$con->close();
		return empty($tmp_result) ? $tmp_result = false : $tmp_result; //Wenn Tmp_result = null, dann mach Boolean draus
	}

	public function db_insertNewAssessment() {
		$con = new db_connection();
		$success = $con->query("INSERT INTO Assessment (ass_amount_stars,ass_description,ass_emp_id,cre_id) VALUES ('".$this->getAss_Amount_Stars()."','".$this->getAss_Description()."','".$this->getAss_Emp_Id()."','".$this->getCre_Id()."');");
		$con->close();
		return $success;
	}

	public function db_deleteAssessmentById() {
		execDBStatement("DELETE FROM Assessment WHERE cre_id='".$this->getCre_Id()."' AND ass_id=".$this->getAss_Id());
	}

	public function db_deleteALLAssessments() {
		execDBStatement("DELETE FROM Assessment WHERE cre_id='".$this->getCre_Id()."'");
	}

	public function db_updateAssessment() { //unused
		execDBStatement("UPDATE Assessment SET ass_amount_stars='".$this->getAss_Amount_Stars()."',ass_description='".$this->getAss_Description()."',ass_emp_id='".$this->getAss_Emp_Id()."' WHERE ass_id=".$this->getAss_Id()." AND cre_id='".$this->getCre_Id()."';");
	}

	public function db_loadAssById() {
		$loaded_assessment = _ass::__construct_empty();
		//LOAD 
		$con = new db_connection();
		$sqlite_obj = $con->query("SELECT * FROM Assessment WHERE cre_id='".$this->getCre_Id()."' AND ass_id=".$this->getAss_Id());

		while ($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
			$loaded_assessment->setAss_Id($row['ass_id']);
			$loaded_assessment->setAss_Amount_Stars($row['ass_amount_stars']);
			$loaded_assessment->setAss_Description($row['ass_description']);
			$loaded_assessment->setAss_Zeitpunkt($row['ass_zeitpunkt']);
			$loaded_assessment->setAss_Emp_Id($row['ass_emp_id']);
			$loaded_assessment->setCre_Id($row['cre_id']);
			break;
		}
		$con->close();
		return $loaded_assessment;
	}


	//GETTER u. SETTER
	public function getCre_Id() {
		return $this->cre_id;
	}
	public function setCre_Id($cre_id) {
		$this->cre_id = $cre_id;
	}
	public function getAss_Id() {
		return $this->ass_id;
	}
	public function setAss_Id($ass_id) {
		$this->ass_id = $ass_id;
	}
	public function getAss_Amount_Stars() {
		return $this->ass_amount_stars;
	}
	public function setAss_Amount_Stars($ass_amount_stars) {
		$this->ass_amount_stars = $ass_amount_stars;
	}
	public function getAss_Description() {
		return $this->ass_description;
	}
	public function setAss_Description($ass_description) {
		$this->ass_description = $ass_description;
	}
	public function getAss_Zeitpunkt() {
		return $this->ass_zeitpunkt;
	}
	public function setAss_Zeitpunkt($ass_zeitpunkt) {
		$this->ass_zeitpunkt = $ass_zeitpunkt;
	}
	public function getAss_Emp_Id() {
		return $this->ass_emp_id;
	}
	public function setAss_Emp_Id($ass_emp_id) {
		$this->ass_emp_id = $ass_emp_id;
	}


}



















?>