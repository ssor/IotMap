<?php
require_once('tools.php');
/**
* 订单信息
*/
class order_info
{
	public $order_index;
	public $order_state;
	public $state;
	function __construct($_order_index='',$_order_state='')
	{
		$this->order_index = $_order_index;
		$this->order_state = $_order_state;
	}
}
/**
*  
*/
class order_link 
{
	public $carID;
	public $order_index;
	public $state;
	function __construct($_carID="",$_order_index="")
	{
		$this->carID = $_carID;
		$this->order_index = $_order_index;
	}
}
// 为车辆管理提供restful接口
class OrderRestAction extends Action
{
	// http://localhost:9001/index.php/OrderRest/add_order_link  POST
	// input  {"carID":"J002","order_index":"123","state":null}
	// output {"carID":"J002","order_index":"123","state":"ok"}	
	public function add_order_link() {
		$jsonInput = file_get_contents("php://input"); 
		$decoded_order_link = json_decode($jsonInput);
		if(!empty($decoded_order_link->carID) && !empty($decoded_order_link->order_index))
		{
			$carID = Tools::checkUTF8($decoded_order_link->carID);
			$order_index = Tools::checkUTF8($decoded_order_link->order_index);
			$sql_select = "select * from t_car_order_link where CAR_ID = '$carID' and ORDER_INDEX = '$order_index'";
			$M = new Model();
			$list = $M->query($sql_select);
			if (count($list) >0) {
				$decoded_order_link->state = "failed";
			}
			else
			{
				$sqlExcute="insert into t_car_order_link(CAR_ID,ORDER_INDEX) values('$carID','$order_index');";
				$state = Tools::executeSql($sqlExcute);
				if ($state) {
					$decoded_order_link->state = "ok";
				}
				else
				{
					$decoded_order_link->state = "failed";
				}
			}

			$foo_json = json_encode($decoded_order_link);
			echo $foo_json;
		}
	}
	// http://localhost:9001/index.php/OrderRest/get_all_order_link  GET
	// output: [{"carID":"J001","order_index":"123","state":null}]
	public function get_all_order_link()
	{
		$result = array();
		$sql_select = "select CAR_ID as carID,ORDER_INDEX as order_index from t_car_order_link";
		$M = new Model();
		$list = $M->query($sql_select);
		// var_dump($list);return;
		if (count($list)>0) {
			for ($i=0; $i < count($list); $i++) { 
				$order_link = new order_link($list[$i]['carID'],$list[$i]['order_index']);
				array_push($result,$order_link);
			}
		}
		$foo_json = json_encode($result);
		echo $foo_json;
	}
	// http://localhost:9001/index.php/OrderRest/get_order_link_by_order  POST
	// input:  {"carID":"","order_index":"123","state":null}
	// output: [{"carID":"J001","order_index":"123","state":null}]
	public function get_order_link_by_order()
	{
		$jsonInput = file_get_contents("php://input"); 
		$decoded_order_link = json_decode($jsonInput);
		$result = array();
		if(!empty($decoded_order_link->order_index))
		{
			$order_index = $decoded_order_link->order_index;
			$sql_select = "select CAR_ID as carID,ORDER_INDEX as order_index from t_car_order_link where ORDER_INDEX = '$order_index'";
			$M = new Model();
			$list = $M->query($sql_select);
			if (count($list)>0) {
				for ($i=0; $i < count($list); $i++) { 
					$order_link = new order_link($list[$i]['carID'],$list[$i]['order_index']);
					array_push($result,$order_link);
				}
			}
		}
		$foo_json = json_encode($result);
		echo $foo_json;
	}
	// http://localhost:9001/index.php/OrderRest/get_order_link_by_carID  POST
	// input:  {"carID":"J001","order_index":null,"state":null}
	// output: [{"carID":"J001","order_index":"123","state":null}]
	public function get_order_link_by_carID()
	{
		$jsonInput = file_get_contents("php://input"); 
		$decoded_order_link = json_decode($jsonInput);
		$result = array();
		if(!empty($decoded_order_link->carID))
		{
			$carID = $decoded_order_link->carID;
			$sql_select = "select CAR_ID as carID,ORDER_INDEX as order_index from t_car_order_link where CAR_ID = '$carID'";
			$M = new Model();
			$list = $M->query($sql_select);
			if (count($list)>0) {
				for ($i=0; $i < count($list); $i++) { 
					$order_link = new order_link($list[$i]['carID'],$list[$i]['order_index']);
					array_push($result,$order_link);
				}
			}
		}
		$foo_json = json_encode($result);
		echo $foo_json;
	}
	// http://localhost:9001/index.php/OrderRest/delete_order_link  POST
	// input: {"carID":"J001","order_index":"123","state":null}
	// output: {"carID":"J001","order_index":"123","state":"ok"}
	public function delete_order_link()
	{
		$jsonInput = file_get_contents("php://input"); 
		$decoded_order_link = json_decode($jsonInput);
		if(!empty($decoded_order_link->carID) && !empty($decoded_order_link->order_index))
		{
			$carID = Tools::checkUTF8($decoded_order_link->carID);
			$order_index = Tools::checkUTF8($decoded_order_link->order_index);
			$sql_select = "select * from t_car_order_link where CAR_ID = '$carID' and ORDER_INDEX = '$order_index'";
			$M = new Model();
			$list = $M->query($sql_select);
			if (count($list) >0) {
				$sqlExcute="delete from t_car_order_link where CAR_ID = '$carID' and ORDER_INDEX = '$order_index';";
				$state = Tools::executeSql($sqlExcute);
				if ($state) {
					$decoded_order_link->state = "ok";
				}
				else
				{
					$decoded_order_link->state = "failed";
				}
			}
			else
			{
				$decoded_order_link->state = "failed";
			}

			$foo_json = json_encode($decoded_order_link);
			echo $foo_json;
		}
	}
	// http://localhost:9001/index.php/OrderRest/add_order_info  POST
	// {"order_index":"123","order_state":"\u5728\u9014","state":null}
	// {"order_index":"123","order_state":"\u5728\u9014","state":"ok"}
	public function add_order_info()
	{
		$jsonInput = file_get_contents("php://input"); 
		$decoded_order_info = json_decode($jsonInput);
		if(!empty($decoded_order_info->order_index))
		{
			$order_index = Tools::checkUTF8($decoded_order_info->order_index); 
			$order_state = Tools::checkUTF8($decoded_order_info->order_state);
			$sql_select = "select ORDER_INDEX as order_index,ORDER_STATE as order_state from t_order_info where ORDER_INDEX = '$order_index'";
			$M = new Model();
			$list = $M->query($sql_select);
			if (count($list) >0) {
				$sqlExcute="update t_order_info set ORDER_STATE = '$order_state' where ORDER_INDEX = '$order_index';";
				$state = Tools::executeSql($sqlExcute);
				if ($state) {
					$decoded_order_info->state = "ok";
				}
				else
				{
					$decoded_order_info->state = "failed";
				}
			}
			else
			{
				$sqlExcute="insert into t_order_info(ORDER_INDEX,ORDER_STATE) values('$order_index','$order_state');";
				$state = Tools::executeSql($sqlExcute);
				if ($state) {
					$decoded_order_info->state = "ok";
				}
				else
				{
					$decoded_order_info->state = "failed";
				}
			}

			$foo_json = json_encode($decoded_order_info);
			echo $foo_json;
		}
	}
	// http://localhost:9001/index.php/OrderRest/get_order_info  POST
	// input : {"order_index":"123","order_state":null,"state":null}
	// {"order_index":"123","order_state":"\u5728\u9014","state":"ok"}
	public function get_order_info()
	{
		$jsonInput = file_get_contents("php://input"); 
		$decoded_order_info = json_decode($jsonInput);
		if(!empty($decoded_order_info->order_index))
		{
			$order_index = $decoded_order_info->order_index;
			$sql_select = "select ORDER_INDEX as order_index,ORDER_STATE as order_state from t_order_info where ORDER_INDEX = '$order_index'";
			$M = new Model();
			$list = $M->query($sql_select);
			if (count($list)>0) {
				$decoded_order_info->order_state = $list[0]['order_state'];
				$decoded_order_info->state = "ok";
			}
			$foo_json = json_encode($decoded_order_info);
			echo $foo_json;
		}

	}
	// http://localhost:9001/index.php/OrderRest/get_all_order  GET
	// [{"order_index":"123","order_state":"\u5728\u9014","state":"ok"}]
	public function get_all_order()
	{
		$result = array();
		$sql_select = "select ORDER_INDEX as order_index,ORDER_STATE as order_state from t_order_info";
		$M = new Model();
		$list = $M->query($sql_select);
		// var_dump($list);return;
		if (count($list)>0) {
			for ($i=0; $i < count($list); $i++) { 
				$order_info = new order_info($list[$i]['order_index'],$list[$i]['order_state']);
				$order_info->state = "ok";
				array_push($result,$order_info);
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