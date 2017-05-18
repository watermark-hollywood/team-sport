
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


    <title>Add New Product</title>

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
      $aCatInfo = GetAllCategoryInfo($_GET['category_id']);
    ?>
    <div class="container" id="body-container">
    <form id="addNewProductForm" action="/user/add-product.php" method="post">
      <input id="domain" name="domain" type="hidden" value="<?= ExtractSubdomains($_SERVER['HTTP_HOST']) ?>">
      <div class="row">
        <div class="col-12">
          <ol class="breadcrumb my-3">
            <li class="breadcrumb-item"><a href="/"><i class="fa fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="/category.php?id=<?= $_GET['category_id'] ?>"><?= $aCatInfo['category_name'] ?></a></li>
            <li class="breadcrumb-item active">Add Product</li>
          </ol>

          <div class="alert alert-success" id="success-alert">
              <button type="button" class="close" data-dismiss="alert">x</button>
              <strong>Success! </strong> The product has been added to your store.
          </div>
          <div class="alert alert-danger" id="error-alert">
              <button type="button" class="close" data-dismiss="alert">x</button>
              <strong>Error! </strong> There was a problem adding the product to your store.
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-8 mb-5">
          <ul class="thumbnails">
            <li>
              <input type="hidden" name="category_id" value="<?= $_GET['category_id'] ?>">
              <div id="imageloader" orakuploader="on"></div>
            </li>
          </ul>
          <div class="row">
            <div class="col-12">
              <h4 class="mt-5 mb-3">Description</h4>
              <textarea class="form-control" id="product_description" rows="5" name="product_description" placeholder="add product description" required></textarea> 
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <h4 class="mt-5 mb-3">Available Options:</h4>
            </div>
          </div>
          <div class="row">
            <?php $aCatOptions = GetCategoryOptions($_GET['category_id']); 
              foreach($aCatOptions as $aCatOption){ 
                $aValues = explode(",", $aCatOption['option_values']);
                $aPrices = explode(",", $aCatOption['option_prices']);?>
            <div class="col-sm-6 mb-3">
              <div class="card featured-card">
                <div class="card-block" name="optionCard">
                  <h4><?= $aCatOption['option_name'] ?></h4>
                  <div class="row">
                    <div class="col-sm-6">
                      <label class="custom-control custom-radio" >
                        <input id="adminRadio<?= $aCatOption['id']?>" name="OptionID<?= $aCatOption['id']?>" type="radio" class="custom-control-input" value="admin" checked>
                        <span class="custom-control-indicator" ></span>
                        <span class="custom-control-description">Admin Selects</span>
                      </label>
                    </div>
                    <div class="col-sm-6">
                      <label class="custom-control custom-radio">
                        <input id="userRadio<?= $aCatOption['id']?>" name="OptionID<?= $aCatOption['id']?>" type="radio" class="custom-control-input" value="user">
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
                            <input type="radio" class="form-check-input" name="option[<?= $aCatOption['id'] ?>][]" id="<?= $aCatOption['id'] ?>" value="admin:<?= $aCatOption['option_name'] ?>:<?= trim($aValue) ?>:<?php if($aPrices[$i]){
                                            echo(trim($aPrices[$i]));
                                          } else {
                                            echo('0.00');
                                          }
                                          ?>:<?= $aCatOption['option_file'] ?>:<?= $aCatOption['option_vdp'] ?>:<?= $aCatOption['id'] ?>">
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
                          <input type="checkbox" class="form-check-input" name="option[<?= $aCatOption['id'] ?>][]" value="user:<?= $aCatOption['option_name'] ?>:<?= trim($aValue) ?>:<?php if($aPrices[$i]){
                                            echo(trim($aPrices[$i]));
                                          } else {
                                            echo('0.00');
                                          }
                                          ?>:<?= $aCatOption['option_file'] ?>:<?= $aCatOption['option_vdp'] ?>:<?= $aCatOption['id'] ?>">
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
                </div>
              </div>
            </div>
            <?php } ?>
          </div>
          
        </div>
        <div class="col-sm-4">
            <input type="text" class="form-control mb-3" id="product_name" name="product_name" placeholder="Product Name" required>
            Product Code:  <input type="text" name="product_code" id="product_code" placeholder="product code"> </br>
            Availability: <?= $aCatInfo['category_availability'] ?></p>
            <hr />
            <?php $price = $aCatInfo['category_base_price']; ?>
            <h3 class="price my-3">Price: $<?= money_format('%i', ($price*$aCatInfo['category_minimum_order'])) ?> <span class="per-unit">for <?= $aCatInfo['category_minimum_order'] ?></span></h3>  
            <div class="alert alert-info" role="alert" id="info-alert">
              <i class="fa fa-info-circle"></i> <input type="text" name="important_info" id="important_info" value="Each order is <?= $aCatInfo['category_minimum_order'] ?> pieces".>
            </div>
            <div class="form-check">
              <label class="form-check-label">
                <input type="checkbox" class="form-check-input" name="featured" value="1">
                Featured Product</label>
            </div>
            <button type="submit" class="btn btn-primary btn-lg btn-block mt-5 mb-3">Add New Product</button>
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
    var fieldCount = 0;
    $(document).ready (function(){

      $("#success-alert").hide();
      $("#error-alert").hide();


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

  $('#addNewProductForm').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
      type: 'post',
      url: 'user/add-product.php',
      data: $('#addNewProductForm').serialize(),
      success: function (data) {
        if(data == "error"){
          $("#error-alert").alert();
          $("#error-alert").fadeTo(2000, 500).slideUp(500, function(){
            $("#error-alert").slideUp(500);
          });
          console.log("error adding new product");
        } else {
          $("#success-alert").alert();
          $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
            $("#success-alert").slideUp(500);
            //$(location).attr('href', ('edit-product.php?id='+data));
          });
           console.log(data);
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