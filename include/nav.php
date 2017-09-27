<script type="text/javascript">

// OWN ACCOUNT --------------------------------------------------------------------------------------------------------
function show_changePassword_form() {
   var data = "<form class='well form-horizontal' action='../php/_emp.php' method='post' id='change_password'><fieldset style='width:800px;'><!-- Form Name --><legend>Change Credentials</legend><div class='form-group'><label class='col-md-4 control-label'>Old password *</label><div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-trash'></i></span><input  id='password_old' placeholder='Old password (required)' class='form-control'  type='password' required></div>  </div></div><div class='form-group'><label class='col-md-4 control-label'>New password *</label><div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-barcode'></i></span><input  id='password_new' placeholder='New password (required)' class='form-control'  type='password' required></div>  </div></div><div class='form-group'><label class='col-md-4 control-label'>Repeat new password *</label><div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-barcode'></i></span><input  id='password_new_repeat' placeholder='Repeat new password (required)' class='form-control'  type='password' required></div>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-warning' name='change_password' onclick='changePassword()'>Change Password <span class='glyphicon glyphicon-cog'></span></button></div></div></fieldset></form>";

   $.SimpleLightbox.open({
       content:data,
       elementClass: 'slbContentEl'
    });
}

function changePassword() {
   var old_password = document.getElementById('password_old').value;
   var new_password = document.getElementById('password_new').value;
   var new_password_repeat = document.getElementById('password_new_repeat').value;
   var username = <?php echo "'".$_SESSION['username']."'";?>;

   if (old_password==="" || new_password==="" || new_password_repeat==="") {
      $.notify("Bitte fülle alle Felder aus!","error");
   } else if (new_password !== new_password_repeat) {
      $.notify("Das neue Passwort stimmt mit der Kontrolleingabe nicht überein","error");
   } else {
            $.ajax({
            type: "POST",
            url: "http://"+<?php echo "\"".$_SERVER["SERVER_NAME"]."\"";?>+"/WSDT_Kueche/php/_auth.php",
            data: {
                examine_password : old_password,
                username : username
            },
            success: function(data){
                eval(data); //Führe JS aus PHP aus (Rückgabe)
                if(pwd_is_right==="wahr") {
                    //Change password

                $.ajax({
                    type: "POST",
                    url: "http://"+<?php echo "\"".$_SERVER["SERVER_NAME"]."\"";?>+"/WSDT_Kueche/php/_auth.php",
                    data: {
                        username : username,
                        set_password : new_password
                    },
                    success: function(data){
                        eval(data); //Führe JS aus PHP aus (Rückgabe)
                        $.notify("Passwort erfolgreich geändert!","success");
                        $('.slbCloseBtn').click();
                    },
                    error: function(data) {
                      $.notify("Passwort konnte nicht geändert werden!","error");
                    }
                });


                } else {
                    $.notify("Altes Passwort falsch!","error");
                }
            },
            error: function(data) {
              $.notify("Altes Passwort konnte nicht geprüft werden!","error");
            }
        });
   }
}
//OWN ACCOUNT END ----------------------------------------------------------------------------------------------
//IF ADMIN OTHER ACCOUNTS START --------------------------------------------------------------------------------
function resetEverything() {
   var data = "<form class='well form-horizontal'><fieldset style='width:800px;'><!-- Form Name --><legend>RESET WSDT</legend><div class='form-group'><div style='padding-left:18px;'><p>Sind Sie sich sicher, dass Sie WSDT zurücksetzen möchten? </p><p>Sie löschen damit alle Accounts und infolgedessen alle Daten aus der Datenbank! Sollten Sie diesen Schritt aufgrund diverser technischer Schwierigkeiten gehen wollen, dann sollte nach der automatischen Neukonfiguration alles wieder wie gehabt funktionieren. </p><p>Mein Rat daher: Setzen Sie WSDT nur zurück wenn Sie die Löschung sämtlicher Daten wünschen oder Sie keinen anderen Weg finden, unbekannte technische Fehlerursachen zu beheben. </p><p>Da die Datenbank gelöscht wird, werden Sie auch automatisch ausgeloggt. </p></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'><marquee><button type='button' class='btn btn-danger' name='delete_wsdt' onclick='resetEverything_now()'>RESET WSDT NOW <span class='glyphicon glyphicon-cog'></span></button></marquee></div></div></fieldset></form>";

   $.SimpleLightbox.open({
      content:data,
      elementClass: 'slbContentEl'
   });
}

