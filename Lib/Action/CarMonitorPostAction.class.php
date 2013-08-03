<?php
require_once('tools.php');
require_once('HttpClient.class.php');

class CarMonitorPostAction extends Action
{
	
	// 增加车辆的点信息
	//Post
	// http://localhost/phpRest/index.php/CarMonitorPost/postCarPoint
	// {"CarID":"001","Time":"2011-11-26 11:19:54","Latitude":"12345","Longitude":"54321"}
	public function postCarPoint()
	{
		$jsonInput = file_get_contents("php://input");
		$this->savePoints2DB($jsonInput);
		$client = new HttpClient(C('PUBLIC_NETWORK_IP'), C('PUBLIC_NETWORK_PORT'));
		// $url = C('PUBLIC_NETWORK_ADDRESS')."/GPSAPIPost/postCarPoint";
		$url = "http://".C('PUBLIC_NETWORK_IP').":".C('PUBLIC_NETWORK_PORT')."/addPos";
		$pageContents = $client->quickPost($url, $jsonInput);
		echo $pageContents;return;
	}
	public function savePoints2DB($json)
	{
		$decodedCarPoint = json_decode($json);
		$carid = $decodedCarPoint->CarID;
		$carid = Tools::checkUTF8($carid);
		
		date_default_timezone_set("Asia/Shanghai");
		$time= date("Y-m-d H:i:s");
		$latitude = $decodedCarPoint->Latitude;
		$longitude = $decodedCarPoint->Longitude;
		$sqlExecute = "insert into T_CARPOINTS(CAR_ID ,CREATE_TIME ,LATITUDE ,LONGITUDE )
						 values( '$carid' ,'$time' ,'$latitude' ,'$longitude' );";
		$r = Tools::executeSql($sqlExecute);
	}
}
?>