<?php
/*
 * @link              https://www.alphapay.com.br/
 * @since             1.0.0
 * @package           Gateway_AlphaPay_BR_WP
 *
 * @wordpress-plugin
 * Plugin Name:       AlphaPay Gateway Brazil
 * Plugin URI:        https://br.wordpress.org/plugins/search/gateway-alphapay-br-wp/
 * Description: O AlphaPay for WooCommerce é a melhor forma de receber pagamentos online por cartão de crédito, sendo possível o cliente fazer todo o pagamento sem sair da sua loja WooCommerce.
 * Version:           1.0.0
 * Author:            AlphaPay
 * Author URI:        https://www.alphapay.com.br/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gateway-alphapay-br-wp
 * Domain Path:       /languages
*/
if(!defined('ABSPATH'))
    exit();
define('WC_ALPHA_PAY_ID', 'alpha');
define('WC_ALPHA_DIR', rtrim(plugin_dir_path(__FILE__), '/'));
define('WC_ALPHA_URL', rtrim(plugin_dir_url(__FILE__), '/'));

add_filter( 'woocommerce_payment_gateways', 'Alpha_add_gateway_class' );

function Alpha_add_gateway_class( $gateways ) {
	$gateways[] = 'WC_Alpha_Gateway';
	return $gateways;
}

function getIP() {
    $ip = $_SERVER['SERVER_ADDR'];

    if (PHP_OS == 'WINNT'){
        $ip = getHostByName(getHostName());
    }

    if (PHP_OS == 'Linux'){
        $command="/sbin/ifconfig";
        exec($command, $output);
        // var_dump($output);
        $pattern = '/inet addr:?([^ ]+)/';

        $ip = array();
        foreach ($output as $key => $subject) {
            $result = preg_match_all($pattern, $subject, $subpattern);
            if ($result == 1) {
                if ($subpattern[1][0] != "127.0.0.1")
                $ip = $subpattern[1][0];
            }
        }
    }
    return $ip;
}

   function hide_slug_options() {
    global $post;
    global $pagenow;
    $hide_slugs = "<style type=\"text/css\">table#list-table:nth-child(3),#list-table:nth-child(3), tr:nth-child(3), [for=\"list-table:nth-child(3)\"] { display: none; }</style>\n";
    if (is_admin() && $pagenow=='post-new.php' OR $pagenow=='post.php') print($hide_slugs);
   }
   add_action( 'admin_head', 'hide_slug_options'  );

// Adding a custom checkout date field
add_filter( 'woocommerce_billing_fields', 'add_birth_date_billing_field', 20, 1 );
function add_birth_date_billing_field($billing_fields) {
    $billing_fields['billing_birthdate'] = array(
        'label'       => __('Nascimento DD/MM/AAAA'),
		'class'       => array('form-row-first'),
        'priority'    => 25,
        'required'    => true,
        'clear'       => true,
    );
    return $billing_fields;
}

//register_activation_hook(__FILE__, 'cyb_activation');
function cyb_activation()
{
    exit( wp_redirect( admin_url( 'admin.php?page=wc-settings&tab=checkout&section='.WC_ALPHA_PAY_ID ) ) );
}

