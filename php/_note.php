<?php
if(!isset($_SESSION)) {session_start();}
require_once 'db_connection.php';
require_once 'functions.php';
//###########################################################################################
if (!empty($_POST)) {
	//SAVE NOTE ++++++++++++++++++++++++++++++++++++++++++++
	if(isset($_POST['saveNote'])) {
		$note = _note::__construct_json($_POST['note']);
		$note->saveNoteToDb();
	}

	//DELETE NOTE ++++++++++++++++++++++++++++++++++++++++++
	if(isset($_POST['deleteAllNotes'])) {
		$tmp_note = _note::__construct_empty();
		$tmp_note->deleteAllNotesFromDb();
		echo '$.notify("Alle Notizen gelöscht.","success");';
	}
	if(isset($_POST['deleteNote'])) {
		$tmp_note = _note::__construct_empty();
		$tmp_note->setId($_POST['deleteNote']);
		$tmp_note->deleteNoteFromDb();
		echo '$.notify("Notiz gelöscht.","success");';
	}	

}

//###########################################################################################
class _note {
	const TYPE = "note"; //no public allowed etc.
	private $id;
	private $title;
	private $content;
	private $position;
	private $flags;
	private $categories;
	private $created;
	private $cre_id;

	public function __construct($id,$title,$content,$position,$flags,$categories,$created) {
		if (!empty($id) || $id==0) { //da leerer String als empty interpretiert sowie 0 als empty interpretiert (aber 0 ist die erste Notiz)
			$this->setId($id); //integer, only Nr. (e.g. 1,2,3 for note1, note2, note3)
			$this->setTitle($title);
			$this->setContent($content);
			$this->setPosition($position);
			$this->setFlags($flags);
			$this->setCategories($categories);
			$this->setCreated($created);
		}
		$this->setCre_Id($_SESSION['username']); //also empty ones need this value
	}

	public static function __construct_empty() {
		return new self("","","","","","","");
	}

	public static function __construct_json($json_array) {
		if (is_string($json_array)) {
			$json_array = json_decode($json_array,true);
		} 

		return new _note(
			$json_array["id"],
			$json_array["title"],
			$json_array["content"],
			$json_array["position"],
			$json_array["flags"],
			$json_array["categories"],
			$json_array["created"]
			);
	}

	public function convertPHPObject2JSObject($note) {
		if(is_array($note) || is_object($note)) {
			$result = array();

			foreach($note as $key => $value) {
				//if (is_numeric($value)) {$value = strval($value);} //convert int to string
				/*if ($key == "content" || $key == "title") {
					$value = htmlspecialchars($value,ENT_QUOTES,'UTF-8');
				}*/

				$result[$key] = $this->convertPHPObject2JSObject(htmlspecialchars($value,ENT_QUOTES,'UTF-8'));
			}
			return $result;
		}
		return $note;
		//When loading this function, then encode the returning value with json_encode()
	}

	// DB FUNCTIONS ##########################################################################
	public static function loadHighestIdFromDb() {
		$con = new db_connection();
		$sqlite_obj = $con->query("SELECT * FROM Note;");
		$highest_id = -1;
		while ($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
			$highest_id = (($row['note_id']) > $highest_id) ? $row['note_id'] : $highest_id;
		}
		$con->close();
		return $highest_id;
	}


	public function loadAllNotesFromDb() { //Only an empty Note Instance needed (cre_id has to be set, but is automatically)
		if(!$this->hasNoteAccess()) {return;}

		$con = new db_connection();
		$sqlite_obj = $con->query("SELECT * FROM Note WHERE cre_id='".$this->getCre_Id()."';");


		//Create all objects and place them in an array
		$i=0;
		while ($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
			$all_notes[$i++] = new _note(
					$row['note_id'],
					$row['note_title'],
					$row['note_content'],
					$row['note_position'],
					$row['note_flags'],
					$row['note_categories'],
					$row['note_created']
				);
		}
		$con->close();
		return empty($all_notes) ? false : $all_notes;
		//returns an Array with all objects
	}

	public function loadNoteFromDb() {
		return $this->doesNoteExist(); //You just have to setId() beforehand.
	}

