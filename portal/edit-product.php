
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


    <title>Edit Product</title>

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
      $aCatInfo = GetAllCategoryInfoFromProductID($_GET['id']);
      $aProdInfo = GetAllProductInfo($_GET['id']);
    ?>
    <div class="container" id="body-container">
      <input id="domain" name="domain" type="hidden" value="<?= ExtractSubdomains($_SERVER['HTTP_HOST']) ?>">
      <div class="row">
        <div class="col-12">
          <ol class="breadcrumb my-3">
            <li class="breadcrumb-item"><a href="/"><i class="fa fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="/manage-products.php">Manage Products</a></li>
            <li class="breadcrumb-item active">Edit Product</li>
          </ol>

          <div class="alert alert-success" id="success-alert">
              <button type="button" class="close" data-dismiss="alert">x</button>
              <strong>Success! </strong> The product has been updated.
          </div>
          <div class="alert alert-danger" id="error-alert">
              <button type="button" class="close" data-dismiss="alert">x</button>
              <strong>Error! </strong> There was a problem editing the product.
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-8 mb-5">
          <div class="row">
            <div class="col-12">
              <div id="showProductImage">
                <?php $aImages = GetProductImages($_GET['id']); ?>
                <ul class="thumbnails">
                  <?php for($q=0;$q<count($aImages);$q++) { 
                    if($q == 0) {?>
                  <li class="thumbnail">
                  <?php } else { ?>
                  <li class="image-additional">
                  <?php } ?>
                    <img src="/domains/<?= ExtractSubdomains($_SERVER['HTTP_HOST']) ?>/images/products/<?= $aImages[$q]['file_name'] ?>" alt="<?= $aImages[$q]['name'] ?>" title="<?= $aImages[$q]['name'] ?>" class="img-thumbnail">
                    <?php if($q == 0) { ?>
                      <button id="editImageButton" class='btn btn-sm btn-success' onclick="editImage()" ><i class='fa fa-edit'></i></button>
                    <?php } ?>
                  </li>
                  <?php } ?>
                </ul>
              </div>
              <div id="editProductImage">
                <form id="image-form" name="image-form" action="user/update-product-image.php" method="post">
                  <input type="hidden" name="product_id" value="<?= $_GET['id'] ?>">
                  <button type="submit" id="editImageButton" class='btn btn-success'>Update</button>
                  <div id="imageloader" orakuploader="on"></div>
                </form>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <h4 class="mt-5 mb-3">Description</h4>
              <div id="showProductDescription">
                <p><?= $aProdInfo['description'] ?> 
                <button id="editDescriptionButton" class='btn btn-sm btn-success' onclick="editDescription()" ><i class='fa fa-edit'></i></button></p>
              </div>
              <div id="editProductDescription">
                <div class="input-group">
                    <textarea id="productDescriptionField" class="form-control" rows="5" style="resize:none"><?= $aProdInfo['description'] ?></textarea>
                <span class="input-group-btn">
                  <button class="btn btn-success" type="button" onclick="updateProductDescription(<?= $_GET['id'] ?>)">Update</button>
                </span>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <h4 class="mt-5 mb-3">Available Options:</h4>
            </div>
          </div>
          <div class="row">
            <?php $aCatOptions = GetCategoryOptions($aCatInfo['id']); 
                  $aProdOptions = GetProductOptions($_GET['id']);

              foreach($aCatOptions as $aCatOption){ 
                $aValues = explode(",", $aCatOption['option_values']);
                $aPrices = explode(",", $aCatOption['option_prices']); 
                for($z=0;$z<count($aValues);$z++){
                  $aValues[$z] = trim($aValues[$z]);
                }
                ?>
            <div class="col-sm-6 mb-3">
            <form id="optionForm<?= $aCatOption['id']?>" action="/user/update-product-option.php" method="post">
              <div class="card featured-card">
                <div class="card-block" name="optionCard">
                  <h4><?= $aCatOption['option_name'] ?></h4>
                  <input type="hidden" name="option_id" value="<?= $aCatOption['id']?>">
                  <input type="hidden" name="product_id" value="<?= $_GET['id']?>">
                  <input type="hidden" name="option_file" value="<?= $aCatOption['option_file']?>">
                  <input type="hidden" name="option_vdp" value="<?= $aCatOption['option_vdp']?>">
                  <?php 
                    $thisOption = array();
                    foreach($aProdOptions as $aProdOption){
                      foreach($aProdOption as $thisProdOption){
                      if(in_array(trim($thisProdOption['option_value']), $aValues)){
                        array_push($thisOption, $thisProdOption);
                      }
                    } }?>
                  <div class="row" id="selectors<?= $aCatOption['id']?>">
                    <div class="col-sm-6">
                      <label class="custom-control custom-radio" >
                        <input id="adminRadio<?= $aCatOption['id']?>" name="OptionID<?= $aCatOption['id']?>" type="radio" class="custom-control-input" value="admin" <?php if($thisOption[0]['option_selects'] == "admin") {?> checked <?php } ?>>
                        <span class="custom-control-indicator" ></span>
                        <span class="custom-control-description">Admin Selects</span>
                      </label>
                    </div>
                    <div class="col-sm-6">
                      <label class="custom-control custom-radio">
                        <input id="userRadio<?= $aCatOption['id']?>" name="OptionID<?= $aCatOption['id']?>" type="radio" class="custom-control-input" value="user" <?php if($thisOption[0]['option_selects'] == "user") {?> checked <?php } ?>>
                        <span class="custom-control-indicator"></span>
                        <span class="custom-control-description">Customer Selects</span>
                      </label>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">
                      <fieldset class="form-group">
                        <?php $i=0; foreach($aValues as $aValue) { ?>
                        <div class="form-check">
                          <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="adminoption[<?= $aCatOption['id'] ?>][]" id="<?= $aCatOption['id'] ?>" value="admin:<?= $aCatOption['option_name'] ?>:<?= trim($aValue) ?>:<?php if($aPrices[$i]){
                                            echo(trim($aPrices[$i]));
                                          } else {
                                            echo('0.00');
                                          }
                                          ?>:<?= $aCatOption['option_file'] ?>:<?= $aCatOption['option_vdp'] ?>:<?= $aCatOption['id'] ?>"
                                          <?php if($thisOption[0]['option_selects'] == "user") {?> disabled <?php } ?>
                                          <?php for($y=0;$y<count($thisOption);$y++){
                                              if($thisOption[$y]['option_value'] == trim($aValue) && $thisOption[$y]['option_selects'] == "admin") {?> checked <?php } }?>>
                            <?= trim($aValue) ?> <?php if($aPrices[$i]){
                                            echo(trim($aPrices[$i]));
                                          } else {
                                            echo('0.00');
                                          }
                                          ?>
                          </label>
                        </div>
                        <?php $i++; } ?>
                    </div>
                    <div class="col-sm-6">
                      <?php $i=0; foreach($aValues as $aValue) { ?>
                      <div class="form-check">
                        <label class="form-check-label">
                          <input type="checkbox" class="form-check-input" name="useroption[<?= $aCatOption['id'] ?>][]" id="<?= $aCatOption['id'] ?>" value="user:<?= $aCatOption['option_name'] ?>:<?= trim($aValue) ?>:<?php if($aPrices[$i]){
                                            echo(trim($aPrices[$i]));
                                          } else {
                                            echo('0.00');
                                          }
                                          ?>:<?= $aCatOption['option_file'] ?>:<?= $aCatOption['option_vdp'] ?>:<?= $aCatOption['id'] ?>" <?php if($thisOption[0]['option_selects'] == "admin") {?> disabled <?php } ?>
                                          <?php for($y=0;$y<count($thisOption);$y++){
                                              if($thisOption[$y]['option_value'] == trim($aValue) && $thisOption[$y]['option_selects'] == "user") {?> checked <?php } }?>>
                          <?= trim($aValue) ?> 
                          <?php if($aPrices[$i]){
                              echo('$'.trim($aPrices[$i]));
                            } else {
                              echo('-');
                            } 
                          ?>
                        </label>
                      </div>
                      <?php $i++; } ?>
                    </div>
                  </div>
                  <?php if($aCatOption['option_file'] > 0){ ?>
                  <div class="row">
                    <div class="col-12">
                      <div class="form-group">
                        <input type="file" name="img[<?= $aCatOption['option_name'] ?>]" class="file">
                        <div class="input-group col-xs-12">
                          <span class="input-group-addon"><i class="fa fa-file-image-o"></i></span>
                          <input type="text" class="form-control" name="image[<?= $aCatOption['option_name'] ?>]" disabled placeholder="Upload Image">
                          <span class="input-group-btn">
                            <button class="browse btn btn-primary" type="button"><i class="fa fa-search"></i> Browse</button>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php } ?>
                  <?php if($aCatOption['option_vdp'] > 0) { ?>
                    <div class="row">
                      <div class="col-12">
                        <hr/>
                        <h4 class="mb-3">Variable Data Printing</h4>
                      </div>
                    </div>
                    <div class="row" id="vdp_fields<?=$aCatOption['id']?>">
                    </div>
                    <div class="row mb-3">
                      <div class="col-12 text-center">
                        <button class="btn btn-success" id="add_VDP_button<?= $aCatOption['id'] ?>">Add VDP Field</button>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-12">
                        <div class="form-group">
                          <input type="file" name="img[<?= $aCatOption['option_name'] ?>]" class="file">
                          <div class="input-group col-xs-12">
                            <span class="input-group-addon"><i class="fa fa-file-image-o"></i></span>
                            <input type="text" class="form-control" name="image[<?= $aCatOption['option_name'] ?>]" disabled placeholder="Upload Plan File">
                            <span class="input-group-btn">
                              <button class="browse btn btn-primary" type="button"><i class="fa fa-search"></i> Browse</button>
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-12">
                        <div class="form-group">
                          <input type="file" name="img[<?= $aCatOption['option_name'] ?>]" class="file">
                          <div class="input-group col-xs-12">
                            <span class="input-group-addon"><i class="fa fa-file-image-o"></i></span>
                            <input type="text" class="form-control" name="image[<?= $aCatOption['option_name'] ?>]" disabled placeholder="Upload Data Source">
                            <span class="input-group-btn">
                              <button class="browse btn btn-primary" type="button"><i class="fa fa-search"></i> Browse</button>
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <?php } ?>

                    <div class="row">
                      <div class="col-12 text-center">
                        <button class="btn btn-success" type="submit">Update</button>
                      </div>
                    </div>
                </div>
              </div>
              </form>
            </div>
            <?php } ?>
          </div>
          
        </div>
        <div class="col-sm-4">
          <div id="showProductName"><h1 class="mb-3"><?= $aProdInfo['name'] ?>  <button class='btn btn-sm btn-success' onclick="editName()" ><i class='fa fa-edit'></i></button></h1></div>
          <div id="editProductName" class="row">
            <div class="col-12">
              <div class="input-group">
                <input type="text" class="form-control" id="productNameField" value="<?= $aProdInfo['name'] ?>">
                <span class="input-group-btn">
                  <button class="btn btn-secondary" type="button" onclick="updateProductName(<?= $_GET['id'] ?>)">Update</button>
                </span>
              </div>
            </div>
          </div>

          <div id="showProductCode">Product Code: <?= $aProdInfo['product_code'] ?>  <button class='btn btn-sm btn-success' onclick="editCode()" ><i class='fa fa-edit'></i></button></h1></div>
          <div id="editProductCode" class="row">
            <div class="col-12">
              <div class="input-group">
                <input type="text" class="form-control" id="productCodeField" value="<?= $aProdInfo['product_code'] ?>">
                <span class="input-group-btn">
                  <button class="btn btn-secondary" type="button" onclick="updateProductCode(<?= $_GET['id'] ?>)">Update</button>
                </span>
              </div>
            </div>
          </div>
            Availability: <?= $aCatInfo['category_availability'] ?> (category option)</p>
            <hr />
            <?php $price = $aCatInfo['category_base_price']; ?>
            <h3 class="price my-3">Price: $<?= money_format('%i', ($price*$aCatInfo['category_minimum_order'])) ?> <span class="per-unit">for <?= $aCatInfo['category_minimum_order'] ?> (category option)</span></h3>  
            <div id="showProductAlert">
              <div class="alert alert-info" role="alert" id="info-alert">
                <i class="fa fa-info-circle"></i> <?= $aProdInfo['important_info'] ?> <button class='btn btn-sm btn-success' onclick="editAlert()" ><i class='fa fa-edit'></i></button>
              </div>
            </div>
            <div id="editProductAlert">
              <div class="alert alert-info" role="alert" id="info-alert">
                <div class="input-group"><input type="text" class="form-control" name="productAlertField" id="productAlertField" value="<?= $aProdInfo['important_info'] ?>"> <span class="input-group-btn">
                  <button class="btn btn-secondary" type="button" onclick="updateProductAlert(<?= $_GET['id'] ?>)">Update</button>
                </span>
              </div>
            </div>
          </div>
          <div class="form-check">
            <label class="form-check-label">
              <input type="checkbox" id="featuredProductCheckbox" class="form-check-input" name="featured" value="1" <?php if($aProdInfo['featured'] > 0) { echo("checked"); } ?>>
              Featured Product</label>
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
    <?php foreach($aCatOptions as $aCatOption){ ?>
      $("input[name='OptionID<?= $aCatOption['id']?>']").change(function(e){
        if ($(this).val() === 'admin'){
          $("input[name='useroption[<?= $aCatOption['id'] ?>][]']").prop('checked', false);
          $("input[name='useroption[<?= $aCatOption['id'] ?>][]']").prop('disabled', true);
          $("input[name='adminoption[<?= $aCatOption['id'] ?>][]']").prop('disabled', false);
        } else {
          $("input[name='adminoption[<?= $aCatOption['id'] ?>][]']").prop('checked', false);
          $("input[name='adminoption[<?= $aCatOption['id'] ?>][]']").prop('disabled', true);
          $("input[name='useroption[<?= $aCatOption['id'] ?>][]']").prop('disabled', false);
        }
      });


    $('#optionForm<?= $aCatOption['id']?>').on('submit', function (e) {
      e.preventDefault();
      $.ajax({
        type: 'post',
        url: 'user/update-product-option.php',
        data: $('#optionForm<?= $aCatOption['id']?>').serialize(),
        success: function (data) {
          if(data == "error"){
            $("#error-alert").alert();
            $("#error-alert").fadeTo(2000, 500).slideUp(500, function(){
              $("#error-alert").slideUp(500);
            });
            console.log("error changing product image(s)");
          }
          if(data == "success"){
            $("#success-alert").alert();
            $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
              $("#success-alert").slideUp(500);
              //window.location.reload();
            });
             console.log("product updated");
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

    <?php } ?>



    $('#featuredProductCheckbox').change(function(){
      var c = this.checked ? '1' : '0';
      $.ajax({
        type: 'post',
        url: 'user/update-product-featured.php',
        data: {product_id: <?= $_GET['id'] ?>, featured: c},
        success: function (data) {
          if(data == "error"){
            $("#error-alert").alert();
            $("#error-alert").fadeTo(2000, 500).slideUp(500, function(){
              $("#error-alert").slideUp(500);
            });
            console.log("error changing product featured");
          }
          if(data == "success"){
            $("#success-alert").alert();
            $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
              $("#success-alert").slideUp(500);
              window.location.reload();
            });
             console.log("product updated");
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

    function editName() {
      $("#showProductName").hide();
      $("#editProductName").show();
    }

    function updateProductName (productid) {
      var productname = $('#productNameField').val();
      $.ajax({
        type: 'post',
        url: 'user/update-product-name.php',
        data: {product_id: productid, product_name: productname},
        success: function (data) {
          if(data == "error"){
            $("#error-alert").alert();
            $("#error-alert").fadeTo(2000, 500).slideUp(500, function(){
              $("#error-alert").slideUp(500);
            });
            console.log("error changing product name");
          }
          if(data == "success"){
            $("#success-alert").alert();
            $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
              $("#success-alert").slideUp(500);
              window.location.reload();
            });
             console.log("product updated");
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

    function editCode() {
      $("#showProductCode").hide();
      $("#editProductCode").show();
    }

    function updateProductCode (productid) {
      var productcode = $('#productCodeField').val();
      $.ajax({
        type: 'post',
        url: 'user/update-product-code.php',
        data: {product_id: productid, product_code: productcode},
        success: function (data) {
          if(data == "error"){
            $("#error-alert").alert();
            $("#error-alert").fadeTo(2000, 500).slideUp(500, function(){
              $("#error-alert").slideUp(500);
            });
            console.log("error changing product code");
          }
          if(data == "success"){
            $("#success-alert").alert();
            $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
              $("#success-alert").slideUp(500);
              window.location.reload();
            });
             console.log("product updated");
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

    function editAlert() {
      $("#showProductAlert").hide();
      $("#editProductAlert").show();
    }

    function updateProductAlert (productid) {
      var productalert = $('#productAlertField').val();
      $.ajax({
        type: 'post',
        url: 'user/update-product-alert.php',
        data: {product_id: productid, product_alert: productalert},
        success: function (data) {
          if(data == "error"){
            $("#error-alert").alert();
            $("#error-alert").fadeTo(2000, 500).slideUp(500, function(){
              $("#error-alert").slideUp(500);
            });
            console.log("error changing product alert");
          }
          if(data == "success"){
            $("#success-alert").alert();
            $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
              $("#success-alert").slideUp(500);
              window.location.reload();
            });
             console.log("product updated");
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

    function editImage() {
      $("#showProductImage").hide();
      $("#editProductImage").show();
    }

    function editDescription() {
      $("#showProductDescription").hide();
      $("#editProductDescription").show();
    }



    function updateProductDescription (productid) {
      var productdescription = $('#productDescriptionField').val();
      $.ajax({
        type: 'post',
        url: 'user/update-product-description.php',
        data: {product_id: productid, product_description: productdescription},
        success: function (data) {
          if(data == "error"){
            $("#error-alert").alert();
            $("#error-alert").fadeTo(2000, 500).slideUp(500, function(){
              $("#error-alert").slideUp(500);
            });
            console.log("error changing product description");
          }
          if(data == "success"){
            $("#success-alert").alert();
            $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
              $("#success-alert").slideUp(500);
              window.location.reload();
            });
             console.log("product updated");
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

    var fieldCount = 0;
    $(document).ready (function(){

      $("#success-alert").hide();
      $("#error-alert").hide();
      $("#editProductName").hide();
      $("#editProductCode").hide();
      $("#editProductAlert").hide();
      $("#editProductImage").hide();
      $("#editProductDescription").hide();


      $('#imageloader').orakuploader({
        orakuploader_path : "assets/plugins/orakuploader/",
        
        orakuploader_main_path : "/domains/<?= ExtractSubdomains($_SERVER['HTTP_HOST']) ?>/images/products",
        orakuploader_thumbnail_path : "/domains/<?= ExtractSubdomains($_SERVER['HTTP_HOST']) ?>/images/products/tn",
        
        orakuploader_add_image : 'assets/plugins/orakuploader/images/add.png',
        orakuploader_add_label : 'Add up to 5 images',
        
        orakuploader_resize_to : 600,
        orakuploader_thumbnail_size : 300,
        
        orakuploader_maximum_uploads : 5,
        orakuploader_hide_on_exceed : true,
        
        orakuploader_max_exceeded : function() {
          alert("You are limited to 5 images per product.");
        }
        
      });
  });
  <?php $aCatOptions = GetCategoryOptions($_GET['category_id']); 
    foreach($aCatOptions as $aCatOption){
      if($aCatOption['option_vdp'] > 0) { ?>
  $('#add_VDP_button<?=$aCatOption['id']?>').on('click', function(e){
    e.preventDefault();
    var btn = e.target;
    $("<div class='col-12 vdpField'><div class='row'><div class='col-9 pr-1'><input type='text' class='form-control mb-3'"+
      " name='vdp_field[<?=$aCatOption['id']?>][]' placeholder='VDP Field Name' style='width:100%;' required></div><div class='col-3 pl-1'><button type='button' class='btn btn-danger mb-3' style='width:100%;'>Delete</button></div></div></div>").appendTo("#vdp_fields<?=$aCatOption['id']?>");
      fieldCount++;
  });
  <?php } }  ?>

  $('#vdp_fields').on('click','button', function (e) {
    e.preventDefault();
    console.log(e.target.parentNode.parentNode.parentNode);
    e.target.parentNode.parentNode.parentNode.remove();
  });


    $('#image-form').on('submit', function (e) {
      e.preventDefault();
      $.ajax({
        type: 'post',
        url: 'user/update-product-images.php',
        data: $('#image-form').serialize(),
        success: function (data) {
          if(data == "error"){
            $("#error-alert").alert();
            $("#error-alert").fadeTo(2000, 500).slideUp(500, function(){
              $("#error-alert").slideUp(500);
            });
            console.log("error changing product image(s)");
          }
          if(data == "success"){
            $("#success-alert").alert();
            $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
              $("#success-alert").slideUp(500);
              window.location.reload();
            });
             console.log("product updated");
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


    $(document).on('click', '.browse', function(){
      var file = $(this).parent().parent().parent().find('.file');
      file.trigger('click');
    });
    $(document).on('change', '.file', function(){
      $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
    });
   </script>
</body>
</html>