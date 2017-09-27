<?php if (!isset($_SESSION)) {session_start();}
require_once '../php/_auth.php';session_authenticate(); 

require_once '../php/_note.php';
?>


<html>
<head>
<?php 
	require_once '../include/link_script_meta_import.php'; 
    ?>
	<title>Küche - Notizen</title>
	<!-- JQuery UI eig. nur auf Notizen.php notwendig -->
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"/>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<link rel="stylesheet" href="../css/notizen.css"/>
	<?php require_once "../js/notizen.php"; ?>

</head>
<body onload="page_load()">
<?php 
include_once '../include/nav.php'; ?>
<div id="page">
	<div id="page_info">
		<h1 id="page_heading">Notizen - 'WSDT-Küche'</h1>
		<div id="page_description">
			Hier können Sie einfach und schnell private Notizen abspeichern.  
			<div class="note_menu">
				<img src="../img/add.png" alt="add_note" title="Erstellen Sie eine neue Notiz" onclick="createNewNote();"/>
				<img src="../img/show_hide.png" id="show_hide_note" alt="show_hide_note" title="Blenden Sie Notizen vorübergehend aus" onclick="notify_handler_show_hide()"/>
				<img src="../img/delete.png" alt="delete_note" id="delete_all_notes" title="Löschen Sie alle Notizen" onclick="notify_handler_deleteAll()"/>
				<img src="../img/print.png" alt="print_notes" id="print_notes" title="Drucken Sie Ihre Notizen" onclick="window.print();"/>

				<div id="note_search_div">
					<div id="note_search_options">
						<p><label for="search_title">&nbsp;Nach Titel suchen</label><input type="radio" value="search_title" id="search_title" name="search_for"/></p>
						<p><label for="search_content">&nbsp;Nach Inhalt suchen</label><input type="radio" value="search_content" id="search_content" name="search_for"/></p>
						<p><label for="search_all">&nbsp;Komplette Suche</label><input type="radio" value="search_all" id="search_all" name="search_for" checked/></p>
					</div>
					<input type="text" placeholder="search notes" id="search_notes" class="form-control" style="width:150px;float:right;margin-right:5px;" />
					<input type="button" class="btn btn-info" value="Search" style="float:right;margin-right:5px;" onclick="searchMenu()"/>
				</div>


				<!-- hide trash and show trash -->
			</div>
		</div>
	</div>

	<div id="note_trash" class="trash" title="Remove" onclick="drag_on_me()"><span class="lid"></span><span class="can"></span></div>

	<div id="page_content" style="min-height:100%;height:1500px;">

	</div>
	<!--<div id="page_enlarger" onclick="enlarge_page();">Enlarge Page (click)</div>-->
</div>

<!-- Sternliste für fleißige MA, auch druckbar mit Datum, Zusatzaufgaben etc.  -->
<?php include_once '../include/footer.php'; ?>
	
</body>
</html>
<?php exit(); ?>