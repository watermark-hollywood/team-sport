<div class="container-fluid my-0" id="top">
  <div class="container clearfix pt-2">
    <div class="col-12 clearfix">
        <ul class="nav nav-bar pull-right">
          <li class="nav-item dropdown ml-0 mr-3">
            <?php if(isset($_SESSION['username'])){ ?>
               <a href="#" title="My Account" id="accountPopUp" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user"></i> <span class="hidden-xs hidden-sm hidden-md"><?= $_SESSION['username']?></span> <span class="caret"></span></a>
                  <div class="dropdown-menu mt-3" aria-labelledby="accountPopUp">
                    <a class="dropdown-item" href="#">Edit Account</a>
                    <a class="dropdown-item" href="/user/logout.php">Logout</a>
                  </div>
            <?php } else { ?>
              <a href="#" title="My Account" class="dropdown-toggle"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user"></i> <span class="hidden-xs hidden-sm hidden-md">My Account</span> <span class="caret"></span></a>
                <div class="dropdown-menu mt-3" aria-labelledby="accountPopUp">
                  <a class="dropdown-item" href="register.php">Register</a>
                  <a class="dropdown-item" href="login.php">Login</a>
                </div>
            <?php } ?>
          </li>
          <li  class="nav-item mx-3">
            <a href="#" id="wishlist-total" title="Wish List (0)"><i class="fa fa-heart"></i> <span class="hidden-md-down">Wish List</span> <?php if(isset($_SESSION['wish_list'])){ 
                    if(count($_SESSION['wish_list']) > 0) { ?> (<?= count($_SESSION['wish_list'])?>)
                    <?php } 
                } ?></a>
          </li>
          <li class="nav-item mx-3">
            <a href="/shopping-cart.php" title="Shopping Cart"><i class="fa fa-shopping-cart"></i> <span class="hidden-md-down">Shopping Cart</span> <span id="cart_contents">
            <?php if(isset($_SESSION['cart_contents'])){ 
                    if(count($_SESSION['cart_contents']) > 0) { ?> (<?= count($_SESSION['cart_contents'])?>)
                    <?php } 
                } ?></span></a>
          </li>
          <li class="nav-item ml-3 mr-0">
            <a href="/checkout.php" title="Checkout"><i class="fa fa-share"></i> <span class="hidden-md-down">Checkout</span></a>
          </li>
        </ul>
    </div>
  </div>
</div>
<div class="container-fluid my-3" id="header">
  <div class="container">
    <div class="row">
      <div class="col-6">
        <?php GetDomainLogo($_SERVER['HTTP_HOST']); ?>
      </div>
    </div>
  </div>
