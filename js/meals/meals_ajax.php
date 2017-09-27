
<?php 
	require_once '/../../php/meals/_prodplatz.php';
?>

<script type="text/javascript">

function prod_menu(what) {
	switch(what) {
		case 'show': refresh_pro_list();break;
		case 'add': show_pro_add_form();break;
		case 'edit': show_pro_upd_form();break;
		case 'delete': show_pro_delete_form();break;
		default: console.error("Modul 'Prodplatz': Do not know what to do.");
	}
}

function mea_menu(what) {
	switch(what) {
		case 'show': refresh_mea_list();break;
		case 'add': show_mea_add_form();break;
		case 'edit': show_mea_upd_form();break;
		case 'delete': show_mea_delete_form();break;
		default: console.error("Modul 'Speisen': Do not know what to do.");
	}
}

//Thanks to https://stackoverflow.com/questions/1772941/how-can-i-insert-a-character-after-every-n-characters-in-javascript
function normalize_tags(tag_str) { //ensure that every allergen is separated by an ,
	var n = 1; //After every char place an , if char is not a ,
	var ret = [];
	var i,len;

	for (i=0, len = tag_str.length;i<len;i+=n) {
		if (tag_str.substr(i,n)!==",") { //Only add , if char is not a ,
			ret.push(tag_str.substr(i,n).toUpperCase())
		}
	}
	return ret.filter(onlyUnique).join(',') //Array to string with , delimiter and remove double values (e.g. caused by A, a)
};

function onlyUnique(value, index, self) {
	return self.indexOf(value)===index;
}
// MAHLZEIT FUNCTIONS ##############################################################################################################################################################################
//Mea anzeigen -------------------------------------------------------

function refresh_mea_list() {
	$.ajax({
    	type: "POST",
    	url: "../php/meals/_meal.php",
    	data: {start_procedure : "db_printAllMeaFromDB"},
    	success: function(data){
        	//document.getElementById('emp_liste_inner').innerHTML = data;
        	//data = echo ausgaben von php datei
        	$.notify("Speisen aktualisiert.","info");
        	$.SimpleLightbox.open({
    			content:data,
    			elementClass: 'slbContentEl'
        	});
    	},
    	error: function(data) {
    		$.notify("Speisen nicht aktualisiert!","error");
    	}
	});
}

//Mea ADD-Form ---------------------------------------------------
function show_mea_add_form() {
	<?php $all_pro = _prodplatz::__construct_empty();?>
	var data = "<form class='well form-horizontal' id='contact_form'><fieldset style='width:800px;'><!-- Form Name --><legend>Neue Mahlzeit</legend><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label'>Titel *</label>    <div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-star-empty'></i></span>  <input id='mea_titel' placeholder='Polenta,...(required)' class='form-control'  type='text' />    </div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Beschreibung: </label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-apple'></i></span>  <input id='mea_beschreibung' placeholder='Polenta ist ein xxx, ...' class='form-control'  type='text' />    </div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Allergene: </label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-apple'></i></span>  <input id='mea_allergene' data-role='tagsinput' class='form-control'  type='text'/>    </div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Produktionsplatz: * </label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-tasks'></i></span>  "+<?php echo "\"";printComboBoxOfNestedArray_general($all_pro->db_loadAllProFromDB(),':pro_id',2);echo "\""; ?>+"</div>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-info' name='mea_add' onclick='add_mea_to_db()'>Save <span class='glyphicon glyphicon-send'></span></button>  </div></div></fieldset></form>";
	<?php unset($all_pro); ?>

	$.SimpleLightbox.open({
		content:data,
		elementClass: 'slbContentEl'
	});

	$('#mea_allergene').tagsinput({
		maxChars:1,maxTags:20
	});
	$('#mea_allergene').tagsinput('refresh'); //to notify bootstrap that new tag input available and apply new settings from above
}

//Mea ADD to DB
function add_mea_to_db() {
	var tags = normalize_tags(document.getElementById('mea_allergene').value);

	$.ajax({
    	type: "POST",
    	url: "../php/meals/_meal.php",
    	data: {
    		add_mea_to_db : "isset",
    		mea_titel : document.getElementById('mea_titel').value,
    		mea_beschreibung : document.getElementById('mea_beschreibung').value,
    		mea_allergene : tags,
    		pro_id : document.getElementById('pro_id').value
    	},
    	success: function(data){
        	//document.getElementById('emp_liste_inner').innerHTML = data;
        	//data = echo ausgaben von php datei
        	//$.notify("Mitarbeiterliste aktualisiert.","success");
    		eval(data);
			if(error!==true) {
				setTimeout(function() {window.location.reload(true)},1800);
			}
    		//console.log(data);
    		//console.log("INFO: New Employee submitted to _emp.php");
    	},
    	error: function(data) {
    		$.notify("Daten nicht an den Server übertragen!","error");
    		console.error("Daten konnten nicht zu _prodplatz.php übertragen werden!");
    		console.log(data);
    	}
	});
}

