<?php
require_once('HttpClient.class.php');
require_once('class.CarPoint.php');

class CarMonitorGetAction extends Action
{
	// 根据车辆ID获取其最新位置
	// GET 
	// http://localhost/phpRest/index.php/CarMonitorGet/getLatestCarPoint/id/12345
	// {"strCarID":"001","strTime":"2011-11-26 11:19:54","strLatitude":"12345","strLongitude":"54321"}
	public function getLatestCarPoint()
	{
		$id = $_GET['id'];
		if (empty($id)) {
			echo '[]';
			return;
		}
		$foo_json = '[]';
		$client = new HttpClient(C('PUBLIC_NETWORK_IP'), C('PUBLIC_NETWORK_PORT'));
		$url = "http://".C('PUBLIC_NETWORK_IP').":".C('PUBLIC_NETWORK_PORT')."/getPos";
		// $url = C('PUBLIC_NETWORK_ADDRESS')."/GPSAPIGet/getLatestCarPoint/id/".$id;
		// $pageContents = $client->quickGet($url); 
		$inputPoint = array(CarID => $id, Time => '', Longitude => '', Latitude => '');
		$pageContents = $client->quickPost($url, json_encode($inputPoint));
		// echo $pageContents;
		$decodedCarPoint = json_decode($pageContents);
		
		$carPoint = new CarPoint($decodedCarPoint->CarID,$decodedCarPoint->Time,$decodedCarPoint->Latitude,$decodedCarPoint->Longitude);
		$iDirection = $decodedCarPoint->Direction;
		switch ($iDirection) {
			case 1:
				$carPoint->direction_image = "arrow_up";
				break;
			case 2:
				$carPoint->direction_image = "arrow_down";
				break;
			case 3:
				$carPoint->direction_image = "arrow_left";
				break;
			case 4:
				$carPoint->direction_image = "arrow_right";
				break;									
		}
		$carPoint->direction_image .= ".png";
		// echo $iDirection; return;
		$foo_json = json_encode($carPoint);
		echo $foo_json;

		// $sqlInsert = "insert into ";
		return;
	}


