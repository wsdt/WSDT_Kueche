<?php
require_once '/../db_connection.php';
require_once '/../functions.php';
require_once "/../_auth.php";


if (!empty($_POST)) {
	if (isset($_POST['add_mmo_to_db'])) {
		if (!empty($_POST['pln_id']) && $_POST['pln_id']!="empty" &&
			!empty($_POST['tgz_id']) && $_POST['tgz_id']!="empty" &&
			!empty($_POST['wtg_id']) && !($_POST['wtg_id']=="empty") &&
			!empty($_POST['kpn_id_1']) && $_POST['kpn_id_1']!="empty") {
			$tmp_mmo = _meal_monitor::__construct_empty();
			$tmp_mmo->setPln_Id($_POST['pln_id']);
			$tmp_mmo->setTgz_Id($_POST['tgz_id']);
			$tmp_mmo->setWtg_Id($_POST['wtg_id']);
			$tmp_mmo->setKpn_Id_1($_POST['kpn_id_1']);
			$tmp_mmo->setKpn_Id_2($_POST['kpn_id_2']);
			$tmp_mmo->setKpn_Id_3($_POST['kpn_id_3']);
			$tmp_mmo->setKpn_Id_4($_POST['kpn_id_4']);
			$tmp_mmo->setMmo_Condiments($_POST['mmo_condiments']);
			$tmp_mmo->setMmo_Garnitur($_POST['mmo_garnitur']);
			$tmp_mmo->setMmo_Gewicht_Gramm($_POST['mmo_gewicht_gramm']);

			if($tmp_mmo->db_insertNewMmo()!==false) {
				echo "$.notify('Mahlzeit-Detail gespeichert','success');var error=false;";
			} else {
				echo "$.notify('Mahlzeit-Detail konnte nicht gespeichert werden,'error');var error=true;";
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
			$tmp_mea = _meal_monitor::__construct_empty();
			if (!isset($_POST['values'])) {
				$tmp_mea->$_POST['start_procedure'](); //dynamische Funktion
			} else {
				$tmp_mea->$_POST['start_procedure']($_POST['values']); //dynamische Funktion
			}
			unset($tmp_mea);
		}
	}
}
	

class _meal_monitor {
	private $mmo_id;
	private $pln_id;
	private $tgz_id;
	private $wtg_id;
	private $kta_id;
	private $kpn_id_1;
	private $kpn_id_2;
	private $kpn_id_3;
	private $kpn_id_4;
	private $mmo_condiments;
	private $mmo_garnitur;
	private $mmo_gewicht_gramm;
	private $cre_id; // = LAST EDITOR
	//Cre_id nicht in SELECTs usw dazu, da für jeden einsehbar, aber abspeichern wer was gespeichert hat

	public function __construct($mmo_id,$pln_id,$tgz_id,$kta_id,$wtg_id,$kpn_id_1,$kpn_id_2,$kpn_id_3,$kpn_id_4,$mmo_condiments,$mmo_garnitur,$mmo_gewicht_gramm) {
		if (!empty($mmo_id)) { //wenn erster Parameter nicht existiert, dann wurde rest auch nicht eingegeben
			if ($mmo_id!=-1) { //If Mea_id -1, then do not save it
				$this->setMmo_Id($mmo_id);
			}
			$this->setPln_Id($pln_id);
			$this->setTgz_Id($tgz_id);
			$this->setWtg_Id($wtg_id);
			$this->setKta_Id($kta_id);
			$this->setKpn_Id_1($kpn_id_1);
			$this->setKpn_Id_2($kpn_id_2);
			$this->setKpn_Id_3($kpn_id_3);
			$this->setKpn_Id_4($kpn_id_4);
			$this->setMmo_Condiments($mmo_condiments);
			$this->setMmo_Garnitur($mmo_garnitur);
			$this->setMmo_Gewicht_Gramm($mmo_gewicht_gramm);
		}
		$this->setCre_Id($_SESSION['username']); //username immer zuweisen
	}
	public static function __construct_empty() {
		return new self("","","","","","","","","","","","");
	}

	public static function __construct_From_CSV($param) {
		$tmp_arr = explode(":",$param);
		//var_dump($tmp_arr);die();
		return new self($tmp_arr[0],$tmp_arr[1],$tmp_arr[2],$tmp_arr[3],$tmp_arr[4],$tmp_arr[5],$tmp_arr[6],$tmp_arr[7],$tmp_arr[8],$tmp_arr[9],$tmp_arr[10],$tmp_arr[11]);
	}

	//DB Functions
	public function db_printAllMmoFromDB() {
		printNestedArray($this->db_loadAllMmoFromDB(),"Keine Zuweisungen");
	}

	public function db_printAllMmoANDRelTables() {
		printNestedArray($this->db_loadAllMmoANDRelTables(),"Keine Zuweisungen");
	}

	public static function db_loadAllMmoANDRelTables() {
		$con = new db_connection();
		$a = 0;$tmp_result = array();

		$sqlite_obj = $con->query("SELECT (kpn_komponente_1||', '||kpn_komponente_2||', '||kpn_komponente_3||', '||kpn_komponente_4) as xxx_Bezeichnung,* FROM v_Meal_Monitor_Related;");
		while ($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
			$tmp_result[$a++] = $row;
		}
		return (empty($tmp_result) ? false : $tmp_result);
	}
	
	public function db_loadAllMmoFromDB() {
		$con = new db_connection();
		$a = 0;
		$sqlite_obj = $con->query("SELECT * FROM Meal_Monitor;"); //Nicht nach Cre_Id filtern, da Cre_Id hier nur letzter Bearbeiter, aber nicht alleiniger Besitzer

		while ($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
			$tmp_result[$a++] = $row; //Tmp_result is a nested array
		}
		$con->close();
		return empty($tmp_result) ? $tmp_result = false : $tmp_result; //Wenn Tmp_result = null, dann mach Boolean draus
	}

	public function db_loadAllXFromDB($sql) {
		$con = new db_connection();
		$a = 0;
		$sqlite_obj = $con->query($sql); //Nicht nach Cre_Id filtern, da Cre_Id hier nur letzter Bearbeiter, aber nicht alleiniger Besitzer

		while ($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
			$tmp_result[$a++] = $row; //Tmp_result is a nested array
		}
		$con->close();
		return empty($tmp_result) ? $tmp_result = false : $tmp_result; //Wenn Tmp_result = null, dann mach Boolean draus
	}

	public function db_insertNewMmo() {
		var_dump($this);
		var_dump($this->getKpn_Id_1());
		var_dump($this->getKpn_Id_2());
		var_dump($this->getKpn_Id_3());
		var_dump($this->getKpn_Id_4());
		var_dump("INSERT INTO Meal_Monitor (pln_id,tgz_id,wtg_id,kta_id,kpn_id_1,kpn_id_2,kpn_id_3,kpn_id_4,mmo_condiments,mmo_garnitur,mmo_gewicht_gramm,cre_id) VALUES (".$this->getPln_Id().",'".$this->getTgz_Id()."',".$this->getWtg_Id().",".$this->getKta_Id().",".$this->getKpn_Id_1().",".$this->getKpn_Id_2().",".$this->getKpn_Id_3().",".$this->getKpn_Id_4().",'".$this->getMmo_Condiments()."','".$this->getMmo_Garnitur()."',".$this->getMmo_Gewicht_Gramm().",'".$this->getCre_Id()."');");
		$con = new db_connection();

		$success = $con->query("INSERT INTO Meal_Monitor (pln_id,tgz_id,wtg_id,kta_id,kpn_id_1,kpn_id_2,kpn_id_3,kpn_id_4,mmo_condiments,mmo_garnitur,mmo_gewicht_gramm,cre_id) VALUES (".$this->getPln_Id().",'".$this->getTgz_Id()."',".$this->getWtg_Id().",".$this->getKta_Id().",".$this->getKpn_Id_1().",".$this->getKpn_Id_2().",".$this->getKpn_Id_3().",".$this->getKpn_Id_4().",'".$this->getMmo_Condiments()."','".$this->getMmo_Garnitur()."',".$this->getMmo_Gewicht_Gramm().",'".$this->getCre_Id()."');");
		$con->close();
		return $success;
	}

	public function db_deleteMmoById() {
		execDBStatement("DELETE FROM Meal_Monitor WHERE mmo_id=".$this->getMmo_Id().";");
	}

	public function db_deleteAllMyMmo() {
		execDBStatement("DELETE FROM Meal_Monitor WHERE cre_id='".$this->getCre_Id()."';");
	}

	public function db_deleteALLMmo() {
		execDBStatement("DELETE FROM Meal_Monitor;");
	}

	public function db_updateMmo() {
		execDBStatement("UPDATE Meal_Monitor SET 
			cre_id='".$this->getCre_Id()."',
			pln_id=".$this->getPln_Id().",
			tgz_id='".$this->getTgz_Id()."',
			wtg_id=".$this->getWtg_Id().",
			kta_id=".$this->getKta_Id().",
			kpn_id_1=".$this->getKpn_Id_1().",
			kpn_id_2=".$this->getKpn_Id_2().",
			kpn_id_3=".$this->getKpn_Id_3().",
			kpn_id_4=".$this->getKpn_Id_4().",
			mmo_condiments='".$this->getMmo_Condiments()."',
			mmo_garnitur='".$this->getMmo_Garnitur()."',
			mmo_gewicht_gramm=".$this->getMmo_Gewicht_Gramm().";");
	}

	public function db_loadMmoById() {
		$loaded_mmo = _meal_monitor::__construct_empty();
		//LOAD 
		$con = new db_connection();
		$sqlite_obj = $con->query("SELECT * FROM Meal_Monitor WHERE mmo_id=".$this->getMmo_Id().";");

		while ($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
			$loaded_mmo->setMmo_Id($row['mmo_id']);
			$loaded_mmo->setPln_Id($row['pln_id']);
			$loaded_mmo->setTgz_Id($row['tgz_id']);
			$loaded_mmo->setWtg_Id($row['wtg_id']);
			$loaded_mmo->setKta_Id($row['kta_id']);
			$loaded_mmo->setKpn_Id_1($row['kpn_id_1']);
			$loaded_mmo->setKpn_Id_2($row['kpn_id_2']);
			$loaded_mmo->setKpn_Id_3($row['kpn_id_3']);
			$loaded_mmo->setKpn_Id_4($row['kpn_id_4']);
			$loaded_mmo->setMmo_Condiments($row['mmo_condiments']);
			$loaded_mmo->setMmo_Garnitur($row['mmo_garnitur']);
			$loaded_mmo->setMmo_Gewicht_Gramm($row['mmo_gewicht_gramm']);
			$loaded_mmo->setCre_Id($row['cre_id']);
			break;
		}
		$con->close();
		return $loaded_mmo;
	}


	//Getter/Setter ####################################################
	/* -------------------- IMPORTANT -------------------
	Wochentag und Komponente-Tabelle wird durch Setter abgebildet. 
	Es wird beim Setzen der ID das Objekt als Array in das Attribut gespeichert, 
	nicht nur die ID. Alle Informationen können anschließend vom Getter geholt werden. */

	public function getCre_Id() {
		return $this->cre_id;
	}
	public function setCre_Id($cre_id) {
		$this->cre_id = escapeString($cre_id);
	}
	public function getKta_Id() {
		return $this->kta_id;
	}
	public function setKta_Id($kta_id) {
		$kta_id = escapeString($kta_id);
		if (!is_string($kta_id)) {
			$this->kta_id = $kta_id;
		} else {
			//Wenn String, dann prüfe ob in DB vorhanden
			$con = new db_connection();
			$sqlite_obj = $con->query("SELECT * FROM Kostart WHERE kta_lang='".$kta_id."' OR kta_kurz='".$kta_id."';");
			if (empty($sqlite_obj)) {
				//INSERT INTO DB 
				//$attr_type = (strlen($kta_id)>4) ? 'kta_lang' : 'kta_kurz'; //Entscheide ob String lang genug für kta_lang sonst in kta_kurz speichern. 
				$con->exec("INSERT INTO Kostart (kta_lang,kta_kurz) VALUES ('".$kta_id."','".substr($kta_id,0,4)."');");

				$maxobj = $con->query("SELECT MAX(kta_id) as kta_id FROM Kostart;");
				$kta_id = ($maxobj->fetchArray(SQLITE3_ASSOC));

				$this->kta_id = escapeString($kta_id['kta_id']); //Speichere letzte ID vom Insert rein
			} else {
				while($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
					$this->kta_id = escapeString($row['kta_id']);	
				}
			}

			$con->close();
		}
	}
	public function getPln_Id() {
		return $this->pln_id;
	}
	public function setPln_Id($pln_id) {
			$this->pln_id = escapeString($pln_id);
	}
	public function getTgz_Id() {
		return $this->tgz_id;
	}
	public function setTgz_Id($tgz_id) {
		$this->tgz_id = escapeString($tgz_id);
	}
	public function getWtg_Id($key="wtg_id") {
		return $this->wtg_id[$key]; //return id if no key given
	}
	public function setWtg_Id($wtg_id) {
		$con = new db_connection();
		$sqlite_obj = $con->query("SELECT * FROM Wochentag WHERE wtg_id=".$wtg_id.";");
		$first_only_row = array();
		while ($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
			$first_only_row = $row;
			break;
		}
		$con->close();
		if (empty($first_only_row)) {echo "ERROR: Wochentag nicht gefunden!";die();}
		$this->wtg_id = escapeString($first_only_row); //escape String can also escape Arrays
	}
	public function getMmo_Id() {
		return $this->mmo_id;
	}
	public function setMmo_Id($mmo_id) {
		$this->mmo_id = escapeString($mmo_id);
	}

	private static function setKpnOBJinGetKpn($kpn_id) {
		if (!empty($kpn_id) && $kpn_id!="empty") {
			set_time_limit(30);
			$con = new db_connection();
			$first_only_row = array();
			$kpn_id = escapeString($kpn_id);

			if (is_numeric($kpn_id)) {
				$sqlite_obj = $con->query("SELECT * FROM Komponente WHERE kpn_id=".$kpn_id.";");
				while ($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
					$first_only_row = $row;
					break; //just save first row (but there should be just one)
				}
			}
			if (empty($first_only_row) && !is_numeric($kpn_id)) {
				//INSERT COMPONENT AUTOMATICALLY IF NOT FOUND and STRING is delivered
				$con->exec("INSERT INTO Meal (mea_titel,cre_id) VALUES ('".$kpn_id."','".escapeString($_SESSION['username'])."');");
				$maxobj = $con->query("SELECT MAX(mea_id) as mea_id FROM Meal;");
				$kpn_id = ($maxobj->fetchArray(SQLITE3_ASSOC));
				$con->exec("INSERT INTO Komponente (kpn_gericht) VALUES (".escapeString($kpn_id['mea_id']).");");
				$con->close();
				return self::setKpnOBJinGetKpn($kpn_id['mea_id']);
			} else if (empty($first_only_row)) {
				var_dump($first_only_row);
				echo "ERROR: Komponente leer aber kein String!";die();
			}
			$con->close();
			return $first_only_row;
		} else {
			return ""; //besser als null
		}
	}

	public function getKpn_Id_1($key="kpn_id") {
		return (is_array($this->kpn_id_1)) ? $this->kpn_id_1[$key] : "";
	}
	public function setKpn_Id_1($kpn_id_1) {
		$this->kpn_id_1 = self::setKpnOBJinGetKpn($kpn_id_1);
	}
	public function getKpn_Id_2($key="kpn_id") {
		return (is_array($this->kpn_id_2)) ? $this->kpn_id_2[$key] : "";
	}
	public function setKpn_Id_2($kpn_id_2) {
		$this->kpn_id_2 = self::setKpnOBJinGetKpn($kpn_id_2);
	}
	public function getKpn_Id_3($key="kpn_id") {
		return (is_array($this->kpn_id_3)) ? $this->kpn_id_3[$key] : "";
	}
	public function setKpn_Id_3($kpn_id_3) {
		$this->kpn_id_3 = self::setKpnOBJinGetKpn($kpn_id_3);
	}
	public function getKpn_Id_4($key="kpn_id") {
		return (is_array($this->kpn_id_4)) ? $this->kpn_id_4[$key] : "";
	}
	public function setKpn_Id_4($kpn_id_4) {
		$this->kpn_id_4 = self::setKpnOBJinGetKpn($kpn_id_4);
	}
	public function getMmo_Condiments() {
		return $this->mmo_condiments;
	}
	public function setMmo_Condiments($mmo_condiments) {
		$this->mmo_condiments = escapeString($mmo_condiments);
	}
	public function getMmo_Garnitur() {
		return $this->mmo_garnitur;
	}
	public function setMmo_Garnitur($mmo_garnitur) {
		$this->mmo_garnitur = escapeString($mmo_garnitur);
	}
	public function getMmo_Gewicht_Gramm() {
		return $this->mmo_gewicht_gramm;
	}
	public function setMmo_Gewicht_Gramm($mmo_gewicht_gramm) {
		$this->mmo_gewicht_gramm = escapeString($mmo_gewicht_gramm);
	}
}

?>