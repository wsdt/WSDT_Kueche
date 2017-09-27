<?php require_once "../php/_note.php"; ?>

<script type="text/javascript">
//GLOABAL DECLARATION
var note_count=-1;
//Note JS Object
var Notes = [];
function Note(id,title,content,position,flags,categories,cre_id) {
	this.type="note";
	//this.bg_color="FFFACD"; //statisch derweil EHER Categories dafür nutzen!
	this.id=id;
	this.title=title;
	this.content=content; //by default empty
	this.position=position;
	this.flags = flags; //z.B. HIDDEN (so dass beim Reload immer noch versteckt)
	this.categories = categories; //z.B. Kategorien links/rechts unten als kleine farbige Kästchen anzeigen, die oben im Menü hinzufügbar und automatisch Legenden anzeigen
	this.created = new Date().getTime();
	this.cre_id = cre_id;
}


//Make all notes draggable
function makeNotesDraggable() {
	$( ".note" ).draggable({
			drag: function() {
				var noteid = $(this).attr("id");
				changeNote(Notes[noteid.substr(4)]); //nicht von hinten mit -1, da auch mehrstellige Nr. möglich
			},
			containment: "parent", stack:".note"}
		); //stack weglassen wenn schwarze Move Kreuze immer gesehen werden sollen (besser aber z-index von bild nach vorne)
}

//GLOBALS ##########################################################################
function page_load() {
	$.notify("Deine Notizen werden automatisch gespeichert.","info");
	note_count=db_printAllNotes();
}

function enlarge_page() {
	var height = $("#page_content").css("height");
	if (!height) {
		height = '1000px';
	} else {
		console.log(height);
		height = parseInt(height.substr(0,height.length-2))*1.25;
	} 
	console.log(height);
	$("#page_content").css("height",height);
}

//https://stackoverflow.com/questions/1144783/how-to-replace-all-occurrences-of-a-string-in-javascript
String.prototype.replaceAll = function(search, replacement) {
	var target = this;
	return target.split(search).join(replacement);
}

//Setze note_count auf aktuellen Wert aus DB

