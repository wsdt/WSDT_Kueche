<?php //if (!isset($_SESSION)) {session_start();}
require_once '../php/_auth.php';session_authenticate(); 

require_once './../php/functions.php';
require_once './../php/meals/_meal.php';
require_once './../php/meals/_prodplatz.php';
//require_once './../php/meals/_meal_monitor.php';
require_once './../js/CSV_handler_AJAX.php';
?>

<html>
<head>
	<?php require_once '../include/link_script_meta_import.php'; ?>
	<title>Küche - Resourcemanagement</title>
	<?php
	require_once '../js/meals/meals_ajax.php';
	//require_once '../js/meals/meal_monitor_ajax.php';
	?>

<!-- For Tags-Input -->
<link rel="stylesheet" href="../css/bootstrap-tagsinput.css"/>
<script src="../js/meals/bootstrap-tagsinput.js"></script>


</head>
<body>
<?php include_once '../include/nav.php';
require_once './../php/CSV_handler.php'; ?>

<div id="page">
<div id="page_info">
<h1 id="page_heading">Meals - Management</h1>
<div id="page_description">
	Verwalten Sie Ihre Dokumente sowie Menüs mit wenigen Klicks. 
</div>
</div>

<!-- MODUL 1: Produktionsplatz -->
<div id="page_content">
	<div class="page_content_module_row">
	<div class="page_content_module" id="prodplatz">
		<h3 class="subheading">Produktionsplatz</h3>
		<div id="dp_kuerzel_description" class="page_content_module_description">
			Verwalten Sie Ihre Produktionsplätze. Klassische Beispiele sind 'Konditorei', 'Beilagen', etc.
		</div>
		<div class="db_sub_menu">
			<img src="../img/show.png" alt="show" title="Produktionsplätze anzeigen" width="30px" onclick="prod_menu('show')" />
			<img src="../img/add.png" alt="add" title="Produktionsplatz hinzufügen" width="30px" onclick="prod_menu('add')"/>
			<img src="../img/edit.png" alt="edit" title="Produktionsplatz editieren" width="30px" onclick="prod_menu('edit')"/>
			<img src="../img/delete.png" alt="delete" title="Produktionsplatz löschen" width="30px" onclick="prod_menu('delete')"/>
		</div>

	</div>

	<!-- MODUL 2:  -->
	<div class="page_content_module" id="meal_management">
		<h3 class="subheading">Speisen</h3>
		<div id="assessment_description" class="page_content_module_description">
			Verwalten Sie Ihre Speisen auf einen Klick. 
		</div>

		<div class="db_sub_menu_right">
			<?php showImportForm("../php/meals/_meal","db_insertNewMeal"); ?>
			<img src="../img/export.png" alt="export" title="Export to CSV" class="special_align" onclick="show_export_form(1)"/>
		</div>

		<div class="db_sub_menu">
			<img src="../img/show.png" alt="show" title="Mahlzeiten anzeigen" onclick="mea_menu('show')"/>
			<img src="../img/add.png" alt="add" title="Mahlzeit hinzufügen" onclick="mea_menu('add')"/>
			<img src="../img/edit.png" alt="edit" title="Mahlzeit editieren" onclick="mea_menu('edit')"/>
			<img src="../img/delete.png" alt="delete" title="Mahlzeit löschen" onclick="mea_menu('delete')"/>
		</div>
	</div>
</div>
<div class="page_content_module_row">
	<div class="page_content_module" id="meal_monitor_management">
		<h3 class="subheading">Monitor (DISABLED)</h3>
		<div id="assessment_description" class="page_content_module_description">
			Verwalten Sie Ihre Speisen-Details auf einen Klick. Anschließend können Sie Ihre Daten als CSV u.Ä. exportieren, um diese von der IT-Abteilung angenehmerweise ins 'Monitor-System' übertragen zu lassen. 
		</div>

		<div class="db_sub_menu_right">
			<?php showImportForm("../php/meals/_meal_monitor","db_insertNewMmo"); ?>
			<img src="../img/export.png" alt="export" title="Export to CSV" class="special_align" onclick="show_export_form(2);$.notify('Exportfunktion wurde vorübergehend deaktiviert.','info');"/>
		</div>

		<script type="text/javascript">
		function mmo_menu(tmp) {
			$.notify('Dieses Modul wurde vorübergehend stillgelegt.\nGrund: Modul in Entwicklung.','info');
		}
		</script>

		<div class="db_sub_menu">
			<img src="../img/show.png" alt="show" title="Mahlzeiten anzeigen" onclick="mmo_menu('show')"/>
			<img src="../img/add.png" alt="add" title="Mahlzeit hinzufügen" onclick="mmo_menu('add')"/>
			<img src="../img/edit.png" alt="edit" title="Mahlzeit editieren" onclick="mmo_menu('edit')"/>
			<img src="../img/delete.png" alt="delete" title="Mahlzeit löschen" onclick="mmo_menu('delete')"/>
		</div>
	</div>

</div>


</div>
</div>


<!-- Sternliste für fleißige MA, auch druckbar mit Datum, Zusatzaufgaben etc.  -->
<?php include_once '../include/footer.php'; ?>


</body>
</html>
<?php exit(); ?>