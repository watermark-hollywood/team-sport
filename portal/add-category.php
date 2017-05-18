
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


    <title>Viewing Category</title>

    <!-- domo arigato mr roboto.. load this font -->
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500' rel='stylesheet' type='text/css'>

    <!-- BEGIN Load Styles for Plugins -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css" >
    <link rel="stylesheet" href="assets/plugins/orakuploader/orakuploader.css">


    <!-- page styles -->
    <link rel="stylesheet" href="assets/css/home.css" />
    <link rel="stylesheet" href="domains/<?= ExtractSubdomains($_SERVER['HTTP_HOST']) ?>/css/portal.css" /> 
    
</head>

<body>

    <?php 
      include('includes/topbar.php'); 
      $aCategory = GetAllCategoryInfo($_GET['id']);
    ?>
    <form id="addCategoryForm" action="user/add-category.php" method="post">
    <div class="container" id="body-container">
      <div class="row">
        <div class="col-12">
          <div class="alert alert-success" id="success-alert">
            <button type="button" class="close" data-dismiss="alert">x</button>
            Category added.
          </div>
          <div class="alert alert-danger" id="error-alert">
            <button type="button" class="close" data-dismiss="alert">x</button>
            <strong>Error! </strong> There was a problem adding the category.
          </div>
          <ol class="breadcrumb my-3">
            <li class="breadcrumb-item"><a href="/"><i class="fa fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="/admin-categories.php">Admin Categories</a></li>
            <li class="breadcrumb-item active">Add Category</li>
          </ol>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-3 mb-5 hidden-xs-down">
          <div class="list-group category-list">
          <?php $aCategories = GetAllCategories();
            foreach($aCategories as $aCat) { ?>
              <a href="/edit-category.php?id=<?= $aCat['id'] ?>" <?php if($_GET['id'] == $aCat['id']) { echo('class="list-group-item active"'); } else { echo('class="list-group-item"');}?>><?= $aCat['category_name'] ?></a>
            <?php } ?>
          </div>
        </div>
        <div class="col-sm-9">
          <div id="editCategoryName" class="row">
            <div class="col-12 col-sm-6 col-lg-4">
              <input type="text" class="form-control" id="category_name" name="category_name" placeholder="Category Name" required>
            </div>
          </div>
          <div class="row mb-5">
            <div id="editCategoryImage" class="col-sm-2 mb-5">
                <div id="imageloader" orakuploader="on"></div>
            </div>
            <div id="editCategoryDescription" class="col-sm-10">
              <textarea id="category_description" name="category_description" class="form-control my-3" rows="5" style="resize:none" placeholder="Enter Category Description" required></textarea>
              <div class="form-inline">
                <div class="form-check mb-2 mr-sm-2 mb-sm-0">
                  <label class="form-check-label">
                    <input class="form-check-input" name="VDP" id="VDP" type="checkbox"> Variable Data Printing
                  </label>
                </div>
                <div class="form-check ml-5 mb-2 mr-sm-2 mb-sm-0">
                  <label class="form-check-label">
                    <input class="form-check-input" name="active" id="active" type="checkbox" checked> Active
                  </label>
                </div>
              </div>
            </div>
            </div>
            <div id="editCategoryBasePrice" class="row mb-5">
              <div class="col-12 col-md-6 offset-md-3 col-lg-4 offset-lg-4 text-center"><h2 class="mb-3">Base price for category: </h2>
                <div style="width:50%; margin: auto;">
                  <input type="text" class="form-control mb-5" id="category_base_price" name="category_base_price" placeholder="15.00" required>
                </div>
                <button type="submit" class="btn btn-lg btn-success">Add New Category</button>
              </div>
            </div>
        </div>
      </div>
    </div>
    </form>

   
    <?php include('includes/footer.php'); ?>
    <!-- common functions -->
    <script src="../assets/js/common.js"></script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>


    <script src="assets/plugins/orakuploader/orakuploader.js?ver=1.02"></script>   
   
   <script> 

        $(document).ready(function() {
            $("#success-alert").hide();
            $("#error-alert").hide();


            $('#imageloader').orakuploader({
              orakuploader_path : "assets/plugins/orakuploader/",
              
              orakuploader_main_path : "assets/images/category",
              orakuploader_thumbnail_path : "assets/images/category/tn",
              
              orakuploader_add_image : 'assets/plugins/orakuploader/images/add.png',
              orakuploader_add_label : 'Browse for image',
              
              orakuploader_resize_to : 600,
              orakuploader_thumbnail_size : 300,
              
              orakuploader_maximum_uploads : 1,
              orakuploader_hide_on_exceed : true,
              
              orakuploader_max_exceeded : function() {
                alert("You are limited to one category image.");
              }
              
            });
        });

        $('#addCategoryForm').on('submit', function(e){
          e.preventDefault();
          $.ajax({
            type: 'post',
            url: 'user/add-category.php',
            data: $('#addCategoryForm').serialize(),
            success: function (data) {
              if(data == "error"){
                $("#error-alert").alert();
                $("#error-alert").fadeTo(2000, 500).slideUp(500, function(){
                  $("#error-alert").slideUp(500);
                });
                console.log("error adding option value");
              } else {
                $("#success-alert").alert();
                $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
                  $("#success-alert").slideUp(500);
                  $(location).attr('href', ('edit-category.php?id='+data));
                });
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
        });

   </script>
</body>
</html>