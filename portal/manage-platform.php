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
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                <div class="card text-center mt-5">
                    <div class="card-header">
                        <h5 class="my-0">Invite Users to register a domain at project-oslo.com</h5>
                    </div>
                    <div class="card-block">
                    <div class="alert alert-danger" role="alert" id="exists-alert" style="display:none;">
                      <strong>A conflict exists</strong><br/> Someone has registered this email address. Please check the user list below to verify the user exists..
                    </div>
                    <div class="alert alert-success" role="alert" id="invite-success-alert" style="display:none;">
                      <strong>Invitation Sent</strong>.
                    </div>
                        <form id="invite-form" name="invite-form" action="users/invite-domain-admin.php" method="post">
                            <input type="hidden" id="inviter-name" name="inviter-name" value="<?= $_SESSION['username'] ?>">
                            <div class="input-group">
                              <span class="input-group-addon"><i class="fa fa-envelope-o"></i></span>
                              <input type="email" id="invitee-email" name="invitee-email" class="form-control" placeholder="Enter email address" aria-label="Send invite to join www.project-oslo.com">
                              <span class="input-group-btn">
                                <button type="submit" id="email-btn" name="email-btn" class="btn btn-success" type="button">Email</button>
                              </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
            <?php 
    
    $aUsers = GetAllUserInfo();

    //echo "<pre>";
    //print_r($aUsers);
    //echo "</pre>";
?>
                <div class="card text-center my-5">
                    <div class="card-header">
                        <h5 class="my-0">Manage users for Project Oslo</h5>
                    </div>
                    <div class="card-block">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th class="text-center">ID</th>
                                                <th class="text-center">Domain</th>
                                                <th class="text-center">E-mail</th>
                                                <th class="text-center">First</th>
                                                <th class="text-center">Last</th>  
                                                <th class="text-center">Access</th>
                                                <th class="text-center">Email</th>
                                                <th class="text-center">Approved</th>
                                                <th class="text-center"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
<?php


