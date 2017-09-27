<?php
require_once '/../db_connection.php';
require_once '/../functions.php';
require_once "/../_auth.php";

if (!empty($_POST)) {
	if (isset($_POST['add_mea_to_db'])) {
		if (!empty($_POST['mea_titel']) && !empty($_POST['pro_id']) && !($_POST['pro_id']=="empty")) {
			$tmp_mea = _meal::__construct_empty();
			$tmp_mea->setMea_Titel($_POST['mea_titel']);
			$tmp_mea->setMea_Beschreibung($_POST['mea_beschreibung']);
			$tmp_mea->setMea_Allergene($_POST['mea_allergene']);
			$tmp_mea->setPro_Id($_POST['pro_id']);

			if($tmp_mea->db_insertNewMeal()!==false) {
				echo "$.notify('Mahlzeit gespeichert','success');var error=false;";
			} else {
				echo "$.notify('Mahlzeit konnte nicht gespeichert werden,'error');var error=true;";
			}
		} else {
			echo "$.notify('Bitte fülle die Pflichtfelder!','error');var error=true;";
		}
	} else if (isset($_POST['del_mea_from_db'])) {
		echo "var error=false;"; 
		$mea_id = $_POST['mea_id'];
		if (!empty($mea_id) && ($mea_id != 'empty')) {
			$old_mea = _meal::__construct_empty();
			$old_mea->setMea_Id($mea_id);
			$old_mea->db_deleteMealById(); 
			unset($old_mea);
			echo '$.notify("Mahlzeit aus DB gelöscht!","success");$(".slbCloseBtn").click();var error=false;';
		} else if($mea_id == 'empty') {
			echo '$.notify("Keine Mahlzeit in der Datenbank!","warn");error=true;';
		} else {
			echo '$.notify("Mahlzeit nicht gelöscht!","error");error=true;';
		}
	} else if (isset($_POST['upd_mea_to_db'])) {
		echo "var error=false;"; 

		if(!(empty($_POST['mea_id'])) && !(empty($_POST['pro_id']) && $_POST['mea_id']!="empty" && $_POST['pro_id']!="empty")) {

			$update_mea = new _meal($_POST['mea_id'],$_POST['mea_titel'],$_POST['mea_beschreibung'],$_POST['mea_allergene'],$_POST['pro_id']);
			$update_mea->db_updateMeal(); 
			unset($update_mea);
			echo '$.notify("Mahlzeit aktualisiert!","success");$(".slbCloseBtn").click();error=false;';
		} else {
			echo '$.notify("Bitte füllen Sie alle Pflichtfelder!","error");error=true;';
		}
	} else if (isset($_POST['upd_mea_formOnChange'])) {
		
		echo "var error=false;"; 
		$mea_id = $_POST['mea_id'];

		if(!(empty($mea_id)) && ($mea_id != "empty")) {
			$loaded_meal = _meal::__construct_empty();
			$loaded_meal->setMea_Id($mea_id);
			$loaded_meal = $loaded_meal->db_loadMeaById();

			//Bei Success Nichts ausgeben außer JS
			echo "document.getElementById('mea_titel').value='".$loaded_meal->getMea_Titel()."';";
			echo "document.getElementById('mea_beschreibung').value='".$loaded_meal->getMea_Beschreibung()."';";
			
			//add all Tags to form --------------------
			echo "$('#mea_allergene').tagsinput('removeAll');"; //but remove old ones
			$tags = explode(',',$loaded_meal->getMea_Allergene()); //Make to array
			foreach($tags as $tag) {
				echo "$('#mea_allergene').tagsinput('add','".$tag."');";
			}
			echo "document.getElementsByClassName('bootstrap-tagsinput')[0].lastChild.size = '1';";
			unset($tags);

			//add all Tags to Form END ----------------
			echo "document.getElementById('pro_id').value='".$loaded_meal->getPro_Id()."';";

			//Keine Success Message
		} else {
			echo '$.notify("Mahlzeit nicht gefunden!","error");error=true;';
		}
	} else { //Execute function
		if (isset($_POST['start_procedure'])) {
			$tmp_mea = _meal::__construct_empty();
			if (!isset($_POST['values'])) {
				$tmp_mea->$_POST['start_procedure'](); //dynamische Funktion
			} else {
				$tmp_mea->$_POST['start_procedure']($_POST['values']); //dynamische Funktion
			}
			unset($tmp_mea);
		}
	}
}
	

class _meal {
	private $mea_id;
	private $mea_titel;
	private $mea_beschreibung;
	private $mea_allergene; //= array, but a imploded string in db
	private $pro_id;
	private $cre_id; // = LAST EDITOR
	//Cre_id nicht in SELECTs usw dazu, da für jeden einsehbar, aber abspeichern wer was gespeichert hat

