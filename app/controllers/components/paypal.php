<?php
class PaypalComponent extends Object {
	var $components = array('Cookie');
	var $environment = 'sandbox'; // live | sandbox
	var $debug = true; // Debug to Log
	var $credentials = array(
		'live'=>array(
			'USER'=>'',//Username
			'PWD'=>'',//Password
			'SIGNATURE'=>'A--3HR.KLkVc38u9kIVBaK-oM7eKAbZ2kxbmDp-lBPqHKfJflnopX4XI'
			//'ID?'=>'8dWq6smfnTSlU-29MDK1QV1f0CYJPsMhxkFSPf1Av-lqXHeJzu2sTySfaZC'
		),
		'sandbox'=>array(
			'USER'=>'sdk-three_api1.sdk.com',//Username
			'PWD'=>'QFZCWN5HZM8VBG7Q',//Password
			'SIGNATURE'=>'A-IzJhZZjhg29XQ2qnhapuwxIDzyAZQ92FRP5dqBzVesOkzbdUONzmOU'
		),
	);

	var $version     = '65.0';
	var $endpoints   = array('live' => 'https://api-3t.paypal.com/nvp',	'sandbox' => 'https://api-3t.sandbox.paypal.com/nvp');
	var $paypalURLs  = array('live' => 'https://www.paypal.com/', 		'sandbox' => 'https://www.sandbox.paypal.com/');

	var $return_url	 = '/productos/finalizado';
	var $cancel_url	 = '/productos/cancelado';
	var $response	 = null;
	var $items	 	 = array();
	var $payer		 = null;
	var $recipients  = null;
	var $currency	 = null;
	var $localecode  = 'MX';
	var $failures	 = array();
	var $currencies = array('AUD','BRL','CAD','CZK','DKK','EUR','HKD','HUF','ILS','JPY','MYR','MXN','NOK','NZD','PHP','PLN','GBP','SGD','SEK','CHF','TWD','THB','USD');

	function initialize(&$controller) {
		$this->controller =& $controller;
		$this->controller->Cookie->name = 'ckpp_session';
		$this->controller->Cookie->time = 3600; // or '1 hour'
		$this->controller->Cookie->path = '/';
		//$this->controller->Cookie->domain = '';
		$this->controller->Cookie->secure = false; //i.e. only sent if using secure HTTPS
		$this->controller->Cookie->key = 'duzAstU4hA3?ub#ucRam2trASe2!FREf';

		$this->return_url = Router::url($this->return_url,true);
		$this->cancel_url = Router::url($this->cancel_url,true);
	}

	/**
	 * Initiates an Express Checkout transaction
	 * @param array $items Items to include in the transaction
	 * @param string $currency Supported ISO-4217 currency code
	 * @param string $returnURL URL redirect after confirmed payment
	 * @param string $cancelURL URL redirect after cancelled payment
	 * @return void Redirect on success, else return false
	 */
	public function setExpressCheckout() {
		$request = array_merge(array('RETURNURL'=>$this->return_url, 'CANCELURL'=>$this->cancel_url,'LOCALECODE'=>$this->localecode),$this->getItemTotals());
		$this->buildRequest('setExpressCheckout', $request);

		if($this->execute('setExpressCheckout')){
			header('Location: '.$this->paypalURLs[$this->environment].'webscr?cmd=_express-checkout&token='.$this->response['TOKEN']);
			exit;
		}
		return false;
	}

	/**
	 * Obtain information about an Express Checkout transaction
	 * @return array Transaction details
	 */
	private function getExpressCheckoutDetails() {
		$array = array('TOKEN' => $_GET['token']);
		$this->buildRequest('getExpressCheckoutDetails', $array);
		if($this->execute('getExpressCheckoutDetails')) return $this->response;
		return false;
	}
	
	/**
	 * Completes an Express Checkout transaction.
	 * @return bool
	 */
	public function doExpressCheckoutPayment() {
		$d = $this->getExpressCheckoutDetails();
		$array = array('TOKEN' => $d['TOKEN'],
					   'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
					   'PAYMENTREQUEST_0_AMT' => $d['PAYMENTREQUEST_0_AMT'].' '.$d['PAYMENTREQUEST_0_CURRENCYCODE'],
					   'PAYERID' => $d['PAYERID']);
		$array = array_merge($array, $d);
		$this->buildRequest('doExpressCheckoutPayment', $array);
		if($this->execute('doExpressCheckoutPayment')) return $this->response;
		return false;
	}

	/**
	 * Add an item to the array of items for a transaction
	 * Note: Thousands separator must be ','
	 * @param string $name Item name
	 * @param string $desc Item Description
	 * @param string|int|float $amt Numeric price value
	 * @param int $qty Item quantity
	 */
	function addItem($id, $fields = array()) {
		$fields = array_merge(array('name'=>'Item '.$id,'amt'=>0,'qty'=>0),$fields);
		$fields['amt'] = str_replace(',', '', $fields['amt']);

		if(!is_numeric($fields['amt'])) $this->error('INVALID_AMT');
		else {
			$fields['amt'] = number_format($fields['amt'], 2, '.', '');
			$this->items[$id] = $fields;
		}
	}

