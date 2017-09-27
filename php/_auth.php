<?php
if (!isset($_SESSION)) {session_start();}

//if(!isset($_SESSION)) {sessionStart('_auth');}

require_once "password.php";
require_once "db_connection.php";
require_once "functions.php";




//LOGOUT ROUTINE
if (!empty($_GET)){
	if (isset($_GET['logout'])) {
		if ($_GET['logout']==="true") {
			session_logout('loggedout');
		} 
	}
}


//PASSWORD RESET bzw. SET HANDLING
if (!empty($_POST)) {
	if(isset($_POST['set_password'])) {
		db_setPassword($_POST['username'],$_POST['set_password']);
	}
	if(isset($_POST['examine_password'])) {
		if (db_authenticate($_POST['username'],$_POST['examine_password'])) {
			$pwd_right = 'wahr'; //do not place booleans here, they will not be transferred correctly to js

			$_SESSION['password']=db_getPasswordHash($_POST['username']);
			$_SESSION['username']=$_POST['username'];

		} else {
			$pwd_right = 'falsch';
		}
		echo 'var pwd_is_right = "'.$pwd_right.'";';
	}
	if(isset($_POST['reset_wsdt'])) {
		db_resetWSDT($_POST['username'],$_POST['reset_wsdt']);
	}
	if(isset($_POST['reset_sm_pwd'])) {
		db_resetPwdOfUser($_POST['username']);
	}
	if(isset($_POST['delete_sm_acc'])) {
		db_deleteUser($_POST['username']);
	}
	if(isset($_POST['create_acc'])) {
		db_createUser($_POST['username'],$_POST['password'],$_POST['password_repeat']);
	}
	if(isset($_POST['reset_everything'])) {
		db_resetEverything();
	}
}

// START of Admin account management routines ----------------------------------------------------------------
function db_resetEverything() {
	if ($_SESSION['username']=="super_user") {
		$db_name = "kueche.db";
		$db_verz = $_SERVER["DOCUMENT_ROOT"]."\WSDT_Kueche\php\db_sqlite\\";
		$datenbank = $db_verz.$db_name;
		if(file_exists($datenbank)) {
			chmod($db_verz,0777);
			chmod($datenbank,0777);
			unlink($datenbank);
			echo "$.notify('All data has been erased.','warning')";
		} else {
			echo "$.notify('Could not reset. Found no database!','error');";
		}
	}
}

function db_createUser($username,$password,$password_repeat) {
	if ($_SESSION['username']=="super_user") {
		if(empty($username) || empty($password) || empty($password_repeat) || $password!=$password_repeat) {
			echo "$.notify('Ungültige Userdaten übermittelt!','error');";
		} else {
			execDBStatement("INSERT INTO Credentials (id,password) VALUES ('".escapeString($username)."','".encryptPassword(escapeString($password))."');");
		}
	}
}

function db_deleteUser($username) {
	if ($_SESSION['username']=="super_user") {
		$username = escapeString($username);
		if($username=="super_user") {
			echo "$.notify('Der Super-User darf nicht gelöscht werden!','error');";
			throw new Exception("Super-User not eraseable!",123);
		} else {
			execDBStatement("DELETE FROM Note WHERE cre_id='".$username."';");
			execDBStatement("DELETE FROM Assessment WHERE cre_id='".$username."';");
			execDBStatement("DELETE FROM Employee WHERE cre_id='".$username."';");
			execDBStatement("DELETE FROM Credentials WHERE id='".$username."';");
		}
	}
}

function db_resetPwdOfUser($username) {
	if ($_SESSION['username']=="super_user") {
		$username = escapeString($username);
		if($username=="super_user") {
			echo "$.notify('Passwort von Super-User in Einstellungen zu ändern!','error');";
			throw new Exception("Super-User must change his pwd in settings!",123);
		} else {
			$new_pwd = escapeString(random_str(8)); //zufälliges Pwd mit 8 Stellen
			execDBStatement("UPDATE Credentials SET password='".encryptPassword($new_pwd)."' WHERE id='".$username."'");
			echo "document.getElementById('addit_data_adminsettings').style.display = 'block';document.getElementById('addit_data_adminsettings').innerHTML = '<span id=message>Password of ,<strong>".$username."</strong>, changed to ,<span class=mark_me>".$new_pwd."</span>, <sub onclick=hide_addit_data_adminsettings()>Hide</sub></span>'";
		}
	}
}

