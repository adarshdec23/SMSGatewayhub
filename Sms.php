<?php
class Sms {

	private $parameter = [];
	/**
	 *
	 * @var array All approved templates 
	 */
	private $template = [];
	public $errorMessage;
	public $errorCode;
			
	function __construct() {
		$this->parameter['senderid'] = '';
		$this->parameter['APIKey'] = '';
		$this->parameter['channel'] = 2; //Promotional=1 or Transactional=2
		$this->parameter['route'] = 1; //Change this later
		$this->parameter['flashsms'] = 0;
		$this->parameter['DCS'] = 0;
		//Use \n for a line break
		$this->template = array(
			"approvedTemplate" => "Dear %s , Your package has been shipped.",
		);
	}
	/*
	 * *Setter functions
	 */
	public function setNumbers($numbers) {
		$this->parameter['number'] = implode(',', $numbers);
	}
	public function setContent($templateType, $valuesToInsert=NULL) {
        if($this->parameter['channel'] == 2)
            $this->parameter['text'] = vsprintf($this->template[$templateType], $valuesToInsert);
        else
            $this->parameter['text'] = $templateType
	}
    
    public function setParameter($parameterName, $parameterValue){
        $this->parameter[$parameterName] = $parameterValue;
    }
	
	public function buildUrl() {
		$finalUrl = "http://login.smsgatewayhub.com/api/mt/SendSMS?";
		$getParams = http_build_query($this->parameter, '', '&', PHP_QUERY_RFC3986); //Use %20 for spaces not +
		$finalUrl.=$getParams;
		return $finalUrl;
	}
	public function send() {
		$address = $this->buildUrl();
		$response = @file_get_contents($address);
		if(!$response)
			return FALSE;
		$json = json_decode($response);
		if($json->ErrorCode != "000"){
			$this->errorCode = $json->ErrorCode;
			$this->errorMessage = $json->ErrorMessage;
			return FALSE;
		}
		return TRUE;
	}
	
	public function _sms_debug() {
		return $this;
	}
}
?>