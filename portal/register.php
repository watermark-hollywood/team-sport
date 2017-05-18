
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


    <title>Log in to your portal</title>

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
          <form name="user_register_form" id="user_register_form" role="form" action="../user/register.php" method="post">
            <div class="card text-center my-5">
              <div class="card-header">
                <h5 class="my-0">New User Registration</h5>
              </div>
              <div class="card-block">
                <div class="container-fluid">
                  <div class="row">
                    <div class='col-6'>
                        <div class="form-group">
                           <label for="register_first">First Name</label>
                            <input type="text" class="form-control" id="register_first" name="register_first" required="true" >
                        </div>
                    </div>
                    <div class='col-6'>
                        <div class="form-group">
                           <label for="register_last">Last Name</label>
                            <input type="text" class="form-control" id="register_last" name="register_last" required="true" >
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class='col-12'>
                        <div class="alert alert-danger" role="alert" id="exists-alert">
                          <strong>A conflict exists</strong><br/> Someone has registered this email address. <a href="login.php">Click here to log in</a>, or if you have forgotten your password, <a href="recover.php">click here to recover it</a>.
                        </div>
                        <div class="form-group">
                           <label for="register_email">Email address</label>
                            <input type="email" class="form-control" id="register_email" name="register_email" required="true" >
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class='col-12'>
                        <div class="alert alert-danger" role="alert" id="password-alert">
                          <strong>Password problem</strong><br/> Either your password was not 8 characters, or the fields did not match. Please fix them and try submitting again.
                        </div>
                        <div class="form-group">
                           <label for="register_password">Password</label>
                            <input type="password" class="form-control" id="register_password" name="register_password" required="true" >
                           <small class="form-text text-muted">Password must be at least 8 characters.</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class='col-12'>
                        <div class="form-group">
                           <label for="register_password_repeat">Repeat Password</label>
                            <input type="password" class="form-control" id="register_password_repeat" name="register_password_repeat" required="true">
                           <small class="form-text text-muted">Password fields must match.</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6" style="text-align: left;">
                        <a href="login.php">Return to Login</a>
                    </div>
                    <div class="col-6" style="text-align: right;">
                        <a href="recover.php">Forgot Password?</a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer text-muted">
                <div style="width:100%; text-align:center;">
                    <input type="hidden" id="register_type" name="register_type" value="8">
                    <button type="submit" class="btn btn-primary" style="cursor: pointer;">Sign Up</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Register Success Modal -->
    <div class="modal fade" id="success-modal"  data-backdrop="static" keyboard="false">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Thank you for registering with Project Oslo</h5>
            <button type="button" class="close" id="close-recover" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>An email is being sent to the address you provided with a verification link.</p>
            <p>Click this link to verify your email address and begin using Project Oslo.</p>
          </div>
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
    $('#exists-alert').hide();
    $('#password-alert').hide();

    $('#user_register_form').on('submit', function (e) {
      e.preventDefault();
      $('#exists-alert').hide();
      $('#password-alert').hide();
      
      $.ajax({
        type: 'post',
        url: 'user/register.php',
        data: $('#user_register_form').serialize(),
        success: function (data) {
          if(data == "exists"){
            $('#exists-alert').show();
          }
          if(data == "pass"){
            $('#password-alert').show();
          }
          if(data == "dberr"){
            $('#page-modal').modal('hide');
            $('#dberr-modal').modal('show');
          }
          if(data == "mailerr"){
            $('#page-modal').modal('hide');
            $('#mailerr-modal').modal('show');
          }

          if(data == "success"){
            $('#register-modal').modal('hide');
            $('#success-modal').modal('show');
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