function random_str($length) { //From: https://stackoverflow.com/questions/6101956/generating-a-random-password-in-php
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < $length; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}


// END of Admin account management routines ----------------------------------------------------------------
//RESET WSDT
function db_resetWSDT($username,$curr_passw) { //IMPORTANT: Daten sollten nur über folgende Prozedur löschbar sein!
	//Password should be true, because it was set a few ms ago. But let's see if it worked
	if(db_authenticate($username,$curr_passw)) {
		execDBStatement("DELETE FROM Assessment WHERE cre_id='".escapeString($username)."';");
		execDBStatement("DELETE FROM Employee WHERE cre_id='".escapeString($username)."';");
		execDBStatement("DELETE FROM Note WHERE cre_id='".escapeString($username)."';");

		echo "$.notify('Deine Daten wurden gelöscht und Passwort resettet.','success');";
	} else {
		//error while setting resetted passw., so do not delete data
		echo "$.notify('User ,".$username.", existiert nicht!','error');";
	}
}

function session_automatic_login() {
	if(isset($_SESSION['password']) && isset($_SESSION['username'])){
		if($_SESSION['password']==db_getPasswordHash($_SESSION['username'])) {
			header("Location: http://".$_SERVER["SERVER_NAME"]."/WSDT_Kueche/home.php");
		} //no else because nothing should happen when u have to login
	}
}

function session_automatic_logout() {
	if(isset($_SESSION['last_refresh']) && (time() - $_SESSION['last_refresh'] > 1800)) //30minutes
	{
		session_logout("timeout");
	} 
}

function session_authenticate() {
	session_automatic_logout(); //logout after 30 min no refresh
	$_SESSION['last_refresh']=time();

	if(!isset($_SESSION['username'])) {
		$_SESSION['username'] = "";
	}

	if(!isset($_SESSION['password'])) {
		$_SESSION['password'] = "";
	}
	//Leerer String ergibt true bei empty
	//if (!password_verify($_SESSION['password'],db_getPasswordHash($_SESSION['username']))) { 
	if (!($_SESSION['password'] == db_getPasswordHash($_SESSION['username'])) || empty($_SESSION['username']) || empty($_SESSION['password'])) { //da pwd in session nicht mehr klartext
	//mustlogin zum Sagen, dass Zugriff für Seite benötigt wird.
		session_logout('mustlogin'); //falls falsche Session aktiv
	} //if true do nothing, because session pwd has already correct pwd
}

function session_logout($type) {
	if(isset($_SESSION)) {
		session_unset(); //unset session for run-time
		session_destroy(); //delete session from storage, session start muss vorher aktiv sein (also bei authenticate müsste reichen)
	}
	header("Location: http://".$_SERVER['HTTP_HOST']."/WSDT_Kueche/index.php?".$type."=true");exit(); //da authenticate auf jeder Seite sollte das ausreichen
}


//$super_user = "tk_zk_wsdt2017";
function db_authenticate($username,$password) {
	//execute before opening any files except login form (if failed redirect to login page)
	$passwordhash = db_hasWSDTaPwd($username);
	if($passwordhash!==false) {
		return password_verify($password,$passwordhash); //hier schon mit pwd verify zu prüfen
	} else {
		echo "$.notify('You (".$username.") have no password or user does not exist!','warn');";
		return false;
	}

}


function db_getPasswordHash($username) {
	return db_hasWSDTaPwd($username);
}

function db_setPassword($username,$password) {
	//$hasPwd = db_hasWSDTaPwd($username);

	$encr_pwd = encryptPassword(escapeString($password));
	/*if($hasPwd === false) {
		//DO INSERT
		execDBStatement("INSERT INTO Credentials (password) VALUES ('".$encr_pwd."');");
	} else {*/
		//DO UPDATE
		execDBStatement("UPDATE Credentials SET password='".$encr_pwd."' WHERE id='".escapeString($username)."';"); 
	//}
	if (isset($_SESSION['username'])) { //setze neues session pwd nur, wenn schon eingeloggt
		if ($_SESSION['username'] == $username) {
			$_SESSION['password']=$encr_pwd; //Setze aktuelle Session, um gleich eingeloggt zu sein. 
		}
	}
}

