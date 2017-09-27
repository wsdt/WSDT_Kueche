  $(document).ready(function(){
        if (document.body.contains(document.getElementById('Password_Repeat'))) { //Darauf bezogen wenn gar kein Passwort gesetzt
            $('.log-btn').click(function(){
            var new_password = document.getElementById('Password').value;
            var new_password_repeat = document.getElementById('Password_Repeat').value;
            var username = document.getElementById('UserName').value;
            if(new_password==="" || new_password_repeat==="" || username==="") {
                $.notify("Felder müssen gefüllt sein!","error");
            } else if (new_password !== new_password_repeat) {
                $.notify("Passwörter stimmen nicht überein!","error");
            } else {
              set_new_password(username,new_password);
               $.notify("Passwort wurde gesetzt.","success");
            }
          });
        } else {
            $('.log-btn').click(function(){
                isPasswordRight(document.getElementById('UserName').value,document.getElementById('Password').value); 
            });
            $('.form-control').keypress(function(){
                $('.log-status').removeClass('wrong-entry');
                $('#register_fake_link').removeClass('hide');
            });
    }
  });


  function fake_register() {
      $.notify("Only super_user can create new accounts!\nFile an application..","info");
      download_non_processable_file('./docs/Antrag_NewAccount.docx');
  }

  function download_non_processable_file(url) {
      document.getElementById('download_document').src = url;
  }


  function isPasswordRight(username,password) {
        $.ajax({
        type: "POST",
        url: "php/_auth.php",
        data: {
            username : username,
            examine_password : password
        },
        success: function(data){
            eval(data); //Führe JS aus PHP aus (Rückgabe)
            if(pwd_is_right==="wahr") {
                console.log("Passwort korrekt!");
                $('.log-status').removeClass('wrong-entry');
                window.location = "./home.php";
                return true; //redundant
            } else {
                $.notify("Passwort falsch!","error");
                $('.log-status').addClass('wrong-entry');
                $('#register_fake_link').addClass('hide');
                $('.alert').fadeIn(500);
                setTimeout( "$('.alert').fadeOut(1500);",3000 );
                setTimeout( "$('#register_fake_link').removeClass('hide');",4500 );
                return false;
            }
        },
        error: function(data) {
          $.notify("Passwort konnte nicht geprüft werden!","error");
        }
    });
  }



  function lost_password() {
      $.notify("This will erase all saved data of your account!","warn");

      var data = "<form class='well form-horizontal lightbox-content' action='home.php' method='post' id='password_reset'><fieldset ><!-- Form Name --><legend><h1>Reset password</h1></legend><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label'>Username *</label>    <div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-wrench'></i></span>  <input  id='username' placeholder='Your username (required)' class='form-control'  type='text' required>    </div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label'>New password *</label>    <div class='col-md-4 inputGroupContainer'>  <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-wrench'></i></span>  <input  id='new_password' placeholder='New password (required)' class='form-control'  type='password' required>    </div>  </div></div><!-- Text input--><div class='form-group'>  <label class='col-md-4 control-label' >Repeat password *</label>     <div class='col-md-4 inputGroupContainer'>    <div class='input-group'>  <span class='input-group-addon'><i class='glyphicon glyphicon-wrench'></i></span>  <input id='new_password_repeat' placeholder='Repeat new password (required)' class='form-control'  type='password' required id='emp_add_lastname'>    </div>  </div></div><!-- Button --><div class='form-group'>  <label class='col-md-4 control-label'></label>  <div class='col-md-4'>    <button type='button' class='btn btn-danger' name='password_reset' onclick='reset_wsdt()'>Reset WSDT <span class='glyphicon glyphicon-send'></span></button>  </div></div></fieldset></form>";

      $.SimpleLightbox.open({
          content:data,
          elementClass: 'slbContentEl'
      });
  }


  function reset_wsdt() {
      var username = document.getElementById('username').value;
      var new_password = document.getElementById('new_password').value;
      var new_password_repeat = document.getElementById('new_password_repeat').value;
      if(new_password==="" || new_password_repeat==="" || username==="") {
          $.notify("Felder müssen gefüllt sein!","error");
      } else if (new_password !== new_password_repeat) {
          $.notify("Passwörter stimmen nicht überein!","error");
      } else {
          set_new_password(username,new_password); //pwds sind gleich

          //TODO: DELETE data of tables
          $.ajax({
              type: "POST",
              url: "php/_auth.php",
              data: {
                  reset_wsdt : new_password,
                  username : username
              },
              success: function(data){
                  console.log("WSDT erfolgreich zurückgesetzt!");
                  eval(data);
                  //console.log(data);
                  //setTimeout(function() {window.location.reload(true)},2000);
              },
              error: function(data) {
                $.notify("WSDT konnte nicht zurückgesetzt werden!","error");
                console.error("WSDT konnte nicht zurückgesetzt werden!");
              }
          });
      }
  }


  function set_new_password(username,password) {
          //AJAX: DELETE ALL Data of TABLES EXCEPT USER TABLE and change password
          //set_password übertragen per isset oder mit value von passwort (besser) für _auth.php
          $.ajax({
              type: "POST",
              url: "php/_auth.php",
              data: {
                  set_password : password,
                  username : username
              },
              success: function(data){
                  console.log("Passwort erfolgreich geändert!");
                  //console.log(data);
                  setTimeout(function() {window.location.reload(true)},2000);
              },
              error: function(data) {
                $.notify("Passwort nicht aktualisiert!","error");
                console.error("Passwort konnte nicht aktualisiert werden!");
              }
          });
  }


  //Fancy background if fullscreen
$(document).ready(function() {
  var movementStrength = 25;
  var height = movementStrength / $(window).height();
  var width = movementStrength / $(window).width();
  $("body").mousemove(function(e){
            var pageX = e.pageX - ($(window).width() / 2);
            var pageY = e.pageY - ($(window).height() / 2);
            var newvalueX = width * pageX * -1 - 25;
            var newvalueY = height * pageY * -1 - 50;
            $('body').css("background-position", newvalueX+"px     "+newvalueY+"px");
  });
});