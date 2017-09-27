<?php
require_once '_auth.php';session_authenticate(); //da sonst beim Instanzieren undefined Index 'username'


//####################### EXPORT HANDLE ##########################
if (!empty($_POST)) {
	if (isset($_POST['sql_statement']) && isset($_POST['hasheadings']) && isset($_POST['filename'])) {

		$con = new db_connection();
		$sqlite_obj = $con->query($_POST['sql_statement']);

		$array = [];$i=0;
		while ($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
			$array[$i++] = $row;
		}
		$con->close();
		exportToCSV($array,boolval($_POST['hasheadings']),(empty($_POST['filename']) ? false : $_POST['filename']));
	} 
	if (isset($_POST['download_csv'])) {
		downloadFile($_POST['download_csv'],$_POST['file_type']);
	}
}



//####################### IMPORT HANDLE ##########################
if (!empty($_FILES)) {
	if (isset($_FILES['file2upl'])) {
		if (!isset($_POST['php_class']) || !isset($_POST['php_insertDBfunction'])) {
			echo "$.notify('Could not receive all data (in CSV_handler)','error');";
		} else {
			uploadUseDeleteCSV($_FILES['file2upl'],$_POST['php_class'],$_POST['php_insertDBfunction']);
		}
	}
}


// ############################# DB ###################################
function saveCSVtoDB($php_class,$php_insertDBfunction,$array,$pk_autoincrement=true,$filter_double_values=true) {
	/*IMPORTANT: 
	Es werden alle Spalten ohne weitere Prüfung in die DB geschickt. 
	D.h. Spalten sind chronologisch der PHP-Klasse bzw. SQL-DB anzuordnen sowie die Anzahl
	der Spalten und der Konstruktor-Parameter muss stimmen!

	PHP_CLASS muss inkl. Dateipfad angegeben werden (aber ohne php)
	PK_AUTOINCREMENT true or false
	*/

	$required_class = glob($php_class.".php"); 
	if (empty($required_class[0])) {
		echo "$.notify('ERROR: Class NOT found!','error');";
	} else {
		require_once $required_class[0];
		$classname = basename($required_class[0],".php").PHP_EOL; //substr($required_class[0],strripos($required_class[0], "_"));
		$classname = substr($classname, 0, strlen($classname)-2); //da Leerzeichen + non-visible char drin gewesen

		$row_nr = 0;
		
		//FILTER DOUBLE VALUES IF WANTED
		if ($filter_double_values) {
			$array = array_map("unserialize",array_unique(array_map("serialize",$array))); //Filtere doppelte Einträge raus
		}

		foreach ($array as $row) { // Each row, except headings (first row), gets an object
			$cols_count = sizeof($row)-2; //so von 0 auf gezählt

			if (($row_nr++)!=0) { //skip Headings
				$i=0;$param="";

				if ($pk_autoincrement) {
					$param .= "-1";
				}
				
				foreach($row as $cell) {
					if (($i++) != 0 || $pk_autoincrement) {
						$param .= ":"; //TODO: : wird ab 2tem Parameter NICHT MEHR GESETZT
					}
					$add = str_replace("\"","",str_replace("'","", $cell)); // Anführungszeichen entfernen, sonst Parameter falsch übergeben
					if (empty($add)) {$add="-";}
					$param .= $add;
				}

				$tmp_obj = $classname::__construct_From_CSV($param);

				//######################################################
				//## Make specific multiple INSERT for that (faster) ###
				//######################################################
				$tmp_obj->$php_insertDBfunction(); 
				set_time_limit(30); //starte max_execution time neu (sodass auch lange files bearbeitet werden dürfen)
			}
		}
		echo "<script type='text/javascript'>$.notify('Import successfull!','success');</script>";
		//TODO: Maybe InsertAll in Meal.php und erst hier aufrufen (schneller)
	} //don't do anything outside the else brackets, because class is not imported
}


// ###################### EXPORT ####################################
function exportToCSV($array,$hasheadings=false,$filename=false) {
	echo "var finished = 'false';";
	/*IMPORTANT: For compatibility to the import-function add in array[0]
	all your headings, because they will be ignored when importing it. 

	If hasheadings is false, then this function will add a custom heading line. */
	if (empty($array) || !is_array($array)) {
		echo "$.notify('Database must contain content!\\nERROR: Var is not an array (in exportToCSV())','error');";
	} else { //If $array is array

		cleanExportDir(); //Delete all previous exported files

		//Add headings if hasheadings is false
		if (!$hasheadings) {
			$nr_cols = count((array) $array[0]);

			$headings = [];
			for ($i=1;$i<=$nr_cols;$i++) {
				$headings[$i-1] = "Spalte ".$i;
				if (!(($nr_cols+1)>$i)) {
					$headings[$i-1] = ";";
				}
			}

			array_unshift($array,$headings);
		}

		//Set standard filename if nothing given
		if ($filename===false) {
			$filename='exported_data_';
		}

		//UniqueID prevents export error when file is already opened 
		$exported_file = './_user_data/exports/'.$filename.'_'.date("d-m-Y_H-i-s").'_'.uniqid().'.csv';
		$fp = fopen($exported_file,'w');
		foreach ($array as $fields) {
			if (is_object($fields)) {
				$fields = (array) $fields;
			}
			fputcsv($fp,$fields,";");
		}
		fclose($fp);

		//Say Ajax that it is finished
		echo "finished = 'true';";
		echo "var exported_file='".$exported_file."';";
	}
}

