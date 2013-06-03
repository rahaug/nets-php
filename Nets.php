<?php

class Payment
{
  	/**
	 * Environment
	 *
	 * @var string
	 */
	private $_environment;
	
	
	
	/**
	 * Nets Merchant ID
	 *
	 * @var int
	 */
	private $_merchant_id;
	
	
	
	/**
	 * Nets Merchant Token
	 *
	 * @var string
	 */
	private $_merchant_token;
	
	
	
	/**
	 * Nets URL
	 *
	 * @var string
	 */
	private $_url;



	/**
	 * Show debug information.
	 */
	private $debug = true;
	
	
	
	/**
	 * Constructor
	 *
	 * @author Anthoni Giskegjerde
	 */
	function __construct()
	{
		$this->_set_environment();
	}
	
	
	
	/**
	 * Redirects to the Nets payment terminal.
	 *
	 * @param string $transaction_id 
	 * @return void
	 * @author Anthoni Giskegjerde
	 */
	function launch_terminal($transaction_id)
	{
		    
		header('location: ' . 'https://'.$this->_url.'/Terminal/default.aspx?merchantId='.$this->_merchant_id.'&transactionId='.$transaction_id);
		return true;
	}
	
	
	
	/**
	 * Perform a ANNUL on the transaction
	 *
	 * @param string $transaction_id 
	 * @return mixed bool array
	 * @author Anthoni Giskegjerde
	 */
	function perform_annul($transaction_id)
	{
		$params = array(
			'MerchantId'		=>	$this->_merchant_id,
			'token'				=>	$this->_merchant_token,
			'transactionId'		=>	$transaction_id,
			'operation'			=>	'ANNUL'
		);
	
		return $this->_run_request($params, 'Process');
	}
	
	
		
	/**
	 * Perform an AUTH on the transaction
	 *
	 * @param string $transaction_id 
	 * @return mixed bool array
	 * @author Anthoni Giskegjerde
	 */
	function perform_auth($transaction_id)
	{
		$params = array(
			'MerchantId'		=>	$this->_merchant_id,
			'token'				=>	$this->_merchant_token,
			'transactionId'		=>	$transaction_id,
			'operation'			=>	'AUTH'
		);
	
		return $this->_run_request($params, 'Process');
	}
	
	
	
	/**
	 * Peform a CAPTURE on the transaction
	 *
	 * @param string $transaction_id 
	 * @param int $amount 
	 * @return mixed bool array
	 * @author Anthoni Giskegjerde
	 */
	function perform_capture($transaction_id, $amount)
	{
		$params = array(
			'MerchantId'		=>	$this->_merchant_id,
			'token'				=>	$this->_merchant_token,
			'transactionId'		=>	$transaction_id,
			'amount'			=>	$amount,
			'operation'			=>	'CAPTURE'
		);
	
		return $this->_run_request($params, 'Process');
	}
	
	
	
	/**
	 * Perform a CREDIT on the transaction
	 *
	 * @param string $transaction_id 
	 * @param int $amount 
	 * @return mixed bool array
	 * @author Anthoni Giskegjerde
	 */
	function perform_credit($transaction_id, $amount)
	{
		$params = array(
			'MerchantId'				=>	$this->_merchant_id,
			'token'						=>	$this->_merchant_token,
			'transactionId'				=>	$transaction_id,
			'transactionAmount'			=>	$amount,
			'operation'					=>	'CREDIT'
		);
	
		return $this->_run_request($params, 'Process');
	}
	
	
	
	/**
	 * Perform a QUERY on the transaction.
	 *
	 * @param int $transaction_id 
	 * @return mixed bool array
	 * @author Anthoni Giskegjerde
	 */
	function perform_query($transaction_id)
	{
		$params = array(
			'MerchantId'			=>	$this->_merchant_id,
			'token'					=>	$this->_merchant_token,
			'transactionId'			=>	$transaction_id
		);
	
		return $this->_run_request($params, 'Query');
	}
	
	
	