</div>
<div class="container-fluid py-0">
  <div class="container bg-primary topbar-container">
    <div class="row">
      <div class="col-12">
          <button id="menu-toggler" class="btn btn-primary hidden-lg-up" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fa fa-bars" aria-hidden="true"></i>
          </button>

          <ul id="primary-navigation" class="nav nav-pills hidden-md-down">
              <?php if(isset($_SESSION['type']) && ($_SESSION['type'] & 32) > 0) { ?>
              <li class="nav-item">
                <a class="nav-link <?php if($_SERVER['PHP_SELF'] == '/manage-platform.php') { echo('active'); } ?>" href="manage-platform.php">Manage Platform <span class="sr-only">(current)</span></a>
              </li>
              <?php } ?>
              <?php if(isset($_SESSION['type']) && (($_SESSION['type'] & 16) > 0 || ($_SESSION['type'] & 32) > 0)) { ?>
              <li class="nav-item">
                <a class="nav-link <?php if($_SERVER['PHP_SELF'] == '/manage-portal.php' || $_SERVER['PHP_SELF'] == '/manage-users.php') { echo('active'); } ?>" href="manage-portal.php">Manage Portal <span class="sr-only">(current)</span></a>
              </li>
              <?php } ?>
              <?php 
              $aCategories = GetNavCategories(ExtractSubdomains($_SERVER['HTTP_HOST']));
              for($i=0;$i<count($aCategories);$i++){
                if($i < 5){ ?>
                  <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="/category.php?id=<?= $aCategories[$i]['id'] ?>" id="<?= $aCategories[$i]['category_name'] ?>Dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= $aCategories[$i]['category_name'] ?><span class="caret"></span></a>
                    <div class="dropdown-menu" aria-labelledby="<?= $aCategories[$i]['category_name'] ?>Dropdown">
                    <?php $aProducts = GetNavProducts($aCategories[$i]['id'], ExtractSubdomains($_SERVER['HTTP_HOST']));
                    for($p=0;$p<count($aProducts);$p++){
                      if($p < 4){ ?>
                        <a class="dropdown-item" href="/product.php?id=<?= $aProducts[$p]['id'] ?>"><?= $aProducts[$p]['name'] ?></a>
                    <?php  }
                    } ?>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item all-items" href="/category.php?id=<?= $aCategories[$i]['id'] ?>">Show All <?= $aCategories[$i]['category_name'] ?></a>
                    </div>
                  </li>
              <?php } 
               ?>
              <?php } ?>
              <?php if(count($aCategories) > 5){ ?>
              <li class="nav-item">
                <a class="nav-link <?php if($_SERVER['PHP_SELF'] == '/category.php') { echo('active'); } ?>" href="/category.php">All Categories<span class="sr-only">(current)</span></a>
              </li>
              <?php }?>
              <li class="nav-item">
                <a class="nav-link <?php if($_SERVER['PHP_SELF'] == '/create-postcard.php') { echo('active'); } ?>" href="create-postcard.php">Create Postcard <span class="sr-only">(current)</span></a>
              </li>
          </ul>
      </div>
    </div>

    <div class="collapse navbar-collapse bg-primary hidden-lg-up" id="navbarSupportedContent">
      <ul id="primary-navigation-hamburger" class="nav nav-navbar flex-column">
          <?php if(isset($_SESSION['type']) && (($_SESSION['type'] & 16) > 0 || ($_SESSION['type'] & 32) > 0)) { ?>
          <li class="nav-item">
            <a class="nav-link active" href="manage-portal.php">Manage Portal <span class="sr-only">(current)</span></a>
          </li>
          <?php } ?>
          <?php if(isset($_SESSION['type']) && (($_SESSION['type'] & 16) > 0 || ($_SESSION['type'] & 32) > 0)){ ?>
          <li class="nav-item">
            <a class="nav-link" href="manage-users.php">Manage Users</a>
          </li>
          <?php } ?>
          <?php if(isset($_SESSION['type']) && ($_SESSION['type'] & 16) > 0 ){ ?>
          <li class="nav-item">
            <a class="nav-link" href="manage-products.php">Manage Products</a>
          </li>
          <?php } ?>
          <?php if(isset($_SESSION['type']) && ($_SESSION['type'] & 32) > 0 ){ ?>
          <li class="nav-item">
            <a class="nav-link" href="manage-platform.php">Manage Platform</a>
          </li>
          <?php } ?>
          <?php 
            for($i=0;$i<count($aCategories);$i++){
            if($i < 4){ ?>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="/category.php?id=<?= $aCategories[$i]['id'] ?>" id="<?= $aCategories[$i]['category_name'] ?>Dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= $aCategories[$i]['category_name'] ?><span class="caret"></span></a>
                <div class="dropdown-menu dropdown-menu-mobile" aria-labelledby="<?= $aCategories[$i]['category_name'] ?>Dropdown">
                <?php $aProducts = GetNavProducts($aCategories[$i]['id'], ExtractSubdomains($_SERVER['HTTP_HOST']));
                for($p=0;$p<count($aProducts);$p++){
                  if($p < 4){ ?>
                    <a class="dropdown-item" href="/product.php?id=<?= $aProducts[$p]['id'] ?>"><?= $aProducts[$p]['name'] ?></a>
                <?php  }
                } ?>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item all-items" href="/category.php?id=<?= $aCategories[$i]['id'] ?>">Show All <?= $aCategories[$i]['category_name'] ?></a>
                </div>
              </li>
          <?php } 
           ?>
          <?php if(count($aCategories) > 4){ ?>
          <li class="nav-item">
            <a class="nav-link <?php if($_SERVER['PHP_SELF'] == '/category.php') { echo('active'); } ?>" href="/category.php">All Categories<span class="sr-only">(current)</span></a>
          </li>
          <?php } }?>

          <li class="nav-item">
            <a class="nav-link" href="create-postcard.php">Create Postcard</a>
          </li>
      </ul>
    </div>
  </div>
