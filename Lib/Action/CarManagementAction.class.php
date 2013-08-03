<?php
require_once('tools.php');

class CarManagementAction extends Action
{
	public function deleteCar() {
		if($this->CheckRole())
		{
			$carIDs  = $_GET['ids'];
			if (empty($carIDs)) {
				$this->assign('jumpUrl','/CarManagement/index');
				$this->success('系统操作异常！');
			}
			$carIDs= Tools::checkUTF8($carIDs);
			$carIDA=explode('?',$carIDs);
			
			$sqlExcute="";
			
			for($i=0;$i<count($carIDA);$i++)
			{
				$carID = $carIDA[$i];
				$sqlExcute.="delete from T_CAR_INFO where CAR_ID = '$carID';";
			}
			//		echo $sqlExcute;
			//		return;
			$state = Tools::executeSql($sqlExcute);
			if ($state) {
				$this->assign('jumpUrl','/CarManagement/index');
				$this->success('删除车辆信息成功！');
			}
			else
			{
				$this->assign('jumpUrl',"/CarManagement/index");
				$this->success('删除车辆信息失败！');
			}
			
		}
	}
	public function updateCar() {
		if($this->CheckRole())
		{
			$carID = $_POST['carID'];
			$vihicleType = $_POST['vihicleType'];
			$buyTime = $_POST['buyTime'];
			if (empty($carID)) {
				$this->assign('jumpUrl','/CarManagement/addCar');
				$this->success('系统操作异常！');
			}
			if (empty($vihicleType)) {
				$vihicleType = "未填写类型";
			}
			else
			{
				$vihicleType = Tools::checkUTF8($vihicleType);
			}
			if (empty($buyTime)) {
				date_default_timezone_set("Asia/Shanghai");
				$time= date("Y-m-d");
				$buyTime = $time;
			}
			$sqlInsert = "update T_CAR_INFO  set VIHICLE_TYPE = '$vihicleType',BUY_TIME = '$buyTime' where  CAR_ID = '$carID';";
			//echo $sqlInsert;
			//return;
			//$M = new Model();
			//$state = $M->execute($sqlInsert);
			$state = Tools::executeSql($sqlInsert);
			if ($state) {
				$this->assign('jumpUrl','/CarManagement/index');
				$this->success('更新车辆信息成功！');
			}
			else
			{
				$this->assign('jumpUrl',"/CarManagement/editCar/$carID");
				$this->success('更新车辆信息失败！');
			}
			return;
			
			
		}
	}
	public function editCar() {
		if($this->CheckRole())
		{
			$carID = $_GET['carID'];
			if (empty($carID)) {
				$this->assign('jumpUrl','/CarManagement/index');
				$this->success('系统操作出现异常！');
			}
			if (C('DB_TYPE')== 'Sqlsrv') {
				// sqlserver
				$sql = "select top 1 CAR_ID,VIHICLE_TYPE,BUY_TIME,CAR_STATE from T_CAR_INFO where CAR_ID = '$carID'";
			}
			else if (C('DB_TYPE')== 'pdo' || C('DB_TYPE')== 'mysql') {
					//sqlite
					$sql = "select CAR_ID,VIHICLE_TYPE,BUY_TIME,CAR_STATE from T_CAR_INFO where CAR_ID = '$carID' limit 0,1";
					
				}
			$M = new Model();
			$list = $M->query($sql);
			if (count($list)<=0) {
				$this->assign('jumpUrl','/CarManagement/index');
				$this->success('系统操作出现异常！');
			}
			$this->assign('carInfo',$list[0]);
			$this->display();
		}
		
	}
	public function carInsert()
	{
		if($this->CheckRole())
		{
			$carID = $_POST['carID'];
			$vihicleType = $_POST['vihicleType'];
			$buyTime = $_POST['buyTime'];
			if (empty($carID)) {
				$this->assign('jumpUrl','/CarManagement/addCar');
				$this->success('未填写车牌号码！');
			}
			if (empty($vihicleType)) {
				$vihicleType = "未填写类型";
			}
			if (empty($buyTime)) {
				date_default_timezone_set("Asia/Shanghai");
				$time= date("Y-m-d");
				$buyTime = $time;
			}
			$sqlInsert = "insert  into T_CAR_INFO(CAR_ID,VIHICLE_TYPE,BUY_TIME) values('$carID','$vihicleType','$buyTime');";
			// echo $sqlInsert; // return;
			// $M = new Model();
			$state = Tools::executeSql($sqlInsert);
			if ($state) {
				$this->assign('jumpUrl','/CarManagement/index');
				$this->success('新增车辆成功！');
			}
			else
			{
				// $this->assign('jumpUrl','/CarManagement/addCar');
				$this->success('新增车辆失败！');
			}
			return;
		}
	}
	public function addCar()
	{
		if($this->CheckRole())
		{
			
			$this->display();
		}
	}
	/**
	+----------------------------------------------------------
	* 默认操作
	+----------------------------------------------------------
	*/
	public function index()
	{
		if($this->CheckRole())
		{
			$sql = "select CAR_ID,VIHICLE_TYPE,BUY_TIME,CAR_STATE from T_CAR_INFO ";
			$M = new Model();
			$list = $M->query($sql);
			$this->assign('carList',$list);
			$this->display();
		}
	}

	private function CheckRole()
	{
		if ($_SESSION['logined'] != 1)
		{
			$host = $_SERVER['HTTP_HOST'];
			echo "<script language='javascript' type='text/javascript'>";
			echo "top.location.href = 'http://".$host."/index.php/Welcome/welcome'";
			echo "</script>";
			return false;
			//$this->redirect('Home-Welcome/welcome', null, 1, '您没有权利登录此页面，正在跳转到登录页面...');
			return false;
		}
		return true;
	}
	
}
?>