	/**
	 * Performs a SALE on the transaction.
	 *
	 * @param string $transaction_id 
	 * @return void
	 * @author Anthoni Giskegjerde
	 */
	function perform_sale($transaction_id)
	{
		$params = array(
			'MerchantId'			=>	$this->_merchant_id,
			'token'					=>	$this->_merchant_token,
			'transactionId'			=>	$transaction_id,
			'operation'				=>	'SALE'
		);
	
		$result = $this->_run_request($params, 'Process');
		
		return ((string) $result->ResponseCode == 'OK') ? true : false;				 
	}
	
	
	
	/**
	 * Register a new transaction, and also get pan hash in return for Easypayment.
	 *
	 * @param int $order_id 
	 * @param int $amount 
	 * @param string $redirect_url 
	 * @param int $user_id 
	 * @return mixed bool string
	 * @author Anthoni Giskegjerde
	 */
	function register_transaction_and_save_card($order_id, $amount, $redirect_url, $user_id = false)
	{	
		$params = array(
			'recurringType'			=>	'S',
			'MerchantId'			=>	$this->_merchant_id,
			'token'					=>	$this->_merchant_token,
			'orderNumber'			=>	$order_id,
			'transactionReconRef'	=>	$order_id,
			'amount'				=>	$amount,
			'currencyCode'			=>	'NOK',
			'redirectUrl'			=>	urlencode($redirect_url),
			'customerNumber'		=>	$user_id
		);
	
		return (!$result = $this->_run_request($params)) ? false : (string) $result->TransactionId;
	}
	
	
	
	/**
	 * Register a new transaction.
	 *
	 * @param int $order_id 
	 * @param int $amount 
	 * @param string $redirect_url 
	 * @param int $user_id 
	 * @return mixed bool string
	 * @author Anthoni Giskegjerde
	 */
	function register_transaction($order_id, $amount, $redirect_url, $user_id = false)
	{		
		$params = array(
			'MerchantId'			=>	$this->_merchant_id,
			'token'					=>	$this->_merchant_token,
			'orderNumber'			=>	$order_id,
			'transactionReconRef'	=>	$order_id,
			'amount'				=>	$amount,
			'currencyCode'			=>	'NOK',
			'redirectUrl'			=>	urlencode($redirect_url),
			'customerNumber'		=>	$user_id
		);
	
		return (!$result = $this->_run_request($params)) ? false : (string) $result->TransactionId;
	}
	
	
	/**
	 * Registers a new easypayment.
	 *
	 * @param int $order_id 
	 * @param int $amount 
	 * @param string $pan_hash 
	 * @param int $security_code 
	 * @param int $user_id 
	 * @return mixed bool string
	 * @author Anthoni Giskegjerde
	 */
	function register_easypayment($order_id, $amount, $pan_hash, $security_code, $user_id = false)
	{	
		$params = array(
			'serviceType'			=>	'C',
			'recurringType'			=>	'S',
			'panHash'				=>	urlencode($pan_hash),
			'securityCode'			=>	$security_code,
			'MerchantId'			=>	$this->_merchant_id,
			'token'					=>	$this->_merchant_token,
			'orderNumber'			=>	$order_id,
			'transactionReconRef'	=>	$order_id,
			'amount'				=>	$amount,
			'currencyCode'			=>	'NOK',
			'customerNumber'		=>	$user_id
		);
	
		return (!$result = $this->_run_request($params)) ? false : (string) $result->TransactionId;
	}
	
	
	
