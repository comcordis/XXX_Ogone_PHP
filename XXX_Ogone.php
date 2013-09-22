<?php

/*

Merchant: YAT
PSP (Payment Service Provider): Ogone
Acquirer: Creditcard handler

Template:
	$$$PAYMENT ZONE$$$

*/

abstract class XXX_Ogone
{
	public static $isProduction = false;
	
	public static $PSPID = '';
	
	public static $referrerURIs = array
	(
	);
		
	// http://www.example.com/home
	public static $homeURI = '';
	
	// http://www.example.com/booking
	public static $catalogURI = '';
	
	// http://www.example.com/booking/select-payment-method
	public static $backButtonURI = '';
	
	// http://www.example.com/payment-service-provider/ogone/payment-accepted
	public static $paymentAcceptedURI = '';
	
	// http://www.example.com/payment-service-provider/ogone/payment-declined
	public static $paymentDeclinedURI = '';
	
	// http://www.example.com/payment-service-provider/ogone/payment-exception
	public static $paymentExceptionURI = '';
	
	// http://www.example.com/payment-service-provider/ogone/payment-cancelled
	public static $paymentCancelledURI = '';
	
	// http://www.example.com/payment-service-provider/ogone/dynamic-template
	public static $dynamicTemplateURI = '';
	
	public static $inPassPhrase = '';
	public static $outPassPhrase = '';
	
	public static $testCardTransactionsByAmount = array
	(
		'accepted' => array
		(
			'minimumAmount' => 0,
			'maximumAmount' => 900000,
			'cardNumber' => 'any'
		),
		'declined' => array
		(
			'minimumAmount' => 900001,
			'maximumAmount' => 999899,
			'cardNumber' => '4111113333333333'
		),
		'exception' => array
		(
			'minimumAmount' => 999900,
			'maximumAmount' => 999900,
			'cardNumber' => '4111116666666666'
		)
	);
	
	// Use any expiry date in the future
	public static $testCardTransactionsByCardNumber = array
	(
		'visa' => '4111111111111111',
		'visa3DSecure' => '4000000000000002',
		'masterCard' => '5399999999999999',
		'americanExpress' => '374111111111111'
	);
	
	public static $signatureTest = array
	(
		'passPhrase' => 'Mysecretsig1875!?',
		'parameters' => array
		(
			'AMOUNT' => 1500,
			'CURRENCY' => 'EUR',
			'LANGUAGE' => 'en_US',
			'ORDERID' => 1234,
			'PSPID' => 'MyPSPID'
		),
		'hashAlgorithm' => 'sha1',
		'expectedSignature' => 'F4CC376CD7A834D997B91598FA747825A238BE0A'
	);
	
	public static $additionalPaymentMethodParameters = array
	(
		'iDeal' => array
		(
			'PM' => 'iDEAL',
			'BRAND' => 'iDEAL'
		),
		
		'masterCard' => array
		(
			'PM' => 'CreditCard',
			'BRAND' => 'MasterCard'
		),
		'visa' => array
		(
			'PM' => 'CreditCard',
			'BRAND' => 'VISA'
		),
		'americanExpress' => array
		(
			'PM' => 'CreditCard',
			'BRAND' => 'American Express'
		),
		'dinersClub' => array
		(
			'PM' => 'CreditCard',
			'BRAND' => 'Diners Club'
		),
		
		'payPal' => array
		(
			'PM' => 'PAYPAL',
			'BRAND' => 'PAYPAL'
		)
	);
	
	/*
	
	The format is "language_Country".
	The language value is based on ISO 639-1.
	The country value is based on ISO 3166-1.
	
	*/
	
	public static $allowedLanguages = array
	(
		'ar_AR', // Arabic
		'cs_CZ', // Czech
		'dk_DK', // Danish
		'de_DE', // German
		'el_GR', // Greek
		'en_US', // English
		'es_ES', // Spanish
		'fi_FI', // Finnish
		'fr_FR', // French
		'he_IL', // Hebrew
		'hu_HU', // Hungarian
		'it_IT', // Italian
		'ja_JP', // Japanese
		'ko_KR', // Korean
		'nl_BE', // Flemish
		'nl_NL', // Dutch
		'no_NO', // Norwegian
		'pl_PL', // Polish
		'pt_PT', // Portugese
		'ru_RU', // Russian
		'se_SE', // Swedish
		'sk_SK', // Slovak
		'tr_TR', // Turkish
		'zh_CN' // Simplified Chinese
	);
	