	//**************不再使用**********************
	// 根据POST过来的车辆信息获取车辆最新的位置信息
	//POST
	// http://localhost:9001/index.php/CarMonitorGet/getLatestCarPoints
	// INPUT: 001,002
	// output: [{"strCarID":"J001","strTime":"2011-11-30 17:57:53","strLatitude":"143329401","strLongitude":"419770779"},
	//          {"strCarID":"J002","strTime":"2011-12-01 21:25:22","strLatitude":"143269894","strLongitude":"418579602"}]
	public function getLatestCarPoints()
	{
		$carIDs = file_get_contents("php://input"); 
		$result = array();
		
		if (!empty($carIDs)) {
			$client = new HttpClient(C('PUBLIC_NETWORK_IP'), C('PUBLIC_NETWORK_PORT'));
			$url = C('PUBLIC_NETWORK_ADDRESS').'/GPSAPIGet/getLatestCarPoints';
			$params = $carIDs;
			$pageContents = $client->quickPost($url, $params); 
			$decodedCarPoints = json_decode($pageContents);
			for($i = 0; $i < count($decodedCarPoints); $i++)
			{
				$decodedCarPoint = $decodedCarPoints[$i];
				$carPoint = new CarPoint($decodedCarPoint->CarID,$decodedCarPoint->Time,$decodedCarPoint->Latitude,$decodedCarPoint->Longitude);
				$iDirection = $decodedCarPoint->direction;
				switch ($iDirection) {
					case 1:
						$carPoint->direction_image = "arrow_up";
						break;
					case 2:
						$carPoint->direction_image = "arrow_down";
						break;
					case 3:
						$carPoint->direction_image = "arrow_left";
						break;
					case 4:
						$carPoint->direction_image = "arrow_right";
						break;									
				}				
				$carPoint->direction_image .= ".png";
				array_push($result,$carPoint);
			}
			// echo $pageContents;
		}

		$foo_json = json_encode($result);
		echo $foo_json;
		return;

		$carIDs = file_get_contents("php://input"); 
		
		if (!empty($carIDs)) {
			//echo $carIDs;
			//return;
			$result = array();
			$carIDs = $this->checkUTF8($carIDs);
			$carIDA=explode(',',$carIDs);
			//var_dump($carIDA);
			//echo '<br>';
			//return;
			require_once('class.CarPoint.php');	
			$M = new Model();					
			for($i = 0;$i < count($carIDA);$i++)
			{
				$id = $carIDA[$i];
				if (C('DB_TYPE')== 'Sqlsrv') {
					// sqlserver
					$sql = "select top 2  CAR_ID ,CREATE_TIME ,LATITUDE,LONGITUDE from T_CARPOINTS where CAR_ID = '$id' order by CREATE_TIME desc";
					//echo '<br>';
					
				}
				else if (C('DB_TYPE')== 'pdo' || C('DB_TYPE')== 'mysql'){
						//sqlite
						$sql = "select  CAR_ID ,CREATE_TIME ,LATITUDE,LONGITUDE from T_CARPOINTS where CAR_ID = '$id'  order by CREATE_TIME desc limit 2";
					}
				$list = $M->query($sql);
				if (count($list)>0) {
//**************************************
					if(count($list) > 1)//选择两个相邻点计算方向
					{
						$carPoint = new CarPoint($list[0]['CAR_ID'],$list[0]['CREATE_TIME'],$list[0]['LATITUDE'],$list[0]['LONGITUDE']);
						$iDirection = $this->getDirection($list[0]['LATITUDE'],$list[0]['LONGITUDE'],$list[1]['LATITUDE'],$list[1]['LONGITUDE']);
						//查找车的类型，普通轿车用红色，面包车用蓝色
						if (C('DB_TYPE')== 'Sqlsrv') {
							// sqlserver
							$sql_select_car_info = "select top 1 CAR_ID,VIHICLE_TYPE,BUY_TIME,CAR_STATE from T_CAR_INFO where CAR_ID = '$id'";
						}
						else if (C('DB_TYPE')== 'pdo' || C('DB_TYPE')== 'mysql') {
								//sqlite
								$sql_select_car_info = "select CAR_ID,VIHICLE_TYPE,BUY_TIME,CAR_STATE from T_CAR_INFO where CAR_ID = '$id' limit 0,1";
								
							}
						$M_carinfo = new Model();
						$list_carinfo = $M_carinfo->query($sql_select_car_info);
						$car_type = "普通轿车";
						if (count($list_carinfo) > 0) {
							$car_type = $list_carinfo[0]["VIHICLE_TYPE"];
						}

						switch ($iDirection) {
							case 1:
								$carPoint->direction_image = "arrow_up";
								break;
							case 2:
								$carPoint->direction_image = "arrow_down";
								break;
							case 3:
								$carPoint->direction_image = "arrow_left";
								break;
							case 4:
								$carPoint->direction_image = "arrow_right";
								break;									
						}
						switch($car_type)
						{
							case "普通轿车":
								$carPoint->direction_image .= ".png";
							break;
							case "面包车":
								$carPoint->direction_image .= "_blue.png";
							break;
						}
						// echo $iDirection; return;
						// $foo_json = json_encode($carPoint);
						array_push($result,$carPoint);
					}
					else//只有一个点，那默认为正北
					{
						$carPoint = new CarPoint($list[0]['CAR_ID'],$list[0]['CREATE_TIME'],$list[0]['LATITUDE'],$list[0]['LONGITUDE']);
						$carPoint->direction_image = "CarUp.png";
						// $foo_json = json_encode($carPoint);
						array_push($result,$carPoint);
					}
//***************************************
						// $carPoint = new CarPoint($list[0]['CAR_ID'],$list[0]['CREATE_TIME'],$list[0]['LATITUDE'],$list[0]['LONGITUDE']);
					// array_push($result,$carPoint);
				}				
			}
			//var_dump($result);
			//return;
			
			$foo_json = json_encode($result);
			
			echo $foo_json;
			return;				
			
		}
	}
	// POST 
	// http://localhost/phpRest/index.php/CarMonitorGet/getPointList
	
