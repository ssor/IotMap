<?php

class CarInfo {
	
	
	public $carID;
	public $vihicleType;
	public $buyTime;
	public $carState;
	public $state;
	
	/**
	 * Constructor, sets the initial values
	 * @access public
	 * @return POP3
	 */
	public function __construct($carID="",$vihicleType="",$buyTime="",$carState="") {
		$this->carID=$carID;
		$this->vihicleType=$vihicleType;
		$this->buyTime=$buyTime;
		$this->carState=$carState;
		$this->state = "";	
	}
	
	private function catchWarning ($errno, $errstr, $errfile, $errline) {
		
	}
	
	//  End of class
}
?>