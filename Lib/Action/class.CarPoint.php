<?php

class CarPoint {
	
	
	public $strCarID;
	public $strTime;
	public $strLatitude;
	public $strLongitude;
	public $direction_image;
	
	/**
	 * Constructor, sets the initial values
	 * @access public
	 * @return POP3
	 */
	public function __construct($strCarID="",$strTime="",$strLatitude="",$strLongitude="",$direction_image = "") {
		$this->strCarID=$strCarID;
		$this->strTime=$strTime;
		$this->strLatitude=$strLatitude;
		$this->strLongitude=$strLongitude;	
		$this->direction_image = $direction_image;
	}
	
	private function catchWarning ($errno, $errstr, $errfile, $errline) {
		
	}
	
	//  End of class
}
?>