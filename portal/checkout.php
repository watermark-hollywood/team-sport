<?php
require_once ('includes/managesessions.php'); 
require 'assets/libs/cartman/init.php';

ValidateDomain($_SERVER['HTTP_HOST']);

$total = '';
$invoice_action = '';
$allow_submit = true;

// do some error checking first
if (empty($config['name']) || empty($config['email'])) 
{
    $error = 'You need to enter your name and email in the <a href="admin.php#tab=settings" target="_blank">admin settings</a> area before you can accept payments.';
}

if ($config['enable_paypal'] && empty($config['paypal_email'])) 
{
    $error = 'You need to enter your PayPal email address in the <a href="admin.php#tab=settings" target="_blank">admin settings</a> area before you can accept PayPal payments.';
}

if (empty($config['stripe_secret_key']) || empty($config['stripe_publishable_key'])) 
{
    $error = 'You need to enter your Stripe API details in the <a href="admin.php#tab=settings" target="_blank">admin settings</a> area before you can accept credit card payments.';
}

if (isset($error)) 
{
    $allow_submit = false;
}
?>

<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en"> <!--<![endif]-->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">


    <title>Project-Oslo Checkout</title>

    <!-- domo arigato mr roboto.. load this font -->
    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500' rel='stylesheet' type='text/css'>

    <!-- BEGIN Load Styles for Plugins -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css" >


    <?php template('head'); ?>

    <!-- page styles -->
    <link rel="stylesheet" href="assets/css/home.css" />
    <link rel="stylesheet" href="domains/<?= ExtractSubdomains($_SERVER['HTTP_HOST']) ?>/css/portal.css" /> 


</head>

