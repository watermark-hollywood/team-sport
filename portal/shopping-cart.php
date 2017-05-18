
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


    <title>Shopping Cart</title>

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
    ?>
    <div class="container" id="body-container">
      <div class="row">
        <div class="col-12">
          <ol class="breadcrumb my-3">
            <li class="breadcrumb-item"><a href="/"><i class="fa fa-home"></i></a></li>
            <li class="breadcrumb-item active">Shopping Cart</li>
          </ol>

          <div class="alert alert-success" id="success-alert">
              <button type="button" class="close" data-dismiss="alert">x</button>
              <strong>Success! </strong> Your Shopping Cart has been updated.
          </div>
          <div class="alert alert-danger" id="error-alert">
              <button type="button" class="close" data-dismiss="alert">x</button>
              <strong>Error! </strong> There was a problem updating your Shopping Cart.
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <h1 class="mt-3 mb-3">Shopping Cart</h1>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <td class="text-center hidden-xs-down">Image</td>
                  <td class="text-left">Product Name</td>
                  <td class="text-left">Product Options</td>
                  <td class="text-left hidden-xs-down">Availability</td>
                  <td class="text-left">Quantity</td>
                  <td class="text-right hidden-xs-down">Unit Price</td>
                  <td class="text-right">Total</td>
                </tr>
              </thead>
              <tbody>
                <?php
                  $i=0; 
                  foreach($_SESSION['cart_contents'] as $aProduct) { ?>

                <!-- debugging 
                <tr>
                  <td colspan="6"><pre><? /*php var_dump($aProduct); */?></pre></td>
                </tr> -->
                <!-- /debugging -->
                <tr>
                  <td class="text-center hidden-xs-down"> 
                    <div class="image-additional">
                      <a class="thumbnail mb-0" href="/product.php?id=<?= $aProduct['product_id'] ?>">
                        <img src="domains/<?php echo(ExtractSubdomains($_SERVER['HTTP_HOST'])); ?>/images/products/<?= $aProduct['product_file_name'] ?>" alt="<?= $aProduct['product_name'] ?>" title="<?= $aProduct['product_name'] ?>">
                      </a>
                    </div>
                  </td>
                  <td class="text-left"><a href="/product.php?id=<?= $aProduct['product_id'] ?>"><?= $aProduct['product_name'] ?></a></td>
                  <td class="text-left">
                    <?php 
                        $options = json_decode($aProduct['option']);
                        foreach($options as $key => $value) {
                         echo(str_replace('_',' ', $key).': '.$value.'<br/>'); 
                          }?>
                    <?php if(isset($aProduct['vdp'])) { ?>
                      <hr/>
                      VDP Fields:<br/>
                      <?php $vdpFields = json_decode($aProduct['vdp']);
                        foreach($vdpFields as $key => $value) {
                         echo(str_replace('_',' ', $key).': '.$value.'<br/>'); 
                        }
                      } ?>
                  </td>
                  <td class="text-left hidden-xs-down"><?= $aProduct['product_availability'] ?></td>
                  <td class="text-left">
                    <form id="<?= $i ?>_qty_form" name="<?= $i ?>_qty_form" role="form" action="assets/ajax/update_cart_qty.php" method="post">
                      <input type="hidden" name="cart_index" id="cart_index" value="<?= $i ?>">
                      <div class="input-group" style="max-width: 200px;">
                        <input name="cart_qty" id="cart_qty" value="<?= $aProduct['product_qty'] ?>" size="1" class="form-control" type="text">
                        <span class="input-group-btn">
                          <button type="submit" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="Update"><i class="fa fa-refresh"></i></button>
                        </span>
                        <span class="input-group-btn">
                          <button type="button" data-toggle="tooltip" title="" class="btn btn-danger" onclick="cartRemove(<?= $i ?>)" data-original-title="Remove"><i class="fa fa-times-circle"></i></button>
                        </span>
                      </div>
                    </form>
                    <?php if($aProduct['product_important_info']) { ?>
                      <div class="alert alert-info mt-3" id="info-alert">
                          <i class="fa fa-info-circle"></i> <?= $aProduct['product_important_info'] ?>
                      </div>
                    <?php } ?>
                  </td>
                  <td class="text-right hidden-xs-down">$<?= $aProduct['product_price'] ?></td>
                  <td class="text-right"><?php $rowtotal = ($aProduct['product_price'] * $aProduct['product_qty']);
                                             echo('$'.number_format((float)$rowtotal, 2, '.', ''));?></td>
                </tr>
                <?php $i++; } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-4 offset-sm-8">
          <table class="table table-bordered">
            <tbody>
              <tr>
                <td class="text-right"><strong>Sub-Total:</strong></td>
                <td class="text-right">
                  <?php $subtotal = 0;
                    foreach($_SESSION['cart_contents'] as $aProduct){
                      $subtotal = $subtotal + ($aProduct['product_price'] * $aProduct['product_qty']);
                    }
                    echo('$'.number_format((float)$subtotal, 2, '.', ''));
                  ?>
                </td>
              </tr>
              <tr>
                <td class="text-right"><strong>Shipping (UPS Ground):</strong></td>
                <td class="text-right"><?php $shipping = 15;
                                          echo('$'.number_format((float)$shipping, 2, '.', '')); ?>
                </td>
              </tr>
                <td class="text-right"><strong>Total:</strong></td>
                <td class="text-right"><?php $total = $subtotal + $shipping;
                                          echo('$'.number_format((float)$total, 2, '.', '')); ?>

                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <div class="pull-left"><a href="/" class="btn btn-default">Continue Shopping</a></div>
          <div class="pull-right"><a href="/checkout.php" class="btn btn-primary">Checkout</a></div>
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

      <?php if($_GET['updated']){ ?>
        $("#success-alert").alert();
        $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
          $("#success-alert").slideUp(500);
        });
      <?php } ?>

    });

    function cartRemove(i){
      $("#success-alert").hide();
      $("#error-alert").hide();
      var values = {
        'cart-index': i
      };
      $.ajax({
        type:'post',
        url: 'assets/ajax/delete_from_cart.php',
        data: values,
        success: function (data) {
          if(data == 'success'){
             var url = "shopping-cart.php?updated=true";
             $(location).attr('href',url);
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

    }


      <?php
        $i=0; 
        foreach($_SESSION['cart_contents'] as $aProduct) { ?>
          $('#<?= $i ?>_qty_form').on('submit', function (e) {
            e.preventDefault();    
            $("#success-alert").hide();
            $("#error-alert").hide();

            $.ajax({
              type: 'post',
              url: 'assets/ajax/update_cart_qty.php',
              data: $('#<?= $i ?>_qty_form').serialize(),
              success: function (data) {
                if(data == 'success'){
                   var url = "shopping-cart.php?updated=true";
                   $(location).attr('href',url);
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
      <?php $i++;
          } ?>
   </script>
</body>
</html>