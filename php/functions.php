<?php 
function escapeString($string) {
	if (is_array($string)) {
		foreach ($string as $key=>$value) {
			$string[$key]=escapeString($value); //Escape every element of array
		}
		return $string;
	} else {
		return SQLite3::escapeString($string);
	}
}

function printStars($nr) {
	$stars="";
	for($z=0;$z<$nr;$z++) {
		$stars .= "*";
	}		
	return $stars;
}


function printNestedArray($nested_array,$subject) {
		if(!($nested_array===false)) {
			echo "<div class='table'>";

			$i = 0;
			foreach ($nested_array as $row) {

				echo "<div class='table-row'>";
				//CAPTION ROUTINE START
				foreach ($row as $key => $value) {
					if ($i == 0) {
						//ADAPT COLUMN HEADINGS MANUALLY
						switch($key) {
							case 'cre_id': $key="cre_Last_Editor";break;
							case 'pro_id': $key="pro_Prod.Platz";break;
							//By default do nothing
						}
						//ADAPT COLUMN HEADINGS END
						echo "<div class='table-caption'>".strtoupper(str_replace('_',' ',substr($key,4)))."</div>";
					} else {break;}
				}
				if($i++ == 0) {
					echo "</div><div class='table-row'>";
				}
				//CAPTION ROUTINE END
				
				foreach ($row as $key => $value) {
					$do_substr = true;
					//LINE ADAPTION for custom pref. -------------------
					switch($key) {
						case 'ass_amount_stars':$row[$key]=printStars($row[$key]);break;
						case 'pro_id': $row[$key]=(($row[$key]=="empty" || empty($row[$key])) ? "<span style='color:#ff0000;'>{not assigned}</span>" : $row[$key]);$do_substr=false;break;
						//By default do nothing
					}
					//LINE ADAPTION END --------------------------------
					if($do_substr) {$row[$key] = substr($row[$key], 0, 30);}
					echo "<div class='table-cell'>".$row[$key]."</div>";
				} 
				echo "</div>";
  		  	}
    		echo "</div>";
		} else {
			echo "<span class='error_msg'>".$subject." in der Datenbank gefunden!</span>";
		}
}

function printComboBoxOfNestedArray($nested_array,$box_id) {
		$no_employees = false;
		$onchange = ""; 
		if ($box_id == "empupd_id") {
			$onchange = "onchange='upd_load_Employee_toForm()'";
		} 

		echo "<select name='".$box_id."' id='".$box_id."' class='form-control' ".$onchange.">";
		if(!($nested_array===false)) {

			foreach ($nested_array as $row) {		
				$i = 0;
				foreach ($row as $key => $value) {
					if(strpos($key,"_id")!==false&&$key!="ass_emp_id" || $box_id=="change_account") {
						echo "<option value='".$value."'>";
					}
					if ($i<3 && $box_id!="dpl_kuerzel") { //Drucke nur die ersten 3 Spalten
						if ($box_id=="assdel_id"&&$key=="ass_amount_stars") {
							echo printStars($value);
						} else {
							$is_change_acc = $box_id=="change_account";
							if($is_change_acc) {
								echo "User: '";
							}
							echo substr($value,0,14);
							if($is_change_acc) {
								echo "'";
							}
						}
						if ($i != 2 && $box_id!="change_account") {
							echo " - ";
						}
					} else {
						echo "</option>";
					}
				$i++;
				} 
				echo "</option>";
		  	}
		echo "</div>";
	} else {
		echo "<option value='empty'>";
		if ($box_id=="assdel_id") {
			echo "No Assessments found";
		} else if ($box_id=="change_account") {
			echo "No users found";
		} else {
			echo "No Employees found";
		}
		echo "</option>";
		$no_employees = true;
	}
	echo "</select>";
	return $no_employees;
}

function printComboBoxOfNestedArray_general($nested_array,$box_id,$amount_cols) {
		$nothing_found = false;
		$onchange = ""; 
		
		//FOREIGN KEY CORRECTION, PREVENT ONCHANGE LISTENER IF BOX_ID contains ':'
		if(strpos($box_id,":")===false) {
			switch($box_id) {
				case 'dpl_kuerzel': $onchange="upd_load_Kuerzel_toForm()";break;
				case 'mea_id':$onchange="upd_load_Mahlzeit_toForm()";break;
				case 'pro_id': $onchange="upd_load_Produktionsplatz_toForm()";break;
				default: $onchange = "";
			}
		} else {
			$box_id = substr($box_id, 1); //remove :
		}

		echo "<select name='".$box_id."' id='".$box_id."' class='form-control' onchange='".$onchange."'>";
		//echo "<option value='empty'>&nbsp;</option>"; //Default line 
		if(!($nested_array===false)) {

			$row_i = 0;
			foreach ($nested_array as $row) {		
				$cell_i = 0;
				foreach ($row as $key => $value) {

					if($cell_i==0) { //Erste Zelle
						echo "<option value='".$value."'>";
					}
					if ($cell_i<$amount_cols) { //Drucke nur die ersten X Spalten
						echo $value;

						if (!((++$cell_i) >= $amount_cols)) {
							echo " - ";
						}
					} else {
						echo "</option>";
					}
				} 
		  	}
	} else {
		echo "<option value='empty'>Found nothing..</option>";
		$nothing_found = true;
	}
	echo "</select>";
	return $nothing_found;
}


/*function hasInternetConnection($sCheckHost = 'www.google.com') {
    return (bool) @fsockopen($sCheckHost, 80, $iErrno, $sErrStr, 5);
}*/

function execDBStatement($sql) {
	$con = new db_connection();
	$con->exec($sql);
	$con->close();
}

// TOOLS ####################################################################################################################
// Starrating ---------------------------------------------------------------------------------------------------------------
function printStarrating($id) {
	echo "<span class='form-control' id='".$id."'><span class='rating'><input type='radio' class='rating-input' id='rating-input-1-5' name='rating-input-1' value='5'>  <label for='rating-input-1-5' class='rating-star'></label>  <input type='radio' class='rating-input'   id='rating-input-1-4' name='rating-input-1' value='4'>  <label for='rating-input-1-4' class='rating-star'></label>  <input type='radio' class='rating-input' id='rating-input-1-3' name='rating-input-1' value='3' checked>  <label for='rating-input-1-3' class='rating-star'></label>  <input type='radio' class='rating-input'   id='rating-input-1-2' name='rating-input-1' value='2'>  <label for='rating-input-1-2' class='rating-star'></label>  <input type='radio' class='rating-input' id='rating-input-1-1' name='rating-input-1' value='1'><label for='rating-input-1-1' class='rating-star'></label></span></span>";
}
	

	?>