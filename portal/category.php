
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


    <title>Viewing Category</title>

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
      $aCategory = GetAllCategoryInfo($_GET['id']);
    ?>
    <div class="container" id="body-container">
      <div class="row">
        <div class="col-12">
          <div class="alert alert-success" id="success-alert">
            <button type="button" class="close" data-dismiss="alert">x</button>
            <strong>Success! </strong> the item(s) have been added to your Shopping Cart.
          </div>
          <div class="alert alert-danger" id="error-alert">
            <button type="button" class="close" data-dismiss="alert">x</button>
            <strong>Error! </strong> There was a problem adding the item(s) to your Shopping Cart.
          </div>
          <ol class="breadcrumb my-3">
            <li class="breadcrumb-item"><a href="/"><i class="fa fa-home"></i></a></li>
            <li class="breadcrumb-item active"><?= $aCategory['category_name'] ?></li>
          </ol>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-3 mb-5 hidden-xs-down">
          <div class="list-group category-list">
          <?php $aCategories = GetPortalCategories(ExtractSubdomains($_SERVER['HTTP_HOST']));
            foreach($aCategories as $aCat) { ?>
              <a href="/category.php?id=<?= $aCat['id'] ?>" <?php if($_GET['id'] == $aCat['id']) { echo('class="list-group-item active"'); } else { echo('class="list-group-item"');}?>><?= $aCat['category_name'] ?></a>
            <?php } ?>
          </div>
        </div>
        <div class="col-sm-9">
          <h1 class="mb-3"><?= $aCategory['category_name'] ?></h1>
            <div class="row mb-5">
              <div class="col-sm-2"><img src="/assets/images/category/<?= $aCategory['category_image'] ?>" alt="<?= $aCategory['category_name'] ?>" title="<?= $aCategory['category_name'] ?>" class="img-thumbnail">
              </div>
              <div class="col-sm-10">
                <p><?= $aCategory['category_description'] ?></p>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <h2 class="my-3">Products in <?= $aCategory['category_name'] ?></h2>
              </div>
            </div>
            <div class="row">
              <?php $aProducts = GetCategoryProducts($_GET['id'], ExtractSubdomains($_SERVER['HTTP_HOST']));
                foreach($aProducts as $aProduct){ ?>
                <div class="col-md-3 col-sm-6 col-xs-12 mb-3">
                    <div class="card featured-card">
                        <div class="card-block">
                            <a href="/product.php?id=<?= $aProduct['id'] ?>"><img class="featured-image img-fluid" border="0" src="domains/<?php echo(ExtractSubdomains($_SERVER['HTTP_HOST'])); ?>/images/products/<?= $aProduct['file_name'] ?>" alt="<?= $aProduct['name'] ?>"></a>
                            <h4 class="featured-product-name mt-2"><a href="/product.php?id=<?= $aProduct['id'] ?>"><?= $aProduct['name'] ?></a></h4>
                            <p><?php echo(StringConcat($aProduct['description'], 100)); ?></p>
                            <p class="my-2 text-muted">$<?= money_format('%i', ($aProduct['price']*$aProduct['minimum_order'])) ?> for <?= $aProduct['minimum_order'] ?></p>
                            <p class="small-text">Category: <a class="my-2" href="/category.php?id=<?= $aProduct['category_id'] ?>"><?= $aProduct['category_name'] ?></a></p>
                        </div>
                        <div class="card-footer featured-footer text-muted">
                          <button class="btn btn-secondary feature-footer-cart-btn" onclick="productJump(<?= $aProduct['id'] ?>)"><i class="fa fa-shopping-cart"></i> <span class="hidden-md-down">View Details</span></button> 
                        </div>
                    </div>
                </div>
                 <?php } ?>
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
   
   <script> 

        $(document).ready(function() {
            $("#success-alert").hide();
            $("#error-alert").hide();
            // run test on initial page load
            resizeFeatured();

            // run test on resize of the window
            $(window).resize(resizeFeatured);
        });

        // Make all of the featured equal height
        function resizeFeatured() {
            var tallest = 0;
            $(".featured-card").each(function(i,e){
                var var_e = jQuery(e);
                tallest = (var_e.height() > tallest) ? var_e.height() : tallest;

            });
            $(".featured-card").height(tallest);
        }

        function productJump(productid) {
            $(location).attr('href', ('product.php?id='+productid));
        }

        <?php foreach($aProducts as $aProduct) { ?>
            $("#<?= $aProduct['id'] ?>_form").on('submit', function (e) {
              e.preventDefault();    
              $("#success-alert").hide();
              $("#error-alert").hide();

              $.ajax({
                type: 'post',
                url: 'assets/ajax/add_to_cart.php',
                data: $("#<?= $aProduct['id'] ?>_form").serialize(),
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
        
        <?php } ?>
   </script>
</body>
</html>