
<?php
    require_once ('includes/managesessions.php'); 
    require_once ('includes/swdb_connect.php'); 
    require_once ('includes/utilityfunctions.php'); 

    ValidateDomain($_SERVER['HTTP_HOST']);
?>

<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en"> <!--<![endif]-->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">


    <title>Complete registration for Project Oslo</title>

    <!-- domo arigato mr roboto.. load this font -->
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500' rel='stylesheet' type='text/css'>

    <!-- BEGIN Load Styles for Plugins -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css" >


    <!-- page styles -->
    <link rel="stylesheet" href="assets/css/home.css" />
    <?php if(isset($_SESSION['domain'])) { ?>
        <link rel="stylesheet" href="domains/<?=$_SESSION['domain']?>/css/portal.css" /> 
    <?php } else if($login_domain){ ?>
        <link rel="stylesheet" href="domains/<?= $login_domain ?>/css/portal.css" /> 
    <?php } ?>

</head>

<body>

    <?php include('includes/topbar.php'); ?>
    <div class="container" id="body-container">
      <div class="row">
        <div class="col-xs-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3">
          <form name="user_accept_form" id="user_accept_form" role="form" action="../user/accept-invite.php" method="post">
            <div class="card text-center my-5">
              <div class="card-header">
                <h5 class="my-0">Completing registration</h5>
              </div>
              <div class="card-block">
                <div class="container-fluid">
                  <div class="row">
                    <div class='col-6'>
                        <div class="form-group">
                           <label for="invite_first">First Name</label>
                            <input type="text" class="form-control" id="invite_first" name="invite_first" required="true" >
                        </div>
                    </div>
                    <div class='col-6'>
                        <div class="form-group">
                           <label for="invite_last">Last Name</label>
                            <input type="text" class="form-control" id="invite_last" name="invite_last" required="true" >
                        </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class='col-12'>
                        <div class="alert alert-danger" role="alert" id="password-alert">
                          <strong>Password problem</strong><br/> Either your password was not 8 characters, or the fields did not match. Please fix them and try submitting again.
                        </div>
                        <div class="form-group">
                           <label for="invite_password">Password</label>
                            <input type="password" class="form-control" id="invite_password" name="invite_password" required="true" >
                           <small class="form-text text-muted">Password must be at least 8 characters.</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class='col-12'>
                        <div class="form-group">
                           <label for="invite_password_repeat">Repeat Password</label>
                            <input type="password" class="form-control" id="invite_password_repeat" name="invite_password_repeat" required="true">
                           <small class="form-text text-muted">Password fields must match.</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6" style="text-align: left;">
                        <a href="register.php">Register New User</a>
                    </div>
                    <div class="col-6" style="text-align: right;">
                        <a href="recover.php">Forgot Password?</a>
                    </div>
                </div>
              </div>
            </div>
            <div class="card-footer text-muted">
              <div style="width:100%; text-align:center;">
                  <input type="hidden" id="invite_guid" name="invite_guid" value="<?= $_GET['guid'] ?>">
                  <button type="submit" class="btn btn-primary" style="cursor: pointer;">Complete Registration and Login</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
    
    <?php include('includes/footer.php'); ?>
    <!-- common functions -->
    <script src="../assets/js/common.js"></script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
   
   <script> 
    $('#password-alert').hide();

    $('#user_accept_form').on('submit', function (e) {
      e.preventDefault();
      $('#password-alert').hide();
      
      $.ajax({
        type: 'post',
        url: 'user/accept-invite.php',
        data: $('#user_accept_form').serialize(),
        success: function (data) {
          if(data == "pass"){
            $('#password-alert').show();
          }

          if(data == "error"){
            console.log("error completing registration.");
          }

          if(data == "success"){
            location.replace("/");
          }

        },
        error: function (data) {
               var r = jQuery.parseJSON(data.responseText);
               alert("Message: " + r.Message);
               alert("StackTrace: " + r.StackTrace);
               alert("ExceptionType: " + r.ExceptionType);
        }
      });
    });
   </script>
</body>
</html>