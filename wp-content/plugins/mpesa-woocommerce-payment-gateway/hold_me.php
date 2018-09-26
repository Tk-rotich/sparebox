<?php
/*
	Plugin Name: Mpesa Rest-Api
	Plugin URI: https://github.com/Tk-rotich/Mpesa-Rest-Api
	Description: Mpesa Pay Online STK push for WooCommerce Payment Gateway allows you to accept payments from Mpesa customers(REST).
	Version: 1.1.0
	Author: Titus Kiprotich
	Author URI: https://github.com/Tk-rotich/
	License:           GPL-2.0+
 	License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 	GitHub Plugin URI: https://github.com/Tk-rotich/Mpesa-Rest-Api
*/

if ( ! defined( 'ABSPATH' ) )
	exit;

add_action( 'plugins_loaded', 'mpo_wc_mpesa_pay_init', 0 );

function mpo_wc_mpesa_pay_init() {

	if ( ! class_exists( 'WC_Payment_Gateway' ) ) return;

	/**
 	 * Gateway class
 	 */
	class WC_Mpesa_Payonline_Gateway extends WC_Payment_Gateway {

		public function __construct() {

			$this->id 					= 'mpo_mpesa_pay_gateway';
    		$this->icon 				= apply_filters( 'woocommerce_mpesa_pay_icon', plugins_url( 'assets/images/mpesa_pay-icon.png' , __FILE__ ) );
			$this->has_fields 			= false;
			$this->order_button_text    = 'Make Payment';
			$this->notify_url        	= WC()->api_request_url( 'WC_Mpesa_Payonline_Gateway' );
        	$this->method_title     	= 'Lipa Na Mpesa';
        	$this->method_description  	= 'Allow your cuustomers to pay through Mpesa Pay Online';

			$this->init_form_fields();
			$this->init_settings();

			// Define user set variables
			$this->title 				= $this->get_option( 'title' );
			$this->description 			= $this->get_option( 'description' );
			$this->logo_url				= $this->get_option( 'logo_url' );
			$this->testmode             = $this->get_option( 'testmode' ) === 'yes' ? true : false;

			$this->CONSUMER_KEY_live  	= $this->get_option( 'CONSUMER_KEY_live' );
			$this->CONSUMER_KEY_test  	= $this->get_option( 'CONSUMER_KEY_test' );

			$this->CONSUMER_SECRET_live  	= $this->get_option( 'CONSUMER_SECRET_live' );
			$this->CONSUMER_SECRET_test  	= $this->get_option( 'CONSUMER_SECRET_test' );

			$this->ShortCode_live			= $this->get_option( 'ShortCode_live' );
			$this->ShortCode_test			= $this->get_option( 'ShortCode_test' );

			$this->PassKey_live			= $this->get_option( 'PassKey_live' );
			$this->PassKey_test			= $this->get_option( 'PassKey_test' );  

			$this->AccessToken_Url_live			= $this->get_option( 'AccessToken_Url_live' );
			$this->AccessToken_Url_test			= $this->get_option( 'AccessToken_Url_test' );

			$this->MpesaPay_Url_live			= $this->get_option( 'MpesaPay_Url_live' );
			$this->MpesaPay_Url_test			= $this->get_option( 'MpesaPay_Url_test' );




			$this->CONSUMER_KEY      		= $this->testmode ? $this->CONSUMER_KEY_test : $this->CONSUMER_KEY_live;
			$this->CONSUMER_SECRET      	= $this->testmode ? $this->CONSUMER_SECRET_test : $this->CONSUMER_SECRET_live;
			$this->ShortCode				= $this->testmode ? $this->ShortCode_test : $this->ShortCode_live;
			$this->PassKey			      	= $this->testmode ? $this->PassKey_test : $this->PassKey_live;
			$this->AccessToken_Url      	= $this->testmode ? $this->AccessToken_Url_test : $this->AccessToken_Url_live;
			$this->MpesaPay_Url		      	= $this->testmode ? $this->MpesaPay_Url_test : $this->MpesaPay_Url_live;

			$this->_datetime  = $this->getDateTime();


			//Actions
			add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
			//add_action( 'woocommerce_receipt_mpo_simplepay_gateway', array( $this, 'receipt_page' ) );
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			// Payment listener/API hook
			add_action( 'woocommerce_api_wc_mpo_mpesapay_gateway', array( $this, 'charge_token' ) );

			// Check if the gateway can be used
			if ( ! $this->is_valid_for_use() ) {
				$this->enabled = false;
			}

		}


		/**
	 	* Check if the store curreny is set to Ksh
	 	**/
		public function is_valid_for_use() {

			if( ! in_array( get_woocommerce_currency(), array( 'KES' ) ) ) {
				$this->msg = 'Mpesa Online Payment doesn\'t support your store currency, set it to Kenya Shillings(Ksh) &#8358; <a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=wc-settings&tab=general">here</a>';
				return false;
			}

			return true;

		}
		/**
		 * Check if this gateway is enabled
		 */
		public function is_available() {

			if ( $this->enabled == "yes" ) {

				if ( ! ( $this->CONSUMER_KEY && $this->CONSUMER_SECRET && $this->ShortCode && $this->PassKey && $this->AccessToken_Url && $this->MpesaPay_Url ) ) {
					return false;
				}
				return true;
			}
			return false;
		}
        /**
         * Admin Panel Options
         **/
        public function admin_options() {

            echo '<h3>MpesaPayOnline</h3>';
            echo '<p>MpesaPayOnline WooCommerce Payment Gateway allows you to accept Mpesa payment on your WooCommerce store</p>';
            echo '<p>For more information click <a href=" https://github.com/Tk-rotich/Mpesa-Rest-Api" target="_blank">here</a>';

			if ( $this->is_valid_for_use() ) {
	            echo '<table class="form-table">';
	            $this->generate_settings_html();
	            echo '</table>';

            } else {	 ?>

				<div class="inline error"><p><strong>Mpesa Payment Gateway Disabled</strong>: <?php echo $this->msg ?></p></div>

			<?php }

        }


	    /**
	     * Initialise Gateway Settings Form Fields
	    **/
		function init_form_fields() {

			$this->form_fields = array(
				'enabled' => array(
					'title' 		=> 'Enable/Disable',
					'type' 			=> 'checkbox',
					'label' 		=> 'Enable Mpesa Payonline Payment Gateway',
					'description' 	=> 'Enable or disable the gateway.',
            		'desc_tip'      => true,
					'default' 		=> 'yes'
				),
				'testmode' => array(
					'title'       		=> 'Test Mode',
					'type'        		=> 'checkbox',
					'label'       		=> 'Enable Test Mode',
					'default'     		=> 'no',
					'description' 		=> 'Test mode enables you to test payments before going live. <br />If you ready to start receiving payment on your site, kindly uncheck this.',
				),
				'title' => array(
					'title' 		=> 'Title',
					'type' 			=> 'text',
					'description' 	=> 'This controls the title which the user sees during checkout.',
        			'desc_tip'      => false,
					'default' 		=> 'Mpesa PayBill'
				),
				'description' => array(
					'title' 		=> 'Description',
					'type' 			=> 'textarea',
					'description' 	=> 'This controls the description which the user sees during checkout.',
					'default' 		=> 'Mpesa Online Payment'
				),
				'testing' => array(
					'title'       	=> 'Gateway Test Credentials',
					'type'        	=> 'title',
					'description' 	=> '',
				),
			
				'CONSUMER_KEY_test' => array(
					'title'       => 'Consumer Test Key',
					'type'        => 'password',
					'description' => 'Enter your Consumer Test Key here.',
					'default'     => ''
				),
				'CONSUMER_SECRET_test' => array(
					'title'       => 'Consumer Secret Test Key',
					'type'        => 'password',
					'description' => 'Enter your Consumer Key here',
					'default'     => ''
				),

				'ShortCode_test' => array(
					'title'       => 'Shortcode Test',
					'type'        => 'text',
					'description' => 'Enter your Mpesa Test Shortcode here.',
					'default'     => ''
				),
				'PassKey_test' => array(
					'title'       => 'PassKey Test',
					'type'        => 'text',
					'description' => 'Enter PassKey.',
					'default'     => ''
				),
				'AccessToken_Url_test' => array(
					'title'       => 'Access Token URL for Test',
					'type'        => 'text',
					'description' => 'Enter Test Access Token URL here.',
					'default'     => ''
				),
				'MpesaPay_Url_test' => array(
					'title'       => 'Test Mpesa URL',
					'type'        => 'text',
					'description' => 'Enter Mpesa Test URL here.',
					'default'     => ''
				),
				'live' => array(
					'title'       	=> 'Gateway Live/Production Credentials',
					'type'        	=> 'title',
					'description' 	=> '',
				),
				'CONSUMER_KEY_live' => array(
					'title'       => 'Consumer Live Key',
					'type'        => 'password',
					'description' => 'Enter your Consumer Live Key here.',
					'default'     => ''
				),
				'CONSUMER_SECRET_live' => array(
					'title'       => 'Consumer Secret Live Key',
					'type'        => 'password',
					'description' => 'Enter your Consumer Live Key here.',
					'default'     => ''
				),
				'ShortCode_live' => array(
					'title'       => 'Shortcode Live',
					'type'        => 'text',
					'description' => 'Enter your Mpesa Live Shortcode here.',
					'default'     => ''
				),
				'PassKey_live' => array(
					'title'       => 'PassKey Live',
					'type'        => 'text',
					'description' => 'Enter PassKey.',
					'default'     => ''
				),
				'AccessToken_Url_live' => array(
					'title'       => 'Access Token URL',
					'type'        => 'text',
					'description' => 'Enter Live Access Token URL here.',
					'default'     => ''
				),
				'MpesaPay_Url_live' => array(
					'title'       => 'Live Mpesa URL',
					'type'        => 'text',
					'description' => 'Enter Mpesa URL here.',
					'default'     => ''
				)
				
			);

		}

		// ********************************Mpesa pay functions************************************************

		function getAccessToken(){
			$url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
	
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			$credentials = base64_encode($this->CONSUMER_KEY.':'.$this->CONSUMER_SECRET);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials)); //setting a custom header
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	
			$curl_response = curl_exec($curl);
			$result = json_decode($curl_response);
			$access_token = $result->access_token;
			
			return $access_token;
		}
	
		function getDateTime(){
			$date = new DateTime();
			$datetime = $date->format('YmdHis');
			return $datetime;
		}
	
		function getPassword(){ 
			$password   = base64_encode($this->Shortcode.$this->PassKey.$this->_datetime);
			return $password;
		}
	


		/**
		 * Outputs scripts used for Mpesa payment
		 */
		public function payment_scripts() {

			if ( ! is_checkout_pay_page() ) {
				return;
			}

			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			// wp_enqueue_script( 'mpo_mpesa_pay', 'https://checkout.simplepay.ng/v2/simplepay.js', array( 'jquery' ), '1.0.0', true );

			wp_enqueue_script( 'wc_mpesa_pay', plugins_url( 'assets/js/mpesa-pay'. $suffix . '.js', __FILE__ ), array( 'mpo_mpesa_pay' ), '1.0.0', true );

			if ( is_checkout_pay_page() && get_query_var( 'order-pay' ) ) {



			// 	$order_key 			= urldecode( $_GET['key'] );
			// 	$order_id  			= absint( get_query_var( 'order-pay' ) );

			// 	$order        		= wc_get_order( $order_id );

			// 	$email  			= method_exists( $order, 'get_billing_email' ) ? $order->get_billing_email() : $order->billing_email;
			// 	$billing_address_1 	= method_exists( $order, 'get_billing_address_1' ) ? $order->get_billing_address_1() : $order->billing_address_1;
			// 	$billing_address_2 	= method_exists( $order, 'get_billing_address_2' ) ? $order->get_billing_address_2() : $order->billing_address_2;
			// 	$city  				= method_exists( $order, 'get_billing_city' ) ? $order->get_billing_city() : $order->billing_city;
			// 	$country  			= method_exists( $order, 'get_billing_country' ) ? $order->get_billing_country() : $order->billing_country;

			// 	$amount 			= $order->get_total();
			// 	$address 			= $billing_address_1 . ' ' . $billing_address_2;

			// 	$description 		= 'Payment for Order #' . $order_id;

	        //     $the_order_id 		= method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
	        //     $the_order_key 		= method_exists( $order, 'get_order_key' ) ? $order->get_order_key() : $order->order_key;

			// 	if ( $the_order_id == $order_id && $the_order_key == $order_key ) {
			// 		$mpesapay_params['key'] 			= $this->consumer_key;
			// 		$mpesapay_params['email'] 			= $email;
			// 		$mpesapay_params['address'] 		= $address;
			// 		$mpesapay_params['city'] 			= $city;
			// 		$mpesapay_params['country'] 		= $country;
			// 		$mpesapay_params['amount']  		= $amount;
			// 		$mpesapay_params['order_id']  		= $order_id;
			// 		$mpesapay_params['description']	= $description;
			// 		$mpesapay_params['currency']  		= 'Ksh';
			// 		$mpesapay_params['logo']			= $this->logo_url;
			// 	}

			// }

			// wp_localize_script( 'wc_mpesapay', 'wc_mpesapay_params', $mpesapay_params );

		}


	    /**
	     * Process the payment and return the result
	    **/
		function process_payment( $order_id ) {

			$order 			= wc_get_order( $order_id );

			return array(
	        	'result' 	=> 'success',
				'redirect'	=> $order->get_checkout_payment_url( true )
	        );

		}


	    /**
	     * Output for the order received page.
	    **/
		function receipt_page( $order_id ) {

			$order = wc_get_order( $order_id );

			echo '<p>Thank you for your order, Click the button below to pay with Mpesa.</p>';

			echo '<div id="mpesapay_form"><form id="order_review" method="post" action="'. WC()->api_request_url( 'WC_Mpesa_Payonline_Gateway' ) .'"></form><button class="button alt" id="mpesapay-payment-button">Pay Now</button> <a class="button cancel" href="' . esc_url( $order->get_cancel_order_url() ) . '">Cancel order &amp; restore cart</a></div>
			';
		}


		/**
		 * Verify a payment token
		**/
		function charge_token() {

			if( isset( $_POST['wc_mpesapay_token'], $_POST['wc_mpesapay_order_id'] ) ) {

				$verify_url 	= 'https://checkout.mpesa_pay.co.ke';

				$order_id 		= (int) $_POST['wc_mpesapay_order_id'];

				$order 			= wc_get_order( $order_id );
		        $order_total	= $order->get_total(); // * 100

				$headers = array(
					'Content-Type'	=> 'application/json',
					'Authorization' => 'Basic ' . base64_encode( $this->consumer_secret_key . ':' . '' )
				);

				$body = array(
					'BusinessShortCode' => '174379',
					'Password'			=> '',
					'Timestamp'			=> '',
					'TransactionType'	=> 'CustomerPayBillOnline',
					'Amount'			=>  $order_total,
					'PartyA'			=> '254724637806',
					'PartyB'			=> '174379',
					'PhoneNumber'		=> '254724637806',
					'CallBackURL'		=>  get_site_url().'/wc-api/wc_gateway_my_gateway/';          #'https://tough-impala-62.localtunnel.me/mpesa/index.php',
					'AccountReference'	=> 'Titus',
					'TransactionDesc'	=> 'account',

				);

				$args = array(
					'headers'	=> $headers,
					'body'		=> json_encode( $body ),
					'timeout'	=> 60,
					'method'	=> 'POST'
				);

				$request = wp_remote_post( $verify_url, $args );

		        if ( ! is_wp_error( $request ) && 200 == wp_remote_retrieve_response_code( $request ) ) {

	        		$simplepay_response = json_decode( wp_remote_retrieve_body( $request ) );

	        		$amount_paid 		= $simplepay_response->amount;
	        		$transaction_id		= $simplepay_response->id;

                	do_action( 'tbz_wc_simplepay_after_payment', $simplepay_response );

					if( '20000' == $simplepay_response->response_code ) {

						// check if the amount paid is equal to the order amount.
						if( $amount_paid < $order_total ) {

			                //Update the order status
							$order->update_status( 'on-hold', '' );

							add_post_meta( $order_id, '_transaction_id', $transaction_id, true );

							//Error Note
							$notice = 'Thank you for shopping with us.<br />The payment was successful, but the amount paid is not the same as the order amount.<br />Your order is currently on-hold.<br />Kindly contact us for more information regarding your order and payment status.';

							$notice_type = 'notice';

		                    //Add Admin Order Note
		                    $order->add_order_note( 'Look into this order. <br />This order is currently on hold.<br />Reason: Amount paid is less than the order amount.<br />Amount Paid was &#8358;'. $amount_paid/100 .' while the order amount is &#8358;'. $order_total/100 .'<br />Simplepay Transaction ID: '.$transaction_id );

							// Reduce stock levels
							$order->reduce_order_stock();

							wc_add_notice( $notice, $notice_type );

						} else {

							$order->payment_complete( $transaction_id );

							$order->add_order_note( sprintf( 'Payment via Mpesa was successful, (Transaction ID: %s)', $transaction_id ) );
		                }

						wc_empty_cart();

						wp_redirect( $this->get_return_url( $order ) );

						exit;

					} else {

						wp_redirect( wc_get_page_permalink( 'checkout' ) );

						exit;
		            }

		        }

			}

			wp_redirect( wc_get_page_permalink( 'checkout' ) );

			exit;

		}

	}


	/**
 	* Add Mpesa Gateway to WC
 	**/
	function mpo_wc_add_mpesapay_gateway( $methods ) {

		$methods[] = 'WC_Mpesa_Payonline_Gateway';
		return $methods;

	}
	add_filter('woocommerce_payment_gateways', 'mpo_wc_add_mpesapay_gateway' );


	/**
	* Add Settings link to the plugin entry in the plugins menu
	**/
	function mpo_mpesa_pay_plugin_action_links( $links, $file ) {

	    static $this_plugin;

	    if ( ! $this_plugin ) {

	        $this_plugin = plugin_basename( __FILE__ );

	    }

	    if ( $file == $this_plugin ) {

	        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=wc-settings&tab=checkout&section=wc_mpo_mpesapay_gateway">Settings</a>';
	        array_unshift($links, $settings_link);

	    }

	    return $links;

	}
	add_filter( 'plugin_action_links', 'mpo_mpesa_pay_plugin_action_links', 10, 2 );


	/**
 	* Display the testmode notice
 	**/
	function mpo_wc_mpesa_pay_testmode_notice() {

		$mpesa_pay_settings = get_option( 'woocommerce_mpo_mpesa_pay_gateway_settings' );

		$testmode 			= $mpesa_pay_settings['testmode'] === 'yes' ? true : false;

		$consumer_test_key  	= $mpesa_pay_settings['consumer_test_key'];
		$consumer_secret_test_key  	= $mpesa_pay_settings['consumer_secret_test_key'];

		$consumer_live_key  	= $mpesa_pay_settings['consumer_live_key'];
		$consumer_secret_live_key  	= $mpesa_pay_settings['consumer_secret_live_key'];

		$consumer_key      	= $testmode ? $consumer_test_key : $consumer_live_key;
		$consumer_secret_key      	= $testmode ? $consumer_secret_test_key : $consumer_secret_live_key;

		if ( $testmode ) {
	    ?>
		    <div class="update-nag">
		        Mpesa PayOnline testmode enabled!!!. Click <a href="<?php echo get_bloginfo('wpurl') ?>/wp-admin/admin.php?page=wc-settings&tab=checkout&section=mpo_mpesa_pay_gateway">here</a> to disable it when you are going live, To start accepting live payments on your site.
		    </div>
	    <?php
		}

		// Check required fields
		if ( ! ( $consumer_key && $consumer_secret_key ) ) {
			echo '<div class="error"><p>' . sprintf( 'Please enter your Mpesa API keys <a href="%s">here</a> to be able to use Mpesa Payonline WooCommerce plugin.', admin_url( 'admin.php?page=wc-settings&tab=checkout&section=mpo_mpesa_pay_gateway' ) ) . '</p></div>';
		}

	}
	add_action( 'admin_notices', 'mpo_wc_mpesa_pay_testmode_notice' );

}

// 'CallBackURL'		=>  get_site_url().'/wc-api/wc_gateway_my_gateway/',