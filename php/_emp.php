<?php
//if(!isset($_SESSION)) {session_start();}
require_once "_auth.php";

require_once 'db_connection.php';
require_once 'functions.php';

//AJAX HANDLING
//MA Liste aktualisieren und aufrufen --------------------------
if (!empty($_POST)) {
	if (isset($_POST['refresh_emp_list'])) { 
		$all_emp = _emp::__construct_empty();
		printNestedArray($all_emp->db_loadAllEmpFromDB(),"Keine Mitarbeiter");
		unset($all_emp);
	} //Erlaube multiple Anfragen, also statt else if nur if 

	//Speichere neuen MA in die Datenbank -----------------------
	if (isset($_POST['add_emp_to_db'])) {
		echo "var error=false;"; 
		$vname = $_POST['first_name'];
		$nname = $_POST['last_name'];
		$email = $_POST['email'];
		$phone = $_POST['phone'];
		if (!empty($vname) && !empty($nname)) {
			$new_employee = new _emp(-1,$vname,$nname,$email,$phone); //-1 means that i want auto_increment
			$success = $new_employee->db_insertNewEmployee(); //If success no return value, if error then there is a warning
			unset($new_employee);
			//SAVE TO DB und gib success notification aus
			echo '$.notify("MA in Datenbank gespeichert!","success");$(".slbCloseBtn").click();';
		} else { //Nicht mit script zurückgeben, da AJAX diesen Code direkt im JS ausführt
			echo '$.notify("MA nicht gespeichert!","error");error=true;';
		}
	}

	//Lösche MA by ID aus Datenbank
	if (isset($_POST['del_emp_from_db'])) {
		echo "var error=false;"; 
		$emp_id = $_POST['emp_id'];
		if (!empty($emp_id) && ($emp_id != 'empty')) {
			$old_employee = _emp::__construct_empty();
			$old_employee->setEmp_Id($emp_id);
			$old_employee->db_deleteEmployeeById(); //TODO: IMPORTANT: DELETE All assessments of Employee before deleting employee
			unset($old_employee);
			echo '$.notify("MA aus DB gelöscht!","success");$(".slbCloseBtn").click();';
		} else if($emp_id == 'empty') {
			echo '$.notify("Kein Mitarbeiter in der Datenbank!","warn");error=true;';
		} else {
			echo '$.notify("MA nicht gelöscht!","error");error=true;';
		}
	}

	//Lösche alle MA aus DB
	if (isset($_POST['del_all_emp_from_db'])) {
		echo "var error=false;"; 
		$delete_all_emp = _emp::__construct_empty();
		$delete_all_emp->db_deleteALLEmployees(); //TODO: IMPORTANT: DELETE All assessments of Employee before deleting employee
		unset($delete_all_emp);
		echo '$.notify("ALLE Mitarbeiter gelöscht!","success");$(".slbCloseBtn").click();';
	}

	//Update MA
	if (isset($_POST['upd_emp_to_db'])) {
		echo "var error=false;"; 
		$emp_id = $_POST['empupd_id'];
		$emp_first_name = $_POST['empupd_firstname'];
		$emp_last_name = $_POST['empupd_lastname'];
		$emp_email = $_POST['empupd_email'];
		$emp_phone = $_POST['empupd_phone'];

		if(!(empty($emp_first_name)) && !(empty($emp_last_name))) {
			$update_emp = new _emp($emp_id,$emp_first_name,$emp_last_name,$emp_email,$emp_phone);
			$update_emp->db_updateEmployee(); 
			unset($update_emp);
			echo '$.notify("Mitarbeiter aktualisiert!","success");$(".slbCloseBtn").click();';
		} else {
			echo '$.notify("VName/NName sind Pflichtfelder!","error");error=true;';
			//die(header("HTTP/1.0 404 Not Found"));
		}
	}

	//Update onchange UpdateForm
	if (isset($_POST['upd_emp_formOnchange'])) {
		echo "var error=false;"; 
		$emp_id = $_POST['empupd_id'];

		if(!(empty($emp_id)) && ($emp_id != "empty")) {
			$loaded_employee = _emp::__construct_empty();
			$loaded_employee->setEmp_Id($emp_id);
			$loaded_employee = $loaded_employee->db_loadEmplById();

			//Bei Success Nichts ausgeben außer JS
			echo "document.getElementById('empupd_id').value=".$loaded_employee->getEmp_Id().";";
			echo "document.getElementById('empupd_first_name').value='".$loaded_employee->getEmp_First_Name()."';";
			echo "document.getElementById('empupd_last_name').value='".$loaded_employee->getEmp_Last_Name()."';";
			echo "document.getElementById('empupd_email').value='".$loaded_employee->getEmp_Email()."';";
			echo "document.getElementById('empupd_phone').value='".$loaded_employee->getEmp_Phone()."';";

			//Keine Success Message
		} else {
			echo '$.notify("Could not find Employee!","error");error=true;';
		}
	}

}



