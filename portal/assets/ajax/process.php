<?php
require '../libs/cartman/init.php';

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

// --------------------------------------------------------------------------           
// Manually set action for paypal recurring payments return
// --------------------------------------------------------------------------
if (empty($action) && isset($_GET['auth'])) 
{
	$action = 'paypal_subscription_success';
}

if (!empty($action)) 
{
	// --------------------------------------------------------------------------
	// Check for csrf token first
	// --------------------------------------------------------------------------
	if ($action != 'paypal_ipn') 
	{
		if ( !empty($_POST) && (!isset($_POST['csrf']) || empty($_POST['csrf']) || $_POST['csrf'] != $csrf) ) 
		{
			msg('Invalid CSRF token, please try submitting again.', 'warning');
			go('/checkout.php');
		}
	}

	switch ($action) 
	{

		case 'process_payment':


			$status = true;
			$message = '';

			try 
			{
				// --------------------------------------------------------------------------
				// Make sure we have the payment token first
				// --------------------------------------------------------------------------
				if ( !post('token') ) 
				{
					throw new Exception('Payment could not be completed, please try again.');
				}

				// --------------------------------------------------------------------------
				// Build our customer data
				// --------------------------------------------------------------------------
				$invoice_id = filter_var(trim($_POST["invoice_id"]), FILTER_SANITIZE_STRING);
				$billing_name = filter_var(trim($_POST["billing_name"]), FILTER_SANITIZE_STRING);
				$billing_name_arr = explode(' ', trim($billing_name));
				$billing_first_name = $billing_name_arr[0];
				$billing_last_name = trim(str_replace($billing_first_name, '', $billing_name));
				$email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_STRING);
				$description = '<table class="table table-bordered">
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
					              <tbody>';
				$i = 0;	              
				foreach($_SESSION['cart_contents'] as $aProduct){
					$description .= '	<tr>';
	                $description .= ' <td class="text-center hidden-xs-down"> ';
	                $description .= '    <div class="image-additional">';
	                $description .= '      <a class="thumbnail mb-0" href="/product.php?id='.$aProduct['product_id'].'">';
	                $description .= '        <img src="http://'.$_SERVER['HTTP_HOST'].'/domains/'.ExtractSubdomains($_SERVER['HTTP_HOST']).'/images/products/'.$aProduct['product_file_name'].'" alt="'.$aProduct['product_name'].'" title="'.$aProduct['product_name'].'" width="40px" height="40px">';
	                $description .= '      </a>';
	                $description .= '    </div>';
	                $description .= '  </td>';
	                $description .= '  <td class="text-left"><a href="/product.php?id='.$aProduct['product_id'].'">'.$aProduct['product_name'].'</a></td>';
	                $description .= '  <td class="text-left">';
	                if(isset($aProduct['Size'])) { $description .= 'Size: '.$aProduct['Size'].'<br/>'; }
	                if(isset($aProduct['Color'])) { $description .= 'Color: '.$aProduct['Color']; }
	                $description .= '  </td>';
	                $description .= '  <td class="text-left hidden-xs-down">'.$aProduct['product_availability'].'</td>';
	                $description .= '  <td class="text-left">';
	                $description .= $aProduct['product_qty'].'<br/>';
	                if($aProduct['product_important_info']) { 
	                $description .= '      <div class="alert alert-info mt-3" id="info-alert">';
	                $description .= '          <i class="fa fa-info-circle"></i> '.$aProduct['product_important_info'];
	                $description .= '      </div>';
	                }
	                $description .= '  </td>';
	                $description .= '  <td class="text-right hidden-xs-down">$'.$aProduct['product_price'].'</td>';
	                $description .= '  <td class="text-right">';
	                $rowtotal = ($aProduct['product_price'] * $aProduct['product_qty']);
	                $description .= '$'.number_format((float)$rowtotal, 2, '.', '');
					$description .= '</td>';
	                $description .= '</tr>';
                 $i++;
				}
				$description .= '</tbody>';
				$description .= '</table>';

				$description =  addslashes($description);

				$billing_address1 = filter_var(trim($_POST["billing_address1"]), FILTER_SANITIZE_STRING);
				$billing_address2 = filter_var(trim($_POST["billing_address2"]), FILTER_SANITIZE_STRING);
				$billing_city = filter_var(trim($_POST["billing_city"]), FILTER_SANITIZE_STRING);
				$billing_state = filter_var(trim($_POST["billing_state"]), FILTER_SANITIZE_STRING);
				$billing_zip = filter_var(trim($_POST["billing_zip"]), FILTER_SANITIZE_STRING);
				$billing_country = filter_var(trim($_POST["billing_country"]), FILTER_SANITIZE_STRING);

				
				$shipping_name = filter_var(trim($_POST["shipping_name"]), FILTER_SANITIZE_STRING);
				$shipping_name_arr = explode(' ', trim($shipping_name));
				$shipping_first_name = $shipping_name_arr[0];
				$shipping_last_name = trim(str_replace($shipping_first_name, '', $shipping_name));
				$shipping_address1 = filter_var(trim($_POST["shipping_address1"]), FILTER_SANITIZE_STRING);
				$shipping_address2 = filter_var(trim($_POST["shipping_address2"]), FILTER_SANITIZE_STRING);
				$shipping_city = filter_var(trim($_POST["shipping_city"]), FILTER_SANITIZE_STRING);
				$shipping_state = filter_var(trim($_POST["shipping_state"]), FILTER_SANITIZE_STRING);
				$shipping_zip = filter_var(trim($_POST["shipping_zip"]), FILTER_SANITIZE_STRING);
				$shipping_country = filter_var(trim($_POST["shipping_country"]), FILTER_SANITIZE_STRING);
				$shipping_carrier = filter_var(trim($_POST["shipping_carrier"]), FILTER_SANITIZE_STRING);
				$shipping_charge = filter_var(trim($_POST["shipping_charge"]), FILTER_SANITIZE_STRING);

				$domain = ExtractSubdomains($_SERVER['HTTP_HOST']);
				$domain_id = 0;
				$query = "SELECT * FROM domains WHERE domain = '$domain';"; 
				$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query); 
				$numrows = mysqli_num_rows($result);
				
				if($numrows == 1)
				{
					$row = mysqli_fetch_array($result,  MYSQLI_ASSOC);
				    $domain_id = $row['id'];
				}

				$total = filter_var(trim($_POST["total"]), FILTER_SANITIZE_STRING);

				// --------------------------------------------------------------------------
				// Check for invoice first
				// --------------------------------------------------------------------------
				if ($invoice_id) 
				{
					$invoice = Model::factory('Invoice')->find_one($invoice_id);
					if($invoice){
						echo('exists');
						die();
					}
				
				} 


				
				// --------------------------------------------------------------------------
				// Handle recurring payments
				// --------------------------------------------------------------------------
				if (post('payment_type') == 'recurring') 
				{
					// --------------------------------------------------------------------------
					// Grab list of all plans
					// --------------------------------------------------------------------------
					$plans = Stripe_Plan::all();

					// --------------------------------------------------------------------------
					// Get existing plan if it fits
					// --------------------------------------------------------------------------				
					$create_plan = true;
					$plan_id = '';
					
					if ( isset($plans->data) && !empty($plans->data) ) 
					{
						foreach ($plans->data as $plan) 
						{
							if ($plan->interval == 'month' && $plan->amount / 100 == $total && $plan->interval_count == $config['subscription_interval']) 
							{
								// don't match the plan if the trial values don't line up
								if ( ($config['enable_trial'] && $plan->trial_period_days != $config['trial_days']) || (!$config['enable_trial'] && $plan->trial_period_days) ) 
								{
									continue;
								}
								$plan_id = $plan->id;
								$create_plan = false;
								break;
							}
						}
					}
					
					// --------------------------------------------------------------------------
					// Create the plan if necessary
					// --------------------------------------------------------------------------				
					if ($create_plan) 
					{
						$plan_arr = array(
							'amount' => $total * 100,
							'interval_count' => $config['subscription_interval'],
							'interval' => 'month',
							'name' => '$' . $total . ' every ' . $config['subscription_interval'] . ' month(s)',
							'id' => uniqid(),
							'currency' => $config['currency']
						);
						
						if ( $config['enable_trial'] && $config['trial_days'] > 0 ) 
						{
							$plan_arr['trial_period_days'] = $config['trial_days'];
							$plan_arr['name'] = $plan_arr['name'] . ' (' . $config['trial_days'] . ' day free trial)';
						}
						
						$plan = Stripe_Plan::create($plan_arr);
						$plan_id = $plan->id;
					}

					// --------------------------------------------------------------------------
					// Create the customer
					// --------------------------------------------------------------------------
					$customer = Stripe_Customer::create(array(
						'description' => $name,
					));

					// --------------------------------------------------------------------------
					// Create the subscription
					// --------------------------------------------------------------------------				
					$subscription_s = $customer->subscriptions->create(array(
						'plan' => $plan_id,
						'card' => post('token')
					));

					$unique_subscription_id = uniqid();
					
					// --------------------------------------------------------------------------
					// Save subscription record
					// --------------------------------------------------------------------------				
					$subscription = Model::factory('Subscription')->create();
					$subscription->unique_id = $unique_subscription_id;
					$subscription->stripe_customer_id = $subscription_s->customer;
					$subscription->stripe_subscription_id = $subscription_s->id;
					$subscription->name = $name;
					$subscription->email = $email;
					$subscription->address = $address;
					$subscription->city = $city;
					$subscription->state = $state;
					$subscription->zip = $zip;
					$subscription->country = $country;
					$subscription->description = isset($item) ? $item->name : $description;
					$subscription->price = $subscription_s->plan->amount / 100;
					$subscription->billing_day = date('j', $subscription_s->start);
					$subscription->length = 0;
					$subscription->interval = $subscription_s->plan->interval_count;
					$subscription->trial_days = $config['enable_trial'] ? $config['trial_days'] : null;
					$subscription->status = 'Active';
					$subscription->date_trial_ends = $config['enable_trial'] ? date('Y-m-d', strtotime('+' . $config['trial_days'] . ' days')) : null;
					$subscription->save();

					// set the message 
					$message = 'Your recurring payment has been created successfully, you should receive a confirmation email shortly.';
				} 

				else 
				{
					// --------------------------------------------------------------------------
					// Do the payment now
					// --------------------------------------------------------------------------				
					$transaction = Stripe_Charge::create(array(
					  'amount' => $total * 100,
					  'currency' => $config['currency'],
					  'card' => post('token'),
					  'description' => isset($item) ? $item->name : $description
					));

					// --------------------------------------------------------------------------
					// Save payment record
					$invoice = Model::factory('Invoice')->create();
					$invoice->unique_id = $invoice_id;
					$invoice->domain_id = $domain_id;
					$invoice->billing_name = $billing_name;
					$invoice->shipping_name = $shipping_name;
					$invoice->email = $email;
					$invoice->total = $transaction->amount / 100;
					$invoice->description = $description;
					$invoice->billing_address1 = $billing_address1;
					$invoice->billing_address2 = $billing_address2;
					$invoice->shipping_address1 = $shipping_address1;
					$invoice->shipping_address2 = $shipping_address2;
					$invoice->billing_city = $billing_city;
					$invoice->billing_state = $billing_state;
					$invoice->billing_zip = $billing_zip;
					$invoice->billing_country = $billing_country;
					$invoice->shipping_city = $shipping_city;
					$invoice->shipping_state = $shipping_state;
					$invoice->shipping_zip = $shipping_zip;
					$invoice->shipping_country = $shipping_country;
					$invoice->shipping_carrier = $shipping_carrier;
					$invoice->shipping_charge = $shipping_charge;
					$invoice->cc_name = $transaction->source->name;
					$invoice->cc_last_4 = $transaction->source->last4;
					$invoice->stripe_transaction_id = $transaction->id;
					$invoice->status = 'Paid';
					$invoice->date_paid = date('Y-m-d H:i:s');
					$invoice->save();

					// --------------------------------------------------------------------------
					// Set the message 
					// --------------------------------------------------------------------------
					$message = 'Your payment has been completed successfully, you should receive a confirmation email shortly.';
					$_SESSION['cart_contents'] = array();

				}


				$trial = isset($subscription) && $subscription->date_trial_ends ? ' <span style="color:#999999;font-size:16px">(Billing starts after your ' . $config['trial_days'] . ' day free trial ends)</span>' : '';
				
				// --------------------------------------------------------------------------
				// Build email values first for variable substitution
				// --------------------------------------------------------------------------
				$values = array(
					'customer_name' => $billing_name,
					'customer_email' => $email,
					'amount' => currency($total) . '<small>' . currencySuffix() . '</small>' . $trial,
					'description_title' => isset($item) ? 'Item' : 'Description',
					'description' => isset($item) ? $item->name : $description,
					'payment_method' => 'Credit Card' . (isset($transaction) ? ': XXXX-' . $transaction->source->last4 : ''),
					'transaction_id' => isset($transaction) ? $transaction->id : null,
					'subscription_id' => isset($subscription) ? $subscription->stripe_subscription_id : '',
					'manage_url' => isset($unique_subscription_id) ? url('manage.php?subscription_id=' . $unique_subscription_id) : '',
					'url' => url(''),
				);

				if ( post('payment_type') == 'recurring' ) 
				{
					email($config['email'], 'subscription-confirmation-admin', $values, 'You\'ve received a new recurring payment!');
					email($email, 'subscription-confirmation-customer', $values, 'Thank you for your recurring payment to ' . $config['name']);
				} 

				else 
				{
					email($config['email'], 'payment-confirmation-admin', $values, 'You\'ve received a new payment!');
					email($email, 'payment-confirmation-customer', $values, 'Thank you for your payment to ' . $config['name']);
				}


			} 

			catch (Exception $e) 
			{
				$status = false;
				$message = $e->getMessage();
			}
			

			$response = array(
				'status' => $status,
				'message' => $message
			);
			header('Content-Type: application/json');
			die(json_encode($response));

		break;


		case 'paypal_ipn':

			try 
			{
				// --------------------------------------------------------------------------
		    	// Die if it's a refund notification
				// --------------------------------------------------------------------------
		    	if ( preg_match('/refund/', post('reason_code')) ) 
		    	{
		    		die();
		    	}

				// --------------------------------------------------------------------------
		    	// Parse our custom field data
				// --------------------------------------------------------------------------
				$custom = post('custom');

				if ( $custom ) 
				{
					parse_str(post('custom'), $data);
				} 

				else 
				{
					$data = array();
				}
				
				// --------------------------------------------------------------------------
				// Pull out some values
				// --------------------------------------------------------------------------
				$payment_gross = post('payment_gross');
				$item_name = post('item_name');

				// --------------------------------------------------------------------------
				// Build customer data
				// --------------------------------------------------------------------------
				$name = isset($data['name']) && $data['name'] ? $data['name'] : null;
				$name_arr = explode(' ', trim($name));
				$first_name = $name_arr[0];
				$last_name = trim(str_replace($first_name, '', $name));
				$email = isset($data['email']) && $data['email'] ? $data['email'] : null;
				$description = $item_name ? $item_name : 'no description entered';
				$address = isset($data['address']) && $data['address'] ? $data['address'] : null;
				$city = isset($data['city']) && $data['city'] ? $data['city'] : null;
				$state = isset($data['state']) && $data['state'] ? $data['state'] : null;
				$zip = isset($data['zip']) && $data['zip'] ? $data['zip'] : null;
				$country = isset($data['country']) && $data['country'] ? $data['country'] : null;

				// --------------------------------------------------------------------------
				// Check for invoice first
				// --------------------------------------------------------------------------
				if (isset($data['invoice_id']) && $data['invoice_id']) 
				{
					$invoice = Model::factory('Invoice')->find_one($data['invoice_id']);
					$total = $invoice->total;
					$type = 'invoice';
					$description = $invoice->description;
				} 

				// --------------------------------------------------------------------------
				// Check for item
				// --------------------------------------------------------------------------
				elseif (isset($data['item_id']) && $data['item_id']) 
				{
					$item = Model::factory('Item')->find_one($data['item_id']);
					$total = $item->price;
					$type = 'item';
				} 

				// --------------------------------------------------------------------------
				// Check for input amount
				// --------------------------------------------------------------------------
				elseif ( $payment_gross ) 
				{
					$total = $payment_gross;
					$type = 'input';				
				} 

				// --------------------------------------------------------------------------
				// Return error if not found
				// --------------------------------------------------------------------------
				else 
				{
					$total = 0;
					$type = '';
				}

				switch (post('txn_type')) 
				{
					case 'web_accept':
						// --------------------------------------------------------------------------
						// Save payment record
						// --------------------------------------------------------------------------
						$payment = Model::factory('Payment')->create();
						$payment->invoice_id = isset($invoice) ? $invoice->id : null;
						$payment->name = $name;
						$payment->email = $email;
						$payment->amount = $total;
						$payment->description = isset($item) ? $item->name : $description;
						$payment->address = $address;
						$payment->city = $city;
						$payment->state = $state;
						$payment->zip = $zip;
						$payment->country = $country;
						$payment->type = $type;
						$payment->paypal_transaction_id = post('txn_id');
						$payment->save();

						// --------------------------------------------------------------------------
						// Update paid invoice
						// --------------------------------------------------------------------------
						if ( isset($invoice) ) 
						{
							$invoice->status = 'Paid';
							$invoice->date_paid = date('Y-m-d H:i:s');
							$invoice->save();
						}

						// --------------------------------------------------------------------------
						// Build email values first
						// --------------------------------------------------------------------------
						$values = array(
							'customer_name' => $payment->name,
							'customer_email' => $payment->email,
							'amount' => currency($payment->amount) . '<small>' . currencySuffix() . '</small>',
							'description_title' => isset($item) ? 'Item' : 'Description',
							'description' => $payment->description,
							'transaction_id' => post('txn_id'),
							'payment_method' => 'PayPal',
							'url' => url(''),
						);
						
						email($config['email'], 'payment-confirmation-admin', $values, 'You\'ve received a new payment!');
						email($payment->email, 'payment-confirmation-customer', $values, 'Thank you for your payment to ' . $config['name']);

					break;

					case 'subscr_signup':

						try 
						{						
							$unique_subscription_id = uniqid();
						
							// --------------------------------------------------------------------------
							// Save subscription record
							// --------------------------------------------------------------------------
							$subscription = Model::factory('Subscription')->create();
							$subscription->unique_id = $unique_subscription_id;
							$subscription->paypal_subscription_id = post('subscr_id');
							$subscription->name = $name;
							$subscription->email = $email;
							$subscription->address = $address;
							$subscription->city = $city;
							$subscription->state = $state;
							$subscription->zip = $zip;
							$subscription->country = $country;
							$subscription->description = isset($item) ? $item->name : $description;
							$subscription->price = post('amount3');
							$subscription->billing_day = date('j', strtotime(post('subscr_date')));
							$subscription->length = $config['subscription_length'];
							$subscription->interval = $config['subscription_interval'];
							$subscription->trial_days = $config['enable_trial'] ? $config['trial_days'] : null;
							$subscription->status = 'Active';
							$subscription->date_trial_ends = $config['enable_trial'] ? date('Y-m-d', strtotime('+' . $config['trial_days'] . ' days')) : null;
							$subscription->save();

							$trial = $subscription->date_trial_ends ? ' <span style="color:#999999;font-size:16px">(Billing starts after your ' . $config['trial_days'] . ' day free trial ends)</span>' : '';
							$values = array(
								'customer_name' => $name,
								'customer_email' => $email,
								'amount' => currency(post('amount3')) . '<small>' . currencySuffix() . '</small>' . $trial,
								'description_title' => isset($item) ? 'Item' : 'Description',
								'description' => isset($item) ? $item->name : $description,
								'payment_method' => 'PayPal',
								'subscription_id' => post('subscr_id'),
								'manage_url' => url('manage.php?subscription_id=' . $unique_subscription_id)
							);
							email($config['email'], 'subscription-confirmation-admin', $values, 'You\'ve received a new recurring payment!');
							email($email, 'subscription-confirmation-customer', $values, 'Thank you for your recurring payment to ' . $config['name']);

						} 

						catch (Exception $e) 
						{

						}

					break;

					case 'subscr_cancel':
						
						$subscription = Model::factory('Subscription')->where('paypal_subscription_id', post('subscr_id'))->find_one();
						if ($subscription) 
						{
							$subscription->status = 'Canceled';
							$subscription->date_canceled = date('Y-m-d H:i:s');
							$subscription->save();
						
							// --------------------------------------------------------------------------
							// Send a subscription cancellation email now
							// --------------------------------------------------------------------------
							$values = array(
								'customer_name' => $subscription->name,
								'customer_email' => $subscription->email,
								'amount' => currency($subscription->price) . '<small>' . currencySuffix() . '</small>',
								'description' => $subscription->description,
								'payment_method' => 'PayPal',
								'subscription_id' => $subscription->paypal_subscription_id
							);
							email($config['email'], 'subscription-canceled-admin', $values, 'A recurring payment has been canceled.');
							email($subscription->email, 'subscription-canceled-customer', $values, 'Your recurring payment to ' . $config['name'] . ' has been canceled.');
						}
					break;

					case 'subscr_eot':
						$subscription = Model::factory('Subscription')->where('paypal_subscription_id', post('subscr_id'))->find_one();
						if ($subscription && $subscription->status == 'Active' ) 
						{
							$subscription->status = 'Expired';
							$subscription->date_canceled = null;
							$subscription->save();
						}
					break;

				}


			} 

			catch (Exception $e) 
			{
				die();
			}

		break;


		case 'paypal_success':
			go('/checkout.php#status=paypal_success');
		break;

		case 'paypal_subscription_success':
			go('/checkout.php#status=paypal_subscription_success');
		break;

		case 'paypal_cancel':
			msg('You canceled your PayPal payment, no payment has been made.', 'warning');
			go('/checkout.php');
		break;

		case 'delete_payment':
			if (isset($_GET['id'])) 
			{
				$payment = Model::factory('Payment')->find_one($_GET['id']);
				$payment->delete();
			}
			msg('Payment has been deleted successfully.', 'success');
			go('admin.php#tab=payments');
		break;

		case 'delete_subscription':
			if (isset($_GET['id'])) 
			{
				$subscription = Model::factory('Subscription')->find_one($_GET['id']);
				$subscription->delete();
			}
			msg('Subscription has been deleted successfully.', 'success');
			go('admin.php#tab=subscriptions');
		break;

		case 'cancel_subscription':
			if (isset($_GET['subscription_id'])) 
			{
				$subscription = Model::factory('Subscription')->find_one($_GET['subscription_id']);
				$subscription->status = 'Canceled';
				$subscription->date_canceled = date('Y-m-d H:i:s');
				$subscription->save();
			
				try 
				{
					if ($subscription->stripe_customer_id && $subscription->stripe_subscription_id) 
					{
						$customer = Stripe_Customer::retrieve($subscription->stripe_customer_id);
						$subscription_s = $customer->subscriptions->retrieve($subscription->stripe_subscription_id);
						$subscription_s->cancel();

						// --------------------------------------------------------------------------
						// Send subscription cancellation email now
						// --------------------------------------------------------------------------
						$values = array(
							'customer_name' => $subscription->name,
							'customer_email' => $subscription->email,
							'amount' => currency($subscription->price) . '<small>' . currencySuffix() . '</small>',
							'description' => $subscription->description,
							'payment_method' => 'Credit Card',
							'subscription_id' => $subscription->stripe_subscription_id
						);
						email($config['email'], 'subscription-canceled-admin', $values, 'A recurring payment has been canceled.');
						email($subscription->email, 'subscription-canceled-customer', $values, 'Your recurring payment to ' . $config['name'] . ' has been canceled.');
					}
				} 

				// --------------------------------------------------------------------------
				// TODO: Add more detailed error handling
				// --------------------------------------------------------------------------
				catch (Stripe_CardError $e) 
				{
				} 

				catch (Stripe_InvalidRequestError $e) 
				{
				} 

				catch (Stripe_AuthenticationError $e) 
				{
				} 

				catch (Stripe_ApiConnectionError $e) 
				{
				} 

				catch (Stripe_Error $e) 
				{
				} 

				catch (Exception $e) 
				{
					$error = $e->getMessage();
				}
			}
			
			if (!isset($_GET['prevent_msg'])) 
			{
				if (isset($error)) 
				{
					msg($error, 'danger');
				} 

				else 
				{
					msg('Your subscription has been canceled successfully.', 'success');
				}
			}

			if ( get('return') == 'admin' ) 
			{
				go('admin.php#tab=subscriptions');
			} 
			
			else 
			{
				go('manage.php?subscription_id=' . $subscription->unique_id);
			}
		break;

		case 'create_invoice':
			if (post('email') && post('amount') && post('description')) 
			{
				$unique_invoice_id = uniqid();
				$invoice = Model::factory('Invoice')->create();
				$invoice->unique_id = $unique_invoice_id;
				$invoice->email = post('email');
				$invoice->description = post('description');
				$invoice->amount = post('amount');
				$invoice->number = post('number');
				$invoice->status = 'Unpaid';
				$invoice->date_due = post('date_due') ? date('Y-m-d', strtotime(post('date_due'))) : null;
				$invoice->save();
			}
			
			$number = $invoice->number ? $invoice->number : $invoice->id();
			
			if (post('send_email') && post('send_email')) 
			{
				$values = array(
					'number' => $number,
					'amount' => currency($invoice->amount) . '<small>' . currencySuffix() . '</small>',
					'description' => $invoice->description,
					'date_due' => !is_null($invoice->date_due) ? date('F jS, Y', strtotime($invoice->date_due)) : '<em>no due date set</em>',
					'url' => url('?invoice_id=' . $unique_invoice_id)
				);
				email($invoice->email, 'invoice', $values, 'Invoice from ' . $config['name']);
				$msg = ' and sent';
			}
			
			msg('Invoice has been created' . (isset($msg) ? $msg : '') . ' successfully.', 'success');
			go('admin.php#tab=invoices');
		break;

		case 'delete_invoice':
			if (isset($_GET['id'])) 
			{
				$invoice = Model::factory('Invoice')->find_one($_GET['id']);
				$invoice->delete();
			}
			
			msg('Invoice has been deleted successfully.', 'success');
			go('admin.php#tab=invoices');
		break;

		case 'add_item':
			if (post('name') && post('price')) 
			{
				$item = Model::factory('Item')->create();
				$item->name = post('name');
				$item->price = post('price');
				$item->save();
			}
			
			msg('Item has been added successfully.', 'success');
			go('admin.php#tab=items');
		break;

		case 'edit_item':
			if (post('id') && post('name') && post('price')) 
			{
				$item = Model::factory('Item')->find_one(post('id'));
				$item->name = post('name');
				$item->price = post('price');
				$item->save();
			}
			
			msg('Item has been edited successfully.', 'success');
			go('admin.php#tab=items');
		break;

		case 'delete_item':
			if (isset($_GET['id'])) 
			{
				$item = Model::factory('Item')->find_one($_GET['id']);
				$item->delete();
			}
			
			msg('Item has been deleted successfully.', 'success');
			go('admin.php#tab=items');
		break;

		case 'save_config':


			// --------------------------------------------------------------------------
			// Don't allow login from anywhere other than the admin page 
			// --------------------------------------------------------------------------
			if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) 
			{
				if (!preg_match('/\/admin\.php$/', $_SERVER['HTTP_REFERER'])) 
				{
					msg('Invalid login attempt, please try again.', 'warning');
					go('/checkout.php');
				}
			}

			if (post('config') && is_array(post('config'))) 
			{
				foreach ( post('config') as $key => $value ) 
				{
					$config = Model::factory('Config')->where('key', $key)->find_one();
				
					if ( $config ) 
					{
						$config->value = $value;
						$config->save();
					}
				}
			}
			msg('Your settings have been saved successfully.', 'success');
			go('admin.php#tab=settings');
		break;

		case 'disable_notification':
			$config = Model::factory('Config')->where('key', 'notification_status')->find_one();
			$config->value = 'disabled';
			$config->save();
		break;

		case 'login':

			// --------------------------------------------------------------------------
			// Prevent login from anything other than the admin page
			// --------------------------------------------------------------------------
			if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) 
			{
				if (!preg_match('/\/admin\.php$/', $_SERVER['HTTP_REFERER'])) 
				{
					msg('Invalid login attempt, please try again.', 'warning');
					go('/checkout.php');
				}
			}

			// --------------------------------------------------------------------------
			// Login successful, set session
			// --------------------------------------------------------------------------				
			if (post('admin_username') && post('admin_username') == $config['admin_username'] && 
				post('admin_password') && post('admin_password') == $config['admin_password']) 
			{		
				$_SESSION['admin_username'] = $config['admin_username'];
			} 

			// --------------------------------------------------------------------------
			// Login failed.. set error message
			// --------------------------------------------------------------------------				
			else 
			{
				msg('Login attempt failed, please try again.', 'danger');
			}

			go('admin.php');
		break;

		case 'logout':
			unset($_SESSION['admin_username']);
			session_destroy();
			session_start();
			msg('You have been logged out successfully.', 'success');
			go('admin.php');
		break;

		case 'install':
			$status = true;
			$message = '';

			try 
			{
				$db = new PDO('mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'], $config['db_username'], $config['db_password']);
				$sql = file_get_contents('lib/sql/install.sql');
				$result = $db->exec($sql);
			} 

			catch (PDOException $e) 
			{
				$status = false;
				$message = $e->getMessage();
			}
			
			$response = array(
								'status' => $status,
								'message' => $message
							);
			
			header('Content-Type: application/json');
			die(json_encode($response));
		break;

	} // end switch ($action) 
} // end if (!empty($action)) 