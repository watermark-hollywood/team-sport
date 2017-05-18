<?php
    require_once ('includes/managesessions.php'); 
    require_once ('includes/swdb_connect.php'); 
    require_once ('includes/utilityfunctions.php'); 

    ValidateDomain($_SERVER['HTTP_HOST']);
    ValidateAdminLoggedIn($_SESSION['type']);

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
        <button class="btn btn-lg btn-success" style="width:100%;" onclick="addProduct();">Add New Product</button>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
              <tr>
                  <th class="text-center">ID</th>
                  <th class="text-center">Category</th>
                  <th class="text-center">Name</th>
                  <th class="text-center">Description</th>
                  <th class="text-center">Image</th>  
                  <th class="text-center">Price</th>
                  <th class="text-center">Code</th>
                  <th class="text-center"></th>
              </tr>
          </thead>
          <tbody>
          <?php


    $aProducts = GetProducts(ExtractSubdomains($_SERVER['HTTP_HOST']));
// --------------------------------------------------------------------------
// Load our Template HTML
// --------------------------------------------------------------------------
$strApprovalsBody = file_get_contents('includes/template_view_products.html');



    foreach ($aProducts as $topkey => $topvalue) 
    {
      foreach ($topvalue as $key => $value) 
      {


        if ($key == 'id')
        {
          $id = $value;
        }

        if ($key == 'category_name')
        {
          $catname = $value;
        }

        if ($key == 'product_name')
        {
          $prodname = $value;
        }

        if ($key == 'product_description')
        {
            $desc = $value;
        }

        if ($key == 'product_image')
        {
          $image = $value;
        }

        if ($key == 'product_price')
        {
            $price = $value;
        }

        if ($key == 'product_code')
        {
            $code = $value;
        }
        $delete = "<div class='btn-group' role='group' aria-label='edit/delete'>";
        $delete .= "<button class='btn btn-success' onclick=\"edit('$id')\" ><i class='fa fa-edit'></i></button>";
        $delete .= "<button class='btn btn-danger' onclick=\"deleteConfirm('$id', '$prodname')\" ><i class='fa fa-trash'></i></button>";
        $delete .= "</div>";
        }
      /*
      $buttons = "<div class='btn-group' role='group' aria-label='Approve/Delete'>";
      $buttons .= "<button type='button' class='btn btn-success update-button'>Update</button>";
      $buttons .= "<button type='button' class='btn btn-danger delete-button'><i class='fa fa-trash' aria-hidden='true'></i></button>";
      $buttons .= "</div>";
      */
        $strBuffer = sprintf($strApprovalsBody, $id, $catname, $prodname, $desc, $image, $price, $code, $delete);
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
            <h5 class="modal-title">Confirm Delete Product</h5>
            <button type="button" class="close" id="close-recover" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="hidden-product-id" name="hidden-product-id">
            <p>You are about to delete <span id="productname"></span>.</p>
            <p>This cannot be undone. Are you sure?</p>
          </div>
          <div class="modal-footer">
            <div style="width:100%; text-align:center;">
                <button class="btn btn-danger" onclick="deleteProduct($('#hidden-product-id').val())">Delete</button>
                <button class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Product Category Select Modal -->
    <div class="modal fade" id="category-select-modal"  data-backdrop="static" keyboard="false">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Select Category for New Product</h5>
            <button type="button" class="close" id="close-recover" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
          <div class="list-group category-list">
            <?php $aCategories = GetAllCategories();
              foreach($aCategories as $aCat) { ?>
                <a href="/add-product.php?category_id=<?= $aCat['id'] ?>" class="list-group-item"><?= $aCat['category_name'] ?></a>
              <?php } ?>
            </div>
          </div>
          <div class="modal-footer">
            <div style="width:100%; text-align:center;">
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
                    {bSortable: false, targets: [7]} 
                  ]
                });
              });



            //TableManageButtons.init();

        </script>

  <script>
    function addProduct() {
        $('#category-select-modal').modal('show');
    }

    function edit(product_id){
      $(location).attr('href', ('edit-product.php?id='+product_id));
    }


    function deleteConfirm(productid, name){
        $('#hidden-product-id').val(productid)
        $('#productname').html(name);
        $('#delete-confirm-modal').modal('show');
    }

    function deleteProduct(productid) {
      $('#delete-confirm-modal').modal('hide');
      $.ajax({
        type: 'post',
        url: 'user/delete-product.php',
        data: {product_id: productid},
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