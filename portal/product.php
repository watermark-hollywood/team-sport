
<?php
    require_once ('includes/managesessions.php'); 
    require_once ('includes/swdb_connect.php'); 
    require_once ('includes/utilityfunctions.php'); 

    ValidateDomain($_SERVER['HTTP_HOST']);
?>

<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en"> <!--<![endif]-->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">


    <title>Viewing Product</title>

    <!-- domo arigato mr roboto.. load this font -->
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500' rel='stylesheet' type='text/css'>

    <!-- BEGIN Load Styles for Plugins -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css" >


    <!-- page styles -->
    <link rel="stylesheet" href="assets/css/home.css" />
    <link rel="stylesheet" href="domains/<?= ExtractSubdomains($_SERVER['HTTP_HOST']) ?>/css/portal.css" /> 
    
</head>

<body>

    <?php 
      include('includes/topbar.php'); 
      $aProduct = GetAllProductInfo($_GET['id']);
    ?>
    <div class="container" id="body-container">
      <div class="row">
        <div class="col-12">
          <ol class="breadcrumb my-3">
            <li class="breadcrumb-item"><a href="/"><i class="fa fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="/category.php?id=<?= $aProduct['category_id'] ?>"><?= $aProduct['category_name'] ?></a></li>
            <li class="breadcrumb-item active"><?= $aProduct['name'] ?></li>
          </ol>

          <div class="alert alert-success" id="success-alert">
              <button type="button" class="close" data-dismiss="alert">x</button>
              <strong>Success! </strong> the item(s) have been added to your Shopping Cart.
          </div>
          <div class="alert alert-danger" id="error-alert">
              <button type="button" class="close" data-dismiss="alert">x</button>
              <strong>Error! </strong> There was a problem adding the item(s) to your Shopping Cart.
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-8 mb-5">
          <ul class="thumbnails">
            <?php $aImages = GetProductImages($_GET['id']); 
              for($i=0;$i<count($aImages);$i++) {
                if($i == 0) {?>
            <li>
            <?php } else { ?>
            <li class="image-additional">
            <?php } ?>
              <a class="thumbnail" href="/domains/<?= ExtractSubdomains($_SERVER['HTTP_HOST']) ?>/images/products/<?= $aImages[$i]['file_name']?>" title="<?= $aImages[$i]['name']?>">
                <img src="/domains/<?= ExtractSubdomains($_SERVER['HTTP_HOST']) ?>/images/products/<?= $aImages[$i]['file_name']?>" title="<?= $aImages[$i]['name']?>" alt="<?= $aImages[$i]['name']?>">
              </a>
            </li>
            <?php } ?>
          </ul>
          <div class="row">
            <div class="col-12">
                <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist" style="width:100%;">
              <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#description" role="tab">Description</a>
              </li>
              <?php $aSpecs = GetProductSpecifications($_GET['id']); 
              if(count($aSpecs) > 0) { ?>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#specifications" role="tab">Specifications</a>
              </li>
              <?php } ?>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#reviews" role="tab">Reviews</a>
              </li>
            </ul>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
            <!-- Tab panes -->
            <div class="tab-content">
              <div class="tab-pane active" id="description" role="tabpanel">
                <h4 class="my-3">Description</h4>
                <?= $aProduct['description'] ?>
              </div>
              <?php if(count($aSpecs) > 0) { ?>
              <div class="tab-pane" id="specifications" role="tabpanel">
                <h4 class="my-3">Specifications</h4>
                  <div class="list-group col-sm-8 offset-sm-2">
                  <?php foreach($aSpecs as $aSpec) { ?>
                    <div class="list-group-item spec-item">
                        <h6 class="list-group-item-heading"><?= $aSpec['spec_key'] ?></h6>
                        <p class="list-group-item-text"><?= $aSpec['spec_value'] ?></p>
                    </div>
                  <?php } ?>
                  </div>
              </div>
              <?php } ?>
              <div class="tab-pane" id="reviews" role="tabpanel">
                <h4 class="my-3">Reviews</h4>
                <p>No reviews yet, <a href="#">write a review</a></p>
              </div>
            </div>
            </div>
        </div>
        </div>
        <div class="col-sm-4">
          <div class="btn-group mb-3">
            <button type="button" data-toggle="tooltip" class="btn btn-default" title="" onclick="" data-original-title="Add to Wish List"><i class="fa fa-heart"></i></button>
            <button type="button" data-toggle="tooltip" class="btn btn-default" title="" onclick="" data-original-title="Compare this Product"><i class="fa fa-exchange"></i></button>
          </div>
          <form name="cart_form" id="cart_form" role="form" action="assets/ajax/add_to_cart.php" method="post">
            <h1 class="my=3"><?= $aProduct['name'] ?></h1>
            <input type="hidden" name="product_id" id="product_id" value="<?= $aProduct['id'] ?>">
            <input type="hidden" name="product_file_name" id="product_file_name" value="<?= $aImages[0]['file_name'] ?>">
            <input type="hidden" name="product_name" id="product_name" value="<?= $aProduct['name'] ?>">
            <p>
            <?php $aOptions = GetProductOptions($_GET['id']); ?>
            <?php for($i=0;$i<count($aOptions);$i++){ 
                  if( $aOptions[$i][0]['option_selects'] == "admin" && $aOptions[$i][0]['option_value'] != "None" ) {?>
            <?= $aOptions[$i][0]['option_key'] ?>: <?= $aOptions[$i][0]['option_value'] ?></br>
            <input type="hidden" name="option[<?= str_replace(' ', '_', $aOptions[$i][0]['option_key']) ?>]" value="<?= $aOptions[$i][0]['option_value'] ?>">

            <?php 
              if($aOptions[$i][0]['option_vdp'] > 0) { 
              $vdpFields = GetOptionVDPFields($_GET['id'], $aOptions[$i][0]['option_id']);
              for($p=0;$p<count($vdpFields);$p++){ ?>

                <div class="form-group row">
                    <label class="col-4 col-form-label"><?= $vdpFields[$p] ?>: </label>
                    <div class="col-8">
                        <input type="text" name="vdp[<?= str_replace(' ', '_', $vdpFields[$p]) ?>]" id="<?= str_replace(' ', '_', $vdpFields[$p]) ?>" class="form-control" required="true">
                    </div>
                </div>
            <?php } ?>
            <hr/>
            <?php } } } ?>
            Product Code: <?= $aProduct['product_code'] ?></br>
            <input type="hidden" name="product_code" id="product_code" value="<?= $aProduct['product_code'] ?>">
            Availability: <?= $aProduct['availability'] ?></p>
            <input type="hidden" name="product_availability" id="product_availability" value="<?= $aProduct['availability'] ?>">
            <h3 id="priceField" class="price my-3">$<?= money_format('%i', ($aProduct['price']*$aProduct['minimum_order'])) ?> <span class="per-unit">for <?= $aProduct['minimum_order'] ?> pieces.</span></h3>
            <input type="hidden" name="product_price" id="product_price" value="<?= money_format('%i', ($aProduct['price']*$aProduct['minimum_order'])) ?>">
            <input type="hidden" name="product_important_info" id="product_important_info" value="<?= $aProduct['important_info'] ?>">
              <?php $userOptions = []; 
                for($q=0;$q<count($aOptions);$q++){ 
                  if( $aOptions[$q][0]['option_selects'] == "user") {
                    array_push($userOptions, $aOptions[$q]);
                  }
                }
                if(count($userOptions) > 0) {?>
                  <hr />
                  <h4 class="my-3">Available Options:</h4>
              <?php }
                for($j=0;$j<count($userOptions);$j++){ ?>
                <?php if($userOptions[$j][0]['option_vdp'] > 0 || $userOptions[$j][0]['option_file'] > 0) { ?>
                  <h4><?= $userOptions[$j][0]['option_key'] ?></h4>
                  <div class="form-group row">
                  <label class="col-2 col-form-label">Choose:</label>
                  <?php } else { ?>
                  <div class="form-group row">
                  <label class="col-2 col-form-label"><?= $userOptions[$j][0]['option_key'] ?></label>
                  <?php } ?>
                  <div class="col-10">
                    <select name="option[<?= $userOptions[$i][0]['option_key'] ?>]" id="<?= $userOptions[$i][0]['option_key'] ?>" class="form-control" required="true">
                        <option value="">-- Select --</option>
                        <?php foreach ( $userOptions[$j] as $aOption ) : ?>
                        <option value="<?php echo ($aOption['option_value']); 
                                              if($aOption['option_price'] != "0.00") {
                                                echo(" (+".$aOption['option_price'].")");} ?>"><?php echo ($aOption['option_value']); 
                                              if($aOption['option_price'] != "0.00") {
                                                echo(" (+".$aOption['option_price'].")");} ?></option>
                        <?php endforeach; ?>
                    </select>
                  </div>
              </div>
              <?php if($userOptions[$j][0]['option_file'] > 0) { ?>
                <div class="form-group row">
                  <div class="col-12">
                    <div class="form-group">
                      <input type="file" name="img[<?= $aCatOption['option_name'] ?>]" class="file">
                      <div class="input-group col-xs-12">
                        <span class="input-group-addon"><i class="fa fa-file-image-o"></i></span>
                        <input type="text" class="form-control" name="image[<?= $aCatOption['option_name'] ?>]" disabled placeholder="Upload Design File">
                        <span class="input-group-btn">
                          <button class="browse btn btn-primary" type="button"><i class="fa fa-search"></i> Browse</button>
                        </span>
                      </div>
                    </div>
                  </div>
              </div>
              <?php } ?>
              <?php if($userOptions[$j][0]['option_vdp'] > 0) { ?>
                <div class="row" id="vdp_fields<?=$userOptions[$j][0]['option_id'] ?>">
                </div>
                <div class="row mb-3">
                  <div class="col-12 text-center">
                    <button class="btn btn-success" id="add_VDP_button<?=$userOptions[$j][0]['option_id'] ?>">Add VDP Field</button>
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
                <hr class="mb-5"/>
                <?php } ?>
              <?php } ?>
            <hr class="my-5" />
            <div class="form-group row">
                <label class="col-2 col-form-label">Quantity</label>
                <div class="col-10">
                    <input type="text" name="product_qty" id="product_qty" class="form-control" placeholder="Enter Quantity" value="1" required="true">
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-lg btn-block mt-5 mb-3">Add to Cart</button>
          </form>

          <?php if($aProduct['important_info'] != "") { ?>
            <div class="alert alert-info" role="alert" id="info-alert">
              <i class="fa fa-info-circle"></i> <?= $aProduct['important_info'] ?>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
    

   
    <?php include('includes/footer.php'); ?>
    <!-- common functions -->
    <script src="../assets/js/common.js"></script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
   
   <script> 
    $(document).ready (function(){
      $("#success-alert").hide();
      $("#error-alert").hide();
    });


    <?php for($i=0;$i<count($userOptions);$i++){ 
        if($userOptions[$i][0]['option_vdp'] > 0) { ?>
    $('#add_VDP_button<?=$userOptions[$i][0]['option_id'] ?>').on('click', function(e){
      e.preventDefault();
      $("<div class='col-12 vdpField'><div class='row'><div class='col-5 pr-1'><input type='text' class='form-control mb-3' name='vdp_field_name[<?=$userOptions[$i][0]['option_key'] ?>][]' placeholder='Field Name' style='width:100%;' required></div><div class='col-5 pl-1 pr-1'><input type='text' class='form-control mb-3' name='vdp_field_value[<?=$userOptions[$i][0]['option_key'] ?>][]' placeholder='Field Value' style='width:100%;' required></div><div class='col-2 pl-1'><button type='button' class='btn btn-danger mb-3' style='width:100%;'><i class='fa fa-trash'></i></button></div></div></div>").appendTo("#vdp_fields<?=$userOptions[$i][0]['option_id'] ?>");
    });
    <?php } } ?>

    $('#vdp_fields').on('click','button', function (e) {
      e.preventDefault();
      console.log(e.target.parentNode.parentNode.parentNode);
      e.target.parentNode.parentNode.parentNode.remove();
    });

    $('#cart_form').on('submit', function (e) {
      e.preventDefault();    
      $("#success-alert").hide();
      $("#error-alert").hide();

      $.ajax({
        type: 'post',
        url: 'assets/ajax/add_to_cart.php',
        data: $('#cart_form').serialize(),
        success: function (data) {
          if(data > 0){
            $("#success-alert").alert();
            $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
              $("#success-alert").slideUp(500);
            });
            $("#cart_contents").text('('+data+')');
          } else {
            $("#error-alert").alert();
            $("#error-alert").fadeTo(2000, 500).slideUp(500, function(){
              $("#error-alert").slideUp(500);
            });
          }
        },
        error: function (data) {
           alert("Message: " + data);
        }
      });

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