<?php

class IndexAction extends Action
{
	// http://localhost/phpRest/index.php/Index/getPeopleByID/id/abcd
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
	// http://localhost/phpRest/index.php/Index/postPeople
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
	/**
	+----------------------------------------------------------
	* 默认操作
	+----------------------------------------------------------
	*/
    public function index($name='ThinkPHP') {
        echo 'Hello,'.$name.'！';
        $this->display();
        }



?>