<?php
/*** Gateway class**/
class WC_Gateway_Paymentsense_Direct extends WC_Payment_Gateway 
{
	public $liveurl = 'paymentsensegateway.com';
	/**	 * Port number	 */	
	public $port = 4430;
	/**	 * Test mode	 */	
	var $testmode;
	/**	 * notify url	 */
	var $notify_url;
	
	public function __construct()
	{
		global $woocommerce;
		$this->id = 'paymentsense_direct';
		$this->method_title = __('Paymentsense Direct', 'woocommerce');
		$this->log = plugins_url('images/paymentsense.gif', __FILE__);
		$this->has_fields 	= true;
		// Load the form fields.
		$this->init_form_fields();
		// Load the settings.
		$this->init_settings();
		// Define user set variables
		$this->title = $this->settings['title'];
		$this->description = $this->settings['description'];
		$this->order_prefix = $this->settings['order_prefix'];
		$this->merchant_id = $this->settings['merchant_id'];
		$this->password = $this->settings['password'];
		$this->transactionDate = date('Y-m-d H:i:s P');
		$this->transaction_type = ($this->settings['transaction_type'] == "yes" ? "PREAUTH" : "SALE");
		$this->amex_accepted = ($this->settings['amex_accepted'] == "yes" ? "TRUE" : "FALSE");
		
		if ( "TRUE" == $this->amex_accepted )
		{
			$this->icon = apply_filters('woocommerce_paymentsense_direct_icon', plugins_url('images/paymentsense-logos-with-amex.png', __FILE__));
		}
		else
		{
			$this->icon = apply_filters('woocommerce_paymentsense_direct_icon', plugins_url('images/paymentsense-logos-no-amex.png', __FILE__ ));
		}
		
		$this->debug = $this->settings['debug'];
		// Actions
		add_action( 'woocommerce_receipt_paymentsense_direct', array(&$this, 'receipt_page') );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ));
		add_action( 'woocommerce_api_wc_gateway_paymentsense_direct', array( $this, 'check_passback' ) );
		$this->notify_url   = add_query_arg( 'wc-api', 'WC_Gateway_Paymentsense_Direct', home_url('/') );
		if ( 'yes' == $this->debug)
		{
			$this->log = $woocommerce->logger();
		}
	}
	
	/**	 * Initialise Gateway Settings Form Fields	 */
	function init_form_fields()
	{
		$this->form_fields = array(
				'enabled' => array(
						'title' => __( 'Enable/Disable:', 'woocommerce' ),
						'type' => 'checkbox',
						'label' => __( 'Enable Paymentsense Payment Module.', 'woocommerce' ),
						'default' => 'yes'
				),
				'title' => array(
						'title' => __( 'Title:', 'woocommerce' ),
						'type' => 'text',
						'description' => __( 'The title which the user sees during checkout.', 'woocommerce' ),
						'default' => __( 'Pay via Paymentsense Direct', 'woocommerce' )
				),
				'description' => array(
						'title' => __( 'Description:', 'woocommerce' ),
						'type' => 'textarea',
						'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce' ),
						'default' => __('Pay securely by Credit or Debit card through Paymentsense.', 'woocommerce')
				),
				'order_prefix' => array(
						'title' => __( 'Order Prefix:', 'woocommerce' ),
						'type' => 'text',
						'description' => __( 'This is the order prefix that you will see in the MMS.', 'woocommerce' ),
						'default' => __( 'Woocommerce', 'woocommerce' )
				),
				'merchant_id' => array(
						'title' => __( 'Merchant ID:', 'woocommerce' ),
						'type' => 'text',
						'description' => __( 'Please enter your Merchant ID as provided by Paymentsense.', 'woocommerce'),
						'default' => ''
				),
				'password' => array(
						'title' => __( 'Merchant Password:', 'woocommerce' ),
						'type' => 'text',
						'description' => __( 'Please enter your Merchant Password as provided by Paymentsense.', 'woocommerce' ),
						'default' => ''
				),
				'secret_key' => array(
						'title' => __( 'Secret Key (Optional):', 'woocommerce' ),
						'type' => 'text',
						'description' => __( 'Enter your Secret Key', 'woocommerce' ),
						'default' => ''
				),
				'transaction_type' => array(
						'title' => __( 'Transaction Type:', 'woocommerce' ),
						'type' => 'select',
						'label' => __( 'Tick to obtain Authorisation for the payment only (you intend to manually collect the payment via the MMS).', 'woocommerce'),
						'default' => 'SALE',
						'options' => array(
							'SALE' => __('Sale', 'woocommerce'),
							'PREAUTH' => __('Pre-auth', 'woocommerce'),
						),
				),
				'amex_accepted' => array(
						'title' => __( 'Accept American Express?', 'woothemes' ),
						'type' => 'checkbox',
						'label' => __( 'Only tick if you have an American Express MID associated with your Paymentsense gateway account.', 'woothemes' ),
						'default' => 'no'
				),
				'debug' => array(
						'title' => __( 'Debug Log', 'woocommerce' ),
						'type' => 'checkbox',
						'label' => __( 'Enable logging', 'woocommerce' ),
						'default' => 'no',
						'description' => __( 'Log Paymentsense events, inside <code>woocommerce/logs/paymentsense_direct.txt</code>' ),
				)
		);
	} 
	/* End init_form_fields()*/
	/**	 * Admin Panel Options 	 * - Options for bits like 'title' and availability on a country-by-country basis	 * @since 1.0.0	 */	
	
	public function admin_options()
	{
	?>
	<p>
		<img src="<?php echo $this->logo;?>" />
	</p>
	<h3><?php _e('Paymentsense Payments', 'woocommerce') ?></h3>
	<p>
		<?php _e('<b>Accept payments from Credit/Debit cards through the Paymentsense Payment Gateway.</b><br />', 'woocommerce') ?>
	</p>
	<p>
		<a href="https://mms.paymentsensegateway.com/" target="_blank">Paymentsense Merchant Management System (MMS)</a>
		<br />
		<a href="http://www.paymentsense.co.uk/" target="_blank">Paymentsense Website</a>
	</p>
	<table class="form-table">
	<?php
	/* Generate the HTML For the settings form.*/
	$this->generate_settings_html();
	?>		
	</table>
	<!--/.form-table-->
	<?php
	} 
	/* End admin_options()*/
	 /**
	  * URL gateway
	  */	
	function get_gateway_url()
	{
		$url = '';
		if ($this->port == 443)
		{
			$url = $this->liveurl ."/";
		}
		else
		{
			$url = $this->liveurl .":".$this->port."/";
		}
		return $url;
	}
	/**     * Check if this gateway is enabled and available in the user's country     */
	
	function is_valid_for_use()
	{
		return true;
	}
	
	/**     * Payment form on checkout page     */
	function payment_fields() 
	{
		global $woocommerce;
		if ($this->testmode=='yes') 
		{ 
		?>
			<p>
				<?php _e('TEST MODE/SANDBOX ENABLED', 'woocommerce'); ?>
			</p>
		<?php 
		}
		if ($this->description)
		{ 
		?>
			<p>
				<?php echo wpautop(wptexturize($this->description)); ?>
			</p>
		<?php 
		}
		?>
		<fieldset>
				<table style="border: none;">
					<tr style="border: none;">
						<td style="border: none; width: 40%">
							<label for="psense_ccname"><?php echo __("Card Name:", 'woocommerce') ?> <span class="required">*</span></label>
						</td>
						<td colspan="2" style="border: none;">
							<input type="text" class="input-text" id="psense_ccname" name="psense_ccname" />
						</td>
					</tr>
					<tr style="border: none;">
						<td style="border: none;">
							<label for="psense_ccnum"><?php echo __("Credit Card number:", 'woocommerce') ?> <span class="required">*</span></label>
						</td>
						<td colspan="2" style="border: none;">
							<input type="text" class="input-text" id="psense_ccnum" name="psense_ccnum" />
						</td>
					</tr>
					<tr>
						<td style="border: none;">
							<label for="psense_cv2"><?php echo __("CVV/CV2 Number:", 'woocommerce') ?> <span class="required">*</span></label>
						</td>
						<td style="border: none;">
							<input type="text" class="input-text" id="psense_cv2" name="psense_cv2" maxlength="4" style="width:60px" />
						</td>
						<td style="border: none;">
							<span class="help"><a href="https://www.cvvnumber.com/cvv.html" target="_blank" style="font-size:11px">What is my CVV code?</a></span>
						</td>
					</tr>
					<tr>
						<td style="border: none;">
							<label for="psense_issueno"><?php echo __("Issue number:", 'woocommerce') ?></label>
						</td>
						<td style="border: none;">
							<input type="text" class="input-text" id="psense_issueno" name="psense_issueno" style="width:60px" />
						</td>
						<td style="border: none;">
							<span class="help"><?php _e('(Maestro/Solo only).', 'woocommerce') ?></span>
						</td>
					</tr>
					<tr>
						<td style="border: none;">
							<label for="psense_expmonth"><?php echo __("Expiration date:", 'woocommerce') ?> <span class="required">*</span></label>
						</td>
						<td style="border: none;">
							<select name="psense_expmonth" id="psense_expmonth" class="woocommerce-select woocommerce-cc-month">
								<option value=""><?php _e('Month', 'woocommerce') ?></option>
								<?php
									$months = array();
									for ($i = 1; $i <= 12; $i++)
									{
										$timestamp = mktime(0, 0, 0, $i, 1);
										$months[date('n', $timestamp)] = date('F', $timestamp);
									}
									foreach ($months as $num => $name)
									{
										printf('<option value="%u">%s</option>', $num, $name);
									}
								?>
							</select>
						</td>
						<td style="border: none;">
							<select name="psense_expyear" id="psense_expyear" class="woocommerce-select woocommerce-cc-year">
								<option value=""><?php _e('Year', 'woocommerce') ?></option>
								<?php 
									for($y=0; $y<=10; $y++)
									{
								?>
								<option value="<?php echo (date('y') + $y);?>"><?php echo (date('Y') + $y);?></option>
								<?php
									}
								?>
							</select>
						</td>
					</tr>
				</table>

				
				
				
			<!--  <div class="clear"></div>-->
		</fieldset>
		<?php
		}
		/**     * Process the payment     */
		function process_payment($order_id)
		{
		    
			global $woocommerce;
			$order = new WC_Order( $order_id );
			$suppcurr = array(
					'USD' => '840',
					'EUR' => '978',
					'GBP' => '826'
			);
			
			if(!empty($suppcurr[get_option('woocommerce_currency')]))
			{
				$currency = $suppcurr[get_option('woocommerce_currency')];
			}
			else
			{
				$currency = '826';
			}
			
			try
			{
			    $headers = array(
			        'SOAPAction:https://www.thepaymentgateway.net/CardDetailsTransaction',
			        'Content-Type: text/xml; charset = utf-8',
			        'Connection: close'
			    );
			    
			    
			    $MerchantID = $this->merchant_id;
			    $Password = $this->password;
			    $Amount = $order->order_total * 100; //Amount must be passed as an integer in pence
			    $CurrencyCode = $currency; //826 = GBP
			    
			    $OrderID = $order_id;
			    $OrderDescription = $this->order_prefix . " " . (string)$order_id; //Order Description for this new transaction
			    
			    $CardName = $this->stripGWInvalidChars($this->get_request('psense_ccname'));
			    //die($Amount);
			    $CardNumber = $this->get_request('psense_ccnum');
			    $ExpMonth = $this->get_request('psense_expmonth');
			    $ExpYear = $this->get_request('psense_expyear');
			    $CV2 = $this->get_request('psense_cv2');
			    $IssueNumber = $this->get_request('psense_issueno');
			    
			    $Address1 = $this->stripGWInvalidChars($order->billing_address_1);
			    $Address2 = $this->stripGWInvalidChars($order->billing_address_2);
			    $Address3 = '';
			    $Address4 = '';
			    $City = $this->stripGWInvalidChars($order->billing_city);
			    $State = $this->stripGWInvalidChars($order->billing_state);
			    $Postcode = $this->stripGWInvalidChars($order->billing_postcode);
			    $Country = $this->stripGWInvalidChars($order->billing_country);
			    $EmailAddress = $this->stripGWInvalidChars($order->billing_email);
			    $PhoneNumber = $this->stripGWInvalidChars($order->billing_phone);
			    $CountryCode = 826;
			    $IPAddress = $_SERVER['REMOTE_ADDR'];
			    
			    
				$xml = '<?xml version="1.0" encoding="utf-8"?>
                        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                            <soap:Body>
                                <CardDetailsTransaction xmlns="https://www.thepaymentgateway.net/">
                                    <PaymentMessage>
                                        <MerchantAuthentication MerchantID="'. $MerchantID .'" Password="'. $Password .'" />
                                        <TransactionDetails Amount="'. $Amount .'" CurrencyCode="'. $CurrencyCode .'">
                                            <MessageDetails TransactionType="SALE" />
                                            <OrderID>'.$OrderID .'</OrderID>
                                            <OrderDescription>'. $OrderDescription . '</OrderDescription>
                                            <TransactionControl>
                                                <EchoCardType>TRUE</EchoCardType>
                                                <EchoAVSCheckResult>TRUE</EchoAVSCheckResult>
                                                <EchoCV2CheckResult>TRUE</EchoCV2CheckResult>
                                                <EchoAmountReceived>TRUE</EchoAmountReceived>
                                                <DuplicateDelay>20</DuplicateDelay>
                                                <CustomVariables>
                                                    <GenericVariable Name="MyInputVariable" Value="Ping" />
                                                </CustomVariables>
                                            </TransactionControl>
                                        </TransactionDetails>
                                        <CardDetails>
                                            <CardName>'. $CardName .'</CardName>
                                            <CardNumber>'. $CardNumber .'</CardNumber>
                                            <StartDate Month="" Year="" />
                                            <ExpiryDate Month="'. $ExpMonth .'" Year="'. $ExpYear .'" />
                                            <CV2>'. $CV2 .'</CV2>
                                            <IssueNumber>'. $IssueNumber .'</IssueNumber>
                                        </CardDetails>
                                        <CustomerDetails>
                                            <BillingAddress>
                                                <Address1>'. $Address1 .'</Address1>
                                                <Address2>'. $Address2 .'</Address2>
                                                <Address3>'. $Address3 .'</Address3>
                                                <Address4>'. $Address4 .'</Address4>
                                                <City>'. $City .'</City>
                                                <State>'. $State .'</State>
                                                <PostCode>'. $Postcode .'</PostCode>
                                                <CountryCode>'. $CountryCode .'</CountryCode>
                                            </BillingAddress>
                                            <EmailAddress>'. $EmailAddress .'</EmailAddress>
                                            <PhoneNumber>'. $PhoneNumber .'</PhoneNumber>
                                            <CustomerIPAddress>'. $IPAddress .'</CustomerIPAddress>
                                        </CustomerDetails>
                                        <PassOutData>Some data to be passed out</PassOutData>
                                    </PaymentMessage>
                                </CardDetailsTransaction>
                            </soap:Body>
                        </soap:Envelope>';
				
				$gwId = 1;
				$domain = "paymentsensegateway.com";
				$port = "4430";
				$transattempt = 1;
				$soapSuccess = false;
				
				while(!$soapSuccess && $gwId <= 3 && $transattempt <= 3)
				{
				    $url = 'https://gw'.$gwId.'.'.$domain.':'.$port.'/';
				    
				    //initialise cURL
				    $curl = curl_init();
				    
				    //set the options
				    curl_setopt($curl, CURLOPT_HEADER, false);
				    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				    curl_setopt($curl, CURLOPT_POST, true);
				    curl_setopt($curl, CURLOPT_URL, $url);
				    curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);
				    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				    curl_setopt($curl, CURLOPT_ENCODING, 'UTF-8');
				    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				    
				    //Execute cURL request
				    //$ret = returned XML
				    $ret = curl_exec($curl);
				    //$err = returned error number
				    $err = curl_errno($curl);
				    //retHead = returned XML header
				    $retHead = curl_getinfo($curl);
				    //die($ret);
				    //close cURL connection
				    curl_close($curl);
				    $curl = null;
				    //die($ret);
				    if($err == 0)
				    {
				        $StatusCode = $this->GetXMLValue("StatusCode", $ret, "[0-9]+");
				        
				        if(is_numeric($StatusCode)) {
				            //request was processed correctly
				            	
				            if( $StatusCode != 30 ) {
				                //set success flag so it will not run the request again.
				                $soapSuccess = true;
				        
				                //grab some of the most commonly used information from the response
				                $szMessage = $this->GetXMLValue("Message", $ret, ".+");
				                $szAuthCode = $this->GetXMLValue("AuthCode", $ret, ".+");
				                $szCrossReference = $this->GetCrossReference($ret);
				                $szAddressNumericCheckResult = $this->GetXMLValue("AddressNumericCheckResult", $ret, ".+");
				                $szPostCodeCheckResult = $this->GetXMLValue("PostCodeCheckResult", $ret, ".+");
				                $szCV2CheckResult = $this->GetXMLValue("CV2CheckResult", $ret, ".+");
				                $szThreeDSecureAuthenticationCheckResult = $this->GetXMLValue("ThreeDSecureAuthenticationCheckResult", $ret, ".+");
				            
				                switch ($StatusCode) {
				                    case 0:
				                        // transaction authorised
				                        $transaction_status = 'success';
				                        break;
				                    case 3:
				                        //3D Secure Auth required
				                        //Gather required variables
				                        if ('yes'==$this->debug)
				                        {
				                            $this->log->add( 'paymentsense_direct', '3D Secure authentication required');
				                        }
				                        
				                        $pareq = $this->GetXMLValue("PaREQ", $ret, ".+");
				                        $crossref 	= $szCrossReference;
				                        $url = $this->GetXMLValue("ACSURL", $ret, ".+");
				                        $woocommerce->session->paymentsense = array('pareq' => $pareq, 'crossref' => $crossref, 'url' => $url);
				                        return array(
				                            'result' 	=> 'success',
				                            'redirect'	=> add_query_arg('key', $order->order_key, add_query_arg('order', $order_id, get_permalink(woocommerce_get_page_id('pay'))))
				                        );
				                        break;
				                    case 4:
				                        //Card Referred - treat as a decline
				                        $transaction_status = 'failed';
				                        break;
				                    case 5:
				                        //Card declined
				                        
				                        $transaction_status = 'failed';
				                        break;
				                    case 20:
				                        if (preg_match('#<PreviousTransactionResult>(.+)</PreviousTransactionResult>#iU', $ret, $soapPreviousTransactionResult)) {
				                            $PreviousTransactionResult = $soapPreviousTransactionResult[1];
				                            	
				                            $PreviousMessage = $this->GetXMLValue("Message", $PreviousTransactionResult, ".+");
				                            $PreviousStatusCode = $this->GetXMLValue("StatusCode", $PreviousTransactionResult, ".+");
				                        }
				                        	
				                        // need to look at the previous status code to see if the transaction was successful
				                        if ($PreviousStatusCode == 0) 
				                        {
				                            $transaction_status = 'success';
				                        } 
				                        else 
				                        {
				                            $transaction_status = 'failed';
				                        }
				                        break;
				                    default:
				                        $transaction_status = 'failed';
				                        break;
				                }
				            }
				            else 
				            {
				                // status code is 30 - error occured
				                // get the reason from the xml
				                $szMessageDetail = $this->GetXMLValue("Detail", $ret, ".+");
				            
				                //run the function to get the cause of the error
				                $Response = "Error occurred: ";
				                $Response .= $szMessageDetail;
				            }
				        }
				    }
				    if($transattempt <=2) 
				    {
				        $transattempt++;
				    } 
				    else 
				    {
				        //reset transaction attempt to 1 & incremend $gwID (to use next numeric gateway number (eg. use gw2 rather than gw1 now))
				        $transattempt = 1;
				        $gwId++;
				    }
				}
								
				if($transaction_status == 'success')
				{
					$order->payment_complete();
					$order->add_order_note('Payment Successful: '.$szMessage.'<br />',0);
					return array(
						'result' => 'success',
						'redirect' => $this->get_return_url( $order )
					);
				}
				elseif ($transaction_status == 'failed')
				{
				    //die("123456");
					$order->get_checkout_payment_url(false);
					$order->update_status('failed', sprintf( __( 'Payment Failed due to: %s .<br />', 'woocommerce' ), strtolower( $szMessage ) ));
					wc_add_notice(__('Payment Failed due to: ', 'woothemes') . $szMessage. '<br /> Please check your card details and try again.', 'error');
					

					return;
				}
			} 
    		catch(Exception $ex)
    		{
    			if ($this->debug=='yes')
    			{
    				$this->log->add( 'paymentsense_direct', "Error: " . $szMessage);
    			}
    			echo '<div class="woocommerce-error woocommerce_error">'. $szMessage . '</div>';
    			exit;
    		}
    		exit;
    		}
		
		/* receipt_page	 */	
		function receipt_page( $order_id )
		{
			global $woocommerce;
			
			$order = new WC_Order( $order_id );
			$term_url = '';
			$target = 'ACSFrame';
			$hashdigest = '';
			$paymentsense_sess = $woocommerce->session->paymentsense;
			
			if ($this->debug=='yes')
			{
				$this->log->add( 'paymentsense_direct', "Generate form session: ". print_r($paymentsense_sess, true) );
			}
			
			if(empty($paymentsense_sess)) 
			{
				return;
			}
			$pareq = $paymentsense_sess['pareq'];
			$crossref = $paymentsense_sess['crossref'];
			//$term_url = add_query_arg('key', $order->order_key, add_query_arg('order', $order_id, get_permalink(woocommerce_get_page_id('pay'))));
			$term_url = add_query_arg('key', $order->order_key, add_query_arg('order', $order_id, $this->notify_url));
			$pay_url = $paymentsense_sess['url'];
			$args['pay_url'] = $pay_url;
			$args['target'] = $target;
			$args['hashdigest'] = $hashdigest;
			$args['term_url'] = $term_url;
			$args['pareq'] = $pareq;
			$args['crossref'] = $crossref;
			$args['cancel_url'] = $order->get_cancel_order_url();
			echo $this->generate_form( $args );
		}
		/* Generate the paymentsense_direct button link	 */
		function generate_form( $args = array() )
		{
			global $woocommerce;
			ob_start();
		?>
		<form name="pms<?php echo $args['target'] ?>" action="<?php echo $args['pay_url'] ?>" method="post" target="<?php echo $args['target'] ?>" id="pms<?php echo $args['target'] ?>">
			<?php 
			if(!empty($args['hashdigest']))
			{ ?>
				<input name="ShoppingCartHashDigest" type="hidden" value="<?php echo $args['hashdigest'] ?>">
				<input name="CrossReference" type="hidden" value="<?php echo $args['crossref'] ?>" />
			<?php 
			} 
			else
			{ ?>
				<input name="TermUrl" type="hidden" value="<?php echo $args['term_url'] ?>" />
				<input name="MD" type="hidden" value="<?php echo $args['crossref'] ?>" />
			<?php
			} ?>
			<input name="PaReq" type="hidden" value="<?php echo $args['pareq'] ?>" />
			<?php
			if( 'ACSFrame' == $args['target'] )
			{ ?>
				<iframe id="ACSFrame" name="ACSFrame" src="<?php echo plugins_url('images/AJAXSpinner.gif', __FILE__) ?>" width="100%" height="400"  style="overflow-y:scroll;"></iframe>
			<?php
			} ?>
			<a class="button cancel" href="<?php echo $args['cancel_url'] ?>"><?php _e('Cancel order &amp; restore cart', 'woocommerce') ?></a>
			<script type="text/javascript">
				window.onload=function()
				{
					var auto_refresh = setInterval(function() { submitform(); }, 1000);
					function submitform()
					{
						document.getElementById("pms<?php echo $args['target'] ?>").submit();
						clearTimeout(auto_refresh);
					}
				}
			</script>
		</form>
		<?php

		return ob_get_clean();
	}	

	/* Validate the payment form */	
	function validate_fields() 
	{
		return true;
	}
	
	/* Check passback 3d confirm */	
	function check_passback() 
	{
		global $woocommerce;
		
		$headers = array(
		    'SOAPAction:https://www.thepaymentgateway.net/ThreeDSecureAuthentication',
		    'Content-Type: text/xml; charset = utf-8',
		    'Connection: close'
		);
		if(empty($_GET['paymentsense']) && !empty($_POST['PaRes']) && !empty($_POST['MD']))
		{
			if ($this->debug=='yes')
			{
				$this->log->add( 'paymentsense_direct', "Passback: ". print_r($_POST, true) );
			}
			
			$pareq = $_POST['PaRes'];
			$crossref = $_POST['MD'];
			
			$pay_url = add_query_arg( 'paymentsense', 'confirm', $this->notify_url );
			$pay_url = add_query_arg('key', $_GET['key'], add_query_arg('order', $_GET['order'], $pay_url));
			$target = '_parent';
			$hashdigest = $this->calculateHashDigest($this->generateStringToHash2($pareq, $crossref, $this->secret_key));
			$args['pay_url'] = $pay_url;
			$args['target'] = $target;
			$args['hashdigest'] = $hashdigest;
			$args['term_url'] = '';
			$args['pareq'] = $pareq;
			$args['crossref'] = $crossref;
			$args['cancel_url'] = '';
			
			?>
			<html>
				<head>
					<title>Processing Payment...</title>
					<?php wp_print_scripts(); ?>
				</head>
				<body style="max-height: 400px; overflow: hidden;">
					<img src="<?php echo plugins_url('images/AJAXSpinner.gif', __FILE__) ?>" alt="<?php _e('Redirecting...', 'woocommerce') ?>" />
					<?php
						echo $this->generate_form( $args );?>
				</body>
			</html>
			<?php
			exit;
		}

		$order_id = (int)$_GET['order'];
		$order = new WC_Order( $order_id );
		$redirect = get_permalink(woocommerce_get_page_id('cart')); 

		/*default redirect to cart*/
		if ($this->debug=='yes')
		{
			$this->log->add( 'paymentsense_direct', "Confirm: ". print_r($_POST, true) );
		}
		
		if(!empty($_POST['ShoppingCartHashDigest'])) 
		{ 
			/* last submit*/
			$hashdigest = empty($_POST['ShoppingCartHashDigest']) ? '' : $_POST['ShoppingCartHashDigest'];
			$aVariables = array();
			$aVariables["PaRES"] = $_POST['PaReq'];
			$aVariables["CrossReference"] = $_POST['CrossReference'];
			$MerchantID = $this->merchant_id;
			$Password = $this->password;
			//die($pareq . ' ' . $crossref . ' ' . $MerchantID);
			$xml = '<?xml version="1.0" encoding="utf-8"?>
                    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                        xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                        xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                        <soap:Body>
                            <ThreeDSecureAuthentication xmlns="https://www.thepaymentgateway.net/">
                                <ThreeDSecureMessage>
                                    <MerchantAuthentication MerchantID="'. $MerchantID .'" Password="'. $Password .'" />
                                    <ThreeDSecureInputData CrossReference="'. $aVariables["CrossReference"] .'">
                                        <PaRES>'. $aVariables["PaRES"] .'</PaRES>
                                    </ThreeDSecureInputData>
                                    <PassOutData>Some data to be passed out</PassOutData>
                                </ThreeDSecureMessage>
                            </ThreeDSecureAuthentication>
                        </soap:Body>
                    </soap:Envelope>';
			
			$gwId = 1;
			$domain = "paymentsensegateway.com";
			$port = "4430";
			$transattempt = 1;
			$soapSuccess = false;
			
			while(!$soapSuccess && $gwId <= 3 && $transattempt <= 3) 
			{
			    $url = 'https://gw'.$gwId.'.'.$domain.':'.$port.'/';
			    
			    //initialise cURL
			    $curl = curl_init();
			    
			    //set the options
			    curl_setopt($curl, CURLOPT_HEADER, false);
			    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			    curl_setopt($curl, CURLOPT_POST, true);
			    curl_setopt($curl, CURLOPT_URL, $url);
			    curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);
			    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			    curl_setopt($curl, CURLOPT_ENCODING, 'UTF-8');
			    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			    
			    //Execute cURL request
			    //$ret = returned XML
			    $ret = curl_exec($curl);
			    //$err = returned error number
			    $err = curl_errno($curl);
			    //retHead = returned XML header
			    $retHead = curl_getinfo($curl);
			   // die($ret);
			    //close cURL connection
			    curl_close($curl);
			    $curl = null;
			    
			    if($err == 0) 
			    {
			        //Get the status code
			        $StatusCode = $this->GetXMLValue("StatusCode", $ret, "[0-9]+");
			    
			        if(is_numeric($StatusCode)) 
			        {
			            //request was processed correctly
			            if( $StatusCode != 30 ) 
			            {
			                //set success flag so it will not run the request again.
			                $soapSuccess = true;
			    
			                //collect some of the most commonly used information from the response
			                $szMessage = $this->GetXMLValue("Message", $ret, ".+");
			                $szAuthCode = $this->GetXMLValue("AuthCode", $ret, ".+");
			                $szCrossReference = $this->GetCrossReference($ret);
			                $szAddressNumericCheckResult = $this->GetXMLValue("AddressNumericCheckResult", $ret, ".+");
			                $szPostCodeCheckResult = $this->GetXMLValue("PostCodeCheckResult", $ret, ".+");
			                $szCV2CheckResult = $this->GetXMLValue("CV2CheckResult", $ret, ".+");
			                $szThreeDSecureAuthenticationCheckResult = $this->GetXMLValue("ThreeDSecureAuthenticationCheckResult", $ret, ".+");
			    
			                switch ($StatusCode) {
			                    case 0:
			                        /* status code of 0 - means transaction successful
            							* Successful payment
            							*/							
            							if ('yes'==$this->debug)
            							{
            								$this->log->add( 'paymentsense_direct', 'PaymentSenes payment 3D completed');
            							}
            							
            							
            							if ( ! empty( $szAuthCode ) )
            							{
            								update_post_meta( (int) $order_id, 'AuthCode', $szAuthCode );
            							}
            							
            							$order->add_order_note( __('Paymentsense Direct 3D payment completed', 'woocommerce') );
            							$order->payment_complete();
            							$woocommerce->cart->empty_cart();
            							return (wp_redirect(wc_get_endpoint_url( 'order-received', $order->id, $order->get_checkout_order_received_url() )));
			                        break;
			                    case 5:
			                        $order->get_checkout_payment_url(false);
        							$order->update_status('failed', sprintf( __( 'Paymentsense Direct 3D Secure Password Check Failed .<br />', 'woocommerce' ), strtolower( $szMessage ) ));
        							wc_add_notice(__('Payment Failed due to: ', 'woothemes') . $szMessage. '<br /> Please check your card details and try again.', 'error');
        							return (wp_redirect(wc_get_endpoint_url( 'order-pay', $order->id, $order->get_checkout_payment_url(false)),200));
			                        break;
			                    default:
							
            						$order->get_checkout_payment_url(false);
        							$order->update_status('failed', sprintf( __( 'Unexpected error occured while process payment .<br />', 'woocommerce' ), strtolower( $szMessage ) ));
        							wc_add_notice(__('Payment Failed due to: ', 'woothemes') . $szMessage. '<br /> Please check your card details and try again.', 'error');
        							return (wp_redirect(wc_get_endpoint_url( 'order-pay', $order->id, $order->get_checkout_payment_url(false)),200));
			                        break;
			                }
			            }
			            else 
			            {
			                // status code is 30 - error occured
			                if ($this->debug=='yes')
            				{
            					$this->log->add( 'paymentsense_direct', 'Variable tampering detected' );
            				}
            				wc_add_notice(__('Variable tampering detected', 'woocommerce'));
            				
			            }
			        }
			    }
			}	
		}
		else
		{
			if ($this->debug=='yes')
			{
				$this->log->add( 'paymentsense_direct', 'Empty Shopping Cart Hash Digest' );
			}
			wc_add_notice(__('Empty Shopping Cart Hash Digest', 'woocommerce'));

		}
		
		wp_safe_redirect($redirect);
		
		exit;
	}

	private function calculateHashDigest($szInputString)
	{
	    $hashDigest = md5($szInputString);
	
	    return ($hashDigest);
	}
	
	private function get_request($name)
	{
		if(isset($_REQUEST[$name]))
		{
			return trim($_REQUEST[$name]);
		}
		return NULL;
	}
	
	private function generateStringToHash2($szPaRES, $szCrossReference, $szSecretKey)
	{
	    $szReturnString = "PaRES=".$szPaRES."&CrossReference=".$szCrossReference."&SecretKey=".$szSecretKey;
	}
	
	private function GetXMLValue($XMLElement, $XML, $pattern) {
	    $soapArray = null;
	    $ToReturn = null;
	    if (preg_match('#<'.$XMLElement.'>('.$pattern.')</'.$XMLElement.'>#iU', $XML, $soapArray)) {
	        $ToReturn = $soapArray[1];
	    } else {
	        $ToReturn = $XMLElement . " Not Found";
	    }
	
	    return $ToReturn;
	}
	
	private function GetCrossReference($XML) {
	    $soapArray = null;
	    $ToReturn = null;
	    if (preg_match('#<TransactionOutputData CrossReference="(.+)">#iU', $XML, $soapArray)) {
	        $ToReturn = $soapArray[1];
	    } else {
	        $ToReturn = "No Data Found";
	    }
	
	    return $ToReturn;
	}
	
	private function stripGWInvalidChars($strToCheck) {
	    $toReplace = array("<","&");
	    $replaceWith = array("","&amp;");
	    $cleanString = str_replace($toReplace, $replaceWith, $strToCheck);
	    //die($cleanString);
	    return $cleanString;
	}
	
	protected function getISOCountry($country_code='')
	{
		$countriesArray = array(
			'AL' => '8',
			'DZ' => '12',
			'AS' => '16',
			'AD' => '20',
			'AO' => '24',
			'AI' => '660',
			'AG' => '28',
			'AR' => '32',
			'AM' => '51',
			'AW' => '533',
			'AU' => '36',
			'AT' => '40',
			'AZ' => '31',
			'BS' => '44',
			'BH' => '48',
			'BD' => '50',
			'BB' => '52',
			'BY' => '112',
			'BE' => '56',
			'BZ' => '84',
			'BJ' => '204',
			'BM' => '60',
			'BT' => '64',
			'BO' => '68',
			'BA' => '70',
			'BW' => '72',
			'BR' => '76',
			'BN' => '96',
			'BG' => '100',
			'BF' => '854',
			'BI' => '108',
			'KH' => '116',
			'CM' => '120',
			'CA' => '124',
			'CV' => '132',
			'KY' => '136',
			'CF' => '140',
			'TD' => '148',
			'CL' => '152',
			'CN' => '156',
			'CO' => '170',
			'KM' => '174',
			'CG' => '178',
			'CD' => '180',
			'CK' => '184',
			'CR' => '188',
			'CI' => '384',
			'HR' => '191',
			'CU' => '192',
			'CY' => '196',
			'CZ' => '203',
			'DK' => '208',
			'DJ' => '262',
			'DM' => '212',
			'DO' => '214',
			'EC' => '218',
			'EG' => '818',
			'SV' => '222',
			'GQ' => '226',
			'ER' => '232',
			'EE' => '233',
			'ET' => '231',
			'FK' => '238',
			'FO' => '234',
			'FJ' => '242',
			'FI' => '246',
			'FR' => '250',
			'GF' => '254',
			'PF' => '258',
			'GA' => '266',
			'GM' => '270',
			'GE' => '268',
			'DE' => '276',
			'GH' => '288',
			'GI' => '292',
			'GR' => '300',
			'GL' => '304',
			'GD' => '308',
			'GP' => '312',
			'GU' => '316',
			'GT' => '320',
			'GN' => '324',
			'GW' => '624',
			'GY' => '328',
			'HT' => '332',
			'VA' => '336',
			'HN' => '340',
			'HK' => '344',
			'HU' => '348',
			'IS' => '352',
			'IN' => '356',
			'ID' => '360',
			'IR' => '364',
			'IQ' => '368',
			'IE' => '372',
			'IL' => '376',
			'IT' => '380',
			'JM' => '388',
			'JP' => '392',
			'JO' => '400',
			'KZ' => '398',
			'KE' => '404',
			'KI' => '296',
			'KP' => '408',
			'KR' => '410',
			'KW' => '414',
			'KG' => '417',
			'LA' => '418',
			'LV' => '428',
			'LB' => '422',
			'LS' => '426',
			'LR' => '430',
			'LY' => '434',
			'LI' => '438',
			'LT' => '440',
			'LU' => '442',
			'MO' => '446',
			'MK' => '807',
			'MG' => '450',
			'MW' => '454',
			'MY' => '458',
			'MV' => '462',
			'ML' => '466',
			'MT' => '470',
			'MH' => '584',
			'MQ' => '474',
			'MR' => '478',
			'MU' => '480',
			'MX' => '484',
			'FM' => '583',
			'MD' => '498',
			'MC' => '492',
			'MN' => '496',
			'MS' => '500',
			'MA' => '504',
			'MZ' => '508',
			'MM' => '104',
			'NA' => '516',
			'NR' => '520',
			'NP' => '524',
			'NL' => '528',
			'AN' => '530',
			'NC' => '540',
			'NZ' => '554',
			'NI' => '558',
			'NE' => '562',
			'NG' => '566',
			'NU' => '570',
			'NF' => '574',
			'MP' => '580',
			'NO' => '578',
			'OM' => '512',
			'PK' => '586',
			'PW' => '585',
			'PA' => '591',
			'PG' => '598',
			'PY' => '600',
			'PE' => '604',
			'PH' => '608',
			'PN' => '612',
			'PL' => '616',
			'PT' => '620',
			'PR' => '630',
			'QA' => '634',
			'RE' => '638',
			'RO' => '642',
			'RU' => '643',
			'RW' => '646',
			'SH' => '654',
			'KN' => '659',
			'LC' => '662',
			'PM' => '666',
			'VC' => '670',
			'WS' => '882',
			'SM' => '674',
			'ST' => '678',
			'SA' => '682',
			'SN' => '686',
			'SC' => '690',
			'SL' => '694',
			'SG' => '702',
			'SK' => '703',
			'SI' => '705',
			'SB' => '90',
			'SO' => '706',
			'ZA' => '710',
			'ES' => '724',
			'LK' => '144',
			'SD' => '736',
			'SR' => '740',
			'SJ' => '744',
			'SZ' => '748',
			'SE' => '752',
			'CH' => '756',
			'SY' => '760',
			'TW' => '158',
			'TJ' => '762',
			'TZ' => '834',
			'TH' => '764',
			'TG' => '768',
			'TK' => '772',
			'TO' => '776',
			'TT' => '780',
			'TN' => '788',
			'TR' => '792',
			'TM' => '795',
			'TC' => '796',
			'TV' => '798',
			'UG' => '800',	
			'UA' => '804',
			'AE' => '784',
			'GB' => '826',
			'US' => '840',
			'UY' => '858',
			'UZ' => '860',
			'VU' => '548',
			'VE' => '862',
			'VN' => '704',
			'VG' => '92',
			'VI' => '850',
			'WF' => '876',
			'EH' => '732',
			'YE' => '887',
			'ZM' => '894',
			'ZW' => '716'
		);
		
		if(!empty($countriesArray[$country_code]))
		{
			return $countriesArray[$country_code];
		}
		return '';
	}
} // end woocommerce_paymentsense_direct