//Mea Update Form
function show_mea_upd_form() {
	<?php 
		$all_pro = _prodplatz::__construct_empty();
		$all_mea = _meal::__construct_empty();
	?>
	var data = "<form class='well form-horizontal' id='contact_form'><fieldset style='width:800px;'><!-- Form Name --><legend>Update Mahlzeit</legend><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >ID *: </label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-tasks'></i></span>  "+<?php echo "\"";printComboBoxOfNestedArray_general($all_mea->db_loadAllMeaFromDB(),'mea_id',2);echo "\""; ?>+"</div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label'>Titel *</label>    <div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-star-empty'></i></span>  <input id='mea_titel' placeholder='Polenta,...(required)' class='form-control'  type='text' />    </div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Beschreibung: </label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-apple'></i></span>  <input id='mea_beschreibung' placeholder='Polenta ist ein xxx, ...' class='form-control'  type='text' />    </div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Allergene: </label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-apple'></i></span>  <input id='mea_allergene' data-role='tagsinput' class='form-control'  type='text' />    </div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Produktionsplatz: </label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-tasks'></i></span>  "+<?php echo "\"";printComboBoxOfNestedArray_general($all_pro->db_loadAllProFromDB(),':pro_id',2);echo "\""; ?>+"</div>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-info' name='mea_add' onclick='upd_mea_to_db()'>Save <span class='glyphicon glyphicon-send'></span></button>  </div></div></fieldset></form>";
	<?php unset($all_pro); ?>

	$.SimpleLightbox.open({
		content:data,
		elementClass: 'slbContentEl'
	});

	$('#mea_allergene').tagsinput({
		maxChars:1,maxTags:20
	});
	$('#mea_allergene').tagsinput('refresh'); //to notify bootstrap that new tag input available and apply new settings from above

	upd_load_Mahlzeit_toForm(); //Lade Employee gleich rein.
}

function upd_load_Mahlzeit_toForm() {
	$.ajax({
		type: "POST",
		url: "../php/meals/_meal.php",
		data: {
			upd_mea_formOnChange : "isset",
			mea_id : document.getElementById('mea_id').value
		},
		success: function(data){
			//Execute echo Ausgaben aus PHP Datei
			eval(data);
			//HIER KEIN REFRESH, da diese Funktion Daten ins Formular ladet!
			//console.log(data);
		},
		error: function(data) {
			$.notify("Mahlzeit konnte nicht geladen werden!","error");
			console.error("Daten konnten nicht zu _meal.php übertragen werden!");
			console.log(data);
		}
	});
}

function upd_mea_to_db() {
	
	$.ajax({
		type: "POST",
		//datatype: 'script',
		url: "../php/meals/_meal.php",
		data: {
			upd_mea_to_db : "isset",
			mea_id : document.getElementById('mea_id').value,
    		mea_titel : document.getElementById('mea_titel').value,
    		mea_beschreibung : document.getElementById('mea_beschreibung').value,
    		mea_allergene : normalize_tags(document.getElementById('mea_allergene').value),
    		pro_id : document.getElementById('pro_id').value
		},
		success: function(data){
			//Execute echo Ausgaben aus PHP Datei
			eval(data);
			if(error!==true) {
				setTimeout(function() {window.location.reload(true)},1800);
			}
			//console.log(data);
		},
		error: function(data) {
			$.notify("Daten nicht an den Server übertragen!","error");
			console.error("Daten konnten nicht zu _emp.php übertragen werden!");
			console.log(data);
		}
	});
}

//UPDATE MEA PROCECURE END----------------------------
//--------------------------------------------------------------------------------
//MA Delete Form
function show_mea_delete_form() {
	<?php $all_mea = _meal::__construct_empty();?>
	var data = "<form class='well form-horizontal' method='post'  id='contact_form'><fieldset style='width:800px;'><!-- Form Name --><legend>Lösche Speisen</legend><div class='form-group'><label class='col-md-4 control-label'>ID *</label><div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-sunglasses'></i></span>  "+<?php echo "\"";printComboBoxOfNestedArray_general($all_mea->db_loadAllMeaFromDB(),':mea_id',2);echo "\""; ?>+"</div>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-warning' name='emp_del' onclick='del_mea_from_db()'>Lösche Mahlzeit <span class='glyphicon glyphicon-trash'></span></button>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-danger' name='mea_del_all' onclick='del_all_my_mea_from_db()'>Lösche alle meine Mahlzeiten <span class='glyphicon glyphicon-trash'></span></button>  </div></div></fieldset></form>";
	<?php unset($all_pro); ?>
	$.SimpleLightbox.open({
		content:data,
		elementClass: 'slbContentEl'
	});
}

