<?php
    require_once ('includes/managesessions.php'); 
    require_once ('includes/swdb_connect.php'); 
    require_once ('includes/utilityfunctions.php'); 

    ValidateDomain($_SERVER['HTTP_HOST']); 
    ValidateAdminLoggedIn($_SESSION['type']);

    $styles =[];
    $domain = $_SESSION['domain_id'];
    $query = "SELECT style_name, property_name, property_value FROM domain_styles ";
    $query .="WHERE domain_id ='$domain' ORDER BY id;"; 
    $result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
    
    if (!empty($result)) {
      foreach ($result as $row) {
        $styles[] = $row;
      }
    }

?>

<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en"> <!--<![endif]-->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">


    <title>Welcome to Project Oslo</title>

    <!-- domo arigato mr roboto.. load this font -->
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500' rel='stylesheet' type='text/css'>

    <!-- BEGIN Load Styles for Plugins -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css" >


    <!-- page styles -->
    <link rel="stylesheet" href="assets/css/home.css" />
    <link rel="stylesheet" href="assets/plugins/orakuploader/orakuploader.css">
    <link href="assets/plugins/colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <link rel="stylesheet" href="domains/<?= ExtractSubdomains($_SERVER['HTTP_HOST']) ?>/css/portal.css" /> 

</head>

<body>
	<?php include('includes/topbar.php'); ?>
	<div class="container" id="body-container">
		<div class="row">
            
			  <div class="col-xs-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3">
          <div class="card text-center my-5">
            <form id="style-form" name="style-form" action="assets/ajax/update-styles.php" method="post">
              <div class="card-header">
                <h5 class="my-0">Manage colors and images for <?= $_SESSION['domain'] ?>.project-oslo.com</h5>
              </div>
              <div class="card-block text-left">
                <p class="card-text">Customize your project-oslo portal to match your corporate branding.</p>
                <div class="alert alert-danger" role="alert" id="error-alert" name="error-alert">
                  <strong>There was a problem updating your portal styles.</strong><br/>Please try again, or contact <a href="mailto:customerservice@project-oslo.com">customer service</a>.
                </div>
                <strong>Body Styles</strong><br/>
                <div class="form-group row">
                  <label for="body-background-picker" class="col-12 col-sm-12 col-md-6 col-lg-4 col-form-label text-right">background-color:</label>
                  <div class="col-12 col-sm-12 col-md-6 col-lg-4">
                    <div id="body-background-picker" class="input-group colorpicker-component"> <input type="text" id="body-background-color" name="body-background-color" value="<?= $styles[0]['property_value'] ?>" class="form-control" /> <span class="input-group-addon"><i></i></span> </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="body-text-picker" class="col-12 col-sm-12 col-md-6 col-lg-4 col-form-label text-right">(text) color:</label>
                  <div class="col-12 col-sm-12 col-md-6 col-lg-4">
                    <div id="body-text-picker" class="input-group colorpicker-component"> <input type="text" id="body-text-color" name="body-text-color" value="<?= $styles[1]['property_value'] ?>" class="form-control" /> <span class="input-group-addon"><i></i></span> </div>
                  </div>
                </div>
                <hr />
                <strong>Top Bar Styles</strong><br/>
                <div class="form-group row">
                  <label for="topbar-background-picker" class="col-12 col-sm-12 col-md-6 col-lg-4 col-form-label text-right">background color:</label>
                  <div class="col-12 col-sm-12 col-md-6 col-lg-4">
                    <div id="topbar-background-picker" class="input-group colorpicker-component"> <input type="text" id="topbar-background-color" name="topbar-background-color" value="<?= $styles[2]['property_value'] ?>" class="form-control" /> <span class="input-group-addon"><i></i></span> </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="topbar-link-picker" class="col-12 col-sm-12 col-md-6 col-lg-4 col-form-label text-right">link color:</label>
                  <div class="col-12 col-sm-12 col-md-6 col-lg-4">
                    <div id="topbar-link-picker" class="input-group colorpicker-component"> <input type="text" id="topbar-link-color" name="topbar-link-color" value="<?= $styles[3]['property_value'] ?>" class="form-control" /> <span class="input-group-addon"><i></i></span> </div>
                  </div>
                </div>
                <hr />
                <strong>Portal logo</strong><br/>
                <div class="form-group row">
                  <label for="topbar-link-picker" class="col-12 col-sm-12 col-md-4 col-form-label text-right">Current PNG:</label> 
                  <div class="col-12 col-sm-12 col-md-8 ">
                    <img src="domains/<?=$_SESSION['domain']?>/images/tn/<?= $styles[4]['property_value'] ?>"/>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="topbar-link-picker" class="col-12 col-sm-12 col-md-4 col-form-label text-right">Upload PNG:</label> 
                  <div class="col-12 col-sm-12 col-md-8">
                    <div id="imageloader" orakuploader="on"></div>
                  </div>
                </div>
              </div>
              <div class="card-footer text-muted">
                <button type="submit" class="btn btn-primary" style="cursor: pointer;">Update</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
	</div>


    <?php include('includes/footer.php'); ?>
    <!-- common functions -->
    <script src="assets/js/common.js"></script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
    
    <!--color picker -->
    <script src="assets/plugins/colorpicker/js/bootstrap-colorpicker.js"></script>

    <!-- Uploader -->
    <script src="assets/plugins/orakuploader/orakuploader.js?ver=1.02"></script>   

  <script>
    $(document).ready(function(){
    $('#imageloader').orakuploader({
      orakuploader_path : "assets/plugins/orakuploader/",
      
      orakuploader_main_path : "domains/<?=$_SESSION['domain']?>/images",
      orakuploader_thumbnail_path : "domains/<?=$_SESSION['domain']?>/images/tn",
      
      orakuploader_add_image : 'assets/plugins/orakuploader/images/add.png',
      orakuploader_add_label : 'Browse for image',
      
      orakuploader_resize_to : 600,
      orakuploader_thumbnail_size : 300,
      
      orakuploader_maximum_uploads : 1,
      orakuploader_hide_on_exceed : true,
      
      orakuploader_max_exceeded : function() {
        alert("You are limited to one logo image.");
      }
      
    });
  });


  $('#error-alert').hide();

  $('#style-form').on('submit', function (e) {
      e.preventDefault();
      $.ajax({
        type: 'post',
        url: 'assets/ajax/update-styles.php',
        data: $('#style-form').serialize(),
        success: function (data) {
          if(data == "error"){
            $('#error-alert').show();
          }
          if(data == "success"){
             location.reload();
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

    $('#body-background-picker').colorpicker();
    $('#topbar-background-picker').colorpicker();
    $('#topbar-link-picker').colorpicker();
    $('#body-text-picker').colorpicker();
  </script>
   
</body>
</html>