<?php if (!isset($_SESSION)) {session_start();}require_once 'php/_auth.php';if(!isset($_SESSION)) {sessionStart('_auth');}//session_authenticate(); 
?>
<html>
<head>
<?php 
	require_once 'include/link_script_meta_import.php'; 
    ?>
	<title>Küche - Verwaltungssystem</title>
</head>
<body>
<?php 
include_once 'include/nav.php'; ?>
<div id="page">
	<div id="page_info">
		<h1 id="page_heading">Willkommen! - Küche</h1>
		<div id="page_description" style="width:50%">
			Sie befinden sich hier auf der Startseite des neuen Küchenverwaltungssystems (= WSDT). 
		</div>
	</div>
	<div id="page-content" style="width:50%;"><p></p>
	<p>WSDT ist eine datenbank-basierende Webapplikation, die viele Vorteile, im Gegensatz zu den Lösungen, die ich in Excel in den vergangenen Jahren implementiert habe, besitzt. </p><br />
	<p>So kann diese Webapplikation beliebig erweitert werden. Bereits vorhandene Daten (wie z.B. eingegebene Mitarbeiter, Bewertungen, etc.) können auch in anderen Teilen des Programms weitergenutzt werden. In Excel waren dafür gewisse "Work-Arounds" notwendig, die zum Beispiel das Öffnen von anderen Excel-Files im Hintergrund erforderte. </p><br />
	<p>Weitere Vorteile erstrecken sich bis hin zur besseren Portierbarkeit, Effizienz, Schnelligkeit, Sicherheit sowie ist diese Applikation auch weniger anfällig für ungewollte Beschädigungen durch seine Benutzer. </p><br />
	</div>
</div>

<!-- Sternliste für fleißige MA, auch druckbar mit Datum, Zusatzaufgaben etc.  -->
<?php include_once 'include/footer.php'; ?>
	
</body>
</html>
<?php exit(); ?>