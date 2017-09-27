<script type="text/javascript">
//######################################################################################
//################################ EXPORT HANDLING #####################################
//######################################################################################
function show_export_form(sql_statement) {
	var data = "<form class='well form-horizontal' method='post'  id='contact_form'><fieldset style='width:800px;'><!-- Form Name --><legend>Exportiere Speisen</legend><div class='form-group'>  <label class='col-md-4 control-label'>Gewünschter Dateiname:<br /> (nicht erforderlich)</label>    <div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-transfer'></i></span><input id='filename' name='filename' type='text' class='form-control' placeholder='meine_datei'/></div>  </div></div><!--RADIO--><div class='form-group'>  <label class='col-md-4 control-label'>Dateityp:</label>    <div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <input id='xlsx' type='radio' name='file_type' checked value='xlsx'/><label style='margin:0 0 -3px 0;' for='xlsx'>&nbsp;.XLSX (Excel 2007)</label><br /><input id='xls' type='radio' name='file_type' value='xls'/><label style='margin:0 0 -3px 0;' for='xls'>&nbsp;.XLS (Excel 2005)</label><br /><input id='csv' type='radio' name='file_type' value='csv'/><label style='margin:0 0 -3px 0;' for='csv'>&nbsp;.CSV (Comma-separated-file)</label></div>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-primary' name='export_data' onclick='send_export_request(&#39;"+sql_statement+"&#39;)'>Export + Download <span class='glyphicon glyphicon-send'></span></button>  </div></div></fieldset></form>";
	$.SimpleLightbox.open({
		content:data,
		elementClass: 'slbContentEl'
	});
}

function send_export_request(sql_statement) {
	//Choose DB-Statement
	switch(sql_statement) {
		case '1': sql_statement = "SELECT * FROM Meal;";break;
		case '2': sql_statement="SELECT (kpn_komponente_1||', '||kpn_komponente_2||', '||kpn_komponente_3||', '||kpn_komponente_4) as xxx_Bezeichnung,* FROM v_Meal_Monitor_Related;";break;
		default: sql_statement = "SELECT * FROM Meal;";
	}

	var custom_file_name = document.getElementById('filename').value;
	var file_type_name = document.getElementsByName('file_type');
	var file_type = [];
	for (var box of file_type_name) {
		if (box.checked) {
			file_type['file_type'] = box.value;
		}
	}

	$.ajax({
		type: "POST",
		url: "../php/CSV_handler.php",
		data: {
			sql_statement : sql_statement,
			hasheadings : "false",
			filename : custom_file_name
		},
		success: function(data){
			eval(data);
			if (finished === 'true') {
				post(exported_file,file_type);
			}
		},
		error: function(data) {
			$.notify("Daten nicht an den Server übertragen!","error");
			console.error("Daten konnten nicht zu CSV_handler.php übertragen werden!");
			console.log(data);
		}
	});
}

function post(exported_file,params) { //give params = "" to just download file
	var form = document.createElement("form");
	form.setAttribute("method","post");
	form.setAttribute("action","../php/CSV_handler.php");
	//form.setAttribute("target","_blank");

	var hiddenField = document.createElement("input");
	hiddenField.setAttribute("type","hidden");
	hiddenField.setAttribute("name","download_csv");
	hiddenField.setAttribute("value",exported_file);
	form.appendChild(hiddenField);


	if (Array.isArray(params)) { //Do not add param hidden fields if params is not an array
		for (var key in params) {
			if(params.hasOwnProperty(key)) {
				hiddenField = document.createElement("input");
				hiddenField.setAttribute("type","hidden");
				hiddenField.setAttribute("name",key);
				hiddenField.setAttribute("value",params[key]);

				form.appendChild(hiddenField);
			}
		}
	}
	document.body.appendChild(form);
	form.submit();
}
</script>