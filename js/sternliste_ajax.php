<script type="text/javascript">
// ASSESSMENT FUNCTIONS ##############################################################################################################################################################################
//Mitarbeiter anzeigen -------------------------------------------------------
function refresh_ass_list() {
	$.ajax({
    	type: "POST",
    	url: "../php/_ass.php",
    	data: {refresh_ass_list : "isset"},
    	success: function(data){
        	//document.getElementById('emp_liste_inner').innerHTML = data;
        	//data = echo ausgaben von php datei
        	$.notify("Assessments aktualisiert.","info");
        	$.SimpleLightbox.open({
    			content:data,
    			elementClass: 'slbContentEl'
        	});
    	},
    	error: function(data) {
    		$.notify("Assessments nicht aktualisiert!","error");
    	}
	});
}



//Assessment ADD-Form ---------------------------------------------------
function show_ass_add_form() {
	<?php 
	$all_ass = _ass::__construct_empty(); 
	$all_emp = _emp::__construct_empty();
	?>
	var data = "<form  class='well form-horizontal'><fieldset style='width:800px;'><!-- Form Name --><legend>New Assessment</legend><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label'>Amount of Stars *</label>    <div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-thumbs-up'></i></span>    "+<?php echo "\"";printStarrating('ass_add_amount_stars');echo "\""; ?>+"</div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Beschreibung *</label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-eye-open'></i></span>  <textarea id='ass_add_description' placeholder='Description (required)' class='form-control'></textarea>    </div>  </div></div><!-- Text input-->       <div class='form-group'>  <label class='col-md-4 control-label'>Welcher Mitarbeiter? *</label>      <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>        <span class='input-group-addon'><i class='glyphicon glyphicon-user'></i></span>"+<?php echo "\"";printComboBoxOfNestedArray($all_emp->db_loadAllEmpFromDB(),'ass_add_emp_id');echo "\""; ?>+"</div>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-info' name='ass_add' onclick='add_ass_to_db()'>Save <span class='glyphicon glyphicon-send'></span></button>  </div></div></fieldset></form>";
	<?php unset($all_ass); unset($all_emp); ?>

	$.SimpleLightbox.open({
		content:data,
		elementClass: 'slbContentEl'
	});
}

//ASS ADD to DB ----------------------------------------------------------
function add_ass_to_db() {
	var ass_add_amount_stars = "";
	var ass_add_amount_stars_array_result = [5,4,3,2,1]; //Starrating Inputs sind umgekehrt von ID her
	var ass_add_amount_stars_arrayclass = document.getElementsByClassName('rating-input');
	for (var i=0;i<ass_add_amount_stars_arrayclass.length;i++) {
		if (ass_add_amount_stars_arrayclass[i].checked === true) {
			ass_add_amount_stars = ass_add_amount_stars_array_result[i];
		}
	}

	$.ajax({
    	type: "POST",
    	//datatype: 'script',
    	url: "../php/_ass.php",
    	data: {
    		ass_add_to_db : "isset",
    		ass_add_amount_stars : ass_add_amount_stars, //erster param f. php $_POST[name], zweiter ist der Wert
    		ass_add_description : document.getElementById('ass_add_description').value,
    		ass_add_emp_id : document.getElementById('ass_add_emp_id').value,
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
    		console.error("Daten konnten nicht zu _ass.php übertragen werden!");
    		console.log(data);
    	}
	});
}


//--------------------------------------------------------------------------------
//ASS Delete Form
function show_ass_del_form() {
	<?php $all_ass = _ass::__construct_empty();?>
	var data = "<form class='well form-horizontal' action='../php/_ass.php' method='post'  id='contact_form'><fieldset style='width:800px;'><!-- Form Name --><legend>Delete Assessment</legend><div class='form-group'><label class='col-md-4 control-label'>Ass_ID *</label><div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-trash'></i></span>  "+<?php echo "\"";printComboBoxOfNestedArray($all_ass->db_loadAllAssFromDB(),'assdel_id');echo "\""; ?>+"<!--<input  id='assdel_id' placeholder='Ass_ID (required)' class='form-control'  type='text' required>-->    </div>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-warning' name='ass_del' onclick='del_ass_from_db()'>Delete Assessment <span class='glyphicon glyphicon-send'></span></button>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-danger' name='ass_del_all' onclick='del_all_ass_from_db()'>Delete ALL Assessments <span class='glyphicon glyphicon-send'></span></button>  </div></div></fieldset></form>";
	<?php unset($all_ass); ?>
	$.SimpleLightbox.open({
		content:data,
		elementClass: 'slbContentEl'
	});
}

