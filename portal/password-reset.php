
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


    <title>Reset your password</title>

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
          <form name="reset_form" id="reset_form" role="form" action="../user/password-reset.php" method="post">
            <div class="card text-center my-5">
              <div class="card-header">
                <h5 class="my-0">Reset your password</h5>
              </div>
              <div class="card-block">
                <div class="container-fluid">
                  <div class="row">
                    <div class='col-12'>
                        <div class="alert alert-danger" role="alert" id="bad-link-alert">
                          <strong>There was a problem resetting your password.</strong><br/>The link in the email only works once. <a href="recover.php" >Click here to restart the recovery process</a>.
                        </div>
                        <div class="alert alert-danger" role="alert" id="bad-pw-alert">
                          <strong>There was a problem resetting your password.</strong><br/>Passwords must be at least 8 characters in length and both fields must match.
                        </div>
                        <div class="form-group">
                           <label for="recover_email">Enter new password</label>
                            <input type="password" class="form-control" id="reset_password" name="reset_password" required="true" >
                           <small class="form-text text-muted">Password must be at least 8 characters.</small>
                        </div>
                      </div>
                  </div>
                  <div class="row">
                    <div class='col-12'>
                        <div class="form-group">
                           <label for="recover_email">Re-enter new password</label>
                            <input type="password" class="form-control" id="reset_password-repeat" name="reset_password_repeat" required="true" >
                           <small class="form-text text-muted">Password fields must match.</small>
                        </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-6" style="text-align: left;">
                          <a href="register.php">Register New User</a>
                      </div>
                      <div class="col-6" style="text-align: right;">
                          <a href="recover.php">Forgot your password?</a>
                      </div>
                  </div>
                </div>
              </div>
              <div class="card-footer text-muted">
                <div style="width:100%; text-align:center;">
                    <input type="hidden" id="email_guid" name="email_guid" value="<?= $_GET['guid'] ?>">
                    <button type="submit" class="btn btn-primary" style="cursor: pointer;">Submit</button>
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
    $('#bad-link-alert').hide();
    $('#bad-pw-alert').hide();

    $('#reset_form').on('submit', function (e) {
      e.preventDefault();
      $('#bad-link-alert').hide();
      $('#bad-pw-alert').hide();
      $.ajax({
        type: 'post',
        url: 'user/password-reset.php',
        data: $('#reset_form').serialize(),
        success: function (data) {
          if(data == "bad-link-error"){
            $('#bad-link-alert').show();
          }
          if(data == "bad-pw-error"){
            $('#bad-pw-alert').show();
          }
          if(data == "success"){
            location.replace("/");
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