<body>
	<?php include('includes/topbar.php'); ?>

    <noscript>
        <div class="alert alert-danger mt20neg">
            <div class="container aligncenter">
                <strong>Oops!</strong> It looks like your browser doesn't have Javascript enabled.  Please enable Javascript to use this website.
            </div>
        </div>
    </noscript>
	
    <div class="container py-5" id="body-container">
        <div class="card">
            <div class="card-block">
        <?php template('message', false); ?>

        <?php if ( isset($error) ) : ?>
            <div class="alert alert-danger">
                <strong><i class="fa fa-exclamation-circle"></i> Oops!</strong><br>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ( $invoice_action == 'already paid' ) : ?>
            <div class="alert alert-success">
                <strong><i class="fa fa-check"></i> This invoice has already been paid!</strong><br>
                Payment for this invoice was received on <?php echo date('F jS, Y', strtotime($invoice->date_paid)); ?>.
            </div>
        <?php endif; ?>


        <form action="<?php echo url('assets/ajax/process.php'); ?>" method="post" class="validate form-horizontal <?php echo !$allow_submit ? 'disabled' : ''; ?>" id="order_form">
            <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
            <input type="hidden" name="action" value="process_payment">
            <input type="hidden" name="shipping_carrier" value="UPS Ground">
            <input type="hidden" name="invoice_id" value="<?php getGuid(); ?>">
            <input type="hidden" class="enable-subscriptions" value="<?php echo $config['enable_subscriptions']; ?>">
            <input type="hidden" class="publishable-key" value="<?php echo trim($config['stripe_publishable_key']); ?>">

            <div class="row">
                <div class="col-md-4 offset-md-2">

                  <h3 class="colorgray mb20">Invoice Details</h3>

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
                        <tr>
                          <td class="text-right"><strong>Total:</strong></td>
                          <td class="text-right"><?php $total = $subtotal + $shipping;
                                                  echo('$'.number_format((float)$total, 2, '.', '')); ?>
                          </td>
                        </tr>
                      </tbody>
                    </table>

                    <input type="hidden" name="total" value="<?php echo $total; ?>">
                    <input type="hidden" name="shipping_charge" value="<?= $shipping ?>">
                    <div class="text-center"><a href="shopping-cart.php" class="btn btn-default">Return to Shopping Cart</a></div>
                    <hr class="hidden-md-up">
                    <h3 class="colorgray mt40 mb20">Your Information</h3>
                    <div class="form-group row">
                        <label class="col-2 col-form-label"><span class="colordanger">*</span>Email</label>
                        <div class="col-10">
                            <input type="text" name="email" class="form-control" placeholder="Email" value="<?php echo isset($invoice) && $invoice ? $invoice->email : ''; ?>" required="true" data-rule-email="true">
                        </div>
                    </div>
                    <?php if ( $config['show_shipping_address'] ) : ?>
                    <hr class="hidden-md-up">
                    <h3 class="colorgray mt40 mb20">Billing Address</h3>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>Name</label>
                            <div class="col-10">
                                <input type="text" name="billing_name" class="form-control" placeholder="Name" value="" required="true">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>Address 1</label>
                            <div class="col-10">
                                <input type="text" name="billing_address1" class="form-control" placeholder="Address" value="" required="true">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Address 2</label>
                            <div class="col-10">
                                <input type="text" name="billing_address2" class="form-control" placeholder="Address" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>City</label>
                            <div class="col-10">
                                <input type="text" name="billing_city" class="form-control" placeholder="City" value="" required="true">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>State/Zip</label>
                            <div class="col-10">
                                <div class="row">
                                    <div class="col-8 pr5">
                                        <select name="billing_state" class="form-control" required="true">
                                            <option value="">-- Select State --</option>
                                            <?php foreach ( states() as $country_name => $states_arr ) : ?>
                                            <optgroup label="<?php echo $country_name; ?>">
                                                <?php foreach ( $states_arr as $state_code => $state_name ) : ?>
                                                <option value="<?php echo $state_code; ?>"><?php echo $state_name; ?></option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                            <?php endforeach; ?>
                                            <option value="N/A">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-4 pl5">
                                        <input type="text" name="billing_zip" class="form-control" placeholder="Zip" value="" required="true">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>Country</label>
                            <div class="col-10">
                                <select name="billing_country" class="form-control" required="true">
                                    <option value="">-- Select Country --</option>
                                    <?php foreach ( countries() as $country_code => $country_name ) : ?>
                                    <option value="<?php echo $country_code; ?>" <?php echo $country_code == 'US' ? 'selected' : ''; ?>><?php echo $country_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">

                    <?php if ( $config['show_shipping_address'] ) : ?>
                        <hr class="hidden-md-up">
                        <h3 class="colorgray mb20">Shipping Address</h3>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>Name</label>
                            <div class="col-10">
                                <input type="text" name="shipping_name" class="form-control" placeholder="Name" value="" required="true">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>Address 1</label>
                            <div class="col-10">
                                <input type="text" name="shipping_address1" class="form-control" placeholder="Address" value="" required="true">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Address 2</label>
                            <div class="col-10">
                                <input type="text" name="shipping_address2" class="form-control" placeholder="Address" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>City</label>
                            <div class="col-10">
                                <input type="text" name="shipping_city" class="form-control" placeholder="City" value="" required="true">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>State/Zip</label>
                            <div class="col-10">
                                <div class="row">
                                    <div class="col-8 pr5">
                                        <select name="shipping_state" class="form-control" required="true">
                                            <option value="">-- Select State --</option>
                                            <?php foreach ( states() as $country_name => $states_arr ) : ?>
                                            <optgroup label="<?php echo $country_name; ?>">
                                                <?php foreach ( $states_arr as $state_code => $state_name ) : ?>
                                                <option value="<?php echo $state_code; ?>"><?php echo $state_name; ?></option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                            <?php endforeach; ?>
                                            <option value="N/A">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-4 col-xs-4 pl5">
                                        <input type="text" name="shipping_zip" class="form-control" placeholder="Zip" value="" required="true">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>Country</label>
                            <div class="col-10">
                                <select name="shipping_country" class="form-control" required="true">
                                    <option value="">-- Select Country --</option>
                                    <?php foreach ( countries() as $country_code => $country_name ) : ?>
                                    <option value="<?php echo $country_code; ?>" <?php echo $country_code == 'US' ? 'selected' : ''; ?>><?php echo $country_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <hr class="hidden-md-up">
                    <h3 class="colorgray mt40 mb20">
                        Payment Method
                        <div class="floatright">
                            <?php if ( $config['enable_paypal'] && !empty($config['paypal_email']) ) : ?>
                                <label class="radio-inline pt0 mt10neg">
                                    <input type="radio" name="payment_method" value="creditcard" checked> 
                                    <img src="<?php echo url('assets/images/credit-cards.jpg'); ?>" class="">
                                </label>
                                <label class="radio-inline pt0 mt10neg">
                                    <input type="radio" name="payment_method" value="paypal"> 
                                    <img src="<?php echo url('assets/images/paypal.jpg'); ?>" class="w100">
                                </label>
                            <?php else : ?>
                                <img src="<?php echo url('assets/images/credit-cards.jpg'); ?>" class="">
                            <?php endif; ?>
                        </div>
                    </h3>

                    <div class="creditcard-content">

                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>Name</label>
                            <div class="col-10">
                                <div class="input-group">
                                    <input type="text" data-stripe="name" name="cardholder_name" class="form-control" placeholder="Name on Card" value="" required="true">
                                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                </div> 
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>Card #</label>
                            <div class="col-10">
                                <div class="input-group">
                                    <input type="text" data-stripe="number" class="form-control card-number" placeholder="Card Number" value="" required="true" data-rule-creditcard="true">
                                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                </div> 
                                <div class="card-type-image none"></div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label"><span class="colordanger">*</span>Expiration/CVC</label>
                            <div class="col-10">
                                <div class="row">
                                    <div class="col-4 pr5">
                                        <select data-stripe="exp-month" class="form-control" required="true">
                                            <?php for ( $i = 1; $i <= 12; $i++ ) : ?>
                                            <option value="<?php echo $i; ?>" <?php echo $i == date('n') ? 'selected="selected"' : ''; ?>><?php echo date('m', strtotime('2000-' . $i . '-01'));; ?></option>
                                            <?php endfor; ?>
                                        </select>   
                                    </div>
                                    <div class="col-4 pl5 pr5">
                                        <select data-stripe="exp-year" class="form-control" required="true">
                                            <?php for ( $i = date('Y'); $i <= date('Y') + 10; $i++ ) : ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="col-4 pl5">
                                        <div class="input-group">
                                            <input type="text" data-stripe="cvc" name="cvc" class="form-control" placeholder="CVC" value="" required="true">
                                            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt40">
                        
                        <div class="col-12 text-center">
                            <div class="creditcard-content">

                                <button type="submit" class="btn btn-lg btn-primary submit-button mb20" data-loading-text='<i class="fa fa-spinner fa-spin"></i> Submitting...' data-complete-text='<i class="fa fa-check"></i> Payment Complete!' <?php echo $allow_submit ? '' : 'disabled'; ?>>
                                    <span class="total <?php echo !empty($total) ? 'show' : ''; ?>">Total: <?php echo currencySymbol(); ?><span><?php echo $total; ?></span> <small><?php echo currencySuffix(); ?></small></span>
                                    <i class="fa fa-check"></i> Submit Payment
                                </button>

                            </div>
                            <div class="paypal-content displaynone">
                                <a href="#" class="btn btn-lg btn-primary submit-button paypal-button" data-loading-text='<i class="fa fa-spinner fa-spin"></i> Sending to PayPal...' <?php echo $allow_submit ? '' : 'disabled'; ?>>
                                    <span class="total <?php echo !empty($total) ? 'show' : ''; ?>">Total: <?php echo currencySymbol(); ?><span><?php echo $total; ?></span> <small><?php echo currencySuffix(); ?></small></span>
                                    Continue to PayPal <i class="fa fa-angle-double-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </form>

        
            </div>
        </div>
	</div>


    <?php include('includes/footer.php'); ?>
    <!-- common functions -->
    <?php template('javascript-includes'); ?>
</body>
</html>
<?php require 'assets/libs/cartman/close.php'; ?>