// --------------------------------------------------------------------------
// Load our Template HTML
// --------------------------------------------------------------------------
$strApprovalsBody = file_get_contents('includes/admin_template_view_approvals.html');



    foreach ($aUsers as $topkey => $topvalue) 
    {
      foreach ($topvalue as $key => $value) 
      {


        if ($key == 'id')
        {
          $id = $value;
        }

        if ($key == 'domain')
        {
          $domain = $value;
        }

        if ($key == 'email')
        {
          $email = $value;
        }

        if ($key == 'lname')
        {
          $lname = $value;
        }

        if ($key == 'fname')
        {
          $fname = $value;
        }

        if ($key == 'type')
        {
            if(($value & 32) > 0 && ($_SESSION['type'] & 32) < 1){
                $type = "Platform Admin";
            } else {
                $type = "<input type='hidden' id='user_id' name='user_id' value='$id'>";
                $type .= "<input type='hidden' id='original_type' name='original_type' value='$value'>";
                $type .= "<div class='form-group my-0'>";
                $type .= "<select id='user_type' name='user_type' class='form-control-sm' onchange='changeUserType($id, this.value, $value)'>";
                if (($value & 8) > 0) {
                    $type .= "<option value='8' selected='selected'>Portal User</option>";
                } else {
                    $type .= "<option value='8'>Portal User</option>";
                }
                if (($value & 16) > 0) {
                    $type .= "<option value='16' selected='selected'>Portal Admin</option>";
                } else {
                    $type .= "<option value='16'>Portal Admin</option>";
                }
                if(($_SESSION['type'] & 32) > 0) {
                    if (($value & 32) > 0) {
                        $type .= "<option value='32' selected='selected'>Platform Admin</option>";
                    } else {
                        $type .= "<option value='32'>Platform Admin</option>";
                    }
                }
                $type .= "</select>";
                $type .= "</div>";
            }

            if (($value & 1) > 0) {
                $verified = "<div class='form-check'>";
                if(($value & 32) > 0 && ($_SESSION['type'] & 32) < 1){
                    $verified .="<fieldset disabled>";
                }
                $verified .="   <label class='form-check-label'>";
                $verified .="       <input class='form-check-input' type='checkbox' id='verified_checkbox$id' name='verified_checkbox$id' onchange='changeUserVerify($id, this.checked, $value)' aria-label='Email Verified' >";
                $verified .="   </label>";
                if(($value & 32) > 0 && ($_SESSION['type'] & 32) < 1){
                    $verified .="</fieldset>";
                }
                $verified .="</div>";
            } else {
                $verified = "<div class='form-check'>";
                if(($value & 32) > 0 && ($_SESSION['type'] & 32) < 1){
                    $verified .="<fieldset disabled>";
                }
                $verified .="   <label class='form-check-label'>";
                $verified .="       <input class='form-check-input' type='checkbox' id='verified_checkbox$id' name='verified_checkbox$id' onchange='changeUserVerify($id, this.checked, $value)'  aria-label='Email Verified' checked='checked'>";
                $verified .="   </label>";
                if(($value & 32) > 0 && ($_SESSION['type'] & 32) < 1){
                    $verified .="</fieldset>";
                }
                $verified .="</div>";
            }

            if (($value & 2) > 0) {
                $approved = "<div class='form-check'>";
                if(($value & 32) > 0 && ($_SESSION['type'] & 32) < 1){
                    $approved .="<fieldset disabled>";
                }
                $approved .="   <label class='form-check-label'>";
                $approved .="       <input class='form-check-input' type='checkbox' id='approved_checkbox$id' name='approved_checkbox$id' onchange='changeUserApprove($id, this.checked, $value)' aria-label='Admin Approved' >";
                $approved .="   </label>";
                if(($value & 32) > 0 && ($_SESSION['type'] & 32) < 1){
                    $approved .="</fieldset>";
                }
                $approved .="</div>";
            } else {
                $approved = "<div class='form-check'>";
                if(($value & 32) > 0 && ($_SESSION['type'] & 32) < 1){
                    $approved .="<fieldset disabled>";
                }
                $approved .="   <label class='form-check-label'>";
                $approved .="       <input class='form-check-input' type='checkbox' id='approved_checkbox$id' name='approved_checkbox$id'  onchange='changeUserApprove($id, this.checked, $value)' aria-label='Admin Approved' checked='checked'>";
                $approved .="   </label>";
                if(($value & 32) > 0 && ($_SESSION['type'] & 32) < 1){
                    $approved .="</fieldset>";
                }
                $approved .="</div>";
            }
            
            if(($value & 32) > 0 && ($_SESSION['type'] & 32) < 1){
                $delete = '<button class=\'btn btn-danger disabled\'><i class=\'fa fa-trash\'></i></button>';
            } else {
                $delete = "<button class='btn btn-danger' onclick=\"deleteConfirm('$id', '$fname', '$lname')\" ><i class='fa fa-trash'></i></button>";
            }

        }
      } // end foreach
      /*
      $buttons = "<div class='btn-group' role='group' aria-label='Approve/Delete'>";
      $buttons .= "<button type='button' class='btn btn-success update-button'>Update</button>";
      $buttons .= "<button type='button' class='btn btn-danger delete-button'><i class='fa fa-trash' aria-hidden='true'></i></button>";
      $buttons .= "</div>";
      */
      $strBuffer = sprintf($strApprovalsBody, $id, $domain, $email, $fname, $lname, $type, $verified, $approved, $delete);
      echo $strBuffer;  

    }
?>




                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-muted">
                    </div>
                </div>
            </div><!-- end col -->
        </div><!-- end row -->
	</div>



    <!-- Delete Confirm Modal -->
    <div class="modal fade" id="delete-confirm-modal"  data-backdrop="static" keyboard="false">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirm Delete User</h5>
            <button type="button" class="close" id="close-recover" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="hidden-user-id" name="hidden-user-id">
            <p>You are about to delete <span id="username"></span>.</p>
            <p>This cannot be undone. Are you sure?</p>
          </div>
          <div class="modal-footer">
            <div style="width:100%; text-align:center;">
                <button class="btn btn-danger" onclick="deleteUser($('#hidden-user-id').val())">Delete</button>
                <button class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
            </div>
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


        <!--Datatables--> 
        <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
        <!--<script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>
        <script src="assets/plugins/datatables/jszip.min.js"></script> -->
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
  $('#exists-alert').hide();
  $('#invite-success-alert').hide();

  $('#invite-form').on('submit', function (e) {
      e.preventDefault();
      $('#exists-alert').hide();
      $('#invite-success-alert').hide();
      $.ajax({
        type: 'post',
        url: 'user/invite-domain-admin.php',
        data: $('#invite-form').serialize(),
        success: function (data) {
          if(data == "error"){
            $('#exists-alert').show();
          }
          if(data == "success"){
            $('#invite-success-alert').show();
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
    });

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


    $('#body-background-picker').colorpicker();
    $('#topbar-background-picker').colorpicker();
    $('#topbar-link-picker').colorpicker();
    $('#body-text-picker').colorpicker();
  </script>
   
</body>
</html>