	public function __construct($mea_id,$mea_titel,$mea_beschreibung,$mea_allergene,$pro_id) {
		if (!empty($mea_id)) { //wenn erster Parameter nicht existiert, dann wurde rest auch nicht eingegeben
			if ($mea_id!=-1) { //If Mea_id -1, then do not save it
				$this->setMea_Id($mea_id);
			}
			$this->setMea_Titel($mea_titel);
			$this->setMea_Beschreibung($mea_beschreibung);
			$this->setMea_Allergene($mea_allergene); //must not be an array
			$this->setPro_Id($pro_id);
		}
		$this->setCre_Id($_SESSION['username']); //username immer zuweisen
	}
	public static function __construct_empty() {
		return new self("","","","","");
	}

	public static function __construct_From_CSV($param) {
		$tmp_arr = explode(":",$param);
		return new self($tmp_arr[0],$tmp_arr[1],$tmp_arr[2],$tmp_arr[3],$tmp_arr[4]);
	}

	//DB Functions
	public function db_printAllMeaFromDB() {
		printNestedArray($this->db_loadAllMeaFromDB(),"Keine Speisen");
	}
	
	public function db_loadAllMeaFromDB() {
		$con = new db_connection();
		$a = 0;
		$sqlite_obj = $con->query("SELECT * FROM Meal;"); //Nicht nach Cre_Id filtern, da Cre_Id hier nur letzter Bearbeiter, aber nicht alleiniger Besitzer

		while ($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
			$tmp_result[$a++] = $row; //Tmp_result is a nested array
		}
		$con->close();
		return empty($tmp_result) ? $tmp_result = false : $tmp_result; //Wenn Tmp_result = null, dann mach Boolean draus
	}

	public function db_insertNewMeal() {
		$con = new db_connection();
		$success = false;
		try {
		$success = $con->query("INSERT INTO Meal (mea_titel,mea_beschreibung,mea_allergene,pro_id,cre_id) VALUES ('".$this->getMea_Titel()."','".$this->getMea_Beschreibung()."','".$this->getMea_Allergene()."','".$this->getPro_Id()."','".$this->getCre_Id()."');");
		} catch (Exception $e) {
			echo "<script type='text/javascript'>$.notify(\"Mahlzeit '".$this->getMea_Titel()."' wurde kein\\nProduktionsplatz zugewiesen!\",\"error\");</script>";
		}

		$con->close();
		return $success;
	}

	//Foreign Key constraints are setting components automatically to null
	public function db_deleteMealById() {
		execDBStatement("DELETE FROM Meal WHERE mea_id=".$this->getMea_Id().";");
	}

	public function db_deleteAllMyMeal() {
		execDBStatement("DELETE FROM Meal WHERE cre_id='".$this->getCre_Id()."';");
	}

	public function db_deleteALLMeal() {
		execDBStatement("DELETE FROM Meal;");
	}

	public function db_updateMeal() {
		execDBStatement("UPDATE Meal SET mea_titel='".$this->getMea_Titel()."',mea_beschreibung='".$this->getMea_Beschreibung()."',mea_allergene='".$this->getMea_Allergene()."',pro_id='".$this->getPro_Id()."',cre_id='".$this->getCre_Id()."' WHERE mea_id='".$this->getMea_Id()."';");
	}

	public function db_loadMeaById() {
		$loaded_meal = _meal::__construct_empty();
		//LOAD 
		$con = new db_connection();
		$sqlite_obj = $con->query("SELECT * FROM Meal WHERE mea_id=".$this->getMea_Id().";");

		while ($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
			$loaded_meal->setMea_Id($row['mea_id']);
			$loaded_meal->setMea_Titel($row['mea_titel']);
			$loaded_meal->setMea_Beschreibung($row['mea_beschreibung']);
			$loaded_meal->setPro_Id($row['pro_id']);
			$loaded_meal->setMea_Allergene($row['mea_allergene']); 
			$loaded_meal->setCre_Id($row['cre_id']);
			break;
		}
		$con->close();
		return $loaded_meal;
	}



	//Getter/Setter
	public function getCre_Id() {
		return $this->cre_id;
	}
	public function setCre_Id($cre_id) {
		$this->cre_id = escapeString($cre_id);
	}
	public function getMea_Allergene() {
		return $this->mea_allergene;
	}
	public function setMea_Allergene($mea_allergene) {
		if (is_array($mea_allergene)) {
			$this->mea_allergene = escapeString(implode($mea_allergene));
		} else {
			$this->mea_allergene = escapeString($mea_allergene);
		}
	}
	public function getPro_Id() {
		return $this->pro_id;
	}
	public function setPro_Id($pro_id) {
		$this->pro_id = escapeString($pro_id);
	}
	public function getMea_Id() {
		return $this->mea_id;
	}
	public function setMea_Id($mea_id) {
		$this->mea_id = escapeString($mea_id);
	}
	public function getMea_Titel() {
		return $this->mea_titel;
	}
	public function setMea_Titel($mea_titel) {
		$this->mea_titel = escapeString($mea_titel);
	}
	public function getMea_Beschreibung() {
		return $this->mea_beschreibung;
	}
	public function setMea_Beschreibung($mea_beschreibung) {
		$this->mea_beschreibung = escapeString($mea_beschreibung);
	}
}

?>