<?php

require_once('tools.php');

class MobileAction extends Action{
	
	public function index()
	{
		$sql = "select CAR_ID,VIHICLE_TYPE,BUY_TIME,CAR_STATE from T_CAR_INFO;";
		$M = new Model();
		$list = $M->query($sql);
		$this->assign('carList',$list);
		$this->display();
	}
	public function start_upload_location()
	{
		$carID = Tools::request('carid');
		$this->assign('carID', $carID);
		$this->assign('ip', C('LOCAL_IP'));
		$this->assign('port', C('LOCAL_PORT'));
		$this->assign('interval', C('INTERVAL'));

		$this->display();		
	}

}

?>
