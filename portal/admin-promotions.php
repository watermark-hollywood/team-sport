<?php
    require_once ('includes/managesessions.php'); 
    require_once ('includes/swdb_connect.php'); 
    require_once ('includes/utilityfunctions.php'); 

    ValidateDomain($_SERVER['HTTP_HOST']);
    ValidatePlatformAdminLoggedIn($_SESSION['type']);

?>

<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en"> <!--<![endif]-->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">


    <title>Manage Project Oslo Platform</title>

    <!-- domo arigato mr roboto.. load this font -->
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500' rel='stylesheet' type='text/css'>

    <!-- BEGIN Load Styles for Plugins -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css" >


    <!-- page styles -->
    <link rel="stylesheet" href="assets/css/home.css" />
    <link rel="stylesheet" href="domains/<?= ExtractSubdomains($_SERVER['HTTP_HOST']) ?>/css/portal.css" /> 

    <!-- DataTables -->
    <link href="assets/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="assets/css/datatableicons.css" rel="stylesheet" type="text/css" />

</head>

<body>
	<?php include('includes/topbar.php'); ?>


	<div class="container" id="body-container">
        <div class="row">
          
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


        <!--Datatables--> 
        <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="assets/plugins/datatables/dataTables.buttons.min.js"></script>
        <script src="assets/plugins/datatables/buttons.bootstrap.min.js"></script>
        <script src="assets/plugins/datatables/pdfmake.min.js"></script>
        <script src="assets/plugins/datatables/vfs_fonts.js"></script>
        <script src="assets/plugins/datatables/buttons.html5.min.js"></script>
        <script src="assets/plugins/datatables/buttons.print.min.js"></script>
        <script src="assets/plugins/datatables/dataTables.fixedHeader.min.js"></script>
        <script src="assets/plugins/datatables/dataTables.keyTable.min.js"></script>
        <script src="assets/plugins/datatables/dataTables.responsive.min.js"></script>
        <script src="assets/plugins/datatables/responsive.bootstrap.min.js"></script>
        <script src="assets/plugins/datatables/dataTables.scroller.min.js"></script> 
        <script src="assets/plugins/datatables/datatables.init.js"></script>

        <script type="text/javascript">

            $(document).ready( function() {
              $('#datatable-responsive').DataTable({
                  columnDefs: [
                    {bSortable: false, targets: [8]} 
                  ]
                });
              });



            //TableManageButtons.init();

        </script>

  <script>


    function changeUserType(userid, type, original) {
      $.ajax({
        type: 'post',
        url: 'user/update-user-type.php',
        data: {user_id: userid, user_type: type, original_type: original},
        success: function (data) {
          if(data == "error"){
            console.log("error changing user type");
          }
          if(data == "success"){
             console.log("user updated");
          }
        },
        error: function (data) {
               var r = jQuery.parseJSON(data.responseText);
               alert("Message: " + r.Message);
               alert("StackTrace: " + r.StackTrace);
               alert("ExceptionType: " + r.ExceptionType);
        }
      });
      return false;
    };

    function changeUserVerify(userid, verified, original) {
      $.ajax({
        type: 'post',
        url: 'user/update-user-verify.php',
        data: {user_id: userid, user_verified: verified, original_type: original},
        success: function (data) {
          if(data == "error"){
            console.log("error changing user verified status");
          }
          if(data == "success"){
             console.log("user updated");
          }
        },
        error: function (data) {
               var r = jQuery.parseJSON(data.responseText);
               alert("Message: " + r.Message);
               alert("StackTrace: " + r.StackTrace);
               alert("ExceptionType: " + r.ExceptionType);
        }
      });
      return false;
    };

    function changeUserApprove(userid, approved, original) {
      $.ajax({
        type: 'post',
        url: 'user/update-user-approve.php',
        data: {user_id: userid, user_approved: approved, original_type: original},
        success: function (data) {
          if(data == "error"){
            console.log("error changing user approved status");
          }
          if(data == "success"){
             console.log("user updated");
          }
        },
        error: function (data) {
               var r = jQuery.parseJSON(data.responseText);
               alert("Message: " + r.Message);
               alert("StackTrace: " + r.StackTrace);
               alert("ExceptionType: " + r.ExceptionType);
        }
      });
      return false;
    };

    function deleteConfirm(userid, fname, lname){
        $('#hidden-user-id').val(userid)
        $('#username').html(fname+' '+lname);
        $('#delete-confirm-modal').modal('show');
    }

    function deleteUser(userid) {
      $('#delete-confirm-modal').modal('hide');
      $.ajax({
        type: 'post',
        url: 'user/delete-user.php',
        data: {user_id: userid},
        success: function (data) {
          if(data == "error"){
            console.log("error deleting user");
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
      return false;
    };
  </script>
   
</body>
</html>