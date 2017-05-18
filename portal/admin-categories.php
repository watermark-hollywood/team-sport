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
    <div class="row mt-3">
      <div class="col-12 col-md-4 offset-md-8">
        <button class="btn btn-lg btn-success" style="width:100%;" onclick="addCategory();">Add New Category</button>
      </div>
    </div>
      <div class="row">
        <div class="col-12">
          <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
              <thead>
                  <tr>
                      <th class="text-center">ID</th>
                      <th class="text-center">Name</th>
                      <th class="text-center">Description</th>
                      <th class="text-center">Image</th>  
                      <th class="text-center">VDP</th>
                      <th class="text-center">Active</th>
                      <th class="text-center"></th>
                  </tr>
              </thead>
              <tbody>
<?php


    $aCategories = GetCategories();
// --------------------------------------------------------------------------
// Load our Template HTML
// --------------------------------------------------------------------------
$strApprovalsBody = file_get_contents('includes/template_view_categories.html');



    foreach ($aCategories as $topkey => $topvalue) 
    {
      foreach ($topvalue as $key => $value) 
      {


        if ($key == 'id')
        {
          $id = $value;
        }

        if ($key == 'category_name')
        {
          $name = $value;
        }

        if ($key == 'category_description')
        {
          $desc = $value;
        }

        if ($key == 'category_image')
        {
          $image = $value;
        }

        if ($key == 'category_vdp')
        {
            if ($value > 0) {
                $vdp = "<div class='form-check'>";
                $vdp .="   <label class='form-check-label'>";
                $vdp .="       <input class='form-check-input' type='checkbox' id='vdp$id' name='vdp$id' onchange='changeVDP($id, this.checked, $value)' aria-label='Category allows VDP' checked='checked'>";
                $vdp .="   </label>";
                $vdp .="</div>";
            } else {
                $vdp = "<div class='form-check'>";
                $vdp .="   <label class='form-check-label'>";
                $vdp .="       <input class='form-check-input' type='checkbox' id='vdp$id' name='vdp$id' onchange='changeVDP($id, this.checked)'  aria-label='Category allows VDP'>";
                $vdp .="   </label>";
                $vdp .="</div>";
            }
        }

        if ($key == 'category_active')
        {
                $active = "<div class='form-check'>";
                $active .="   <label class='form-check-label'>";
                $active .="       <input class='form-check-input' type='checkbox' id='vdp$id' name='vdp$id' onchange='changeActive($id, this.checked)' aria-label='Category allows VDP'";
                if ($value > 0) { 
                  $active .= "checked='checked'";
                }
                $active .=">   </label>";
                $active .="</div>";
        }
        $delete = "<div class='btn-group' role='group' aria-label='edit/delete'>";
        $delete .= "<button class='btn btn-success' onclick=\"edit('$id')\" ><i class='fa fa-edit'></i></button>";
        $delete .= "<button class='btn btn-danger' onclick=\"deleteConfirm('$id', '$name')\" ><i class='fa fa-trash'></i></button>";
        $delete .= "</div>";
        }
      /*
      $buttons = "<div class='btn-group' role='group' aria-label='Approve/Delete'>";
      $buttons .= "<button type='button' class='btn btn-success update-button'>Update</button>";
      $buttons .= "<button type='button' class='btn btn-danger delete-button'><i class='fa fa-trash' aria-hidden='true'></i></button>";
      $buttons .= "</div>";
      */
        $strBuffer = sprintf($strApprovalsBody, $id, $name, $desc, $image, $vdp, $active, $delete);
        echo $strBuffer;  
      } // end foreach
?>




                  </tbody>
              </table>
          </div>
      </div>
	</div>



    <!-- Delete Confirm Modal -->
    <div class="modal fade" id="delete-confirm-modal"  data-backdrop="static" keyboard="false">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirm Delete Category</h5>
            <button type="button" class="close" id="close-recover" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="hidden-category-id" name="hidden-category-id">
            <p>You are about to delete <span id="categoryname"></span>.</p>
            <p>This cannot be undone. Are you sure?</p>
          </div>
          <div class="modal-footer">
            <div style="width:100%; text-align:center;">
                <button class="btn btn-danger" onclick="deleteCategory($('#hidden-category-id').val())">Delete</button>
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
                    {bSortable: false, targets: [6]} 
                  ],
                  buttons: []
                });
              });



            TableManageButtons.init();

        </script>

  <script>
  function addCategory() {
      $(location).attr('href', ('add-category.php'));
  }
    function changeVDP(categoryid, vdp) {
      if(vdp == true){
        vdp = 1;  
      } else {
        vdp = 0;
      }
        
      $.ajax({
        type: 'post',
        url: 'user/update-category-vdp.php',
        data: {category_id: categoryid, category_vdp: vdp},
        success: function (data) {
          if(data == "error"){
            console.log("error changing category vdp");
          }
          if(data == "success"){
             console.log("category updated");
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

    function changeActive(categoryid, active) {
      if(active == true){
        active = 1;  
      } else {
        active = 0;
      }
        
      $.ajax({
        type: 'post',
        url: 'user/update-category-active.php',
        data: {category_id: categoryid, category_active: active},
        success: function (data) {
          if(data == "error"){
            console.log("error changing category active");
          }
          if(data == "success"){
             console.log("category updated");
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


    function edit(categoryid){
      $(location).attr('href', ('edit-category.php?id='+categoryid));
    }

    function deleteConfirm(categoryid, name){
        $('#hidden-category-id').val(categoryid)
        $('#categoryname').html(name);
        $('#delete-confirm-modal').modal('show');
    }

    function deleteCategory(categoryid) {
      $('#delete-confirm-modal').modal('hide');
      $.ajax({
        type: 'post',
        url: 'user/delete-category.php',
        data: {category_id: categoryid},
        success: function (data) {
          if(data == "error"){
            console.log("error deleting category");
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