//AJAX HANDLING END -- START of Class ------------------------------------------------------
class _emp {
	private $emp_id;
	private $emp_first_name;
	private $emp_last_name;
	private $emp_email;
	private $emp_phone;
	private $cre_id;

	public function __construct($emp_id,$emp_first_name,$emp_last_name,$emp_email,$emp_phone) {
		if (!empty($emp_id)) { //wenn erster Parameter nicht existiert, dann wurde rest auch nicht eingegeben
			$this->setEmp_Id($emp_id);
			$this->setEmp_First_Name($emp_first_name);
			$this->setEmp_Last_Name($emp_last_name);
			$this->setEmp_Email($emp_email);
			$this->setEmp_Phone($emp_phone);
		}
		$this->setCre_Id($_SESSION['username']); //username immer zuweisen
	}
	public static function __construct_empty() {
		return new self("","","","","");
	}


	//DB Functions
	public function db_loadAllEmpFromDB() {
		$con = new db_connection();
		$a = 0;
		$sqlite_obj = $con->query("SELECT emp_id,emp_first_name,emp_last_name,emp_email,emp_phone FROM Employee WHERE cre_id='".$this->getCre_Id()."';");

		while ($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
			$tmp_result[$a++] = $row; //Tmp_result is a nested array
		}
		$con->close();
		return empty($tmp_result) ? false : $tmp_result; //Wenn Tmp_result = null, dann mach Boolean draus
	}

	public function db_insertNewEmployee() {
		$con = new db_connection();
		$success = $con->query("INSERT INTO Employee (emp_first_name,emp_last_name,emp_email,emp_phone,cre_id) VALUES ('".$this->getEmp_First_Name()."','".$this->getEmp_Last_Name()."','".$this->getEmp_Email()."','".$this->getEmp_Phone()."','".$this->getCre_Id()."');");
		$con->close();
		return $success;
	}

	public function db_deleteEmployeeById() {
		execDBStatement("DELETE FROM Employee WHERE cre_id='".$this->getCre_Id()."' AND emp_id=".$this->getEmp_Id());
		execDBStatement("DELETE FROM Assessment WHERE cre_id='".$this->getCre_Id()."' AND ass_emp_id=".$this->getEmp_Id());
	}

	public function db_deleteALLEmployees() {
		execDBStatement("DELETE FROM Employee WHERE cre_id='".$this->getCre_Id()."'");
		execDBStatement("DELETE FROM Assessment WHERE cre_id='".$this->getCre_Id()."'"); //Kann keine Assessments ohne Employees geben
	}

	public function db_updateEmployee() {
		execDBStatement("UPDATE Employee SET emp_first_name='".$this->getEmp_First_Name()."',emp_last_name='".$this->getEmp_Last_Name()."',emp_email='".$this->getEmp_Email()."',emp_phone='".$this->getEmp_Phone()."' WHERE cre_id='".$this->getCre_Id()."' AND emp_id=".$this->getEmp_Id().";");
	}

	public function db_loadEmplById() {
		$loaded_employee = _emp::__construct_empty();
		//LOAD 
		$con = new db_connection();
		$sqlite_obj = $con->query("SELECT * FROM Employee WHERE cre_id='".$this->getCre_Id()."' AND emp_id=".$this->getEmp_Id());

		while ($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
			$loaded_employee->setEmp_Id($row['emp_id']);
			$loaded_employee->setEmp_First_Name($row['emp_first_name']);
			$loaded_employee->setEmp_Last_Name($row['emp_last_name']);
			$loaded_employee->setEmp_Email($row['emp_email']);
			$loaded_employee->setEmp_Phone($row['emp_phone']);
			$loaded_employee->setCre_Id($row['cre_id']);
			break;
		}
		$con->close();
		return $loaded_employee;
	}


	//GETTER u. SETTER
	public function getCre_Id() {
		return $this->cre_id;
	}
	public function setCre_Id($cre_id) {
		$this->cre_id = escapeString($cre_id);
	}
	public function getEmp_Id() {
		return $this->emp_id;
	}
	public function setEmp_Id($emp_id) {
		$this->emp_id = escapeString($emp_id);
	}
	public function getEmp_First_Name() {
		return $this->emp_first_name;
	}
	public function setEmp_First_Name($emp_first_name) {
		$this->emp_first_name = escapeString($emp_first_name);
	}
	public function getEmp_Last_Name() {
		return $this->emp_last_name;
	}
	public function setEmp_Last_Name($emp_last_name) {
		$this->emp_last_name = escapeString($emp_last_name);
	}
	public function getEmp_Email() {
		return $this->emp_email;
	}
	public function setEmp_Email($emp_email) {
		$this->emp_email = escapeString($emp_email);
	}
	public function getEmp_Phone() {
		return $this->emp_phone;
	}
	public function setEmp_Phone($emp_phone) {
		$this->emp_phone = escapeString($emp_phone);
	}


}
?>