</div>
<?php if($_SERVER['PHP_SELF'] == '/manage-portal.php' || $_SERVER['PHP_SELF'] == '/manage-users.php' || $_SERVER['PHP_SELF'] == '/manage-products.php') { ?>
<div class="container-fluid hidden-md-down  mt-3 mb-4" id="manage_submenu">
  <div class="container">
    <div class="row">
      <div class="col-12 px-0">
        <ul id="portal-tabs" class="nav nav-tabs">
          <?php if(isset($_SESSION['type']) &&(($_SESSION['type'] & 16) > 0 || ($_SESSION['type'] & 32) > 0)){ ?>
            <li class="nav-item">
              <a class="nav-link <?php if($_SERVER['PHP_SELF'] == '/manage-portal.php') { echo('active'); } ?>" href="manage-portal.php">Manage Styles <span class="sr-only">(current)</span></a>
            </li>
          <?php } ?>
          <?php if(isset($_SESSION['type']) &&(($_SESSION['type'] & 16) > 0 || ($_SESSION['type'] & 32) > 0)) { ?>
          <li class="nav-item">
            <a class="nav-link <?php if($_SERVER['PHP_SELF'] == '/manage-users.php') { echo('active'); } ?>" href="manage-users.php">Manage Users <span class="sr-only">(current)</span></a>
          </li>
          <?php } ?>
          <?php if(isset($_SESSION['type']) &&(($_SESSION['type'] & 16) > 0 || ($_SESSION['type'] & 32) > 0)) { ?>
          <li class="nav-item">
            <a class="nav-link <?php if($_SERVER['PHP_SELF'] == '/manage-products.php') { echo('active'); } ?>" href="manage-products.php">Manage Products <span class="sr-only">(current)</span></a>
          </li>
          <?php } ?>
        </ul>
      </div>
    </div>
  </div>
</div>
<?php } ?>
<?php if($_SERVER['PHP_SELF'] == '/manage-platform.php' || $_SERVER['PHP_SELF'] == '/admin-products.php' || $_SERVER['PHP_SELF'] == '/admin-categories.php' || $_SERVER['PHP_SELF'] == '/admin-promotions.php') { ?>
<div class="container-fluid hidden-md-down mt-3 mb-4" id="manage_submenu">
  <div class="container">
    <div class="row">
      <div class="col-12 px-0">
        <ul id="portal-tabs" class="nav nav-tabs">
          <?php if(isset($_SESSION['type']) && ($_SESSION['type'] & 32) > 0){ ?>
            <li class="nav-item">
              <a class="nav-link <?php if($_SERVER['PHP_SELF'] == '/manage-platform.php') { echo('active'); } ?>" href="manage-platform.php">Manage Users <span class="sr-only">(current)</span></a>
            </li>
          <?php } ?>
          <?php if(isset($_SESSION['type']) && ($_SESSION['type'] & 32) > 0) { ?>
          <li class="nav-item">
            <a class="nav-link <?php if($_SERVER['PHP_SELF'] == '/admin-categories.php') { echo('active'); } ?>" href="admin-categories.php">Manage Product Categories <span class="sr-only">(current)</span></a>
          </li>
          <?php } ?>
          <?php if(isset($_SESSION['type']) && ($_SESSION['type'] & 32) > 0) { ?>
          <li class="nav-item">
            <a class="nav-link <?php if($_SERVER['PHP_SELF'] == '/admin-promotions.php') { echo('active'); } ?>" href="admin-promotions.php">Manage Platform Promotions<span class="sr-only">(current)</span></a>
          </li>
          <?php } ?>
        </ul>
      </div>
    </div>
  </div>
</div>
<?php } ?>