	/**
	 * Registers a new recurring payment
	 *
	 * @param int $order_id 
	 * @param int $amount 
	 * @param string $pan_hash 
	 * @param string $expiry_date 
	 * @return mixed bool string
	 */
	function register_new_recurring_payment($order_id, $amount, $redirect_url, $expiry_date)
	{
	    $expiry_date = date('Ymd', strtotime($expiry_date));
	    
	    $params = array(
	       'serviceType'            =>  'B',
	       'MerchantId'			    =>	$this->_merchant_id,
		   'token'					=>	$this->_merchant_token,
		   'orderNumber'            =>  $order_id,
		   'transactionReconRef'	=>	$order_id,
		   'currencyCode'			=>	'NOK',
		   'amount'                 =>  $amount,
		   'redirectUrl'			=>	urlencode($redirect_url),
		   'recurringType'			=>	'R',
		   'recurringFrequency'     =>  1,
		   'recurringExpiryDate'    =>  $expiry_date
	    );
	    
	    return (!$result = $this->_run_request($params)) ? false : (string) $result->TransactionId;
	}
	
	
	
	
	/**
	 * Registers a new recurring payment
	 *
	 * @param int $order_id 
	 * @param int $amount 
	 * @param string $pan_hash 
	 * @param string $expiry_date 
	 * @return mixed bool string
	 */
	function register_recurring_payment($order_id, $amount, $pan_hash)
	{	    
	    $params = array(
	       'serviceType'            =>  'C',
	       'recurringType'          =>  'R',
	       'MerchantId'			    =>	$this->_merchant_id,
		   'token'					=>	$this->_merchant_token,
		   'orderNumber'            =>  $order_id,
		   'transactionReconRef'	=>	$order_id,
		   'currencyCode'			=>	'NOK',
		   'amount'                 =>  $amount,
		   'panHash'				=>	urlencode($pan_hash)
	    );
	    
	    return (!$result = $this->_run_request($params)) ? false : (string) $result->TransactionId;
	}
	
	
	/**
	 * Save a credit card for easypayment use without an actual order.
	 *
	 * @param int $order_id 
	 * @param string $redirect_url 
	 * @return void
	 * @author Anthoni Giskegjerde
	 */
	function save_card($order_id, $redirect_url)
	{	
		$params = array(
			'MerchantId'				=>	$this->_merchant_id,
			'token'						=>	$this->_merchant_token,
			'orderNumber'				=>	$order_id,
			'redirectUrl'				=>	urlencode($redirect_url),
			'updateStoredPaymentInfo'	=> 	'true',
			'operation'					=>	'AUTH'
		);
	
		return (!$result = $this->_run_request($params)) ? false : (string) $result->TransactionId;
	}
	
	
	/**
	 * Validates the response from Nets
	 *
	 * @param string $transaction_id 
	 * @param string $response_code 
	 * @return bool
	 * @author Anthoni Giskegjerde
	 */
	function validate_response($transaction_id, $response_code)
	{
	    if($response_code == 'Cancel') return 'cancel';
		return ($response_code != 'OK') ? false : $transaction_id;
	}
	
	

	/**
	 * Converts an array to a GET string.
	 *
	 * @param array $array 
	 * @return string
	 * @author Anthoni Giskegjerde
	 */
	function _array_to_url($array)
	{
		if(!is_array($array) || count($array) < 1) return false;
		
		$counter = 1;
		$url_string = '';
		
		foreach($array as $key => $value)
		{
			$url_string .= ($counter == 1) ? "?$key=$value" : "&$key=$value";
			$counter++;
		}
		
		return $url_string;
	}
	
	
	
	/**
	 * Runs the CURL request.
	 *
	 * @param array $params 
	 * @return mixed bool array
	 * @author Anthoni Giskegjerde
	 */
	function _run_request($params, $type = 'Register')
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://' . $this->_url . '/Netaxept/' . $type . '.aspx' . $this->_array_to_url($params));
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_POST, 0);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($curl); 
		curl_close($curl);
		
		$result = simplexml_load_string($response);
		
		if(isset($result->Error) && $this->debug) {
	    	die('Nets Error: ' . $result->Error->Message);
	    }
		
		return $result;
	}
	
	
	
	/**
     * Sets the environment.
     *
     * @return bool
     * @author Anthoni Giskegjerde
     */
	function _set_environment()
	{
	    $this->_environment = (defined('ENVIRONMENT')) ? ENVIRONMENT : 'development';
	    
		switch($this->_environment)
		{
		    default:
			case 'localhost':
			case 'development':
				$this->_merchant_id = '';
				$this->_merchant_token = urlencode('');
				$this->_url = 'epayment-test.bbs.no';
				break;
			
			case 'production':
				$this->_merchant_id = '123456';
				$this->_merchant_token = urlencode('your_token');
				$this->_url = 'epayment.bbs.no';
		}
		
		return true;
	}
}
