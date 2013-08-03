<?php
require_once('HttpClient.class.php');
require_once 'tools.php';

class Task 
{
	public $carID;
	public $task;
	public $send_time;
	public $task_state;

	public function __construct($carID, $task, $send_time, $task_state = "处理中")
	{
		$this->carID = $carID;
		$this->task = $task;
		$this->send_time = $send_time;
		$this->task_state = $task_state;
	}
}

class MonitorAction extends Action
{
	public function get_new_pic()
	{
		$id = Tools::request("id");
		$finded = false;
		// echo stristr("1001-rt.png", $id); return;
		// echo $id;return;
		foreach(glob("Public/realTimePic/*") as $d)
		{
		    if(!is_dir($d))
		    {
		    	$name = basename($d);
		        if($name != 'rt.png')
		        {
		        	// $bfind = stripos($name, $id, 0);
		        	if(false == stristr($name, $id, 0)){
		        		//没找到
		        		continue;
		        	}
		        	else{
		        		$finded = true;
				        echo $name;
				        break;
		        	}
		        }
		        // echo "<br>";
		    }
		}
		if($finded == false){
			echo "rt.png";
		}
		// echo "Blender";
		// echo "rt2";
	}
	public function realtimeDlg()
	{
		$this->assign("pic_name","rt");
		// $this->assign("pic_name","Blender");
		$this->display();
	}
	public function chooseCartoMnt()
	{
		if($this->CheckRole())
		{
			$sql = "select CAR_ID,VIHICLE_TYPE,BUY_TIME,CAR_STATE from T_CAR_INFO ";
			$M = new Model();
			$list = $M->query($sql);
			for($i=0;$i<count($list);$i++)
			{
				$carID = $list[$i]["CAR_ID"];
				$sql_select = "select CAR_ID, TASK, SEND_TIME from T_CAR_TASK
		                 where CAR_ID = '$carID' and TASK_STATE = '处理中'
						 order by SEND_TIME asc limit 1";
				// echo $sql_select; return;
				$M_select = new  Model();
				$list_select = $M_select->query($sql_select);
				if(count($list_select) > 0)
				{
					$list[$i]["CAR_STATE"] = "执行任务中";
				}
			}
			// var_dump($list);
			// return;
			$this->assign('carList',$list);
			$this->display();
		}
	}
	public function startMntingOrder()
	{
		if($this->CheckRole())
		{
			$order_index = $_GET['order_index'];
			if (empty($order_index)) {
				return;
			}
			
			$sql = "select CAR_ID,VIHICLE_TYPE,BUY_TIME,CAR_STATE from T_CAR_INFO where CAR_ID in (";
			for($i = 0; $i <count($carIDA);$i++)
			{
				if ($i == (count($carIDA)-1) ){
					$sql.= "'".	$carIDA[$i]."'";
				}
				else{
					$sql.= "'".	$carIDA[$i]."',";
				}
			}
			$sql.= ")";
			//echo $sql;
			//return;
			
			require_once('class.CarInfo.php');
			$M = new Model();
			$list = $M->query($sql);
			$foo_json = "[]";
			if (count($list)>0) {
				$result = array();
				for	($i=0;$i<count($list);$i++)
				{
					$carInfo = new CarInfo($list[$i]['CAR_ID'],$list[$i]['VIHICLE_TYPE'],$list[$i]['BUY_TIME'],$list[$i]['CAR_STATE']);
					array_push($result,$carInfo);
				}
				
				
				$foo_json = json_encode($result);
				
				
			}
			$this->assign('carList',$foo_json);	
			
			
			//echo $carid;
			//return;
			$this->assign('carID',$carid);
			$this->display();
		}
	}
	public function startMnting()
	{
		if($this->CheckRole())
		{
			$carIDs = $_GET['carid'];
			if (empty($carIDs)) {
				return;
			}
			$carIDs = $this->checkUTF8($carIDs);
			$carIDA=explode(',',$carIDs);
			$carid = $carIDA[0];
			$sql = "select CAR_ID,VIHICLE_TYPE,BUY_TIME,CAR_STATE from T_CAR_INFO where CAR_ID in (";
			for($i = 0; $i <count($carIDA);$i++)
			{
				if ($i == (count($carIDA)-1) ){
					$sql.= "'".	$carIDA[$i]."'";
				}
				else{
					$sql.= "'".	$carIDA[$i]."',";
				}
			}
			$sql.= ")";
			//echo $sql;
			//return;
			
			require_once('class.CarInfo.php');
			$M = new Model();
			$list = $M->query($sql);
			$foo_json = "[]";
			if (count($list)>0) {
				$result = array();
				for	($i=0;$i<count($list);$i++)
				{
					$carInfo = new CarInfo($list[$i]['CAR_ID'],$list[$i]['VIHICLE_TYPE'],$list[$i]['BUY_TIME'],$list[$i]['CAR_STATE']);
					array_push($result,$carInfo);
				}
				
				
				$foo_json = json_encode($result);
				
				
			}
			$this->assign('carList',$foo_json);	
			$this->assign('LOCAL_IP',C('LOCAL_IP'));	
			$this->assign('LOCAL_PORT',C('LOCAL_PORT'));	
			
			
			//echo $carid;
			//return;
			$this->assign('carID',$carid);
			$this->display();
		}
	}
	public function setRoutePara()
	{
		if($this->CheckRole())
		{
			$sql = "select CAR_ID,CAR_STATE from T_CAR_INFO ";//where CAR_STATE = '1'
			$M = new Model();
			$list = $M->query($sql);
			$this->assign('carList',$list);
			
			date_default_timezone_set("Asia/Shanghai");
			$date=getdate();
			$todayStart = 
				mktime(0,0,0
					,$date['mon'],$date['mday'],$date['year']);
			$vTimeStart = date("Y-m-d H:i:s",$todayStart);
			$vTimeEnd = date("Y-m-d H:i:s",strtotime('+1 day',$todayStart));
			
			$this->assign('defaultStartTime',$vTimeStart);
			$this->assign('defaultEndTime',$vTimeEnd);
			
			//echo $vTimeStart;
			$this->display();
		}
	}
	public function startReplaying() {
		if($this->CheckRole())
		{
			$carid = $_GET['carid']; 
			$startTime = $_GET['start'];
			$endTime = $_GET['end'];
			if (empty($carid) || empty($startTime)||empty($endTime)) {
				return;
			} 
			$this->assign('startTime',$startTime);
			$this->assign('endTime',$endTime);
			$this->assign('carID',$carid);
			$this->assign('LOCAL_IP',C('LOCAL_IP'));	
			$this->assign('LOCAL_PORT',C('LOCAL_PORT'));	
			
			$this->display();	
		}	
	}
	public function showRouteInfo()
	 {
		if($this->CheckRole())
		{
			// var_dump($_POST['carID']); return;
			$id = $_POST['carID'];
			$timeStart = $_POST['startTime'];
			$timeEnd = $_POST['endTime'];
			
			// echo $timeStart; return;
			if (empty($id)) {
				//echo 'false';
				return;
			}
			$routePointCount = 20;

			// $client = new HttpClient(C('PUBLIC_NETWORK_IP'), C('PUBLIC_NETWORK_PORT'));
			// $url = C('PUBLIC_NETWORK_ADDRESS').'/GPSAPIGet/getPointList';
			// $params = array('carID' => $id, 'startTime' => $timeStart, 'endTime' => $timeEnd);
			// $pageContents = $client->quickPost($url, json_encode($params)); 
			// $list = json_decode($pageContents, true);
			$routeList= array();
			$sql = "select  CAR_ID ,CREATE_TIME Time,LATITUDE,LONGITUDE from T_CARPOINTS
					where CAR_ID = '$id' and CREATE_TIME >= '$timeStart' and CREATE_TIME <= '$timeEnd' order by CREATE_TIME asc";
			
			$M = new Model();
			$list = $M->query($sql);

			$totalPointCount = count($list);
			if ($totalPointCount > 0) {
				for	($i = 0; $i < $totalPointCount; $i = $i + $routePointCount)
				{
					$startTime = $list[$i]['Time'];
					$j = $i + $routePointCount-1;
					if ($totalPointCount <= $i + $routePointCount - 1) {
						$j = $totalPointCount - 1;
					}
					$endTime=$list[$j]['Time'];
					$count = $j-$i+1;
					$newRout = array('carID'=>$id,'TimeStart'=>$startTime,'TimeEnd'=>$endTime,'pointCount'=>$count);
					//var_dump($newRout);
					//return;
					array_push($routeList,$newRout);
				}
			}
			//var_dump($routeList);
			//return;
			
			$this->assign('routeList',$routeList);
			
			
			$this->display();
		}
	}
	// Monitor/addTask/task/任务内容rrr/carid/J001,J002
	public function addTask() {

		$carIDs  = $_GET['carid'];
		$task  = $_GET['task'];
		if (empty($carIDs)) {
			echo '';
			return;
		}
		$carIDs=$this->checkUTF8($carIDs);
		$task=$this->checkUTF8($task);
		$carIDA=explode(',',$carIDs);
		
		$sqlExcute="";
		date_default_timezone_set("Asia/Shanghai");
		$time = date("Y-m-d H:i:s");
		$state = true;
		for($i=0;$i<count($carIDA);$i++)
		{
			$M = new Model();
			$carID = $carIDA[$i];
			$sqlExcute = "insert into T_CAR_TASK(CAR_ID, TASK, SEND_TIME) 
							values('$carID', '$task', '$time');";

			$r = $M->execute($sqlExcute);
			if($r == false)
			{
				$state = false;
				break;
			}
		}
		if ($state == true) {
			echo "发送任务成功";
		}
		else
		{
			echo "发送任务失败";
		}
			
	}
	// Monitor/endTask/carid/J001
	public function endTask()
	{
		$carID  = $_GET['carid'];
		// date_default_timezone_set("Asia/Shanghai");
		// $time = date("Y-m-d H:i:s");		
		// $send_time  = $time;
		// $send_time = substr($send_time, 0, 10).' '.substr($send_time, 11);
		$carID = $this->checkUTF8($carID);
		$sql_update = "update T_CAR_TASK set TASK_STATE = '已完成' where CAR_ID = '$carID';";
		// $sql_select = "select CAR_ID, TASK from T_CAR_TASK where CAR_ID = '$carID' and TASK = '$task'";
		// echo $sql_update;
		// return;
		$M_update = new Model();
		$r = $M_update->execute($sql_update);
		if($r)
		{
			echo "ok";
		}
		else
		{
			echo "failed";
		}

	}
	// Monitor/checkTask/carid/J001
	public function checkTask() {
		$carID  = $_GET['carid'];
		$sql_select = "select CAR_ID, TASK, SEND_TIME from T_CAR_TASK
		                 where CAR_ID = '$carID' and TASK_STATE = '处理中'
						 order by SEND_TIME asc limit 1";
		$M_select = new Model();
		$list_select = $M_select->query($sql_select);
		// $result = array();
		// for($i = 0;$i < count($list_select); $i++)	
		// {
		// 	$task_temp = $list_select[$i];
		// 	$task = new Task($task_temp["CAR_ID"], $task_temp["TASK"], $task_temp["SEND_TIME"]);
		// 	array_push($result,$task);
		// }
		// $foo_json = json_encode($result);
		if (count($list_select) > 0) {
			echo $list_select[0]["TASK"]."   (".$list_select[0]["SEND_TIME"].")";
		}
		else
		{
			echo "no task";
		}
		// echo $foo_json;
	}
	public function index()
	{
		if($this->CheckRole())
		{
			$this->display();
		}
		
	}
	public function left()
	{
		if($this->CheckRole())
		{
			$this->display();
		}
	}
	
	public function right()
	{
		if($this->CheckRole())
		{
			$this->display();
		}
	}
	public function top()
	{
		if($this->CheckRole())
		{
			$this->display();
		}
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
		// if ($_SESSION['logined'] != 1)
		// {
		// 	$host = $_SERVER['HTTP_HOST'];
		// 	echo "<script language='javascript' type='text/javascript'>";
		// 	echo "top.location.href = 'http://".$host."/index.php/Welcome/welcome'";
		// 	echo "</script>";
		// 	return false;
		// 	//$this->redirect('Home-Welcome/welcome', null, 1, '您没有权利登录此页面，正在跳转到登录页面...');
		// 	return false;
		// }
		return true;
	}
}
?>