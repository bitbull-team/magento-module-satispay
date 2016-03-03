<?php

/**
 * Satispay APIs v1.0 Client
 */
class Satispay_Core_Client {
    const HTTP_TIMEOUT = 60;
    
    const PRODUCTION_ENDPOINT = 'authservices.satispay.com';
    const STAGING_ENDPOINT = 'staging.authservices.satispay.com';
    const STAGING_SECURITY_TOKEN = 'osh_cgml4ekip198n22sef1f05kfa389j9pqnll6hg5nq05ppla1f3b1eitv73o53mqhjdujq4m5kgc0fa0f5nojvisrqmunbk14dutt1qi95qgfstb60glek063beftbmc12htftpkjvchtckj7fi9ep5c56l9d356ev30tlrfubvfiugo44lomgac0hd9rpbf326lsmj06';
    
    const PAYMENT_STATUS_REQUESTED = 'REQUESTED';
    const PAYMENT_STATUS_SUCCEESS = 'SUCCESS';
    const PAYMENT_STATUS_DECLINED = 'DECLINED';
    const PAYMENT_STATUS_FAILURE = 'FAILURE';
    
    protected $_staging;
    protected $_security_token;

    public function setStaging($value) {
        $this->_staging = $value;
        return $this;
    }
    
    public function setSecurityToken($value) {
        $this->_security_token = $value;
        return $this;
    }
    
    /**
     * Get API endpoint
     */
    protected function getEndpoint() {
    	return $this->_staging ? self::STAGING_ENDPOINT : self::PRODUCTION_ENDPOINT;
    }
    
    /**
     * Get security token for bearer authentication
     */
    protected function getSecurityToken() {
    	return $this->_staging ? self::STAGING_SECURITY_TOKEN : $this->_security_token;
    }
    
    /**
     * Make an API call to the specified resource
     * 
     * @param string $idempotencyKey Request unique value, to avoid duplication
     * @param string $resource API resource
     * @param array $params POST request parameters (optional)
     */
	private function getResponse($idempotencyKey, $resource, $params=array()) {
		$h = curl_init();
		
		// Initialize request
		curl_setopt($h, CURLOPT_URL, 'https://' . $this->getEndpoint() . $resource);
		curl_setopt($h, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($h, CURLOPT_CONNECTTIMEOUT, self::HTTP_TIMEOUT);
		curl_setopt($h, CURLOPT_TIMEOUT, self::HTTP_TIMEOUT);

		// Ignore certificate issues in staging
		if($this->_staging) {
			curl_setopt($h, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($h, CURLOPT_SSL_VERIFYPEER, false);
		}

		// Add headers
		$headers = array(
		    'Content-type: application/json',
		    'Authorization: Bearer ' . $this->getSecurityToken(),
		);
		curl_setopt($h, CURLOPT_HTTPHEADER, $headers);

		// Add parameter (if post request)
        if($params) {
			curl_setopt($h, CURLOPT_POST, 1);
			curl_setopt($h, CURLOPT_POSTFIELDS, json_encode($params));
        }
		
		$content = curl_exec($h);
		curl_close($h);

		return json_decode($content);
	}
	
	/**
     * Create a new user
     * 
     * @param string $phoneNumber The user's phone number
     */
	public function userCreate($phoneNumber) {
		// Idempotency key is not important in this request, as
		// multiple calls with the same number returns the same id
		$idempotencyKey = microtime(true);
		
		// Make request
	    return $this->getResponse($idempotencyKey, '/online/v1/users', array(
	    	'phone_number' => $phoneNumber,
    	));
	}
	
	/**
	 * Create charge request
	 * @param string $orderId
	 * @param string $userId
	 * @param string $currency
	 * @param float $amount
	 */
	public function chargeCreate($orderId, $userId, $currency, $amount) {
		// Using order id as idempotency key, in order to avoid multiple charges for same order
	    return $this->getResponse($orderId, '/online/v1/charges', array(
	        'user_id' => $userId,
	        'order_id' => $orderId,
	        'currency' => $currency,
	        'amount' => $amount,
        ));
	}
	
	/**
	 * Get charge informations
	 * @param string $id
	 */
	public function chargeGet($id) {
		// Idempotency key is not important in this request
		$idempotencyKey = microtime(true);
		
		// Make request
	    return $this->getResponse($idempotencyKey, '/online/v1/charges/' . $id);
	}
	
	/**
	 * Create refund request
	 * @param string $chargeId
	 * @param string $currency
	 * @param float $amount
	 */
	public function refundCreate($chargeId, $currency, $amount) {
		$idempotencyKey = microtime(true);
		
	    return $this->getResponse($idempotencyKey, '/online/v1/refunds', array(
	        'charge_id' => $chargeId,
	        'currency' => $currency,
	        'amount' => $amount,
        ));
	}
}