function del_all_my_mea_from_db() {
	$.ajax({
		type: "POST",
		//datatype: 'script',
		url: "../php/meals/_meal.php",
		data: {
			start_procedure : "db_deleteAllMyMeal"
		},
		success: function(data){
			//Execute echo Ausgaben aus PHP Datei
			eval(data);
			$.notify("Alle Mahlzeiten gelöscht!","success");
			setTimeout(function() {window.location.reload(true)},1800);
			//console.log(data);
		},
		error: function(data) {
			$.notify("Daten nicht an den Server übertragen!","error");
			console.error("Daten konnten nicht zu _meal.php übertragen werden!");
			console.log(data);
		}
	});
}

function del_mea_from_db() {
	$.ajax({
		type: "POST",
		//datatype: 'script',
		url: "../php/meals/_meal.php",
		data: {
			del_mea_from_db : "isset",
			mea_id : document.getElementById('mea_id').value
		},
		success: function(data){
			//Execute echo Ausgaben aus PHP Datei
			eval(data);
			if(error!==true) {
				setTimeout(function() {window.location.reload(true)},1800);
			}
			//console.log(data);
		},
		error: function(data) {
			$.notify("Daten nicht an den Server übertragen!","error");
			console.error("Daten konnten nicht zu _meal.php übertragen werden!");
			console.log(data);
		}
	});
}


// Produktionsplatz FUNCTIONS ##############################################################################################################################################################################
//Produktionsplatz anzeigen -------------------------------------------------------

function refresh_pro_list() {
	$.ajax({
    	type: "POST",
    	url: "../php/meals/_prodplatz.php",
    	data: {start_procedure : "db_printAllProFromDB"},
    	success: function(data){
        	//document.getElementById('emp_liste_inner').innerHTML = data;
        	//data = echo ausgaben von php datei
        	$.notify("Produktionsplätze aktualisiert.","info");
        	$.SimpleLightbox.open({
    			content:data,
    			elementClass: 'slbContentEl'
        	});
    	},
    	error: function(data) {
    		$.notify("Produktionsplätze nicht aktualisiert!","error");
    	}
	});
}

//Kürzel ADD-Form ---------------------------------------------------
function show_pro_add_form() {
var data = "<form class='well form-horizontal' method='post'  id='contact_form'><fieldset style='width:800px;'><!-- Form Name --><legend>Neuer Produktionsplatz</legend><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label'>Kürzel *</label>    <div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-console'></i></span>  <input maxlength='15' id='pro_id' placeholder='z.B. 1, K, ...(required)' class='form-control'  type='text' required>    </div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Beschreibung: </label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-console'></i></span>  <input id='pro_beschreibung' placeholder='Beilagen, Konditorei, ...' class='form-control'  type='text' required>    </div>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-info' name='ku_add' onclick='add_pro_to_db()'>Save <span class='glyphicon glyphicon-send'></span></button>  </div></div></fieldset></form>";

	$.SimpleLightbox.open({
		content:data,
		elementClass: 'slbContentEl'
	});
}

//KU ADD to DB
function add_pro_to_db() {
	$.ajax({
    	type: "POST",
    	//datatype: 'script',
    	url: "../php/meals/_prodplatz.php",
    	data: {
    		add_pro_to_db : "isset",
    		pro_id : document.getElementById('pro_id').value,
    		pro_beschreibung : document.getElementById('pro_beschreibung').value
    	},
    	success: function(data){
        	//document.getElementById('emp_liste_inner').innerHTML = data;
        	//data = echo ausgaben von php datei
        	//$.notify("Mitarbeiterliste aktualisiert.","success");
    		eval(data);
			if(error!==true) {
				setTimeout(function() {window.location.reload(true)},1800);
			}
    		//console.log(data);
    		//console.log("INFO: New Employee submitted to _emp.php");
    	},
    	error: function(data) {
    		$.notify("Daten nicht an den Server übertragen!","error");
    		console.error("Daten konnten nicht zu _prodplatz.php übertragen werden!");
    		console.log(data);
    	}
	});
}

