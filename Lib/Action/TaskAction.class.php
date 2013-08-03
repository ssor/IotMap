<?php

class TaskAction extends Action
{
	public function addTask() {

		$carIDs  = $_GET['carid'];
		$task  = $_GET['task'];
		if (empty($carIDs)) {
			echo '';
			return;
		}
		$carIDs=$this->checkUTF8($carIDs);
		$carIDA=explode(',',$carIDs);
		
		$sqlExcute="";
		date_default_timezone_set("Asia/Shanghai");
		$time= date("Y-m-d");
		for($i=0;$i<count($carIDA);$i++)
		{
			$carID = $carIDA[$i];
			$sqlExcute .= "insert into T_CAR_INFO(CAR_ID, TASK, SEND_TIME) 
							values('$carID', '$task', '$time');";
		}
		//		echo $sqlExcute;
		//		return;
		$state = $this->executeSql($sqlExcute);
		if ($state) {
			echo "ok";
		}
		else
		{
			echo "failed";
		}
			
	}
	public function endTask()
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
			$vihicleType = $this->checkUTF8($vihicleType);
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
		$state = $this->executeSql($sqlInsert);
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
	public function checkTask() {
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
			else if (C('DB_TYPE')== 'pdo') {
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
			$M = new Model();
			$state = $this->executeSql($sqlInsert);
			if ($state) {
				$this->assign('jumpUrl','/CarManagement/index');
				$this->success('新增车辆成功！');
			}
			else
			{
				$this->assign('jumpUrl','/CarManagement/addCar');
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
	private function executeSql($sql) {
		$state=true;
		if (!empty($sql)) {
			
			//***************************************
			//  将修改记录注释 oracle数据时使用这种sql写法
			if (C('DB_TYPE') == 'oracle') {
				$sql="begin ".$sql." end;";	
				$M = new Model(); 
				if (!$M->execute($sql))
				{
					$state = false;
				}
			}
			
			//TODO oracle
			////////////////////////////////////////	
			
			////////////////////////////////////////
			//******************************************
			// sqlite使用不同的方法执行
			if (C('DB_TYPE') == 'pdo') {
				//$sql="BEGIN; $sql ";	
				//$sql="BEGIN TRANSACTION; $sql COMMIT;";	
				$M = new Model();
				$M->startTrans();
				//$M->execute($sql);
				$sqlArray = explode(';',$sql);
				for($i=0;$i<count($sqlArray)-1;$i++)
				{
					//$M = new Model();
					
					if (!$M->execute($sqlArray[$i])) {
						$state=false;
						break;
					}
				}
				if(!$M->commit())
				{
					$state=false;
					$M->rollback();
				}
			}
			//******************************************		
		}
		
		return $state;
	}
	private function checkUTF8($str) {
		$cod = mb_check_encoding($str,"UTF-8");
		if("UTF-8" != $cod ||  empty($cod))
		{
			$str = mb_convert_encoding( $str,'utf-8','gb2312'); 
		}
		return $str;
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
	/**
	+----------------------------------------------------------
	* 探针模式
	+----------------------------------------------------------
	*/
	public function checkEnv()
	{
		load('pointer',THINK_PATH.'/Tpl/Autoindex');//载入探针函数
		$env_table = check_env();//根据当前函数获取当前环境
		echo $env_table;
	}
	
}
?>