function resetEverything_now() {
   var base_path = <?php echo "\"".$wsdt_path."\"";?>;
   $.ajax({
         type: "POST",
         //datatype: 'script',
         url: base_path+"php/_auth.php",
         data: {
            reset_everything : "isset"
         },
         success: function(data){
            try {
              eval(data);
              $.notify("WSDT wurde zurückgesetzt!","success");    
              setTimeout(function() {window.location.reload(true)},1800);
            } catch (err) {
              $.notify("Missing permissions! Delete 'kueche.db' manually.","error");
            }
            
            //console.log(data);
         },
         error: function(data) {
            $.notify("Daten nicht an den Server übertragen!","error");
            console.error("Daten konnten nicht zu _auth.php übertragen werden!");
            console.log(data);
         }
      });
}

function createAccount_form() {
   var data = "<form class='well form-horizontal'><fieldset style='width:800px;'><!-- Form Name --><legend>Create User</legend><div class='form-group'><label class='col-md-4 control-label'>Username *</label><div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-user'></i></span><input  id='username' placeholder='Username (required)' class='form-control'  type='text'></div>  </div></div><div class='form-group'><label class='col-md-4 control-label'>Password *</label><div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-barcode'></i></span><input  id='password' placeholder='Password (required)' class='form-control'  type='password'></div>  </div></div><div class='form-group'><label class='col-md-4 control-label'>Repeat password *</label><div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-barcode'></i></span><input  id='password_repeat' placeholder='Repeat password (required)' class='form-control'  type='password'></div></div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-primary' name='change_password' onclick='createAccount()'>Create User <span class='glyphicon glyphicon-cog'></span></button></div></div></fieldset></form>";

   $.SimpleLightbox.open({
      content:data,
      elementClass: 'slbContentEl'
   });
}

function createAccount() {
   var username = document.getElementById('username').value;
   var password = document.getElementById('password').value;
   var password_repeat = document.getElementById('password_repeat').value;
   var base_path = <?php echo "\"".$wsdt_path."\"";?>;

   if (username==="" || password_repeat==="" || password==="") {
        $.notify("Please fill all fields!","error");
   } else if (password!==password_repeat) {
         $.notify("Passwörter müssen übereinstimmen!","error");
   } else {
         $.ajax({
         type: "POST",
         //datatype: 'script',
         url: base_path+"php/_auth.php",
         data: {
            create_acc : "isset",
            username : username,
            password : password,
            password_repeat : password_repeat
         },
         success: function(data){
            eval(data);
            $.notify("User '"+username+"' wurde erstellt!","success");    
            setTimeout(function() {window.location.reload(true)},1800);
            //console.log(data);
         },
         error: function(data) {
            $.notify("Daten nicht an den Server übertragen!","error");
            console.error("Daten konnten nicht zu _auth.php übertragen werden!");
            console.log(data);
         }
      });
   }
}


function changeAccount(type) {
   //Show Form (same code just other lables)
   //declare vars
   var btn_label = "X",form_legend="X",onclick_function="X",btn_style="btn-danger";

   if (type==="reset_pwd") {
      btn_label="Reset password";
      form_legend="Reset someones password";
      onclick_function="reset_sm_pwd";
      btn_style="btn-warning";
   } else if (type==="delete_acc") {
      btn_label="Delete account";
      form_legend="Delete an account";
      onclick_function="delete_sm_acc";
   }

   var data = "<form class='well form-horizontal'><fieldset style='width:800px;'><!-- Form Name --><legend>"+form_legend+"</legend><!-- Text input--><div class='form-group'><label class='col-md-4 control-label'>Select account: *</label>      <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>        <span class='input-group-addon'><i class='glyphicon glyphicon-user'></i></span>"+<?php echo "\"";printComboBoxOfNestedArray(db_loadAllUsers(),'change_account');echo "\""; ?>+"</div>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn "+btn_style+"' name='btn_change_account' onclick='"+onclick_function+"();'>"+btn_label+" <span class='glyphicon glyphicon-send'></span></button>  </div></div><div style='width:100%'><div id='addit_data_adminsettings'></div></div></fieldset></form>";

   $.SimpleLightbox.open({
      content:data,
      elementClass: 'slbContentEl'
   });
}


