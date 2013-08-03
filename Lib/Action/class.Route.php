<?php

class Route {
	
	
	public $TimeStart;
	public $TimeEnd;
	public $pointCount;
	public $carID;
	/**
	 * Constructor, sets the initial values
	 * @access public
	 * @return POP3
	 */
	public function __construct($strCarID="",$TimeStart="",$TimeEnd="",$pointCount=0) {
		$this->carID=$strCarID;
		$this->TimeStart=$TimeStart;
		$this->TimeEnd=$TimeEnd;
		$this->pointCount=$pointCount;	
	}
	
	
	//  End of class
}
?>