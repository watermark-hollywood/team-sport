<?php
	$allowed_hosts = array('local.project-oslo.com','project-oslo.com','www.project-oslo.com','osloideas.com','portal.osloideas.com','local.osloideas.com','www.osloideas.com','www.watermarkdigital.com');
	if (!isset($_SERVER['HTTP_HOST']) || !in_array($_SERVER['HTTP_HOST'], $allowed_hosts)) {
		header( "Location: portal/login.php" );
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
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500|Montserrat:100,300,400,600' rel='stylesheet' type='text/css'>

    <!-- BEGIN Load Styles for Plugins -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">


    <!-- page styles -->
	<link rel="stylesheet" href="assets/plugins/platform/font-awesome-4.7.0/css/font-awesome.min.css" type="text/css" />
	<link rel="stylesheet" href="assets/css/platform/platform.css" type="text/css" />

</head>

<body class="stretched">
	<!-- Navigation Menu and Slider -->
	<div id="mySidenav" class="sidenav">
	    <a class="navmenu-brand" href="http://<?= $_SERVER['HTTP_HOST'] ?>">
	        <img id="sidebar-logo" src="images/platform/Oslo_Logo-header-black.png" height="36" alt="Oslo" />
	    </a>
	    <ul id="sidebar-navbar-nav" class="nav navbar-nav">
	        <li class="hidden-md-up register-link"><a href="#">Register</a>
	        </li>
	        <li class="hidden-md-up"><hr></li>
	        <li><a href="#">My Dashboard</a>
	        </li>
	        <li><a href="#">New Order</a>
	        </li>
	        <li><a href="#">Open Orders</a>
	        </li>
	        <li><a href="#">Past Orders</a>
	        </li>
	        <li><a href="#">Account Settings</a>
	        </li>
	    </ul>
	</div>

	<!-- Document Wrapper -->
	<div id="main" class="container-fluid">
		<nav id="top-navbar" class="navbar">
			<ul class="nav navmenu-nav">
      		  <li class="nav-item">
		  	    <button class="btn btn-default" id="open-navbar-btn" type="button" aria-label="Toggle navigation">
		          <i class="fa fa-bars" aria-hidden="true"></i>
		        </button>
	          </li>
		      <li class="mr-auto">
		        <a href="index.php">
		          <img id="top-navbar-logo" src="images/platform/Oslo_Logo-header.png" alt=""  />
		        </a>
		      </li>
		      <li>
		          <div class="hidden-sm hidden-xs" style="float:right;">
		            <button href="#" id="register-btn" class="btn btn-default register-link">Register</button>
		          </div>
		      </li>
		    </ul>
		</nav>
	<?php 
		include ('includes/platform/slider.php');
	?>


	<!-- Body Content -->
		<section id="content">
    		<div class="content-wrap">

		<!-- Load Content Sections -->
		<?php 
			include ('includes/platform/content.php');
			include ('includes/platform/pricing.php');
			include ('includes/platform/connections.php');
			include ('includes/platform/signup.php');
		?>
        
        	</div><!-- end div class="content-wrap" -->
		</section><!-- end section id="content" -->


	<!-- Register Admin Modal -->
	<div class="modal fade" id="page-modal">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Create an Oslo Portal</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
		  <form name="admin_register_form" id="admin_register_form" role="form" action="portal/user/register.php" method="post">
	      <div class="modal-body">
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
					  <strong>A conflict exists</strong><br/> Either someone has registered with this email address, or your Desired Domain Prefix is in use. If you suspect the domain is in use, try visiting it and logging in or registering with that domain.
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
					    <input type="password" class="form-control" id="register_password_repeat" name="register_password_repeat" required="true" >
					   <small class="form-text text-muted">Password fields must match.</small>
					</div>
                </div>
            </div>
            <div class="row">
            	<div class='col-12'>
            		<div class="form-group">
					   <label for="register_first">Desired Domain Prefix</label>
					    <input type="text" class="form-control" id="register_domain" name="register_domain" required="true" >
					    <small class="form-text text-muted">Portal domain will be prefix.project-oslo.com</small>
					</div>
                </div>
            </div>
            <div class="row">
            	<div class="col-12">
                	<a class="pull-right" href="#" id="login_help_show">Need help?</a>
                </div>
            </div>
	      </div>
	      <div class="modal-footer">
	      	<div style="width:100%; text-align:center;">
	      		<input type="hidden" id="register_type" name="register_type" value="16" >
	        	<button type="submit" class="btn btn-primary" style="cursor: pointer;">Sign Up</button>
	        </div>
	      </div>
	      </form>
	    </div>
	  </div>
	</div>

	<!--Help Modal -->
	<div class="modal fade bd-example-modal-sm" id="help-modal">
	  <div class="modal-dialog modal-sm" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Problems Registering?</h5>
	        <button type="button" class="close" data-dismiss="modal" id="help-close" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
		  <div class="modal-body">
            <p>Thank you for your interest in Oslo. We want to help you set up your portal as quickly as possible. </p>
            <p>If you are having problems please <a href="#" id="password_reset_show">contact support online</a>.</p>
            <p>Or if you prefer to speak to a representative, call 1-800-555-1212.</p>
          </div>
        </div>
      </div>
    </div>

	<div class="modal fade" id="success-modal" >
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Thank you for registering with Project Oslo</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
		  <div class="modal-body">
            <p>An email is being sent to the address you provided with a verification link.</p>
            <p>Click this link to verify your email address and begin configuring your Oslo portal.</p>
          </div>
        </div>
      </div>
    </div>

	<!--Failure Modal -->
	<div class="modal fade" id="dberr-modal">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Database Error</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
		  <div class="modal-body">
            <p>There was a database error when trying to add this record to the system.</p>
            <p>Please email <a href="mailto:customersupport@projectoslo.com">customer service</a> and inform them of this error.</p>
          </div>
        </div>
      </div>
    </div>

	<!--Failure Modal -->
	<div class="modal fade" id="mailerr-modal">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Email Error</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
		  <div class="modal-body">
            <p>There was an error when trying to send your validation email.</p>
            <p>Please email <a href="mailto:customersupport@projectoslo.com">customer service</a> and inform them of this error.</p>
          </div>
        </div>
      </div>
    </div>
    </main>
    <?php include('includes/platform/footer.php'); ?>
    <!-- common functions -->
    <script src="portal/assets/js/common.js"></script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>

	<!--<script type="text/javascript" src="assets/js/platform/jquery-2.2.4.min.js"></script>
	<script type="text/javascript" src="assets/js/platform/bootstrap.min.js"></script> -->
    <!--<script type="text/javascript" src="assets/js/platform/jasny-bootstrap.min.js"></script>  -->
   
   <script> 

	

   	var $item = $('.carousel .carousel-item'); 
	var $wHeight = $(window).height();
	$item.eq(0).addClass('active');
	$item.height($wHeight); 
	$item.addClass('full-screen');

	$('.carousel img').each(function() {
	  var $src = $(this).attr('src');
	  var $color = $(this).attr('data-color');
	  $(this).parent().css({
	    'background-image' : 'url(' + $src + ')',
	    'background-color' : $color
	  });
	  $(this).remove();
	});

	$(window).on('resize', function (){
	  $wHeight = $(window).height();
	  $item.height($wHeight);
	});

	$('.carousel').carousel({
	  interval: 6000,
	  pause: "false"
	});

   	$('#exists-alert').hide();
   	$('#password-alert').hide();

	$('#admin_register_form').on('submit', function (e) {
      e.preventDefault();
      
      $.ajax({
        type: 'post',
        url: 'portal/user/register.php',
        data: $('#admin_register_form').serialize(),
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
			$('#page-modal').modal('hide');
			$('#success-modal').modal('show');
			$('#launch-button').hide();
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

	$('#login_help_show').click(function(event){
		event.preventDefault();
		$('#page-modal').modal('hide');
		$('#help-modal').modal('show');
	});

	$('#launch-button').click(function(event){
		$('#page-modal').modal('show');
	});

	$('.register-link').click(function(event){
		event.preventDefault();
		$('#page-modal').modal('show');
	});

	$('#help-close').click(function(event){
		$('#page-modal').modal('show');
	});

	function open_navbar(){
	    $("#mySidenav").css("left", "0px");
	    $("#main").css("marginLeft", "250px");
	    $("#top-navbar-logo").css("visibility", "hidden");
	    $(this).one("click", close_navbar);
	}
	function close_navbar(){
	    $("#mySidenav").css("left", "-250px");
	    $("#main").css("marginLeft", "0px");
	    $("#top-navbar-logo").css("visibility", "visible");
	    $(this).one("click", open_navbar);
	}

	$('#open-navbar-btn').one("click", open_navbar);
   </script>
</body>
</html>