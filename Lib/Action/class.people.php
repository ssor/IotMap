<?php

class people {
	
	
	public $name;
	public $age;
	
	
	
	/**
	 * Constructor, sets the initial values
	 * @access public
	 * @return POP3
	 */
	public function __construct($_name="",$_age=0) {
		$this->name=$_name;
		$this->age=$_age;
		
	}
	
	private function catchWarning ($errno, $errstr, $errfile, $errline) {
		
	}
	
	//  End of class
}
?>