function downloadFile($exported_file,$file_type) {
	if (file_exists($exported_file)) {
		if ($file_type != "csv") { //Wenn anderes Dateiformat gewünscht
			$exported_file = convertCSVtoXLSX($exported_file,$file_type);
		}
		//Gib nichts anderes aus!!
		header('Content-Description: File Transfer');
		header('Content-Type: text/csv');
		header('Content-disposition: attachment; filename="'.basename($exported_file).'"');

		if (readfile($exported_file)===false) {
			echo "$.notify('Could not download file.','error');";
		}		
	} else {
		echo "$.notify('Export did not work. Please try again','error');";
	}
}

function cleanExportDir() {
	foreach (glob($_SERVER['DOCUMENT_ROOT']."/WSDT_KUECHE/php/_user_data/exports/*") as $key => $value) {
		unlink($value);
	}
}

function convertCSVtoXLSX($csv_file,$file_type) {
	require_once 'PHPExcel/PHPExcel/IOFactory.php';
	$objReader = PHPExcel_IOFactory::createReader('CSV');

	$objReader->setDelimiter(";");
	$objReader->setInputEncoding("UTF-8");
	$objPHPExcel = $objReader->load($csv_file);

	//Set FileType
	switch($file_type) {
		case 'xlsx': $PHP_Excel_mime = 'Excel2007';break;
		case 'xls': $PHP_Excel_mime = 'Excel5';break;
		default: echo "ERROR: Unsupported file_type: '".$file_type."'!";die();
	}

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $PHP_Excel_mime);//Excel5
	$filename_new = substr(basename($csv_file),0,-4).".".$file_type;
	$objWriter->save($filename_new);
	return $filename_new;
}

// ###################### IMPORT ####################################
function showImportForm($php_class,$php_insertDBfunction,$pk_autoincrement=true,$filter_double_values=true) {
	//INCLUDE Progress bar for huge files?
	//https://www.sitepoint.com/tracking-upload-progress-with-php-and-javascript/
	$unique_form_name = "import_".uniqid();
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data' id='".$unique_form_name."' class='import_CSV_form' autocomplete='off'>";
	echo "<input type='hidden' value='".$php_class."' name='php_class'/>";
	echo "<input type='hidden' value='".$php_insertDBfunction."' name='php_insertDBfunction'/>";
	echo "<input type='file' accept='.csv' name='file2upl' id='file2upl' onchange='document.getElementById(\"".$unique_form_name."\").submit()'/>";
	echo "<label title='Import CSV files' for='file2upl'><img src='../img/import.png' alt='import'/></label>";
	echo "</form>";
}


//USE THIS FUNCTION FROM OUTSIDE
function uploadUseDeleteCSV($file,$php_class,$php_insertDBfunction,$pk_autoincrement=true,$filter_double_values=true) {//$_FILES['NAME']

	$target_dir = $_SERVER['DOCUMENT_ROOT']."/wsdt_kueche/php/_user_data/uploaded_files/";
	$target_file = $target_dir .uniqid()."_". basename($file["name"]);
	$filetype = pathinfo($target_file,PATHINFO_EXTENSION);

	if ($filetype!="csv") {
		echo "<script type='text/javascript'>$.notify('Datei muss vom Typ csv sein!','error');</script>";
	} else {
		$cancel_resubmission = (isset(glob($target_dir."*_".basename($file["name"]))[0])) ? glob($target_dir."*_".basename($file["name"]))[0] : $target_file; //target file makes following if redundant

		if (file_exists($cancel_resubmission)) { //Breche DB Insert ab, wenn Datei mit gleichem UniqId ankommt!
			//unlink($target_file); //delete old file 
			echo "<script type='text/javascript'>$.notify('Resubmission of form cancelled.','info');</script>";
			unlink($cancel_resubmission);
			$_POST = array();
		} else if (move_uploaded_file($file["tmp_name"], $target_file)) {
			saveCSVtoDB($php_class,$php_insertDBfunction,loadCSVFile($target_file),$pk_autoincrement=true,$filter_double_values=true);

			//Delete all files except new one (for refresh bug [prevent resubmission of form])
			foreach(glob("$target_dir*") as $file) {
				if ($file != $target_file) { //Delete only old files
					unlink($file);
				} 
			}

		} else {
			echo "ERROR: Datei konnte nicht hochgeladen werden!";
		}
	}
}


function loadCSVFile($file_path) {
	/*Converts CSV file to array separated by rows, including nested arrays for single columns
	and returns it as utf8-encoded nested array.*/

	/*CLEAN CSV File from ',' so only ; are essential*/
	$delimiter_arr = [];$new_arr = [];
	foreach(file($file_path,FILE_SKIP_EMPTY_LINES) as $row) {
		array_push($delimiter_arr,str_replace(",", "--ESCAPED--",$row));
	}
	/*CLEAN END, START MAKING NESTED ARRAY (row contains further arrays)*/
	foreach(array_map('str_getcsv',$delimiter_arr) as $sub_arr) {
		//get temporarly removed , back; encode to utf8 and add to array
		array_push($new_arr, explode(";",utf8_encode(str_replace("--ESCAPED--", ",",$sub_arr[0]))));
	}
	return $new_arr; //first row contains headings
}



//######################## PRINT FUNCTIONS ###########################
function printCSVTable($array) {
	echo "<table>";
	$i=0;
	foreach ($array as $row) {
		echo "<tr>";
		foreach ($row as $cell) {
			if (($i)<=0) {
				echo "<th>".$cell."</th>";
			} else {
				echo "<td>".$cell."</td>";
			}
		}
		$i++;
		echo "</tr>";
	}
	echo "</table>";
}


?>