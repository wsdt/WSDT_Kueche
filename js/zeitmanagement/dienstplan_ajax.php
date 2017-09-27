

<script type="text/javascript">
// DP-Verw. FUNCTIONS ##############################################################################################################################################################################
//DP anzeigen -------------------------------------------------------

function show_dienstplan() {
	//TODO Jahr und Monat entgegennehmen
	var plan = document.getElementById('select_dp').value;

	$.ajax({
    	type: "POST",
    	url: "../php/zeitmanagement/_dienstplan.php",
    	data: {
    		show_dienstplan : "isset",
    		year : plan.substr(3), //TODO: Year etc. aus plan var extrahieren
    		month: plan.substr(0,2),
    	},
    	success: function(data){
        	//document.getElementById('emp_liste_inner').innerHTML = data;
        	//data = echo ausgaben von php datei
        	document.getElementById('plan_view').innerHTML += data;
        	$.notify("Dienstpläne aktualisiert.","info");
    	},
    	error: function(data) {
    		$.notify("Dienstpläne konnten nicht abgerufen werden!","error");
    		console.error(data);
    	}
	});
}


















// KÜRZEL FUNCTIONS ##############################################################################################################################################################################
//Kürzel anzeigen -------------------------------------------------------

function refresh_ku_list() {
	$.ajax({
    	type: "POST",
    	url: "../php/zeitmanagement/_dplkuerzel.php",
    	data: {refresh_ku_list : "isset"},
    	success: function(data){
        	//document.getElementById('emp_liste_inner').innerHTML = data;
        	//data = echo ausgaben von php datei
        	$.notify("DP-Kürzel aktualisiert.","info");
        	$.SimpleLightbox.open({
    			content:data,
    			elementClass: 'slbContentEl'
        	});
    	},
    	error: function(data) {
    		$.notify("DP-Kürzel nicht aktualisiert!","error");
    	}
	});
}

//Kürzel ADD-Form ---------------------------------------------------
function show_ku_add_form() {
var data = "<form class='well form-horizontal' method='post'  id='contact_form'><fieldset style='width:800px;'><!-- Form Name --><legend>Neues Kürzel</legend><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label'>Kürzel *</label>    <div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-console'></i></span>  <input  id='dpl_kuerzel' placeholder='E.g.: U, 2,.. (required)' class='form-control'  type='text' required>    </div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Wert von Kürzel (in Stunden) *</label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-console'></i></span>  <input id='dpl_kuerzelvalue' placeholder='8,4,...(required)' class='form-control'  type='number' required>    </div>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-info' name='ku_add' onclick='add_ku_to_db()'>Save <span class='glyphicon glyphicon-send'></span></button>  </div></div></fieldset></form>";

	$.SimpleLightbox.open({
		content:data,
		elementClass: 'slbContentEl'
	});
}

//KU ADD to DB
function add_ku_to_db() {
	$.ajax({
    	type: "POST",
    	//datatype: 'script',
    	url: "../php/zeitmanagement/_dplkuerzel.php",
    	data: {
    		add_ku_to_db : "isset",
    		dpl_kuerzel : document.getElementById('dpl_kuerzel').value,
    		dpl_kuerzelvalue : document.getElementById('dpl_kuerzelvalue').value
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
    		console.error("Daten konnten nicht zu _dpl_kuerzel.php übertragen werden!");
    		console.log(data);
    	}
	});
}


// UPDATE KÜRZEL PROCEDURE ----------------------------------------------
//MA Update Form
function show_ku_upd_form() {
	<?php $all_ku = _dplkuerzel::__construct_empty();?>
	var data = "<form class='well form-horizontal' method='post'  id='contact_form'><fieldset style='width:800px;'><!-- Form Name --><legend>Update Kürzel</legend><div class='form-group'>  <label class='col-md-4 control-label'>Edit Kürzel: *</label>    <div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-transfer'></i></span>"+<?php echo "\"";printComboBoxOfNestedArray_general($all_ku->db_loadAllKuFromDB(),'dpl_kuerzel',2);echo "\""; ?>+"</div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >DPL-Kürzel-Wert *</label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-user'></i></span>  <input id='dpl_kuerzelvalue' placeholder='E.g. 5,6... (required)' class='form-control'  type='number' required>    </div>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-primary' name='ku_upd' onclick='upd_ku_to_db()'>Save <span class='glyphicon glyphicon-send'></span></button>  </div></div></fieldset></form>";
	<?php unset($all_ku); ?>

	$.SimpleLightbox.open({
		content:data,
		elementClass: 'slbContentEl'
	});

	upd_load_Kuerzel_toForm(); //Lade Employee gleich rein.
}

function upd_load_Kuerzel_toForm() {
	$.ajax({
		type: "POST",
		//datatype: 'script',
		url: "../php/zeitmanagement/_dplkuerzel.php",
		data: {
			upd_ku_formOnchange : "isset",
			dpl_kuerzel : document.getElementById('dpl_kuerzel').value
		},
		success: function(data){
			//Execute echo Ausgaben aus PHP Datei
			eval(data);
			//HIER KEIN REFRESH, da diese Funktion Daten ins Formular ladet!
			//console.log(data);
		},
		error: function(data) {
			$.notify("Employee konnte nicht geladen werden!","error");
			console.error("Daten konnten nicht zu _emp.php übertragen werden!");
			console.log(data);
		}
	});
}

function upd_ku_to_db() {
	$.ajax({
		type: "POST",
		//datatype: 'script',
		url: "../php/zeitmanagement/_dplkuerzel.php",
		data: {
			upd_ku_to_db : "isset",
			dpl_kuerzel : document.getElementById('dpl_kuerzel').value,
			dpl_kuerzelvalue : document.getElementById('dpl_kuerzelvalue').value
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
function show_ku_del_form() {
	<?php $all_ku = _dplkuerzel::__construct_empty();?>
	var data = "<form class='well form-horizontal' method='post'  id='contact_form'><fieldset style='width:800px;'><!-- Form Name --><legend>Lösche Kürzel</legend><div class='form-group'><label class='col-md-4 control-label'>Kürzel *</label><div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-sunglasses'></i></span>  "+<?php echo "\"";printComboBoxOfNestedArray_general($all_ku->db_loadAllKuFromDB(),'dpl_kuerzel',2);echo "\""; ?>+"</div>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-warning' name='emp_del' onclick='del_ku_from_db()'>Lösche Kürzel <span class='glyphicon glyphicon-trash'></span></button>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-danger' name='ku_del_all' onclick='del_all_ku_from_db()'>Lösche alle meine Kürzel <span class='glyphicon glyphicon-trash'></span></button>  </div></div></fieldset></form>";
	<?php unset($all_emp); ?>
	$.SimpleLightbox.open({
		content:data,
		elementClass: 'slbContentEl'
	});
}

function del_all_ku_from_db() {
	$.ajax({
		type: "POST",
		//datatype: 'script',
		url: "../php/zeitmanagement/_dplkuerzel.php",
		data: {
			del_all_ku_from_db : "isset",
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
			console.error("Daten konnten nicht zu _dplkuerzel.php übertragen werden!");
			console.log(data);
		}
	});
}

function del_ku_from_db() {
	$.ajax({
		type: "POST",
		//datatype: 'script',
		url: "../php/zeitmanagement/_dplkuerzel.php",
		data: {
			del_ku_from_db : "isset",
			dpl_kuerzel : document.getElementById('dpl_kuerzel').value
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

</script>