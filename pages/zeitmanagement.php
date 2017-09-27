<?php //if (!isset($_SESSION)) {session_start();}
require_once '../php/_auth.php';session_authenticate(); 

require_once './../php/functions.php';
require_once './../php/zeitmanagement/_dienstplan.php';
require_once './../php/zeitmanagement/_dplkuerzel.php';
?>

<html>
<head>
	<?php require_once '../include/link_script_meta_import.php'; ?>
	<title>Küche - Resourcemanagement</title>
	<?php
	//Page-related imports
	require_once '../php/_emp.php'; //Employee class
	require_once '../js/zeitmanagement/dienstplan_ajax.php';
	?>

</head>
<body>
<?php include_once '../include/nav.php'; ?>

<div id="page">
<div id="page_info">
<h1 id="page_heading">Pläne - Zeitmanagement</h1>
<div id="page_description">
	Verwalten Sie Ihre Dienstpläne sowie Urlaubspläne immer und überall. Sollten Sie Urlaubs-/Dienstpläne auch anderen Accounts zur Verfügung stellen wollen, dann haben Sie auch hier dazu die Möglichkeit. 
</div>
</div>

<!-- MODUL 1: DP-Kürzel -->
<div id="page_content">
<marquee>Dienstplan bearbeiten etc. onchange (wie Notizen), aber auch readonly view machen</marquee>
	<div class="page_content_module_row">
	<div class="page_content_module" id="dp_kuerzel">
		<!-- Bei Neuanlegung prüfen ob Vname schon vorgekommen ist sowie Nachname, aber nur benachrichtigen-->
		<h3 class="subheading">Dienstplan - Kürzel</h3>
		<div id="dp_kuerzel_description" class="page_content_module_description">
			Auf Wunsch von Herrn Kern, haben Sie hier die Möglichkeit Kürzel für Ihren Dienstplan zu erstellen. Wenn Sie Werte in den Dienstplan eintragen, dann wird das Kürzel '1' (z.B.) in eine 8 umgewandelt. <br /><strong>Es können nur bekannte Kürzel gespeichert werden!</strong>
		</div>
		<div class="db_sub_menu">
			<img src="../img/show.png" alt="show" title="Kürzel anzeigen" width="30px" onclick="refresh_ku_list()" />
			<img src="../img/add.png" alt="add" title="Kürzel hinzufügen" width="30px" onclick="show_ku_add_form()"/>
			<img src="../img/edit.png" alt="edit" title="Kürzel editieren" width="30px" onclick="show_ku_upd_form()"/>
			<img src="../img/delete.png" alt="delete" title="Kürzel löschen" width="30px" onclick="show_ku_del_form()"/>
		</div>

	</div>

	<!-- MODUL 2: DP-Verwaltung -->
	<div class="page_content_module" id="dp_verwaltung">
		<h3 class="subheading">Dienstpläne</h3>
		<div id="assessment_description" class="page_content_module_description">
			Hier können Sie Dienstpläne einsehen, drucken, bearbeiten, löschen oder editieren. 
		</div>

		<div class="db_sub_menu" style="left:5px;right:auto;">
			<label for="select_dp">Dienstplan auswählen: </label>
			<select id="select_dp">
				<?php
				$dienstplaene = loadDPListAsArray();
				if ($dienstplaene === false) {
					echo "<option value='empty'>Kein Plan verfügbar</option>";
				} else {
					foreach ($dienstplaene as $dienstplan) {
						$label = sprintf('%02d',$dienstplan['pl_monat']).".".sprintf('%02d',$dienstplan['pl_jahr']); //zeige führende nullen
						echo "<option value='".$label."'>".$label."</option>";
					}
				}
				?>
			</select>
		</div>

		<div class="db_sub_menu">
			<img src="../img/show.png" alt="show" title="Dienstpläne anzeigen" width="30px" onclick="show_dienstplan()"/>
			<img src="../img/add.png" alt="add" title="Dienstplan hinzufügen" width="30px" onclick="show_ass_add_form()"/>
			<img src="../img/edit.png" alt="edit" title="Dienstplan editieren" width="30px" onclick="show_emp_upd_form()"/>
			<img src="../img/delete.png" alt="delete" title="Dienstplan löschen" width="30px" onclick="show_ass_del_form()"/>
		</div>

	</div>
</div>
<div class="page_content_module_row">
	<!-- MODUL 3: Urlaubsplan-Verwaltung -->
	<div class="page_content_module" id="up_verwaltung">
		<h3 class="subheading">Urlaubspläne</h3>
		<div id="assessment_description" class="page_content_module_description">
			Hier können Sie Urlaubspläne einsehen, drucken, bearbeiten, löschen oder editieren. 
		</div>
		<div class="db_sub_menu">
			<img src="../img/show.png" alt="show" title="Urlaubspläne anzeigen" width="30px" onclick="refresh_ass_list()"/>
			<img src="../img/add.png" alt="add" title="Urlaubsplan hinzufügen" width="30px" onclick="show_ass_add_form()"/>
			<img src="../img/edit.png" alt="edit" title="Urlaubsplan editieren" width="30px" onclick="show_emp_upd_form()"/>
			<img src="../img/delete.png" alt="delete" title="Urlaubsplan löschen" width="30px" onclick="show_ass_del_form()"/>
		</div>

	</div>
</div>

<!-- PLAN VIEW -->
<div id="plan_view"></div>



</div>
</div>


<!-- Sternliste für fleißige MA, auch druckbar mit Datum, Zusatzaufgaben etc.  -->
<?php include_once '../include/footer.php'; ?>


</body>
</html>
<?php exit(); ?>