add_action( 'activated_plugin', 'cyb_activation' );
add_action( 'plugins_loaded', 'Alpha_init_gateway_class' );
function Alpha_init_gateway_class() {

	class WC_Alpha_Gateway extends WC_Payment_Gateway {

		private $SUCCESS_CALLBACK_URL = "Alpha_payment_success";
		private $FAILURE_CALLBACK_URL = "Alpha_payment_failure";
		private $SUCCESS_REDIRECT_URL = "/checkout/order-received/";
		private $FAILURE_REDIRECT_URL = "/checkout/order-received/";
		private $API_HOST = '';
		private $API_SESSION_CREATE_ENDPOINT = "";

 		public function __construct() {
 
			$this->id = WC_ALPHA_PAY_ID; 
			$this->icon ='';    
			$this->has_fields = true;
			$this->method_title = 'Alpha Payment Gateway Plugin';
			$this->method_description = 'Alpha Payment Gateway Plugin.';  
			$this->supports = array(
               'products'
	        );

			$this->init_form_fields();
			$this->init_settings();


			$this->title = $this->get_option( 'title' );
			$this->description = $this->get_option( 'description' );

			$this->testmode = 'yes' === $this->get_option( 'testmode' );
			$this->merchant_id = $this->testmode ? $this->get_option( 'test_merchant_id' ) : $this->get_option( 'merchant_id' );
			$this->auth_token = $this->testmode ? $this->get_option( 'test_auth_token' ) : $this->get_option( 'auth_token' );

			$this->api_address = $this->get_option('api_address');
			$this->api_key = $this->get_option('api_key');
			
			if($this->api_key == '' || $this->api_key == 'undefined' || $this->api_key == 'null') {
				$this->update_option('enabled', 'no');
			}
			// Site URL
			$this->siteUrl = get_site_url(); 

			// This action hook saves the settings

			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action('woocommerce_checkout_update_order_meta', array($this, 'custom_payment_update_order_meta'));

 		}

 		public function init_form_fields()
		        {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => 'Habilitar/Disabilitar',
                    'label' => 'Habilitar o AlphaPay Gateway',
                    'type' => 'checkbox',
                    'description' => 'Por favor em caso de dúvida, verifique informações em: <a href=\"https://developer.alphapay.com.br/docs/\" target="_blank">https://developer.alphapay.com.br/docs</a>',
                    'default' => 'no'
                ) ,
                'title' => array(
                    'title' => 'Título',
                    'type' => 'text',
                    'description' => 'Isto controle o título que é exibido no checkout.',
                    'default' => 'Alpha',
                    'desc_tip' => true,
                ) ,
                'description' => array(
                    'title' => __('Descrição', 'woocommerce') ,
                    'type' => 'textarea',
                    'description' => __('Descrição a ser exibida durante o checkout.', 'woocommerce') ,
                    'default' => __('Cartões de Crédito', 'woocommerce') ,
                    'desc_tip' => true,
                ) ,
                // 'testmode' => array(
                // 'title'       => 'Modo de teste',
                // 'label'       => 'Habilitar',
                // 'type'        => 'checkbox',
                // 'description' => 'Place the payment gateway in test mode using test API keys.',
                // 'default'     => 'yes',
                // 'desc_tip'    => true,
                // ),
                // 'test_merchant_id' => array(
                // 'title'       => 'Test MerchantID',
                // 'type'        => 'text',
                // 'placeholder' => 'Enter Test MerchantID'
                // ),
                // 'test_auth_token' => array(
                // 'title'       => 'Test Auth Token',
                // 'type'        => 'text',
                // 'placeholder' => 'Enter Test Auth Token'
                // ),
                // 'merchant_id' => array(
                // 'title' => 'Nome de usuário da Live API',
                // 'type' => 'text',
                // 'placeholder' => 'Enter Live MerchantID'
                // ) ,
                // 'auth_token' => array(
                // 'title' => 'Token da Live API',
                // 'type' => 'text',
                // 'placeholder' => 'Enter Live Auth Token'
                // ) ,
                'api_address' => array(
                    'title' => 'Endereço da Live API',
                    'type' => 'text',
                    'placeholder' => 'Endereço completo da Live API'
                ) ,
                'api_key' => array(
                    'title' => 'KEY da Live API',
                    'type' => 'text',
                    'placeholder' => 'Chave única recebida em sua ativação de conta',
                    'description' => 'Ainda não é nosso cliente e quer utilizar nossos serviços? <a href=\"https://www.alphapay.com.br/\" target="_blank">Solicite sua filiação!</a>',
                    'default' => ''
                )
            );
        }
		public function payment_fields() {
			if ( $this->description ) {
				if ( $this->testmode ) {
					$this->description .= ' TEST MODE ENABLED. In test mode, you can use theese ';
					$this->description  = trim( $this->description );
				}
				echo wpautop( wp_kses_post( $this->description ) );
			}
		 
			echo '<fieldset id="wc-' . esc_attr( $this->id ) . '-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent;">';
				do_action( 'woocommerce_credit_card_form_start', $this->id );
					echo '
						<style>
							.wc_payment_methods .payment_box p {
								padding: 15px;
								margin: 0;
								text-align: center;
								font-family: NonBreakingSpaceOverride, "Hoefler Text", Garamond, "Times New Roman", serif;
							}
							.form-row {
								margin-bottom: 10px;
							}
							select {
								border-color: #dcd7ca;
								width: 100%!important;
							    background: #fff;
							    border-radius: 0;
							    border-style: solid;
							    border-width: 0.1rem;
							    box-shadow: none;
							    display: block;
							    font-size: 1.6rem;
							    letter-spacing: -0.015em;
							    margin: 0;
							    max-width: 100%;
							    padding: 1.5rem 1.8rem;
							    width: 100%;
							}
							.alpha-logos {
								display: flex;
								justify-content: space-between;
							}
							.alpha-logos img{
									height: 50px;
									width: 90%;
									opacity: 0.3;
							}
							.alpha-logos label {
								display: flex!important;
								align-items: center;
							}
							.alpha-logos input[type=radio] { 
							  position: absolute;
							  opacity: 0;
							  width: 0;
							  height: 0;
							}

							.alpha-logos input[type=radio] + img {
							  cursor: pointer;
							}
							.alpha-logos label:hover img {
							      opacity: 1;
							}
							.alpha-logos input[type=radio]:checked + img {
								  opacity: 1;
							}
						</style>
						<script type="text/javascript">
					      function selectOnlyThis(id) {
                           for (var i = 1;i <= 8; i++)
                           {
                           document.getElementById(i).checked = false;
                           }
                           document.getElementById(id).checked = true;
                           }
						</script>
						<script type="text/javascript">
							var Alphaexpdate_input = document.querySelectorAll(".Alpha-expdate")[0];
							var Alphaexpdate_input_dateInputMask = function Alphaexpdate_input_dateInputMask(elm) {
							  elm.addEventListener("keypress", function(e) {
							    if(e.keyCode < 47 || e.keyCode > 57) {
							      e.preventDefault();
							    }
							    var len = elm.value.length;
							    if(len !== 2 || len !== 4) {
							      if(e.keyCode == 47) {
							        e.preventDefault();
							      }
							    }
							    if(len === 2) {
							      elm.value += "/20";
							    }
							  });
							};
							Alphaexpdate_input_dateInputMask(Alphaexpdate_input);

							var Alpha_cvv_input = document.querySelectorAll(".Alpha-cvv")[0];
							var alphacvvMask = function alphacvvMask(elm) {
							  elm.addEventListener("keypress", function(e) {
							    if(e.keyCode < 47 || e.keyCode > 57) {
							      e.preventDefault();
							    }
							  });
							};
							alphacvvMask(Alpha_cvv_input);
						</script>
				        <div class="form-row form-row-wide alpha-logos">
							 <label style="">
				                <input type="radio" id="1" name="Alpha_paytype" value="mastercard" onclick="selectOnlyThis(this.id)" checked/>
				                <img src="'. WC_ALPHA_URL .'/images/mastercard.svg" style="max-height: 100%">
				            </label>
				            <label style="">
				                <input type="radio" id="2" name="Alpha_paytype" value="visa" onclick="selectOnlyThis(this.id)"/>
				                <img src="'. WC_ALPHA_URL .'/images/visa.svg" style="max-height: 100%">
				            </label>
				            <label style="">
				                <input type="radio" id="3" name="Alpha_paytype" value="amex" onclick="selectOnlyThis(this.id)"/>
				                <img src="'. WC_ALPHA_URL .'/images/amex.svg" style="max-height: 100%">
				            </label>
				            <label style="">
				                <input type="radio" id="4" name="Alpha_paytype" value="elo" onclick="selectOnlyThis(this.id)"/>
				                <img src="'. WC_ALPHA_URL .'/images/elo.svg" style="max-height: 100%">
				            </label>
				            <label style="">
				                <input type="radio" id="5" name="Alpha_paytype" value="diners" onclick="selectOnlyThis(this.id)"/>
				                <img src="'. WC_ALPHA_URL .'/images/diners.svg" style="max-height: 100%">
				            </label>
					    <label style="">
				                <input type="radio" id="6" name="Alpha_paytype" value="discover" onclick="selectOnlyThis(this.id)"/>
				                <img src="'. WC_ALPHA_URL .'/images/discover.svg" style="max-height: 100%">
				            </label>
				            <label style="">
				                <input type="radio" id="7" name="Alpha_paytype" value="hipercard" onclick="selectOnlyThis(this.id)"/>
				                <img src="'. WC_ALPHA_URL .'/images/hiper.svg" style="max-height: 100%">
				            </label>
				            <label style="">
				                <input type="radio" id="8" name="Alpha_paytype" value="jcb" onclick="selectOnlyThis(this.id)"/>
				                <img src="'. WC_ALPHA_URL .'/images/jcb.svg" style="max-height: 100%">
				            </label>
				        </div>
						<div class="form-row form-row-wide"><label>Número do cartão <span class="required">*</span></label>
						<input id="Alpha_ccNo" name="Alpha_ccNo" type="text" autocomplete="off" maxlength="16">
						</div>
				                <div class="form-row form-row-wide"><label>Nome completo (igual ao cartão) <span class="required">*</span></label>
						<input id="Alpha_name" name="Alpha_name" type="text" autocomplete="off">
						</div>
						<div class="form-row form-row-first">
							<label>Vencimento (MM/20AA) <span class="required">*</span></label>
							<input id="Alpha_expdate" class="Alpha-expdate" name="Alpha_expdate" type="text" autocomplete="off" pattern="[0-9]{2}/[0-9]{4}" placeholder="MM / 20AA" maxlength="7">
						</div>
						<div class="form-row form-row-last">
							<label>Código de segurança <span class="required">*</span></label>
							<input id="Alpha_cvv" name="Alpha_cvv" class="Alpha-cvv" type="password" autocomplete="off" placeholder="CVV" minlength="2" maxlength="4" pattern="[0-9]">
						</div>
						<div class="form-row form-row-wide"><label>Parcelamento <span class="required">*</span></label>
						<select name="Alpha_installments">
							<option value="1">1x</option><option value="2">2x</option><option value="3">3x</option><option value="4">4x</option><option value="5">5x</option><option value="6">6x</option><option value="7">7x</option><option value="8">8x</option><option value="9">9x</option><option value="10">10x</option><option value="11">11x</option><option value="12">12x</option>

						<select>
						</div>
						<div class="clear"></div>';
				do_action( 'woocommerce_credit_card_form_end', $this->id );
			echo '<div class="clear"></div></fieldset>';
 		}

        function custom_payment_update_order_meta($order_id){
	        if($_POST['payment_method'] != WC_ALPHA_PAY_ID){
	            return;
	        }
	        $payType = sanitize_text_field($_POST['Alpha_paytype']);
	        $holderName = sanitize_text_field($_POST['Alpha_name']);
	        $exdate = sanitize_text_field($_POST['Alpha_expdate']);
	        $ccNo = sanitize_text_field($_POST['Alpha_ccNo']);
	        $cvv = sanitize_text_field($_POST['Alpha_cvv']);
	        $installments = sanitize_text_field($_POST['Alpha_installments']);
	        $billing_birthdate = wp_strip_all_tags(sanitize_text_field($_POST['billing_birthdate']));
	        $billing_cpf = sanitize_text_field($_POST['billing_cpf']);

	        update_post_meta($order_id, 'payType', $payType);
	        update_post_meta($order_id, 'holderName', $holderName);
	        update_post_meta($order_id, 'exdate', $exdate);
	        update_post_meta($order_id, 'ccNo', $ccNo);
	        update_post_meta($order_id, 'cvv', $cvv);
	        update_post_meta($order_id, 'installments', $installments);
	        update_post_meta($order_id, 'billing_birthdate', $billing_birthdate);
	        update_post_meta($order_id, 'billing_cpf', $billing_cpf);

	    }
	 	public function payment_scripts() {
 
	 	}
		public function validate_fields(){
		 
			if( empty( $_POST[ 'Alpha_name' ]) ) {
				wc_add_notice(  'Nome no cartão é obrigatório!', 'error' );
				return false;
			}
			if( empty( $_POST[ 'Alpha_ccNo' ]) ) {
				wc_add_notice(  'Número do cartão é obrigatório!', 'error' );
				return false;
			}
			$exdate = sanitize_text_field($_POST['Alpha_expdate']);
          	$ex_date_array = explode('/',$exdate);
    	    $ex_month = $ex_date_array[0];
    	    $ex_year = $ex_date_array[1];

    	    if (empty($ex_month)) {
    	    	wc_add_notice(  'Informe o mês de vencimento do cartão de crédito!', 'error' );
				return false;
    	    }
    	    if (empty($ex_year)) {
    	    	wc_add_notice(  'Informe o ano de vencimento do cartão de crédito!', 'error' );
				return false;
    	    }

			if( empty( $_POST[ 'Alpha_ccNo' ]) ) {
				wc_add_notice(  'Informe o número do cartão de crédito!', 'error' );
				return false;
			}
			if( empty( $_POST[ 'Alpha_cvv' ]) ) {
				wc_add_notice(  'Informe o código de segurança do cartão de crédito!', 'error' );
				return false;
			}
			return true;
		 
		}
		public function process_payment( $order_id ) {
	        global $woocommerce;
	  
	        $order = wc_get_order( $order_id );
	        $amount = $order->get_total();
	        $currency = get_woocommerce_currency();
	        $merchantCustomerId = $order->get_user_id();
	        $merchantOrderId = $order->get_order_number();
	        $orderIdString = '?orderId=' . $order_id;
	        $transaction = array(
	            "amount" => $amount,
	            "currency" => $currency,
	        );
	        $transactions = array(
	            $transaction
	        );

	       $customer_user_id = get_post_meta( $order_id, '_customer_user', true );
		   $get_customer = new WC_Customer( $customer_user_id );

		   $birth_date = '';
		   $birth_date = DateTime::createFromFormat('d/m/Y',str_replace("\/", "/",get_post_meta($order_id, 'billing_birthdate', true)));

		   if($birth_date) {
			   	$birth_date = $birth_date->format('Y-m-d').'T00:00:00';
		   }else {
			   	$birth_date = '';
		   }

		   $document_r = str_replace("-", "", get_post_meta($order_id, 'billing_cpf', true));
		   $document_r = str_replace(".", "", $document_r);
    	   $customer = array (
	        	"name"=> $order->get_billing_first_name().' '.$order->get_billing_last_name(),
				"birthDate"=> $birth_date,
				"document"=> $document_r,
				"email"=> $order->get_billing_email(),
	        	"billingAddress" => array (
		        	        "street"=> $order->get_billing_address_1(),
					"number"=> $order->get_order_number(),
					"complement"=> $order->get_billing_address_2(),
					"zipCode"=> str_replace('-', '', $order->get_billing_postcode()),
					"city"=> $order->get_billing_city(),
					"state"=> $order->get_billing_state(),
					"country"=> 'BR'
	        	),
	        	"deliveryAddress"=> array (
		        	        "street"=> $order->get_shipping_address_1(),
					"number"=> $order->get_order_number(),
					"complement"=> $order->get_shipping_address_2(),
					"zipCode"=> str_replace('-', '', $order->get_shipping_postcode()),
					"city"=> $order->get_shipping_city(),
					"state"=> $order->get_shipping_state(),
					"country"=> 'BR'
				)
	        );
			$ex_date_array = explode('/',get_post_meta($order_id, 'exdate', true));
    	    $ex_month = $ex_date_array[0];
    	    $ex_year = $ex_date_array[1];
			$payment= array(
				"method"=> 'Credit',
				"amount"=> $order->get_total(),
				"installments"=> get_post_meta($order_id, 'installments', true),
				"currency"=> get_woocommerce_currency(),
				"card"=> array(
					"issuer"=> array(
						"name"=> get_post_meta($order_id, 'payType', true)
					),
					"holderName"=> get_post_meta($order_id, 'holderName', true),
					"number"=> get_post_meta($order_id, 'ccNo', true),
					"expiration"=> array(
						"month"=> $ex_month,
						"year"=>  $ex_year
					),
					"securityCode"=> get_post_meta($order_id, 'cvv', true)
				)
			);
		   $customer_ip = '';
		   if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
				$customer_ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
				$customer_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$customer_ip = $_SERVER['REMOTE_ADDR'];
	   		}
	       $requestBody = array(
	       		'customer' => $customer,
	       		'payment' => $payment,
	            "splitGroup"=> array (
            	    "splitGroupHash"=> null,
				    "itens"=> array(
				    	array (
						        "amount"=> 0,

						        "sellerHash"=> null
				    	)
				    )
	            ),

	            "sellerId"=> 0,
	            "callbackUrl"=> null,
	            "softDescriptor"=> 'lksontology',
	            "referenceId"=> null,
	            "deviceFingerPrint"=> null,
	            "trackingData"=> array (
	            	"originDomainName"=> wp_parse_url ( get_site_url(), PHP_URL_HOST ),
	            	"customerIpAddress"=> gethostbyname(gethostname()),
	            ),
	            "notes"=> null
	        );


	        $header = array(
	            //'Authorization' => $this->auth_token,
	            'accept' => 'text/plain',
	            'Authorization' => $this->api_key,
	            'Content-Type' => 'application/json-patch+json'
	        );
	        $args = array(
	            'method' => 'POST',
	            'headers' => $header,
	            'body' => json_encode($requestBody),
	        );

	        $apiUrl = $this->api_address;
	        $response = wp_remote_post( $apiUrl, $args );  


	        if( !is_wp_error( $response ) ) {

	            $body = json_decode( $response['body'], true );

	            if ( $body['isSuccess'] ) {

	            	if($body['payload']['status'] == "8") {
		                $transactionId = $body['payload']['transactionId'];

		                $order->update_meta_data( 'Alpha_transactionId', $transactionId );

		                $transaction_note = "Alpha transactionId: " . $transactionId;

		                $order->add_order_note( $transaction_note );

		                update_post_meta( $order_id, '_transactionId', $transactionId );

						

				        $order = wc_get_order($order_id);

				        if(empty($order))

				            return;


				        if($order->get_status() != 'completed' || $order->get_status() != 'processing') {

				            $order->payment_complete();

				        }

				        $woocommerce->cart->empty_cart();

		                return array(

				            'result' => 'success',

				            'redirect' => $this->get_return_url($order)

				        );


					}else if($body['payload']['status'] == "3") {

						wc_add_notice(  'Falha no pagamento devido a um cartão recusado. Por favor, tente mais tarde ou utilize outro cartão.'.$body['failureReason'], 'error' );

		                return;

				    } 

				}else {

	                wc_add_notice(  'Ocorreu um erro durante a compra.', 'error' );

	                return;

			    }

	        } else {

				wc_add_notice(  'Erro de conexão com o servidor'.':'.$body['failureReason'], 'error' );

	            return;

	        }

    	}

	}

}

add_action('woocommerce_admin_order_data_after_billing_address', 'wc_alpha_custom_display_admin', 10, 1);

function wc_alpha_custom_display_admin($order){
    $method = get_post_meta($order->get_id(), '_payment_method', true);
    if($method != WC_ALPHA_PAY_ID){
        return;
    }
    $payType = get_post_meta($order->get_id(), 'payType', true);
    echo '<p><strong>'.__('Pay Type').':</strong> '.$payType.'</p>';
}