//BASIC NOTE OPERATIONS --------------------------------------------------------------------------------
function db_printAllNotes() { //prints all saved notes when they are NOT added already
	<?php 
		$note_count = _note::loadHighestIdFromDb();
		$tmp_note = _note::__construct_empty();
		$all_notes = $tmp_note->loadAllNotesFromDb();
		if ($all_notes !== false) {
			foreach($all_notes as $note) {
				//$note_count = ($note->getId() > $note_count) ? $note->getId() : $note_count; //weise nur die nächstgrößere ID immer zu (DEPRECATED, da auch andere Accounts IDs anlegen)
				echo "if (!document.getElementById('note".$note->getId()."')) {";
				echo "Notes[".$note->getId()."] = '".json_encode($note->convertPHPObject2JSObject($note))."';";
				//echo "console.log('".var_dump($note->convertPHPObject2JSObject($note))."');";break;
				echo "$(\"#page_content\").prepend(
					$('<div class=\"note ui-draggable ui-draggable-handle\" style=\"".$note->getPosition()."\" id=\"note".$note->getId()."\"><input type=\"text\" placeholder=\"note title\" value=\"".htmlspecialchars($note->getTitle(), ENT_QUOTES, 'UTF-8')."\" class=\"note-title\" onkeyup=\"changeNote(Notes[".$note->getId()."]);\"/><textarea class=\"note-content\" onkeyup=\"changeNote(Notes[".$note->getId()."]);\">'+\"".htmlspecialchars($note->getContent(),ENT_QUOTES,'UTF-8')."\".replaceAll(\"{ENTER}\",\"\\n\")+'</textarea><div class=\"dragme\"></div></div>').hide().fadeIn('slow'));";
				echo "}";
			}
		}
		echo "makeNotesDraggable();";
		echo "return ".($note_count).";"; 
	?>
}


function createNewNote() {
	note_count++;
	//Instantiate Note Object
	Notes[note_count] = new Note(note_count,"","","","",""); //ID 'noteX' --> X = Array Index

	$("#page_content").prepend($('<div class="note ui-draggable ui-draggable-handle" id="note'+(note_count)+'"><input type="text" placeholder="note title" class="note-title" onkeyup="changeNote(Notes['+note_count+']);"/><textarea class="note-content" onkeyup="changeNote(Notes['+note_count+']);"></textarea><div class="dragme"></div></div>').hide().fadeIn('slow'));
	makeNotesDraggable(); //also new notes should be draggable

	
	//Save Note into db
	$.ajax({
    	type: "POST",
    	url: "../php/_note.php",
    	data: {
    		saveNote : "isset",
    		note : Notes[note_count] //Increment note_count for next use
    	},
    	success: function(data) {
    		//console.log(data);
    	},
    	error: function(data) {
    		$.notify("Notiz konnte nicht gespeichert werden!","error");
    	}
	});
}

function deleteAllNotes() {	
	$.ajax({
    	type: "POST",
    	url: "../php/_note.php",
    	data: {deleteAllNotes : "isset"},
    	success: function(data){
      		document.getElementById('page_content').innerHTML = ""; //Lösche Notizen auf Clientseite erst wenn aus DB gelöscht. 
      		eval(data);
      		Notes = []; //empty note object
    	},
    	error: function(data) {
    		$.notify("Notizen konnten nicht dauerhaft gelöscht werden!","error");
    	}
	});
}

function deleteNote(id) {
	//Lösche Notiz am Bildschirm sofort, aber mache Datenbankoperation erst danach (für User scheint es schneller zu sein)
	$("#"+id).draggable("destroy");
	$("#"+id).remove(); //Lösche Notiz auf Clientseite erst wenn aus DB gelöscht. 
	Notes[id.substring(4)] = ""; //delete note obj

	$.ajax({
    	type: "POST",
    	url: "../php/_note.php",
    	data: {deleteNote : id},
    	success: function(data){
      		eval(data);
    	},
    	error: function(data) {
    		$.notify("Notiz konnte nicht dauerhaft gelöscht werden!","error");
    	}
	});
}

function changeNote(note) { //note is an object!
	//Sobald Note verändert wird diese Funktion aufrufen. 
	//Hier neue Position speichern, neuen Status evtl, neue Kategorien, etc.
	if (typeof(note)==='object') {
		note = JSON.stringify(note);	
	} 
	note = refreshNote_from_HTML(JSON.parse(note));

	$.ajax({
    	type: "POST",
    	url: "../php/_note.php",
    	data: {
    		saveNote : "isset",
    		note : note
    	},
    	success: function(data){
    		//console.log(data);
    	},
    	error: function(data) {
    		$.notify("Notiz konnte nicht gespeichert werden!","error");
    	}
	});
}

function refreshNote_from_HTML(note) { //TAKE HERE ALL ELEMENTS FROM DOM FOR DB
	var elem = document.getElementById('note'+note.id);
	//Speichere HTML Daten in JS Objekt
	note.title = elem.getElementsByClassName('note-title')[0].value;
	note.content = elem.getElementsByClassName('note-content')[0].value;
	note.position = "top:"+elem.style.top+";left:"+elem.style.left+";z-index:"+getZIndex(elem)+";";
	return note;
}

//ADDITIONAL NOTE OPERATIONS --------------------------------------------------------

//NOTIFY HANDLER START --------------------------------------------------------------
/*Handler are helping to prevent showing multiple notifications when double clicking on 
a menu button. */
var hide_show = "hide"; //what to do
var hide_show_notif = "true";
function notify_handler_show_hide() { 
	if (hide_show === "hide" && hide_show_notif === "true") {
		$.notify('Doppelklick zum Verstecken aller Notizen. \nEinzelne Notizen über Notizmenü steuern.','info');
		hide_show_notif = "false";
	} else if (hide_show === "show" && hide_show_notif === "true") {
		$.notify('Doppelklick zum Anzeigen aller Notizen. \nEinzelne Notizen über Notizmenü steuern.','info');
		hide_show_notif = "false";
	} 
}

var deleteAll_notif = "true";
function notify_handler_deleteAll() {
	if (deleteAll_notif === "true") {
		$.notify('Doppelklick zum Löschen aller Notizen. \nEinzelne Notizen über Drag u. Drop auf roten Mülleimer löschen.','info');
		deleteAll_notif = "false";
	}
}

//NOTIFY HANDLER END ----------------------------------------------------------------

function hideAllNotes() {
	$(".note").fadeOut("slow");
	hide_show = "show";
	$.notify("Notizen versteckt. \nKeine Sorge sie sind noch da.","info");
	//Status nicht speichern
}

function hideNote(id) {
	$("#"+id).fadeOut("slow");
	$.notify("Notiz versteckt. \nKeine Sorge sie ist noch da.","info");
	//Status nicht speichern
}

function showAllNotes() {
	$(".note").fadeIn("slow");
	hide_show = "hide";
	$.notify("Das sind alle deine Notizen.","info");
	//Status nicht speichern
}

function showNote(id) {
	$("#"+id).fadeIn("slow");
	$.notify("Notiz '"+id+"' eingeblendet.","info");
	//Status nicht speichern
}

//DEPRECATED #############################################################
function arrangeNotes() {
	//alle in einer Reihe anzeigen. 
	$(".note").css('cssText','display:inline-block;');
	
	//Neue Positionen in DB eintragen
}

function arrangeNote(id) {
	$("#"+id).css('cssText','');

	//Neue Position in DB eintragen
}
//DEPRECATED END #########################################################

//SEARCH FUNCTIONS ----------------------------------------------------------------
var found_nothing="true";
function searchMenu() { //Managing function for search menu
	found_nothing = "true";
	//what to do
	var search_all = document.getElementById('search_all').checked;
	var search_title = document.getElementById('search_title').checked;
	var search_content = document.getElementById('search_content').checked;
	var search_keywords = document.getElementById('search_notes').value;

	if (search_keywords === "") {
		$.notify('Bitte tippe vorher etwas ins Suchfeld.','info');
	} else if (!(search_all || search_title || search_content)){
		search_all = true;
	}

	//Kein else if da sonst wenn nichts ausgewählt nicht trotzdem nach allem gesucht wird. 
	if (search_all) {
		searchNotes(search_keywords);
	} else if (search_title) {
		searchNotesByTitle(search_keywords);
	} else if (search_content) {
		searchNotesByContent(search_keywords);
	} else {
		$.notify('Suchoption nicht gefunden!','error');
	}
}


function searchNotes(keywords) { //more keywords possible when separating by , or |
	keywords = keywords.replace(",","|");
	$(".note").each(function(i) {
		if($(this).find(".note-title").val().match( new RegExp(keywords) ) ||
		$(this).find(".note-content").val().match( new RegExp(keywords) )) { //Returns position or -1 if not found
			//arrangeNote($(this).attr("id")); //place found note at the beginning
			foundEffekt($(this));
			//if notes found just emphasize them
		} 
	});
	if (found_nothing==="true") {
		$.notify("Leider Nichts gefunden!","warn");
	}
}

function searchNotesByTitle(title) {
	title = title.replace(",","|");
	$(".note").each(function(i) {
		if($(this).find(".note-title").val().match( new RegExp(title) )) { //Returns position or -1 if not found
			//arrangeNote($(this).attr("id")); //place found note at the beginning
			foundEffekt($(this));
			//if notes found just emphasize them
		} 
	});
	if (found_nothing==="true") {
		$.notify("Leider Nichts gefunden!","warn");
	}
}

function searchNotesByContent(content) {
	content = content.replace(",","|"); //Da in Regex umgewandelt wird.
	$(".note").each(function(i) {
		if($(this).find(".note-content").val().match( new RegExp(content) )) { //Returns position or -1 if not found
			//arrangeNote($(this).attr("id")); //place found note at the beginning
			foundEffekt($(this));
			//if notes found just emphasize them
		} 
	});
	if (found_nothing==="true") {
		$.notify("Leider Nichts gefunden!","warn");
	}
}

function foundEffekt(elem) {
	found_nothing = "false";
	elem.effect("shake",{times:6,distance:5},500);
	elem.effect("highlight",{},2000);
}


// TRASH BIN - MAKE DROPPABLE ################################################
$(function() { //execute on load
	$("#note_trash").droppable({
		tolerance: 'touch',
		accept: '.note', //Only notes should be droppable


		over: function(event, ui) { //ähnlich hover aber auch für drag 
			$('.trash > .lid').addClass('trash_hover');
			$(ui.draggable).css('opacity','0.35');			
		},
		out: function(event, ui) { //wenn nicht mehr hover
			$('.trash > .lid').removeClass('trash_hover');
			ui.draggable.css('opacity','1');
		},
		drop: function(event, ui) { //wenn fallen gelassen
			deleteNote($(ui.draggable).attr('id'));
		}
	});

	//-------------------------------------------------
	//Double Click delete all notes
	$("#delete_all_notes").dblclick(function() {
		deleteAllNotes();
		deleteAll_notif = "true";
	});

	//-------------------------------------------------
	//Double Click show/hide all notes
	$("#show_hide_note").dblclick(function() {
		if (hide_show === "show") {
			showAllNotes();
		} else if (hide_show === "hide") {
			hideAllNotes();
		} else {
			$.notify('Command not available. Restored it. ','warn');
			hide_show = "show";
		}
		hide_show_notif = "true";
	});
});

function drag_on_me() {
	$.notify("Drop some notes on me, I'm hungry.","info");
}

//Additional functions -----------------------------
window.getZIndex = function (e) {
	try {
		var z = document.defaultView.getComputedStyle(e).getPropertyValue('z-index');
		if (isNaN(z)) return getZIndex(e.parentNode);
		else return z;
	} catch (err) {
		return 1;
	}
};

</script>