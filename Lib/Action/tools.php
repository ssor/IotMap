<?php


// || C('DB_TYPE')== 'mysql')
class Tools extends Action
{
	static function request($name)
	{
		$value = $_GET[$name];
		if($value == null){
			$value = $_POST[$name];
		}
		return $value;
	}	
	static public function executeSql($sql) {
		$state=true;
		if (!empty($sql)) {
			switch (C('DB_TYPE')) {
				case 'oracle':
					$sql="begin ".$sql." end;";	
					$M = new Model(); 
					if (!$M->execute($sql))
					{
						$state = false;
					}
					break;
				case 'pdo':
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
					break;
				case 'mysql':
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
					break;					
				default:
					# code...
					break;
			}	
		}
		
		return $state;
	}	
	// http://localhost/phpRest/index.php/Rest/getPeopleByID/id/abcd
	// {"name":"demo","age":1}
	// GET 
	public function getPeopleByID()
	{
		$id = $_GET['id'];
		if (empty($id)) {
			echo 0;
			return;
		}
		require_once('class.people.php');
		$newPeople = new people("demo",1);
		$foo_json = json_encode($newPeople);
		
		echo $foo_json;
		return;
	}
	//Post
	// http://localhost/phpRest/index.php/Rest/postPeople
	// {"name":"demo","age":2}
	public function postPeople()
	{
		$jsonInput = file_get_contents("php://input"); 
		$decoded_people = json_decode($jsonInput);
		
		var_dump($decoded_people);
		//echo $decoded->str;
		//$decoded = json_decode($jsonInput,true);
		//var_dump($jsonInput);
		return;
	}
	//http://localhost/phpRest/index.php/Rest/getDataFromMSSql
	public function getDataFromMSSql()
	{
		$M = new Model();
		$sql = "SELECT TOP 10 productName,productQuantity FROM IMS.dbo.tbOrder";
		$list = $M->query($sql);
		var_dump($list);
		return;
	}
	public function addOrder() {
		$M = new Model();
		$name = '糖果1';
		$name=$this->checkUTF8($name);
		$count = 10;
		$sqlExecute="insert into tbOrder(productName,productQuantity) values('$name',$count); ";
		$r = $M->execute($sqlExecute);
		if ($r) {
			echo true;
		}
		else
		{
			echo false;
		}
	}
	
	public function checkUTF8($str) {
		$cod = mb_check_encoding($str,"UTF-8");
		if("UTF-8" != $cod ||  empty($cod))
		{
			$str = mb_convert_encoding( $str,'utf-8','gb2312'); 
		}
		return $str;
	}
}
?>