//Pro Update Form
function show_pro_upd_form() {
	<?php $all_pro = _prodplatz::__construct_empty();?>
	var data = "<form class='well form-horizontal' method='post'  id='contact_form'><fieldset style='width:800px;'><!-- Form Name --><legend>Update Produktionsplatz</legend><div class='form-group'>  <label class='col-md-4 control-label'>Edit Prod.Platz: *</label>    <div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-transfer'></i></span>"+<?php echo "\"";printComboBoxOfNestedArray_general($all_pro->db_loadAllProFromDB(),'pro_id',2);echo "\""; ?>+"</div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Neue Beschreibung: *</label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-user'></i></span>  <input id='pro_beschreibung' placeholder='Konditorei, Fleischplatz etc. (required)' class='form-control'  type='text' required>    </div>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-primary' name='ku_upd' onclick='upd_pro_to_db()'>Save <span class='glyphicon glyphicon-send'></span></button>  </div></div></fieldset></form>";
	<?php unset($all_pro); ?>

	$.SimpleLightbox.open({
		content:data,
		elementClass: 'slbContentEl'
	});

	upd_load_Produktionsplatz_toForm(); //Lade Employee gleich rein.
}

function upd_load_Produktionsplatz_toForm() {
	$.ajax({
		type: "POST",
		//datatype: 'script',
		url: "../php/meals/_prodplatz.php",
		data: {
			upd_pro_formOnChange : "isset",
			pro_id : document.getElementById('pro_id').value
		},
		success: function(data){
			//Execute echo Ausgaben aus PHP Datei
			eval(data);
			//HIER KEIN REFRESH, da diese Funktion Daten ins Formular ladet!
			//console.log(data);
		},
		error: function(data) {
			$.notify("Produktionsplatz konnte nicht geladen werden!","error");
			console.error("Daten konnten nicht zu _prodplatz.php übertragen werden!");
			console.log(data);
		}
	});
}

function upd_pro_to_db() {
	$.ajax({
		type: "POST",
		//datatype: 'script',
		url: "../php/meals/_prodplatz.php",
		data: {
			upd_pro_to_db : "isset",
			pro_id : document.getElementById('pro_id').value,
			pro_beschreibung : document.getElementById('pro_beschreibung').value
		},
		success: function(data){
			//Execute echo Ausgaben aus PHP Datei
			eval(data);
			if(error!==true) {
				setTimeout(function() {window.location.reload(true)},1800);
			}
			//console.log(data);
		},
		error: function(data) {
			$.notify("Daten nicht an den Server übertragen!","error");
			console.error("Daten konnten nicht zu _emp.php übertragen werden!");
			console.log(data);
		}
	});
}

//UPDATE KÜRZEL PROCECURE END----------------------------
//--------------------------------------------------------------------------------
//MA Delete Form
function show_pro_delete_form() {
	<?php $all_pro = _prodplatz::__construct_empty();?>
	var data = "<form class='well form-horizontal' method='post'  id='contact_form'><fieldset style='width:800px;'><!-- Form Name --><legend>Lösche Produktionsplatz</legend><div class='form-group'><label class='col-md-4 control-label'>Kürzel *</label><div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-sunglasses'></i></span>  "+<?php echo "\"";printComboBoxOfNestedArray_general($all_pro->db_loadAllProFromDB(),'pro_id',2);echo "\""; ?>+"</div>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-warning' name='emp_del' onclick='del_pro_from_db()'>Lösche Kürzel <span class='glyphicon glyphicon-trash'></span></button>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-danger' name='pro_del_all' onclick='del_all_my_pro_from_db()'>Lösche alle meine Kürzel <span class='glyphicon glyphicon-trash'></span></button>  </div></div></fieldset></form>";
	<?php unset($all_pro); ?>
	$.SimpleLightbox.open({
		content:data,
		elementClass: 'slbContentEl'
	});
}

function del_all_my_pro_from_db() {
	$.ajax({
		type: "POST",
		//datatype: 'script',
		url: "../php/meals/_prodplatz.php",
		data: {
			start_procedure : "db_deleteAllMyPro"
		},
		success: function(data){
			//Execute echo Ausgaben aus PHP Datei
			eval(data);
			$.notify("Alle Produktionsplätze gelöscht!","success");
			setTimeout(function() {window.location.reload(true)},1800);
			//console.log(data);
		},
		error: function(data) {
			$.notify("Daten nicht an den Server übertragen!","error");
			console.error("Daten konnten nicht zu _prodplatz.php übertragen werden!");
			console.log(data);
		}
	});
}

function del_pro_from_db() {
	$.ajax({
		type: "POST",
		//datatype: 'script',
		url: "../php/meals/_prodplatz.php",
		data: {
			del_pro_from_db : "isset",
			pro_id : document.getElementById('pro_id').value
		},
		success: function(data){
			//Execute echo Ausgaben aus PHP Datei
			eval(data);
			if(error!==true) {
				setTimeout(function() {window.location.reload(true)},1800);
			}
			//console.log(data);
		},
		error: function(data) {
			$.notify("Daten nicht an den Server übertragen!","error");
			console.error("Daten konnten nicht zu _prodplatz.php übertragen werden!");
			console.log(data);
		}
	});
}

</script>