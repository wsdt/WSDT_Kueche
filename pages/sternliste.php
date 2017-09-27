<?php //if (!isset($_SESSION)) {session_start();}
require_once '../php/_auth.php';session_authenticate(); ?>

<html>
<head>
	<?php require_once '../include/link_script_meta_import.php'; ?>
	<title>Küche - Sternliste</title>
	<?php
	//Page-related imports
	require_once '../php/_emp.php'; //Employee class
	require_once '../php/_ass.php'; //Assessment Class
	?>


	<?php require_once "../js/sternliste_ajax.php"; ?>
</head>
<body>
<?php include_once '../include/nav.php'; ?>

<div id="page">
<div id="page_info">
<h1 id="page_heading">Sternliste - Küche</h1>
<div id="page_description">
	Möchten Sie überengagierte Mitarbeiter irgendwie für deren Leistungen belohnen? 
	Hier haben Sie die Möglichkeit die Art, den Zeitpunkt sowie andere Merkmale zu notieren, 
	um eventuell, zu einem anderen Zeitpunkt, die Zusatzleistung abzufertigen. 
</div>
</div>

<!-- Mit Stern arbeiten (nur Stern, also nicht negativ), max. positive Bewertungen löschbar 
evtl. Häckchen setzbar für bereits abgegolten -->

<!-- MODUL 1: Employee -->
<div id="page_content">
	<div class="page_content_module_row">
	<div class="page_content_module" id="employees">
		<!-- Bei Neuanlegung prüfen ob Vname schon vorgekommen ist sowie Nachname, aber nur benachrichtigen-->
		<h3 class="subheading">Mitarbeiter</h3>
		<div id="employees_description" class="page_content_module_description">
			Möchten Sie einen neuen Mitarbeiter hinzufügen, löschen oder bearbeiten?
		</div>
		<div class="db_sub_menu">
			<img src="../img/show.png" alt="show" title="Mitarbeiter anzeigen" width="30px" onclick="refresh_emp_list()" />
			<img src="../img/add.png" alt="add" title="Mitarbeiter hinzufügen" width="30px" onclick="show_emp_add_form()"/>
			<img src="../img/edit.png" alt="edit" title="Mitarbeiter editieren" width="30px" onclick="show_emp_upd_form()"/>
			<img src="../img/delete.png" alt="delete" title="Mitarbeiter löschen" width="30px" onclick="show_emp_del_form()"/>
		</div>

	</div>

	<!-- MODUL 2: Bewertungen -->
	<div class="page_content_module" id="assessment">
		<h3 class="subheading">Bewertungen</h3>
		<div id="assessment_description" class="page_content_module_description">
			Wenn Sie einen Mitarbeiter dabei beobachtet haben, wie dieser freiwillig (stille) Zusatzleistungen erbringt, dann können Sie dies hier vermerken und später nach eigenem Ermessen belohnen!
		</div>
		<div class="db_sub_menu">
			<img src="../img/show.png" alt="show" title="Bewertungen anzeigen" width="30px" onclick="refresh_ass_list()"/>
			<img src="../img/add.png" alt="add" title="Bewertung hinzufügen" width="30px" onclick="show_ass_add_form()"/>
			<img src="../img/delete.png" alt="delete" title="Bewertung löschen" width="30px" onclick="show_ass_del_form()"/>
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