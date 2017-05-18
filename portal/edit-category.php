
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
    <div class="container" id="body-container">
      <div class="row">
        <div class="col-12">
          <div class="alert alert-success" id="success-alert">
            <button type="button" class="close" data-dismiss="alert">x</button>
            Category details updated.
          </div>
          <div class="alert alert-danger" id="error-alert">
            <button type="button" class="close" data-dismiss="alert">x</button>
            <strong>Error! </strong> There was a problem updating the category details.
          </div>
          <ol class="breadcrumb my-3">
            <li class="breadcrumb-item"><a href="/"><i class="fa fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="/admin-categories.php">Admin Categories</a></li>
            <li class="breadcrumb-item active">Edit <?= $aCategory['category_name'] ?></li>
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
          <div id="showCategoryName"><h1 class="mb-3"><?= $aCategory['category_name'] ?>  <button class='btn btn-sm btn-success' onclick="editName()" ><i class='fa fa-edit'></i></button></h1></div>
          <div id="editCategoryName" class="row">
            <div class="col-12 col-sm-6 col-lg-4">
              <div class="input-group">
                <input type="text" class="form-control" id="categoryNameField" value="<?= $aCategory['category_name'] ?>">
                <span class="input-group-btn">
                  <button class="btn btn-secondary" type="button" onclick="updateCategoryName(<?= $_GET['id'] ?>)">Update</button>
                </span>
              </div>
            </div>
          </div>
            <div class="row mb-5">
              <div id="showCategoryImage" class="col-sm-2 mb-5"><img src="/assets/images/category/<?= $aCategory['category_image'] ?>" alt="<?= $aCategory['category_name'] ?>" title="<?= $aCategory['category_name'] ?>" class="img-thumbnail">
                <button id="editImageButton" class='btn btn-sm btn-success' onclick="editImage()" ><i class='fa fa-edit'></i></button>
              </div>
              <div id="editCategoryImage" class="col-sm-2 mb-5">
                <form id="image-form" name="image-form" action="user/update-category-image.php" method="post">
                  <input type="hidden" name="category_id" value="<?= $_GET['id'] ?>">
                  <div id="imageloader" orakuploader="on"></div>
                  <button type="submit" id="editImageButton" class='btn btn-success'>Update</button>
                </form>
              </div>
              <div id="showCategoryDescription" class="col-sm-10">
                <h3>Description</h3>
                <p><?= $aCategory['category_description'] ?> 
                <button id="editDescriptionButton" class='btn btn-sm btn-success' onclick="editDescription()" ><i class='fa fa-edit'></i></button></p>
              </div>
              <div id="editCategoryDescription" class="col-sm-10">
                <h3>Description</h3>
                <div class="input-group">
                    <textarea id="categoryDescriptionField" class="form-control" rows="5" style="resize:none"><?= $aCategory['category_description'] ?></textarea>
                <span class="input-group-btn">
                  <button class="btn btn-success" type="button" onclick="updateCategoryDescription(<?= $_GET['id'] ?>)">Update</button>
                </span>
                </div>
              </div>
            </div>
            <div class="row">
              <div id="showCategoryBasePrice" class="col-12 col-md-6 text-center"><h2>Base Price for <?= $aCategory['category_name'] ?>: $<?= $aCategory['category_base_price'] ?> each. <button class='btn btn-sm btn-success' onclick="editBasePrice()" ><i class='fa fa-edit'></i></button></h2>
              </div>
              <div id="editCategoryBasePrice" class="col-12 col-md-6 text-center"><h2 class="mb-3">Base Price for <?= $aCategory['category_name'] ?>: </h2>
                <div class="input-group" style="width:50%; margin: auto;">
                  <input type="text" class="form-control" id="categoryBasePriceField" value="<?= $aCategory['category_base_price'] ?>">
                  <span class="input-group-btn">
                    <button class="btn btn-secondary" type="button" onclick="updateCategoryBasePrice(<?= $_GET['id'] ?>)">Update</button>
                  </span>
                </div>
              </div>
              <div id="showCategoryMinimumOrder" class="col-sm-12 col-md-6 text-center"><h2>Minimum Order: <?= $aCategory['category_minimum_order'] ?> pieces. <button class='btn btn-sm btn-success' onclick="editMinimumOrder()" ><i class='fa fa-edit'></i></button></h2>
              </div>
              <div id="editCategoryMinimumOrder" class="col-sm-12 col-md-6 text-center"><h2 class="mb-3">Minimum Order: </h2>
                <div class="input-group" style="width:50%; margin: auto;">
                  <input type="text" class="form-control" id="categoryMinimumOrderField" value="<?= $aCategory['category_minimum_order'] ?>">
                  <span class="input-group-btn">
                    <button class="btn btn-secondary" type="button" onclick="updateCategoryMinimumOrder(<?= $_GET['id'] ?>)">Update</button>
                  </span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <h2 class="my-3">Options for <?= $aCategory['category_name'] ?></h2>
                    <div class="row">
                  <?php $aOptions = getCategoryOptions($_GET['id']); 
                    foreach($aOptions as &$aOption){
                      $aValues = explode(",", $aOption['option_values']);
                      $aPrices = explode(",", $aOption['option_prices']);
                  ?>
                    <div class="col-sm-4 mb-5">
                      <div class="card featured-card">
                        <div class="card-block">
                            <h4><?= $aOption['option_name'] ?> <?php if($aOption['option_file'] > 0){ ?>  <span style="font-size: 12px; color:rgb(92, 184, 92);">upload<?php } ?> <?php if($aOption['option_vdp'] > 0){ ?>  <span style="font-size: 12px; color:rgb(92, 184, 92);">vdp<?php } ?></span></h4>
                            <table style="width:100%;">
                              <tr>
                                <th>value</th>
                                <th>price</th>
                                <th></th>
                              </tr>
                              <?php 
                                $i=0;
                                  foreach($aValues as $aValue) { ?>
                                  <tr>
                                    <td><?= trim($aValue) ?></td>
                                    <td><?php if($aPrices[$i]){
                                            echo('$'.trim($aPrices[$i]));
                                          } else {
                                            echo('-');
                                          }
                                          ?>
                                    <td class="text-right"><button class="btn btn-sm btn-danger" onclick='deleteOptionValue(<?= $aOption['id'] ?>, <?php echo(json_encode($aValues)); ?>, <?php echo(json_encode($aPrices)); ?>, <?= $i ?>)'><i class="fa fa-trash"></i></button>
                                  </tr>
                                <?php $i++; } ?>
                                <tr id="showAddValue<?= $aOption['id'] ?>">
                                  <td colspan="3" class="text-center">
                                    <button class="btn btn-success btn-sm mt-3" onclick="showAddOptionValue(<?= $aOption['id'] ?>)">Add Value</button>
                                  </td>
                                </tr>
                                <form class="addOptionValueForm" action="user/add-option-value.php" method="post">
                                <tr id="editAddValue<?= $aOption['id'] ?>" >
                                  <td>
                                    <input type="hidden" name="option_id" id="option_id" value="<?= $aOption['id'] ?>">
                                    <input type="text" name="option_name" id="option_name" style="width:100%" placeholder="option name" required>
                                  </td>
                                  <td>
                                    <input type="text" name="option_price" id="option_price" style="width:100%" placeholder="price">
                                  </td>
                                  <td>
                                    <button type="submit" class="btn btn-success btn-sm">Add</button>
                                  </td>
                                </tr>
                                </form>
                                <tr>
                                  <td colspan="3" class="text-center">
                                    <button class="btn btn-secondary mt-3" onclick="deleteCategoryOption(<?= $aOption['id'] ?>)">Delete <?= $aOption['option_name'] ?></button>
                                  </td>
                                </tr>
                            </table>
                        </div>
                      </div>
                    </div>
                  <?php } ?>
                  <div class="col-sm-4 mb-5">
                    <div class="card featured-card">
                      <div class="card-block">
                          <div class="input-group mb-3">
                            <input type="text" class="form-control" id="addCategoryOptionField" placeholder="Add Option"><br/>
                          </div>
                          <div class="form-check">
                            <label class="form-check-label">
                              <input class="form-check-input" id="addCategoryOptionFile" type="checkbox" value="1">
                              This option requires a file upload.
                            </label>
                          </div>
                          <div class="form-check">
                            <label class="form-check-label">
                              <input class="form-check-input" id="addCategoryOptionVDP" type="checkbox" value="1">
                              This option uses VDP.
                            </label>
                          </div>
                          <span class="input-group-btn">
                            <button class="btn btn-secondary" type="button" onclick="addCategoryOption(<?= $_GET['id'] ?>)">Add</button>
                          </span>
                        </div> 
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

        </div>
      </div>
    </div>
    

   
    <?php include('includes/footer.php'); ?>
    <!-- common functions -->
    <script src="../assets/js/common.js"></script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>


    <script src="assets/plugins/orakuploader/orakuploader.js?ver=1.02"></script>   
   
   <script> 

        $(document).ready(function() {
            $("#editCategoryName").hide();
            $('#editCategoryBasePrice').hide();
            $("#editCategoryImage").hide();
            $("#editCategoryDescription").hide();
            $("#editCategoryMinimumOrder").hide();
            <?php for($p=0;$p<count($aOptions);$p++){ ?>
              $("#editAddValue<?= $aOptions[$p]['id'] ?>").hide();
            <?php } ?>
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

        function editName() {
          $("#showCategoryName").hide();
          $("#editCategoryName").show();
        }

        function editBasePrice() {
          $("#showCategoryBasePrice").hide();
          $("#editCategoryBasePrice").show();
        }

        function editMinimumOrder() {
          $("#showCategoryMinimumOrder").hide();
          $("#editCategoryMinimumOrder").show();
        }

        function editImage() {
          $("#showCategoryImage").hide();
          $("#editCategoryImage").show();
        }

        function editDescription() {
          $("#showCategoryDescription").hide();
          $("#editCategoryDescription").show();
        }

        function showAddOptionValue(i) {
          $("#showAddValue"+i).hide();
          $("#editAddValue"+i).show();
        }

        function deleteOptionValue (optionid, aValues, aPrices, i) {
          aValues.splice(i, 1);
          aPrices.splice(i, 1);
          
          var sValues = aValues.toString();
          var sPrices = aPrices.toString();
          $.ajax({
            type: 'post',
            url: 'user/delete-option-value.php',
            data: {option_id: optionid, option_values: sValues, option_prices: sPrices},
            success: function (data) {
              if(data == "error"){
                $("#error-alert").alert();
                $("#error-alert").fadeTo(2000, 500).slideUp(500, function(){
                  $("#error-alert").slideUp(500);
                });
                console.log("error deleting option value");
              }
              if(data == "success"){
                $("#success-alert").alert();
                $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
                  $("#success-alert").slideUp(500);
                  window.location.reload();
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
        }

        function deleteCategoryOption (optionid) {
          $.ajax({
            type: 'post',
            url: 'user/delete-category-option.php',
            data: {option_id: optionid},
            success: function (data) {
              if(data == "error"){
                $("#error-alert").alert();
                $("#error-alert").fadeTo(2000, 500).slideUp(500, function(){
                  $("#error-alert").slideUp(500);
                });
                console.log("error deleting category option");
              }
              if(data == "success"){
                $("#success-alert").alert();
                $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
                  $("#success-alert").slideUp(500);
                  window.location.reload();
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
        }

        function addCategoryOption (categoryid) {
          var optionname = $('#addCategoryOptionField').val();
          var optionfile = 0;
          var optionvdp = 0;
          if(document.getElementById('addCategoryOptionFile').checked) {
            optionfile = 1;
          }
          if(document.getElementById('addCategoryOptionVDP').checked) {
            optionvdp = 1;
          }
          $.ajax({
            type: 'post',
            url: 'user/add-category-option.php',
            data: {category_id: categoryid, option_name: optionname, option_file: optionfile, option_vdp: optionvdp},
            success: function (data) {
              if(data == "error"){
                $("#error-alert").alert();
                $("#error-alert").fadeTo(2000, 500).slideUp(500, function(){
                  $("#error-alert").slideUp(500);
                });
                console.log("error adding category option");
              }
              if(data == "success"){
                $("#success-alert").alert();
                $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
                  $("#success-alert").slideUp(500);
                  window.location.reload();
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
        }

        function updateCategoryDescription (categoryid) {
          var categorydescription = $('#categoryDescriptionField').val();
          $.ajax({
            type: 'post',
            url: 'user/update-category-description.php',
            data: {category_id: categoryid, category_description: categorydescription},
            success: function (data) {
              if(data == "error"){
                $("#error-alert").alert();
                $("#error-alert").fadeTo(2000, 500).slideUp(500, function(){
                  $("#error-alert").slideUp(500);
                });
                console.log("error changing category description");
              }
              if(data == "success"){
                $("#success-alert").alert();
                $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
                  $("#success-alert").slideUp(500);
                  window.location.reload();
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
        }

        $('.addOptionValueForm').on('submit', function(e){
          e.preventDefault();
          $.ajax({
            type: 'post',
            url: 'user/add-option-value.php',
            data: $(this).serialize(),
            success: function (data) {
              if(data == "error"){
                $("#error-alert").alert();
                $("#error-alert").fadeTo(2000, 500).slideUp(500, function(){
                  $("#error-alert").slideUp(500);
                });
                console.log("error adding option value");
              }
              if(data == "success"){
                $("#success-alert").alert();
                $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
                  $("#success-alert").slideUp(500);
                  window.location.reload();
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

        $('#image-form').on('submit', function (e) {
          e.preventDefault();
          $.ajax({
            type: 'post',
            url: 'user/update-category-image.php',
            data: $('#image-form').serialize(),
            success: function (data) {
              if(data == "error"){
                $("#error-alert").alert();
                $("#error-alert").fadeTo(2000, 500).slideUp(500, function(){
                  $("#error-alert").slideUp(500);
                });
                console.log("error changing category image");
              }
              if(data == "success"){
                $("#success-alert").alert();
                $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
                  $("#success-alert").slideUp(500);
                  window.location.reload();
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

        function updateCategoryName (categoryid) {
          var categoryname = $('#categoryNameField').val();
          $.ajax({
            type: 'post',
            url: 'user/update-category-name.php',
            data: {category_id: categoryid, category_name: categoryname},
            success: function (data) {
              if(data == "error"){
                $("#error-alert").alert();
                $("#error-alert").fadeTo(2000, 500).slideUp(500, function(){
                  $("#error-alert").slideUp(500);
                });
                console.log("error changing category name");
              }
              if(data == "success"){
                $("#success-alert").alert();
                $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
                  $("#success-alert").slideUp(500);
                  window.location.reload();
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
        }

        function updateCategoryBasePrice (categoryid) {
          var categorybaseprice = $('#categoryBasePriceField').val();
          $.ajax({
            type: 'post',
            url: 'user/update-category-base-price.php',
            data: {category_id: categoryid, category_base_price: categorybaseprice},
            success: function (data) {
              if(data == "error"){
                $("#error-alert").alert();
                $("#error-alert").fadeTo(2000, 500).slideUp(500, function(){
                  $("#error-alert").slideUp(500);
                });
                console.log("error changing category base price");
              }
              if(data == "success"){
                $("#success-alert").alert();
                $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
                  $("#success-alert").slideUp(500);
                  window.location.reload();
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
        }

        function updateCategoryMinimumOrder (categoryid) {
          var categoryminimumorder = $('#categoryMinimumOrderField').val();
          $.ajax({
            type: 'post',
            url: 'user/update-category-minimum-order.php',
            data: {category_id: categoryid, category_minimum_order: categoryminimumorder},
            success: function (data) {
              if(data == "error"){
                $("#error-alert").alert();
                $("#error-alert").fadeTo(2000, 500).slideUp(500, function(){
                  $("#error-alert").slideUp(500);
                });
                console.log("error changing category minimum order");
              }
              if(data == "success"){
                $("#success-alert").alert();
                $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
                  $("#success-alert").slideUp(500);
                  window.location.reload();
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
        }

   </script>
</body>
</html>