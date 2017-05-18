<?php
	require_once ('portal/includes/utilityfunctions.php');
	require_once ('portal/includes/swdb_connect.php'); 
	
	// --------------------------------------------------------------------------  
	// Check the GUID in the database
	// --------------------------------------------------------------------------  
	$guid = $_GET['guid'];
	$query = "SELECT * FROM users WHERE validationGUID = '$guid';"; 
	$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
	$numrows = mysqli_num_rows($result);

	// --------------------------------------------------------------------------  
	// If the number of rows is 1 that means that this guid was found, therefore
	// validated.
	// --------------------------------------------------------------------------  	

	if($numrows == 1)
	{
		$row = mysqli_fetch_array($result,  MYSQLI_ASSOC);
		$admin_id = $row['id'];
	}


?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en"> <!--<![endif]-->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">


    <title><?php if($row){ ?>
    			Complete Project Oslo Portal Registration
    		<?php } else { ?>
    			There Is a Problem Completing Your Oslo Portal Registration
    		<?php } ?>
    		</title>

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
	<?php if($row) { ?>
	  <form name="admin_accept_form" id="admin_accept_form" role="form" action="portal/user/admin-accept-invite.php" method="post">
		<div class="modal fade" id="page-modal" data-backdrop="static" keyboard="false">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Completing Registration</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
			  <div class="modal-body">
	             <div class="container-fluid">
	             	<div class="row" id="success-alert">
	             		<div class="col-12">
	                        <div class="alert alert-success" role="alert">
	                          <strong>Portal Registraton Complete</strong><br/> It will take a few minutes to complete setting up your new portal. We will send you an email when it is ready. This process usually takes about 10 minutes. Thank you for joining Project Oslo!
	                        </div>
	                    </div>
	                </div>
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
	            	<div class='col-12'>
                        <div class="alert alert-danger" role="alert" id="exists-alert">
                          <strong>Prefix problem</strong><br/> Someone has already created a portal using this domain prefix.
                        </div>
	            		<div class="form-group">
						   <label for="invite_domain">Desired Domain Prefix</label>
						    <input type="text" class="form-control" id="invite_domain" name="invite_domain" required="true" >
						    <small class="form-text text-muted">Portal domain will be prefix.project-oslo.com</small>
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
		      <div class="modal-footer">
		      	<div id="accept-submit-button" style="width:100%; text-align:center;">
                  <input type="hidden" id="invite_guid" name="invite_guid" value="<?= $_GET['guid'] ?>">
                  <button type="submit" class="btn btn-primary" style="cursor: pointer;">Finish creating portal</button>
		        </div>
		      	<div id="close-modal-button" style="width:100%; text-align:center;">
		        	<button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">Close</button>
		        </div>
		      </div>
		    </div>
		  </div>
		</div>
	  </form>
	<?php } else {?>
		<div class="modal fade" id="page-modal">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title">Problem Creating Portal</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
			  <div class="modal-body">
	            <p>There was an error processing the link that brought you here. You can <a href="index.php">start the registration process over</a>, or email <a href="mailto:customersupport@project-oslo.com">customer support</a> and inform them of this error.</p>
	          </div>
	          <div class="modal-footer">
		      	<div style="width:100%; text-align:center;">
		        	<a class="btn btn-primary" href="mailto:customersupport@project-oslo.com">Contact Customer Support</a>
		        </div>
		      </div>
	        </div>
	      </div>
	    </div>
	<?php } ?>
	<div class="modal fade" id="success-modal">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Portal Created</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
		  <div class="modal-body">
            <p>We are in the process of completing the setup of your portal. You will receive an email once it is completed (usually 10-15 minutes). Thank you for creating your Project Oslo Portal!</p>
          </div>
          <div class="modal-footer">
	      	<div style="width:100%; text-align:center;">
	        	<button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Close</button>
	        </div>
	      </div>
        </div>
      </div>
    </div>

    </main>

	<form id="create-domain" name="create-domain" action="portal/user/admin-create-domain.php" method="post">
		<input type="hidden" id="create_admin_id" name="create_admin_id" value="<?= $admin_id ?>"/>
	</form>
    <?php include('includes/platform/footer.php'); ?>
    <!-- common functions -->
    <script src="portal/assets/js/common.js"></script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>


    <?php include('portal/includes/footer.php'); ?>
   
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

   $(document).ready(function(){
   		$('#success-modal').modal('hide');
		$('#close-modal-button').hide();
		$('#success-alert').hide();
		$('#exists-alert').hide();
		$('#success-alert').hide();
		$('#password-alert').hide();
		$('#page-modal').modal('show');
   });

   $('#admin_accept_form').on('submit', function (e) {
      e.preventDefault();
      
      $.ajax({
        type: 'post',
        url: 'portal/user/admin-accept-invite.php',
        data: $('#admin_accept_form').serialize(),
        success: function (data) {
          if(data == "pass"){
          	$('#password-alert').show();
          }
          if(data == "success"){
          	$('#success-alert').show();
          	$("#accept-submit-button").hide();
          	$("#close-modal-button").show();
          	$("#create-domain").submit();
          }
          if(data == "exists"){
          	$('#exists-alert').show();
          }
        },
        error: function (data) {
          console.log(data);
        }
      });

    });

   	$('#create-domain').on('submit', function (e) {
   		e.preventDefault();
   		$.ajax({
	        type: 'post',
	        url: 'portal/user/admin-create-domain.php',
	        data: $('#create-domain').serialize(),
	        success: function (data) {
	        	if(data == "success") {
	        		console.log("domain created");
	        	} else {
	        		console.log("error creating domain");
	        	}
	        },

	        error: function (data) {
	          console.log(data);
	        }
		});
   	});
   

	$('.register-link').click(function(event){
		event.preventDefault();
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