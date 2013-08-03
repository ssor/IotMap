<?php
require_once('tools.php');
// 为车辆管理提供restful接口
class CarManagementRestAction extends Action
{
	// http://localhost:9001/index.php/CarManagementRest/delete_car  POST
	// input  {"carID":"J004","vihicleType":"\u9762\u5305\u8f66","buyTime":"2012-11-30 ","carState":"\u6b63\u5e38","state":""}
	// output {"carID":"J004","vihicleType":"\u9762\u5305\u8f66","buyTime":"2012-11-30 ","carState":"\u6b63\u5e38","state":"ok"}	
	public function delete_car() {
		$jsonInput = file_get_contents("php://input"); 
		$decoded_car = json_decode($jsonInput);
		if(!empty($decoded_car->carID))
		{
			$carID = Tools::checkUTF8($decoded_car->carID);
			$sqlExcute="delete from T_CAR_INFO where CAR_ID = '$carID';";
			$state = Tools::executeSql($sqlExcute);
			if ($state) {
				$decoded_car->state = "ok";
			}
			else
			{
				$decoded_car->state = "failed";
			}
			$foo_json = json_encode($decoded_car);
			echo $foo_json;
		}
	}
	// http://localhost:9001/index.php/CarManagementRest/update_car  POST
	// input  {"carID":"J004","vihicleType":"\u9762\u5305\u8f66","buyTime":"2012-11-30 ","carState":"\u6b63\u5e38","state":""}
	// output {"carID":"J004","vihicleType":"\u9762\u5305\u8f66","buyTime":"2012-11-30 ","carState":"\u6b63\u5e38","state":"ok"}
	public function update_car() {
		$jsonInput = file_get_contents("php://input"); 
		$decoded_car = json_decode($jsonInput);
		if(!empty($decoded_car->carID))
		{
			if (empty($decoded_car->vihicleType)) {
				$decoded_car->vihicleType = "未填写类型";
			}
			else
			{
				$decoded_car->vihicleType = Tools::checkUTF8($decoded_car->vihicleType);
			}
			if (empty($decoded_car->buyTime)) {
				date_default_timezone_set("Asia/Shanghai");
				$time= date("Y-m-d");
				$decoded_car->buyTime = $time;
			}
			$carID = $decoded_car->carID;
			$vihicleType = $decoded_car->vihicleType;
			$buyTime = $decoded_car->buyTime;
			$sqlInsert = "update T_CAR_INFO  set VIHICLE_TYPE = '$vihicleType',BUY_TIME = '$buyTime' where  CAR_ID = '$carID';";
			$state = Tools::executeSql($sqlInsert);
			if ($state) {
				$decoded_car->state = "ok";
			}
			else
			{
				$decoded_car->state = "failed";
			}
			$foo_json = json_encode($decoded_car);
			echo $foo_json;
			return;
		}
	}
	// http://localhost:9001/index.php/CarManagementRest/add_car  POST
	// input  {"carID":"J004","vihicleType":"\u9762\u5305\u8f66","buyTime":"2011-11-30 ","carState":"\u6b63\u5e38","state":""}
	// output {"carID":"J004","vihicleType":"\u9762\u5305\u8f66","buyTime":"2011-11-30 ","carState":"\u6b63\u5e38","state":"ok"}
	public function add_car()
	{
		$jsonInput = file_get_contents("php://input"); 
		$decoded_car = json_decode($jsonInput);
		if(!empty($decoded_car->carID))
		{
			if (empty($decoded_car->vihicleType)) {
				$decoded_car->vihicleType = "未填写类型";
			}
			else
			{
				$decoded_car->vihicleType = Tools::checkUTF8($decoded_car->vihicleType);
			}
			if (empty($decoded_car->buyTime)) {
				date_default_timezone_set("Asia/Shanghai");
				$time= date("Y-m-d");
				$decoded_car->buyTime = $time;
			}
			$carID = $decoded_car->carID;
			$vihicleType = $decoded_car->vihicleType;
			$buyTime = $decoded_car->buyTime;
			$sqlInsert = "insert  into T_CAR_INFO(CAR_ID,VIHICLE_TYPE,BUY_TIME) values('$carID','$vihicleType','$buyTime');";
			$M = new Model();
			$state = Tools::executeSql($sqlInsert);
			if ($state) {
				$decoded_car->state = "ok";
			}
			else
			{
				$decoded_car->state = "failed";
			}
			$foo_json = json_encode($decoded_car);
			echo $foo_json;
			return;
		}
	}
	/**
	+----------------------------------------------------------
	* 默认操作
	+----------------------------------------------------------
	*/
	// http://localhost:9001/index.php/CarManagementRest/get_car_list GET
	public function get_car_list()
	{
		$sql = "select CAR_ID,VIHICLE_TYPE,BUY_TIME,CAR_STATE from T_CAR_INFO ";
		$M = new Model();
		$list = $M->query($sql);
		$result = array();
		if (count($list)>0) {
			require_once('class.CarInfo.php');
			for	($i=0;$i<count($list);$i++)
			{
				$car_info = new CarInfo($list[$i]['CAR_ID'],$list[$i]['VIHICLE_TYPE'],$list[$i]['BUY_TIME'],$list[$i]['CAR_STATE']);
				array_push($result,$car_info);
			}
		}
		$foo_json = json_encode($result);
		echo $foo_json;
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