	// post like below:
	// {"start":"2011-11-26 11:50:35","end":"2011-11-26 12:14:30"}
	// {"carID":"001","startTime":"2011-11-26 11:50:35","endTime":"2011-11-26 12:14:30"}
	// return like this:
	// [{"strCarID":"001","strTime":"2011-11-26 11:50:35","strLatitude":"12345","strLongitude":"54321"},
	// {"strCarID":"001","strTime":"2011-11-26 12:14:23","strLatitude":"12345","strLongitude":"54321"},
	// {"strCarID":"001","strTime":"2011-11-26 12:14:30","strLatitude":"12345","strLongitude":"54321"}]
	public function getPointList() {
		
		$jsonInput = file_get_contents("php://input"); 
		// $result = array();
		// if (!empty($jsonInput)) {
	
		// 	$client = new HttpClient(C('PUBLIC_NETWORK_IP'), C('PUBLIC_NETWORK_PORT'));
		// 	$url = C('PUBLIC_NETWORK_ADDRESS').'/GPSAPIGet/getPointList';
		// 	$params = $jsonInput;
		// 	$pageContents = $client->quickPost($url, $params); 
		// 	$decodedCarPoints = json_decode($pageContents);
		// 	for($i = 0; $i < count($decodedCarPoints); $i++)
		// 	{
		// 		$decodedCarPoint = $decodedCarPoints[$i];
		// 		$carPoint = new CarPoint($decodedCarPoint->CarID,$decodedCarPoint->Time,$decodedCarPoint->Latitude,$decodedCarPoint->Longitude);
		// 		$iDirection = $decodedCarPoint->direction;								
		// 		array_push($result,$carPoint);
		// 	}	
		// }
		// $foo_json = json_encode($result);
		// echo $foo_json;
		// return;

		$decodedCarParas = json_decode($jsonInput,true);
		//var_dump($decodedCarParas);
		//return;
		$carid = $decodedCarParas['carID']; 
		$startTime = $decodedCarParas['startTime'];
		$endTime = $decodedCarParas['endTime'];		
		if (empty($carid) || empty($startTime)||empty($endTime)) {
			return;
		}
		// else
		// {
		// 	return;
			
		// 	date_default_timezone_set("Asia/Shanghai");
		// 	$date=getdate();
		// 	$todayStart = 
		// 		mktime(0,0,0
		// 			,$date['mon'],$date['mday'],$date['year']);
		// 	$vTimeStart = date("Y-m-d H:i:s",$todayStart);
		// 	$vTimeEnd = date("Y-m-d H:i:s",strtotime('+1 day',$todayStart));
		// }
		
		
		
		require_once('class.CarPoint.php');
		$M = new Model();

		$sql = "select  CAR_ID ,CREATE_TIME ,LATITUDE,LONGITUDE from T_CARPOINTS
				where CAR_ID = '$carid' and CREATE_TIME >= '$startTime' and CREATE_TIME <= '$endTime' order by CREATE_TIME asc";
		$list = $M->query($sql);
		if (count($list)>0) {
			$result = array();
			for	($i=0;$i<count($list);$i++)
			{
				$carPoint = new CarPoint($list[$i]['CAR_ID'],$list[$i]['CREATE_TIME'],$list[$i]['LATITUDE'],$list[$i]['LONGITUDE']);
				array_push($result,$carPoint);
			}
			
			
			$foo_json = json_encode($result);
			
			echo $foo_json;		
		}
		else
		{
			echo '';
		}
		
		return;
	}
	private function checkUTF8($str) {
		$cod = mb_check_encoding($str,"UTF-8");
		if("UTF-8" != $cod ||  empty($cod))
		{
			$str = mb_convert_encoding( $str,'utf-8','gb2312'); 
		}
		return $str;
	}
	public function testDirection()
	{
		// echo $this->getDirection(143276823,419852044,143300339,419815177);
		echo $this->getDirection(143300339,419815177,143276823,419852044);
	}
	public function getDirection($lat1, $lng1, $lat2, $lng2){
		$k1 = $lng2-$lng1;
		$k2 = $lat2-$lat1;

		if( 0 == $k1){
			if($k2>0){
				$str="聊友在您的正北方 ";
				return 1;
			}
			else if( $k2<0){
				$str ="聊友在您的正南方 ";
				return 2;
			}
			else if( $k2 == 0){
				$str="聊友正在您的附近 ";
				return 1;
			}
		}else if( 0 == $k2){
			if($k1>0){
				$str="聊友在您的正东方 ";
				return 4;
			}
			else if( $k1<0){
				$str="聊友在您的正西方 ";
				return 3;
			}
		}else{
			$k=$k2/$k1;

			if($k2>0){
				if($k1>0){
					$angle = 180*atan($k)/M_PI;
					$str="聊友在您的东偏北 $angle 度方向  ";
					if ($angle > 45) {
						return 4;
					}
					else
					{
						return 1;
					}
				}else if($k1<0){
					$angle = 180*atan(-$k)/M_PI;
					$str= "聊友在您的西偏北 $angle 度方向  ";
					if ($angle > 45) {
						return 1;
					}
					else
					{
						return 3;
					}
				}
			}else if($k2<0){
				if($k1<0){
					$angle = 180*atan($k)/M_PI;
					$str = "聊友在您的西偏南 $angle 度方向  ";
					if ($angle > 45) {
						return 2;
					}
					else
					{
						return 3;
					}
				}
				else if($k1>0){
					$angle = 180*atan($k)/M_PI;
					$str="聊友在您的东偏南 $angle 度方向  ";
					if ($angle > 45) {
						return 2;
					}
					else
					{
						return 4;
					}
				}
			}
		}
		// return $str;

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