function delete_sm_acc() {
   var username = document.getElementById('change_account').value;
   var base_path = <?php echo "\"".$wsdt_path."\"";?>;

   if (username==="" || username==="empty") {
        $.notify("No user selected.","error");
   } else {
         $.ajax({
         type: "POST",
         //datatype: 'script',
         url: base_path+"php/_auth.php",
         data: {
            delete_sm_acc : "isset",
            username : username
         },
         success: function(data){
            eval(data);
            $.notify("User '"+username+"' unwiderruflich gelöscht!","success");    
            setTimeout(function() {window.location.reload(true)},1800);
            //console.log(data);
         },
         error: function(data) {
            $.notify("Daten nicht an den Server übertragen!","error");
            console.error("Daten konnten nicht zu _auth.php übertragen werden!");
            console.log(data);
         }
      });
   }
}

function hide_addit_data_adminsettings() {document.getElementById('addit_data_adminsettings').style.display = 'none';}

function reset_sm_pwd() {
   var username = document.getElementById('change_account').value;
   var base_path = <?php echo "\"".$wsdt_path."\"";?>;

   if (username==="" || username==="empty") {
        $.notify("No user selected.","error");
   } else {
         $.ajax({
         type: "POST",
         //datatype: 'script',
         url: base_path+"php/_auth.php",
         data: {
            reset_sm_pwd : "isset",
            username : username
         },
         success: function(data){
            eval(data);
            $.notify("Passwort von '"+username+"' geändert!","success");    
            //console.log(data);
         },
         error: function(data) {
            $.notify("Daten nicht an den Server übertragen!","error");
            console.error("Daten konnten nicht zu _auth.php übertragen werden!");
            console.log(data);
         }
      });
   }
}

//IF ADMIN OTHER ACCOUNTS END ----------------------------------------------------------------------------------

</script>
<?php $wsdt_path = "http://".$_SERVER['HTTP_HOST']."/WSDT_Kueche/"; ?>

<!-- TODO: active class for open page -->
<div id='cssmenu'>
<ul>
   <li><a href='<?php echo $wsdt_path; ?>home.php'><span>Startseite</span></a></li>
   <li><a href='<?php echo $wsdt_path; ?>pages/sternliste.php' title="Waren Mitarbeiter überengagiert? Hier können Sie das vermerken!"><span>Sternliste</span></a></li>
   <li><a href='<?php echo $wsdt_path; ?>pages/notizen.php' title="Nie wieder Zettelchaos!"><span>Notizen</span></a></li>
   <li><a href='<?php echo $wsdt_path; ?>pages/meals.php' title="Alle Infos und Tools rund zu allen Speisen"><span>Küchenverwaltung</span></a></li>
   <li class='has-sub'><a href='#'><span>Settings</span></a>
      <ul>
         <li class='has-sub'><a href='#' onclick='show_changePassword_form()'><span>Passwort ändern</span></a></li>
         
         <?php if ($_SESSION['username']=="super_user") {
                  echo "<li class='has-sub'><a href='#'><span>Manage accounts <sub style='float:right;'>ADMIN</sub></span></a>
                           <ul>
                              <li><a href='#' onclick='createAccount_form()'><span>Create new account</span></a></li>
                              <li><a href='#' onclick='changeAccount(&apos;reset_pwd&apos;);'><span>Reset password of account</span></a></li>
                              <!--<li><a href='#'><span>Change privileges</span></a></li>-->
                              <li><a href='#' onclick='changeAccount(&apos;delete_acc&apos;);'><span>Delete account</span></a></li>
                              <li class='last'><a href='#' onclick='resetEverything();'><span>Reset WSDT</span></a></li>
                           </ul>
                        </li>";
               } ?>
      </ul>
   </li>
   <!--<li><a href='<?php echo $wsdt_path; ?>pages/impressum.php'><span>Impressum</span></a></li>-->
   <li class='last logout_nav'><a href='<?php echo $wsdt_path; ?>php/_auth.php?logout=true'><span>Log-Out</span></a></li>
</ul>
</div>
<!-- NAVIGATION END -->