	public function saveNoteToDb() {
		if(!$this->hasNoteAccess()) {return;} //return null to stop the function

		//execDBStatement("");
		$note_not_exist_or_node = $this->doesNoteExist();

		if ($note_not_exist_or_node === false) {
			//INSERT
			execDBStatement("INSERT INTO Note (note_id,note_title,note_content,note_position,note_flags,note_categories,cre_id) VALUES (
				".$this->getId().",
				'".$this->getTitle()."',
				'".$this->getContent()."',
				'".$this->getPosition()."',
				'".$this->getFlags()."',
				'".$this->getCategories()."',
				'".$this->getCre_Id()."'
				);");
		} else { //Also save note when empty, because u could not empty a note
			//UPDATE
			execDBStatement("UPDATE Note SET 
				note_title='".$this->getTitle()."',
				note_content='".$this->getContent()."',
				note_position='".$this->getPosition()."',
				note_flags='".$this->getFlags()."',
				note_categories='".$this->getCategories()."',
				cre_id='".$this->getCre_Id()."'
				WHERE note_id=".$this->getId()."
				;"); // do not give possibility to change primary key
		}
	}

	public function deleteAllNotesFromDb() {
		if(!$this->hasNoteAccess()) {return;} //return null to stop the function

		execDBStatement("DELETE FROM Note WHERE cre_id='".$this->getCre_Id()."';");
	}

	public function deleteNoteFromDb() {
		if($this->doesNoteExist()===false) { //kein hasNoteAccess nötig, da schon hier verschachtelt
			return false; //nicht löschbar weil nicht vorhanden. 
		} else {
			execDBStatement("DELETE FROM Note WHERE cre_id='".$this->getCre_Id()."' AND note_id='".$this->getId()."';");
			return true;
		}
	}

	public function doesNoteExist() { //Returns node or returns false if it does not exist
		if(!$this->hasNoteAccess()) {return;} //return null to stop the function
		//DO NOT RETURN FALSE HERE BECAUSE OF CREATE NOTE

		$con = new db_connection();

		$sqlite_obj = $con->query("SELECT * FROM Note WHERE note_id=".$this->getId()." AND cre_id='".$this->getCre_Id()."';");
		

		$first_row = $sqlite_obj->fetchArray(SQLITE3_ASSOC); //because ID is primary key
		$con->close();


		if (empty($first_row)) {return false;} else {
			$requested_note = new _note(
					$first_row['note_id'],
					$first_row['note_title'],
					$first_row['note_content'],
					$first_row['note_position'],
					$first_row['note_flags'],
					$first_row['note_categories'],
					$first_row['note_created']
				);
			return $requested_note;
		}
	}

	public function hasNoteAccess() {
		if ($this->getCre_Id() == $_SESSION['username']) {
			return true;
		} else {
			return false;
		}
	}


	// GETTER and SETTER ##################################################################### 
	public function setCre_Id($cre_id) {
		$this->cre_id=escapeString($cre_id);
	}
	public function getCre_Id() {
		return $this->cre_id;
	}
	public function setId($id) {
		if(!is_numeric($id)) {
			$id=substr($id,4); //if no clean note id, then extract id from css id (note2 -> 2)
		}
		$this->id=escapeString($id);
	}
	public function getId() {
		return $this->id;
	}
	public function setTitle($title) {
		$this->title=escapeString($title);
	}
	public function getTitle() {
		return $this->title;
	}
	public function setContent($content) {
		$this->content=escapeString(str_replace("\n",'{ENTER}',$content));
	}
	public function getContent() {
		return $this->content;
	}
	public function setPosition($position) {
		$this->position=escapeString($position);
	}
	public function getPosition() {
		return $this->position;
	}
	public function setFlags($flags) {
		$this->flags = escapeString($flags);
	}
	public function getFlags() {
		return $this->flags;
	}
	public function setCategories($categories) {
		$this->categories = escapeString($categories);
	}
	public function getCategories() {
		return $this->categories;
	}
	public function setCreated($created) {
		$this->created = escapeString($created);
	}
	public function getCreated() {
		return $this->created;
	}


}
?>