function db_hasWSDTaPwd($username) {
	$con = new db_connection();
	$sqlite_obj = $con->query("SELECT * FROM Credentials WHERE id='".escapeString($username)."';"); //there is just one row
	$row = $sqlite_obj->fetchArray(SQLITE3_ASSOC); //it will just save the first value (because where for primary key)
	$con->close();

	if (empty($row['password'])) {
		return false;
	} else {
		return $row['password'];
	}
}

function db_loadAllUsers() {
	//Loads user data without passwords
		$con = new db_connection();
		$a = 0;
		$sqlite_obj = $con->query("SELECT id FROM Credentials WHERE id NOT IN ('super_user');");

		while ($row = $sqlite_obj->fetchArray(SQLITE3_ASSOC)) {
			$tmp_result[$a++] = $row; //Tmp_result is a nested array
		}
		$con->close();
		return empty($tmp_result) ? $tmp_result = false : $tmp_result; //Wenn Tmp_result = null, dann mach Boolean draus
}

function encryptPassword($password) {
	return password_hash($password,PASSWORD_BCRYPT);
}


//#########################################################################################################################
/*SESSION HACK PROOF
//Knowledge from: 
//http://blog.teamtreehouse.com/how-to-create-bulletproof-sessions
function sessionStart($name, $limit=0,$path = '/', $domain=null,$secure=null) {
	//Cookie Name Set
	session_name($name . '_Session');

	//Set Domain to default to current domain
	$domain = isset($domain) ? $domain : isset($_SERVER['SERVER_NAME']);

	//Set default secure value to whether site is being accessed with ssl
	$https = isset($secure) ? $secure : isset($_SERVER['HTTPS']);

	//Set Cookie settings and start session
	session_set_cookie_params($limit,$path,$domain,$secure,true);
	
	session_start();

	//Problem immer neue Sessions, so untere Session vars ständig neu gesetzt und username/pwd gehen verloren. 


	
	if(validateSession()) {
		if(!preventHijacking()) { //bei session start zb hier rein aber auch sonst
			$_SESSION = array();
			$_SESSION['IPaddress'] = $_SERVER['REMOTE_ADDR'];
			$_SESSION['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
			regenerateSession();
		} else if (rand(1,100) <= 5) {
			//Give 5% chance of the session id changing on any request
			regenerateSession();
		}
	} else {
		$_SESSION = array();
		session_destroy();
		session_start();
	}
}

 function preventHijacking() {
	if(!isset($_SESSION['IPaddress']) || !isset($_SESSION['userAgent'])) {
		return false; //new sessions
	}
	if ($_SESSION['IPaddress'] != $_SERVER['REMOTE_ADDR']) {
		return false; //Externe IP nicht gleich
	}
	if ($_SESSION['userAgent'] != $_SERVER['HTTP_USER_AGENT']) {
		return false; //User Agent nicht gleich
	}

	return true; //sonst true
}

function regenerateSession() {
	//If Session is obsolete it means there already is a new id
	if (isset($_SESSION['OBSOLETE']) /*|| $_SESSION['OBSOLETE'] === true) {
		return; //break, 
	}

	//Set current session to expire in 10 seconds (ajax has time to do things)
	$_SESSION['OBSOLETE'] = true;
	$_SESSION['EXPIRES'] = time() + 10;


	//Create new session without destroying old one
	session_regenerate_id(false);

	//Grab current session ID and close both sessions to allow other scripts to use them
	$newSession = session_id();
	session_write_close();

	//Set session ID to the new one, and start it back up again
	session_id($newSession);
	session_start();


	//Now we unset the obsolete and expiration values for the session we want to keep
	unset($_SESSION['OBSOLETE']);
	unset($_SESSION['EXPIRES']);
}


 function validateSession() {
	if(isset($_SESSION['OBSOLETE']) && !isset($_SESSION['EXPIRES'])) {
		return false;
	}
	if (isset($_SESSION['EXPIRES']) && $_SESSION['EXPIRES'] < time()) {
		return false;
	}
	return true;
}*/







?>