	/*
	
	http://www.currency-iso.org/iso_index/iso_tables/iso_tables_a1.htm
	
	If a merchant wants to accept payments in a currency that is not in our list, he can ask us to add the currency.

	The currencies a merchant can accept payments in depend on the contract with his acquirer.
	
	If the merchant wants to accept a currency that is not supported by his acquirer, we can set a dynamic currency conversion on our side (this is a paying option). 
	
	*/
	
	public static $allowedCurrencies = array
	(
		'AED',
		'ANG',
		'ARS',
		'AUD',
		'AWG',
		'BGN',
		'BRL',
		'BYR',
		'CAD',
		'CHF',
		'CNY',
		'CZK',
		'DKK',
		'EEK',
		'EGP',
		'EUR',
		'GBP',
		'GEL',
		'HKD',
		'HRK',
		'HUF',
		'ILS',
		'ISK',
		'JPY',
		'KRW',
		'LTL',
		'LVL',
		'MAD',
		'MXN',
		'NOK',
		'NZD',
		'PLN',
		'RON',
		'RUB',
		'SEK',
		'SGD',
		'SKK',
		'THB',
		'TRY',
		'UAH',
		'USD',
		'XAF',
		'XOF',
		'XPF',
		'ZAR'
	);
	
	public static function composeSubmitURI ($isProduction = false)
	{
		$result = 'https://secure.ogone.com/ncol/' . ($isProduction ? 'prod' : 'test') . '/orderstandard.asp';
		
		return $result;
	}
	
	// Should be multiplied by 100, e.g. 1 EUR = 100, no points/commas etc.
	public static function formatAmount ($amount = 0)
	{
		$amount = XXX_Number::highest($amount, 0);
		$amount *= 100;
		$amount = XXX_Number::round($amount);
		
		return $amount;
	}
	
	/*
	
	Hash: SHA-512
	
	Encoding: UTF-8
	
	SHA-IN encryption: yourairporttransfer2013
	
	SHA-OUT encryption: yourairporttransfer2013
		
	*/	
	
	public static function composeParameters ($pspID = '', $customReference = '', $fullName = '', $emailAddress = '', $amount = 0, $currency = 'EUR', $language = 'en_US', $paymentMethod = false, $additionalParameters = array(), $passPhrase = '', $hashAlgorithm = 'sha1', $paymentAcceptedURI = '', $paymentDeclinedURI = '', $paymentExceptionURI = '', $paymentCancelledURI = '', $backURI = '', $homeURI = '', $bookARideURI = '', $dynamicTemplateURI = '')
	{
		$result = array();
		
		$result['PSPID'] = $pspID;
		$result['ORDERID'] = $customReference;
		
		$result['AMOUNT'] = self::formatAmount($amount);		
		$result['CURRENCY'] = XXX_Default::toOption($currency, self::$allowedCurrencies, 'EUR');
		$result['LANGUAGE'] = XXX_Default::toOption($language, self::$allowedLanguages, 'en_US');
		
		$result['ACCEPTURL'] = $paymentAcceptedURI;
		$result['DECLINEURL'] = $paymentDeclinedURI;
		$result['EXCEPTIONURL'] = $paymentExceptionURI;
		$result['CANCELURL'] = $paymentCancelledURI;
		$result['BACKURL'] = $backURI;
		$result['HOMEURL'] = $homeURI;
		$result['CATALOGURL'] = $bookARideURI;
		$result['TP'] = $dynamicTemplateURI;
				
		if ($fullName != '')
		{
			$result['CN'] = $fullName;
		}
		
		if ($emailAddress != '')
		{
			$result['EMAIL'] = $emailAddress;
		}
		
		if ($paymentMethod)
		{
			if (XXX_Type::isArray(self::$additionalPaymentMethodParameters[$paymentMethod]))
			{
				foreach (self::$additionalPaymentMethodParameters[$paymentMethod] as $key => $value)
				{
					$result[$key] = $value;
				}
			}
		}
		
		if (XXX_Type::isArray($additionalParameters))
		{
			foreach ($additionalParameters as $key => $value)
			{
				$result[$key] = $value;
			}
		}
		
		$result = self::convertParametersToUpperCaseAndFilterOutEmpty($result);
		
		if ($passPhrase != '')
		{
			$result['SHASIGN'] = self::composeSignature($result, $passPhrase, $hashAlgorithm);
		}
		
		return $result;
	}
	
	public static function convertParametersToUpperCaseAndFilterOutEmpty ($parameters = array())
	{
		$newParameters = array();
		
		foreach ($parameters as $key => $value)
		{
			$key = XXX_String::trim($key);
			$key = XXX_String::convertToUpperCase($key);
			$value = XXX_String::trim($value);
			
			if ($key != '' && $value != '')
			{
				$newParameters[$key] = $value;
			}
		}
		
		return $newParameters;
	}
	
