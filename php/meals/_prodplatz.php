<?php
require_once "/../_auth.php";
require_once "/../db_connection.php";
require_once "/../functions.php";


if (!empty($_POST)) {
	if (isset($_POST['add_pro_to_db'])) {
		$tmp_prod = _prodplatz::__construct_empty();
		$tmp_prod->setPro_Id($_POST['pro_id']);
		$tmp_prod->setPro_Beschreibung($_POST['pro_beschreibung']);
		if($tmp_prod->db_insertNewProdplatz()!==false) {
			echo "$.notify('Produktionsplatz gespeichert','success');var error=false;";
		}
	} else if (isset($_POST['del_pro_from_db'])) {
		echo "var error=false;"; 
		$pro_id = $_POST['pro_id'];
		if (!empty($pro_id) && ($pro_id != 'empty')) {
			$old_pro = _prodplatz::__construct_empty();
			$old_pro->setPro_Id($pro_id);
			$old_pro->db_deleteProById(); 
			unset($old_pro);
			echo '$.notify("Produktionsplatz aus DB gelöscht!","success");$(".slbCloseBtn").click();';
		} else if($pro_id == 'empty') {
			echo '$.notify("Kein Produktionsplatz in der Datenbank!","warn");error=true;';
		} else {
			echo '$.notify("Produktionsplatz nicht gelöscht!","error");error=true;';
		}
	} else if (isset($_POST['upd_pro_to_db'])) {
		echo "var error=false;"; 
		$pro_id = $_POST['pro_id'];
		$pro_beschreibung = $_POST['pro_beschreibung'];

		if(!(empty($pro_id)) && !(empty($pro_beschreibung))) {
			$update_pro = new _prodplatz($pro_id,$pro_beschreibung);
			$update_pro->db_updateProdplatz(); 
			unset($update_pro);
			echo '$.notify("Produktionsplatz aktualisiert!","success");$(".slbCloseBtn").click();';
		} else {
			echo '$.notify("Bitte füllen Sie alle Pflichtfelder!","error");error=true;';
		}
	} else if (isset($_POST['upd_pro_formOnChange'])) {
		
		echo "var error=false;"; 
		$pro_id = $_POST['pro_id'];

		if(!(empty($pro_id)) && ($pro_id != "empty")) {
			$loaded_pro = _prodplatz::__construct_empty();
			$loaded_pro->setPro_Id($pro_id);
			$loaded_pro = $loaded_pro->db_loadProById();

			//Bei Success Nichts ausgeben außer JS
			echo "document.getElementById('pro_beschreibung').value='".$loaded_pro->getPro_Beschreibung()."';";

			//Keine Success Message
		} else {
			echo '$.notify("Produktionsplatz nicht gefunden!","error");error=true;';
		}
	} else { //Execute function
		if (isset($_POST['start_procedure'])) {
			$tmp_prod = _prodplatz::__construct_empty();
			if (!isset($_POST['values'])) {
				$tmp_prod->$_POST['start_procedure'](); //dynamische Funktion
			} else {
				$tmp_prod->$_POST['start_procedure']($_POST['values']); //dynamische Funktion
			}
			unset($tmp_prod);
		}
	}
}

class _prodplatz {
	private $pro_id;
	private $pro_beschreibung;
	private $cre_id; // = LAST EDITOR
	//Cre_id nicht in SELECTs usw dazu, da für jeden einsehbar, aber abspeichern wer was gespeichert hat

	public function __construct($pro_id,$pro_beschreibung) {
		if (!empty($pro_id)) { //wenn erster Parameter nicht existiert, dann wurde rest auch nicht eingegeben
			$this->setPro_Id($pro_id);
			$this->setPro_Beschreibung($pro_beschreibung);
		}
		$this->setCre_Id($_SESSION['username']); //username immer zuweisen
	}
	public static function __construct_empty() {
		return new self("","");
	}

	//DB Functions
	public function db_printAllProFromDB() {
		printNestedArray($this->db_loadAllProFromDB(),"Keine Produktionsplätze");
	}

	public function db_loadAllProFromDB() {
		$con = new db_connection();
		$a = 0;
		$sqlite_obj = $con->query("SELECT * FROM Prodplatz;"); //Nicht nach Cre_Id filtern, da Cre_Id hier nur letzter Bearbeiter, aber nicht alleiniger Besitzer

		while ($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
			$tmp_result[$a++] = $row; //Tmp_result is a nested array
		}
		$con->close();
		return empty($tmp_result) ? $tmp_result = false : $tmp_result; //Wenn Tmp_result = null, dann mach Boolean draus
	}

	public function db_insertNewProdplatz() {
		$con = new db_connection();
		try {
			if (!($this->getPro_Id()) || !($this->getCre_Id())) { //Wenn leer dann automatisch falsch
				//throw new Exception();
			}

			$success = $con->query("INSERT INTO Prodplatz (pro_id,pro_beschreibung,cre_id) VALUES ('".$this->getPro_Id()."','".$this->getPro_Beschreibung()."','".$this->getCre_Id()."');");
		} catch (Exception $e) {
			echo "$.notify('Produktionsplatz nicht gespeichert!','error');var error=true";
			$success = false;
		}
		$con->close();
		return $success;
	}

	public function db_deleteProById() {
		execDBStatement("DELETE FROM Prodplatz WHERE pro_id='".$this->getPro_Id()."';");
	}

	public function db_deleteAllMyPro() {
		execDBStatement("DELETE FROM Prodplatz WHERE cre_id='".$this->getCre_Id()."';");
	}

	public function db_deleteALLPro() {
		execDBStatement("DELETE FROM Prodplatz;");
	}

	public function db_updateProdplatz() {
		execDBStatement("UPDATE Prodplatz SET pro_beschreibung='".$this->getPro_Beschreibung()."',cre_id='".$this->getCre_Id()."' WHERE pro_id='".$this->getPro_Id()."';");
	}

	public function db_loadProById() {
		$loaded_pro = _prodplatz::__construct_empty();
		//LOAD 
		$con = new db_connection();
		$sqlite_obj = $con->query("SELECT * FROM Prodplatz WHERE pro_id='".$this->getPro_Id()."';");

		while ($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
			$loaded_pro->setPro_Id($row['pro_id']);
			$loaded_pro->setPro_Beschreibung($row['pro_beschreibung']);
			$loaded_pro->setCre_Id($row['cre_id']);
			break;
		}
		$con->close();
		return $loaded_pro;
	}


	//Getter/Setter
	public function getCre_Id() {
		return $this->cre_id;
	}
	public function setCre_Id($cre_id) {
		$this->cre_id = escapeString($cre_id);
	}
	public function getPro_Id() {
		return $this->pro_id;
	}
	public function setPro_Id($pro_id) {
		$this->pro_id = escapeString($pro_id);
	}
	public function getPro_Beschreibung() {
		return $this->pro_beschreibung;
	}
	public function setPro_Beschreibung($pro_beschreibung) {
		$this->pro_beschreibung = escapeString($pro_beschreibung);
	}	
}

?>