	/**
	 * Return an array of items to be merged with current NVP array
	 * @param array $items Items to include in the transaction
	 * @param string $currency Supported ISO-4217 currency code
	 * @return array
	 */
	function getItemTotals(){
		$i = 0;
		$total = 0;
		$currency = $this->getCurrencyCode();
		
		if(empty($this->items)){ $this->error('INVALID_ITEMS');return false; }

		foreach($this->items as $item_id => $item){
			$array['L_PAYMENTREQUEST_0_NAME'.$i] = $item['name'];
			$array['L_PAYMENTREQUEST_0_NUMBER'.$i] = $item_id;
			$array['L_PAYMENTREQUEST_0_DESC'.$i] = $item['desc'];
			$array['L_PAYMENTREQUEST_0_AMT'.$i] = $item['amt'].' '.$currency;
			$array['L_PAYMENTREQUEST_0_QTY'.$i] = $item['qty'];
			$total = $total + ($item['amt'] * $item['qty']);
			$i++;
		}

		$array['PAYMENTREQUEST_0_AMT'] = $total.' '.$currency;
		$array['PAYMENTREQUEST_0_CURRENCYCODE'] = $currency;
		$this->items = null;

		return $array;
	}

	/**
	 * Merge 2 arrays and create a name-value pair string from
	 * the resulting array using http_build_query()
	 * @param string $method API method name
	 * @param array $methodArray Name-value pair array
	 * @return void
	 */
	function buildRequest($method, $nvpArray = array()) {
		$array = array_merge(array('METHOD' => $method,'VERSION' => $this->version),$this->credentials[$this->environment], $nvpArray);
		$this->request = http_build_query($array, '', '&');
	}

	/**
	 * Execute an API call via cURL
	 * @return bool|array Return false on failure, response array on success
	 */
	function execute($call_name) {
		// Set the curl parameters.
		$ch = curl_init($this->endpoints[$this->environment]);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);

		// Turn off the server and peer verification (TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request);

		$output = curl_exec($ch);
		curl_close($ch);

		$this->response = $this->processOutput($output);
		if($this->debug) $this->debug($call_name);

		if((!empty($this->response['ACK'])) && strpos($this->response['ACK'], 'Failure') !== false){ $this->log($this->response,'cart_responses_failure'); return false; }
		return true;
	}

	/**
	 * Convert an NVP response to an associative array
	 * @param string $output
	 * @return array
	 */
	function processOutput($output) {
		$outArray = explode('&', $output);
		foreach($outArray as $val){
			$assoc = explode('=', $val);
			$return[$assoc[0]] = urldecode($assoc[1]);
		}
		return $return;
	}
	
	/**
	 * Simple response debugger
	 * var_dump $this->response so we can debug
	 * @return void
	 */
	function debug($call_name) {
		$this->response['REQUEST'] = $this->request;
		$this->response['CALL_NAME'] = $call_name;
		$this->log($this->response,'cart_responses');
	}
	
	/**
	 * PayPalPHP error handler
	 * @param string $code
	 */
	function error($code) {
		$trace = debug_backtrace();
		$errors['INVALID_ENVIRONMENT']		 = "PayPal environment must be either 'LIVE' or 'SANDBOX'";
		$errors['INVALID_TOKEN']			 = 'Invalid token supplied';
		$errors['INVALID_ITEMS']			 = 'No items added to this transaction. Add at least 1 item using addItem()';
		$errors['INVALID_AMT']				 = "Amount must be a numeric value";
		$errors['INVALID_CURRENCY_CODE']	 = 'Currency must be a PayPal supported ISO-4217 currency code';
		$errors['UNDEFINED_CURRENCY_CODE']	 = 'Currency code is undefined. Set currency code using setCurrencyCode()';
		$errors['INVALID_TRANSACTIONID']	 = 'Transaction ID must be an alphanumeric string and contain 17 characters';
		$errors['INVALID_RECIPIENTS']		 = 'No recipients added. Add at least 1 recipient using addRecipient()';
		$errors['INVALID_EMAIL_ADDRESS']	 = 'Invalid email address';
		$errors['MAX_RECIPIENTS']			 = 'Maximum of 250 recipients per Mass Payment transaction';
		$errors['UNDEFINED_PAYER_DETAILS']	 = 'Payer details undefined. You must set payer details using setPayerDetails()';
		$errors['UNDEFINED_CARD_DETAILS']	 = 'Card details undefined. You must set card details using setCardDetails()';
		$errors['UNDEFINED_PROFILE_DETAILS'] = 'Profile details undefined. You must set profile details using setProfileDetails()';
		$errors['INVALID_CARD_NUMBER']		 = 'Credit card number/CVV2 must be numeric';
		$errors['INVALID_CARD_TYPE']		 = 'Credit card type must be Visa, MasterCard, Discover, Amex, Maestro or Solo (Case-sensitive)';
		$errors['INVALID_CARD_CVV2']		 = 'Credit card CVV2 must be an integer';
		$errors['INVALID_EXPIRY_DATE']		 = 'Credit card expiry must be an integer in date format MMYYYY';
		$errors['CARD_VALIDATION_FAILED']	 = 'Credit card number validation failed';

		//$this->failures[] = $errors[$code].' in '.$trace[0]['file'].' on line '.$trace[0]['line'];
		echo 'PayPal API Error: '.$errors[$code].' in '.$trace[0]['file'].' on line '.$trace[0]['line'];
		exit;
	}
	
	/**
	 * Set the transaction currency code
	 * @param string $currency
	 * @return void
	 */
	function setCurrencyCode($currency) {
		if(!in_array($currency, $this->currencies)) $this->error('INVALID_CURRENCY_CODE');
		else $this->currency = $currency;
	}
	
	/**
	 * Return the currency code
	 * @return string
	 */
	function getCurrencyCode() {
		if(is_null($this->currency)){
			$this->error('UNDEFINED_CURRENCY_CODE');
			return false;
		}
		return $this->currency;
	}
}
?>