function del_all_ass_from_db() {
	$.ajax({
		type: "POST",
		//datatype: 'script',
		url: "../php/_ass.php",
		data: {
			del_all_ass_from_db : "isset",
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

function del_ass_from_db() {
	$.ajax({
		type: "POST",
		url: "../php/_ass.php",
		data: {
			del_ass_from_db : "isset",
			ass_id : document.getElementById('assdel_id').value
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


// #################################################################################################################################################################################################
// #################################################################################################################################################################################################
// EMPLOYEE FUNCTIONS ##############################################################################################################################################################################
var type_request;
//Mitarbeiter anzeigen -------------------------------------------------------
function refresh_emp_list() {
	$.ajax({
    	type: "POST",
    	url: "../php/_emp.php",
    	data: {refresh_emp_list : "isset"},
    	success: function(data){
        	//document.getElementById('emp_liste_inner').innerHTML = data;
        	//data = echo ausgaben von php datei
        	//console.log(data);
        	console.log(data);
        	$.notify("Mitarbeiterliste aktualisiert.","info");
        	$.SimpleLightbox.open({
    			content:data,
    			elementClass: 'slbContentEl'
        	});
    	},
    	error: function(data) {
    		$.notify("MA-Liste nicht aktualisiert!","error");
    	}
	});
}



//Mitarbeiter ADD-Form ---------------------------------------------------
function show_emp_add_form() {
	var data = "<style>#success_message{ display: none;}</style><form class='well form-horizontal' action='../php/_emp.php' method='post'  id='contact_form'><fieldset style='width:800px;'><!-- Form Name --><legend>New Employee</legend><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label'>First Name *</label>    <div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-user'></i></span>  <input  id='empadd_first_name' placeholder='First Name (required)' class='form-control'  type='text' required id='emp_add_firstname'>    </div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Last Name *</label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-user'></i></span>  <input id='empadd_last_name' placeholder='Last Name (required)' class='form-control'  type='text' required id='emp_add_lastname'>    </div>  </div></div><!-- Text input-->       <div class='form-group'>  <label class='col-md-4 control-label'>E-Mail</label>      <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>        <span class='input-group-addon'><i class='glyphicon glyphicon-envelope'></i></span>  <input id='empadd_email' placeholder='E-Mail Address' class='form-control'  type='text' id='emp_add_email'>    </div>  </div></div><!-- Text input-->       <div class='form-group'>  <label class='col-md-4 control-label'>Phone #</label>      <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>        <span class='input-group-addon'><i class='glyphicon glyphicon-earphone'></i></span>  <input id='empadd_phone' placeholder='+4365 55 1212' class='form-control' type='text' id='emp_add_phone'>    </div>  </div></div><!-- Success message --><div class='alert alert-success' role='alert' id='success_message'>Success <i class='glyphicon glyphicon-thumbs-up'></i>Employee saved successfully!</div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-info' name='emp_add' onclick='add_emp_to_db()'>Save <span class='glyphicon glyphicon-send'></span></button>  </div></div></fieldset></form>";

	$.SimpleLightbox.open({
		content:data,
		elementClass: 'slbContentEl'
	});
}

//MA ADD to DB
function add_emp_to_db() {
	$.ajax({
    	type: "POST",
    	//datatype: 'script',
    	url: "../php/_emp.php",
    	data: {
    		add_emp_to_db : "isset",
    		first_name : document.getElementById('empadd_first_name').value,
    		last_name : document.getElementById('empadd_last_name').value,
    		email : document.getElementById('empadd_email').value,
    		phone : document.getElementById('empadd_phone').value
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
    		console.error("Daten konnten nicht zu _emp.php übertragen werden!");
    		console.log(data);
    	}
	});
}


//--------------------------------------------------------------------------------
//MA Delete Form
function show_emp_del_form() {
	<?php $all_emp = _emp::__construct_empty();?>
	var data = "<form class='well form-horizontal' action='../php/_emp.php' method='post'  id='contact_form'><fieldset style='width:800px;'><!-- Form Name --><legend>Delete Employee</legend><div class='form-group'><label class='col-md-4 control-label'>Emp_ID *</label><div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-sunglasses'></i></span>  "+<?php echo "\"";printComboBoxOfNestedArray($all_emp->db_loadAllEmpFromDB(),'empdel_id');echo "\""; ?>+"<!--<input  id='empdel_id' placeholder='Emp_ID (required)' class='form-control'  type='text' required>-->    </div>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-warning' name='emp_del' onclick='del_emp_from_db()'>Delete Employee <span class='glyphicon glyphicon-send'></span></button>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-danger' name='emp_del_all' onclick='del_all_emp_from_db()'>Delete ALL Employees <span class='glyphicon glyphicon-send'></span></button>  </div></div></fieldset></form>";
	<?php unset($all_emp); ?>
	$.SimpleLightbox.open({
		content:data,
		elementClass: 'slbContentEl'
	});
}

function del_all_emp_from_db() {
	$.ajax({
		type: "POST",
		//datatype: 'script',
		url: "../php/_emp.php",
		data: {
			del_all_emp_from_db : "isset",
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

function del_emp_from_db() {
	$.ajax({
		type: "POST",
		//datatype: 'script',
		url: "../php/_emp.php",
		data: {
			del_emp_from_db : "isset",
			emp_id : document.getElementById('empdel_id').value
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

//--------------------------------------------------------------------------------
//MA Update Form
function show_emp_upd_form() {
	<?php $all_emp = _emp::__construct_empty();?>
	var data = "<style>#success_message{ display: none;}</style><form class='well form-horizontal' action='../php/_emp.php' method='post'  id='contact_form'><fieldset style='width:800px;'><!-- Form Name --><legend>Update Employee</legend><div class='form-group'>  <label class='col-md-4 control-label'>Edit Employee: *</label>    <div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-transfer'></i></span>"+<?php echo "\"";printComboBoxOfNestedArray($all_emp->db_loadAllEmpFromDB(),'empupd_id');echo "\""; ?>+"</div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label'>First Name *</label>    <div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-user'></i></span>  <input placeholder='First Name (required)' class='form-control'  type='text' required id='empupd_first_name'>    </div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Last Name *</label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-user'></i></span>  <input id='empupd_last_name' placeholder='Last Name (required)' class='form-control'  type='text' required>    </div>  </div></div><!-- Text input-->       <div class='form-group'>  <label class='col-md-4 control-label'>E-Mail</label>      <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>        <span class='input-group-addon'><i class='glyphicon glyphicon-envelope'></i></span>  <input id='empupd_email' placeholder='E-Mail Address' class='form-control'  type='text'>    </div>  </div></div><!-- Text input-->       <div class='form-group'>  <label class='col-md-4 control-label'>Phone #</label>      <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>        <span class='input-group-addon'><i class='glyphicon glyphicon-earphone'></i></span>  <input id='empupd_phone' placeholder='+4365 55 1212' class='form-control' type='text'>    </div>  </div></div><!-- Success message --><div class='alert alert-success' role='alert' id='success_message'>Success <i class='glyphicon glyphicon-thumbs-up'></i>Employee saved successfully!</div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-primary' name='emp_upd' onclick='upd_emp_to_db()'>Save <span class='glyphicon glyphicon-send'></span></button>  </div></div></fieldset></form>";
	<?php unset($all_emp); ?>

	$.SimpleLightbox.open({
		content:data,
		elementClass: 'slbContentEl'
	});

	upd_load_Employee_toForm(); //Lade Employee gleich rein.
}

function upd_load_Employee_toForm() {
	$.ajax({
		type: "POST",
		//datatype: 'script',
		url: "../php/_emp.php",
		data: {
			upd_emp_formOnchange : "isset",
			empupd_id : document.getElementById('empupd_id').value,
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

function upd_emp_to_db() {
	$.ajax({
		type: "POST",
		//datatype: 'script',
		url: "../php/_emp.php",
		data: {
			upd_emp_to_db : "isset",
			empupd_id : document.getElementById('empupd_id').value,
			empupd_firstname : document.getElementById('empupd_first_name').value,
			empupd_lastname : document.getElementById('empupd_last_name').value,
			empupd_email : document.getElementById('empupd_email').value,
			empupd_phone : document.getElementById('empupd_phone').value
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