	public static function composeHiddenInputsForParameters ($parameters = array())
	{
		$result = '';
		
		foreach ($parameters as $key => $value)
		{
			$result .= '<input type="hidden" name="' . XXX_String_HTMLEntities::encode($key) . '" value="' . XXX_String_HTMLEntities::encode($value) . '">';
		}
		
		return $result;
	}
		
	/*
		
	- All sent parameters are needed (which are present in the list on https://secure.ogone.com/ncol/Ogone_e-Com-BAS_NL.pdf)
	- All parameters should be in alphabetical order (Some sorting algorithms sort special characters incorrect, if so use the order given in https://secure.ogone.com/ncol/Ogone_e-Com-BAS_NL.pdf)
	- All parameter keys should be upper case
	- Empty parameters should not be included
	- Use different passPhrases for testing and production
	
	*/
	
	public static function composeSignature ($parameters = array(), $passPhrase = '', $hashAlgorithm = 'sha1')
	{
		ksort($parameters);
		
		$result = '';
		
		foreach ($parameters as $key => $value)
		{
			$key = XXX_String::convertToUpperCase($key);
			$value = XXX_String::trim($value);
			
			if ($value != '')
			{
				$result .= $key;
				$result .= '=';
				$result .= $value;
				
				$result .= $passPhrase;
			}
		}
		
		$result = XXX_String_Hash::hash($result, $hashAlgorithm);
		
		$result = XXX_String::convertToUpperCase($result);
		
		return $result;
	}
	
	public static function parseCallbackParameters ($passPhrase = '', $hashAlgorithm = 'sha1')
	{
		$result = false;
		
		$tempParameters = array();
		
		if (XXX_String::convertToLowerCase($_SERVER['REQUEST_METHOD']) == 'get')
		{
			$tempParameters = $_GET;
		}
		else
		{
			$tempParameters = $_POST;
		}
		
		$parameterSignature = $tempParameters['SHASIGN'];
		
		unset($tempParameters['route']);
		unset($tempParameters['SHASIGN']);
		
		$tempParameters = self::convertParametersToUpperCaseAndFilterOutEmpty($tempParameters);
		
		$reconstructedSignature = self::composeSignature($tempParameters, $passPhrase, $hashAlgorithm);
		
		if ($parameterSignature == $reconstructedSignature)
		{
			$result = $tempParameters;
		}
		
		return $result;
	}
	
	public static function parseCallbackInputVariables ()
	{
		$result = false;
		
		$callbackParameters = self::parseCallbackParameters(self::$outPassPhrase);
		
		if ($callbackParameters)
		{
			$result = array
			(
				'customReference' => false,
				'paymentServiceProviderReference' => false,
				'paymentServiceProviderStatus' => false,
				'paymentServiceProviderErrorCode' => false,
				'originalParameters' => $callbackParameters
			);
			
			if ($callbackParameters['ORDERID'] != '')
			{
				$result['customReference'] = XXX_Type::makeInteger($callbackParameters['ORDERID']);
			}
			
			if ($callbackParameters['PAYID'] != '')
			{
				$result['paymentServiceProviderReference'] = XXX_Type::makeInteger($callbackParameters['PAYID']);
			}
			
			if ($callbackParameters['STATUS'] != '')
			{
				$result['paymentServiceProviderStatus'] = XXX_Type::makeInteger($callbackParameters['STATUS']);
			}
			
			if ($callbackParameters['NCERROR'] != '')
			{
				$result['paymentServiceProviderErrorCode'] = XXX_Type::makeInteger($callbackParameters['NCERROR']);
			}
		}
		
		return $result;
	}
	
	public static function savePaymentUpdate ()
	{
		$inputVariables = self::parseCallbackInputVariables();
		
		$content = array
		(
			'post' => $_POST,
			'get' => $_GET,
			'inputVariables' => $inputVariables
		);
		
		$fileContent = XXX_String_JSON::encode($content);
		
		$timestampPartsForPath = XXX_TimestampHelpers::getTimestampPartsForPath();
				
		$file = 'paymentUpdate_ogone_' . XXX_TimestampHelpers::getTimestampPartForFile() . '_' . XXX_String::getPart(XXX_String::getRandomHash(), 0, 8) . '.tmp';
		
		$filePath = XXX_Path_Local::extendPath(XXX_Path_Local::$deploymentDataPathPrefix, array('paymentUpdates', 'ogone', $timestampPartsForPath['year'], $timestampPartsForPath['month'], $timestampPartsForPath['date'], $file));
		
		XXX_FileSystem_Local::writeFileContent($filePath, $fileContent);
	}
}

?>