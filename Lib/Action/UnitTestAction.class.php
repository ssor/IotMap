<?php

class UnitTestAction extends Action
{
	public function index() {
		
		$sql = "select CAR_ID,VIHICLE_TYPE,BUY_TIME,CAR_STATE from T_CAR_INFO ";
		require_once('class.CarPoint.php');
		$M = new Model();
		$list = $M->query($sql);
		$foo_json = "[]";
		if (count($list)>0) {
			$result = array();
			for	($i=0;$i<count($list);$i++)
			{
				$carPoint = new CarPoint($list[$i]['CAR_ID'],'11','11','11');
				array_push($result,$carPoint);
			}
			
			
			$foo_json = json_encode($result);
			
			$this->assign('carList',$foo_json);	
		}
		else
		{
			$this->assign('carList',$foo_json);	
		}
		
		
		$this->display();
	}
	public function SendPosTest()
	{
		$this->display();
		
	}
}
?>