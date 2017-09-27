

<script type="text/javascript">


function mmo_menu(what) {
	switch(what) {
		case 'show': $.notify('Funktion vorübergehend deaktiviert.','info');/*refresh_mmo_list()*/break;
		case 'add': $.notify('Daten werden nicht gespeichert!','warn');show_mmo_add_form();break;
		case 'edit': $.notify('Funktion noch nicht verfügbar!','info');break;
		case 'delete': $.notify('Funktion noch nicht verfügbar!','info');break;
		default: console.error("Modul 'Monitor': Do not know what to do.");
	}
}


// MAHLZEIT FUNCTIONS ##############################################################################################################################################################################
//Mea anzeigen -------------------------------------------------------

function refresh_mmo_list() {
	$.ajax({
    	type: "POST",
    	url: "../php/meals/_meal_monitor.php",
    	data: {start_procedure : "db_printAllMmoANDRelTables"},
    	success: function(data){
        	//document.getElementById('emp_liste_inner').innerHTML = data;
        	//data = echo ausgaben von php datei
        	$.notify("Speisendetails aktualisiert.","info");
        	$.SimpleLightbox.open({
    			content:data,
    			elementClass: 'slbContentEl'
        	});
    	},
    	error: function(data) {
    		$.notify("Speisendetails nicht aktualisiert!","error");
    	}
	});
}

