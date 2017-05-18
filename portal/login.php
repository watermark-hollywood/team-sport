
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
    <link rel="stylesheet" href="domains/<?= ExtractSubdomains($_SERVER['HTTP_HOST']) ?>/css/portal.css" /> 

</head>

<body>

    <?php include('includes/topbar.php'); ?>
    <div class="container" id="body-container">
      <div class="row">
        <div class="col-xs-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3">
          <form name="login_form" id="login_form" role="form" action="user/login.php" method="post">
            <div class="card text-center my-5">
              <div class="card-header">
                <h5 class="my-0">Log in to Project Oslo</h5>
              </div>
              <div class="card-block">
                <div class="container-fluid">
                  <div class="row">
                    <div class='col-12'>
                        <div class="alert alert-danger" role="alert" id="login-alert">
                          <strong>There was a problem logging in</strong><br/> Either your password or email address was incorrect. <a href="register.php">Click here to register as a new user</a>, or if you think you have forgotten your password, <a href="recover.php"">click here to recover it</a>.
                        </div>
                        <div class="alert alert-danger" role="alert" id="unverified-alert">
                          <strong>There was a problem logging in</strong><br/> You must verify your email address before you can log in. We have just re-sent the email just in case you did not receive it before.
                        </div>
                        <div class="alert alert-danger" role="alert" id="unapproved-user-alert">
                          <strong>There was a problem logging in</strong><br/> The Portal Administrator for <?php echo($_SERVER['HTTP_HOST']) ?> has yet to approve your registration. You will receive an email as soon as it has been approved.
                        </div>
                        <div class="alert alert-danger" role="alert" id="unapproved-admin-alert">
                          <strong>There was a problem logging in</strong><br/>The Project Oslo Platform administrator has yet to approve your Portal Admin registration. You will receive an email as soon as it has been approved.
                        </div>
                        <div class="form-group">
                           <label for="login_email">Email address</label>
                            <input type="email" class="form-control" id="login_email" name="login_email" required="true" >
                        </div>
                    </div>
                  </div>
                  <div class="row">
                      <div class='col-12'>
                          <div class="form-group">
                             <label for="login_password">Password</label>
                              <input type="password" class="form-control" id="login_password" name="login_password" required="true" >
                             <small class="form-text text-muted">Password must be at least 8 characters.</small>
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
                    <input type="hidden" id="register_type" name="register_type" value="8">
                    <button type="submit" class="btn btn-primary" style="cursor: pointer;">Log in</button>
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
    $('#login-alert').hide();
    $('#unapproved-user-alert').hide();
    $('#unapproved-admin-alert').hide();
    $('#unverified-alert').hide();

    $('#login_form').on('submit', function (e) {
      e.preventDefault();
      $('#login-alert').hide();
      $('#unapproved-user-alert').hide();
      $('#unapproved-admin-alert').hide();
      $('#unverified-alert').hide();
      $.ajax({
        type: 'post',
        url: 'user/login.php',
        data: $('#login_form').serialize(),
        success: function (data) {
          if(data == "error"){
            $('#login-alert').show();
          }
          if(data == "unverified"){
            $('#unverified-alert').show();
          }
          if(data == "unapproved-user"){
            $('#unapproved-user-alert').show();
          }
          if(data == "unapproved-admin"){
            $('#unapproved-admin-alert').show();
          }
          if(data == "success"){
             var url = "index.php";
             $(location).attr('href',url);
          }
        },
        error: function (data) {
               var r = data.responseText;
               alert("Message: " + r.Message);
               alert("StackTrace: " + r.StackTrace);
               alert("ExceptionType: " + r.ExceptionType);
        }
      });

    });
   </script>
</body>
</html>