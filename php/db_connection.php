<?php

class DB_Connection extends SQLite3 {
	
	function __construct() {
		$db_name = "kueche.db";
		//$datenbank = $_SERVER["DOCUMENT_ROOT"]."\WSDT_Kueche\php\db_sqlite\\".$db_name;

		//SINCE VERSION 2
		//$db_folder = "\\\\tilak.cc\share\daten01\Kuechensekr\_Speiseplaene\src\WSDT\\";
		//SINCE VERSION 4
		$db_folder = "\\\\tilak.cc\share\daten01\Kuechensekr\_Speiseplaene\install_wsdt\WSDT\www\WSDT_Kueche\php\db_sqlite\\";
		$datenbank = $db_folder.$db_name;
		//$db = new \SQLite3($datenbank);

		$this->enableExceptions(true); //Try catch works now

		if ($this->db_exists($datenbank,true) && $this->db_writeable($datenbank,true)) {
			//echo "INFO: Opened database successfully!<br />";
			$this->open($datenbank); 
			//$pdo = new PDO('sqlite:'.$datenbank) or die("FATAL ERROR: Cannot open database!");
			//Pragma für Fremdschlüssel-Integrität muss bei jeder Verbindung aktiv sein
			$this->exec("PRAGMA foreign_keys = ON;");
		} else if ($this->db_exists($datenbank,true) && !($this->db_writeable($datenbank,true))) {
			if(!chmod($db_folder, 0777)) {
				echo "ERROR: Missing write permission on database and cannot change it!";
			}
		} else {
			echo "<div class='db_fatal_error'>INFO: 'kueche.db' (=database) NOT found. Created an empty one. Please do not delete that file in future.</div>";
			$database = new SQLite3($datenbank);
			$sql_file = file_get_contents(substr($datenbank,0,-3).".sql"); //speichere alle SQL Befehle in Variable
			$this->open($datenbank);
			$this->exec($sql_file); //erstelle alle Tabellen etc.
		}
	}

	function db_exists($db_name,$full_path) {
		if ($full_path === true) {
			return file_exists($db_name);
		} else {
			return file_exists($db_folder.$db_name);
		}
	}

	function db_writeable($db_name,$full_path) {
		if ($full_path === true) {
			return is_writable($db_name);
		} else {
			return is_writable($db_folder.$db_name);
		}
	}


	/*public static function getConnection() {
		if (self::$_DB_CONNECTION === null) {
			self::$_DB_CONNECTION = new self;
		}
		return self::$_DB_CONNECTION;
	}

	protected function __clone() {}
	protected function __construct() {}*/

}


?>