//Mea ADD-Form ---------------------------------------------------
function show_mmo_add_form() {
	<?php $all_mmo = _meal_monitor::__construct_empty();?>
	var data = "<form class='well form-horizontal' id='contact_form'><fieldset style='width:800px;'><!-- Form Name --><legend>Neue Zuweisung</legend><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label'>Plan: *</label>    <div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-star-empty'></i></span>  "+<?php echo "\"";printComboBoxOfNestedArray_general($all_mmo->db_loadAllXFromDB("SELECT pln_id,('Plan '||pln_id) as pln_id_text FROM Plan;"),'pln_id',2);echo "\""; ?>+"    </div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Tageszeit: *</label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-apple'></i></span>"+<?php echo "\"";printComboBoxOfNestedArray_general($all_mmo->db_loadAllXFromDB("SELECT tgz_id FROM Tageszeit;"),'tgz_id',1);echo "\""; ?>+"</div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Wochentag: *</label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-apple'></i></span> "+<?php echo "\"";printComboBoxOfNestedArray_general($all_mmo->db_loadAllXFromDB("SELECT wtg_id,wtg_kurz,wtg_lang FROM Wochentag;"),'wtg_id',3);echo "\""; ?>+"</div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Kostart: </label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-apple'></i></span> "+<?php echo "\"";printComboBoxOfNestedArray_general($all_mmo->db_loadAllXFromDB("SELECT kta_id,kta_lang,kta_kurz FROM Kostart;"),'kta_id',3);echo "\""; ?>+"</div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Komponente 1: *</label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-tasks'></i></span>"+<?php echo "\"";printComboBoxOfNestedArray_general($all_mmo->db_loadAllXFromDB("SELECT kpn_id,mea_titel,kpn_menge FROM (SELECT * FROM Komponente as k LEFT JOIN Meal as m ON k.kpn_gericht=m.mea_id);"),'kpn_id_1',3);echo "\""; ?>+"</div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Komponente 2: </label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-tasks'></i></span>"+<?php echo "\"";printComboBoxOfNestedArray_general($all_mmo->db_loadAllXFromDB("SELECT kpn_id,mea_titel,kpn_menge FROM (SELECT * FROM Komponente as k LEFT JOIN Meal as m ON k.kpn_gericht=m.mea_id);"),'kpn_id_2',3);echo "\""; ?>+"</div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Komponente 3: </label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-tasks'></i></span>"+<?php echo "\"";printComboBoxOfNestedArray_general($all_mmo->db_loadAllXFromDB("SELECT kpn_id,mea_titel,kpn_menge FROM (SELECT * FROM Komponente as k LEFT JOIN Meal as m ON k.kpn_gericht=m.mea_id);"),'kpn_id_3',3);echo "\""; ?>+"</div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Komponente 4: </label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-tasks'></i></span>"+<?php echo "\"";printComboBoxOfNestedArray_general($all_mmo->db_loadAllXFromDB("SELECT kpn_id,mea_titel,kpn_menge FROM (SELECT * FROM Komponente as k LEFT JOIN Meal as m ON k.kpn_gericht=m.mea_id);"),'kpn_id_4',3);echo "\""; ?>+"</div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Condiments: </label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-tasks'></i></span><input type='text' id='mmo_condiments' placeholder='Place condiments here..' class='form-control'/></div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Garnitur: </label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-tasks'></i></span><input type='text' id='mmo_garnitur' placeholder='Place Garnitur here..' class='form-control'/></div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Gewicht in Gramm: </label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-tasks'></i></span><input type='number' id='mmo_gewicht_gramm' placeholder='Weight in gramm' class='form-control'/></div>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-info' name='mea_add' onclick='add_mmo_to_db()'>Save <span class='glyphicon glyphicon-send'></span></button>  </div></div></fieldset></form>";
	<?php unset($all_mo); ?>

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
function add_mmo_to_db() {

	$.ajax({
    	type: "POST",
    	url: "../php/meals/_meal_monitor.php",
    	data: {
    		DO_NOTHING_add_mmo_to_db : "isset",
    		pln_id : document.getElementById('pln_id').value,
    		tgz_id : document.getElementById('tgz_id').value,
    		wtg_id : document.getElementById('wtg_id').value,
    		kta_id : document.getElementById('kta_id').value,
    		kpn_id_1 : document.getElementById('kpn_id_1').value,
    		kpn_id_2 : document.getElementById('kpn_id_2').value,
    		kpn_id_3 : document.getElementById('kpn_id_3').value,
    		kpn_id_4 : document.getElementById('kpn_id_4').value,
    		mmo_condiments : document.getElementById('mmo_condiments').value,
    		mmo_garnitur : document.getElementById('mmo_garnitur').value,
    		mmo_gewicht_gramm : document.getElementById('mmo_gewicht_gramm').value
    	},
    	success: function(data){
    		$.notify("INFO: Funktion steht noch nicht zur Verfügung. ","info");
        	//document.getElementById('emp_liste_inner').innerHTML = data;
        	//data = echo ausgaben von php datei
        	//$.notify("Mitarbeiterliste aktualisiert.","success");
    		console.log(data);
			if(error!==true) {
				setTimeout(function() {window.location.reload(true)},1800);
			}
    		//console.log(data);
    		//console.log("INFO: New Employee submitted to _emp.php");
    	},
    	error: function(data) {
    		$.notify("Daten nicht an den Server übertragen!","error");
    		console.error("Daten konnten nicht zu _meal_monitor.php übertragen werden!");
    		console.log(data);
    	}
	});
}

/*Mea Update Form
function show_mea_upd_form() {
	<?php 
		/*$all_pro = _prodplatz::__construct_empty();
		$all_mea = _meal::__construct_empty();*/
	?>
	var data = "<form class='well form-horizontal' id='contact_form'><fieldset style='width:800px;'><!-- Form Name --><legend>Update Mahlzeit</legend><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >ID *: </label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-tasks'></i></span>  "+<?php //echo "\"";printComboBoxOfNestedArray_general($all_mea->db_loadAllMeaFromDB(),'mea_id',2);echo "\""; ?>+"</div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label'>Titel *</label>    <div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-star-empty'></i></span>  <input id='mea_titel' placeholder='Polenta,...(required)' class='form-control'  type='text' />    </div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Beschreibung: </label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-apple'></i></span>  <input id='mea_beschreibung' placeholder='Polenta ist ein xxx, ...' class='form-control'  type='text' />    </div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Allergene: </label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-apple'></i></span>  <input id='mea_allergene' data-role='tagsinput' class='form-control'  type='text' />    </div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Produktionsplatz: </label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-tasks'></i></span>  "+<?php //echo "\"";printComboBoxOfNestedArray_general($all_pro->db_loadAllProFromDB(),':pro_id',2);echo "\""; ?>+"</div>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-info' name='mea_add' onclick='upd_mea_to_db()'>Save <span class='glyphicon glyphicon-send'></span></button>  </div></div></fieldset></form>";
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
	<?php //$all_mea = _meal::__construct_empty();?>
	var data = "<form class='well form-horizontal' method='post'  id='contact_form'><fieldset style='width:800px;'><!-- Form Name --><legend>Lösche Speisen</legend><div class='form-group'><label class='col-md-4 control-label'>ID *</label><div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-sunglasses'></i></span>  "+<?php //echo "\"";printComboBoxOfNestedArray_general($all_mea->db_loadAllMeaFromDB(),'mea_id',2);echo "\""; ?>+"</div>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-warning' name='emp_del' onclick='del_mea_from_db()'>Lösche Mahlzeit <span class='glyphicon glyphicon-trash'></span></button>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-danger' name='mea_del_all' onclick='del_all_my_mea_from_db()'>Lösche alle meine Mahlzeiten <span class='glyphicon glyphicon-trash'></span></button>  </div></div></fieldset></form>";
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


*/
</script>